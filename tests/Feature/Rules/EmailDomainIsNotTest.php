<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Tests\Feature\Rules;

use AshAllenDesign\EmailUtilities\Rules\EmailDomainIsNot;
use AshAllenDesign\EmailUtilities\Tests\Feature\TestCase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;

class EmailDomainIsNotTest extends TestCase
{
    #[TestWith(['fails@example.com', ['test.com']])]
    #[TestWith(['fails@example.com', ['example']])]
    #[Test]
    public function rule_passes_if_the_domain_does_not_match_a_pattern(string $email, array $patterns): void
    {
        (new EmailDomainIsNot($patterns))->validate(
            attribute: 'email',
            value: $email,
            fail: fn () => $this->fail('The domain matched when it should not have.'),
        );

        // If we reach here it means the validation passed.
        $this->assertTrue(true);
    }

    #[TestWith(['assertTrue@example.com', ['example.com', 'test.com']])]
    #[TestWith(['assertTrue@example.com', ['*']])]
    #[TestWith(['assertTrue@example.com', ['*example.com']])]
    #[TestWith(['assertTrue@subdomain.example.com', ['*ex*le.com']])]
    #[TestWith(['assertTrue@subdomain.example.com', ['*example.com']])]
    #[TestWith(['assertTrue@subdomain.example.com', ['*.example.com']])]
    #[TestWith(['assertTrue@example.com', ['example*']])]
    #[Test]
    public function rule_fails_if_the_domain_matches_a_pattern(string $email, array $patterns): void
    {
        (new EmailDomainIsNot($patterns))->validate(
            attribute: 'email',
            value: $email,
            fail: fn () => $this->assertTrue(true),
        );
    }

    #[Test]
    public function rule_fails_if_the_value_is_a_disposable_email(): void
    {
        EmailDomainIsNot::disposable()->validate(
            attribute: 'email',
            value: 'hello@0-mail.com',
            fail: fn () => $this->assertTrue(true),
        );
    }

    #[Test]
    public function disposable_patterns_are_loaded_only_once_per_request(): void
    {
        EmailDomainIsNot::flushPatternsCache();

        // Create disposable domain file for this test
        File::put(
            $path = './tests/Feature/Lists/disposable-domains-test.json',
            json_encode(['customdomain.com', 'hellodomain.com'], JSON_PRETTY_PRINT)
        );

        config(['email-utilities.disposable_email_list_path' => $path]);

        // Below logic is much simpler than mocking DisposableDomainList::get() ...

        // 1st call → should read the JSON file and patternsCache
        EmailDomainIsNot::disposable();

        // Delete the JSON file — if the rule tries to reload it, it will fail
        $this->assertTrue(File::delete($path));

        // 2nd and 3rd calls MUST succeed → they must use the cached patterns
        EmailDomainIsNot::disposable();
        EmailDomainIsNot::disposable();

        // If we reached here, caching worked
        $this->assertTrue(true);
    }
}
