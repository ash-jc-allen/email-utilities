<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Tests\Feature\Email;

use AshAllenDesign\EmailUtilities\Email;
use AshAllenDesign\EmailUtilities\Exceptions\InvalidEmailAddressException;
use AshAllenDesign\EmailUtilities\Tests\Feature\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;

class EmailTest extends TestCase
{
    #[Test]
    public function object_can_be_created_correctly(): void
    {
        $email = new Email('hello@example.com');

        $this->assertSame('hello@example.com', $email->address());
        $this->assertSame('hello', $email->localPart());
        $this->assertSame('example.com', $email->domain());
    }

    #[Test]
    public function domain_is_split_on_the_last_at_symbol(): void
    {
        $email = new Email('"hello@world"@example.com');

        $this->assertSame('"hello@world"', $email->localPart());
        $this->assertSame('example.com', $email->domain());
    }

    #[TestWith(['not-an-email'])]
    #[TestWith([''])]
    #[TestWith(['@example.com'])]
    #[TestWith(['hello@'])]
    #[TestWith(['@'])]
    #[Test]
    public function exception_is_thrown_if_the_address_cannot_be_parsed(string $emailAddress): void
    {
        $this->expectException(InvalidEmailAddressException::class);
        $this->expectExceptionMessage('The value ['.$emailAddress.'] could not be parsed as an email address.');

        new Email($emailAddress);
    }
}
