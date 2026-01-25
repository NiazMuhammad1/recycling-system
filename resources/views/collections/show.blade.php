@extends('adminlte::page')
@section('title', 'Collection '.$collection->collection_number)

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center mb-2">
        <h1 class="mb-0">Collection {{ $collection->collection_number }}</h1>
        <a class="ml-2" href="{{ route('collections.edit',$collection) }}">[edit]</a>
    </div>
    <div class="mb-3 text-muted">Status: {{ ucfirst($collection->status) }}</div>

    <div class="card">
        <div class="card-body">
            {{-- top summary --}}
            <div class="row">
                <div class="col-md-6">
                    <h5>Client Details & Location</h5>
                    <table class="table table-borderless table-sm mb-0">
                        <tr><td style="width:200px;">Client</td><td>{{ $collection->client?->name }}</td></tr>
                        <tr><td>Collection Date</td><td>{{ optional($collection->collection_date)->format('d/m/Y H:i') }}</td></tr>
                        <tr><td>Address Line 1</td><td>{{ $collection->address_line_1 }}</td></tr>
                        <tr><td>Address Line 2</td><td>{{ $collection->address_line_2 }}</td></tr>
                        <tr><td>Town</td><td>{{ $collection->town }}</td></tr>
                        <tr><td>County</td><td>{{ $collection->county }}</td></tr>
                        <tr><td>Country</td><td>{{ $collection->country }}</td></tr>
                        <tr><td>Postcode</td><td>{{ $collection->postcode }}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Contact Details</h5>
                    <table class="table table-borderless table-sm mb-0">
                        <tr><td style="width:200px;">Contact Name</td><td>{{ $collection->contact_name }}</td></tr>
                        <tr><td>Contact Email</td><td>{{ $collection->contact_email }}</td></tr>
                        <tr><td>Contact Number</td><td>{{ $collection->contact_number }}</td></tr>
                        <tr><td>On Site Contact Name</td><td>{{ $collection->on_site_contact_name }}</td></tr>
                        <tr><td>On Site Contact Number</td><td>{{ $collection->on_site_contact_number }}</td></tr>
                    </table>
                </div>
            </div>

            <hr>

            {{-- Tabs --}}
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tab_items" role="tab">Items</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab_tasks" role="tab">Tasks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab_docs" role="tab">Documents</a>
                </li>
            </ul>

            <div class="tab-content border border-top-0 p-3">
                <div class="tab-pane fade show active" id="tab_items" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">ITEMS</h5>
                        <div>
                            <a class="mr-2" href="{{ route('collections.items.edit',$collection) }}">[edit]</a>
                            <a class="mr-2" href="{{ route('collections.collect.form',$collection) }}">[collect]</a>
                            <a class="mr-2" href="{{ route('collections.process.index',$collection) }}">[process]</a>
                        </div>
                    </div>

                    <table class="table table-sm table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Qty</th>
                                <th>Category</th>
                                <th>Manufacturer</th>
                                <th>Model</th>
                                <th>Serial</th>
                                <th>Asset Tag(s)</th>
                                <th>Dimensions</th>
                                <th>Weight</th>
                                <th>Erasure Req.</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($collection->items->sortBy('item_code') as $it)
                            <tr>
                                <td>{{ $it->item_code }}</td>
                                <td>{{ $it->qty }}</td>
                                <td>{{ $it->category?->name }}</td>
                                <td>{{ $it->manufacturerRel?->name ?? $it->manufacturer_text }}</td>
                                <td>{{ $it->productModel?->name ?? $it->model_text }}</td>
                                <td>{{ $it->serial_number }}</td>
                                <td>{{ $it->asset_tags }}</td>
                                <td>{{ $it->dimensions }}</td>
                                <td>{{ number_format((float)$it->weight_kg,2) }} Kg</td>
                                <td>{{ $it->erasure_required ? 'Yes' : 'No' }}</td>
                                <td>
                                    @if($it->status === 'added_to_stock')
                                        <span class="badge badge-success">Added To Stock</span>
                                    @elseif($it->status === 'collected')
                                        <span class="badge badge-primary">Collected</span>
                                    @elseif($it->status === 'processed')
                                        <span class="badge badge-dark">Processed</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($it->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="8" class="text-right">Total Weight</th>
                                <th colspan="3">{{ number_format($collection->total_weight,2) }} Kg</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="tab-pane fade" id="tab_tasks" role="tabpanel">
                    <div class="text-muted">Tasks module next.</div>
                </div>

                <div class="tab-pane fade" id="tab_docs" role="tabpanel">
                    <div class="text-muted">Documents module next.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
