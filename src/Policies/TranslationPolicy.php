<?php

namespace HexideDigital\HexideAdmin\Policies;

use HexideDigital\HexideAdmin\Models\Translation;

class TranslationPolicy extends DefaultPolicy
{
    protected function module(): string
    {
        return module_name_from_model(new Translation);
    }
}
