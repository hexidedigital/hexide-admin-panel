<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Database\Factories;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @mixin Factory
 */
trait FactoryWithTranslations
{
    public function configure(): self
    {
        return $this
            ->afterCreating(fn(Model $model) => $this->translate($model)->save())
            ->afterMaking(fn(Model $model) => $this->translate($model));
    }

    public function translate(Model $model): Model
    {
        $translatableModel = 'App\\Models\\' . \Str::studly(str_singular($model->getTable())) . 'Translation';

        if (!$this->isTranslatable($model, $translatableModel)) {
            return $model;
        }

        $title = $this->getTitleTranslation();

        if (in_array('slug', $model->getFillable())) {
            $translations = ['slug' => \Str::slug($title),];
        } else {
            $translations = [];
        }

        $translatableModel = new $translatableModel();

        foreach (config('translatable.locales') as $locale) {
            $item = [
                'name'        => $this->getNameTranslation() . ' ' . $locale,
                'title'       => "$title $locale",
                'content'     => $this->getDescriptionTranslation(),
                'description' => $this->getDescriptionTranslation(),
            ];

            $item = Arr::only($item, $translatableModel->getFillable());

            $translations[$locale] = $item;
        }

        $model->fill($translations);

        return $model;
    }

    public function getTitleTranslation($words = 6)
    {
        return $this->faker->words($words, true);
    }

    public function getNameTranslation($words = 3)
    {
        return $this->faker->words($words, true);
    }

    public function getDescriptionTranslation($nb = 3)
    {
        return $this->faker->paragraphs($nb, true);
    }

    private function isTranslatable(Model $model, string $translatableModel): bool
    {
        return in_array(Translatable::class, array_keys(class_uses($model)))
            && class_exists($translatableModel);
    }
}
