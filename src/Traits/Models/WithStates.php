<?php

namespace HexideDigital\HexideAdmin\Traits\Models;

use Arr;
use HexideDigital\HexideAdmin\Contracts\WithStatesContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait WithStates
 * @package HexideDigital\HexideAdmin\Traits\Models
 * @implements WithStatesContract
 * @mixin Model
 */
trait WithStates
{


    /**
     * @param Builder $builder
     * @param string $state
     * @param string $field = 'state'
     * @return Builder
     */
    public function scopeOfState(Builder $builder, string $state, string $field = 'state'): Builder
    {
        return $builder->where($this->getTable() . '.' . $field, $this->getValueByKey($state));
    }

    /**
     * @param Builder $builder
     * @param array $states
     * @param string $field = 'state'
     * @return Builder
     */
    public function scopeOfStates(Builder $builder, array $states, string $field = 'state'): Builder
    {
        $_states = [];

        foreach ($states as $_state) {
            $_states[] = $this->getValueByKey($_state);
        }

        return $builder->whereIn($this->getTable() . '.' . $field, $_states);
    }
    /**
     * @return array
     */
    public function getStates(): array
    {
        return $this->states ?? [];
    }

    /**
     * @return array
     */
    public function getStatesKeys(): array
    {
        return array_keys($this->getStates());
    }

    /**
     * @param int|string|null $value
     * @return int|string|null
     */
    public function getKeyByValue($value)
    {
        foreach ($this->getStates() as $key => $_value) {
            if ($_value === $value) {
                return $key;
            }
        }

        return null;
    }

    /**
     * @param int|string|null $key
     * @return int|string|null
     */
    public function getValueByKey($key = null)
    {
        return Arr::get($this->getStates(), $key, null);
    }

}
