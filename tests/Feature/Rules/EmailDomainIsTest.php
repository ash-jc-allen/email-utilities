<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Tests\Feature\Rules;

use AshAllenDesign\EmailUtilities\Rules\EmailDomainIs;
use AshAllenDesign\EmailUtilities\Tests\Feature\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;

class EmailDomainIsTest extends TestCase
{
    #[TestWith(['assertTrue@example.com', ['example.com', 'test.com']])]
    #[TestWith(['assertTrue@example.com', ['*']])]
    #[TestWith(['assertTrue@example.com', ['*example.com']])]
    #[TestWith(['assertTrue@subdomain.example.com', ['*ex*le.com']])]
    #[TestWith(['assertTrue@subdomain.example.com', ['*example.com']])]
    #[TestWith(['assertTrue@subdomain.example.com', ['*.example.com']])]
    #[TestWith(['assertTrue@example.com', ['example*']])]
    #[Test]
    public function rule_passes_if_the_domain_matches_a_pattern(string $email, array $patterns): void
    {
        (new EmailDomainIs($patterns))->validate(
            attribute: 'email',
            value: $email,
            fail: fn () => $this->fail('The domain did not match when it should have.'),
        );

        // If we reach here it means the validation passed.
        $this->assertTrue(true);
    }

    #[TestWith(['fails@example.com', ['test.com']])]
    #[TestWith(['fails@example.com', ['example']])]
    #[Test]
    public function rule_fails_if_the_domain_does_not_match_a_pattern(string $email, array $patterns): void
    {
        (new EmailDomainIs($patterns))->validate(
            attribute: 'email',
            value: $email,
            fail: fn () => $this->assertTrue(true),
        );
    }

    #[TestWith(['not-an-email'])]
    #[TestWith([''])]
    #[TestWith(['@example.com'])]
    #[TestWith(['hello@'])]
    #[Test]
    public function rule_fails_gracefully_if_the_value_cannot_be_parsed_as_an_email(string $value): void
    {
        $failed = false;

        (new EmailDomainIs(['example.com']))->validate(
            attribute: 'email',
            value: $value,
            fail: function (string $message) use (&$failed): void {
                $failed = true;

                $this->assertSame('The :attribute must be a valid email address.', $message);
            },
        );

        $this->assertTrue($failed);
    }

    #[TestWith([null])]
    #[TestWith([123])]
    #[TestWith([['hello@example.com']])]
    #[Test]
    public function rule_fails_gracefully_if_the_value_is_not_a_string(mixed $value): void
    {
        $failed = false;

        (new EmailDomainIs(['example.com']))->validate(
            attribute: 'email',
            value: $value,
            fail: function () use (&$failed): void {
                $failed = true;
            },
        );

        $this->assertTrue($failed);
    }
}
