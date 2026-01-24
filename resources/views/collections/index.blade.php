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
        <form class="form-inline" method="GET" action="{{ route('collections.index') }}">
            <div class="input-group input-group-sm mr-2" style="width: 300px;">
                <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="Search J-code or client...">
                <div class="input-group-append">
                    <button class="btn btn-default"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <select name="status" class="form-control form-control-sm mr-2">
                <option value="">All Status</option>
                @foreach($statuses as $st)
                    <option value="{{ $st }}" {{ $status===$st?'selected':'' }}>
                        {{ ucfirst(str_replace('_',' ',$st)) }}
                    </option>
                @endforeach
            </select>

            <button class="btn btn-sm btn-outline-secondary" type="submit">Filter</button>
        </form>
    </div>

    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Status</th>
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
