<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Tests\Feature\Stores;

use AshAllenDesign\EmailUtilities\Lists\ListType;
use AshAllenDesign\EmailUtilities\Stores\DiskListStore;
use AshAllenDesign\EmailUtilities\Tests\Feature\TestCase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

class DiskListStoreTest extends TestCase
{
    #[Test]
    public function null_is_returned_if_the_list_is_not_on_the_disk(): void
    {
        Storage::fake();

        $this->assertNull((new DiskListStore())->get(ListType::DisposableDomains));
    }

    #[Test]
    public function list_can_be_stored_and_retrieved(): void
    {
        Storage::fake();

        $store = new DiskListStore();

        $store->put(ListType::DisposableDomains, ['disposable.com', 'throwaway.com']);

        Storage::assertExists('email-utilities/disposable-domains.json');

        $this->assertSame(['disposable.com', 'throwaway.com'], $store->get(ListType::DisposableDomains));
    }

    #[Test]
    public function named_disk_and_custom_path_are_used(): void
    {
        Storage::fake('remote');

        config([
            'email-utilities.storage.disk.disk' => 'remote',
            'email-utilities.storage.disk.disposable_email_list_path' => 'custom/blocklist.json',
        ]);

        $store = new DiskListStore();

        $store->put(ListType::DisposableDomains, ['disposable.com']);

        Storage::disk('remote')->assertExists('custom/blocklist.json');

        $this->assertSame(['disposable.com'], $store->get(ListType::DisposableDomains));
    }

    #[Test]
    public function null_is_returned_if_the_stored_file_is_not_valid_json(): void
    {
        Storage::fake();

        Storage::put('email-utilities/disposable-domains.json', 'NOT VALID JSON');

        $this->assertNull((new DiskListStore())->get(ListType::DisposableDomains));
    }
}
