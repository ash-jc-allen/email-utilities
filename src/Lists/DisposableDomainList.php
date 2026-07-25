<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Lists;

use AshAllenDesign\EmailUtilities\ListStoreManager;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

class DisposableDomainList
{
    /**
     * Holds the contents of the disposable domains list. This means we only
     * read the list once instead of on every call to get the list.
     *
     * @var list<string>|null
     */
    protected static ?array $cachedList;

    /**
     * The path used by the "file" storage driver. Kept for backwards
     * compatibility; the store now owns path resolution.
     */
    public static function getListPath(): string
    {
        /** @var string $path */
        $path = config('email-utilities.disposable_email_list_path') ?: static::defaultListPath();

        return $path;
    }

    public static function defaultListPath(): string
    {
        return ListType::DisposableDomains->bundledPath();
    }

    /**
     * Get the list from the configured store, falling back to the copy
     * bundled with the package if the store does not hold it.
     *
     * @return list<string>
     * @throws FileNotFoundException
     */
    public static function get(): array
    {
        return static::$cachedList ??= app(ListStoreManager::class)->store()->get(ListType::DisposableDomains)
            ?? array_values(File::json(ListType::DisposableDomains->bundledPath()));
    }

    public static function flushCachedList(): void
    {
        self::$cachedList = null;
    }
}
