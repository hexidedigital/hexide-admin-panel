<?php

namespace HexideDigital\HexideAdmin\Http\ViewComposers;

use HexideDigital\HexideAdmin\Classes\HexideAdmin;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HexideAdminComposer
{
    protected HexideAdmin $hexideAdmin;

    public function __construct(HexideAdmin $hexideAdmin)
    {
        $this->hexideAdmin = $hexideAdmin;
    }

    public function compose(View $view)
    {
        $data = [
            'hexide_admin' => $this->hexideAdmin,
            'toggle_attributes' => $this->getToggleAttributes(),
        ];

        if (!$view->offsetExists('locales')) {
            $data['locales'] = config('translatable.locales');
        }

        if (!$view->offsetExists('module')) {
            $data['module'] = 'shared';
        }

        \View::share($data);
        $view->with($data);
    }

    public function getToggleAttributes(): Collection
    {
        return collect(config('hexide-admin.toggle.styles'));
    }
}
