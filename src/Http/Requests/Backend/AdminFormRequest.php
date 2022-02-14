<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Http\Requests\Backend;

use HexideDigital\ModelPermissions\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

/**
 * @property-read bool $withAuthorization
 * @property-read string $modelClass
 * @property-read string $routeKeyName
 * @mixin FormRequest
 */
trait AdminFormRequest
{
    // protected string $modelClass;
    // protected string $routeKeyName;
    // protected bool $withAuthorization = true;

    public function authorize(): ?bool
    {
        if (!$this->withAuthorization) {
            return true;
        }

        if (!empty($this->route($this->routeKeyName))) {
            return Gate::allows(Permission::Update, call_user_func([$this->modelClass, 'find'], $this->modelId()));
        } else {
            return Gate::allows(Permission::Create, $this->modelClass);
        }
    }

    protected function modelId(): int
    {
        $modelId = $this->route($this->routeKeyName) ?? 0;

        if (isset($modelId->id) && ($modelId instanceof $this->modelClass)) {
            return $modelId->id;
        }

        return intval($modelId);
    }
}
