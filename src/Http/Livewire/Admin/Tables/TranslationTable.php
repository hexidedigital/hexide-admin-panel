<?php

namespace HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables;

use HexideDigital\HexideAdmin\Models\Translation;
use HexideDigital\HexideAdmin\Services\Backend\TranslationsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Rappasoft\LaravelLivewireTables\Views\Column;

class TranslationTable extends DefaultTable
{
    public $refresh = false;
    public bool $showSearch = false;

    public string $keyColumnWidth = '200px';
    public string $group;
    public array $locales;

    public function mount(Request $request)
    {
        $this->group = $request->route('group');

        $service = new TranslationsService($this->group);

        $this->locales = $service->getLocales();
    }

    public function columns(): array
    {
        $columns = [
            Column::make(__("admin_labels.attributes.key"))
                ->addAttributes(['style' => "width: {$this->keyColumnWidth};"])
            ,
        ];

        $locales = sizeof($this->locales);
        foreach ($this->locales as $locale) {
            $columns[] = Column::make(__('admin_labels.locales.' . $locale))
                ->addAttributes(['style' => "width: calc((100% - {$this->keyColumnWidth}) / {$locales})"]);
        }

        return $columns;
    }

    public function query(): Builder
    {
        return Translation::query();
    }

    public function rowView(): string
    {
        return "hexide-admin::admin.view.{$this->getModuleName()}.partials.translations-row";
    }

    public function getModuleName(): string
    {
        return 'translations';
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function render()
    {
        $service = new TranslationsService($this->group);

        $rows = $service->getPaginatedList(
            $this->perPage,
            $this->page,
            [
                'path' => route('admin.' . $this->getModuleName() . '.index', $this->group),
                'query' => [],
            ]
        );

        return view('livewire-tables::' . config('livewire-tables.theme') . '.datatable')
            ->with([
                'columns' => $this->columns(),
                'rowView' => $this->rowView(),
                'filtersView' => $this->filtersView(),
                'customFilters' => $this->filters(),
                'rows' => $rows,
                'modalsView' => $this->modalsView(),
                'bulkActions' => $this->bulkActions,
            ]);
    }
}
