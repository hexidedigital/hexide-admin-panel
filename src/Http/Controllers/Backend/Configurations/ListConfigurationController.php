<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend\Configurations;

use App\Http\Controllers\Backend\BackendController;
use HexideDigital\HexideAdmin\Http\Requests\Backend\Configurations\ListUpdateRequest;
use HexideDigital\HexideAdmin\Models\AdminConfiguration;
use HexideDigital\HexideAdmin\Services\Backend\ListConfigurationService;
use Illuminate\Http\RedirectResponse;

class ListConfigurationController extends BackendController
{
    public function __construct()
    {
        parent::__construct();

        $this->withoutBreadcrumbs();

        $this->setResourceAccessMap();

        $this->setServiceClassName(ListConfigurationService::class);
        $this->setFromRequestClassName(ListUpdateRequest::class);
        $this->setModule(AdminConfiguration::class);
    }

    public function index()
    {
        $this->data([
            'admin_configuration_groups' => AdminConfiguration::joinTranslations()
                ->select([
                    'admin_configurations.*',
                    'admin_configuration_translations.text as text',
                ])
                ->with('translations')
                ->sorted()
                ->get()
                ->groupBy('group'),
        ]);

        return $this->render('list');
    }

    public function update(ListUpdateRequest $request, AdminConfiguration $admin_configuration): RedirectResponse
    {
        $service = $this->getService();

        $service->handleRequest($request, $admin_configuration);

        return redirect()->to(
            route('admin.admin_configurations.list.index', ['active_tab' => $admin_configuration->group])
            . '#form_' . $admin_configuration->id
        );
    }
}
