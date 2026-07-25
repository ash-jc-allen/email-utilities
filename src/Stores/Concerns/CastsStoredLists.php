<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Stores\Concerns;

trait CastsStoredLists
{
    /**
     * Cast a decoded payload to a list of strings. Returns null for anything
     * empty or unusable so that callers fall back to the bundled list rather
     * than validating against an empty one.
     *
     * @return list<string>|null
     */
    protected function castToStringList(mixed $items): ?array
    {
        if (! is_array($items)) {
            return null;
        }

        $list = array_values(array_filter($items, is_string(...)));

        return $list === [] ? null : $list;
    }
}
