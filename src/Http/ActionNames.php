<?php

namespace HexideDigital\HexideAdmin\Http;

final class ActionNames
{
    /* follow locale file in lang/__/messages.php' */

    public const Action = 'action';
    public const Index = 'index';
    public const Show = 'show';
    public const Edit = 'edit';
    public const Create = 'create';
    public const Delete = 'delete';
    public const Restore = 'restore';
    public const ForceDelete = 'force_delete';

    public const ALLOWED = [
        self::Action,
        self::Index,
        self::Show,
        self::Edit,
        self::Create,
        self::Delete,
        self::Restore,
        self::ForceDelete,
    ];

    public static function isAllowed(?string $action): bool
    {
        return !empty($action) && in_array($action, self::ALLOWED);
    }

    private function __construct()
    {
    }
}
