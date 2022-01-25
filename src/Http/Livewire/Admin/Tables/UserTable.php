<?php

namespace HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables;

use App\Models\User;
use HexideDigital\ModelPermissions\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class UserTable extends DefaultTable
{
    public ?string $module = 'users';
    public Collection $roles;

    public array $filterNames = [
        'deleted' => 'Delete state',
    ];

    public function mount()
    {
        $this->roles = Role::pluck('title', 'id');
    }

    public function setUserRole()
    {
        if (sizeof($this->selectedKeys()) > 0) {
            $this->selectedRowsQuery()->get()->each(fn(User $user) => $user
                ->roles()->syncWithoutDetaching([Role::User]));
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
        return [
            'deleted' => Filter::make(__('Deleted uses'))
                ->select([
                    'default' => 'Default',
                    'all' => 'All',
                    'only_trashed' => 'Only trashed',
                ]),

            'roles' => Filter::make(trans_choice('models.roles.name', 2))
                ->multiSelect($this->roles->all()),
        ];
    }

    public function columns(): array
    {
        return [
            $this->getIdColumn(),

            Column::make(__("models.{$this->getModuleName()}.attributes.email"), 'email')
                ->sortable()
                ->searchable()
                ->linkTo(fn($value, $column, $row) => route("admin.{$this->getModuleName()}.edit", $row)),

            Column::make(trans_choice('models.roles.name', 2), 'roles')
                ->format(fn($value) => view('components.admin.badge', ['list' => $value ? $value->pluck('title') : null]))
                ->asHtml(),

            $this->getActionsColumn(),
        ];
    }

    public function getModuleName(): string
    {
        return 'users';
    }

    public function query(): Builder
    {
        return User::query()
            ->with('roles')
            ->when($this->getFilter('roles'), fn(Builder $query, $roles) => $query
                ->whereHas('roles', fn(Builder $query) => $query->whereIn('id', $roles)))
            ->when($this->getFilter('deleted'), function (Builder $query, string $filter) {
                $query->when($filter == 'default', fn(Builder $query) => $query);
                $query->when($filter == 'all', fn(Builder $query) => /** @var User $query */ $query->withTrashed());
                $query->when($filter == 'only_trashed', fn(Builder $query) => /** @var User $query */ $query->onlyTrashed());
            })
            ->select();
    }
}
