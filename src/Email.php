<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities;

use Illuminate\Support\Str;

class Email
{
    protected(set) string $localPart;

    protected(set) string $domain;

    public function __construct(private string $emailAddress)
    {
        [$this->localPart, $this->domain] = explode('@', $emailAddress);
    }

    public function isDisposable(): bool
    {
        return in_array(
            needle: strtolower($this->domain),
            haystack: DisposableDomainList::get(),
            strict: true,
        );
    }

    public function isRoleAccount(): bool
    {
        $roleAccounts = [
            'admin',
            'administrator',
            'contact',
            'info',
            'sales',
            'support',
            'help',
            'office',
        ];

        return in_array(strtolower($this->localPart), $roleAccounts, true);
    }

    public function domainIs(array $patterns): bool
    {
        return Str::is($patterns, $this->domain);
    }

    public function domainIsNot(array $patterns): bool
    {
        return ! $this->domainIs($patterns);
    }
}
