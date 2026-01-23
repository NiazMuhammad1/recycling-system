<?php 
namespace App\Services;

use Illuminate\Support\Facades\DB;

class NumberService
{
    public static function next(string $key, string $prefix, int $pad): string
    {
        return DB::transaction(function () use ($key, $prefix, $pad) {
            $row = DB::table('counters')->where('key', $key)->lockForUpdate()->first();

            if (!$row) {
                DB::table('counters')->insert([
                    'key' => $key,
                    'value' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $n = 1;
            } else {
                $n = $row->value + 1;
                DB::table('counters')->where('id', $row->id)->update([
                    'value' => $n,
                    'updated_at' => now(),
                ]);
            }

            return $prefix . str_pad((string)$n, $pad, '0', STR_PAD_LEFT);
        });
    }
}
