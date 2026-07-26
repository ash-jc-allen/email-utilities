<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Tests\Feature\Stores;

use AshAllenDesign\EmailUtilities\Lists\ListType;
use AshAllenDesign\EmailUtilities\Stores\DatabaseListStore;
use AshAllenDesign\EmailUtilities\Tests\Feature\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class DatabaseListStoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('email_utilities_lists', function (Blueprint $table): void {
            $table->string('name')->primary();
            $table->json('items');
            $table->timestamp('updated_at')->nullable();
        });
    }

    #[Test]
    public function null_is_returned_if_the_list_is_not_in_the_table(): void
    {
        $this->assertNull((new DatabaseListStore())->get(ListType::DisposableDomains));
    }

    #[Test]
    public function list_can_be_stored_and_retrieved(): void
    {
        $store = new DatabaseListStore();

        $store->put(ListType::DisposableDomains, ['disposable.com', 'throwaway.com']);

        $this->assertSame(['disposable.com', 'throwaway.com'], $store->get(ListType::DisposableDomains));
        $this->assertSame(1, DB::table('email_utilities_lists')->count());
    }

    #[Test]
    public function storing_a_list_again_updates_the_existing_row(): void
    {
        $store = new DatabaseListStore();

        $store->put(ListType::DisposableDomains, ['disposable.com']);
        $store->put(ListType::DisposableDomains, ['throwaway.com']);

        $this->assertSame(['throwaway.com'], $store->get(ListType::DisposableDomains));
        $this->assertSame(1, DB::table('email_utilities_lists')->count());
    }

    #[Test]
    public function lists_are_stored_under_separate_rows(): void
    {
        $store = new DatabaseListStore();

        $store->put(ListType::DisposableDomains, ['disposable.com']);
        $store->put(ListType::RoleAccounts, ['admin']);

        $this->assertSame(['disposable.com'], $store->get(ListType::DisposableDomains));
        $this->assertSame(['admin'], $store->get(ListType::RoleAccounts));
        $this->assertSame(2, DB::table('email_utilities_lists')->count());
    }

    #[Test]
    public function custom_table_name_is_used(): void
    {
        Schema::create('custom_lists', function (Blueprint $table): void {
            $table->string('name')->primary();
            $table->json('items');
            $table->timestamp('updated_at')->nullable();
        });

        config(['email-utilities.storage.database.table' => 'custom_lists']);

        $store = new DatabaseListStore();

        $store->put(ListType::DisposableDomains, ['disposable.com']);

        $this->assertSame(1, DB::table('custom_lists')->count());
        $this->assertSame(['disposable.com'], $store->get(ListType::DisposableDomains));
    }
}
