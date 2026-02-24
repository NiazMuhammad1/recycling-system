<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'type' => ['required', Rule::in(['both','hazard','duty_of_care'])],

            'ewc_code' => ['nullable','string','max:30'],
            'default_weight_kg' => ['nullable','numeric','min:0'],
            'component' => ['nullable','string','max:255'],
            'concentration' => ['nullable','string','max:255'],
            'physical_form' => ['nullable','string','max:50'],
            'hazard_codes' => ['nullable','string','max:50'],

            'is_active' => ['nullable','boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}