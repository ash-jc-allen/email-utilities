<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Lists;

use AshAllenDesign\EmailUtilities\ListStoreManager;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

class RoleAccountList
{
    /**
     * Holds the contents of the role accounts list. This means we only read
     * the list once instead of on every call to get the list.
     *
     * @var list<string>|null
     */
    protected static ?array $cachedList;

    /**
     * Get the list from the configured store, falling back to the copy
     * bundled with the package if the store does not hold it.
     *
     * @return list<string>
     * @throws FileNotFoundException
     */
    public static function get(): array
    {
        return static::$cachedList ??= app(ListStoreManager::class)->store()->get(ListType::RoleAccounts)
            ?? array_values(File::json(ListType::RoleAccounts->bundledPath()));
    }

    public static function flushCachedList(): void
    {
        self::$cachedList = null;
    }
}
