<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Database\Factories;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @mixin Factory
 */
trait FactoryWithTranslations
{
    public function configure(): self
    {
        return $this->translate();
    }

    public function translate(): self
    {
        return $this->afterMaking(function(Model $model) {
            if (!$this->isTranslatable($model)) {
                return $model;
            }

            $title = $this->getTitleTranslation();

            if (in_array('slug', $model->getFillable())) {
                $model->fill(['slug' => \Str::slug($title)]);
            }

            /** @var Translatable|Model $model */
            $translatableModel = $model->getTranslationModelName();
            $translatableModel = new $translatableModel();

            $model->fill($this->getTranslatedAttributes([
                'name' => $this->getNameTranslation(),
                'title' => $title,
                'content' => $this->getDescriptionTranslation(),
                'description' => $this->getDescriptionTranslation(),
            ], $translatableModel->getFillable()));

            return $model;
        });
    }

    public function translateMetaTitle(string $title = null)
    {
        return $this->translateKey('meta_title', $title);
    }

    public function translateKey(string $key, ?string $value = null)
    {
        return $this->afterMaking(function (Model $model) use ($key, $value) {
            /** @var Translatable|Model $model */
            $model->fill($this->getTranslatedAttributes([
                $key => $value ?: ($model->title ?? $this->getTitleTranslation()),
            ]));

            return $model;
        });
    }

    public function getTranslatedAttributes(array $keyValues, array $filterKeys = null): array
    {
        $translations = collect();

        foreach (config('translatable.locales') as $locale) {
            $translations[$locale] = collect($keyValues)
                ->mapWithKeys(fn($value, $key) => [
                    $key => $value . ' ' . $locale,
                ])
                ->when(!empty($filterKeys), fn(Collection $collection) => $collection->only($filterKeys));
        }

        return $translations->toArray();
    }

    public function getTitleTranslation(int $words = 6): string
    {
        return $this->faker->words($words, true);
    }

    public function getNameTranslation(int $words = 3): string
    {
        return $this->faker->words($words, true);
    }

    public function getDescriptionTranslation(int $nb = 3): string
    {
        return $this->faker->paragraphs($nb, true);
    }

    private function isTranslatable(Model $model): bool
    {
        return in_array(Translatable::class, array_keys(class_uses($model)));
    }
}
