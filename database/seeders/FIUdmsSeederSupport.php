<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Schema;

trait FIUdmsSeederSupport
{
    protected function onlyExistingColumns(string $table, array $payload): array
    {
        if (! Schema::hasTable($table)) {
            return [];
        }

        $columns = Schema::getColumnListing($table);

        return array_intersect_key($payload, array_flip($columns));
    }

    protected function nowColumns(string $table): array
    {
        $payload = [];

        if (Schema::hasColumn($table, 'created_at')) {
            $payload['created_at'] = now();
        }

        if (Schema::hasColumn($table, 'updated_at')) {
            $payload['updated_at'] = now();
        }

        return $payload;
    }
}
