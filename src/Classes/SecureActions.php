<?php

namespace HexideDigital\HexideAdmin\Classes;

use HexideDigital\ModelPermissions\Models\Permission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class SecureActions
{
    /**
     * <p>pair: 'action' => 'permission'</p>
     * <p>if key of action if 'all' = full actions for all actions</p>
     * <p>['all' => 'viewAny'] or ['all' => true]</p>
     *
     * @var Collection<string, string|bool|null>
     */
    protected Collection $accessMap;
    protected string $moduleName;

    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    public function setModuleName(string $moduleName): void
    {
        $this->moduleName = $moduleName;
    }

    public function getAccessMap(): Collection
    {
        return $this->accessMap;
    }

    public function setAccessMap($accessMap): void
    {
        $this->accessMap = collect($accessMap);
    }

    public function removeAccessMap(): void
    {
        $this->setAccessMap(null);
    }

    public function setResourceAccessMap(): void
    {
        $this->setAccessMap([
            'index' => Permission::ViewAny,
            'show' => Permission::View,
            'create' => Permission::Create,
            'store' => Permission::Create,
            'edit' => Permission::Update,
            'update' => Permission::Update,
            'destroy' => Permission::Delete,
        ]);
    }

    public function setFullAccessMap(): void
    {
        $this->setResourceAccessMap();

        $this->merge([
            'ajaxFieldChange' => 'ajax',
        ]);
    }

    public function __construct()
    {
        $this->accessMap = collect();
    }

    /**
     * @param array<string, string|bool|null>|null $array
     *
     * @return void
     */
    public function merge(?array $array): void
    {
        $this->accessMap = $this->accessMap->merge($array);
    }

    public function checkWithAbort(string $action, $model = null)
    {
//        abort_if(!$this->check($action, $model), 403);
    }

    public function check(string $action, $model = null): bool
    {
        if ($this->accessMap->isEmpty()) {
            return true;
        }

        $permission = $this->getPermissionForAction($action);

        return $permission === true
            // not empty string '' OR not NULL or not FALSE
            || (!empty($permission) && $this->gateCheck($permission, $model));
    }

    /**
     * @param string $action
     *
     * @return string|bool|null
     */
    public function getPermissionForAction(string $action)
    {
        $permission = $this->accessMap->get($action);

        if (isset($permission)) {
            return $permission;
        }

        return $this->accessMap->get('all');
    }

    private function gateCheck(string $permission, $model): bool
    {
        $args = $model;

        if ($model && in_array($permission, [Permission::ViewAny, Permission::Create])) {
            $args = get_class($model);
        }

        return Gate::any([
            $permission,
            Permission::key($this->moduleName, $permission),
        ], $args);
    }
}
