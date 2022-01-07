<?php

namespace HexideDigital\HexideAdmin\Components\Tabs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\View\Component;
use Illuminate\View\View;

class TabsComponent extends Component
{
    public ?Model $model;
    public string $module;
    public array $locales;

    public bool $showLocaleTabs;
    public bool $showGeneralTab;
    public bool $showTabPanel;
    public array $generalTabErrors;

    public $generalViewPath;
    public $localeViewPath;

    /**
     * @param bool $showLocaleTabs
     * @param bool $showGeneralTab
     * @param bool $showTabPanel
     * @param array|null $generalTabErrors
     * @param View|string|null $generalViewPath
     * @param View|string|null $localeViewPath
     */
    public function __construct(
        bool  $showLocaleTabs = null,
        bool  $showGeneralTab = null,
        bool  $showTabPanel = null,
        array $generalTabErrors = null,
              $generalViewPath = null,
              $localeViewPath = null
    )
    {
        $shared = \View::getShared();;

        $this->model = Arr::get($shared, 'model');
        $this->module = Arr::get($shared, 'module');
        $this->locales = Arr::get($shared, 'locales', []);

        $this->showLocaleTabs = $showLocaleTabs ?? false;
        $this->showGeneralTab = $showGeneralTab ?? true;
        $this->showTabPanel = $this->showLocaleTabs || ($showTabPanel ?? false);
        $this->generalTabErrors = array_merge(['slug', 'position', 'status'], $generalTabErrors ?? []);

        $this->generalViewPath = $generalViewPath;
        $this->localeViewPath = $localeViewPath;
    }

    public function localeView(): string
    {
        return $this->localeViewPath ?: "admin.view.$this->module.tabs.locale";
    }

    public function generalView(): string
    {
        return $this->generalViewPath ?: "admin.view.$this->module.tabs.general";
    }

    public function getIconForLocale($locale): string
    {
        switch ($locale) {
//            case 'ru': $icon_locale = 'ru'; break;
            case 'uk':
                $icon_locale = 'ua';
                break;
            case 'en':
                $icon_locale = 'us';
                break;
            default:
                $icon_locale = $locale;
        }

        return $icon_locale;
    }

    public function render()
    {
        return view('hexide-admin::components.tabs.tabs-component');
    }
}
