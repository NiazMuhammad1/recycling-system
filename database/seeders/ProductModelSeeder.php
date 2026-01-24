<?php 
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{Category, Manufacturer, ProductModel};
class ProductModelSeeder extends Seeder
{
    public function run(): void
    {
        $pcs = Category::where('name','PCs')->first();
        $laptops = Category::where('name','Laptops')->first();

        $apple = Manufacturer::where('name','Apple')->first();
        $dell = Manufacturer::where('name','Dell')->first();

        if ($pcs && $apple) {
            ProductModel::firstOrCreate(['category_id'=>$pcs->id,'manufacturer_id'=>$apple->id,'name'=>'iMac']);
            ProductModel::firstOrCreate(['category_id'=>$pcs->id,'manufacturer_id'=>$apple->id,'name'=>'Mac Mini']);
        }

        if ($laptops && $dell) {
            ProductModel::firstOrCreate(['category_id'=>$laptops->id,'manufacturer_id'=>$dell->id,'name'=>'Latitude']);
            ProductModel::firstOrCreate(['category_id'=>$laptops->id,'manufacturer_id'=>$dell->id,'name'=>'XPS']);
        }
    }
}
