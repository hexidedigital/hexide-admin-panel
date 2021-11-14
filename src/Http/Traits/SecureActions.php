<?php

namespace HexideDigital\HexideAdmin\Http\Traits;

use Gate;
use HexideDigital\ModelPermissions\Models\Permission;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Symfony\Component\HttpFoundation\Response;

trait SecureActions
{
    /**
     * @param string $method
     *
     * @return bool|JsonResponse|RedirectResponse|object|\Symfony\Component\HttpFoundation\Response
     */
    public function protectAction(string $method)
    {
        $permission = array_get($this->accessMap, $method);

        if (!isset($permission)) {
            $permission = array_get($this->accessMap, 'all');
        }

        return $this->allowIfCan($permission, $method);
    }

    /**
     * @param string|bool $permission
     * @return bool|Application|JsonResponse|RedirectResponse|Redirector|object
     */
    private function allowIfCan($permission = false)
    {
        if ($permission === true) {
            return true;
        }

        if (empty($permission)) {
            return $this->abort();
        }

        $key = Permission::key($this->module, $permission);

        if (Gate::allows($key) || Gate::allows($permission)) {
            return true;
        }

        return $this->abort();
    }

    /**
     * @return Application|JsonResponse|RedirectResponse|Redirector|object
     */
    private function abort()
    {
        if (request()->ajax()) {
            return response()->json(['message' => __('api_labels.forbidden'), 'type' => 'error'])
                ->setStatusCode(Response::HTTP_FORBIDDEN);
        } else {
            return redirect(redirect()->back()->getTargetUrl());
        }
    }
}
