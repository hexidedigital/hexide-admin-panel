<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 *
 * @property int $id
 * @property string $locale
 * @property int $admin_configuration_id
 * @property string|null $text
 * @property string|null $json
 * @method static Builder|AdminConfigurationTranslation newModelQuery()
 * @method static Builder|AdminConfigurationTranslation newQuery()
 * @method static Builder|AdminConfigurationTranslation query()
 * @method static Builder|AdminConfigurationTranslation whereContent($value)
 * @method static Builder|AdminConfigurationTranslation whereId($value)
 * @method static Builder|AdminConfigurationTranslation whereLocale($value)
 * @method static Builder|AdminConfigurationTranslation whereVariableId($value)
 */
class AdminConfigurationTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'locale', 'admin_configuration_id',
        'text',
        'json',
    ];

    public $casts = [
        'json' => 'array'
    ];
}
