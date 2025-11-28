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

    /**
     * @return list<string>
     * @throws FileNotFoundException
     */
    public static function get(): array
    {
        /** @var string $listLocation */
        $listLocation = config('email-utilities.disposable_email_list_path')
            ?: __DIR__.'/../../lists/disposable-domains.json';

        return static::$patternsCache ??= array_values(File::json($listLocation));
    }

    public static function flushPatternsCache(): void
    {
        self::$patternsCache = null;
    }
}
