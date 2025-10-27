<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Lists;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

class RoleAccountList
{
    /**
     * @return list<string>
     * @throws FileNotFoundException
     */
    public static function get(): array
    {
        /** @var string $listLocation */
        $listLocation = config('email-utilities.role_accounts_list_path') ?: __DIR__.'/../../lists/role-accounts.json';

        return array_values(File::json($listLocation));
    }
}
