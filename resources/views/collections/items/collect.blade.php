@extends('adminlte::page')
@section('title', 'Collect Items')

@section('content')
<div class="container-fluid">
    <h1>Collection {{ $collection->collection_number }}</h1>
    <div class="text-muted mb-2">Status: {{ ucfirst($collection->status) }}</div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('collections.collect.save',$collection) }}">
                @csrf

                <div class="mb-2">
                    <button class="btn btn-success btn-sm">Mark Collected</button>
                    <a href="{{ route('collections.show',$collection) }}" class="btn btn-link btn-sm">Cancel</a>
                    <span class="ml-3 text-muted">Tick the items you collected.</span>
                </div>

                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:60px;">Collected</th>
                            <th>#</th>
                            <th>Qty</th>
                            <th>Category</th>
                            <th>Manufacturer</th>
                            <th>Model</th>
                            <th>Serial</th>
                            <th>Asset Tag(s)</th>
                            <th>Erasure Req.</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($collection->items->sortBy('item_number') as $it)
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="collect_ids[]" value="{{ $it->id }}" {{ $it->collected?'checked':'' }}>
                            </td>
                            <td>{{ $it->item_number }}</td>
                            <td>{{ $it->qty }}</td>
                            <td>{{ $it->category?->name }}</td>
                            <td>{{ $it->manufacturer?->name ?? $it->manufacturer_text }}</td>
                            <td>{{ $it->productModel?->name ?? $it->model_text }}</td>
                            <td>{{ $it->serial_number }}</td>
                            <td>{{ $it->asset_tags }}</td>
                            <td>{{ $it->erasure_required?'Yes':'No' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <button class="btn btn-success btn-sm">Mark Collected</button>
            </form>
        </div>
    </div>
</div>
@endsection
