<?php

namespace HexideDigital\HexideAdmin\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Add translations scope to class
 *
 * @mixin Model
 *
 * @method static Builder|\Illuminate\Database\Query\Builder|self joinTranslations($modelTable = null, $translationsTable = null, $modelTableKey = null, $translationsTableKey = null)
 * @method static Builder|\Illuminate\Database\Query\Builder|self withTranslations()
 */
trait WithTranslationsTrait
{
    public function scopeWithTranslations(Builder $query): Builder
    {
        return $query->with([
            'translations' => function ($query) {
                $query->where('locale', app()->getLocale());
            },
        ]);
    }

    public function scopeJoinTranslations(
        Builder $query,
        ?string $modelTable = null,
        ?string $translationsTable = null,
        ?string $modelTableKey = null,
        ?string $translationsTableKey = null
    ): Builder
    {
        if (!$modelTable) {
            $modelTable = $this->getTable();
        }

        $singularModelTable = Str::singular($modelTable);

        if (!$translationsTable) {
            $translationsTable = $singularModelTable . "_translations";
        }

        $translationsTableKey = (empty($translationsTableKey) ? $singularModelTable . "_id" : $translationsTableKey);
        $modelTableKey = (empty($modelTableKey) ? "id" : $modelTableKey);

        return $query->leftJoin(
            $translationsTable,
            function ($join) use ($modelTable, $translationsTable, $translationsTableKey, $modelTableKey) {
                $join->on("$translationsTable.$translationsTableKey", '=', "$modelTable.$modelTableKey")
                    ->where('locale', '=', app()->getLocale());
            }
        );
    }
}
