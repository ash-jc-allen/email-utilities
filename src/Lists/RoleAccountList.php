<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Lists;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

class RoleAccountList
{
    public static function getListPath(): string
    {
        return config('email-utilities.role_accounts_list_path')
            ?: __DIR__.'/../../lists/role-accounts.json';
    }

    /**
     * @return list<string>
     * @throws FileNotFoundException
     */
    public static function get(): array
    {
        return File::json(self::getListPath());
    }
}
