<?php

namespace HexideDigital\HexideAdmin\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait VisibleTrait
{
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where($this->getTable() . '.status', true);
    }

    public function scopeHidden(Builder $query): Builder
    {
        return $query->where($this->getTable() . '.status', false);
    }
}
