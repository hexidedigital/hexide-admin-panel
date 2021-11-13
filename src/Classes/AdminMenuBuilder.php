<?php

namespace HexideDigital\HexideAdmin\Classes;

use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Str;

class AdminMenuBuilder
{

    public static function setUpLocaleMenu(BuildingMenu &$event)
    {
        $sub_menu = [];
        $locales = config('hexide_admin.locales');

        if(sizeof($locales) > 1) {
            foreach ($locales as $locale) {
                $sub_menu[] = [
                    'text' => Str::upper($locale) . ($locale == app()->getLocale() ? ' *' : ''),
                    'route' => ['admin.locale', ['locale' => $locale]],
                    'icon' => 'mr-2 flag-icon flag-icon-' . static::iconLocale($locale),
                ];
            }

            $event->menu->addAfter('admin_locale',
                [
                    'text' => '',
                    'icon' => 'fas fa-globe',
                    'topnav_right' => true,
                    'submenu' => $sub_menu
                ],
            );
        }
    }

    private static function iconLocale($locale)
    {
        switch ($locale) {
            case 'ru':
                $icon_locale = 'ru';
                break;
            case 'uk':
                $icon_locale = 'ua';
                break;
            case 'en':
                $icon_locale = 'gb';
                break;
            default:
                $icon_locale = $locale;
        }
        return $icon_locale;
    }

}
