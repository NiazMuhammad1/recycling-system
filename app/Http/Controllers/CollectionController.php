<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Collection;
use App\Services\NumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\StockItem;
class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $collections = Collection::with(['client'])
            ->when($request->number, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('collection_code', 'like', '%' . $request->number . '%')
                        ->orWhere('collection_number', 'like', '%' . $request->number . '%');
                });
            })
            ->when($request->client_id, function ($q) use ($request) {
                $q->where('client_id', $request->client_id);
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->collection_type, function ($q) use ($request) {
                $q->where('collection_type', $request->collection_type);
            })
            ->when($request->town, function ($q) use ($request) {
                $q->where('town', 'like', '%' . $request->town . '%');
            })
            ->when($request->contact_name, function ($q) use ($request) {
                $q->where('contact_name', 'like', '%' . $request->contact_name . '%');
            })
            ->when($request->postcode, function ($q) use ($request) {
                $q->where('postcode', 'like', '%' . $request->postcode . '%');
            })
            ->when($request->date_from, function ($q) use ($request) {
                $q->whereDate('collection_date', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($q) use ($request) {
                $q->whereDate('collection_date', '<=', $request->date_to);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $clients = Client::orderBy('name')->get();

        return view('collections.index', compact('collections', 'clients'));
    }

    public function create()
    {
        $drivers = User::role('Driver')->get();
        return view('collections.create', compact('drivers'));
    }

    public function store(Request $request)
    {
        $data = $this->validateCollection($request);

        $collection = null;

        DB::transaction(function () use (&$collection, $data) {

            /*
            |---------------------------------------
            | CLIENT
            |---------------------------------------
            */

            $clientId = $data['client_id'] ?? null;

            // If client not selected → create new one
            if (!$clientId) {

                $client = Client::create([
                    'name' => $data['client_name'] ?? null,
                    'county' => $data['county'] ?? null,
                    'country' => $data['country'] ?? 'UK',
                    'address_line_1' => $data['address_line_1'] ?? null,
                    'address_line_2' => $data['address_line_2'] ?? null,
                    'town' => $data['town'] ?? null,
                    'postcode' => $data['postcode'] ?? null,
                    'contact_name' => $data['contact_name'] ?? null,
                    'contact_email' => $data['contact_email'] ?? null,
                    'sec_contact_email' => $data['sec_contact_email'] ?? null,
                    'contact_number' => $data['contact_number'] ?? null,
                    'on_site_contact_name' => $data['on_site_contact_name'] ?? null,
                    'on_site_contact_number' => $data['on_site_contact_number'] ?? null,
                    'is_active' => 1,
                ]);

                $clientId = $client->id;
            }

            /*
            |---------------------------------------
            | CREATE COLLECTION
            |---------------------------------------
            */
            $collection = new Collection($data);
            $collection->client_id = $clientId;
            $collection->user_id = auth()->id();
            $collection->collection_number = NumberService::next('collection', 'J', 5);
            $collection->save();

            $this->snapshotClientIfSelected($collection);
        });

        return redirect()
            ->route('collections.show', $collection)
            ->with('success', 'Collection created.');
    }

    public function show(Collection $collection)
    {
        $collection->load(['client','items.category','items.manufacturerRel','items.productModel','items.stockItem']);
        return view('collections.show', compact('collection'));
    }

    public function edit(Collection $collection)
    {
        $drivers = User::role('Driver')->get();
        $collection->load('client');
        return view('collections.edit', compact('collection', 'drivers'));
    }

    public function update(Request $request, Collection $collection)
    {
        
        $data = $this->validateCollection($request, true);
        DB::transaction(function () use ($collection, $data, $request) {

            // CLIENT (your existing logic)
            $clientId = $data['client_id'] ?? null;
            if (!$clientId) {
                $clientId = Client::create([
                    'name' => $request->input('client_name'),
                    'county' => $request->input('county'),
                    'country' => $request->input('country', 'UK'),
                    'address_line_1' => $request->input('address_line_1'),
                    'address_line_2' => $request->input('address_line_2'),
                    'town' => $request->input('town'),
                    'postcode' => $request->input('postcode'),
                    'contact_name' => $request->input('contact_name'),
                    'contact_email' => $request->input('contact_email'),
                    'sec_contact_email' => $request->input('sec_contact_email'),
                    'contact_number' => $request->input('contact_number'),
                    'on_site_contact_name' => $request->input('on_site_contact_name'),
                    'on_site_contact_number' => $request->input('on_site_contact_number'),
                    'is_active' => 1,
                ])->id;
            }

            // PARTNER (new logic)
            $partnerId = $data['partner_id'] ?? null;

            if (!$partnerId && $request->filled('partner_name')) {
                $partnerId = \App\Models\Partner::create([
                    'name' => $request->input('partner_name'),
                    'county' => $request->input('partner_county'),
                    'country' => $request->input('partner_country', 'UK'),
                    'address_line_1' => $request->input('partner_address_line_1'),
                    'address_line_2' => $request->input('partner_address_line_2'),
                    'town' => $request->input('partner_town'),
                    'postcode' => $request->input('partner_postcode'),
                    'contact_name' => $request->input('partner_contact_name'),
                    'contact_email' => $request->input('partner_contact_email'),
                    'contact_number' => $request->input('partner_contact_number'),
                    'on_site_contact_name' => $request->input('partner_on_site_contact_name'),
                    'on_site_contact_number' => $request->input('partner_on_site_contact_number'),
                    'is_active' => 1,
                ])->id;
            }
           
            $collection->update(array_merge($data, [
                'client_id' => $clientId,
                'partner_id' => $partnerId, // 👈 attach partner
            ]));

            $this->snapshotClientIfSelected($collection);
            // optional: $this->snapshotPartnerIfSelected($collection);
        });

        return redirect()->route('collections.show', $collection)->with('success','Collection updated.');
    }

    public function destroy(Collection $collection)
    {
        $collection->delete();
        return redirect()->route('collections.index')->with('success','Collection deleted.');
    }

    private function snapshotClientIfSelected(Collection $collection): void
    {
        if (!$collection->client_id) return;
        $client = $collection->client()->first();
        if (!$client) return;

        // Fill snapshot fields ONLY if empty OR always (choose)
        $collection->update([
            'address_line_1' => $collection->address_line_1 ?? $client->address_line_1,
            'address_line_2' => $collection->address_line_2 ?? $client->address_line_2,
            'town' => $collection->town ?? $client->town,
            'county' => $collection->county ?? $client->county,
            'postcode' => $collection->postcode ?? $client->postcode,
            'country' => $collection->country ?? $client->country,

            'contact_name' => $collection->contact_name ?? $client->contact_name,
            'contact_email' => $collection->contact_email ?? $client->contact_email,
            'contact_number' => $collection->contact_number ?? $client->contact_number,
            'on_site_contact_name' => $collection->on_site_contact_name ?? $client->on_site_contact_name,
            'on_site_contact_number' => $collection->on_site_contact_number ?? $client->on_site_contact_number,
        ]);
    }

    private function validateCollection(Request $request): array
    {
        return $request->validate([
            'client_id'   => ['nullable', 'exists:clients,id'],
            'client_name' => ['nullable', 'string', 'max:255'],

            'address_line_1' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'town'           => ['nullable', 'string', 'max:255'],
            'county'         => ['nullable', 'string', 'max:255'],
            'postcode'       => ['nullable', 'string', 'max:50'],
            'country'        => ['required', 'string', 'max:100'],

            'collection_date' => ['nullable', 'date'],
            'sla_target'      => ['nullable', 'integer'],

            'contact_name'   => ['nullable', 'string', 'max:255'],
            'contact_email'  => ['nullable', 'email', 'max:255'],
            'sec_contact_email'  => ['nullable', 'email', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:100'],
            'on_site_contact_name'   => ['nullable', 'string', 'max:255'],
            'on_site_contact_number' => ['nullable', 'string', 'max:100'],

            'vehicles_used' => ['nullable', 'string', 'max:255'],
            'driver_id'     => ['nullable', 'exists:users,id'],

            'equipment_location' => ['nullable', 'string'],
            'access_elevator'    => ['nullable', 'string'],
            'route_restrictions' => ['nullable', 'string'],
            'other_information'  => ['nullable', 'string'],
            'internal_notes'     => ['nullable', 'string'],

            'data_sanitisation' => ['nullable', 'string', 'max:255'],
            'collection_type'   => ['nullable', 'string', 'max:255'],
            'logistics'         => ['nullable', 'string', 'max:255'],
            'pre_collection_audit' => ['nullable', 'string', 'max:255'],
            'adisa_dial_rating'    => ['nullable', 'string', 'max:255'],

            'equipment_classification' => ['nullable', Rule::in(['EEE','WEEE'])],
        ]);
    }

    public function bulkProcess(Request $request, Collection $collection)
    {
        $allowedActions = [
            'add_to_stock',
            'broken_for_parts',
            'charge',
            'completed',
            'data_erased',
            'degaussed',
            'disposed',
            'electrical_tested',
            'erased',
            'factory_reset',
            'needs_refurbishing',
            'needs_reviewed',
            'physical_destruction',
            'quarantined',
            'recycled',
            'returned_to_customer',
            'scrapped',
            'shredded_15mm',
            'shredded_6mm',
            'stage_1',
            'stage_2',
            'value',
        ];

        $data = $request->validate([
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'integer|exists:collection_items,id',
            'process_action' => 'required|in:' . implode(',', $allowedActions),
        ]);

        DB::transaction(function () use ($collection, $data) {
            $items = $collection->items()
                ->whereIn('id', $data['item_ids'])
                ->get();

            foreach ($items as $item) {
                $item->update([
                    'process_action' => $data['process_action'],
                    'status' => $data['process_action'] === 'add_to_stock' ? 'add_to_stock' : 'processed',
                    'processed_at' => now(),
                ]);

                if ($data['process_action'] === 'add_to_stock' && !$item->stock_item_id) {
                    $stock = StockItem::create([
                        'stock_number' => NumberService::next('stock', 'S', 7),
                        'category_id' => $item->category_id,
                        'manufacturer_id' => $item->manufacturer_id,
                        'product_model_id' => $item->product_model_id,
                        'serial_number' => $item->serial_number,
                        'asset_tags' => $item->asset_tags,
                        'status' => 'in_stock',
                        'source_collection_id' => $collection->id,
                        'source_collection_item_id' => $item->id,
                    ]);

                    $item->update([
                        'stock_item_id' => $stock->id,
                    ]);
                }
            }

            $pending = $collection->items()
                ->whereIn('status', ['collected', 'processing'])
                ->exists();

            if (!$pending) {
                $collection->update([
                    'status' => 'processed',
                    'processed_at' => now(),
                ]);
            }
        });

        return redirect()
            ->route('collections.process.index', $collection)
            ->with('success', 'Selected items processed successfully.');
    }

}
