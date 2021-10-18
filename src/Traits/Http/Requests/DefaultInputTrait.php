<?php

namespace HexideDigital\HexideAdmin\Http\Requests\Traits;

use Arr;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @mixin FormRequest
 */
trait DefaultInputTrait
{

    protected function handleDefaultInputs()
    {
        $inputs = $this->all();

        foreach ($this->defaults??[] as $key => $value){
            $item = Arr::get($inputs, $key);
            if(empty($item) && $item !== 0 && $item !== '0' && $item !== ''){
                /* todo - add rule for "asterisk" notation */
                /* items.old.*.status ,,, items.new.*.status */

                Arr::set($inputs, $key, $value);
            }
        }

        $this->merge($inputs);
    }

}
