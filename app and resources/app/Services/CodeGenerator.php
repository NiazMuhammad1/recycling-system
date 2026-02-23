<?php

namespace App\Services;

use App\Models\CodeCounter;
use Illuminate\Support\Facades\DB;

class CodeGenerator
{
    /**
     * Generate next code from a named counter, with prefix and left padding.
     * Example: next('collections', 'J', 5) -> J16003 (if value becomes 16003)
     */
    public static function next(string $key, string $prefix, int $pad = 5): string
    {
        $nextValue = DB::transaction(function () use ($key) {
            // Lock row for update so concurrent requests don't duplicate.
            $counter = CodeCounter::where('key', $key)->lockForUpdate()->first();

            if (!$counter) {
                $counter = CodeCounter::create(['key' => $key, 'value' => 0]);
                // Need to lock the newly created row in this transaction for safety
                $counter = CodeCounter::where('key', $key)->lockForUpdate()->first();
            }

            $counter->value = $counter->value + 1;
            $counter->save();

            return $counter->value;
        });

        return $prefix . str_pad((string)$nextValue, $pad, '0', STR_PAD_LEFT);
    }

    /**
     * Generate item code for a collection: J16003-01, J16003-02, ...
     * Uses max existing suffix for that collection.
     */
    public static function nextCollectionItemCode(string $collectionCode, int $nextIndex): string
    {
        return $collectionCode . '-' . str_pad((string)$nextIndex, 2, '0', STR_PAD_LEFT);
    }
}
