<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend\Configurations;

use HexideDigital\HexideAdmin\Classes\Configurations\Configuration;
use HexideDigital\HexideAdmin\Http\Controllers\Backend\HexideAdminBaseController;
use HexideDigital\HexideAdmin\Http\Requests\Backend\Configurations\BaseRequest;
use HexideDigital\HexideAdmin\Models\AdminConfiguration;

class ConfigurationController extends HexideAdminBaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->setFullAccessMap();

        $this->setFromRequestClassName(BaseRequest::class);
        $this->initModule(AdminConfiguration::class);


        $types = [];
        foreach (app(Configuration::class)::getTypes() as $type) {
            $types[$type] = __('models.admin_configurations.type.' . $type);
        }

        $groups = AdminConfiguration::select()->groupBy('group')->pluck('group', 'group')->toArray();

        $this->data(compact('types', 'groups'));
    }
}
