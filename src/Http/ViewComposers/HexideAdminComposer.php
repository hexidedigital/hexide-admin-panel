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
        if(!$view->offsetExists('locales')){
            $view->offsetSet('locales', config('translatable.locales'));
        }

        $view->with([
            'hexideAdmin' => $this->hexideAdmin,
            'breadcrumbs' => $this->hexideAdmin->getBreadcrumbs()->get(),
            'toggle_attributes' => $this->getToggleAttributes(),
        ]);
    }

    public function getToggleAttributes(): Collection
    {
        return collect([
            'status' => [
                'data-on' => '<i class="fas fa-eye"></i>',
                'data-off' => '<i class="fas fa-eye-slash"></i>',
                'data-onstyle' => 'success',
                'data-offstyle' => 'secondary',
                'data-width' => '75',
                'data-size' => 'small',
                'class' => 'toggle_attributes',
            ],
            'state_read' => [
                'data-on' => '<i class="far fa-envelope-open"></i>',
                'data-off' => '<i class="far fa-envelope"></i>',
                'data-onstyle' => 'default',
                'data-offstyle' => 'primary',
                'class' => 'toggle_attributes',
            ],
        ]);
    }

}
