<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities;

use AshAllenDesign\EmailUtilities\Lists\DisposableDomainList;
use AshAllenDesign\EmailUtilities\Lists\RoleAccountList;
use Illuminate\Support\Str;

class Email
{
    protected(set) string $localPart;

    protected(set) string $domain;

    public function __construct(protected(set) string $emailAddress)
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
        return in_array(
            needle: strtolower($this->localPart),
            haystack: RoleAccountList::get(),
            strict: true,
        );
    }

    /**
     * @param list<string> $patterns
     */
    public function domainIs(array $patterns): bool
    {
        return Str::is($patterns, $this->domain);
    }

    /**
     * @param list<string> $patterns
     */
    public function domainIsNot(array $patterns): bool
    {
        return ! $this->domainIs($patterns);
    }
}
