<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Lists;

enum ListType: string
{
    case DisposableDomains = 'disposable-domains';
    case RoleAccounts = 'role-accounts';

    /**
     * The path to the copy of this list that ships with the package.
     */
    public function bundledPath(): string
    {
        return match ($this) {
            self::DisposableDomains => __DIR__.'/../../lists/disposable-domains.json',
            self::RoleAccounts => __DIR__.'/../../lists/role-accounts.json',
        };
    }

    /**
     * The config key (within the "email-utilities" file) holding this list's
     * file path for the "file" storage driver.
     */
    public function filePathConfigKey(): string
    {
        return match ($this) {
            self::DisposableDomains => 'disposable_email_list_path',
            self::RoleAccounts => 'role_accounts_list_path',
        };
    }
}
