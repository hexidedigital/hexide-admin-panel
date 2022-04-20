<?php

namespace HexideDigital\HexideAdmin\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** @mixin Model|\Eloquent */
trait Publishable
{
    public function publishedIsDate(): bool
    {
        return $this->hasCast('published_at', 'date') || in_array('published_at', $this->getDates());
    }

    public function scopePublished(Builder $builder): Builder
    {
        return $builder
            ->orderByDesc('published_at')
            ->whereNotNull('published_at')
            ->when($this->publishedIsDate(), fn(Builder $b) => $b->whereDate('published_at', '<=', today()))
            ->when(!$this->publishedIsDate(), fn(Builder $b) => $b->where('published_at', '<=', now()));
    }
}
