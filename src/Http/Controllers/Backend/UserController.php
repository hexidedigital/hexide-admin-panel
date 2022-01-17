<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

use App\Models\Role;
use App\Models\User;
use HexideDigital\ModelPermissions\Models\Permission;

class UserController extends HexideAdminBaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->setResourceAccessMap([
            'restore' => Permission::Restore,
            'forceDelete' => Permission::ForceDelete,
        ]);

        $this->initModule(User::class);

        $this->data('roles', Role::pluck('title', 'id'));
    }
}
