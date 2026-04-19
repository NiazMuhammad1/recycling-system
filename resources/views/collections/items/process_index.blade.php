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
                 <form method="POST" action="{{ route('collections.process.bulk', $collection) }}">
                @csrf   
                    <table class="table table-sm table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th><th>Qty</th><th>Category</th><th>Manufacturer</th><th>Model</th>
                                <th>Serial</th><th>Asset Tag(s)</th><th>Our Asset #</th><th>Storage Serial #</th><th>S.Storage Serial #</th><th>Process Action</th><th>Status</th><th>
                    <input type="checkbox" id="selectAll">
                </th><th style="width:120px;">Action</th>
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
                                    <td>
                                        @if(in_array($it->status, ['collected','processing']))
                                            <input type="checkbox" name="item_ids[]" value="{{ $it->id }}" class="item-checkbox">
                                        @endif
                                    </td>
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
                     <div class="d-flex justify-content-end align-items-center mt-3">
                        <label class="mr-2 mb-0"><strong>Bulk Process</strong></label>

                        <select name="process_action" class="form-control mr-2" style="max-width: 300px;" required>
                            <option value="">-- Bulk Process Selected items --</option>
                            <option value="add_to_stock">Add To Stock</option>
                            <option value="broken_for_parts">Broken For Parts</option>
                            <option value="charge">Charge</option>
                            <option value="completed">Completed</option>
                            <option value="data_erased">Data Erased</option>
                            <option value="degaussed">Degaussed</option>
                            <option value="disposed">Disposed</option>
                            <option value="electrical_tested">Electrical Tested</option>
                            <option value="erased">Erased</option>
                            <option value="factory_reset">Factory Reset</option>
                            <option value="needs_refurbishing">Needs Refurbishing</option>
                            <option value="needs_reviewed">Needs Reviewed</option>
                            <option value="physical_destruction">Physical Destruction</option>
                            <option value="quarantined">Quarantined</option>
                            <option value="recycled">Recycled</option>
                            <option value="returned_to_customer">Returned To Customer</option>
                            <option value="scrapped">Scrapped</option>
                            <option value="shredded_15mm">Shredded 15mm</option>
                            <option value="shredded_6mm">Shredded 6mm</option>
                            <option value="stage_1">Stage 1</option>
                            <option value="stage_2">Stage 2</option>
                            <option value="value">Value</option>
                        </select>

                        <button type="submit" class="btn btn-success">Apply</button>
                    </div>
                </form>
                <a class="btn btn-link btn-sm" href="{{ route('collections.show',$collection) }}">Back</a>
            @endif
        </div>
    </div>
</div>
@push('js')
<script>
document.getElementById('selectAll')?.addEventListener('change', function () {
    document.querySelectorAll('.item-checkbox').forEach(cb => {
        cb.checked = this.checked;
    });
});
</script>
@endpush
@endsection

