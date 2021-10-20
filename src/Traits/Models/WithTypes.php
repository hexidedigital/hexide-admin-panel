<?php

namespace HexideDigital\HexideAdmin\Traits\Models;

use Arr;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WithTypes
 * @package HexideDigital\HexideAdmin\Traits\Models
 * @mixin Model
 */
trait WithTypes
{

    /**
     * @param Builder $builder
     * @param string $type
     * @param string $field = 'type'
     * @return Builder
     */
    public function scopeOfType(Builder $builder, string $type, string $field = 'type'): Builder
    {
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
    public function getTypes(): array
    {
        return $this->types ?? [];
    }

    /**
     * @return array
     */
    public function getTypesKeys(): array
    {
        return array_keys($this->getTypes());
    }

    /**
     * @param int|string|null $key
     * @return int|string|null
     */
    public function getValueByKey($key = null)
    {
        return Arr::get($this->getTypes(), $key, null);
    }

}
