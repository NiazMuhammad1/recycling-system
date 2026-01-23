<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ManufacturerSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Apple','Dell','HP','Lenovo','Acer','Asus','Microsoft','Samsung',
            'Toshiba','Fujitsu','Intel','Cisco','HPE','IBM','Sony','LG'
        ];

        foreach ($names as $n) {
            DB::table('manufacturers')->updateOrInsert(
                ['name' => $n],
                ['is_active' => 1, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
