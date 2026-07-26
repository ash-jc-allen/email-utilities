<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Stores;

use AshAllenDesign\EmailUtilities\Contracts\ListStore;
use AshAllenDesign\EmailUtilities\Lists\ListType;
use AshAllenDesign\EmailUtilities\Stores\Concerns\CastsStoredLists;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;

class CacheListStore implements ListStore
{
    use CastsStoredLists;

    public function get(ListType $listType): ?array
    {
        return $this->castToStringList($this->cache()->get($this->key($listType)));
    }

    public function put(ListType $listType, array $items): void
    {
        $ttl = config('email-utilities.storage.cache.ttl');

        is_int($ttl)
            ? $this->cache()->put($this->key($listType), $items, $ttl)
            : $this->cache()->forever($this->key($listType), $items);
    }

    public function ensureCanStore(ListType $listType): void
    {
        //
    }

    public function location(ListType $listType): string
    {
        $store = $this->storeName();

        return "cache store '".($store ?? 'default')."': key '".$this->key($listType)."'";
    }

    protected function cache(): Repository
    {
        return Cache::store($this->storeName());
    }

    protected function storeName(): ?string
    {
        $store = config('email-utilities.storage.cache.store');

        return is_string($store) && $store !== '' ? $store : null;
    }

    protected function key(ListType $listType): string
    {
        $prefix = config('email-utilities.storage.cache.prefix');

        $prefix = is_string($prefix) && $prefix !== '' ? $prefix : 'email-utilities';

        return $prefix.':'.$listType->value;
    }
}
