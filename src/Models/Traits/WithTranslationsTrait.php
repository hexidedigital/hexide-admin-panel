<?php

namespace HexideDigital\HexideAdmin\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Trail WithTranslationsTrait
 * Add translations scope to class
 *
 * @package HexideDigital\HexideAdmin\Models\Traits
 * @mixin Model
 *
 * @method static Builder|\Illuminate\Database\Query\Builder|self joinTranslations($modelTable = null, $translationsTable = null, $modelTableKey = null, $translationsTableKey = null)
 * @method static Builder|\Illuminate\Database\Query\Builder|self withTranslations()
 */
trait WithTranslationsTrait
{
    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeWithTranslations(Builder $query): Builder
    {
        return $query->with(
            [
                'translations' => function ($query) {
                    $query->where('locale', app()->getLocale());
                },
            ]
        );
    }

    /**
     * @param Builder $query
     * @param string|null $modelTable
     * @param string|null $translationsTable
     * @param string|null $modelTableKey
     * @param string|null $translationsTableKey
     *
     * @return Builder|\Illuminate\Database\Query\Builder
     */
    public function scopeJoinTranslations(
        Builder $query,
        ?string $modelTable = null,
        ?string $translationsTable = null,
        ?string $modelTableKey = null,
        ?string $translationsTableKey = null
    )
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
