<?php

declare(strict_types=1);

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

            Column::make(__('admin_labels.admin_access'), 'admin_access')
                ->sortable()
                ->format(function ($value, $col, $row) {
                    /** @var Role $row */
                    $icon = $row->admin_access ? 'fas fa-check' : 'fas fa-times';
                    $color = $row->admin_access ? 'text-success' : 'text-danger';

                    return <<<HTML
                        <div class="row"><span class="col-12 text-center $color"><i class="$icon"></i></span></div>
                    HTML;
                })
                ->asHtml(),

            Column::make(trans_choice('models.permissions.name', 2), 'permissions')
                ->format(fn($value) => view('components.admin.badge', ['list' => $value ? $value->pluck('title') : null]))
                ->asHtml(),

            $this->getActionsColumn(),
        ];
    }

    public function getModuleName(): string
    {
        return module_name_from_model(new Role);
    }

    public function query(): Builder
    {
        return Role::with('permissions')->select();
    }
}
