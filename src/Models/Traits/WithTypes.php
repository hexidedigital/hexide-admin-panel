<?php

namespace HexideDigital\HexideAdmin\Models\Traits;

use Arr;
use HexideDigital\HexideAdmin\Contracts\WithTypesContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WithTypes
 * @package HexideDigital\HexideAdmin\Models\Traits
 * @implements WithTypesContract
 * @method static \Illuminate\Database\Eloquent\Builder|self ofTypes($builder, $state, $field)
 * @mixin Model
 */
trait WithTypes
{
    /**
     * @param Builder $builder
     * @param string|array $type
     * @param string $field = 'type'
     * @return Builder
     */
    public function scopeOfType(Builder $builder, $type, string $field = 'type'): Builder
    {
        if (is_array($type)) return $this->ofTypes($builder, $type, $field);

        return $builder->where($this->getTable() . '.' . $field, $this->getValueByKey($type));
    }

    /**
     * @param Builder $builder
     * @param array $types
     * @param string $field = 'type'
     * @return Builder
     */
    public function scopeOfTypes(Builder $builder, array $types, string $field = 'type'): Builder
    {
        $_types = [];

        foreach ($types as $_type) {
            $_types[] = $this->getValueByKey($_type);
        }

        return $builder->whereIn($this->getTable() . '.' . $field, $_types);
    }

    /**
     * @return array
     */
    public static function getTypes(): array
    {
        return static::$types ?? [];
    }

    /**
     * @return array
     */
    public static function getTypesKeys(): array
    {
        return array_keys(static::getTypes());
    }

    /**
     * @param int|string|null $key
     * @return int|string|null
     */
    public static function getValueByKey($key = null)
    {
        return Arr::get(static::getTypes(), $key, null);
    }

}
