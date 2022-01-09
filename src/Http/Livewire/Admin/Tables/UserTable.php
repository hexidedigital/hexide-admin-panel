<?php

namespace HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class UserTable extends DefaultTable
{
    public ?string $module = 'users';

    public array $filterNames = [
        'deleted' => 'Delete state',
    ];

    public function setUserRole()
    {
        if (sizeof($this->selectedKeys()) > 0) {
            $this->selectedRowsQuery()->get()->each(fn(User $user) => $user
                ->roles()->syncWithoutDetaching(Role::where('key', 'user')->first()));
        }

        $this->resetAll();
    }

    public function bulkActions(): array
    {
        return [
            'setUserRole' => __('Set user roles'),
        ];
    }

    public function filters(): array
    {
        $rolesArray = Role::pluck('title', 'id')->toArray();

        return [
            'deleted' => Filter::make(__('Deleted uses'))
                ->select([
                    'default' => 'Default',
                    'all' => 'All',
                    'only_trashed' => 'Only trashed',
                ])
            ,
            'roles' => Filter::make(trans_choice('models.roles.name', 2))
                ->multiSelect($rolesArray)
            ,
        ];
    }

    public function columns(): array
    {
        return [
            Column::make(__("admin_labels.attributes.id"), 'id')
                ->addAttributes(['style' => 'width: 50px;'])
                ->sortable()
            ,
            Column::make(__("models.$this->module.attributes.email"), 'email')
                ->sortable()
                ->searchable()
                ->linkTo(fn($value, $column, $row) => route("admin.$this->module.edit", $row))
            ,
            Column::make(trans_choice('models.roles.name', 2), 'roles')
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
        return User::query()
            ->with('roles')
            ->when($this->getFilter('roles'), fn(Builder $query, $roles) => $query
                ->whereHas('roles', fn(Builder $query) => $query->whereIn('id', $roles)))
            ->when($this->getFilter('deleted'), function (Builder $query, string $filter) {
                $query->when($filter == 'default', fn(Builder $query) => $query);
                $query->when($filter == 'all', fn(Builder $query) => $query->withTrashed());
                $query->when($filter == 'only_trashed', fn(Builder $query) => $query->onlyTrashed());
            })
            ->select();
    }
}
