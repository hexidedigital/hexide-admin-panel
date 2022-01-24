<?php

namespace HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables;

use HexideDigital\ModelPermissions\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class PermissionTable extends DefaultTable
{
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
            $this->getIdColumn(),

            Column::make('title')
                ->sortable()
                ->searchable(),

            $this->getActionsColumn(),
        ];
    }

    public function getModuleName(): string
    {
        return 'permissions';
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
