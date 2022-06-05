<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables;

use HexideDigital\HexideAdmin\Classes\DBTranslations\TranslationItem;
use HexideDigital\HexideAdmin\Classes\DBTranslations\TranslationRow;
use HexideDigital\HexideAdmin\Models\Translation;
use HexideDigital\HexideAdmin\Services\Backend\TranslationsService;
use HexideDigital\ModelPermissions\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

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
                ->searchable(),
        ];

        $localesCount = sizeof($this->locales);
        foreach ($this->locales as $locale) {
            $columns[] = Column::make(__('admin_labels.locales.' . $locale), 'value')
                ->addAttributes(['style' => "width: calc((100% - {$this->keyColumnWidth}) / {$localesCount})"]);
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
        ], array_combine($this->locales, $this->locales));

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

        $translationRows = (new TranslationsService($this->group))->getGroupTranslations();

        if (\Auth::user()->isRole(Role::SuperAdmin)) {
            $translationRows = $translationRows
                ->when($this->hasFilter('is_same'), fn(Collection $rows) => $rows
                    ->filter(fn(TranslationRow $row) => $row
                        ->applyIsSame(boolval($this->getFilter('is_same')))))
                ->when($this->hasFilter('exists_in_file'), fn(Collection $rows) => $rows
                    ->filter(fn(TranslationRow $row) => $row
                        ->applyIsValueExistsInFile(boolval($this->getFilter('exists_in_file')))))
                ->when($this->hasFilter('value_from_db'), fn(Collection $rows) => $rows
                    ->filter(fn(TranslationRow $row) => $row
                        ->applyIsValueFromDatabase(boolval($this->getFilter('value_from_db')))))
                ->when($this->hasFilter('locale'), fn(Collection $rows) => $rows
                    ->filter(fn(TranslationRow $row) => $row
                        ->applyHasLocale((string)$this->getFilter('locale'))));
        }

        return $this->applySearchFilterForCollection($translationRows);
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

        $translationRows = $collection;

        if ($this->hasFilter('search') && count($searchableColumns)) {
            $search = $this->getFilter('search');

            $translationRows = $collection->filter(fn(TranslationRow $row) => $row->applyMatch($search));
        }

        return $translationRows;
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
