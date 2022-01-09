<?php

namespace HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables;

use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class RoleTable extends DefaultTable
{
    public ?string $module = 'roles';

    public function columns(): array
    {
        return [
            Column::make(__("admin_labels.attributes.id"), 'id')
                ->addAttributes(['style' => 'width: 50px;'])
                ->sortable()
            ,

            Column::make('title')
                ->sortable()
                ->searchable()
            ,
            Column::make(trans_choice('models.permissions.name', 2), 'permissions')
                ->format(fn($value) => view('components.admin.badge', ['list' => $value ? $value->pluck('title') : null]))
                ->asHtml()
            ,

            Column::make(__("hexide-admin::buttons.actions"))
                ->addAttributes([
                    'style' => 'width: 95px'
                ])
                ->format(fn($value, $column, $row) => view('hexide-admin::partials.control_buttons', [
                    'model' => $row,
                    'module' => $this->module,
                ]))
                ->asHtml()
            ,
        ];
    }

    public function query(): Builder
    {
        return Role::with('permissions')->select();
    }
}
