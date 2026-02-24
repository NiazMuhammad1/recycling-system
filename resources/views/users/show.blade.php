@extends('adminlte::page')

@section('title', 'Client Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>{{ $client->name }}</h1>
        <div>
            <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('clients.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <h5 class="mb-3">Client Details & Location</h5>
            <table class="table table-sm">
                <tr><th style="width:220px;">Client</th><td>{{ $client->name }}</td></tr>
                <tr><th>Address Line 1</th><td>{{ $client->address_line_1 }}</td></tr>
                <tr><th>Address Line 2</th><td>{{ $client->address_line_2 }}</td></tr>
                <tr><th>Town</th><td>{{ $client->town }}</td></tr>
                <tr><th>County</th><td>{{ $client->county }}</td></tr>
                <tr><th>Country</th><td>{{ $client->country }}</td></tr>
                <tr><th>Postcode</th><td>{{ $client->postcode }}</td></tr>
                <tr><th>Status</th>
                    <td>
                        @if($client->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </td>
                </tr>
            </table>

            <hr>

            <h5 class="mb-3">Contact Details</h5>
            <table class="table table-sm">
                <tr><th style="width:220px;">Contact Name</th><td>{{ $client->contact_name }}</td></tr>
                <tr><th>Contact Email</th><td>{{ $client->contact_email }}</td></tr>
                <tr><th>Contact Number</th><td>{{ $client->contact_number }}</td></tr>
                <tr><th>On Site Contact Name</th><td>{{ $client->on_site_contact_name }}</td></tr>
                <tr><th>On Site Contact Number</th><td>{{ $client->on_site_contact_number }}</td></tr>
            </table>

            <hr>

            <h5 class="mb-3">Notes</h5>
            <div class="border rounded p-3 bg-light">
                {!! nl2br(e($client->notes ?? '')) !!}
            </div>
        </div>
    </div>
@stop
