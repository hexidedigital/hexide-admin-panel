<?php

namespace HexideDigital\HexideAdmin\Components\NavItems;

use Illuminate\View\Component;
use Str;

class LanguageItem extends Component
{
    public array $locales;

    /** Create a new component instance. */
    public function __construct(array $locales = null)
    {
        $this->locales = $locales ?? config('hexide-admin.locales');
    }

    public function showLanguages(): bool
    {
        return sizeof($this->locales) > 1;
    }

    public function currentLocale(): string
    {
        return Str::lower(app()->getLocale());
    }

    public function isCurrentLocale(string $locale): bool
    {
        return $locale === $this->currentLocale();
    }

    public function localeIcon(string $locale): string
    {
        $prefix = 'flag-icon flag-icon-';

        switch ($locale) {
            case 'ru':
                $icon_locale = 'ru';
                break;
            case 'uk':
            case 'ua':
                $icon_locale = 'ua';
                break;
            case 'en':
                $icon_locale = 'us';
                break;
            default:
                $icon_locale = $locale;
        }

        return $prefix . $icon_locale;
    }

    public function currentLocaleIcon(): string
    {
        return $this->localeIcon($this->currentLocale());
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('hexide-admin::components.nav-items.language-item');
    }
}
