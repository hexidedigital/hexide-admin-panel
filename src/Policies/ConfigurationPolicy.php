<?php

namespace HexideDigital\HexideAdmin\Policies;

use HexideDigital\HexideAdmin\Models\AdminConfiguration;

class ConfigurationPolicy extends DefaultPolicy
{
    protected function module(): string
    {
        return module_name_from_model(new AdminConfiguration);
    }
}
