@extends('adminlte::page')
@section('title', 'Process Item')

@section('content')
<div class="container-fluid">

    <h1>{{ $item->item_number }} - {{ $item->manufacturer?->name ?? $item->manufacturer_text }} {{ $item->productModel?->name ?? $item->model_text }}</h1>

    <div class="card">
        <div class="card-body">

            <form method="POST" action="{{ route('collections.process.itemSave', [$collection,$item]) }}" enctype="multipart/form-data">
                @csrf

                <h5 class="mb-3">Process Details</h5>

                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Weight</label>
                            <div class="input-group input-group-sm">
                                <input class="form-control" name="weight_kg" value="{{ old('weight_kg', $item->weight_kg) }}">
                                <div class="input-group-append"><span class="input-group-text">Kg</span></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Dimensions</label>
                            <input class="form-control form-control-sm" name="dimensions" value="{{ old('dimensions', $item->dimensions) }}">
                        </div>

                        <div class="form-group">
                            <label>HDD Serial</label>
                            <input class="form-control form-control-sm" name="hdd_serial" value="{{ old('hdd_serial', $item->hdd_serial) }}">
                        </div>

                        <div class="form-group">
                            <label>Erasure Report</label>
                            <input type="file" class="form-control-file" name="erasure_report">
                            @if($item->erasure_report_path)
                                <div class="small text-muted mt-1">Uploaded: {{ $item->erasure_report_path }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Action</label>
                            <select class="form-control form-control-sm" name="process_action" required>
                                @foreach(['add_to_stock'=>'Add To Stock','physical_destruction'=>'Physical Destruction','recycle'=>'Recycle','resale'=>'Resale'] as $k=>$v)
                                    <option value="{{ $k }}" {{ old('process_action',$item->process_action) === $k ? 'selected':'' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Item Valuation</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-append"><span class="input-group-text">£</span></div>
                                <input class="form-control" name="item_valuation" value="{{ old('item_valuation', $item->item_valuation) }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Refurb Cost</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-append"><span class="input-group-text">£</span></div>
                                <input class="form-control" name="refurb_cost" value="{{ old('refurb_cost', $item->refurb_cost) }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="erasure_required"
                                       name="erasure_required" value="1" {{ old('erasure_required',$item->erasure_required) ? 'checked':'' }}>
                                <label class="custom-control-label" for="erasure_required">Erasure Required</label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <h5 class="mb-3">Stock Item Details (only used when Action = Add To Stock)</h5>

                <div class="form-row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Warehouse Location</label>
                            <input class="form-control form-control-sm" name="warehouse_location" value="{{ old('warehouse_location') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Cosmetic Condition</label>
                            <select class="form-control form-control-sm" name="cosmetic_condition">
                                @foreach(['A','B','C','D'] as $c)
                                    <option value="{{ $c }}">{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Price</label>
                            <div class="input-group input-group-sm">
                                <div class="input-group-append"><span class="input-group-text">£</span></div>
                                <input class="form-control" name="price" value="{{ old('price',0) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Condition Notes</label>
                    <textarea class="form-control form-control-sm" name="condition_notes" rows="3">{{ old('condition_notes') }}</textarea>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="fully_functional"
                               name="fully_functional" value="1" {{ old('fully_functional',1) ? 'checked':'' }}>
                        <label class="custom-control-label" for="fully_functional">Fully Functional</label>
                    </div>
                </div>

                <button class="btn btn-success btn-sm">Process</button>
                <a class="btn btn-link btn-sm" href="{{ route('collections.process.index',$collection) }}">Cancel</a>
            </form>
        </div>
    </div>

</div>
@endsection
