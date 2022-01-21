<?php

namespace HexideDigital\HexideAdmin\Classes\Notifications;

class ToastrNotification implements NotificationInterface
{
    public function notify($message, $type = null, $title = null, $options = [])
    {
        return toastr($message, $type, $title, $options);
    }
}
