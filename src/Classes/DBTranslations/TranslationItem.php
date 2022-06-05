<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Classes\DBTranslations;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class TranslationItem implements Arrayable
{
    private string $locale;
    private string $key;
    private ?string $value = null;
    private bool $isValueFromDatabase = false;
    private bool $isValueExistsInFile = false;
    private bool $isSame = false;
    private bool $isStub = false;

    public static function touch(string $locale): TranslationItem
    {
        $ins = new self();

        return $ins->setLocale($locale);
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return TranslationItem
     */
    public function setLocale(string $locale): TranslationItem
    {
        $this->locale = $locale;

        return $this;
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
     * @return TranslationItem
     */
    public function setKey(string $key): TranslationItem
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string|null $value
     * @return TranslationItem
     */
    public function setValue(?string $value): TranslationItem
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isValueFromDatabase(): bool
    {
        return $this->isValueFromDatabase;
    }

    /**
     * @param bool $isValueFromDatabase
     * @return TranslationItem
     */
    public function setIsValueFromDatabase(bool $isValueFromDatabase): TranslationItem
    {
        $this->isValueFromDatabase = $isValueFromDatabase;

        return $this;
    }

    /**
     * @return bool
     */
    public function isValueExistsInFile(): bool
    {
        return $this->isValueExistsInFile;
    }

    /**
     * @param bool $isValueExistsInFile
     * @return TranslationItem
     */
    public function setIsValueExistsInFile(bool $isValueExistsInFile): TranslationItem
    {
        $this->isValueExistsInFile = $isValueExistsInFile;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSame(): bool
    {
        return $this->isSame;
    }

    /**
     * @param bool $isSame
     * @return TranslationItem
     */
    public function setIsSame(bool $isSame): TranslationItem
    {
        $this->isSame = $isSame;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStub(): bool
    {
        return $this->isStub;
    }

    /**
     * @param bool $isStub
     * @return TranslationItem
     */
    public function setIsStub(bool $isStub): TranslationItem
    {
        $this->isStub = $isStub;

        return $this;
    }

    public function match(string $search): bool
    {
        $search = Str::lower($search);

        return Str::contains($this->getKey(), $search)
            || Str::of($this->getValue())->lower()->contains($search);
    }

    public function suggestRows(int $countOfLocales): float
    {
        $length = \Str::of($this->getValue())->length();

        return ceil($length / (160 / $countOfLocales)) ?: 1;
    }

    public function collect(): Collection
    {
        return collect($this->toArray());
    }

    public function toArray(): array
    {
        return [
            'locale' => $this->getLocale(),
            'value' => $this->getValue(),
            'value_from_db' => $this->isValueFromDatabase(),
            'exists_in_file' => $this->isValueExistsInFile(),
            'is_same' => $this->isSame(),
        ];
    }
}
