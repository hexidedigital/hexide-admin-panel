<?php

namespace HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class PermissionTable extends DefaultTable
{
    public ?string $module = 'permissions';
    public Collection $modules;

    public function mount()
    {
        $permissions = Permission::get('title');

        $modules = $permissions->groupBy(fn(Permission $permission) => $permission->module)->keys();

        $this->modules = $modules->combine($modules);
    }

    public function filters(): array
    {
        return [
            'modules' => Filter::make(__('Modules'))->multiSelect($this->modules->toArray()),
        ];
    }

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
            Column::make(__("hexide-admin::buttons.actions"))
                ->addAttributes(['style' => 'width: 95px'])
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
        return Permission::query()
            ->when($this->getFilter('modules'), fn(Builder $builder, $modules) => $this->filterPermissions($builder))
            ->select();
    }

    private function filterPermissions(Builder $builder): Builder
    {
        $modules = $this->getFilter('modules');

        foreach ($modules as $module) {
            $builder->orWhere('title', 'like', $module . '%');
        }

        return $builder;
    }
}
