<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
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
        ];
    }
}
