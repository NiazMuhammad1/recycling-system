@extends('adminlte::page')
@section('title', 'Home')

@section('content')
    <div class="container-fluid">
        <h1>Dashboard</h1>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover table-sm mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Assign To</th>
                </tr>
            </thead>
            <tbody>
                @forelse($collections as $c)
                <tr>
                    <td><a href="{{ route('collections.show',$c) }}">{{ $c->collection_code }}</a></td>
                    <td>{{ $c->client?->name }}</td>
                    <td>{{ optional($c->collection_date)->format('d/m/Y H:i') }}</td>
                    <td>{{ $c->user?->name }}</td>
                    
                </tr>
                @empty
                <tr><td colspan="5" class="text-center p-4">No collections found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
