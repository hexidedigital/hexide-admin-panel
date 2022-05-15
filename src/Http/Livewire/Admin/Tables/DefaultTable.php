<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

abstract class DefaultTable extends DataTableComponent
{
    public string $defaultSortColumn = 'id';
    public string $defaultSortDirection = 'desc';
    public ?int $searchFilterDebounce = 500;
    public $refresh = 20000; // every 20 seconds
    public array $perPageAccepted = [10, 25, 50, 100];

    public array $sortDirectionNames = [
        'id' => [
            'asc' => '1-9',
            'desc' => '9-1',
        ],
    ];

    abstract public function getModuleName(): string;

    public function setTableClass(): string
    {
        return 'table table-striped table-hover';
    }

    protected function getIdColumn(): Column
    {
        return Column::make(__("admin_labels.attributes.id"), 'id')
            ->addAttributes(['style' => 'width: 50px;'])
            ->sortable();
    }

    protected function getPositionColumn(): Column
    {
        return Column::make(__('admin_labels.attributes.position'), 'position')
            ->sortable()
            ->format(fn($value, $column, $row) => view('hexide-admin::admin.partials.ajax.input', [
                'model' => $row,
                'module' => $this->getModuleName(),
                'field' => 'position',
                'type' => 'number',
            ]))
            ->asHtml();
    }

    protected function getPriorityColumn(): Column
    {
        return Column::make(__('admin_labels.attributes.priority'), 'priority')
            ->sortable()
            ->format(fn($value, $column, $row) => view('hexide-admin::admin.partials.ajax.input', [
                'model' => $row,
                'module' => $this->getModuleName(),
                'field' => 'priority',
                'type' => 'number',
            ]))
            ->asHtml();
    }

    protected function getStatusColumn(): Column
    {
        return Column::make(__('admin_labels.attributes.status'), 'status')
            ->sortable()
            ->format(fn($value, $column, $row) => view('hexide-admin::admin.partials.ajax.toggler', [
                'model' => $row,
                'module' => $this->getModuleName(),
                'field' => 'status',
            ]))
            ->asHtml();
    }

    protected function getActionsColumn(): Column
    {
        return Column::make(__("hexide-admin::buttons.actions"))
            ->addAttributes(['style' => 'width: 95px'])
            ->format(fn($value, $column, $row) => view('hexide-admin::partials.control_buttons', [
                'model' => $row,
                'module' => $this->getModuleName(),
            ]))
            ->asHtml();
    }

    protected function getImageColumn(): Column
    {
        return Column::make(__("admin_labels.attributes.image"), 'image')
            ->format(fn($value, $column, $row) => view('hexide-admin::partials.image', ['src' => $row->image]))
            ->asHtml();
    }

    protected function getTitleColumn(string $field = 'title'): Column
    {
        return Column::make(__("admin_labels.attributes.title"), $field)
            ->sortable()
            ->searchable();
    }
}
