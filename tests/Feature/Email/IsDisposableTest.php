<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Tests\Feature\Email;

use AshAllenDesign\EmailUtilities\Email;
use AshAllenDesign\EmailUtilities\Tests\Feature\TestCase;
use PHPUnit\Framework\Attributes\Test;

class IsDisposableTest extends TestCase
{
    #[Test]
    public function true_is_returned_if_the_email_address_is_disposable(): void
    {
        $this->assertTrue(
            (new Email('hello@0-mail.com'))->isDisposable()
        );
    }

    #[Test]
    public function false_is_returned_if_the_email_address_is_not_disposable(): void
    {
        $this->assertFalse(
            (new Email('hello@0-mail.co.uk'))->isDisposable()
        );
    }
}
