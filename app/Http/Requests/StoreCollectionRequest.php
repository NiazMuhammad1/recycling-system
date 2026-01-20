<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCollectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:created,client_confirmed,pending,collected,processing,processed,cancelled'],
            'client_id' => ['required','exists:clients,id'],
            'collection_date' => ['nullable','date'],

            'address_line_1' => ['nullable','string','max:255'],
            'address_line_2' => ['nullable','string','max:255'],
            'town' => ['nullable','string','max:255'],
            'county' => ['nullable','string','max:255'],
            'postcode' => ['nullable','string','max:20'],
            'country' => ['required','string','max:255'],

            'contact_name' => ['nullable','string','max:255'],
            'contact_email' => ['nullable','email','max:255'],
            'contact_number' => ['nullable','string','max:50'],
            'on_site_contact_name' => ['nullable','string','max:255'],
            'on_site_contact_number' => ['nullable','string','max:50'],

            // Collection details
            'data_sanitisation' => ['nullable','string','max:255'],
            'collection_type' => ['nullable','string','max:255'],
            'logistics' => ['nullable','string','max:255'],

            // Questions
            'equipment_location' => ['nullable','string'],
            'access_elevator' => ['nullable','string'],
            'route_restrictions' => ['nullable','string'],
            'other_information' => ['nullable','string'],

            // Internal
            'vehicles_used' => ['nullable','string','max:255'],
            'staff_members' => ['nullable','string','max:255'],
            'internal_notes' => ['nullable','string'],

            // Optional blocks
            'pre_collection_audit' => ['nullable','string'],
            'equipment_classification' => ['nullable','string'],
        ];
    }
}
