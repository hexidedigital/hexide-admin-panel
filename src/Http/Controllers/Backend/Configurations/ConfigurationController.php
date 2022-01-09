<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend\Configurations;

use App\Http\Controllers\Backend\BackendController;
use HexideDigital\HexideAdmin\Http\Requests\Backend\Configurations\BaseRequest;
use HexideDigital\HexideAdmin\Models\AdminConfiguration;

class ConfigurationController extends BackendController
{
    public function __construct()
    {
        parent::__construct();

        $this->setFullAccessMap();

        $this->setFromRequestClassName(BaseRequest::class);
        $this->setModule(AdminConfiguration::class);


        $types = [];
        foreach (AdminConfiguration::getTypes() as $type) {
            $types[$type] = __('models.admin_configurations.type.' . $type);
        }

        $groups = AdminConfiguration::select()->groupBy('group')->pluck('group', 'group')->toArray();

        $this->data(compact('types', 'groups'));
    }
}
