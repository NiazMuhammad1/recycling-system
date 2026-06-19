<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Manufacturer;
use App\Models\ProductModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverOfflineCollectionController extends Controller
{
    public function show(Collection $collection)
    {
        return view('driver.collections.offline', compact('collection'));
    }

    public function data(Collection $collection)
    {
        $collection->load([
            'items.hdds',
            'items.category',
            'items.manufacturerRel',
            'items.productModel',
        ]);

        return response()->json([
            'collection' => [
                'id' => $collection->id,
                'collection_number' => $collection->collection_number,
                'client_signature' => '',
                'driver_signature' => '',
                'client_print_name' => $collection->client_print_name,
                'driver_print_name' => $collection->driver_print_name,
            ],

            'items' => $collection->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'item_code' => $item->item_code,
                    'qty' => $item->qty,
                    'category_id' => $item->category_id,
                    'category_name' => $item->category_name,
                    'manufacturer' => $item->manufacturerRel->name ?? $item->manufacturer_text,
                    'model' => $item->productModel->name ?? $item->model_text,
                    'serial_number' => $item->serial_number,
                    'asset_tags' => $item->asset_tags,
                    'our_asset_number' => $item->our_asset_number,
                    'storage_serial_number' => $item->storage_serial_number,
                    'is_collected' => (bool) $item->is_collected,
                    'erasure_required' => (bool) $item->erasure_required,

                    'hdds' => $item->hdds->map(function ($hdd) {
                        return [
                            'id' => $hdd->id,
                            'serial' => $hdd->serial,
                            'size' => $hdd->size,
                            'status' => $hdd->status,
                            'notes' => $hdd->notes,
                        ];
                    })->values(),
                ];
            })->values(),
        ]);
    }

    public function masterData()
    {
        return response()->json([
            'categories' => Category::orderBy('name')->get([
                'id',
                'name',
                'ewc_code',
                'is_erasure',
                'default_weight_kg',
                'component',
                'concentration',
                'physical_form',
                'hazard_codes',
                'type',
            ]),

            'manufacturers' => Manufacturer::orderBy('name')->get([
                'id',
                'name',
            ]),

            'models' => ProductModel::orderBy('name')->get([
                'id',
                'name',
                'manufacturer_id',
                'category_id',
            ]),
        ]);
    }

    public function sync(Request $request, Collection $collection)
    {
        $data = $request->validate([
            'items' => ['nullable', 'array'],
            'items.*.id' => ['required'],
            'items.*.is_collected' => ['nullable', 'boolean'],
            'items.*.serial_number' => ['nullable', 'string', 'max:255'],
            'items.*.asset_tags' => ['nullable', 'string', 'max:255'],
            'items.*.our_asset_number' => ['nullable', 'string', 'max:255'],
            'items.*.storage_serial_number' => ['nullable', 'string', 'max:255'],
            'items.*.hdds' => ['nullable', 'array'],

            'new_items' => ['nullable', 'array'],
            'new_items.*.qty' => ['required', 'integer', 'min:1', 'max:500'],
            'new_items.*.category_id' => ['nullable'],
            'new_items.*.category_name' => ['required', 'string', 'max:255'],
            'new_items.*.weight_kg' => ['nullable'],
            'new_items.*.ewc_code' => ['nullable', 'string', 'max:50'],
            'new_items.*.component' => ['nullable', 'string', 'max:255'],
            'new_items.*.concentration' => ['nullable', 'string', 'max:255'],
            'new_items.*.physical_form' => ['nullable', 'string', 'max:100'],
            'new_items.*.hazard_codes' => ['nullable', 'string', 'max:100'],
            'new_items.*.manufacturer_id' => ['nullable'],
            'new_items.*.manufacturer_text' => ['nullable', 'string', 'max:120'],
            'new_items.*.product_model_id' => ['nullable'],
            'new_items.*.model_text' => ['nullable', 'string', 'max:120'],
            'new_items.*.serial_number' => ['nullable', 'string', 'max:255'],
            'new_items.*.asset_tags' => ['nullable', 'string', 'max:255'],
            'new_items.*.our_asset_number' => ['nullable', 'string', 'max:255'],
            'new_items.*.storage_serial_number' => ['nullable', 'string', 'max:255'],
            'new_items.*.is_collected' => ['nullable', 'boolean'],
            'new_items.*.erasure_required' => ['nullable', 'boolean'],
            'new_items.*.hdds' => ['nullable', 'array'],

            'client_signature' => ['nullable', 'string'],
            'driver_signature' => ['nullable', 'string'],
            'client_print_name' => ['nullable', 'string', 'max:255'],
            'driver_print_name' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($collection, $data) {
            foreach (($data['items'] ?? []) as $row) {
                $item = $collection->items()->whereKey($row['id'])->first();

                if (!$item) {
                    continue;
                }

                $isCollected = (bool)($row['is_collected'] ?? false);

                $item->update([
                    'is_collected' => $isCollected,
                    'status' => $isCollected ? 'collected' : 'created',
                    'serial_number' => $row['serial_number'] ?? null,
                    'asset_tags' => $row['asset_tags'] ?? null,
                    'our_asset_number' => $row['our_asset_number'] ?? null,
                    'storage_serial_number' => $row['storage_serial_number'] ?? null,
                ]);

                $this->saveOfflineHdds($item, $row['hdds'] ?? []);
            }

            foreach (($data['new_items'] ?? []) as $row) {
                [$manufacturerId, $productModelId, $manufacturerText, $modelText] =
                    $this->resolveManufacturerModelOffline($row);

                $isCollected = (bool)($row['is_collected'] ?? false);

                $newItem = $collection->items()->create([
                    'category_id' => is_numeric($row['category_id'] ?? null) ? $row['category_id'] : null,
                    'category_name' => $row['category_name'],
                    'qty' => $row['qty'] ?? 1,
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

                    'serial_number' => $row['serial_number'] ?? null,
                    'asset_tags' => $row['asset_tags'] ?? null,
                    'our_asset_number' => $row['our_asset_number'] ?? null,
                    'storage_serial_number' => $row['storage_serial_number'] ?? null,

                    'is_collected' => $isCollected,
                    'erasure_required' => (bool)($row['erasure_required'] ?? false),
                    'status' => $isCollected ? 'collected' : 'created',
                ]);

                $this->saveOfflineHdds($newItem, $row['hdds'] ?? []);
            }

            $collection->update([
                'client_signature' => $data['client_signature'] ?? null,
                'driver_signature' => $data['driver_signature'] ?? null,
                'client_print_name' => $data['client_print_name'] ?? null,
                'driver_print_name' => $data['driver_print_name'] ?? null,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Offline collection synced successfully.',
        ]);
    }

    private function resolveManufacturerModelOffline(array $row): array
    {
        $manufacturerId = null;
        $productModelId = null;
        $manufacturerText = null;
        $modelText = null;

        if (!empty($row['manufacturer_id']) && is_numeric($row['manufacturer_id'])) {
            $manufacturerId = $row['manufacturer_id'];
        } else {
            $manufacturerText = $row['manufacturer_text'] ?? null;
        }

        if (!empty($row['product_model_id']) && is_numeric($row['product_model_id'])) {
            $productModelId = $row['product_model_id'];
        } else {
            $modelText = $row['model_text'] ?? null;
        }

        return [$manufacturerId, $productModelId, $manufacturerText, $modelText];
    }

    private function saveOfflineHdds($item, array $hdds): void
    {
        foreach ($hdds as $hddRow) {
            if (!empty($hddRow['id'])) {
                $hdd = $item->hdds()->whereKey($hddRow['id'])->first();

                if ($hdd) {
                    $hdd->update([
                        'serial' => $hddRow['serial'] ?? null,
                        'size' => $hddRow['size'] ?? null,
                        'status' => $hddRow['status'] ?? 'not_processed',
                        'notes' => $hddRow['notes'] ?? null,
                    ]);
                }
            } else {
                if (!empty($hddRow['serial']) || !empty($hddRow['size']) || !empty($hddRow['notes'])) {
                    $item->hdds()->create([
                        'serial' => $hddRow['serial'] ?? null,
                        'size' => $hddRow['size'] ?? null,
                        'status' => $hddRow['status'] ?? 'not_processed',
                        'notes' => $hddRow['notes'] ?? null,
                    ]);
                }
            }
        }
    }
}