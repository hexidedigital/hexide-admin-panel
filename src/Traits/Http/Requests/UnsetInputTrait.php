<?php

namespace HexideDigital\HexideAdmin\Http\Requests\Traits;

use Arr;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @mixin FormRequest
 */
trait UnsetInputTrait
{

    protected function handleUnsetInput()
    {
        $inputs = $this->all();

        foreach ($this->unset_keys??[] as $key){
            Arr::forget($inputs, $key);
        }

        $this->merge($inputs);
    }

}
