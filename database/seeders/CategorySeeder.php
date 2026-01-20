<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'PCs',
            'Laptops',
            'Monitors',
            'Servers',
            'Networking',
            'Printers',
            'Phones',
            'Projectors',
            'Components',
            'Hard Drives',
            'Other',
        ];

        foreach ($categories as $name) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'is_active' => true
                ]
            );
        }
    }
}
