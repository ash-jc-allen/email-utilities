<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Stores;

use AshAllenDesign\EmailUtilities\Contracts\ListStore;
use AshAllenDesign\EmailUtilities\Exceptions\ListStorageException;
use AshAllenDesign\EmailUtilities\Lists\ListType;
use Illuminate\Support\Facades\File;

class FileListStore implements ListStore
{
    public function get(ListType $listType): ?array
    {
        $path = $this->configuredPath($listType);

        if ($path === null) {
            return null;
        }

        return array_values(File::json($path));
    }

    public function put(ListType $listType, array $items): void
    {
        $this->ensureCanStore($listType);

        /** @var string $path */
        $path = $this->configuredPath($listType);

        File::put($path, json_encode($items, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
    }

    public function ensureCanStore(ListType $listType): void
    {
        if ($this->configuredPath($listType) === null) {
            throw ListStorageException::missingFilePath($listType);
        }
    }

    public function location(ListType $listType): string
    {
        return $this->configuredPath($listType) ?? $listType->bundledPath();
    }

    public function configuredPath(ListType $listType): ?string
    {
        $path = config('email-utilities.'.$listType->filePathConfigKey());

        return is_string($path) && $path !== '' ? $path : null;
    }
}
