<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Manufacturer;
use App\Models\ProductModel;

class AjaxController extends Controller
{
    public function manufacturersByCategory(Category $category)
    {
        // if you ALSO store category_id on manufacturers, filter directly:
        // $mans = Manufacturer::where('category_id', $category->id)->orderBy('name')->get();

        // better: derive manufacturers from models that belong to this category:
        $mans = Manufacturer::whereHas('models', function ($q) use ($category) {
                $q->where('category_id', $category->id);
            })
            ->orderBy('name')
            ->get(['id','name']);

        return $mans->map(fn($m) => ['id'=>$m->id, 'text'=>$m->name]);
    }

    public function modelsByManufacturer(Manufacturer $manufacturer)
    {
        $categoryId = request('category_id'); // optional filter

        $q = ProductModel::where('manufacturer_id', $manufacturer->id)
            ->orderBy('name');

        if ($categoryId) {
            $q->where('category_id', $categoryId);
        }

        return $q->get(['id','name'])->map(fn($m)=>['id'=>$m->id,'text'=>$m->name]);
    }
}
