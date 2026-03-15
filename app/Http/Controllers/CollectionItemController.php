<?php
namespace App\Http\Controllers;


use App\Models\{Collection, CollectionItem, Manufacturer, ProductModel};
use App\Models\StockItem;
use App\Services\NumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\CollectionItemCode;
class CollectionItemController extends Controller
{
    // STEP 2: Edit items grid (screenshot #2)
   
    public function edit(Collection $collection)
    {
        $mode = 'edit';
        $collection->load(['items.category', 'driver', 'user']);
        $driver = auth()->user();
        $categories = \App\Models\Category::query()
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
       

        return view('collections.items.edit', compact('collection', 'categories', 'mode'));
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
        $mode = 'collect';
        $collection->load(['items.category', 'driver','user']);
        $driver = auth()->user();
        $categories = \App\Models\Category::query()
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        // If collection doesn't already have saved driver signature, prefill from user

        return view('collections.items.edit', compact('collection', 'categories', 'mode'));
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
        $collection->load(['items.category','items.manufacturerRel','items.productModel']);
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
        $item->load(['manufacturerRel','productModel','hdds.manufacturerRel','hdds.productModel']);
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

            // existing hdds
            'hdds' => 'array',
            'hdds.*.manufacturer_id' => 'nullable',
            'hdds.*.manufacturer_text' => 'nullable|string|max:120',
            'hdds.*.product_model_id' => 'nullable',
            'hdds.*.model_text' => 'nullable|string|max:120',
            'hdds.*.serial' => 'nullable|string|max:255',
            'hdds.*.status' => 'nullable|string|max:30',
            'hdds.*.delete' => 'nullable|in:0,1',
            'hdds.*.erasure_report' => 'nullable|file|max:5120', // 5MB

            // new hdds
            'new_hdds' => 'array',
            'new_hdds.*.manufacturer_id' => 'nullable',
            'new_hdds.*.manufacturer_text' => 'nullable|string|max:120',
            'new_hdds.*.product_model_id' => 'nullable',
            'new_hdds.*.model_text' => 'nullable|string|max:120',
            'new_hdds.*.serial' => 'nullable|string|max:255',
            'new_hdds.*.status' => 'nullable|string|max:30',
            'new_hdds.*.erasure_report' => 'nullable|file|max:5120',
            'quantity' => 'nullable|integer|min:1',
            'carbon_footprint' => 'nullable|numeric|min:0',

            // HDD extra columns (from table)
            'hdds.*.size' => 'nullable|string|max:50',
            'hdds.*.notes' => 'nullable|string|max:255',
            'hdds.*.create_separate_stock_item' => 'nullable|boolean',

            'new_hdds.*.size' => 'nullable|string|max:50',
            'new_hdds.*.notes' => 'nullable|string|max:255',
            'new_hdds.*.create_separate_stock_item' => 'nullable|boolean',

            // Stock Item Details (screen)
            'sku' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:255',
            'asset_tags' => 'nullable|string|max:255',

            // Model Details (screen)
            'category_id' => 'nullable|integer',
            'manufacturer_id' => 'nullable|integer',
            'product_model_id' => 'nullable|integer',
            'model' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:50',
            'chassis' => 'nullable|string|max:50',

            // Specs (screen)
            'processor_manufacturer' => 'nullable|string|max:120',
            'processor_type' => 'nullable|string|max:120',
            'processor_speed_ghz' => 'nullable|numeric|min:0',

            'ram_type' => 'nullable|string|max:120',
            'ram_gb' => 'nullable|numeric|min:0',

            'hdd_gb' => 'nullable|numeric|min:0',
            'ssd_gb' => 'nullable|numeric|min:0',
            'nvme_gb' => 'nullable|numeric|min:0',

            'operating_system' => 'nullable|string|max:255',
            'optical_drives' => 'nullable|string|max:255',

            // Checkboxes (screen)
            'charger_included' => 'nullable|boolean',
            'accessories_included' => 'nullable|boolean',

            // Bottom section (screen)
            'product_images' => 'nullable|array',
            'product_images.*' => 'file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'notes' => 'nullable|string',

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
                        'sku' => $data['sku'] ?? null,
                        'serial_number' => $data['serial_number'] ?? ($item->serial_number ?? null),
                        'asset_tags' => $data['asset_tags'] ?? ($item->asset_tags ?? null),

                        'category_id' => $data['category_id'] ?? $item->category_id,
                        'manufacturer_id' => $data['manufacturer_id'] ?? $item->manufacturer_id,
                        'product_model_id' => $data['product_model_id'] ?? $item->product_model_id,
                        'model' => $data['model'] ?? null,
                        'year' => $data['year'] ?? null,
                        'chassis' => $data['chassis'] ?? null,

                        'processor_manufacturer' => $data['processor_manufacturer'] ?? null,
                        'processor_type' => $data['processor_type'] ?? null,
                        'processor_speed_ghz' => $data['processor_speed_ghz'] ?? null,

                        'ram_type' => $data['ram_type'] ?? null,
                        'ram_gb' => $data['ram_gb'] ?? null,

                        'hdd_gb' => $data['hdd_gb'] ?? null,
                        'ssd_gb' => $data['ssd_gb'] ?? null,
                        'nvme_gb' => $data['nvme_gb'] ?? null,

                        'operating_system' => $data['operating_system'] ?? null,
                        'optical_drives' => $data['optical_drives'] ?? null,

                        'charger_included' => !empty($data['charger_included']),
                        'accessories_included' => !empty($data['accessories_included']),

                        'notes' => $data['notes'] ?? null,
                    ]);

                    $item->update([
                        'stock_item_id' => $stock->id,
                        'status' => 'add_to_stock',
                        'processed_at' => now(),
                        'quantity' => $data['quantity'] ?? $item->quantity,
                        'carbon_footprint' => $data['carbon_footprint'] ?? $item->carbon_footprint,
                    ]);
                } else {
                    if ($item->stock_item_id) {
                        $stock = StockItem::find($item->stock_item_id);

                        if ($stock) {
                            $stock->update([
                                'price' => $data['price'] ?? $stock->price,
                                'warehouse_location' => $data['warehouse_location'] ?? $stock->warehouse_location,
                                'cosmetic_condition' => $data['cosmetic_condition'] ?? $stock->cosmetic_condition,
                                'condition_notes' => $data['condition_notes'] ?? $stock->condition_notes,
                                'fully_functional' => !empty($data['fully_functional']),

                                'sku' => $data['sku'] ?? $stock->sku,
                                'serial_number' => $data['serial_number'] ?? $stock->serial_number,
                                'asset_tags' => $data['asset_tags'] ?? $stock->asset_tags,

                                'category_id' => $data['category_id'] ?? $stock->category_id,
                                'manufacturer_id' => $data['manufacturer_id'] ?? $stock->manufacturer_id,
                                'product_model_id' => $data['product_model_id'] ?? $stock->product_model_id,
                                'model' => $data['model'] ?? $stock->model,
                                'year' => $data['year'] ?? $stock->year,
                                'chassis' => $data['chassis'] ?? $stock->chassis,

                                'processor_manufacturer' => $data['processor_manufacturer'] ?? $stock->processor_manufacturer,
                                'processor_type' => $data['processor_type'] ?? $stock->processor_type,
                                'processor_speed_ghz' => $data['processor_speed_ghz'] ?? $stock->processor_speed_ghz,

                                'ram_type' => $data['ram_type'] ?? $stock->ram_type,
                                'ram_gb' => $data['ram_gb'] ?? $stock->ram_gb,

                                'hdd_gb' => $data['hdd_gb'] ?? $stock->hdd_gb,
                                'ssd_gb' => $data['ssd_gb'] ?? $stock->ssd_gb,
                                'nvme_gb' => $data['nvme_gb'] ?? $stock->nvme_gb,

                                'operating_system' => $data['operating_system'] ?? $stock->operating_system,
                                'optical_drives' => $data['optical_drives'] ?? $stock->optical_drives,

                                'charger_included' => !empty($data['charger_included']),
                                'accessories_included' => !empty($data['accessories_included']),
                                'notes' => $data['notes'] ?? $stock->notes,
                            ]);
                        }
                    }

                    $item->update([
                        'status' => 'add_to_stock',
                        'processed_at' => now(),
                        'quantity' => $data['quantity'] ?? $item->quantity,
                        'carbon_footprint' => $data['carbon_footprint'] ?? $item->carbon_footprint,
                    ]);
                }
            } else {
                // other actions -> processed
                $item->update([
                    'status' => 'processed',
                    'processed_at' => now(),
                    'quantity' => $data['quantity'] ?? $item->quantity,
                    'carbon_footprint' => $data['carbon_footprint'] ?? $item->carbon_footprint,
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

       
        // hdd
        foreach (($data['hdds'] ?? []) as $hddId => $row) {
            $hdd = $item->hdds()->whereKey($hddId)->lockForUpdate()->first();
            if (!$hdd) continue;

            if (!empty($row['delete']) && $row['delete'] == '1') {
                $hdd->clearMediaCollection('erasure_reports'); // optional
                $hdd->delete();
                continue;
            }

            [$manId, $modelId, $manText, $modelText] = $this->resolveManufacturerModel($row);

            // ✅ Spatie media upload (replaces store + path column)
            if ($request->hasFile("hdds.$hddId.erasure_report")) {
                $hdd
                    ->clearMediaCollection('erasure_reports') // safe, even with singleFile
                    ->addMediaFromRequest("hdds.$hddId.erasure_report")
                    ->toMediaCollection('erasure_reports');
            }

            $hdd->update([
                'manufacturer_id' => $manId,
                'product_model_id' => $modelId,
                'manufacturer_text' => $manText,
                'model_text' => $modelText,
                'serial' => $row['serial'] ?? null,
                'status' => $row['status'] ?? 'not_processed',
                'size' => $row['size'] ?? null,
                'notes' => $row['notes'] ?? null,
                'create_separate_stock_item' => !empty($row['create_separate_stock_item']),
                // ❌ remove 'erasure_report_path'
            ]);
        }

        // create new
        foreach (($data['new_hdds'] ?? []) as $key => $row) {

            $hasSomething = !empty($row['serial']) || !empty($row['manufacturer_id']) || !empty($row['manufacturer_text']) || !empty($row['product_model_id']) || !empty($row['model_text']);
            if (!$hasSomething) continue;

            [$manId, $modelId, $manText, $modelText] = $this->resolveManufacturerModel($row);

            $hdd = $item->hdds()->create([
                'manufacturer_id' => $manId,
                'product_model_id' => $modelId,
                'manufacturer_text' => $manText,
                'model_text' => $modelText,
                'serial' => $row['serial'] ?? null,
                'status' => $row['status'] ?? 'not_processed',
                'size' => $row['size'] ?? null,
                'notes' => $row['notes'] ?? null,
                'create_separate_stock_item' => !empty($row['create_separate_stock_item']),
                // ❌ remove 'erasure_report_path'
            ]);

            // ✅ attach media after create
            if ($request->hasFile("new_hdds.$key.erasure_report")) {
                $hdd
                    ->addMediaFromRequest("new_hdds.$key.erasure_report")
                    ->toMediaCollection('erasure_reports');
            }
        }


        return redirect()->route('collections.process.index', $collection)->with('success','Item processed.');
    }

    public function updateGrid(Request $request, Collection $collection)
    {
        $data = $request->validate([

            'items' => ['array'],

            'items.*.qty' => ['required','integer','min:1','max:500'],
            'items.*.category_id' => ['nullable','exists:categories,id'],
            'items.*.category_name' => ['required','string','max:255'],
            'items.*.weight_kg' => ['nullable','numeric','min:0'],

            'items.*.ewc_code' => ['nullable','string','max:50'],
            'items.*.component' => ['nullable','string','max:255'],
            'items.*.concentration' => ['nullable','string','max:255'],
            'items.*.physical_form' => ['nullable','string','max:100'],
            'items.*.hazard_codes' => ['nullable','string','max:100'],

            'items.*.is_collected' => ['nullable','boolean'],

            'new_items' => ['array'],

            'new_items.*.qty' => ['required','integer','min:1','max:500'],
            'new_items.*.category_id' => ['nullable','exists:categories,id'],
            'new_items.*.category_name' => ['required','string','max:255'],
            'new_items.*.weight_kg' => ['nullable','numeric','min:0'],

            'new_items.*.ewc_code' => ['nullable','string','max:50'],
            'new_items.*.component' => ['nullable','string','max:255'],
            'new_items.*.concentration' => ['nullable','string','max:255'],
            'new_items.*.physical_form' => ['nullable','string','max:100'],
            'new_items.*.hazard_codes' => ['nullable','string','max:100'],

            'new_items.*.is_collected' => ['nullable','boolean'],

            'client_signature' => ['nullable','string'],
            'driver_signature' => ['nullable','string'],
            'client_print_name' => ['nullable','string','max:255'],
            'driver_print_name' => ['nullable','string','max:255'],
            'mode' => ['nullable','string'],
        ]);

        
        \DB::transaction(function () use ($collection,$data) {

            foreach (($data['items'] ?? []) as $id => $row) {

                $item = $collection->items()->whereKey($id)->lockForUpdate()->firstOrFail();

                $isCollected = (bool)($row['is_collected'] ?? false);

                $item->update([
                    'qty' => $row['qty'],
                    'category_id' => $row['category_id'],
                    'category_name' => $row['category_name'],
                    'weight_kg' => $row['weight_kg'] ?? 0,
                    'ewc_code' => $row['ewc_code'] ?? null,
                    'component' => $row['component'] ?? null,
                    'concentration' => $row['concentration'] ?? null,
                    'physical_form' => $row['physical_form'] ?? null,
                    'hazard_codes' => $row['hazard_codes'] ?? null,
                    'is_collected' => $isCollected,
                    'status' => $isCollected ? 'collected' : 'created',
                ]);
            }

            foreach (($data['new_items'] ?? []) as $row) {

                $isCollected = (bool)($row['is_collected'] ?? false);

                $collection->items()->create([
                    'category_id' => $row['category_id'],
                    'category_name' => $row['category_name'],
                    'qty' => $row['qty'],
                    'weight_kg' => $row['weight_kg'] ?? 0,
                    'ewc_code' => $row['ewc_code'] ?? null,
                    'component' => $row['component'] ?? null,
                    'concentration' => $row['concentration'] ?? null,
                    'physical_form' => $row['physical_form'] ?? null,
                    'hazard_codes' => $row['hazard_codes'] ?? null,
                    'is_collected' => $isCollected,
                    'status' => $isCollected ? 'collected' : 'created',
                ]);
            }

            if (($data['mode'] ?? '') === 'collect') {

                $collection->update([
                    'client_signature' => $data['client_signature'] ?? null,
                    'client_print_name' => $data['client_print_name'] ?? null,
                    'driver_signature' => $data['driver_signature'] ?? null,
                    'driver_print_name' => $data['driver_print_name'] ?? null,
                ]);
            }

        });

        return back()->with('success','Saved successfully.');
    }

    /**
     * Returns: [manufacturer_id, product_model_id, manufacturer_text, model_text]
     */
    private function resolveManufacturerModel(array $row): array
    {
        $categoryId = $row['category_id'] ?? null;

        // manufacturer can be numeric OR string tag
        $manId = $row['manufacturer_id'] ?? null;
        $manText = $row['manufacturer_text'] ?? null;

        if ($manId && !is_numeric($manId)) {
            $manText = $manId;     // select2 tag value
            $manId = null;
        }

        if (!$manId && !empty($manText)) {
            $m = Manufacturer::firstOrCreate(
                ['name' => trim($manText)],
                ['is_active' => 1]
            );
            $manId = $m->id;
        }

        // model can be numeric OR string tag
        $modelId = $row['product_model_id'] ?? null;
        $modelText = $row['model_text'] ?? null;

        if ($modelId && !is_numeric($modelId)) {
            $modelText = $modelId; // select2 tag value
            $modelId = null;
        }

        if (!$modelId && $manId && $categoryId && !empty($modelText)) {
            $pm = ProductModel::firstOrCreate(
                [
                    'category_id' => $categoryId,
                    'manufacturer_id' => $manId,
                    'name' => trim($modelText),
                ],
                ['is_active' => 1]
            );
            $modelId = $pm->id;
        }

        return [$manId, $modelId, $manText, $modelText];
    }


    private function renumberItems(Collection $collection): void
    {
        $items = $collection->items()
            ->orderByRaw('COALESCE(seq, 999999), id') // keep existing seq first
            ->lockForUpdate()
            ->get();

        $seq = 1;
        foreach ($items as $item) {
            $item->update([
                'seq' => $seq,
                'item_number' => $collection->collection_number . '-' . str_pad((string)$seq, 3, '0', STR_PAD_LEFT),
            ]);
            $seq++;
        }
    }

    // public function destroy(CollectionItem $item)
    // {
    //     $collection = $item->collection;
    //     $item->delete();

    //     $this->renumberItems($collection);

    //     return back()->with('success','Item deleted.');
    // }

    public function assignCode(CollectionItem $item)
    {
        $prefix = 'OLE954';

        // remove old codes if any
        $item->codes()->delete();

        $maxSeq = CollectionItemCode::where('item_prefix',$prefix)->max('seq') ?? 0;

        $rows = [];
        for ($i=1; $i<=$item->qty; $i++) {
            $rows[] = [
                'collection_item_id'=>$item->id,
                'item_prefix'=>$prefix,
                'seq'=>$maxSeq + $i,
                'created_at'=>now(),
                'updated_at'=>now(),
            ];
        }

        CollectionItemCode::insert($rows);

        return response()->json([
            'status'=>'assigned',
            'codes'=>$item->codes()->pluck('seq')->map(fn($s) => $prefix.'/'.$s)
        ]);
    }

    public function destroy(CollectionItem $item)
    {
        $item->delete(); // codes auto deleted via cascade

        return response()->json([
            'success' => true
        ]);
    }

    public function destroyitemcode(CollectionItemCode $code)
    {
        $code->delete();

        return response()->json([
            'success' => true
        ]);
    }

}
