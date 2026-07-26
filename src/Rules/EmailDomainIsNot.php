<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Rules;

use AshAllenDesign\EmailUtilities\Email;
use AshAllenDesign\EmailUtilities\Exceptions\InvalidEmailAddressException;
use AshAllenDesign\EmailUtilities\Lists\DisposableDomainList;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class EmailDomainIsNot implements ValidationRule
{
    /**
     * @param list<string> $patterns
     */
    public function __construct(protected array $patterns)
    {
        //
    }

    /**
     * Run the validation rule.
     *
     * @param Closure(string, ?string=): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a valid email address.');

            return;
        }

        try {
            $email = new Email($value);
        } catch (InvalidEmailAddressException) {
            $fail('The :attribute must be a valid email address.');

            return;
        }

        if ($email->domainIs($this->patterns)) {
            $fail('The :attribute domain is not allowed.');
        }
    }

    public static function disposable(): self
    {
        return new self(patterns: DisposableDomainList::get());
    }
}
