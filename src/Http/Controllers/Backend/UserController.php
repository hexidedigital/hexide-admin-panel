<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

use App\Models\Role;
use App\Models\User;
use HexideDigital\HexideAdmin\Http\ViewNames;
use HexideDigital\ModelPermissions\Models\Permission;
use Illuminate\Database\Eloquent\Model;

class UserController extends HexideAdminBaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->setResourceAccessMap([
            'restore' => Permission::Restore,
            'forceDelete' => Permission::ForceDelete,
        ]);

        $this->setModule(User::class);
    }

    public function show(User $user)
    {
        $this->dataModel($user->load('roles'));

        return $this->render(ViewNames::Show);
    }

    public function create()
    {
        $this->data('roles', Role::pluck('title', 'id'));

        return $this->render(ViewNames::Create);
    }

    public function edit(User $user)
    {
        $this->data('roles', Role::pluck('title', 'id'));
        $this->dataModel($user->load('roles'));

        return $this->render(ViewNames::Edit);
    }

    /**
     * @param User $model
     *
     * @return bool
     */
    protected function canDestroyModel(Model $model): bool
    {
        return !$model->is_system;
    }
}
