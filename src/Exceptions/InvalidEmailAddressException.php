<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Exceptions;

class InvalidEmailAddressException extends EmailUtilitiesException
{
    public static function forAddress(string $emailAddress): self
    {
        return new self('The value ['.$emailAddress.'] could not be parsed as an email address.');
    }
}
