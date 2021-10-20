<?php

namespace HexideDigital\HexideAdmin\Traits\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SlugTrait
 * @package HexideDigital\HexideAdmin\Traits\Models
 * @mixin Model
 */
trait SlugTrait
{

    /**
     * @param Builder $query
     * @param string|null $slug
     * @return Builder
     */
    public function scopeSlug(Builder $query, ?string $slug = null): Builder
    {
        return $query->where($this->getTable().'.slug', $slug);
    }
}
