<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities;

use AshAllenDesign\EmailUtilities\Contracts\ListStore;
use AshAllenDesign\EmailUtilities\Stores\CacheListStore;
use AshAllenDesign\EmailUtilities\Stores\DatabaseListStore;
use AshAllenDesign\EmailUtilities\Stores\DiskListStore;
use AshAllenDesign\EmailUtilities\Stores\FileListStore;
use Illuminate\Support\Manager;
use InvalidArgumentException;

class ListStoreManager extends Manager
{
    public function getDefaultDriver(): string
    {
        $driver = $this->config->get('email-utilities.storage.driver');

        return is_string($driver) && $driver !== '' ? $driver : 'file';
    }

    /**
     * Get a list store driver instance, verified against the contract so that
     * drivers registered via "extend" are checked too.
     */
    public function store(?string $driver = null): ListStore
    {
        $store = $this->driver($driver);

        if (! $store instanceof ListStore) {
            throw new InvalidArgumentException(
                sprintf('List store driver [%s] must implement [%s].', $driver ?? $this->getDefaultDriver(), ListStore::class),
            );
        }

        return $store;
    }

    public function createFileDriver(): FileListStore
    {
        return new FileListStore();
    }

    public function createDiskDriver(): DiskListStore
    {
        return new DiskListStore();
    }

    public function createCacheDriver(): CacheListStore
    {
        return new CacheListStore();
    }

    public function createDatabaseDriver(): DatabaseListStore
    {
        return new DatabaseListStore();
    }
}
