<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Manufacturer;
use Illuminate\Http\Request;
class CatalogAjaxController extends Controller
{
    public function manufacturers(Request $request)
    {
        $q = trim((string)$request->query('q', ''));

        $items = Manufacturer::query()
            ->where('is_active', 1)
            ->when($q, fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($m) => [
                'id' => (string) $m->id,
                'text' => $m->name,
            ])
            ->values();

        return response()->json(['results' => $items]);
    }

    public function models(Request $request, Manufacturer $manufacturer)
    {
        $q = trim((string)$request->query('q', ''));
        $categoryId = $request->query('category_id');

        $items = $manufacturer->productModels()
            ->where('is_active', 1)
            ->when($categoryId, fn($qq) => $qq->where('category_id', $categoryId))
            ->when($q, fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($m) => [
                'id' => (string) $m->id,
                'text' => $m->name,
            ])
            ->values();

        return response()->json(['results' => $items]);
    }
}
