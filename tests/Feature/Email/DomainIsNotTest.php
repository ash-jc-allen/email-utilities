<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Tests\Feature\Email;

use AshAllenDesign\EmailUtilities\Email;
use AshAllenDesign\EmailUtilities\Tests\Feature\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;

class DomainIsNotTest extends TestCase
{
    #[TestWith(['fails@example.com', ['test.com']])]
    #[TestWith(['fails@example.com', ['example']])]
    #[Test]
    public function true_is_returned_if_the_domain_does_not_match_a_pattern(string $email, array $patterns): void
    {
        $this->assertTrue(new Email($email)->domainIsNot($patterns));
    }

    #[TestWith(['assertTrue@example.com', ['example.com', 'test.com']])]
    #[TestWith(['assertTrue@example.com', ['*']])]
    #[TestWith(['assertTrue@example.com', ['*example.com']])]
    #[TestWith(['assertTrue@subdomain.example.com', ['*ex*le.com']])]
    #[TestWith(['assertTrue@subdomain.example.com', ['*example.com']])]
    #[TestWith(['assertTrue@subdomain.example.com', ['*.example.com']])]
    #[TestWith(['assertTrue@example.com', ['example*']])]
    #[Test]
    public function false_is_returned_if_the_domain_does_match_a_pattern(string $email, array $patterns): void
    {
        $this->assertFalse(new Email($email)->domainIsNot($patterns));
    }
}
