<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Rules;

use AshAllenDesign\EmailUtilities\Email;
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
     * @param string $value
     * @param Closure(string, ?string=): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ((new Email($value))->domainIs($this->patterns)) {
            $fail('The :attribute domain is not allowed.');
        }
    }

    public static function disposable(): self
    {
        return new self(patterns: DisposableDomainList::get());
    }
}
