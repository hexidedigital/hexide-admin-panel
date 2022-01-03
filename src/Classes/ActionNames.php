<?php

namespace HexideDigital\HexideAdmin\Classes;

abstract class ActionNames
{
    /* follow locale file in lang/__/messages.php' */

    public const Action = 'action';
    public const Index = 'index';
    public const Show = 'show';
    public const Edit = 'edit';
    public const Create = 'create';
    public const Delete = 'delete';

    public const ALLOWED = [
        self::Action,
        self::Index,
        self::Show,
        self::Edit,
        self::Create,
        self::Delete,
    ];

    public static function isAllowed(?string $action): bool
    {
        return !empty($action) && in_array($action, self::ALLOWED);
    }
}
