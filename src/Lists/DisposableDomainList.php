<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Lists;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

class DisposableDomainList
{
    /**
     * @return string[]
     * @throws FileNotFoundException
     */
    public static function get(): array
    {
        $listLocation = config('email-utilities.disposable_email_list_path') ?: __DIR__.'/../../lists/disposable-domains.json';

        return File::json($listLocation);
    }
}
