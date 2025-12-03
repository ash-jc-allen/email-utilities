<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Lists;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

class RoleAccountList
{
    /**
     * Holds the contents of the role accounts list file. This means we only read
     * and parse the file once instead of on every call to get the list.
     *
     * @var list<string>|null
     */
    protected static ?array $cachedList;

    /**
     * @return list<string>
     * @throws FileNotFoundException
     */
    public static function get(): array
    {
        /** @var string $listLocation */
        $listLocation = config('email-utilities.role_accounts_list_path') ?: __DIR__.'/../../lists/role-accounts.json';

        return static::$cachedList ??= array_values(File::json($listLocation));
    }

    public static function flushCachedList(): void
    {
        self::$cachedList = null;
    }
}
