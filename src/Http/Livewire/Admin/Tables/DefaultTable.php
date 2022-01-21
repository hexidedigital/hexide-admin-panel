<?php

namespace HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;

abstract class DefaultTable extends DataTableComponent
{
    public ?string $module = null;

    public string $defaultSortColumn = 'id';
    public string $defaultSortDirection = 'desc';
    public ?int $searchFilterDebounce = 500;
    public $refresh = 10000; // every 10 seconds
    public array $perPageAccepted = [10, 25, 50, 100];

    public array $sortDirectionNames = [
        'id' => [
            'asc' => '1-9',
            'desc' => '9-1',
        ],
    ];

    public function getModuleName(): ?string
    {
        return $this->module;
    }

    public function setTableClass(): string
    {
        return 'table table-striped table-hover';
    }
}
