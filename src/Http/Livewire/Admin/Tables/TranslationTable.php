<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables;

use HexideDigital\HexideAdmin\Models\Translation;
use HexideDigital\HexideAdmin\Services\Backend\TranslationsService;
use HexideDigital\ModelPermissions\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;
use Str;

class TranslationTable extends DefaultTable
{
    protected TranslationsService $service;
    public $refresh = false;

    public string $keyColumnWidth = '200px';
    public string $group;
    public array $locales;

    public function mount(Request $request)
    {
        $this->group = $request->route('group');

        $this->locales = (new TranslationsService($this->group))->getLocales();
    }

    public function columns(): array
    {
        $columns = [
            Column::make(__("admin_labels.attributes.key"))
                ->addAttributes(['style' => "width: {$this->keyColumnWidth};"])
                ->searchable(fn($value) => dd(func_num_args()))
            ,
        ];

        $locales = sizeof($this->locales);
        foreach ($this->locales as $locale) {
            $columns[] = Column::make(__('admin_labels.locales.' . $locale))
                ->addAttributes(['style' => "width: calc((100% - {$this->keyColumnWidth}) / {$locales})"]);
        }

        return $columns;
    }

    public function filters(): array
    {
        if (!\Auth::user()->isRole(Role::SuperAdmin)) {
            return [];
        }

        $locale = array_merge([
            '' => 'All',
        ],array_combine($this->locales, $this->locales));

        return [
            'locale' => Filter::make('Locale')
                ->select($locale),
            'is_same' => Filter::make('Is same')
                ->select([
                    '' => 'All',
                    1 => 'Same',
                    0 => 'Different',
                ]),
            'exists_in_file' => Filter::make('Exists in file')
                ->select([
                    '' => 'All',
                    1 => 'Exist',
                    0 => 'No exist',
                ]),
            'value_from_db' => Filter::make('Value from db')
                ->select([
                    '' => 'All',
                    1 => 'From DB',
                    0 => 'From file',
                ]),
        ];
    }

    public function query()
    {
        return null;
    }

    public function rowView(): string
    {
        return "hexide-admin::admin.view.{$this->getModuleName()}.partials.translations-row";
    }

    public function getModuleName(): string
    {
        return module_name_from_model(new Translation);
    }

    public function rowsQuery()
    {
        $this->cleanFilters();

        $collection = (new TranslationsService($this->group))->getGroupTranslations();

        if(\Auth::user()->isRole(Role::SuperAdmin)) {
            $collection = $collection
                ->when($this->hasFilter('locale'), fn(Collection $list) => $collection
                    ->filter(fn(Collection $translation) => $this
                            ->filterForTranslation($translation, 'locale')->count() > 0))
                ->when($this->hasFilter('is_same'), fn(Collection $list) => $collection
                    ->filter(fn(Collection $translation) => $this
                            ->filterForTranslation($translation, 'is_same')->count() > 0))
                ->when($this->hasFilter('exists_in_file'), fn(Collection $list) => $collection
                    ->filter(fn(Collection $translation) => $this
                            ->filterForTranslation($translation, 'exists_in_file')->count() > 0))
                ->when($this->hasFilter('value_from_db'), fn(Collection $list) => $collection
                    ->filter(fn(Collection $translation) => $this
                            ->filterForTranslation($translation, 'value_from_db')->count() > 0));
        }

        return $this->applySearchFilterForCollection($collection);
    }

    public function filterForTranslation(Collection $translation, string $field): Collection
    {
        return $translation->filter(function ($locale) use ($field) {
            if ($locale instanceof Collection) {
                return $locale->get($field) == $this->getFilter($field);
            }

            return false;
        });
    }

    public function getRowsProperty()
    {
        if ($this->paginationEnabled) {
            $list = $this->rowsQuery();

            return new LengthAwarePaginator(
                $list->slice(($this->page - 1) * $this->perPage, $this->perPage),
                $list->count(),
                $this->perPage,
                $this->page,
                [
                    'path' => route('admin.' . $this->getModuleName() . '.index', $this->group),
                    'query' => [],
                ],
            );
        }

        return $this->rowsQuery();
    }

    public function applySearchFilterForCollection(Collection $collection): Collection
    {
        $searchableColumns = $this->getSearchableColumns();

        if ($this->hasFilter('search') && count($searchableColumns)) {
            $search = $this->getFilter('search');

            $collection = $collection->filter(function (Collection $translation) use ($search) {
                return
                    Str::contains($translation->get('key'), $search)
                    || $translation->filter(function ($locale) use ($search) {
                        if ($locale instanceof Collection) {
                            return Str::contains($locale->get('value'), $search);
                        }

                        return false;
                    })
                        ->count() > 0;
            });
        }

        return $collection;
    }

    public function render()
    {
        return view('livewire-tables::' . config('livewire-tables.theme') . '.datatable')
            ->with([
                'columns' => $this->columns(),
                'rowView' => $this->rowView(),
                'filtersView' => $this->filtersView(),
                'customFilters' => $this->filters(),
                'rows' => $this->rows,
                'modalsView' => $this->modalsView(),
                'bulkActions' => $this->bulkActions,
            ]);
    }
}
