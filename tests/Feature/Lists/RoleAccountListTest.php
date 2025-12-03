<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Tests\Feature\Lists;

use AshAllenDesign\EmailUtilities\Lists\RoleAccountList;
use AshAllenDesign\EmailUtilities\Tests\Feature\TestCase;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use TypeError;

class RoleAccountListTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        RoleAccountList::flushCachedList();
    }

    #[Test]
    public function default_role_accounts_are_loaded_correctly(): void
    {
        config(['email-utilities.role_accounts_list_path' => null]);

        $this->assertCount(8, RoleAccountList::get());
    }

    #[Test]
    public function custom_disposable_domains_are_loaded_correctly(): void
    {
        File::put('./tests/Feature/Lists/role-accounts-list-test.json', json_encode(['admin', 'support']));

        config(['email-utilities.role_accounts_list_path' => './tests/Feature/Lists/role-accounts-list-test.json']);

        $this->assertCount(2, RoleAccountList::get());
    }

    #[Test]
    public function exception_is_thrown_if_the_custom_list_does_not_exist(): void
    {
        $this->expectException(FileNotFoundException::class);

        config(['email-utilities.role_accounts_list_path' => './invalid-path.json']);

        RoleAccountList::get();
    }

    #[Test]
    public function exception_is_thrown_if_the_list_is_not_valid_json(): void
    {
        $this->expectException(TypeError::class);

        File::put('./tests/Feature/Lists/role-accounts-list-test.json', 'NOT VALID JSON');

        config(['email-utilities.role_accounts_list_path' => './tests/Feature/Lists/role-accounts-list-test.json']);

        RoleAccountList::get();
    }
    #[Test]
    public function role_list_is_loaded_only_once_per_request(): void
    {
        config(['email-utilities.role_accounts_list_path' => './tests/Feature/Lists/role-accounts-list-test.json']);

        File::put(
            $path = './tests/Feature/Lists/role-accounts-list-test.json',
            json_encode(['admin', 'support']),
        );

        // 1st call → should read the JSON file and patternsCache
        $this->assertSame(
            ['admin', 'support'],
            RoleAccountList::get(),
        );

        // Delete the JSON file — if the rule tries to reload it, it will fail
        $this->assertTrue(File::delete($path));

        // 2nd and 3rd calls MUST succeed → they must use the cached patterns
        $this->assertSame(
            ['admin', 'support'],
            RoleAccountList::get(),
        );

        $this->assertSame(
            ['admin', 'support'],
            RoleAccountList::get(),
        );
    }


    protected function tearDown(): void
    {
        File::delete('./tests/Feature/Lists/role-accounts-list-test.json');
        RoleAccountList::flushCachedList();

        parent::tearDown();
    }
}
