<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Lists;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

class DisposableDomainList
{
    public static function getListPath(): string
    {
        return config('email-utilities.disposable_email_list_path') ?: static::defaultListPath();
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
        return File::json(self::getListPath());
    }
}
