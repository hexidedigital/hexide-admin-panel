<?php

namespace HexideDigital\HexideAdmin\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Eloquent;

/**
 * @mixin Eloquent
 *
 * @property int $id
 * @property string $locale
 * @property string $group
 * @property string $key
 * @property string|null $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Translation newModelQuery()
 * @method static Builder|Translation newQuery()
 * @method static Builder|Translation ofTranslatedGroup(string $group)
 * @method static Builder|Translation orderByGroupKeys(bool $ordered = false)
 * @method static Builder|Translation query()
 * @method static Builder|Translation selectDistinctGroup()
 * @method static Builder|Translation whereCreatedAt($value)
 * @method static Builder|Translation whereGroup($value)
 * @method static Builder|Translation whereId($value)
 * @method static Builder|Translation whereKey($value)
 * @method static Builder|Translation whereLocale($value)
 * @method static Builder|Translation whereUpdatedAt($value)
 * @method static Builder|Translation whereValue($value)
 */
class Translation extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function scopeOfTranslatedGroup(Builder $query, string $group): Builder
    {
        return $query->where('group', $group)->whereNotNull('value');
    }

    public function scopeOrderByGroupKeys(Builder $query, bool $ordered = false): Builder
    {
        return $query->when($ordered, fn(Builder $builder) => $builder
            ->orderBy('group')
            ->orderBy('key'));
    }

    public function scopeSelectDistinctGroup(Builder $query): Builder
    {
        $select = '';

        switch (DB::getDriverName()) {
            case 'mysql':
                $select = 'DISTINCT `group`';
                break;
            default:
                $select = 'DISTINCT "group"';
                break;
        }

        return $query->select(DB::raw($select));
    }
}
