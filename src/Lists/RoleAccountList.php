<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Lists;

class RoleAccountList
{
    /**
     * @var list<string>
     */
    public static array $roleAccountList = [
        'admin',
        'administrator',
        'contact',
        'info',
        'sales',
        'support',
        'help',
        'office',
    ];

    /**
     * @return list<string>
     */
    public static function get(): array
    {
        return self::$roleAccountList;
    }
}
