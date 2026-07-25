<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Stores;

use AshAllenDesign\EmailUtilities\Contracts\ListStore;
use AshAllenDesign\EmailUtilities\Lists\ListType;
use AshAllenDesign\EmailUtilities\Stores\Concerns\CastsStoredLists;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

class DiskListStore implements ListStore
{
    use CastsStoredLists;

    public function get(ListType $listType): ?array
    {
        $contents = $this->disk()->get($this->path($listType));

        if ($contents === null) {
            return null;
        }

        return $this->castToStringList(json_decode($contents, associative: true));
    }

    public function put(ListType $listType, array $items): void
    {
        $this->disk()->put(
            $this->path($listType),
            json_encode($items, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT),
        );
    }

    public function ensureCanStore(ListType $listType): void
    {
        //
    }

    public function location(ListType $listType): string
    {
        $disk = $this->diskName();

        return "disk '".($disk ?? 'default')."': ".$this->path($listType);
    }

    protected function disk(): Filesystem
    {
        return Storage::disk($this->diskName());
    }

    protected function diskName(): ?string
    {
        $disk = config('email-utilities.storage.disk.disk');

        return is_string($disk) && $disk !== '' ? $disk : null;
    }

    protected function path(ListType $listType): string
    {
        $path = config('email-utilities.storage.disk.'.$listType->filePathConfigKey());

        return is_string($path) && $path !== ''
            ? $path
            : 'email-utilities/'.$listType->value.'.json';
    }
}
