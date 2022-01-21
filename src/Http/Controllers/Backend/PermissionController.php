<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

use HexideDigital\ModelPermissions\Models\Permission;
use HexideDigital\HexideAdmin\Http\Requests\Backend\PermissionRequest;

class PermissionController extends HexideAdminBaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->setResourceAccessMap();

        $this->setModelClassName(Permission::class);
        $this->setModuleName('users');
        $this->setServiceClassName();
        $this->setService($this->getService());
        $this->setFromRequestClassName(PermissionRequest::class);
    }
}
