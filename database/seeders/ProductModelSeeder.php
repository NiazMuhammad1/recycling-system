<?php 
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductModelSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'Apple' => ['Mac','iMac','Mac mini','MacBook Pro','MacBook Air','iPad','iPhone'],
            'Dell'  => ['Optiplex','Latitude','Inspiron','Precision','PowerEdge'],
            'HP'    => ['EliteBook','ProBook','Pavilion','ZBook','ProDesk','EliteDesk'],
            'Lenovo'=> ['ThinkPad','ThinkCentre','IdeaPad','Yoga','Legion'],
            'Acer'  => ['Aspire','Swift','Predator','TravelMate'],
            'Asus'  => ['VivoBook','ZenBook','ROG','TUF'],
            'Microsoft' => ['Surface Pro','Surface Laptop','Surface Book'],
            'Samsung' => ['Galaxy Book','Galaxy Tab'],
        ];

        foreach ($map as $manufacturerName => $models) {
            $man = DB::table('manufacturers')->where('name', $manufacturerName)->first();
            if (!$man) continue;

            foreach ($models as $m) {
                DB::table('product_models')->updateOrInsert(
                    ['manufacturer_id' => $man->id, 'name' => $m],
                    ['is_active' => 1, 'updated_at' => now(), 'created_at' => now()]
                );
            }
        }
    }
}
