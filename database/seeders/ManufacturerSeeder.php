<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Manufacturer;
class ManufacturerSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Apple','Dell','HP','Lenovo','Acer','Asus','Microsoft','Samsung'] as $name) {
            Manufacturer::firstOrCreate(['name' => $name]);
        }
    }
}
