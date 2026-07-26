<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Tests\Feature;

use AshAllenDesign\EmailUtilities\Contracts\ListStore;
use AshAllenDesign\EmailUtilities\Lists\ListType;
use AshAllenDesign\EmailUtilities\ListStoreManager;
use AshAllenDesign\EmailUtilities\Stores\CacheListStore;
use AshAllenDesign\EmailUtilities\Stores\DatabaseListStore;
use AshAllenDesign\EmailUtilities\Stores\DiskListStore;
use AshAllenDesign\EmailUtilities\Stores\FileListStore;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;

class ListStoreManagerTest extends TestCase
{
    #[Test]
    public function file_driver_is_used_by_default(): void
    {
        $this->assertInstanceOf(FileListStore::class, app(ListStoreManager::class)->store());
    }

    #[Test]
    public function driver_is_resolved_from_the_config(): void
    {
        config(['email-utilities.storage.driver' => 'cache']);

        $this->assertInstanceOf(CacheListStore::class, app(ListStoreManager::class)->store());
    }

    #[Test]
    public function drivers_can_be_resolved_explicitly(): void
    {
        $manager = app(ListStoreManager::class);

        $this->assertInstanceOf(FileListStore::class, $manager->store('file'));
        $this->assertInstanceOf(DiskListStore::class, $manager->store('disk'));
        $this->assertInstanceOf(CacheListStore::class, $manager->store('cache'));
        $this->assertInstanceOf(DatabaseListStore::class, $manager->store('database'));
    }

    #[Test]
    public function exception_is_thrown_for_an_unknown_driver(): void
    {
        $this->expectException(InvalidArgumentException::class);

        app(ListStoreManager::class)->store('unknown');
    }

    #[Test]
    public function list_store_contract_resolves_the_configured_driver(): void
    {
        config(['email-utilities.storage.driver' => 'cache']);

        $this->assertInstanceOf(CacheListStore::class, app(ListStore::class));
    }

    #[Test]
    public function custom_drivers_can_be_registered(): void
    {
        $custom = new class () implements ListStore {
            public function get(ListType $listType): ?array
            {
                return ['custom.com'];
            }

            public function put(ListType $listType, array $items): void
            {
                //
            }

            public function ensureCanStore(ListType $listType): void
            {
                //
            }

            public function location(ListType $listType): string
            {
                return 'custom';
            }
        };

        $manager = app(ListStoreManager::class);

        $manager->extend('custom', fn (): ListStore => $custom);

        config(['email-utilities.storage.driver' => 'custom']);

        $this->assertSame(['custom.com'], $manager->store()->get(ListType::DisposableDomains));
    }
}
