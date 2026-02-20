@extends('adminlte::page')
@section('title', 'Process Item')
@section('plugins.Select2', true) 
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
                <h5 class="mb-2">Hard Disks</h5>

                <table class="table table-sm table-bordered" id="hddTable">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:240px;">Manufacturer</th>
                            <th style="width:240px;">Model</th>
                            <th style="width:220px;">Serial</th>
                            <th style="width:180px;">Status</th>
                            <th style="width:240px;">Erasure Report</th>
                            <th style="width:60px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item->hdds as $hdd)
                            <tr data-row="existing" data-hdd-id="{{ $hdd->id }}">
                                <td>
                                    <select class="form-control hddManSel" name="hdds[{{ $hdd->id }}][manufacturer_id]" style="width:100%">
                                        @if($hdd->manufacturerRel)
                                            <option value="{{ $hdd->manufacturerRel->id }}" selected>{{ $hdd->manufacturerRel->name }}</option>
                                        @elseif($hdd->manufacturer_text)
                                            <option value="{{ $hdd->manufacturer_text }}" selected>{{ $hdd->manufacturer_text }}</option>
                                        @endif
                                    </select>
                                    <input type="hidden" class="hddManText" name="hdds[{{ $hdd->id }}][manufacturer_text]" value="{{ $hdd->manufacturer_text }}">
                                </td>

                                <td>
                                    <select class="form-control hddModelSel" name="hdds[{{ $hdd->id }}][product_model_id]" style="width:100%">
                                        @if($hdd->productModel)
                                            <option value="{{ $hdd->productModel->id }}" selected>{{ $hdd->productModel->name }}</option>
                                        @elseif($hdd->model_text)
                                            <option value="{{ $hdd->model_text }}" selected>{{ $hdd->model_text }}</option>
                                        @endif
                                    </select>
                                    <input type="hidden" class="hddModelText" name="hdds[{{ $hdd->id }}][model_text]" value="{{ $hdd->model_text }}">
                                </td>

                                <td>
                                    <input class="form-control" name="hdds[{{ $hdd->id }}][serial]" value="{{ $hdd->serial }}">
                                </td>

                                <td>
                                    <select class="form-control" name="hdds[{{ $hdd->id }}][status]">
                                        @foreach(['not_processed'=>'Not Processed','erased'=>'Erased','failed'=>'Failed'] as $k=>$v)
                                            <option value="{{ $k }}" {{ $hdd->status===$k?'selected':'' }}>{{ $v }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <input type="file" name="hdds[{{ $hdd->id }}][erasure_report]" class="form-control-file">
                                    @php $media = $hdd->getFirstMedia('erasure_reports'); @endphp

                                    @if($media)
                                        <img src="{{ $media->getUrl() }}" width="120">
                                    @endif
                                </td>

                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger removeHddRow">&times;</button>
                                    <input type="hidden" name="hdds[{{ $hdd->id }}][delete]" value="0" class="hddDeleteFlag">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <button type="button" class="btn btn-primary btn-sm" id="addHddBtn">Add HDD</button>
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
@push('js')
<script>
$(function () {

    function uid(){ return 'n' + Math.random().toString(16).slice(2); }
    function isNumeric(v){ return /^\d+$/.test(v + ''); }

    function initHddManufacturer($sel){
        $sel.select2({
            placeholder: '-- Manufacturer --',
            allowClear: true,
            tags: true,
            width: '100%',
            ajax: {
                url: function () {
                    // HDD does NOT depend on category -> use global manufacturers endpoint
                    return "{{ route('ajax.manufacturers') }}"; // make sure you have this route
                },
                dataType: 'json',
                delay: 200,
                data: function (params) { return { q: params.term || '' }; },
                processResults: function (data) { return data; } // expects {results:[...]}
            },
            createTag: function (params) {
                var term = $.trim(params.term);
                if (!term) return null;
                return { id: term, text: term, newTag: true };
            }
        });
    }

    function initHddModel($sel, getManValFn){
        $sel.select2({
            placeholder: '-- Model --',
            allowClear: true,
            tags: true,
            width: '100%',
            ajax: {
                transport: async (params, success, failure) => {
                    try{
                        const manVal = getManValFn();
                        if (!manVal || !isNumeric(manVal)) return success({results: []});

                        let url = "{{ route('ajax.manufacturers.models', ':id') }}".replace(':id', manVal);
                        url += '?q=' + encodeURIComponent(params.data?.term || '');

                        const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
                        const data = await res.json();
                        success(data); // {results:[...]}
                    }catch(e){ failure(e); }
                },
                delay: 200,
                processResults: data => data
            },
            createTag: function (params) {
                var term = $.trim(params.term);
                if (!term) return null;
                return { id: term, text: term, newTag: true };
            }
        });
    }

    function bindHddRow($tr){
        // init select2
        const $man = $tr.find('.hddManSel');
        const $model = $tr.find('.hddModelSel');

        initHddManufacturer($man);
        initHddModel($model, () => $man.val());

        // tag behavior -> store text in hidden, keep select value visible
        $man.on('change', function(){
            var val = $(this).val();
            var text = $(this).find('option:selected').text();
            if (val && !isNumeric(val)) $tr.find('.hddManText').val(text);
            else $tr.find('.hddManText').val('');

            // reset model when manufacturer changes
            $model.val(null).trigger('change.select2');
            $tr.find('.hddModelText').val('');
        });

        $model.on('change', function(){
            var val = $(this).val();
            var text = $(this).find('option:selected').text();
            if (val && !isNumeric(val)) $tr.find('.hddModelText').val(text);
            else $tr.find('.hddModelText').val('');
        });

        // remove button -> for existing rows mark delete=1, for new rows remove from DOM
        $tr.find('.removeHddRow').on('click', function(){
            if ($tr.data('row') === 'existing') {
                $tr.hide();
                $tr.find('.hddDeleteFlag').val('1');
            } else {
                $tr.remove();
            }
        });
    }

    // init existing hdd rows
    $('#hddTable tbody tr').each(function(){ bindHddRow($(this)); });

    // add new hdd row
    $('#addHddBtn').on('click', function(){
        var key = uid();

        var $tr = $(`
            <tr data-row="new">
                <td>
                    <select class="form-control hddManSel" name="new_hdds[${key}][manufacturer_id]" style="width:100%"></select>
                    <input type="hidden" class="hddManText" name="new_hdds[${key}][manufacturer_text]" value="">
                </td>
                <td>
                    <select class="form-control hddModelSel" name="new_hdds[${key}][product_model_id]" style="width:100%"></select>
                    <input type="hidden" class="hddModelText" name="new_hdds[${key}][model_text]" value="">
                </td>
                <td><input class="form-control" name="new_hdds[${key}][serial]" value=""></td>
                <td>
                    <select class="form-control" name="new_hdds[${key}][status]">
                        <option value="not_processed">Not Processed</option>
                        <option value="erased">Erased</option>
                        <option value="failed">Failed</option>
                    </select>
                </td>
                <td><input type="file" name="new_hdds[${key}][erasure_report]" class="form-control-file"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger removeHddRow">&times;</button></td>
            </tr>
        `);

        $('#hddTable tbody').append($tr);
        bindHddRow($tr);
    });

});
</script>
@endpush