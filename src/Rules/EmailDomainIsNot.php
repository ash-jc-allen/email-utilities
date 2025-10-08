<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Rules;

use AshAllenDesign\EmailUtilities\Email;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class EmailDomainIsNot implements ValidationRule
{
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
        if (new Email($value)->domainIs($this->patterns)) {
            $fail('The :attribute domain is not allowed.');
        }
    }
}
