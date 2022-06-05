<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Classes\DBTranslations;

use Illuminate\Support\Collection;

final class TranslationRow
{
    private string $key;
    private Collection $translations;

    public function __construct()
    {
        $this->translations = collect();
    }

    public static function touch(string $key): self
    {
        $ins = new self();

        return $ins->setKey($key);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return TranslationRow
     */
    public function setKey(string $key): TranslationRow
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    /**
     * @param Collection $translations
     * @return TranslationRow
     */
    public function setTranslations(Collection $translations): TranslationRow
    {
        $this->translations = $translations;

        return $this;
    }

    public function addTranslation(TranslationItem $item, string $locale = null): void
    {
        $locale = $locale ?: $item->getLocale();

        $this->translations->put($locale, $item);
    }

    public function getTranslationItem(string $locale): TranslationItem
    {
        return  $this->getTranslations()
            ->get($locale, TranslationItem::touch($locale)
                ->setKey($this->getKey())
                ->setIsStub(true));
    }

    public function applyHasLocale(string $locale): bool
    {
        return $this->getTranslations()->has($locale);
    }

    public function applyIsSame(bool $state): bool
    {
        return $this->getTranslations()
            ->contains(fn(TranslationItem $item) => $item->isSame() === $state);
    }

    public function applyIsValueExistsInFile(bool $state): bool
    {
        return $this->getTranslations()
            ->contains(fn(TranslationItem $item) => $item->isValueExistsInFile() === $state);
    }

    public function applyIsValueFromDatabase(bool $state): bool
    {
        return $this->getTranslations()
            ->contains(fn(TranslationItem $item) => $item->isValueFromDatabase() === $state);
    }

    public function applyMatch(string $search): bool
    {
        return $this->getTranslations()
            ->contains(fn(TranslationItem $item) => $item->match($search));
    }
}
