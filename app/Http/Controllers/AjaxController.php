<?php 

namespace App\Http\Controllers;

use App\Models\{Category, Manufacturer, ProductModel};
use Illuminate\Http\Request;

class AjaxController extends Controller
{
   
    public function manufacturersByCategory(Request $request, Category $category)
    {
        $q = trim((string)$request->query('q', ''));

        $mans = Manufacturer::query()
            ->when($q, fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->get(['id','name']);

        return response()->json([
            'results' => $mans->map(fn($m) => [
                'id' => (string)$m->id,
                'text' => $m->name,
            ]),
        ]);
    }
   public function modelsByManufacturer(Request $request, Manufacturer $manufacturer)
{
    $categoryId = $request->query('category_id');
    $q = trim((string)$request->query('q', ''));

    $models = ProductModel::query()
        ->where('manufacturer_id', $manufacturer->id)
        ->when($categoryId, fn($qq) => $qq->where('category_id', $categoryId))
        ->when($q, fn($qq) => $qq->where('name', 'like', "%{$q}%"))
        ->orderBy('name')
        ->get(['id','name']);

    return response()->json([
        'results' => $models->map(fn($m) => [
            'id' => (string)$m->id,
            'text' => $m->name,
        ]),
    ]);
}
    public function storeManufacturer(Request $request)
    {
        $data = $request->validate(['name'=>'required|string|max:120']);
        $m = Manufacturer::firstOrCreate(['name' => trim($data['name'])]);

        return response()->json(['id'=>$m->id,'text'=>$m->name]);
    }

    public function storeProductModel(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'category_id' => 'required|exists:categories,id',
            'manufacturer_id' => 'required|exists:manufacturers,id',
        ]);

        $pm = ProductModel::firstOrCreate([
            'name' => trim($data['name']),
            'category_id' => $data['category_id'],
            'manufacturer_id' => $data['manufacturer_id'],
        ]);

        return response()->json(['id'=>$pm->id,'text'=>$pm->name]);
    }
}
