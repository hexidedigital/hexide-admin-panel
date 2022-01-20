<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

use App\Models\Translation;
use App\Services\Backend\TranslationsService;
use HexideDigital\HexideAdmin\Http\ActionNames;
use HexideDigital\HexideAdmin\Http\Requests\Backend\TranslationUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TranslationController extends HexideAdminBaseController
{
    private int $perPage = 30;

    public function __construct(Request $request)
    {
        parent::__construct();

        $this->withoutBreadcrumbs();

        $this->initModule(Translation::class);

        $service = new TranslationsService($request->route('group'));
        $this->setService($service);
    }

    public function index(Request $request)
    {
        /** @var TranslationsService $service */
        $service = $this->getService();

        $page = $request->get('page', 1);
        $group = $service->getGroup();

        $list = $service->getPaginatedList(
            $this->perPage,
            $page,
            [
                'path' => route('admin.' . $this->getModuleName() . '.index', $group),
                'query' => [],
            ]
        );

        $this->data([
            'locales' => $service->getLocales(),
            'list' => $list,
            'group' => $group,
            'page' => $page,
            'page_title' => trans('labels.translation_group_' . $group),
        ]);

        $this->notifyIfExistsErrors(ActionNames::Edit);

        $request->flush();

        return $this->render();
    }

    /** @throws \Throwable */
    public function update(TranslationUpdateRequest $request): RedirectResponse
    {
        /** @var TranslationsService $service */
        $service = $this->getService();

        $service->updateTranslations($request);

        $request->flush();

        return $this->redirect('index', [
            'group' => $service->getGroup(),
            'page' => $request->input('page', 1),
        ]);
    }
}
