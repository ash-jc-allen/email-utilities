<?php

declare(strict_types=1);

namespace AshAllenDesign\EmailUtilities\Stores;

use AshAllenDesign\EmailUtilities\Contracts\ListStore;
use AshAllenDesign\EmailUtilities\Lists\ListType;
use AshAllenDesign\EmailUtilities\Stores\Concerns\CastsStoredLists;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class DatabaseListStore implements ListStore
{
    use CastsStoredLists;

    public function get(ListType $listType): ?array
    {
        $row = $this->table()->where('name', $listType->value)->first();

        if ($row === null || ! is_string($row->items)) {
            return null;
        }

        return $this->castToStringList(json_decode($row->items, associative: true));
    }

    public function put(ListType $listType, array $items): void
    {
        $this->table()->upsert(
            values: [[
                'name' => $listType->value,
                'items' => json_encode($items, JSON_THROW_ON_ERROR),
                'updated_at' => now(),
            ]],
            uniqueBy: ['name'],
            update: ['items', 'updated_at'],
        );
    }

    public function ensureCanStore(ListType $listType): void
    {
        //
    }

    public function location(ListType $listType): string
    {
        return "database table '".$this->tableName()."': name '".$listType->value."'";
    }

    protected function table(): Builder
    {
        $connection = config('email-utilities.storage.database.connection');

        return DB::connection(is_string($connection) && $connection !== '' ? $connection : null)
            ->table($this->tableName());
    }

    protected function tableName(): string
    {
        $table = config('email-utilities.storage.database.table');

        return is_string($table) && $table !== '' ? $table : 'email_utilities_lists';
    }
}
