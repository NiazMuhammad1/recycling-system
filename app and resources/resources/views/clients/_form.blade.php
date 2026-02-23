@php
    $client = $client ?? null;
@endphp

<div class="card">
    <div class="card-body">
        <div class="row">

            {{-- LEFT COLUMN --}}
            <div class="col-md-6 pr-lg-5">

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">
                        Client Name *
                    </label>
                    <div class="col-sm-8">
                        <input type="text" name="name"
                               class="form-control form-control-sm"
                               value="{{ old('name', $client?->name) }}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">
                        Country *
                    </label>
                    <div class="col-sm-8">
                        <input type="text" name="country"
                               class="form-control form-control-sm"
                               value="{{ old('country', $client?->country ?? 'UK') }}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">
                        Address Line 1
                    </label>
                    <div class="col-sm-8">
                        <input type="text" name="address_line_1"
                               class="form-control form-control-sm"
                               value="{{ old('address_line_1', $client?->address_line_1) }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">
                        Address Line 2
                    </label>
                    <div class="col-sm-8">
                        <input type="text" name="address_line_2"
                               class="form-control form-control-sm"
                               value="{{ old('address_line_2', $client?->address_line_2) }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">
                        Town
                    </label>
                    <div class="col-sm-8">
                        <input type="text" name="town"
                               class="form-control form-control-sm"
                               value="{{ old('town', $client?->town) }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">
                        Postcode
                    </label>
                    <div class="col-sm-8">
                        <input type="text" name="postcode"
                               class="form-control form-control-sm"
                               value="{{ old('postcode', $client?->postcode) }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">
                        Status
                    </label>
                    <div class="col-sm-8 pt-1">
                        <div class="custom-control custom-switch">
                            <input type="checkbox"
                                   class="custom-control-input"
                                   id="is_active"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', $client?->is_active ?? true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN --}}
            <div class="col-md-6 pr-lg-5">

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">
                        County
                    </label>
                    <div class="col-sm-8">
                        <input type="text" name="county"
                               class="form-control form-control-sm"
                               value="{{ old('county', $client?->county) }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">
                        Contact Name
                    </label>
                    <div class="col-sm-8">
                        <input type="text" name="contact_name"
                               class="form-control form-control-sm"
                               value="{{ old('contact_name', $client?->contact_name) }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">
                        Contact Email
                    </label>
                    <div class="col-sm-8">
                        <input type="email" name="contact_email"
                               class="form-control form-control-sm"
                               value="{{ old('contact_email', $client?->contact_email) }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">
                        Contact Number
                    </label>
                    <div class="col-sm-8">
                        <input type="text" name="contact_number"
                               class="form-control form-control-sm"
                               value="{{ old('contact_number', $client?->contact_number) }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">
                        On Site Contact
                    </label>
                    <div class="col-sm-8">
                        <input type="text" name="on_site_contact_name"
                               class="form-control form-control-sm"
                               value="{{ old('on_site_contact_name', $client?->on_site_contact_name) }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">
                        On Site Number
                    </label>
                    <div class="col-sm-8">
                        <input type="text" name="on_site_contact_number"
                               class="form-control form-control-sm"
                               value="{{ old('on_site_contact_number', $client?->on_site_contact_number) }}">
                    </div>
                </div>

            </div>

            {{-- NOTES (FULL WIDTH) --}}
            <div class="col-12 px-2">
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes"
                              rows="3"
                              class="form-control form-control-sm">{{ old('notes', $client?->notes) }}</textarea>
                </div>
            </div>

        </div>
    </div>
</div>
