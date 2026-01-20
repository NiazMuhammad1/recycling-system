@extends('adminlte::page')
@section('title', 'Collection')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>Collection {{ $collection->collection_code }}</h1>
    <div>
        <a href="{{ route('collections.edit',$collection) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('collections.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>
<div class="text-muted">Status: {{ ucfirst(str_replace('_',' ',$collection->status)) }}</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body">

        <div class="row text-center mb-3">
            <div class="col-md-3"><strong>£{{ number_format((float)$collection->value_amount,2) }}</strong><div class="text-muted">Value</div></div>
            <div class="col-md-3"><strong>£{{ number_format((float)$collection->sold_amount,2) }}</strong><div class="text-muted">Sold</div></div>
            <div class="col-md-3"><strong>£{{ number_format((float)$collection->costs_amount,2) }}</strong><div class="text-muted">Costs</div></div>
            <div class="col-md-3"><strong>£{{ number_format((float)$collection->profit_amount,2) }}</strong><div class="text-muted">Profit</div></div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h5>Client Details & Location</h5>
                <table class="table table-sm">
                    <tr><th style="width:220px;">Client</th><td>{{ $collection->client?->name }}</td></tr>
                    <tr><th>Collection Date</th><td>{{ optional($collection->collection_date)->format('d/m/Y H:i') }}</td></tr>
                    <tr><th>Address Line 1</th><td>{{ $collection->address_line_1 }}</td></tr>
                    <tr><th>Address Line 2</th><td>{{ $collection->address_line_2 }}</td></tr>
                    <tr><th>Town</th><td>{{ $collection->town }}</td></tr>
                    <tr><th>County</th><td>{{ $collection->county }}</td></tr>
                    <tr><th>Country</th><td>{{ $collection->country }}</td></tr>
                    <tr><th>Postcode</th><td>{{ $collection->postcode }}</td></tr>
                </table>
            </div>

            <div class="col-md-6">
                <h5>Contact Details</h5>
                <table class="table table-sm">
                    <tr><th style="width:220px;">Contact Name</th><td>{{ $collection->contact_name }}</td></tr>
                    <tr><th>Contact Email</th><td>{{ $collection->contact_email }}</td></tr>
                    <tr><th>Contact Number</th><td>{{ $collection->contact_number }}</td></tr>
                    <tr><th>On Site Contact Name</th><td>{{ $collection->on_site_contact_name }}</td></tr>
                    <tr><th>On Site Contact Number</th><td>{{ $collection->on_site_contact_number }}</td></tr>
                </table>

                <h5 class="mt-4">Internal Use</h5>
                <table class="table table-sm">
                    <tr><th style="width:220px;">Vehicles Used</th><td>{{ $collection->vehicles_used }}</td></tr>
                    <tr><th>Staff Members</th><td>{{ $collection->staff_members }}</td></tr>
                </table>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <h5>Collection Details</h5>
                <table class="table table-sm">
                    <tr><th style="width:220px;">Data Sanitisation</th><td>{{ $collection->data_sanitisation }}</td></tr>
                    <tr><th>Collection Type</th><td>{{ $collection->collection_type }}</td></tr>
                    <tr><th>Logistics</th><td>{{ $collection->logistics }}</td></tr>
                </table>

                <div class="mt-2">
                    <div><strong>Where is the equipment located in the building?</strong></div>
                    <div class="text-muted">{{ $collection->equipment_location }}</div>

                    <div class="mt-2"><strong>Access to suitable elevator?</strong></div>
                    <div class="text-muted">{{ $collection->access_elevator }}</div>

                    <div class="mt-2"><strong>Restrictions on route the equipment can take through the building?</strong></div>
                    <div class="text-muted">{{ $collection->route_restrictions }}</div>

                    <div class="mt-2"><strong>Any other relevant information?</strong></div>
                    <div class="text-muted">{{ $collection->other_information }}</div>
                </div>
            </div>

            <div class="col-md-6">
                <h5>Pre-Collection Audit</h5>
                <div class="border rounded p-2 bg-light">{{ $collection->pre_collection_audit }}</div>

                <h5 class="mt-4">Equipment Classification</h5>
                <div class="border rounded p-2 bg-light">{{ $collection->equipment_classification }}</div>
            </div>
        </div>

        <hr>

        <h5>Notes</h5>
        <div class="border rounded p-3 bg-light">
            {!! nl2br(e($collection->internal_notes ?? '')) !!}
        </div>

    </div>
</div>
@stop
