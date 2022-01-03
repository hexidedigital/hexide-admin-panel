<?php

namespace HexideDigital\HexideAdmin\Classes;

use HexideDigital\ModelPermissions\Models\Permission;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

class SecureActions
{
    /**
     * <p>pair: 'action' => 'permission'</p>
     * <p>if key of action if 'all' = full actions for all actions</p>
     * <p>['all' => 'access'] or ['all' => true]</p>
     *
     * @var array<string, string|bool|null>|null
     */
    protected ?array $accessMap;
    protected ?string $moduleName;

    public function __construct(string $moduleName = null, array $accessMap = null)
    {
        $this->moduleName = $moduleName;
        $this->accessMap = $accessMap;
    }

    public function getModuleName(): ?string
    {
        return $this->moduleName;
    }

    public function setModuleName(?string $moduleName): void
    {
        $this->moduleName = $moduleName;
    }

    /**
     * @return array<string, string|bool|null>|null
     */
    public function getAccessMap(): ?array
    {
        return $this->accessMap;
    }

    /**
     * @param array<string, string|bool|null>|null $accessMap
     */
    public function setAccessMap(?array $accessMap): void
    {
        $this->accessMap = $accessMap;
    }

    public function removeAccessMap(): void
    {
        $this->setAccessMap(null);
    }

    /**
     * @param string $action
     * @return string|bool|null
     */
    public function getPermissionForAction(string $action)
    {
        $permission = Arr::get($this->accessMap, $action);

        if (!isset($permission)) {
            $permission = Arr::get($this->accessMap, 'all');
        }

        return $permission;
    }

    public function issetPermission(string $action): bool
    {
        return boolval($this->getPermissionForAction($action));
    }

    public function hasPermissions(): bool
    {
        return is_array($this->accessMap) && sizeof($this->accessMap) > 0;
    }

    public function setResourceAccessMap(): void
    {
        $this->setAccessMap([
            'index' => Permission::Access,
            'show' => Permission::View,
            'create' => Permission::Create,
            'store' => Permission::Create,
            'edit' => Permission::Edit,
            'update' => Permission::Edit,
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

    /**
     * @param array<string, string|bool|null>|null $array
     * @return void
     */
    public function merge(?array $array): void
    {
        $this->accessMap = array_merge($this->accessMap, $array ?? []);
    }

    public function check(string $action): bool
    {
        $permission = $this->getPermissionForAction($action);

        return !$this->hasPermissions() || (!empty($permission) && $this->checkPermission($permission));
    }

    /**
     * @param string|bool $permission
     * @return bool
     */
    private function checkPermission($permission): bool
    {
        return $permission === true || $this->gateCheck($permission);
    }

    private function gateCheck(string $permission): bool
    {
        return Gate::allows(Permission::key($this->moduleName, $permission)) || Gate::allows($permission);
    }
}
