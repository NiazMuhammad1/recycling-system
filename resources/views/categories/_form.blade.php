@php
    $category = $category ?? null;
@endphp

<div class="card">
    <div class="card-body">
        <div class="row">

            {{-- LEFT --}}
            <div class="col-md-6 pr-lg-5">

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">Name *</label>
                    <div class="col-sm-8">
                        <input type="text" name="name"
                               class="form-control form-control-sm @error('name') is-invalid @enderror"
                               value="{{ old('name', $category?->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">Type *</label>
                    <div class="col-sm-8">
                        <select name="type"
                                class="form-control form-control-sm @error('type') is-invalid @enderror"
                                required>
                            @php $type = old('type', $category?->type ?? 'both'); @endphp
                            <option value="both" @selected($type==='both')>Both</option>
                            <option value="hazard" @selected($type==='hazard')>Hazard</option>
                            <option value="duty_of_care" @selected($type==='duty_of_care')>Duty of Care</option>
                        </select>
                        @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">EWC Code</label>
                    <div class="col-sm-8">
                        <input type="text" name="ewc_code"
                               class="form-control form-control-sm @error('ewc_code') is-invalid @enderror"
                               value="{{ old('ewc_code', $category?->ewc_code) }}"
                               placeholder="e.g. 20:01:35">
                        @error('ewc_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">Default Weight (kg)</label>
                    <div class="col-sm-8">
                        <input type="number" step="0.01" name="default_weight_kg"
                               class="form-control form-control-sm @error('default_weight_kg') is-invalid @enderror"
                               value="{{ old('default_weight_kg', $category?->default_weight_kg) }}">
                        @error('default_weight_kg') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="text-muted">Optional: used to prefill approx weight</small>
                    </div>
                </div>

            </div>

            {{-- RIGHT --}}
            <div class="col-md-6 pr-lg-5">

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">Component</label>
                    <div class="col-sm-8">
                        <input type="text" name="component"
                               class="form-control form-control-sm"
                               value="{{ old('component', $category?->component) }}"
                               placeholder="e.g. Lead, Mercury">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">Concentration</label>
                    <div class="col-sm-8">
                        <input type="text" name="concentration"
                               class="form-control form-control-sm"
                               value="{{ old('concentration', $category?->concentration) }}"
                               placeholder="e.g. Up to 2% / Approx 0.5kg">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">Physical Form</label>
                    <div class="col-sm-8">
                        <input type="text" name="physical_form"
                               class="form-control form-control-sm"
                               value="{{ old('physical_form', $category?->physical_form ?? 'Solid') }}"
                               placeholder="e.g. Solid">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">Hazard Code(s)</label>
                    <div class="col-sm-8">
                        <input type="text" name="hazard_codes"
                               class="form-control form-control-sm"
                               value="{{ old('hazard_codes', $category?->hazard_codes) }}"
                               placeholder="e.g. H6 or H6,H14">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">Status</label>
                    <div class="col-sm-8 pt-1">
                        <div class="custom-control custom-switch">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', $category?->is_active ?? true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>