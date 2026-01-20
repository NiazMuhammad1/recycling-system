@extends('adminlte::page')

@section('title', 'Clients')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Clients</h1>
        <div>
            <a href="{{ route('clients.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create
            </a>
            <a href="#" class="btn btn-outline-secondary">
                <i class="fas fa-file-export"></i> Export
            </a>
        </div>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <form class="form-inline" method="GET" action="{{ route('clients.index') }}">
                <div class="input-group input-group-sm" style="width: 300px;">
                    <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Search by name...">
                    <div class="input-group-append">
                        <button class="btn btn-default" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>County</th>
                        <th>Country</th>
                        <th class="text-right" style="width: 220px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td>
                                <a href="{{ route('clients.show', $client) }}">
                                    {{ $client->name }}
                                </a>
                                @if(!$client->is_active)
                                    <span class="badge badge-secondary ml-2">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $client->county }}</td>
                            <td>{{ $client->country }}</td>
                            <td class="text-right">
                                <a class="btn btn-sm btn-info" href="{{ route('clients.show', $client) }}">
                                    View
                                </a>
                                <a class="btn btn-sm btn-warning" href="{{ route('clients.edit', $client) }}">
                                    Edit
                                </a>
                                <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this client?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center p-4">No clients found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer">
            {{ $clients->links() }}
        </div>
    </div>
@stop
