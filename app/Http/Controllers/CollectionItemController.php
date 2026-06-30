<?php
namespace App\Http\Controllers;


use App\Models\{Collection, CollectionItem, Manufacturer, ProductModel};
use App\Models\StockItem;
use App\Services\NumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\CollectionItemCode;
use App\Models\CollectionItemHdd;
use Illuminate\Support\Facades\Mail;
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
            ->whereNotIn('status',['created','pending'])
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

        $request->validate([
            'process_action' => 'required',
            'item_valuation' => 'nullable|numeric|min:0',
            'refurb_cost' => 'nullable|numeric|min:0',
            'hdd_serial' => 'nullable|string|max:255',
            'weight_kg' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:255',
            'erasure_required' => 'nullable|boolean',

            'erasure_report' => 'nullable|file|max:5120',

            'warehouse_location' => 'nullable|string|max:50',
            'cosmetic_condition' => 'nullable|string|max:10',
            'condition_notes' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'fully_functional' => 'nullable|boolean',

            'hdds' => 'array',
            'hdds.*.manufacturer_id' => 'nullable',
            'hdds.*.manufacturer_text' => 'nullable|string|max:120',
            'hdds.*.product_model_id' => 'nullable',
            'hdds.*.model_text' => 'nullable|string|max:120',
            'hdds.*.serial' => 'nullable|string|max:255',
            'hdds.*.status' => 'nullable|string|max:30',
            'hdds.*.delete' => 'nullable|in:0,1',
            'hdds.*.erasure_report' => 'nullable|file|max:5120',

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

            'hdds.*.size' => 'nullable|string|max:50',
            'hdds.*.notes' => 'nullable|string|max:255',
            'hdds.*.create_separate_stock_item' => 'nullable|boolean',

            'new_hdds.*.size' => 'nullable|string|max:50',
            'new_hdds.*.notes' => 'nullable|string|max:255',
            'new_hdds.*.create_separate_stock_item' => 'nullable|boolean',

            'sku' => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:255',
            'asset_tags' => 'nullable|string|max:255',

            'category_id' => 'nullable|integer',
            'manufacturer_id' => 'nullable|integer',
            'product_model_id' => 'nullable|integer',
            'model' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:50',
            'chassis' => 'nullable|string|max:50',

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

            'charger_included' => 'nullable|boolean',
            'accessories_included' => 'nullable|boolean',

            'product_images' => 'nullable|array',
            'product_images.*' => 'file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'notes' => 'nullable|string',


            // HDDs for existing items
            'items.*.hdds' => ['nullable', 'array'],
            'items.*.hdds.*.manufacturer_id' => ['nullable'],
            'items.*.hdds.*.manufacturer_text' => ['nullable', 'string', 'max:120'],
            'items.*.hdds.*.product_model_id' => ['nullable'],
            'items.*.hdds.*.model_text' => ['nullable', 'string', 'max:120'],
            'items.*.hdds.*.serial' => ['nullable', 'string', 'max:255'],
            'items.*.hdds.*.size' => ['nullable', 'string', 'max:50'],
            'items.*.hdds.*.status' => ['nullable', 'string', 'max:30'],
            'items.*.hdds.*.notes' => ['nullable', 'string', 'max:255'],
            'items.*.hdds.*.delete' => ['nullable', 'in:0,1'],
            'items.*.hdds.*.create_separate_stock_item' => ['nullable', 'boolean'],

            // HDDs for new items
            'new_items.*.hdds' => ['nullable', 'array'],
            'new_items.*.hdds.*.manufacturer_id' => ['nullable'],
            'new_items.*.hdds.*.manufacturer_text' => ['nullable', 'string', 'max:120'],
            'new_items.*.hdds.*.product_model_id' => ['nullable'],
            'new_items.*.hdds.*.model_text' => ['nullable', 'string', 'max:120'],
            'new_items.*.hdds.*.serial' => ['nullable', 'string', 'max:255'],
            'new_items.*.hdds.*.size' => ['nullable', 'string', 'max:50'],
            'new_items.*.hdds.*.status' => ['nullable', 'string', 'max:30'],
            'new_items.*.hdds.*.notes' => ['nullable', 'string', 'max:255'],
            'new_items.*.hdds.*.create_separate_stock_item' => ['nullable', 'boolean'],

        ]);

        DB::transaction(function () use ($collection, $item, $request) {
            $path = $item->erasure_report_path;

            if ($request->hasFile('erasure_report')) {
                $path = $request->file('erasure_report')->store('erasure_reports', 'public');
            }

            $item->process_action = $request->process_action;
            $item->item_valuation = $request->item_valuation ?? 0;
            $item->refurb_cost = $request->refurb_cost ?? 0;
            $item->hdd_serial = $request->hdd_serial ?? null;
            $item->weight_kg = $request->weight_kg ?? $item->weight_kg;
            $item->dimensions = $request->dimensions ?? $item->dimensions;
            $item->erasure_required = !empty($request->erasure_required);
            $item->erasure_report_path = $path;
            $item->save();

            if ($request->process_action === 'add_to_stock') {

                if (!$item->stock_item_id) {
                    $stock = new StockItem();

                    $stock->stock_number = NumberService::next('stock', 'S', 7);
                    $stock->category_id = $request->category_id ?? $item->category_id;
                    $stock->manufacturer_id = $request->manufacturer_id ?? $item->manufacturer_id;
                    $stock->product_model_id = $request->product_model_id ?? $item->product_model_id;

                    $stock->sku = $request->sku ?? null;
                    $stock->serial_number = $request->serial_number ?? $item->serial_number ?? null;
                    $stock->asset_tags = $request->asset_tags ?? $item->asset_tags ?? null;

                    $stock->price = $request->price ?? 0;
                    $stock->warehouse_location = $request->warehouse_location ?? null;
                    $stock->cosmetic_condition = $request->cosmetic_condition ?? null;
                    $stock->condition_notes = $request->condition_notes ?? null;
                    $stock->fully_functional = !empty($request->fully_functional);

                    $stock->status = 'in_stock';
                    $stock->source_collection_id = $collection->id;
                    $stock->source_collection_item_id = $item->id;

                    $stock->model = $request->model ?? null;
                    $stock->year = $request->year ?? null;
                    $stock->chassis = $request->chassis ?? null;

                    $stock->processor_manufacturer = $request->processor_manufacturer ?? null;
                    $stock->processor_type = $request->processor_type ?? null;
                    $stock->processor_speed_ghz = $request->processor_speed_ghz ?? null;

                    $stock->ram_type = $request->ram_type ?? null;
                    $stock->ram_gb = $request->ram_gb ?? null;

                    $stock->hdd_gb = $request->hdd_gb ?? null;
                    $stock->ssd_gb = $request->ssd_gb ?? null;
                    $stock->nvme_gb = $request->nvme_gb ?? null;

                    $stock->operating_system = $request->operating_system ?? null;
                    $stock->optical_drives = $request->optical_drives ?? null;

                    $stock->charger_included = !empty($request->charger_included);
                    $stock->accessories_included = !empty($request->accessories_included);

                    $stock->notes = $request->notes ?? null;
                    $stock->save();

                    $item->stock_item_id = $stock->id;
                    $item->status = 'add_to_stock';
                    $item->processed_at = now();
                    $item->quantity = $request->quantity ?? $item->quantity;
                    $item->carbon_footprint = $request->carbon_footprint ?? $item->carbon_footprint;
                    $item->save();
                } else {
                    $stock = StockItem::find($item->stock_item_id);

                    if ($stock) {
                        $stock->price = $request->price ?? $stock->price;
                        $stock->warehouse_location = $request->warehouse_location ?? $stock->warehouse_location;
                        $stock->cosmetic_condition = $request->cosmetic_condition ?? $stock->cosmetic_condition;
                        $stock->condition_notes = $request->condition_notes ?? $stock->condition_notes;
                        $stock->fully_functional = !empty($request->fully_functional);

                        $stock->sku = $request->sku ?? $stock->sku;
                        $stock->serial_number = $request->serial_number ?? $stock->serial_number;
                        $stock->asset_tags = $request->asset_tags ?? $stock->asset_tags;

                        $stock->category_id = $request->category_id ?? $stock->category_id;
                        $stock->manufacturer_id = $request->manufacturer_id ?? $stock->manufacturer_id;
                        $stock->product_model_id = $request->product_model_id ?? $stock->product_model_id;
                        $stock->model = $request->model ?? $stock->model;
                        $stock->year = $request->year ?? $stock->year;
                        $stock->chassis = $request->chassis ?? $stock->chassis;

                        $stock->processor_manufacturer = $request->processor_manufacturer ?? $stock->processor_manufacturer;
                        $stock->processor_type = $request->processor_type ?? $stock->processor_type;
                        $stock->processor_speed_ghz = $request->processor_speed_ghz ?? $stock->processor_speed_ghz;

                        $stock->ram_type = $request->ram_type ?? $stock->ram_type;
                        $stock->ram_gb = $request->ram_gb ?? $stock->ram_gb;

                        $stock->hdd_gb = $request->hdd_gb ?? $stock->hdd_gb;
                        $stock->ssd_gb = $request->ssd_gb ?? $stock->ssd_gb;
                        $stock->nvme_gb = $request->nvme_gb ?? $stock->nvme_gb;

                        $stock->operating_system = $request->operating_system ?? $stock->operating_system;
                        $stock->optical_drives = $request->optical_drives ?? $stock->optical_drives;

                        $stock->charger_included = !empty($request->charger_included);
                        $stock->accessories_included = !empty($request->accessories_included);
                        $stock->notes = $request->notes ?? $stock->notes;
                        $stock->save();
                    }

                    $item->status = 'add_to_stock';
                    $item->processed_at = now();
                    $item->quantity = $request->quantity ?? $item->quantity;
                    $item->carbon_footprint = $request->carbon_footprint ?? $item->carbon_footprint;
                    $item->save();
                }
            } else {
                $item->status = 'processed';
                $item->processed_at = now();
                $item->quantity = $request->quantity ?? $item->quantity;
                $item->carbon_footprint = $request->carbon_footprint ?? $item->carbon_footprint;
                $item->save();
            }

            $pending = $collection->items()->whereIn('status', ['collected', 'processing'])->exists();
            if (!$pending) {
                $collection->status = 'processed';
                $collection->processed_at = now();
                $collection->save();
            }
        });

        foreach (($request->hdds ?? []) as $hddId => $row) {
            $hdd = $item->hdds()->whereKey($hddId)->first();
            if (!$hdd) {
                continue;
            }

            if (!empty($row['delete']) && $row['delete'] == '1') {
                $hdd->clearMediaCollection('erasure_reports');
                $hdd->delete();
                continue;
            }

            [$manId, $modelId, $manText, $modelText] = $this->resolveManufacturerModel($row);

            if ($request->hasFile("hdds.$hddId.erasure_report")) {
                $hdd->clearMediaCollection('erasure_reports')
                    ->addMediaFromRequest("hdds.$hddId.erasure_report")
                    ->toMediaCollection('erasure_reports');
            }

            $hdd->manufacturer_id = $manId;
            $hdd->product_model_id = $modelId;
            $hdd->manufacturer_text = $manText;
            $hdd->model_text = $modelText;
            $hdd->serial = $row['serial'] ?? null;
            $hdd->status = $row['status'] ?? 'not_processed';
            $hdd->size = $row['size'] ?? null;
            $hdd->notes = $row['notes'] ?? null;
            $hdd->create_separate_stock_item = !empty($row['create_separate_stock_item']);
            $hdd->save();
        }

        foreach (($request->new_hdds ?? []) as $key => $row) {
            $hasSomething =
                !empty($row['serial']) ||
                !empty($row['manufacturer_id']) ||
                !empty($row['manufacturer_text']) ||
                !empty($row['product_model_id']) ||
                !empty($row['model_text']);

            if (!$hasSomething) {
                continue;
            }

            [$manId, $modelId, $manText, $modelText] = $this->resolveManufacturerModel($row);

            $hdd = new CollectionItemHdd(); // replace with your actual HDD model
            $hdd->collection_item_id = $item->id;
            $hdd->manufacturer_id = $manId;
            $hdd->product_model_id = $modelId;
            $hdd->manufacturer_text = $manText;
            $hdd->model_text = $modelText;
            $hdd->serial = $row['serial'] ?? null;
            $hdd->status = $row['status'] ?? 'not_processed';
            $hdd->size = $row['size'] ?? null;
            $hdd->notes = $row['notes'] ?? null;
            $hdd->create_separate_stock_item = !empty($row['create_separate_stock_item']);
            $hdd->save();

            if ($request->hasFile("new_hdds.$key.erasure_report")) {
                $hdd->addMediaFromRequest("new_hdds.$key.erasure_report")
                    ->toMediaCollection('erasure_reports');
            }
        }

        return redirect()->route('collections.show', $collection)->with('success', 'Item processed.');

        // return redirect()->route('collections.process.index', $collection)->with('success', 'Item processed.');
    }

    public function updateGrid(Request $request, Collection $collection)
    {
        $data = $request->validate([
            'items' => ['array'],

            'items.*.qty' => ['required', 'integer', 'min:1', 'max:500'],
            'items.*.category_id' => ['nullable', 'exists:categories,id'],
            'items.*.category_name' => ['required', 'string', 'max:255'],
            'items.*.weight_kg' => ['nullable', 'numeric', 'min:0'],

            'items.*.ewc_code' => ['nullable', 'string', 'max:50'],
            'items.*.component' => ['nullable', 'string', 'max:255'],
            'items.*.concentration' => ['nullable', 'string', 'max:255'],
            'items.*.physical_form' => ['nullable', 'string', 'max:100'],
            'items.*.hazard_codes' => ['nullable', 'string', 'max:100'],

            // manufacturer / model
            'items.*.manufacturer_id' => ['nullable'],
            'items.*.manufacturer_text' => ['nullable', 'string', 'max:120'],
            'items.*.product_model_id' => ['nullable'],
            'items.*.model_text' => ['nullable', 'string', 'max:120'],

            'items.*.is_collected' => ['nullable', 'boolean'],
            'items.*.erasure_required' => ['nullable', 'boolean'],

            'new_items' => ['array'],

            'new_items.*.qty' => ['required', 'integer', 'min:1', 'max:500'],
            'new_items.*.category_id' => ['nullable', 'exists:categories,id'],
            'new_items.*.category_name' => ['required', 'string', 'max:255'],
            'new_items.*.weight_kg' => ['nullable', 'numeric', 'min:0'],

            'new_items.*.ewc_code' => ['nullable', 'string', 'max:50'],
            'new_items.*.component' => ['nullable', 'string', 'max:255'],
            'new_items.*.concentration' => ['nullable', 'string', 'max:255'],
            'new_items.*.physical_form' => ['nullable', 'string', 'max:100'],
            'new_items.*.hazard_codes' => ['nullable', 'string', 'max:100'],

            // manufacturer / model
            'new_items.*.manufacturer_id' => ['nullable'],
            'new_items.*.manufacturer_text' => ['nullable', 'string', 'max:120'],
            'new_items.*.product_model_id' => ['nullable'],
            'new_items.*.model_text' => ['nullable', 'string', 'max:120'],

            'new_items.*.is_collected' => ['nullable', 'boolean'],
            'new_items.*.erasure_required' => ['nullable', 'boolean'],

            'client_signature' => ['nullable', 'string'],
            'driver_signature' => ['nullable', 'string'],
            'client_print_name' => ['nullable', 'string', 'max:255'],
            'driver_print_name' => ['nullable', 'string', 'max:255'],
            'mode_type' => ['nullable', 'string'],
            'mode' => ['nullable', 'string'],
            'items.*.serial_number' => ['nullable', 'string', 'max:255'],
            'items.*.asset_tags' => ['nullable', 'string', 'max:255'],
            'items.*.storage_serial_number' => ['nullable', 'string', 'max:255'],
            'items.*.second_storage_serial_number' => ['nullable', 'string', 'max:255'],
            'items.*.our_asset_number' => ['nullable', 'string', 'max:255'],
            'new_items.*.serial_number' => ['nullable', 'string', 'max:255'],
            'new_items.*.asset_tags' => ['nullable', 'string', 'max:255'],
            'new_items.*.storage_serial_number' => ['nullable', 'string', 'max:255'],
            'new_items.*.second_storage_serial_number' => ['nullable', 'string', 'max:255'],
            'new_items.*.our_asset_number' => ['nullable', 'string', 'max:255'],

            'items.*.hdds' => ['nullable', 'array'],
            'items.*.hdds.*.serial' => ['nullable', 'string', 'max:255'],
            'items.*.hdds.*.size' => ['nullable', 'string', 'max:50'],
            'items.*.hdds.*.status' => ['nullable', 'string', 'max:30'],
            'items.*.hdds.*.notes' => ['nullable', 'string', 'max:255'],
            'items.*.hdds.*.delete' => ['nullable', 'in:0,1'],

            'new_items.*.hdds' => ['nullable', 'array'],
            'new_items.*.hdds.*.serial' => ['nullable', 'string', 'max:255'],
            'new_items.*.hdds.*.size' => ['nullable', 'string', 'max:50'],
            'new_items.*.hdds.*.status' => ['nullable', 'string', 'max:30'],
            'new_items.*.hdds.*.notes' => ['nullable', 'string', 'max:255'],
            'new_items.*.hdds.*.delete' => ['nullable', 'in:0,1'],

        ]);


        // validate numeric manufacturer/model IDs only if numeric
        $checkNumericIds = function (array $row) {
            if (!empty($row['manufacturer_id']) && is_numeric($row['manufacturer_id'])) {
                abort_unless(
                    Manufacturer::whereKey($row['manufacturer_id'])->exists(),
                    422,
                    'Invalid manufacturer selected.'
                );
            }

            if (!empty($row['product_model_id']) && is_numeric($row['product_model_id'])) {
                abort_unless(
                    ProductModel::whereKey($row['product_model_id'])->exists(),
                    422,
                    'Invalid model selected.'
                );
            }
        };

        foreach (($data['items'] ?? []) as $row) {
            $checkNumericIds($row);
        }

        foreach (($data['new_items'] ?? []) as $row) {
            $checkNumericIds($row);
        }

        DB::transaction(function () use ($collection, $data) {

            // update existing items
            foreach (($data['items'] ?? []) as $id => $row) {
                $item = $collection->items()->whereKey($id)->lockForUpdate()->firstOrFail();

                [$manufacturerId, $productModelId, $manufacturerText, $modelText] =
                    $this->resolveManufacturerModel($row);

                $isCollected = (bool)($row['is_collected'] ?? false);
                $isErasureRequired = (bool)($row['erasure_required'] ?? false);

                $item->update([
                    'qty' => $row['qty'],
                    'category_id' => $row['category_id'] ?? null,
                    'category_name' => $row['category_name'],
                    'weight_kg' => $row['weight_kg'] ?? 0,

                    'ewc_code' => $row['ewc_code'] ?? null,
                    'component' => $row['component'] ?? null,
                    'concentration' => $row['concentration'] ?? null,
                    'physical_form' => $row['physical_form'] ?? null,
                    'hazard_codes' => $row['hazard_codes'] ?? null,

                    'manufacturer_id' => $manufacturerId,
                    'product_model_id' => $productModelId,
                    'manufacturer_text' => $manufacturerText,
                    'model_text' => $modelText,
                    'is_collected' => $isCollected,
                    'erasure_required' => $isErasureRequired,
                    'status' => $isCollected ? 'collected' : 'created',

                    'serial_number' => $row['serial_number'] ?? null,
                    'asset_tags' => $row['asset_tags'] ?? null,
                    'storage_serial_number' => $row['storage_serial_number'] ?? null,
                    'second_storage_serial_number' => $row['second_storage_serial_number'] ?? null,
                    'our_asset_number' => $row['our_asset_number'] ?? null,
                ]);

                $this->saveGridItemHdds($item, $row['hdds'] ?? []);
            }

            // create new items
            foreach (($data['new_items'] ?? []) as $row) {
                [$manufacturerId, $productModelId, $manufacturerText, $modelText] =
                    $this->resolveManufacturerModel($row);

                $isCollected = (bool)($row['is_collected'] ?? false);
                $isErasureRequired = (bool)($row['erasure_required'] ?? false);
                $newItem = $collection->items()->create([
                    'category_id' => $row['category_id'] ?? null,
                    'category_name' => $row['category_name'],
                    'qty' => $row['qty'],
                    'weight_kg' => $row['weight_kg'] ?? 0,

                    'ewc_code' => $row['ewc_code'] ?? null,
                    'component' => $row['component'] ?? null,
                    'concentration' => $row['concentration'] ?? null,
                    'physical_form' => $row['physical_form'] ?? null,
                    'hazard_codes' => $row['hazard_codes'] ?? null,

                    'manufacturer_id' => $manufacturerId,
                    'product_model_id' => $productModelId,
                    'manufacturer_text' => $manufacturerText,
                    'model_text' => $modelText,
                    'erasure_required' => $isErasureRequired,
                    'is_collected' => $isCollected,
                    'status' => $isCollected ? 'collected' : 'created',

                    'serial_number' => $row['serial_number'] ?? null,
                    'asset_tags' => $row['asset_tags'] ?? null,
                    'storage_serial_number' => $row['storage_serial_number'] ?? null,
                    'second_storage_serial_number' => $row['second_storage_serial_number'] ?? null,
                    'our_asset_number' => $row['our_asset_number'] ?? null,
                ]);

                $this->saveGridItemHdds($newItem, $row['hdds'] ?? []);
            }

            if (($data['mode_type'] ?? '') === 'collect') {
                $collection->update([
                    'client_signature' => $data['client_signature'] ?? null,
                    'client_print_name' => $data['client_print_name'] ?? null,
                    'driver_signature' => $data['driver_signature'] ?? null,
                    'driver_print_name' => $data['driver_print_name'] ?? null,
                ]);
            }
        });

        if (($data['mode'] ?? '') === 'send_pdf') {
            $collection->refresh();

            $pdfController = new \App\Http\Controllers\CollectionPdfController();

            $dutyPdf = $pdfController->generateDutyOfCarePdf($collection);
            $hazardPdf = $pdfController->generateHazardousPdf($collection);

            Mail::to('niazm6888@gmail.com')
            ->send(new \App\Mail\CollectionPdfsMail($collection, [
                [
                    'data' => $dutyPdf,
                    'name' => 'duty-of-care.pdf',
                ],
                [
                    'data' => $hazardPdf,
                    'name' => 'hazardous-waste.pdf',
                ],
            ]));
            // IF the request came from our background offline-sync script:
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Data saved and PDFs emailed successfully.'
                ]);
            }

            return redirect()->route('collections.show', $collection)->with('success', 'Data saved and PDFs emailed successfully');

            
        }

        // Case 2: Driver just chose normal "Save Data"
            // IF the request came from our background offline-sync script:
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Saved successfully.'
                ]);
            }
        return redirect()->route('collections.show', $collection)->with('success', 'Saved successfully.');
    }

    private function saveGridItemHdds(CollectionItem $item, array $hdds): void
    {
        foreach ($hdds as $hddId => $row) {

            $hasSomething =
                !empty($row['serial']) ||
                !empty($row['size']) ||
                !empty($row['manufacturer_id']) ||
                !empty($row['manufacturer_text']) ||
                !empty($row['product_model_id']) ||
                !empty($row['model_text']) ||
                !empty($row['notes']);

            if (!$hasSomething && empty($row['delete'])) {
                continue;
            }

            $isExisting = is_numeric($hddId);

            if ($isExisting) {
                $hdd = $item->hdds()->whereKey($hddId)->first();

                if (!$hdd) {
                    continue;
                }

                if (!empty($row['delete']) && $row['delete'] == '1') {
                    $hdd->delete();
                    continue;
                }
            } else {
                $hdd = new CollectionItemHdd();
                $hdd->collection_item_id = $item->id;
            }

            [$manId, $modelId, $manText, $modelText] =
                $this->resolveManufacturerModel($row);

            $hdd->manufacturer_id = $manId;
            $hdd->product_model_id = $modelId;
            $hdd->manufacturer_text = $manText;
            $hdd->model_text = $modelText;
            $hdd->serial = $row['serial'] ?? null;
            $hdd->size = $row['size'] ?? null;
            $hdd->status = $row['status'] ?? 'not_processed';
            $hdd->notes = $row['notes'] ?? null;
            $hdd->create_separate_stock_item = !empty($row['create_separate_stock_item']);
            $hdd->save();
        }
    }

    public function updateGridold(Request $request, Collection $collection)
    {
        $request->validate([
            'items' => ['array'],

            'items.*.qty' => ['required', 'integer', 'min:1', 'max:500'],
            'items.*.category_id' => ['nullable', 'exists:categories,id'],
            'items.*.category_name' => ['required', 'string', 'max:255'],
            'items.*.weight_kg' => ['nullable', 'numeric', 'min:0'],

            'items.*.ewc_code' => ['nullable', 'string', 'max:50'],
            'items.*.component' => ['nullable', 'string', 'max:255'],
            'items.*.concentration' => ['nullable', 'string', 'max:255'],
            'items.*.physical_form' => ['nullable', 'string', 'max:100'],
            'items.*.hazard_codes' => ['nullable', 'string', 'max:100'],

            'items.*.manufacturer_id' => ['nullable'],
            'items.*.manufacturer_text' => ['nullable', 'string', 'max:120'],
            'items.*.product_model_id' => ['nullable'],
            'items.*.model_text' => ['nullable', 'string', 'max:120'],

            'items.*.is_collected' => ['nullable', 'boolean'],
            'items.*.erasure_required' => ['nullable', 'boolean'],

            'new_items' => ['array'],

            'new_items.*.qty' => ['required', 'integer', 'min:1', 'max:500'],
            'new_items.*.category_id' => ['nullable', 'exists:categories,id'],
            'new_items.*.category_name' => ['required', 'string', 'max:255'],
            'new_items.*.weight_kg' => ['nullable', 'numeric', 'min:0'],

            'new_items.*.ewc_code' => ['nullable', 'string', 'max:50'],
            'new_items.*.component' => ['nullable', 'string', 'max:255'],
            'new_items.*.concentration' => ['nullable', 'string', 'max:255'],
            'new_items.*.physical_form' => ['nullable', 'string', 'max:100'],
            'new_items.*.hazard_codes' => ['nullable', 'string', 'max:100'],

            'new_items.*.manufacturer_id' => ['nullable'],
            'new_items.*.manufacturer_text' => ['nullable', 'string', 'max:120'],
            'new_items.*.product_model_id' => ['nullable'],
            'new_items.*.model_text' => ['nullable', 'string', 'max:120'],

            'new_items.*.is_collected' => ['nullable', 'boolean'],
            'new_items.*.erasure_required' => ['nullable', 'boolean'],

            'client_signature' => ['nullable', 'string'],
            'driver_signature' => ['nullable', 'string'],
            'client_print_name' => ['nullable', 'string', 'max:255'],
            'driver_print_name' => ['nullable', 'string', 'max:255'],
            'mode_type' => ['nullable', 'string'],
            'mode' => ['nullable', 'string'],

            'items.*.serial_number' => ['nullable', 'string', 'max:255'],
            'items.*.asset_tags' => ['nullable', 'string', 'max:255'],
            'items.*.storage_serial_number' => ['nullable', 'string', 'max:255'],
            'items.*.second_storage_serial_number' => ['nullable', 'string', 'max:255'],
            'items.*.our_asset_number' => ['nullable', 'string', 'max:255'],

            'new_items.*.serial_number' => ['nullable', 'string', 'max:255'],
            'new_items.*.asset_tags' => ['nullable', 'string', 'max:255'],
            'new_items.*.storage_serial_number' => ['nullable', 'string', 'max:255'],
            'new_items.*.second_storage_serial_number' => ['nullable', 'string', 'max:255'],
            'new_items.*.our_asset_number' => ['nullable', 'string', 'max:255'],
        ]);

        $checkNumericIds = function (array $row) {
            if (!empty($row['manufacturer_id']) && is_numeric($row['manufacturer_id'])) {
                abort_unless(
                    Manufacturer::whereKey($row['manufacturer_id'])->exists(),
                    422,
                    'Invalid manufacturer selected.'
                );
            }

            if (!empty($row['product_model_id']) && is_numeric($row['product_model_id'])) {
                abort_unless(
                    ProductModel::whereKey($row['product_model_id'])->exists(),
                    422,
                    'Invalid model selected.'
                );
            }
        };

        foreach (($request->items ?? []) as $row) {
            $checkNumericIds($row);
        }

        foreach (($request->new_items ?? []) as $row) {
            $checkNumericIds($row);
        }

        DB::transaction(function () use ($collection, $request) {

            foreach (($request->items ?? []) as $id => $row) {
                $item = $collection->items()->whereKey($id)->lockForUpdate()->firstOrFail();

                [$manufacturerId, $productModelId, $manufacturerText, $modelText] =
                    $this->resolveManufacturerModel($row);

                $isCollected = (bool)($row['is_collected'] ?? false);
                $isErasureRequired = (bool)($row['erasure_required'] ?? false);

                $item->qty = $row['qty'];
                $item->category_id = $row['category_id'] ?? null;
                $item->category_name = $row['category_name'];
                $item->weight_kg = $row['weight_kg'] ?? 0;

                $item->ewc_code = $row['ewc_code'] ?? null;
                $item->component = $row['component'] ?? null;
                $item->concentration = $row['concentration'] ?? null;
                $item->physical_form = $row['physical_form'] ?? null;
                $item->hazard_codes = $row['hazard_codes'] ?? null;

                $item->manufacturer_id = $manufacturerId;
                $item->product_model_id = $productModelId;
                $item->manufacturer_text = $manufacturerText;
                $item->model_text = $modelText;

                $item->is_collected = $isCollected;
                $item->erasure_required = $isErasureRequired;
                $item->status = $isCollected ? 'collected' : 'created';

                $item->serial_number = $row['serial_number'] ?? null;
                $item->asset_tags = $row['asset_tags'] ?? null;
                $item->storage_serial_number = $row['storage_serial_number'] ?? null;
                $item->second_storage_serial_number = $row['second_storage_serial_number'] ?? null;
                $item->our_asset_number = $row['our_asset_number'] ?? null;

                $item->save();
            }

            foreach (($request->new_items ?? []) as $row) {
                [$manufacturerId, $productModelId, $manufacturerText, $modelText] =
                    $this->resolveManufacturerModel($row);

                $isCollected = (bool)($row['is_collected'] ?? false);
                $isErasureRequired = (bool)($row['erasure_required'] ?? false);

                $item = new CollectionItem();
                $item->collection_id = $collection->id;

                $item->category_id = $row['category_id'] ?? null;
                $item->category_name = $row['category_name'];
                $item->qty = $row['qty'];
                $item->weight_kg = $row['weight_kg'] ?? 0;

                $item->ewc_code = $row['ewc_code'] ?? null;
                $item->component = $row['component'] ?? null;
                $item->concentration = $row['concentration'] ?? null;
                $item->physical_form = $row['physical_form'] ?? null;
                $item->hazard_codes = $row['hazard_codes'] ?? null;

                $item->manufacturer_id = $manufacturerId;
                $item->product_model_id = $productModelId;
                $item->manufacturer_text = $manufacturerText;
                $item->model_text = $modelText;

                $item->erasure_required = $isErasureRequired;
                $item->is_collected = $isCollected;
                $item->status = $isCollected ? 'collected' : 'created';

                $item->serial_number = $row['serial_number'] ?? null;
                $item->asset_tags = $row['asset_tags'] ?? null;
                $item->storage_serial_number = $row['storage_serial_number'] ?? null;
                $item->second_storage_serial_number = $row['second_storage_serial_number'] ?? null;
                $item->our_asset_number = $row['our_asset_number'] ?? null;

                $item->save();
            }

            if (($request->mode_type ?? '') === 'collect') {
                $collection->client_signature = $request->client_signature ?? null;
                $collection->client_print_name = $request->client_print_name ?? null;
                $collection->driver_signature = $request->driver_signature ?? null;
                $collection->driver_print_name = $request->driver_print_name ?? null;
                $collection->save();
            }
        });

        if (($request->mode ?? '') === 'send_pdf') {
            $collection->refresh();

            $pdfController = new \App\Http\Controllers\CollectionPdfController();

            $dutyPdf = $pdfController->generateDutyOfCarePdf($collection);
            $hazardPdf = $pdfController->generateHazardousPdf($collection);

            Mail::to('niazm6888@gmail.com')
                ->send(new \App\Mail\CollectionPdfsMail($collection, $dutyPdf, $hazardPdf));
        }

        return back()->with('success', 'Saved successfully.');
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
