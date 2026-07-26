<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Contracts;

use AshAllenDesign\EmailUtilities\Exceptions\ListStorageException;
use AshAllenDesign\EmailUtilities\Lists\ListType;

interface ListStore
{
    /**
     * Get the stored list, or null if this store does not hold the list. A
     * null return causes the package to fall back to its bundled copy.
     *
     * @return list<string>|null
     */
    public function get(ListType $listType): ?array;

    /**
     * Store the list.
     *
     * @param list<string> $items
     * @throws ListStorageException
     */
    public function put(ListType $listType, array $items): void;

    /**
     * Assert that this store is configured so that "put" can succeed. Throws
     * before any work (such as fetching a fresh list) is carried out.
     *
     * @throws ListStorageException
     */
    public function ensureCanStore(ListType $listType): void;

    /**
     * A human-readable description of where the list is stored, for
     * console output.
     */
    public function location(ListType $listType): string;
}
