<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Tests\Feature\Email;

use AshAllenDesign\EmailUtilities\Email;
use AshAllenDesign\EmailUtilities\Tests\Feature\TestCase;
use PHPUnit\Framework\Attributes\Test;

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
}
