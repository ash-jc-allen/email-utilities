<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Lists;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

class DisposableDomainList
{
    /**
     * Holds the contents of the disposable domains list file. This means we only
     * read and parse the file once instead of on every call to get the list.
     *
     * @var list<string>|null
     */
    protected static ?array $cachedList;

    public static function getListPath(): string
    {
        /** @var string $path */
        $path =  config('email-utilities.disposable_email_list_path') ?: static::defaultListPath();

        return $path;
    }

    public static function defaultListPath(): string
    {
        return __DIR__.'/../../lists/disposable-domains.json';
    }

    /**
     * @return list<string>
     * @throws FileNotFoundException
     */
    public static function get(): array
    {
        return static::$cachedList ??= array_values(File::json(self::getListPath()));
    }

    public static function flushCachedList(): void
    {
        self::$cachedList = null;
    }
}
