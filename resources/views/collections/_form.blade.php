@php
    $collection = $collection ?? null;
@endphp

<div class="card">
    <div class="card-body">

        {{-- ===== TOP ROW (2 columns with gutter + divider) ===== --}}
        <div class="row">
            {{-- LEFT --}}
            <div class="col-lg-6 pr-lg-5">
                <div class="itad-section">
                    <div class="itad-title">Client Details &amp; Location</div>
                    
                    {{-- Client --}}
                    <div class="form-group row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-normal">Client</label>
                        <div class="col-sm-8">
                            <select id="client_id" name="client_id"
                                    class="form-control form-control-sm @error('client_id') is-invalid @enderror"
                                    style="width:100%;">
                                @if($collection?->client)
                                    <option value="{{ $collection->client->id }}" selected>
                                        {{ $collection->client->name }}
                                    </option>
                                @endif
                            </select>
                            @error('client_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            {{-- hidden client_name used when client_id is empty --}}
                            <input type="hidden" name="client_name" id="client_name"
                                   value="{{ old('client_name', $collection?->client?->name ?? '') }}">
                        </div>
                    </div>

                    {{-- Address Line 1 --}}
                    <div class="form-group row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-normal">Address Line 1</label>
                        <div class="col-sm-8">
                            <input type="text" id="address_line_1" name="address_line_1"
                                   value="{{ old('address_line_1', $collection?->address_line_1) }}"
                                   class="form-control form-control-sm @error('address_line_1') is-invalid @enderror">
                            @error('address_line_1')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Address Line 2 --}}
                    <div class="form-group row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-normal">Address Line 2</label>
                        <div class="col-sm-8">
                            <input type="text" id="address_line_2" name="address_line_2"
                                   value="{{ old('address_line_2', $collection?->address_line_2) }}"
                                   class="form-control form-control-sm @error('address_line_2') is-invalid @enderror">
                            @error('address_line_2')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Town / County / Postcode --}}
                    <div class="form-group row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-normal">Town</label>
                        <div class="col-sm-8">
                            <input type="text" id="town" name="town"
                                   value="{{ old('town', $collection?->town) }}"
                                   class="form-control form-control-sm @error('town') is-invalid @enderror">
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-normal">County</label>
                        <div class="col-sm-8">
                            <input type="text" id="county" name="county"
                                   value="{{ old('county', $collection?->county) }}"
                                   class="form-control form-control-sm @error('county') is-invalid @enderror">
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-normal">Postcode</label>
                        <div class="col-sm-8">
                            <input type="text" id="postcode" name="postcode"
                                   value="{{ old('postcode', $collection?->postcode) }}"
                                   class="form-control form-control-sm @error('postcode') is-invalid @enderror">
                        </div>
                    </div>

                    {{-- Country --}}
                    <div class="form-group row align-items-center mb-0">
                        <label class="col-sm-4 col-form-label font-weight-normal">Country *</label>
                        <div class="col-sm-8">
                            <input type="text" id="country" name="country"
                                   value="{{ old('country', $collection?->country ?? 'UK') }}"
                                   class="form-control form-control-sm @error('country') is-invalid @enderror" required>
                            @error('country')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="itad-title mt-4">Collection Date</div>
                    <div class="form-group row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-normal">Collection Date</label>
                        <div class="col-sm-8">
                            <input type="date" id="collection_date" name="collection_date"
                                   value="{{ old('collection_date', $collection?->collection_date) }}"
                                   class="form-control form-control-sm @error('collection_date') is-invalid @enderror">
                        </div>
                    </div>
                    {{-- SLA Target --}}
                    <div class="form-group row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-normal">SLA Target</label>
                        <div class="col-sm-8 d-flex">
                            <input type="number" min="0" name="sla_target"
                                value="{{ old('sla_target', $collection?->sla_target) }}"
                                class="form-control form-control-sm @error('sla_target') is-invalid @enderror">
                            <div class="ml-2 align-self-center small text-muted">Working Days</div>
                        </div>
                        @error('sla_target')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <small class="text-muted ml-auto d-block">
                            Time in which the collection should be processed. (set to 0 to ignore SLA tracking)
                        </small>
                    </div>

                </div>
            </div>

            {{-- RIGHT --}}
            <div class="col-lg-6 pl-lg-5">
                <div class="itad-section itad-right">
                    <div class="itad-title">Contact Details</div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-normal">Contact Name</label>
                        <div class="col-sm-8">
                            <input type="text" id="contact_name" name="contact_name"
                                   value="{{ old('contact_name', $collection?->contact_name) }}"
                                   class="form-control form-control-sm @error('contact_name') is-invalid @enderror">
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-normal">Contact Email</label>
                        <div class="col-sm-8">
                            <input type="email" id="contact_email" name="contact_email"
                                   value="{{ old('contact_email', $collection?->contact_email) }}"
                                   class="form-control form-control-sm @error('contact_email') is-invalid @enderror">
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-normal">Contact Number</label>
                        <div class="col-sm-8">
                            <input type="text" id="contact_number" name="contact_number"
                                   value="{{ old('contact_number', $collection?->contact_number) }}"
                                   class="form-control form-control-sm @error('contact_number') is-invalid @enderror">
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-normal">On Site Contact Name</label>
                        <div class="col-sm-8">
                            <input type="text" id="on_site_contact_name" name="on_site_contact_name"
                                   value="{{ old('on_site_contact_name', $collection?->on_site_contact_name) }}"
                                   class="form-control form-control-sm @error('on_site_contact_name') is-invalid @enderror">
                        </div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-normal">On Site Contact Number</label>
                        <div class="col-sm-8">
                            <input type="text" id="on_site_contact_number" name="on_site_contact_number"
                                   value="{{ old('on_site_contact_number', $collection?->on_site_contact_number) }}"
                                   class="form-control form-control-sm @error('on_site_contact_number') is-invalid @enderror">
                        </div>
                    </div>
                    
                    <div class="itad-title mt-4">Internal Use</div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-4 col-form-label font-weight-normal">Vehicles Used</label>
                        <div class="col-sm-8">
                            <input type="text" name="vehicles_used"
                                   value="{{ old('vehicles_used', $collection?->vehicles_used) }}"
                                   class="form-control form-control-sm @error('vehicles_used') is-invalid @enderror">
                        </div>
                    </div>

                    <div class="form-group row align-items-center mb-0">
                        <label class="col-sm-4 col-form-label font-weight-normal">Staff Members</label>
                        <div class="col-sm-8">
                            <select name="driver_id" id="driver_id" class="form-control select2">
                                <option value="">-- Select Driver --</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}">
                                        {{ $driver->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        

        {{-- ===== Collection Details ===== --}}
        <div class="itad-title pt-5 mt-5">Collection Details</div>

        <div class="row">
            <div class="col-md-6">
                <label class="font-weight-normal">Where is the equipment located in the building?</label>
                <textarea name="equipment_location" class="form-control form-control-sm" rows="2">{{ old('equipment_location', $collection?->equipment_location) }}</textarea>

                <label class="font-weight-normal mt-3">Access to suitable elevator?</label>
                <textarea name="access_elevator" class="form-control form-control-sm" rows="2">{{ old('access_elevator', $collection?->access_elevator) }}</textarea>
            </div>

            <div class="col-md-6">
                <label class="font-weight-normal">Restrictions on route the equipment can take through the building?</label>
                <textarea name="route_restrictions" class="form-control form-control-sm" rows="2">{{ old('route_restrictions', $collection?->route_restrictions) }}</textarea>

                <label class="font-weight-normal mt-3">Any other relevant information?</label>
                <textarea name="other_information" class="form-control form-control-sm" rows="2">{{ old('other_information', $collection?->other_information) }}</textarea>
            </div>
        </div>

        <div class="form-group mt-3">
            <label class="font-weight-normal">Notes</label>
            <textarea name="internal_notes" class="form-control form-control-sm" rows="4">{{ old('internal_notes', $collection?->internal_notes) }}</textarea>
        </div>

        <hr>

        {{-- ===== Radio groups ===== --}}
        <div class="row">
            <div class="col-md-6">
                <label class="font-weight-normal">Data Sanitisation</label>
                @php
                    $sanOptions = [
                        'No Sanitisation Required',
                        'On Site Data Destruction',
                        'Physical Destruction',
                        'Premium Erasure - HMG Infosec Level 5 Enhanced (CESG)'
                    ];
                    $sanVal = old('data_sanitisation', $collection?->data_sanitisation);
                @endphp
                @foreach($sanOptions as $opt)
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio"
                               id="san_{{ md5($opt) }}" name="data_sanitisation" value="{{ $opt }}"
                               {{ $sanVal === $opt ? 'checked' : '' }}>
                        <label class="custom-control-label" for="san_{{ md5($opt) }}">{{ $opt }}</label>
                    </div>
                @endforeach
            </div>

            <div class="col-md-6">
                <label class="font-weight-normal">Collection Type</label>
                @php
                    $typeOptions = [
                        'IT Asset Disposal (ITAD)',
                        'IT Asset Redeployment',
                    ];
                    $typeVal = old('collection_type', $collection?->collection_type);
                @endphp
                @foreach($typeOptions as $opt)
                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio"
                               id="type_{{ md5($opt) }}" name="collection_type" value="{{ $opt }}"
                               {{ $typeVal === $opt ? 'checked' : '' }}>
                        <label class="custom-control-label" for="type_{{ md5($opt) }}">{{ $opt }}</label>
                    </div>
                @endforeach

                <div class="mt-3">
                    <label class="font-weight-normal">Logistics</label>
                    @php
                        $logOptions = [
                            'Multi-Point Collection',
                            'Dedicated Point to Point Collection',
                            'Un-liveried Vehicle',
                        ];
                        $logVal = old('logistics', $collection?->logistics);
                    @endphp
                    @foreach($logOptions as $opt)
                        <div class="custom-control custom-radio">
                            <input class="custom-control-input" type="radio"
                                   id="log_{{ md5($opt) }}" name="logistics" value="{{ $opt }}"
                                   {{ $logVal === $opt ? 'checked' : '' }}>
                            <label class="custom-control-label" for="log_{{ md5($opt) }}">{{ $opt }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <div class="mt-3">
                    <label class="font-weight-normal">Pre-Collection Audit</label>
                    @php
                        $auditOptions = [
                            'Customer To Audit Equipment & Enter Into OCS Prior To Collection',
                            'Customer Has Requested Eco Green IT Recycling to Audit Post Collection',
                        ];
                        $auditVal = old('pre_collection_audit', $collection?->pre_collection_audit);
                    @endphp

                    @foreach($auditOptions as $opt)
                        <div class="custom-control custom-radio">
                            <input class="custom-control-input" type="radio"
                                id="audit_{{ md5($opt) }}" name="pre_collection_audit" value="{{ $opt }}"
                                {{ $auditVal === $opt ? 'checked' : '' }}>
                            <label class="custom-control-label" for="audit_{{ md5($opt) }}">{{ $opt }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-6">
                {{-- ADISA DIAL Rating --}}
                <div class="form-group">
                    <label class="font-weight-normal">ADISA DIAL Rating</label>
                    @php
                        $dialOptions = ['Not Specified', 'DIAL Rating 1', 'DIAL Rating 2', 'DIAL Rating 2'];
                        $dialVal = old('adisa_dial_rating', $collection?->adisa_dial_rating);
                    @endphp
                    <select name="adisa_dial_rating" class="form-control form-control-sm">
                        @foreach($dialOptions as $opt)
                            <option value="{{ $opt }}" {{ $dialVal === $opt ? 'selected' : '' }}>
                                {{ $opt }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Equipment Classification --}}
                <div class="form-group">
                    <label class="font-weight-normal">Equipment Classification</label>
                    @php $eqVal = old('equipment_classification', $collection?->equipment_classification); @endphp

                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" id="eq_eee" name="equipment_classification" value="EEE"
                            {{ $eqVal === 'EEE' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="eq_eee">EEE - Re-use wherever possible</label>
                    </div>

                    <div class="custom-control custom-radio">
                        <input class="custom-control-input" type="radio" id="eq_weee" name="equipment_classification" value="WEEE"
                            {{ $eqVal === 'WEEE' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="eq_weee">WEEE - Waste Electrical</label>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('css')
<style>
    /* Section title like ITAD screenshot (dashed underline) */
    .itad-title{
        font-weight: 600;
        margin: 0 0 12px 0;
        padding-bottom: 10px;
        border-bottom: 1px dashed #d6d6d6;
    }

    /* Make labels align nicely */
    .col-form-label{
        padding-top: .3rem;
        padding-bottom: .3rem;
        color: #333;
    }

    /* Match Select2 single height to BS4 .form-control form-control-sm */
    .select2-container--default .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: .375rem .75rem;
        border: 1px solid #ced4da;
        border-radius: .25rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5rem;
        padding-left: 0;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(2.25rem + 2px);
        top: 0;
    }
</style>

@endpush

@push('js')

<script>

(function () {
    const $client = $('#client_id');

    $client.select2({
        placeholder: 'Search client name...',
        allowClear: true,
        ajax: {
            url: '{{ route('ajax.clients.select2') }}',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term || '' }),
            processResults: data => data,
            cache: true
        },
        width: '100%'
    });

    function setVal(id, v) {
        const el = document.getElementById(id);
        if (el) el.value = v ?? '';
    }

    function fillFromClient(c) {
        // store name in hidden client_name for validation/creation
        setVal('client_name', c.name);

        setVal('address_line_1', c.address_line_1);
        setVal('address_line_2', c.address_line_2);
        setVal('town', c.town);
        setVal('county', c.county);
        setVal('postcode', c.postcode);
        setVal('country', c.country || 'UK');

        setVal('contact_name', c.contact_name);
        setVal('contact_email', c.contact_email);
        setVal('contact_number', c.contact_number);
        setVal('on_site_contact_name', c.on_site_contact_name);
        setVal('on_site_contact_number', c.on_site_contact_number);
    }

    async function loadClient(id) {
        const url = '{{ route('ajax.clients.show', ':id') }}'.replace(':id', id);
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) throw new Error('Failed');
        return await res.json();
    }

    $client.on('change', async function () {
        const id = $(this).val();

        // If cleared => new client mode; keep typed fields
        if (!id) {
            // if user typed client name somewhere else later you can sync it here
            setVal('client_name', '');
            return;
        }

        try {
            const data = await loadClient(id);
            fillFromClient(data);
        } catch (e) {
            console.error(e);
            alert('Unable to load client details.');
        }
    });
})();
</script>

@endpush
