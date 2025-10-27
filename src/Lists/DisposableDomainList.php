<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Lists;

use JsonException;

class DisposableDomainList
{
    /**
     * @return list<string>
     * @throws JsonException
     */
    public static function get(): array
    {
        $listLocation = config('email-utilities.disposable_email_list_path') ?: __DIR__.'/../../lists/disposable-domains.json';

        return json_decode(file_get_contents($listLocation), true, 512, JSON_THROW_ON_ERROR);
    }
}
