<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Tests\Feature\Stores;

use AshAllenDesign\EmailUtilities\Lists\ListType;
use AshAllenDesign\EmailUtilities\Stores\CacheListStore;
use AshAllenDesign\EmailUtilities\Tests\Feature\TestCase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;

class CacheListStoreTest extends TestCase
{
    #[Test]
    public function null_is_returned_if_the_list_is_not_cached(): void
    {
        $this->assertNull((new CacheListStore())->get(ListType::DisposableDomains));
    }

    #[Test]
    public function list_can_be_stored_and_retrieved(): void
    {
        $store = new CacheListStore();

        $store->put(ListType::DisposableDomains, ['disposable.com', 'throwaway.com']);

        $this->assertSame(['disposable.com', 'throwaway.com'], $store->get(ListType::DisposableDomains));
        $this->assertSame(['disposable.com', 'throwaway.com'], Cache::get('email-utilities:disposable-domains'));
    }

    #[Test]
    public function lists_are_stored_under_separate_keys(): void
    {
        $store = new CacheListStore();

        $store->put(ListType::DisposableDomains, ['disposable.com']);
        $store->put(ListType::RoleAccounts, ['admin']);

        $this->assertSame(['disposable.com'], $store->get(ListType::DisposableDomains));
        $this->assertSame(['admin'], $store->get(ListType::RoleAccounts));
    }

    #[Test]
    public function custom_prefix_is_used(): void
    {
        config(['email-utilities.storage.cache.prefix' => 'my-app']);

        (new CacheListStore())->put(ListType::DisposableDomains, ['disposable.com']);

        $this->assertSame(['disposable.com'], Cache::get('my-app:disposable-domains'));
    }

    #[Test]
    public function null_is_returned_if_the_cached_value_is_not_a_list_of_strings(): void
    {
        Cache::forever('email-utilities:disposable-domains', 'not-a-list');

        $this->assertNull((new CacheListStore())->get(ListType::DisposableDomains));
    }

    #[Test]
    public function null_is_returned_if_the_cached_list_is_empty(): void
    {
        Cache::forever('email-utilities:disposable-domains', []);

        $this->assertNull((new CacheListStore())->get(ListType::DisposableDomains));
    }
}
