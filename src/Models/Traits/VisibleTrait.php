<?php

namespace HexideDigital\HexideAdmin\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VisibleTrait
 * @package HexideDigital\HexideAdmin\Models\Traits
 * @mixin Model
 */
trait VisibleTrait
{
    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where($this->getTable() . '.status', true);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeHidden(Builder $query): Builder
    {
        return $query->where($this->getTable() . '.status', false);
    }
}
