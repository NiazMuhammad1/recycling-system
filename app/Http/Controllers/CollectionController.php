<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Collection;
use App\Services\NumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\User;
class CollectionController extends Controller
{
    public function index()
    {
        $collections = Collection::with(['client', 'partner'])->latest()->paginate(20);
        return view('collections.index', compact('collections'));
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

}
