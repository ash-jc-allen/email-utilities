<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Tests\Feature\Email;

use AshAllenDesign\EmailUtilities\Email;
use AshAllenDesign\EmailUtilities\Tests\Feature\TestCase;
use PHPUnit\Framework\Attributes\Test;

class IsRoleAccountTest extends TestCase
{
    #[Test]
    public function true_is_returned_if_the_email_address_is_for_a_role_account(): void
    {
        $this->assertTrue(
            (new Email('sales@example.com'))->isRoleAccount()
        );
    }

    #[Test]
    public function false_is_returned_if_the_email_address_is_for_a_role_account(): void
    {
        $this->assertFalse(
            (new Email('mail-123@example.com'))->isRoleAccount()
        );
    }
}
