<?php

namespace HexideDigital\HexideAdmin\Policies;

use HexideDigital\HexideAdmin\Models\AdminConfiguration;

class ConfigurationPolicy extends DefaultPolicy
{
    protected function module(): string
    {
        return (new AdminConfiguration())->getTable();
        // return 'admin_configurations';
    }
}
