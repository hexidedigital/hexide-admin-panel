<?php

namespace HexideDigital\HexideAdmin\Classes\Notifications;

interface NotificationInterface
{
    public function notify($message, $type, $title, $options);
}
