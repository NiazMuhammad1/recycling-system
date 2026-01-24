<?php
namespace App\Http\Controllers;


use App\Models\{Collection, CollectionItem, Manufacturer, ProductModel};
use App\Models\StockItem;
use App\Services\NumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CollectionItemController extends Controller
{
    // STEP 2: Edit items grid (screenshot #2)
    public function edit(Collection $collection)
    {
        $collection->load(['items.category','items.manufacturer','items.productModel']);
        $categories = \App\Models\Category::where('is_active',1)->orderBy('sort_order')->get();
        return view('collections.items.edit', compact('collection','categories'));
    }

    // Add many items when qty typed and button clicked
    public function bulkStore(Request $request, Collection $collection)
    {
        $data = $request->validate([
            'qty' => 'required|integer|min:1|max:500',
            'category_id' => 'required|exists:categories,id',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'product_model_id' => 'nullable|exists:product_models,id',
        ]);

        DB::transaction(function () use ($collection, $data) {
            for ($i=0; $i < (int)$data['qty']; $i++) {
                $collection->items()->create([
                    'qty' => 1,
                    'category_id' => $data['category_id'],
                    'manufacturer_id' => $data['manufacturer_id'] ?? null,
                    'product_model_id' => $data['product_model_id'] ?? null,
                    'status' => 'created',
                    'collected' => false,
                ]);
            }
        });

        return back()->with('success', $data['qty'].' item(s) added.');
    }

    // Save changes to grid rows
    public function update(Request $request, Collection $collection)
    {
        $rows = $request->input('items', []);

        DB::transaction(function () use ($rows, $collection) {
            foreach ($rows as $id => $r) {
                /** @var CollectionItem $item */
                $item = $collection->items()->where('id',$id)->first();
                if (!$item) continue;

                $item->update([
                    'qty' => (int)($r['qty'] ?? 1),
                    'category_id' => $r['category_id'] ?? $item->category_id,
                    'manufacturer_id' => $r['manufacturer_id'] ?? null,
                    'product_model_id' => $r['product_model_id'] ?? null,
                    'serial_number' => $r['serial_number'] ?? null,
                    'asset_tags' => $r['asset_tags'] ?? null,
                    'dimensions' => $r['dimensions'] ?? null,
                    'weight_kg' => $r['weight_kg'] ?? 0,
                    'erasure_required' => !empty($r['erasure_required']),
                ]);
            }
        });

        return back()->with('success','Items saved.');
    }

    // STEP 3: Collect form (screenshot #4)
    public function collectForm(Collection $collection)
    {
        $collection->load(['items.category','items.manufacturer','items.productModel']);
        return view('collections.items.collect', compact('collection'));
    }

    public function collectSave(Request $request, Collection $collection)
    {
        $ids = $request->input('collect_ids', []); // item IDs checked

        DB::transaction(function () use ($collection, $ids) {
            $now = now();

            $collection->items()
                ->whereIn('id', $ids)
                ->update([
                    'collected' => true,
                    'status' => 'collected',
                    'collected_at' => $now,
                ]);

            // if any items collected => collection becomes collected
            if ($collection->items()->where('collected', true)->exists()) {
                $collection->update([
                    'status' => 'collected',
                    'collected_at' => $collection->collected_at ?? $now,
                ]);
            }
        });

        return redirect()->route('collections.show',$collection)->with('success','Marked collected.');
    }

    // STEP 4: Process list (screenshot #5)
    public function processIndex(Collection $collection)
    {
        $collection->load(['items.category','items.manufacturer','items.productModel']);
        // show only items that are collected and not processed/stocked yet
        $items = $collection->items()
            ->whereIn('status',['collected','processing'])
            ->orderBy('item_number')
            ->get();

        return view('collections.items.process_index', compact('collection','items'));
    }

    // STEP 5: Process item (screenshot #6)
    public function processItemForm(Collection $collection, CollectionItem $item)
    {
        abort_unless($item->collection_id === $collection->id, 404);
        $item->load(['category','manufacturer','productModel','stockItem']);

        return view('collections.items.process_item', compact('collection','item'));
    }

    public function processItemSave(Request $request, Collection $collection, CollectionItem $item)
    {
        abort_unless($item->collection_id === $collection->id, 404);

        $data = $request->validate([
            'process_action' => 'required|in:add_to_stock,physical_destruction,recycle,resale',
            'item_valuation' => 'nullable|numeric|min:0',
            'refurb_cost' => 'nullable|numeric|min:0',
            'hdd_serial' => 'nullable|string|max:255',
            'weight_kg' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:255',
            'erasure_required' => 'nullable|boolean',

            'erasure_report' => 'nullable|file|max:5120', // 5MB
            // Stock fields when add_to_stock
            'warehouse_location' => 'nullable|string|max:50',
            'cosmetic_condition' => 'nullable|string|max:10',
            'condition_notes' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'fully_functional' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($collection, $item, $data, $request) {
            $path = $item->erasure_report_path;

            if ($request->hasFile('erasure_report')) {
                $path = $request->file('erasure_report')->store('erasure_reports','public');
            }

            $item->update([
                'process_action' => $data['process_action'],
                'item_valuation' => $data['item_valuation'] ?? 0,
                'refurb_cost' => $data['refurb_cost'] ?? 0,
                'hdd_serial' => $data['hdd_serial'] ?? null,
                'weight_kg' => $data['weight_kg'] ?? $item->weight_kg,
                'dimensions' => $data['dimensions'] ?? $item->dimensions,
                'erasure_required' => !empty($data['erasure_required']),
                'erasure_report_path' => $path,
            ]);

            // ACTION: add_to_stock
            if ($data['process_action'] === 'add_to_stock') {

                // create stock item if not created already
                if (!$item->stock_item_id) {
                    $stock = StockItem::create([
                        'stock_number' => NumberService::next('stock','S',7), // S1600003 style
                        'category_id' => $item->category_id,
                        'manufacturer_id' => $item->manufacturer_id,
                        'product_model_id' => $item->product_model_id,
                        'serial_number' => $item->serial_number,
                        'asset_tags' => $item->asset_tags,
                        'price' => $data['price'] ?? 0,
                        'warehouse_location' => $data['warehouse_location'] ?? null,
                        'cosmetic_condition' => $data['cosmetic_condition'] ?? null,
                        'condition_notes' => $data['condition_notes'] ?? null,
                        'fully_functional' => !empty($data['fully_functional']),
                        'status' => 'in_stock',
                        'source_collection_id' => $collection->id,
                        'source_collection_item_id' => $item->id,
                    ]);

                    $item->update([
                        'stock_item_id' => $stock->id,
                        'status' => 'added_to_stock',
                        'processed_at' => now(),
                    ]);
                } else {
                    $item->update([
                        'status' => 'added_to_stock',
                        'processed_at' => now(),
                    ]);
                }
            } else {
                // other actions -> processed
                $item->update([
                    'status' => 'processed',
                    'processed_at' => now(),
                ]);
            }

            // if no more collected items pending -> mark collection processed
            $pending = $collection->items()->whereIn('status',['collected','processing'])->exists();
            if (!$pending) {
                $collection->update([
                    'status' => 'processed',
                    'processed_at' => now(),
                ]);
            }
        });

        return redirect()->route('collections.process.index', $collection)->with('success','Item processed.');
    }

    public function updateGrid(Request $request, Collection $collection)
    {
        $data = $request->validate([
            'items' => 'array',
            'items.*.qty' => 'required|integer|min:1|max:500',
            'items.*.category_id' => 'required|exists:categories,id',
            'items.*.manufacturer_id' => 'nullable|exists:manufacturers,id',
            'items.*.manufacturer_text' => 'nullable|string|max:120',
            'items.*.product_model_id' => 'nullable|exists:product_models,id',
            'items.*.model_text' => 'nullable|string|max:120',
            'items.*.serial_number' => 'nullable|string|max:255',
            'items.*.asset_tags' => 'nullable|string|max:255',
            'items.*.dimensions' => 'nullable|string|max:255',
            'items.*.weight_kg' => 'nullable|numeric|min:0',
            'items.*.erasure_required' => 'nullable|boolean',

            'new_items' => 'array',
            'new_items.*.qty' => 'required|integer|min:1|max:500',
            'new_items.*.category_id' => 'required|exists:categories,id',
            'new_items.*.manufacturer_id' => 'nullable|exists:manufacturers,id',
            'new_items.*.manufacturer_text' => 'nullable|string|max:120',
            'new_items.*.product_model_id' => 'nullable|exists:product_models,id',
            'new_items.*.model_text' => 'nullable|string|max:120',
        ]);

        DB::transaction(function () use ($collection, $data) {

            // Update existing
            foreach (($data['items'] ?? []) as $id => $row) {
                /** @var CollectionItem $item */
                $item = $collection->items()->whereKey($id)->lockForUpdate()->firstOrFail();

                [$manId, $modelId] = $this->resolveManufacturerModel($row);

                $item->update([
                    'qty' => $row['qty'],
                    'category_id' => $row['category_id'],
                    'manufacturer_id' => $manId,
                    'product_model_id' => $modelId,
                    'serial_number' => $row['serial_number'] ?? null,
                    'asset_tags' => $row['asset_tags'] ?? null,
                    'dimensions' => $row['dimensions'] ?? null,
                    'weight_kg' => $row['weight_kg'] ?? 0,
                    'erasure_required' => (bool)($row['erasure_required'] ?? false),
                ]);
            }

            // Create new rows (added from top bar)
            foreach (($data['new_items'] ?? []) as $row) {
                [$manId, $modelId] = $this->resolveManufacturerModel($row);

                $collection->items()->create([
                    'qty' => $row['qty'],
                    'category_id' => $row['category_id'],
                    'manufacturer_id' => $manId,
                    'product_model_id' => $modelId,
                    'status' => 'created',
                    'collected' => false,
                ]);
            }

            // Renumber sequence -> creates J00001-001 style
            $this->renumberItems($collection);
        });

        return back()->with('success','Items saved.');
    }

    private function resolveManufacturerModel(array $row): array
    {
        $manId = $row['manufacturer_id'] ?? null;
        if (!$manId && !empty($row['manufacturer_text'])) {
            $m = Manufacturer::firstOrCreate(['name' => trim($row['manufacturer_text'])]);
            $manId = $m->id;
        }

        $modelId = $row['product_model_id'] ?? null;
        if (!$modelId && $manId && !empty($row['model_text'])) {
            $pm = ProductModel::firstOrCreate([
                'category_id' => $row['category_id'],
                'manufacturer_id' => $manId,
                'name' => trim($row['model_text']),
            ]);
            $modelId = $pm->id;
        }

        return [$manId, $modelId];
    }

    private function renumberItems(Collection $collection): void
    {
        $items = $collection->items()->orderBy('id')->get();

        $seq = 1;
        foreach ($items as $item) {
            $item->seq = $seq;
            $item->item_number = $collection->collection_number.'-'.str_pad((string)$seq, 3, '0', STR_PAD_LEFT);
            $item->save();
            $seq++;
        }
    }

    public function destroy(CollectionItem $item)
    {
        $collection = $item->collection;
        $item->delete();

        $this->renumberItems($collection);

        return back()->with('success','Item deleted.');
    }

}
