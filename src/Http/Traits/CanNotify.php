<?php

namespace HexideDigital\HexideAdmin\Http\Traits;

use HexideDigital\HexideAdmin\Classes\ActionNames;
use HexideDigital\HexideAdmin\Classes\Notifications\NotificationInterface;

trait CanNotify
{
    protected function setNotifier(NotificationInterface $notification)
    {
        $this->notificator = $notification;
    }

    protected function notifyIfExistsErrors(string $action = '', string $message = '')
    {
        if (!empty(request()->old()) && $this->show_error_notification) {
            $this->notify($action, $message, 'error');
        }
    }

    protected function notify(string $action = '', string $message = null, string $type = 'success', string $title = '', array $options = []): \Yoeunes\Toastr\Toastr
    {
        if(!ActionNames::isAllowed($action)){
            $action = ActionNames::DEFAULT;
        }

        if (empty($title)) {
            $title = __("hexide_admin::messages.$type.title");
        }

        if (empty($message)) {
            if (in_array($type, ['error', 'success'])) {
                $message = __("hexide_admin::messages.$type.$action",
                    ['model' => trans_choice("models.{$this->getModuleName()}.name", 1)]
                );
            }
        }

        return $this->notificator->notify($message, $type, $title, $options);
    }
}
