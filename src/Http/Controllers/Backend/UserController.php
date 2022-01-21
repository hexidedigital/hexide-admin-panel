<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

use App\Models\User;
use HexideDigital\ModelPermissions\Models\Permission;
use App\Services\Backend\UserService;
use HexideDigital\HexideAdmin\Http\Requests\Backend\UserRequest;
use HexideDigital\HexideAdmin\Http\ViewNames;
use HexideDigital\ModelPermissions\Models\Role;

class UserController extends HexideAdminBaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->setResourceAccessMap([
            'restore' => Permission::Restore,
            'forceDelete' => Permission::ForceDelete,
        ]);

        $this->setModelClassName(User::class);
        $this->setModuleName('users');
        $this->setServiceClassName(UserService::class);
        $this->setService(new UserService());
        $this->setFromRequestClassName(UserRequest::class);
    }

    protected function render(?string $view = null, array $data = [], string $forceActionType = null)
    {
        if (in_array($view, [ViewNames::Create, ViewNames::Edit])) {
            $this->data('roles', Role::pluck('title', 'id'));
        }

        return parent::render($view, $data, $forceActionType);
    }
}
