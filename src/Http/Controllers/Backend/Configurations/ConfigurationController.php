<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend\Configurations;

use HexideDigital\HexideAdmin\Classes\Configurations\Configuration;
use HexideDigital\HexideAdmin\Http\Controllers\Backend\HexideAdminBaseController;
use HexideDigital\HexideAdmin\Http\Requests\Backend\Configurations\BaseRequest;
use HexideDigital\HexideAdmin\Models\AdminConfiguration;
use HexideDigital\HexideAdmin\Http\ViewNames;
use HexideDigital\HexideAdmin\Services\Backend\Configurations\ConfigurationService;

class ConfigurationController extends HexideAdminBaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->setFullAccessMap();

        $this->setModelClassName(AdminConfiguration::class);
        $this->setModuleName('admin_configurations');
        $this->setServiceClassName(ConfigurationService::class);
        $this->setService(new ConfigurationService());
        $this->setFromRequestClassName(BaseRequest::class);
    }

    protected function render(?string $view = null, array $data = [], string $forceActionType = null)
    {
        if (in_array($view, [ViewNames::Create, ViewNames::Edit])) {
            $types = collect(\App::make(Configuration::class)::getTypes())
                ->mapWithKeys(fn($type) => [
                    $type => __('models.admin_configurations.type.' . $type),
                ]);

            $groups = AdminConfiguration::select()->groupBy('group')->pluck('group', 'group')->toArray();

            $this->data(compact('types', 'groups'));
        }

        return parent::render($view, $data, $forceActionType);
    }
}
