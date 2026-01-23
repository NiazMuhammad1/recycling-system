<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionItemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function bulkStore(Request $request, Collection $collection)
    {
        $data = $request->validate([
            'qty' => 'required|integer|min:1|max:500',
            'category_id' => 'required|exists:categories,id',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'product_model_id' => 'nullable|exists:product_models,id',
            'serial_number' => 'nullable|string|max:255',
            'asset_tags' => 'nullable|string|max:255',
            'dimensions' => 'nullable|string|max:255',
            'weight_kg' => 'nullable|numeric|min:0',
            'erasure_required' => 'nullable|boolean',
        ]);

        $qty = (int)$data['qty'];

        DB::transaction(function () use ($qty, $data, $collection) {
            for ($i = 0; $i < $qty; $i++) {
                $collection->items()->create([
                    'category_id' => $data['category_id'],
                    'manufacturer_id' => $data['manufacturer_id'] ?? null,
                    'product_model_id' => $data['product_model_id'] ?? null,
                    'serial_number' => $data['serial_number'] ?? null,
                    'asset_tags' => $data['asset_tags'] ?? null,
                    'dimensions' => $data['dimensions'] ?? null,
                    'weight_kg' => $data['weight_kg'] ?? 0,
                    'erasure_required' => (bool)($data['erasure_required'] ?? false),

                    // workflow
                    'status' => 'created',
                ]);
            }
        });

        return back()->with('success', "{$qty} item(s) added.");
    }
}
