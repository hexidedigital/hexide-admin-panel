<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables;

use HexideDigital\HexideAdmin\Models\AdminConfiguration;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ConfigurationTable extends DefaultTable
{
    public $refresh = false;

    public function columns(): array
    {
        return [
            $this->getIdColumn(),

            $this->makeColumn('name')
                ->sortable()
                ->searchable(),

            $this->makeColumn('type')
                ->sortable()
                ->format(fn($value, $col, $row) => __('models.admin_configurations.type.' . $row->type ?? '')),

            $this->makeColumn('key')
                ->sortable()
                ->searchable(),

            $this->booleanColumn('translatable'),

            $this->makeColumn('group')
                ->sortable()
                ->searchable(),

            $this->ajaxNumberColumn('in_group_position'),

            $this->getActionsColumn(),
        ];
    }

    public function getModuleName(): string
    {
        return module_name_from_model(new AdminConfiguration);
    }

    public function query(): Builder
    {
        return AdminConfiguration::query();
    }
}
