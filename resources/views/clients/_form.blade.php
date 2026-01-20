@php
    $client = $client ?? null;
@endphp

<div class="card">
    <div class="card-body">
        <div class="row">
            {{-- Basic --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label>Client Name *</label>
                    <input type="text" name="name" value="{{ old('name', $client?->name) }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>County</label>
                    <input type="text" name="county" value="{{ old('county', $client?->county) }}"
                           class="form-control @error('county') is-invalid @enderror">
                    @error('county')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Country *</label>
                    <input type="text" name="country" value="{{ old('country', $client?->country ?? 'UK') }}"
                           class="form-control @error('country') is-invalid @enderror" required>
                    @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Address --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label>Address Line 1</label>
                    <input type="text" name="address_line_1" value="{{ old('address_line_1', $client?->address_line_1) }}"
                           class="form-control @error('address_line_1') is-invalid @enderror">
                    @error('address_line_1')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Address Line 2</label>
                    <input type="text" name="address_line_2" value="{{ old('address_line_2', $client?->address_line_2) }}"
                           class="form-control @error('address_line_2') is-invalid @enderror">
                    @error('address_line_2')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Town</label>
                    <input type="text" name="town" value="{{ old('town', $client?->town) }}"
                           class="form-control @error('town') is-invalid @enderror">
                    @error('town')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Postcode</label>
                    <input type="text" name="postcode" value="{{ old('postcode', $client?->postcode) }}"
                           class="form-control @error('postcode') is-invalid @enderror">
                    @error('postcode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group pt-4">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" name="is_active" value="1"
                               class="custom-control-input" id="is_active"
                               {{ old('is_active', $client?->is_active ?? true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>

            {{-- Contact --}}
            <div class="col-md-4">
                <div class="form-group">
                    <label>Contact Name</label>
                    <input type="text" name="contact_name" value="{{ old('contact_name', $client?->contact_name) }}"
                           class="form-control @error('contact_name') is-invalid @enderror">
                    @error('contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Contact Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $client?->contact_email) }}"
                           class="form-control @error('contact_email') is-invalid @enderror">
                    @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number', $client?->contact_number) }}"
                           class="form-control @error('contact_number') is-invalid @enderror">
                    @error('contact_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- On site --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label>On Site Contact Name</label>
                    <input type="text" name="on_site_contact_name" value="{{ old('on_site_contact_name', $client?->on_site_contact_name) }}"
                           class="form-control @error('on_site_contact_name') is-invalid @enderror">
                    @error('on_site_contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>On Site Contact Number</label>
                    <input type="text" name="on_site_contact_number" value="{{ old('on_site_contact_number', $client?->on_site_contact_number) }}"
                           class="form-control @error('on_site_contact_number') is-invalid @enderror">
                    @error('on_site_contact_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Notes --}}
            <div class="col-12">
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $client?->notes) }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
</div>
