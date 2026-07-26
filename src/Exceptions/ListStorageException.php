<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Exceptions;

use AshAllenDesign\EmailUtilities\Lists\ListType;

class ListStorageException extends EmailUtilitiesException
{
    public static function missingFilePath(ListType $listType): self
    {
        $configKey = 'email-utilities.'.$listType->filePathConfigKey();

        return new self("The configuration '{$configKey}' is not set. Please set it to a valid file path.");
    }
}
