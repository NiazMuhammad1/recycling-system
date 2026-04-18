@extends('adminlte::page')
@section('title', 'Process Items')

@section('content')
<div class="container-fluid">
    <h1>Collection {{ $collection->collection_number }}</h1>
    <div class="text-muted mb-2">Status: {{ ucfirst($collection->status) }}</div>

    <div class="card">
        <div class="card-body">
            @if($items->isEmpty())
                <div class="text-muted">No items to process.</div>
                <a class="btn btn-link btn-sm" href="{{ route('collections.show',$collection) }}">Back</a>
            @else
                <div class="text-muted mb-2">One or more items still need to be processed</div>

                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th><th>Qty</th><th>Category</th><th>Manufacturer</th><th>Model</th>
                            <th>Serial</th><th>Asset Tag(s)</th><th>Our Asset #</th><th>Storage Serial #</th><th>S.Storage Serial #</th><th>Process Action</th><th>Status</th><th style="width:120px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $it)
                            <tr>
                                <td>{{ $it->item_code }}</td>
                                <td>{{ $it->qty }}</td>
                                <td>{{ $it->category?->name }}</td>
                                <td>{{ $it->manufacturerRel?->name ?? $it->manufacturer_text }}</td>
                                <td>{{ $it->productModel?->name ?? $it->model_text }}</td>
                                <td>{{ $it->serial_number }}</td>
                                <td>{{ $it->asset_tags }}</td>
                                <td>{{ $it->our_asset_number}}</td>
                                <td>{{ $it->storage_serial_number}}</td>
                                <td>{{ $it->second_storage_serial_number}}</td>
                                <td>{{ str_replace('_', ' ', ucfirst($it->process_action)) }}</td>
                                <td>{{ str_replace('_', ' ', ucfirst($it->status)) }}</td>
                                <td class="text-right">
                                    <a class="btn btn-primary btn-sm"
                                       href="{{ route('collections.process.itemForm', [$collection,$it]) }}">
                                        Process
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <a class="btn btn-link btn-sm" href="{{ route('collections.show',$collection) }}">Back</a>
            @endif
        </div>
    </div>
</div>
@endsection
