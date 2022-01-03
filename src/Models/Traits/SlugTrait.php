<?php

namespace HexideDigital\HexideAdmin\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait SlugTrait
{
    public function scopeSlug(Builder $query, ?string $slug = null): Builder
    {
        return $query->where($this->getTable() . '.slug', $slug);
    }
}
