<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Collection;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCollectionRequest;
use App\Http\Requests\UpdateCollectionRequest;

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

        // Set defaults
        $data['country'] = $data['country'] ?? 'UK';
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        $collection = Collection::create($data);

        return redirect()
            ->route('collections.show', $collection)
            ->with('success', 'Collection created successfully.');
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
        $data['updated_by'] = $request->user()->id;

        $collection->update($data);

        return redirect()
            ->route('collections.show', $collection)
            ->with('success', 'Collection updated successfully.');
    }

    public function destroy(Collection $collection)
    {
        $collection->delete();

        return redirect()
            ->route('collections.index')
            ->with('success', 'Collection deleted successfully.');
    }
}
