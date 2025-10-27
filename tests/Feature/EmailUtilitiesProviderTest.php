<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Tests\Feature;

use AshAllenDesign\EmailUtilities\EmailUtilitiesProvider;
use AshAllenDesign\EmailUtilities\Exceptions\ValidationException;
use PHPUnit\Framework\Attributes\Test;

class EmailUtilitiesProviderTest extends TestCase
{
    #[Test]
    public function package_can_boot_if_the_config_is_invalid_but_config_validation_is_disabled(): void
    {
        config([
            'email-utilities.validate_config' => false,
            'email-utilities.disposable_email_list_path' => ['INVALID'],
        ]);

        $this->getProvider()->boot();
    }

    #[Test]
    public function package_cannot_boot_if_the_config_is_invalid_and_config_validation_is_enabled(): void
    {
        $this->expectException(ValidationException::class);

        config([
            'email-utilities.validate_config' => true,
            'email-utilities.disposable_email_list_path' => ['INVALID'],
        ]);

        $this->getProvider()->boot();
    }

    private function getProvider(): EmailUtilitiesProvider
    {
        $provider = $this->app?->register(EmailUtilitiesProvider::class);

        $this->assertInstanceOf(EmailUtilitiesProvider::class, $provider);

        return $provider;
    }
}
