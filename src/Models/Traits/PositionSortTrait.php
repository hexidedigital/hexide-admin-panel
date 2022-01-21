<?php

namespace HexideDigital\HexideAdmin\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait PositionSortTrait
{
    public function scopeSorted(Builder $query, string $direction = 'ASC', string $field = 'position'): Builder
    {
        return $query->orderBy($this->getTable() . '.' . $field, $direction);
    }

    public function scopeSortedAsc(Builder $query, string $field = 'position'): Builder
    {
        return $query->orderBy($this->getTable() . '.' . $field, 'ASC');
    }

    public function scopeSortedDesc(Builder $query, string $field = 'position'): Builder
    {
        return $query->orderBy($this->getTable() . '.' . $field, 'DESC');
    }
}
