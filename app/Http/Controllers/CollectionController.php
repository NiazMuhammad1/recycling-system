<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Collection;
use App\Services\NumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
    public function index()
    {
        $collections = Collection::with('client')->latest()->paginate(20);
        return view('collections.index', compact('collections'));
    }

    public function create()
    {
        $statuses = $this->statuses();
        return view('collections.create', compact('statuses'));
    }

    public function store(Request $request)
    {
        $data = $this->validateCollection($request);

        DB::transaction(function () use (&$collection, $data, $request) {
            // If client_id not selected => create client from form fields
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

            $collection = Collection::create(array_merge($data, [
                'client_id' => $clientId,
                'collection_number' => NumberService::next('collection', 'J', 5), // J00001
            ]));

            // snapshot from selected client if selected
            $this->snapshotClientIfSelected($collection);
        });

        return redirect()->route('collections.show', $collection)->with('success','Collection created.');
    }

    public function show(Collection $collection)
    {

        $collection->load(['client','items.category','items.manufacturerRel','items.productModel','items.stockItem']);
        return view('collections.show', compact('collection'));
    }

    public function edit(Collection $collection)
    {
        $statuses = $this->statuses();
        $collection->load('client');
        return view('collections.edit', compact('collection','statuses'));
    }

    public function update(Request $request, Collection $collection)
    {
        $data = $this->validateCollection($request, true);

        DB::transaction(function () use ($collection, $data, $request) {
            // same rule: if client_id empty => create a new client from fields
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

            $collection->update(array_merge($data, ['client_id' => $clientId]));
            $this->snapshotClientIfSelected($collection);
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

    private function statuses(): array
    {
        return [
            'created' => 'Created',
            'collected' => 'Collected',
            'processed' => 'Processed',
        ];
    }

    private function validateCollection(Request $request, bool $updating=false): array
    {
        return $request->validate([
            'status' => 'required|in:created,collected,processed',

            // IMPORTANT: client can be null if user is creating new client from fields
            'client_id' => 'nullable|exists:clients,id',
            'client_name' => 'nullable|required_without:client_id|string|max:255',

            'collection_date' => 'nullable|date',

            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'town' => 'nullable|string|max:255',
            'county' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:50',
            'country' => 'required|string|max:100',

            'contact_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_number' => 'nullable|string|max:100',
            'on_site_contact_name' => 'nullable|string|max:255',
            'on_site_contact_number' => 'nullable|string|max:100',

            'vehicles_used' => 'nullable|string|max:255',
            'staff_members' => 'nullable|string|max:255',

            'equipment_location' => 'nullable|string',
            'access_elevator' => 'nullable|string',
            'route_restrictions' => 'nullable|string',
            'other_information' => 'nullable|string',
            'internal_notes' => 'nullable|string',

            'data_sanitisation' => 'nullable|string|max:255',
            'collection_type' => 'nullable|string|max:255',
            'logistics' => 'nullable|string|max:255',

            'pre_collection_audit' => 'nullable|string',
            'equipment_classification' => 'nullable|string',
        ]);
    }
}
