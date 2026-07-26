<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities;

use AshAllenDesign\EmailUtilities\Exceptions\InvalidEmailAddressException;
use AshAllenDesign\EmailUtilities\Lists\DisposableDomainList;
use AshAllenDesign\EmailUtilities\Lists\RoleAccountList;
use Illuminate\Support\Str;

class Email
{
    protected string $localPart;

    protected string $domain;

    /**
     * @throws InvalidEmailAddressException
     */
    public function __construct(protected string $emailAddress)
    {
        // Split on the last "@" rather than the first so that addresses with a
        // quoted local part (e.g. "hello@world"@example.com) resolve to the
        // correct domain.
        $atPosition = strrpos($emailAddress, '@');

        if ($atPosition === false || $atPosition === 0 || $atPosition === strlen($emailAddress) - 1) {
            throw InvalidEmailAddressException::forAddress($emailAddress);
        }

        $this->localPart = substr($emailAddress, 0, $atPosition);
        $this->domain = substr($emailAddress, $atPosition + 1);
    }

    public function localPart(): string
    {
        return $this->localPart;
    }

    public function domain(): string
    {
        return $this->domain;
    }

    public function address(): string
    {
        return $this->emailAddress;
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
