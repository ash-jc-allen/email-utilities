<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Tests\Feature\Lists;

use AshAllenDesign\EmailUtilities\Lists\DisposableDomainList;
use AshAllenDesign\EmailUtilities\Tests\Feature\TestCase;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use TypeError;

class DisposableDomainListTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DisposableDomainList::flushCachedList();
    }

    #[Test]
    public function default_disposable_domains_are_loaded_correctly(): void
    {
        config(['email-utilities.disposable_email_list_path' => null]);

        $this->assertCount(4749, DisposableDomainList::get());
    }

    #[Test]
    public function custom_disposable_domains_are_loaded_correctly(): void
    {
        File::put('./tests/Feature/Lists/disposable-domains-test.json', json_encode(['customdomain.com', 'hellodomain.com']));

        config(['email-utilities.disposable_email_list_path' => './tests/Feature/Lists/disposable-domains-test.json']);

        $this->assertCount(2, DisposableDomainList::get());
    }

    #[Test]
    public function exception_is_thrown_if_the_custom_list_does_not_exist(): void
    {
        $this->expectException(FileNotFoundException::class);

        config(['email-utilities.disposable_email_list_path' => './invalid-path.json']);

        DisposableDomainList::get();
    }

    #[Test]
    public function exception_is_thrown_if_the_list_is_not_valid_json(): void
    {
        $this->expectException(TypeError::class);

        File::put('./tests/Feature/Lists/disposable-domains-test.json', 'NOT VALID JSON');

        config(['email-utilities.disposable_email_list_path' => './tests/Feature/Lists/disposable-domains-test.json']);

        DisposableDomainList::get();
    }

    #[Test]
    public function disposable_patterns_are_loaded_only_once_per_request(): void
    {
        // Create disposable domain file for this test
        File::put(
            $path = './tests/Feature/Lists/disposable-domains-test.json',
            json_encode(['customdomain.com', 'hellodomain.com'], JSON_PRETTY_PRINT)
        );

        config(['email-utilities.disposable_email_list_path' => $path]);

        // 1st call → should read the JSON file and patternsCache
        $this->assertSame(
            ['customdomain.com', 'hellodomain.com'],
            DisposableDomainList::get()
        );

        // Delete the JSON file — if the rule tries to reload it, it will fail
        $this->assertTrue(File::delete($path));

        // 2nd and 3rd calls MUST succeed → they must use the cached patterns
        $this->assertSame(
            ['customdomain.com', 'hellodomain.com'],
            DisposableDomainList::get()
        );

        $this->assertSame(
            ['customdomain.com', 'hellodomain.com'],
            DisposableDomainList::get()
        );
    }

    protected function tearDown(): void
    {
        File::delete('./tests/Feature/Lists/disposable-domains-test.json');
        DisposableDomainList::flushCachedList();

        parent::tearDown();
    }
}
