<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerAjaxController extends Controller
{
    // GET /ajax/partners?q=abc
    public function select2(Request $request)
    {
        $q = trim($request->get('q',''));

        $partners = Partner::query()
            ->where('is_active',1)
            ->when($q, fn($s) => $s->where('name','like',"%{$q}%"))
            ->orderBy('name')
            ->limit(20)
            ->get(['id','name']);

        return response()->json([
            'results' => $partners->map(fn($p) => ['id'=>$p->id,'text'=>$p->name])->values()
        ]);
    }

    // POST /ajax/partners (inline create)
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

        // Only add these if your partners table actually has these columns
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        $partner = Partner::create($data);

        return response()->json([
            'id' => $partner->id,
            'text' => $partner->name,
        ], 201);
    }

    // GET /ajax/partners/{partner}
    public function show(Partner $partner)
    {
        return response()->json($partner);
    }
}
