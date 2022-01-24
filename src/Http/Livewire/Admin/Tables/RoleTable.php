<?php

namespace HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables;

use HexideDigital\ModelPermissions\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class RoleTable extends DefaultTable
{
    public function columns(): array
    {
        return [
            $this->getIdColumn(),

            Column::make('title')
                ->sortable()
                ->searchable(),

            Column::make(trans_choice('models.permissions.name', 2), 'permissions')
                ->format(fn($value) => view('components.admin.badge', ['list' => $value ? $value->pluck('title') : null]))
                ->asHtml(),

            $this->getActionsColumn(),
        ];
    }

    public function getModuleName(): string
    {
        return 'roles';
    }

    public function query(): Builder
    {
        return Role::with('permissions')->select();
    }
}
