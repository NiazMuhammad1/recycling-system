<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Collection;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCollectionRequest;
use App\Http\Requests\UpdateCollectionRequest;
use Illuminate\Support\Facades\DB;
class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $status = $request->string('status')->toString();

        $collections = Collection::query()
            ->with('client:id,name')
            ->when($q, function ($query) use ($q) {
                $query->where('collection_code', 'like', "%{$q}%")
                      ->orWhereHas('client', fn($c) => $c->where('name', 'like', "%{$q}%"));
            })
            ->when($status, fn($query) => $query->where('status', $status))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $statuses = [
            'created','client_confirmed','pending','collected','processing','processed','cancelled'
        ];

        return view('collections.index', compact('collections', 'q', 'status', 'statuses'));
    }

    public function create()
    {
        // we will load client list via select2 ajax, so no need to preload
        $statuses = [
            'created' => 'Created',
            'client_confirmed' => 'Client Confirmed',
            'pending' => 'Pending',
            'collected' => 'Collected',
            'processing' => 'Processing',
            'processed' => 'Processed',
            'cancelled' => 'Cancelled',
        ];

        return view('collections.create', compact('statuses'));
    }

    public function store(StoreCollectionRequest $request)
    {
        $data = $request->validated();

        $collection = DB::transaction(function () use ($request, $data) {

            // 1) Create client if none selected
            if (empty($data['client_id'])) {
                $client = Client::create([
                    'name' => $data['contact_name'],
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
                    'is_active' => true,
                    'created_by' => $request->user()->id,
                    'updated_by' => $request->user()->id,
                ]);

                $data['client_id'] = $client->id;
            }

            // 2) Save collection (snapshot fields remain)
            unset($data['contact_name']); // not a column in collections

            $collection = new Collection();
            $collection->fill($data);
            $collection->created_by = $request->user()->id;
            $collection->updated_by = $request->user()->id;
            $collection->save();

            return $collection;
        });

        return redirect()->route('collections.show', $collection)->with('success', 'Collection created.');
    }

    public function show(Collection $collection)
    {
        $collection->load('client:id,name');

        return view('collections.show', compact('collection'));
    }

    public function edit(Collection $collection)
    {
        $collection->load('client:id,name');

        $statuses = [
            'created' => 'Created',
            'client_confirmed' => 'Client Confirmed',
            'pending' => 'Pending',
            'collected' => 'Collected',
            'processing' => 'Processing',
            'processed' => 'Processed',
            'cancelled' => 'Cancelled',
        ];

        return view('collections.edit', compact('collection', 'statuses'));
    }

    public function update(UpdateCollectionRequest $request, Collection $collection)
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data, $collection) {

            if (empty($data['client_id'])) {
                $client = Client::create([
                    'name' => $data['contact_name'],
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
                    'is_active' => true,
                    'created_by' => $request->user()->id,
                    'updated_by' => $request->user()->id,
                ]);

                $data['client_id'] = $client->id;
            }

            unset($data['contact_name']);

            $collection->fill($data);
            $collection->updated_by = $request->user()->id;
            $collection->save();
        });

        return redirect()->route('collections.show', $collection)->with('success', 'Collection updated.');
    }

    public function destroy(Collection $collection)
    {
        $collection->delete();

        return redirect()
            ->route('collections.index')
            ->with('success', 'Collection deleted successfully.');
    }
}
