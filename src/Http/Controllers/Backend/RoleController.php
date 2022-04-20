<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

use HexideDigital\ModelPermissions\Models\Permission;
use HexideDigital\ModelPermissions\Models\Role;
use HexideDigital\HexideAdmin\Http\ViewNames;
use HexideDigital\HexideAdmin\Services\Backend\RoleService;
use HexideDigital\HexideAdmin\Http\Requests\Backend\RoleRequest;

class RoleController extends HexideAdminBaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->setResourceAccessMap();

        $this->setModelClassName(Role::class);
        $this->setModuleName('roles');
        $this->setServiceClassName(RoleService::class);
        $this->setService(new RoleService());
        $this->setFromRequestClassName(RoleRequest::class);
    }

    protected function render(?string $view = null, array $data = [], string $forceActionType = null)
    {
        if (in_array($view, [ViewNames::Create, ViewNames::Edit])) {
            $permissions = Permission::orderBy('id')->get(['id', 'title']);

            $this->data([
                'modules' => $permissions->groupBy('module'),
                'permissions' => $permissions->pluck('title', 'id'),
            ]);
        }

        return parent::render($view, $data, $forceActionType);
    }
}
