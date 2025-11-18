<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Lists;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

class DisposableDomainList
{
    /**
     * @return list<string>
     * @throws FileNotFoundException
     */
    public static function get(): array
    {
        /** @var string $listLocation */
        $listLocation = config('email-utilities.disposable_email_list_path') ?: __DIR__.'/../../lists/disposable-domains.txt';

        // Laravel-ish file($listLocation, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
        // File::lines() already applies SplFileObject::DROP_NEW_LINE flag, so we just need to filter out empty lines.
        return File::lines($listLocation)
            ->filter() // remove empty lines
            ->values()
            ->all();
    }
}
