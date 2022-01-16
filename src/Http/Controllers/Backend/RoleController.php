<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

use App\Models\Permission;
use App\Models\Role;

class RoleController extends HexideAdminBaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->setResourceAccessMap();

        $this->initModule(Role::class);

        $this->data(['permissions' => Permission::pluck('title', 'id'),]);
    }
}
