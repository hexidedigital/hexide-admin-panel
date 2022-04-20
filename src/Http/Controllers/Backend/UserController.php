<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

use App\Models\User;
use HexideDigital\HexideAdmin\Http\ActionNames;
use HexideDigital\HexideAdmin\Http\Requests\Backend\UserRequest;
use HexideDigital\HexideAdmin\Http\ViewNames;
use HexideDigital\HexideAdmin\Services\Backend\UserService;
use HexideDigital\ModelPermissions\Models\Permission;
use HexideDigital\ModelPermissions\Models\Role;
use Illuminate\Http\RedirectResponse;

class UserController extends HexideAdminBaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->setResourceAccessMap([
            'restore' => Permission::Restore,
            'forceDelete' => Permission::ForceDelete,
            'profile' => Permission::Update,
        ]);

        $this->setModelClassName(User::class);
        $this->setModuleName('users');
        $this->setServiceClassName(get_class(\App::make(UserService::class)));
        $this->setService(\App::make(UserService::class));
        $this->setFromRequestClassName(UserRequest::class);
    }

    protected function addToBreadcrumbs(string $name, ?string $route = null)
    {
        if (!request()->routeIs('admin.users.profile')) {
            parent::addToBreadcrumbs($name, $route);
        }
    }

    public function profile()
    {
        $this->dataModel(\Auth::user());

        $this->protectAction('profile');

        return $this->render('profile', );
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $this->protectAction(ActionNames::Edit, $user);

        $service = $this->getService();

        $user = $service->handleRequest($request, $user);

        if ($request->has('from_profile')) {
            return $this->redirect('profile');
        }

        return $this->nextActionRedirect($user);
    }

    protected function render(?string $view = null, array $data = [], string $forceActionType = null)
    {
        if (in_array($view, [ViewNames::Create, ViewNames::Edit])) {
            $this->data('roles', Role::pluck('title', 'id'));
        }

        return parent::render($view, $data, $forceActionType);
    }
}
