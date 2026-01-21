<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientAjaxController extends Controller
{
    // GET /ajax/clients?q=abc
    public function select2(Request $request)
    {
        $q = trim((string)$request->get('q', ''));

        $clients = Client::query()
            ->when($q !== '', fn($query) => $query->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name']);

        return response()->json([
            'results' => $clients->map(fn($c) => ['id' => $c->id, 'text' => $c->name]),
        ]);
    }

    // POST /ajax/clients (inline create)
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'county' => ['nullable','string','max:255'],
            'country' => ['required','string','max:255'],

            'address_line_1' => ['nullable','string','max:255'],
            'address_line_2' => ['nullable','string','max:255'],
            'town' => ['nullable','string','max:255'],
            'postcode' => ['nullable','string','max:20'],

            'contact_name' => ['nullable','string','max:255'],
            'contact_email' => ['nullable','email','max:255'],
            'contact_number' => ['nullable','string','max:50'],
            'on_site_contact_name' => ['nullable','string','max:255'],
            'on_site_contact_number' => ['nullable','string','max:50'],

            'notes' => ['nullable','string'],
            'is_active' => ['nullable','boolean'],
        ]);

        $data['country'] = $data['country'] ?? 'UK';
        $data['is_active'] = $request->boolean('is_active', true);

        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        $client = Client::create($data);

        return response()->json([
            'id' => $client->id,
            'text' => $client->name,
        ], 201);
    }

    public function show(Client $client)
    {
        return response()->json([
            'id' => $client->id,
            'name' => $client->name,

            'address_line_1' => $client->address_line_1,
            'address_line_2' => $client->address_line_2,
            'town' => $client->town,
            'county' => $client->county,
            'postcode' => $client->postcode,
            'country' => $client->country,

            'contact_name' => $client->contact_name,
            'contact_email' => $client->contact_email,
            'contact_number' => $client->contact_number,
            'on_site_contact_name' => $client->on_site_contact_name,
            'on_site_contact_number' => $client->on_site_contact_number,
        ]);
    }
}
