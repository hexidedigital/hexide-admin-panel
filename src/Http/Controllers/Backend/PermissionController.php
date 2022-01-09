<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

use App\Models\Permission;

class PermissionController extends HexideAdminBaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->setFullAccessMap();

        $this->setModule(Permission::class);
    }
}
