<?php

namespace HexideDigital\HexideAdmin\Classes;

abstract class ActionNames
{
    /* follow locale file in lang/__/messages.php' */

    public const DEFAULT = 'action';
    public const INDEX = 'index';
    public const SHOW = 'show';
    public const EDIT = 'edit';
    public const CREATE = 'create';
    public const DELETE = 'delete';

    public const ALLOWED = [
        self::DEFAULT,
        self::INDEX,
        self::SHOW,
        self::EDIT,
        self::CREATE,
        self::DELETE,
    ];

    public static function isAllowed(?string $action): bool
    {
        return !empty($action) && in_array($action, self::ALLOWED);
    }
}
