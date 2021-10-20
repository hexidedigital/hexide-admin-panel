<?php


namespace HexideDigital\HexideAdmin\Models\Traits;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait PositionSortTrait
 * @package HexideDigital\HexideAdmin\Models\Traits
 * @mixin Model
 */
trait PositionSortTrait
{
    /**
     * @param Builder $query
     * @param string $direction
     * @param string $field
     * @return Builder
     */
    public function scopeSorted(Builder $query, string $direction = 'ASC', string $field = 'position'): Builder
    {
        return $query->orderBy($this->getTable().'.'.$field, $direction);
    }

    /**
     * @param Builder $query
     * @param string $field
     * @return Builder
     */
    public function scopeSortedAsc(Builder $query, string $field = 'position'): Builder
    {
        return $query->orderBy($this->getTable().'.'.$field, 'ASC');
    }

    /**
     * @param Builder $query
     * @param string $field
     * @return Builder
     */
    public function scopeSortedDesc(Builder $query, string $field = 'position'): Builder
    {
        return $query->orderBy($this->getTable().'.'.$field, 'DESC');
    }
}
