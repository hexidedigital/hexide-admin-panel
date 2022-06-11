<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables;

use HexideDigital\ModelPermissions\Models\Role;
use Illuminate\Database\Eloquent\Builder;

class RoleTable extends DefaultTable
{
    public function columns(): array
    {
        return [
            $this->getIdColumn(),
            $this->getTitleColumn(),
            $this->booleanColumn('admin_access', __('admin_labels.admin_access'))
                ->addAttributes(['style' => 'width: 150px']),
            $this->badgesColumn('permissions', trans_choice('models.permissions.name', 2),),
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
