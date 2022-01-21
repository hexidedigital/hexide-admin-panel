<?php

namespace HexideDigital\HexideAdmin\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $locale
 * @property int $admin_configuration_id
 * @property string|null $text
 * @property string|null $json
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigurationTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigurationTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigurationTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigurationTranslation whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigurationTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigurationTranslation whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigurationTranslation whereVariableId($value)
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
