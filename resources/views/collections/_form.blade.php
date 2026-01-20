@php
    $collection = $collection ?? null;
@endphp

<div class="card">
    <div class="card-body">

        {{-- Top row --}}
        <div class="row">
            <div class="col-md-6">
                <h5>Client Details & Location</h5>

                <div class="form-group">
                    <label>Collection Status *</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                        @foreach($statuses as $k => $label)
                            <option value="{{ $k }}" {{ old('status', $collection?->status ?? 'created') === $k ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Client *</label>
                    <div class="d-flex">
                        <select id="client_id" name="client_id"
                                class="form-control @error('client_id') is-invalid @enderror"
                                style="width:100%;">
                            @if($collection?->client)
                                <option value="{{ $collection->client->id }}" selected>
                                    {{ $collection->client->name }}
                                </option>
                            @endif
                        </select>
                        <button type="button" class="btn btn-outline-primary ml-2" data-toggle="modal" data-target="#addClientModal">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    @error('client_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    <small class="text-muted">Search client name. If not found click + to add quickly.</small>
                </div>

                <div class="form-group">
                    <label>Collection Date</label>
                    <input type="datetime-local" name="collection_date"
                           value="{{ old('collection_date', $collection?->collection_date ? $collection->collection_date->format('Y-m-d\TH:i') : '') }}"
                           class="form-control @error('collection_date') is-invalid @enderror">
                    @error('collection_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Address Line 1</label>
                    <input type="text" id="address_line_1" name="address_line_1"
                           value="{{ old('address_line_1', $collection?->address_line_1) }}"
                           class="form-control @error('address_line_1') is-invalid @enderror">
                </div>

                <div class="form-group">
                    <label>Address Line 2</label>
                    <input type="text" id="address_line_2" name="address_line_2"
                           value="{{ old('address_line_2', $collection?->address_line_2) }}"
                           class="form-control @error('address_line_2') is-invalid @enderror">
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Town</label>
                        <input type="text" id="town" name="town"
                               value="{{ old('town', $collection?->town) }}"
                               class="form-control @error('town') is-invalid @enderror">
                    </div>
                    <div class="form-group col-md-4">
                        <label>County</label>
                        <input type="text" id="county" name="county"
                               value="{{ old('county', $collection?->county) }}"
                               class="form-control @error('county') is-invalid @enderror">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Postcode</label>
                        <input type="text" id="postcode" name="postcode"
                               value="{{ old('postcode', $collection?->postcode) }}"
                               class="form-control @error('postcode') is-invalid @enderror">
                    </div>
                </div>

                <div class="form-group">
                    <label>Country *</label>
                    <input type="text" id="country" name="country"
                           value="{{ old('country', $collection?->country ?? 'UK') }}"
                           class="form-control @error('country') is-invalid @enderror" required>
                </div>
            </div>

            <div class="col-md-6">
                <h5>Contact Details</h5>

                <div class="form-group">
                    <label>Contact Name</label>
                    <input type="text" id="contact_name" name="contact_name"
                           value="{{ old('contact_name', $collection?->contact_name) }}"
                           class="form-control @error('contact_name') is-invalid @enderror">
                </div>

                <div class="form-group">
                    <label>Contact Email</label>
                    <input type="email" id="contact_email" name="contact_email"
                           value="{{ old('contact_email', $collection?->contact_email) }}"
                           class="form-control @error('contact_email') is-invalid @enderror">
                </div>

                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number"
                           value="{{ old('contact_number', $collection?->contact_number) }}"
                           class="form-control @error('contact_number') is-invalid @enderror">
                </div>

                <div class="form-group">
                    <label>On Site Contact Name</label>
                    <input type="text" id="on_site_contact_name" name="on_site_contact_name"
                           value="{{ old('on_site_contact_name', $collection?->on_site_contact_name) }}"
                           class="form-control @error('on_site_contact_name') is-invalid @enderror">
                </div>

                <div class="form-group">
                    <label>On Site Contact Number</label>
                    <input type="text" id="on_site_contact_number" name="on_site_contact_number"
                           value="{{ old('on_site_contact_number', $collection?->on_site_contact_number) }}"
                           class="form-control @error('on_site_contact_number') is-invalid @enderror">
                </div>

                <h5 class="mt-4">Internal Use</h5>
                <div class="form-group">
                    <label>Vehicles Used</label>
                    <input type="text" name="vehicles_used"
                           value="{{ old('vehicles_used', $collection?->vehicles_used) }}"
                           class="form-control @error('vehicles_used') is-invalid @enderror">
                </div>

                <div class="form-group">
                    <label>Staff Members</label>
                    <input type="text" name="staff_members"
                           value="{{ old('staff_members', $collection?->staff_members) }}"
                           class="form-control @error('staff_members') is-invalid @enderror">
                </div>
            </div>
        </div>

        <hr>

        {{-- Collection Details --}}
        <h5>Collection Details</h5>

        <div class="row">
            <div class="col-md-6">
                <label>Where is the equipment located in the building?</label>
                <textarea name="equipment_location" class="form-control" rows="2">{{ old('equipment_location', $collection?->equipment_location) }}</textarea>

                <label class="mt-3">Access to suitable elevator?</label>
                <textarea name="access_elevator" class="form-control" rows="2">{{ old('access_elevator', $collection?->access_elevator) }}</textarea>
            </div>

            <div class="col-md-6">
                <label>Restrictions on route the equipment can take through the building?</label>
                <textarea name="route_restrictions" class="form-control" rows="2">{{ old('route_restrictions', $collection?->route_restrictions) }}</textarea>

                <label class="mt-3">Any other relevant information?</label>
                <textarea name="other_information" class="form-control" rows="2">{{ old('other_information', $collection?->other_information) }}</textarea>
            </div>
        </div>

        <div class="form-group mt-3">
            <label>Notes</label>
            <textarea name="internal_notes" class="form-control" rows="4">{{ old('internal_notes', $collection?->internal_notes) }}</textarea>
        </div>

        <hr>

        {{-- Radio groups like your screenshot --}}
        <div class="row">
            <div class="col-md-6">
                <label>Data Sanitisation</label>
                @php
                    $sanOptions = [
                        'No Sanitisation Required',
                        'Basic Erasure – HMG Infosec Level 5 Lower',
                        'Premium Erasure – HMG Infosec Level 5 Enhanced (CESG)',
                        'Physical Destruction',
                        'Physical Destruction (On Site)',
                        'Degaussing',
                        'On Site Data Destruction',
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
                <label>Collection Type</label>
                @php
                    $typeOptions = [
                        'IT Asset Disposal (ITAD)',
                        'IT Asset Remarketing (Resale)',
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
                    <label>Logistics</label>
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
                <label>Pre-Collection Audit</label>
                <textarea name="pre_collection_audit" class="form-control" rows="3">{{ old('pre_collection_audit', $collection?->pre_collection_audit) }}</textarea>
            </div>
            <div class="col-md-6">
                <label>Equipment Classification</label>
                <textarea name="equipment_classification" class="form-control" rows="3">{{ old('equipment_classification', $collection?->equipment_classification) }}</textarea>
            </div>
        </div>

    </div>
</div>

{{-- ADD CLIENT MODAL --}}
<div class="modal fade" id="addClientModal" tabindex="-1" role="dialog" aria-labelledby="addClientModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="addClientModalLabel">Add Client</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div id="clientModalError" class="alert alert-danger d-none"></div>

            <div class="form-row">
                <div class="form-group col-md-8">
                    <label>Client Name *</label>
                    <input type="text" class="form-control" id="m_name">
                </div>
                <div class="form-group col-md-4">
                    <label>Country *</label>
                    <input type="text" class="form-control" id="m_country" value="UK">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>County</label>
                    <input type="text" class="form-control" id="m_county">
                </div>
                <div class="form-group col-md-6">
                    <label>Town</label>
                    <input type="text" class="form-control" id="m_town">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Address Line 1</label>
                    <input type="text" class="form-control" id="m_address_line_1">
                </div>
                <div class="form-group col-md-6">
                    <label>Address Line 2</label>
                    <input type="text" class="form-control" id="m_address_line_2">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Postcode</label>
                    <input type="text" class="form-control" id="m_postcode">
                </div>
                <div class="form-group col-md-4">
                    <label>Contact Email</label>
                    <input type="email" class="form-control" id="m_contact_email">
                </div>
                <div class="form-group col-md-4">
                    <label>Contact Number</label>
                    <input type="text" class="form-control" id="m_contact_number">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Contact Name</label>
                    <input type="text" class="form-control" id="m_contact_name">
                </div>
                <div class="form-group col-md-6">
                    <label>On Site Contact Name</label>
                    <input type="text" class="form-control" id="m_on_site_contact_name">
                </div>
            </div>

            <div class="form-group">
                <label>On Site Contact Number</label>
                <input type="text" class="form-control" id="m_on_site_contact_number">
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" id="saveClientBtn" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Client
            </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    (function () {
        const csrf = '{{ csrf_token() }}';

        // Select2 client selector
        $('#client_id').select2({
            placeholder: 'Select client...',
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

        // OPTIONAL: When client selected, you can auto-fill address/contact via a simple map
        // For now we keep it minimal (you can add a "GET /clients/{id}/json" later if you want auto-fill)

        // Inline create client
        $('#saveClientBtn').on('click', async function () {
            $('#clientModalError').addClass('d-none').text('');

            const payload = {
                name: $('#m_name').val(),
                country: $('#m_country').val() || 'UK',
                county: $('#m_county').val(),
                town: $('#m_town').val(),
                address_line_1: $('#m_address_line_1').val(),
                address_line_2: $('#m_address_line_2').val(),
                postcode: $('#m_postcode').val(),
                contact_name: $('#m_contact_name').val(),
                contact_email: $('#m_contact_email').val(),
                contact_number: $('#m_contact_number').val(),
                on_site_contact_name: $('#m_on_site_contact_name').val(),
                on_site_contact_number: $('#m_on_site_contact_number').val(),
                is_active: 1
            };

            try {
                const res = await fetch('{{ route('ajax.clients.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (!res.ok) {
                    const msg = data?.message || 'Failed to create client';
                    $('#clientModalError').removeClass('d-none').text(msg);
                    return;
                }

                // Add new option to select2 and select it
                const newOption = new Option(data.text, data.id, true, true);
                $('#client_id').append(newOption).trigger('change');

                // Close modal
                $('#addClientModal').modal('hide');

            } catch (e) {
                $('#clientModalError').removeClass('d-none').text('Network error. Try again.');
            }
        });
    })();
    </script>
@endpush
