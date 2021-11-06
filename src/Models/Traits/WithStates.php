<?php

namespace HexideDigital\HexideAdmin\Models\Traits;

use Arr;
use HexideDigital\HexideAdmin\Contracts\WithStatesContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait WithStates
 * @package HexideDigital\HexideAdmin\Models\Traits
 * @implements WithStatesContract
 * @method static \Illuminate\Database\Eloquent\Builder|static ofStates($builder, $state, $field)
 * @mixin Model
 */
trait WithStates
{
    /**
     * @param Builder $builder
     * @param string|array $state
     * @param string $field = 'state'
     * @return Builder
     */
    public function scopeOfState(Builder $builder, $state, string $field = 'state'): Builder
    {
        if (is_array($state)) return $this->ofStates($builder, $state, $field);

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
    public static function getStates(): array
    {
        return static::$states ?? [];
    }

    /**
     * @return array
     */
    public static function getStatesKeys(): array
    {
        return array_keys(static::getStates());
    }

    /**
     * @param int|string|null $key
     * @return int|string|null
     */
    public static function getValueByKey($key = null)
    {
        return Arr::get(static::getStates(), $key, null);
    }

    /**
     * @param int|string|null $value
     * @return int|string|null
     */
    public static function getKeyByValue($value)
    {
        foreach (static::getStates() as $key => $_value) {
            if ($_value === $value) {
                return $key;
            }
        }

        return null;
    }

}
