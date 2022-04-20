<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Http\Controllers\Backend;

use HexideDigital\HexideAdmin\Models\Translation;
use HexideDigital\HexideAdmin\Services\Backend\TranslationsService;
use HexideDigital\HexideAdmin\Http\ActionNames;
use HexideDigital\HexideAdmin\Http\Requests\Backend\TranslationUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TranslationController extends HexideAdminBaseController
{
    public function __construct(Request $request)
    {
        parent::__construct();

        $this->withoutBreadcrumbs();

        $this->setModelClassName(Translation::class);
        $this->setModuleName();
        $this->setServiceClassName(TranslationsService::class);
        $this->setService(new TranslationsService($request->route('group')));
        $this->setFromRequestClassName(TranslationUpdateRequest::class);
    }

    public function index(Request $request)
    {
        /** @var TranslationsService $service */
        $service = $this->getService();

        $this->data([
            'locales' => $service->getLocales(),
            'group' => $service->getGroup(),
            'page' => $request->input('page', 1),
        ]);

        $this->notifyIfExistsErrors(ActionNames::Edit);

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
