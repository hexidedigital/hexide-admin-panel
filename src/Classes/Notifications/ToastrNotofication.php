<?php

namespace HexideDigital\HexideAdmin\Classes\Notifications;

class ToastrNotofication implements NotificationInterface
{
    public function notify($message, $type = null, $title = null, $options = [])
    {
        return toastr($message, $type, $title, $options);
    }
}
