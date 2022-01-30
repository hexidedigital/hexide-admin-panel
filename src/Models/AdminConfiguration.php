<?php

namespace HexideDigital\HexideAdmin\Models;

use Astrotomic\Translatable\Translatable;
use HexideDigital\HexideAdmin\Classes\Configurations\Configuration;
use HexideDigital\HexideAdmin\Models\Traits\VisibleTrait;
use HexideDigital\HexideAdmin\Models\Traits\WithTranslationsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Eloquent;

/**
 * @mixin AdminConfigurationTranslation
 * @mixin Eloquent
 *
 * @property int $id
 * @property string $key
 * @property string $type
 * @property string|null $name
 * @property string|null $description
 * @property int $translatable
 * @property string|null $group
 * @property int $in_group_position
 * @property string|null $content
 * @property string|array|null $value
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read AdminConfigurationTranslation|null $translation
 * @property-read Collection|AdminConfigurationTranslation[] $translations
 * @property-read int|null $translations_count
 * @method static Builder|static joinTranslations(?string $modelTable = null, ?string $translationsTable = null, ?string $modelTableKey = null, ?string $translationsTableKey = null)
 * @method static Builder|static listsTranslations(string $translationField)
 * @method static Builder|static newModelQuery()
 * @method static Builder|static newQuery()
 * @method static Builder|static notTranslatedIn(?string $locale = null)
 * @method static Builder|static orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|static orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|static orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|static query()
 * @method static Builder|static sorted(string $direction = 'ASC')
 * @method static Builder|static sortedAsc()
 * @method static Builder|static sortedDesc()
 * @method static Builder|static translated()
 * @method static Builder|static translatedIn(?string $locale = null)
 * @method static Builder|static visible()
 * @method static Builder|static forGroup($group)
 * @method static Builder|static whereCreatedAt($value)
 * @method static Builder|static whereDescription($value)
 * @method static Builder|static whereGroup($value)
 * @method static Builder|static whereId($value)
 * @method static Builder|static whereInGroupPosition($value)
 * @method static Builder|static whereKey($value)
 * @method static Builder|static whereName($value)
 * @method static Builder|static whereStatus($value)
 * @method static Builder|static whereTranslatable($value)
 * @method static Builder|static whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|static whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|static whereType($value)
 * @method static Builder|static whereUpdatedAt($value)
 * @method static Builder|static whereValue($value)
 * @method static Builder|static withTranslation()
 * @method static Builder|static withTranslations()
 */
class AdminConfiguration extends Model
{
    use Translatable, WithTranslationsTrait;
    use VisibleTrait;

    public string $moduleName = 'admin_configurations';

    /* ------------------------ Model ------------------------ */

    public $translationModel = AdminConfigurationTranslation::class;

    protected array $translatedAttributes = ['text', 'json',];

    protected $fillable = [
        'key', 'type', 'name', 'description', 'translatable', 'content', 'value', 'status', 'group', 'in_group_position',
    ];

    protected $casts = [
        'status' => 'bool',
        'translatable' => 'bool',
        'value' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (AdminConfiguration $adminConfiguration) {
            $adminConfiguration->key = $adminConfiguration->attributes['key'];
        });

        static::updating(function (AdminConfiguration $adminConfiguration) {
            $adminConfiguration->key = $adminConfiguration->attributes['key'];
        });

        static::saved(function (AdminConfiguration $adminConfiguration) {
            \App::make(Configuration::class)->storeToCache();
        });
    }

    /* ------------------------ attributes ------------------------ */

    public function setKeyAttribute(string $value)
    {
        $this->attributes['key'] = Str::slug($value, '_');
    }

    public function setValueAttribute($value)
    {
        $field = $this->storeKey();

        $configuration = \App::make(Configuration::class);

        if ($configuration->isSingleValueType($this->type)) {

            $value = $this->asJson($value);

        } elseif ($configuration->isArrayValueType($this->type)) {

            if (empty($value)) {
                $value = [];
            }

            $value = $this->asJson($value);

        } elseif ($configuration->isObjectValueType($this->type)) {

            $value = array_wrap($value);

            if ($this->isType(Configuration::RANGE)) {

                $value = $this->asJson([
                    'from' => array_get($value, 'from'),
                    'to' => array_get($value, 'to'),
                ]);

            } elseif ($this->isType(Configuration::IMG_BUTTON)) {

                $value = $this->asJson([
                    'image' => array_get($value, 'image'),
                    'url' => array_get($value, 'url'),
                    'title' => array_get($value, 'title'),
                    'content' => array_get($value, 'content'),
                ]);

            }

        }

        $this->attributes[$field] = $value;
    }

    public function getValueAttribute($value)
    {
        $field = $this->storeKey();

        $value = $this->translatable ? ($this->translate()->attributes[$field] ?? null) : $this->attributes[$field];

        $configuration = \App::make(Configuration::class);

        if ($configuration->isTextValueType($this->type)) {
            return $value;
        }

        if ($configuration->isSingleValueType($this->type)) {
            return $this->fromJson($value);
        }

        if ($configuration->isArrayValueType($this->type)) {
            if (empty($value)) {
                return [];
            }

            return $this->fromJson($value);
        }

        if ($configuration->isObjectValueType($this->type)) {
            return $this->fromJson($value);
        }

        return $value;
    }

    /* ------------------------ scopes ------------------------ */

    /**
     * @param Builder $builder
     * @param string|array<string> $groups
     *
     * @return Builder
     */
    public function scopeForGroup(Builder $builder, $groups): Builder
    {
        return $builder->whereIn('group', array_wrap($groups));
    }

    public function scopeSorted(Builder $builder, string $direction = 'ASC'): Builder
    {
        return $builder->orderBy('in_group_position', $direction);
    }

    public function scopeSortedAsc(Builder $builder): Builder
    {
        return $builder->orderBy('in_group_position', 'ASC');
    }

    public function scopeSortedDesc(Builder $builder): Builder
    {
        return $builder->orderBy('in_group_position', 'DESC');
    }

    public function storeKey(): string
    {
        return \App::make(Configuration::class)->getStoreKey($this->type, $this->translatable);
    }

    public function isType(string $type): bool
    {
        return in_array($type, \App::make(Configuration::class)->getTypes()) && $this->type === $type;
    }
}
