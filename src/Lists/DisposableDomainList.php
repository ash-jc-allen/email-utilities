<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Lists;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

class DisposableDomainList
{
    /**
     * Cache the patterns, so we don't load/parse the disposable domains file multiple times per request.
     */
    protected static ?array $patternsCache;

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
        return static::$patternsCache ??= array_values(File::json(self::getListPath()));
    }

    public static function flushPatternsCache(): void
    {
        self::$patternsCache = null;
    }
}
