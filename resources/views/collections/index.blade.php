@extends('adminlte::page')
@section('title','Collections')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>Collections</h1>
    <a href="{{ route('collections.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Create
    </a>
</div>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-header">
       <form method="GET" action="{{ route('collections.index') }}" class="mb-4">
            <div class="card shadow-sm">
                
                <div class="card-header bg-primary text-white">
                    <strong>Find Collections</strong>
                </div>

                <div class="card-body">

                    <div class="row">

                        {{-- Number --}}
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Collection Code</label>
                            <input type="text"
                                name="number"
                                value="{{ request('number') }}"
                                class="form-control">
                        </div>

                        {{-- Client --}}
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Client</label>

                            <select name="client_id" class="form-control">
                                <option value="">All</option>

                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}"
                                        {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->company_name ?? $client->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Status</label>

                            <select name="status" class="form-control">
                                <option value="">All</option>

                                @foreach(['created','client_confirmed','pending','collected','processing','processed','cancelled'] as $status)
                                    <option value="{{ $status }}"
                                        {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- Date From --}}
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Date From</label>

                            <input type="date"
                                name="date_from"
                                value="{{ request('date_from') }}"
                                class="form-control">
                        </div>

                        {{-- Date To --}}
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Date To</label>

                            <input type="date"
                                name="date_to"
                                value="{{ request('date_to') }}"
                                class="form-control">
                        </div>

                        {{-- Postcode --}}
                        <div class="col-md-1 mb-3">
                            <label class="form-label">Postcode</label>

                            <input type="text"
                                name="postcode"
                                value="{{ request('postcode') }}"
                                class="form-control">
                        </div>

                        {{-- Town --}}
                        <div class="col-md-1 mb-3">
                            <label class="form-label">Town</label>

                            <input type="text"
                                name="town"
                                value="{{ request('town') }}"
                                class="form-control">
                        </div>

                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-search"></i> Search
                        </button>

                        <a href="{{ route('collections.index') }}"
                        class="btn btn-secondary">
                            Reset
                        </a>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <table class="table table-striped table-hover table-sm mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Assign To</th>
                    <th class="text-right" style="width: 220px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($collections as $c)
                <tr>
                    <td><a href="{{ route('collections.show',$c) }}">{{ $c->collection_code }}</a></td>
                    <td>{{ $c->client?->name }}</td>
                    <td>{{ optional($c->collection_date)->format('d/m/Y H:i') }}</td>
                    <td>{{ ucfirst(str_replace('_',' ',$c->status)) }}</td>
                    <td>{{ $c->user?->name }}</td>
                    <td class="text-right">
                        <a class="btn btn-sm btn-info" href="{{ route('collections.show',$c) }}">View</a>
                        <a class="btn btn-sm btn-warning" href="{{ route('collections.edit',$c) }}">Edit</a>
                        <form class="d-inline" method="POST" action="{{ route('collections.destroy',$c) }}"
                              onsubmit="return confirm('Delete this collection?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center p-4">No collections found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        {{ $collections->links() }}
    </div>
</div>
@stop
