@extends('adminlte::page')
@section('title', 'Edit Items')
@section('plugins.Select2', true) 
@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-2">
        <h1 class="mb-0">Collection {{ $collection->collection_number }}</h1>
        <a class="ml-2" href="{{ route('collections.show',$collection) }}">Back</a>
    </div>

    <div class="card">
        <div class="card-body">

            {{-- TOP BAR: adds rows only (no DB) --}}
            <div class="form-row align-items-end mb-3">
                <div class="col-md-2">
                    <label>Qty</label>
                    <input class="form-control" type="number" id="bulk_qty" value="1" min="1" max="500">
                </div>

                <div class="col-md-3">
                    <label>Category</label>
                    <select class="form-control" id="bulk_category">
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Manufacturer</label>
                    <select class="form-control" id="bulk_manufacturer" style="width:100%"></select>
                </div>

                <div class="col-md-3">
                    <label>Model</label>
                    <select class="form-control" id="bulk_model" style="width:100%"></select>
                </div>

                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-secondary btn-block" id="bulk_add_btn" title="Add rows">
                        <i class="fas fa-clone"></i>
                    </button>
                </div>
            </div>

            {{-- SAVE FORM (updates existing + creates new) --}}
            <form method="POST" action="{{ route('collections.items.update',$collection) }}" id="itemsForm">
                @csrf
                @method('PUT')

                <table class="table table-bordered">
                    <thead class="thead-light">
                    <tr>
                        <th style="width:95px;">#</th>
                        <th style="width:70px;">Qty</th>
                        <th style="width:180px;">Category</th>
                        <th style="width:220px;">Manufacturer</th>
                        <th style="width:220px;">Model</th>
                        <th style="width:160px;">Serial</th>
                        <th style="width:160px;">Asset Tag(s)</th>
                        <th style="width:140px;">Dimensions</th>
                        <th style="width:100px;">Weight</th>
                        <th style="width:70px;">Erase</th>
                        <th style="width:70px;"></th>
                    </tr>
                    </thead>
                    <tbody id="itemsTbody">

                    {{-- existing rows --}}
                    @foreach($collection->items->sortBy('item_code') as $it)
                        <tr data-row="existing" data-id="{{ $it->id }}">
                            <td>{{ $it->item_code ?? $collection->collection_number.'-'.str_pad($it->seq ?? 0, 3, '0', STR_PAD_LEFT) }}</td>

                            <td>
                                <input class="form-control" name="items[{{ $it->id }}][qty]" value="{{ $it->qty }}">
                            </td>

                            <td>
                                <select class="form-control categorySel" name="items[{{ $it->id }}][category_id]">
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}" {{ $it->category_id==$c->id?'selected':'' }}>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                               
                                <select class="form-control manSel" name="items[{{ $it->id }}][manufacturer_id]" style="width:100%">
                                    @if($it->manufacturerRel)
                                        <option value="{{ $it->manufacturerRel->id }}" selected>{{ $it->manufacturerRel->name }}</option>
                                    @endif
                                </select>
                                <input type="hidden" class="manText" name="items[{{ $it->id }}][manufacturer_text]" value="">
                            </td>

                            <td>
                                <select class="form-control modelSel" name="items[{{ $it->id }}][product_model_id]" style="width:100%">
                                    @if($it->productModel)
                                        <option value="{{ $it->productModel->id }}" selected>{{ $it->productModel->name }}</option>
                                    @endif
                                </select>
                                <input type="hidden" class="modelText" name="items[{{ $it->id }}][model_text]" value="">
                            </td>

                            <td><input class="form-control" name="items[{{ $it->id }}][serial_number]" value="{{ $it->serial_number }}"></td>
                            <td><input class="form-control" name="items[{{ $it->id }}][asset_tags]" value="{{ $it->asset_tags }}"></td>
                            <td><input class="form-control" name="items[{{ $it->id }}][dimensions]" value="{{ $it->dimensions }}"></td>
                            <td><input class="form-control" name="items[{{ $it->id }}][weight_kg]" value="{{ $it->weight_kg }}"></td>

                            <td class="text-center">
                                <input type="checkbox" name="items[{{ $it->id }}][erasure_required]" value="1" {{ $it->erasure_required?'checked':'' }}>
                            </td>

                            <td class="text-center">
                                <form method="POST" action="{{ route('collections.items.destroy', $it) }}"
                                      onsubmit="return confirm('Delete this item?')" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit" title="Delete">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>

                <button class="btn btn-primary">Save Data</button>
                <a class="btn btn-link" href="{{ route('collections.show',$collection) }}">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(function () {

    // -----------------------------
    // Helpers
    // -----------------------------
    function uid() {
        return 'n' + Math.random().toString(16).slice(2);
    }

    function isNumeric(v) {
        return /^\d+$/.test(v + '');
    }

    // -----------------------------
    // Select2 for Manufacturer (depends on category)
    // -----------------------------
    function initManufacturer($manSelect, getCategoryId) {
        $manSelect.select2({
            placeholder: '-- Manufacturer --',
            allowClear: true,
            tags: true,
            width: '100%',
            ajax: {
                url: function () {
                    var catId = getCategoryId();
                    return "{{ route('ajax.categories.manufacturers', ':id') }}".replace(':id', catId || 0);
                },
                dataType: 'json',
                delay: 200,
                data: function (params) {
                    return { q: params.term || '' };
                },
                processResults: function (data) {
                    // your backend returns {results:[...]} so return directly
                    return data;
                }
            },
            createTag: function (params) {
                var term = $.trim(params.term);
                if (!term) return null;
                return { id: term, text: term, newTag: true };
            }
        });
    }

    function initModel($select, getCategoryIdFn, getManufacturerValFn){
        $($select).select2({
            placeholder: '-- Model --',
            allowClear: true,
            tags: true,
            width: '100%',

            ajax: {
            transport: async (params, success, failure) => {
                try {
                const manufacturerVal = getManufacturerValFn();
                const categoryId = getCategoryIdFn();

                // 🚫 if manufacturer is NEW (string), skip ajax
                if (!manufacturerVal || isNaN(Number(manufacturerVal))) {
                    return success({ results: [] });
                }

                let url = "{{ route('ajax.manufacturers.models', ':id') }}"
                    .replace(':id', manufacturerVal);

                url += '?category_id=' + encodeURIComponent(categoryId || '');
                url += '&q=' + encodeURIComponent(params.data?.term || '');

                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();

                success(data);
                } catch (e) {
                failure(e);
                }
            },
            delay: 200,
            processResults: data => data
            },

            createTag: function (params) {
            const term = (params.term || '').trim();
            if (!term) return null;
            return {
                id: term,
                text: term,
                newTag: true
            };
            }
        });
    }


    // -----------------------------
    // Select2 for Model (depends on category + manufacturer)
    // -----------------------------
    
    // -----------------------------
    // When user types new Manufacturer / Model (tags)
    // We store text into hidden input and clear numeric id
    // -----------------------------
    function bindTagBehavior($row) {

        // Manufacturer change
        $row.find('.manSel').on('change', function () {
            var val = $(this).val();
            var text = $(this).find('option:selected').text();

            if (val && !isNumeric(val)) {
                $row.find('.manText').val(text);
                // DO NOT clear select2 value
            } else {
                $row.find('.manText').val('');
            }

            // reset model when manufacturer changes
            $row.find('.modelSel').val(null).trigger('change.select2');
            $row.find('.modelText').val('');
        });

        // Model change
        $row.find('.modelSel').on('change', function () {
            var val = $(this).val();
            var text = $(this).find('option:selected').text();

            if (val && !isNumeric(val)) {
                $row.find('.modelText').val(text);
            } else {
                $row.find('.modelText').val('');
            }
        });

        // Category change (inside table)
        $row.find('.categorySel').on('change', function () {
            $row.find('.manSel').val(null).trigger('change.select2');
            $row.find('.modelSel').val(null).trigger('change.select2');
            $row.find('.manText').val('');
            $row.find('.modelText').val('');
        });
    }

    // -----------------------------
    // Init a single row (existing or new)
    // -----------------------------
    function initRow($row) {
        var getCat = function () { return $row.find('.categorySel').val(); };
        var getMan = function () { return $row.find('.manSel').val(); };

        initManufacturer($row.find('.manSel'), getCat);
        initModel($row.find('.modelSel'), getCat, getMan);
        bindTagBehavior($row);
    }

    // -----------------------------
    // Init EXISTING rows
    // -----------------------------
    $('#itemsTbody tr[data-row="existing"]').each(function () {
        initRow($(this));
    });

    // -----------------------------
    // TOP BAR Select2 (bulk controls)
    // -----------------------------
    var $bulkCat   = $('#bulk_category');
    var $bulkMan   = $('#bulk_manufacturer');
    var $bulkModel = $('#bulk_model');

    initManufacturer($bulkMan, function () { return $bulkCat.val(); });
    initModel($bulkModel, function () { return $bulkCat.val(); }, function () { return $bulkMan.val(); });

    $bulkCat.on('change', function () {
        $bulkMan.val(null).trigger('change.select2');
        $bulkModel.val(null).trigger('change.select2');
    });

    $bulkMan.on('change', function () {
        $bulkModel.val(null).trigger('change.select2');
    });

    // -----------------------------
    // Add Qty rows (NOT DB)
    // -----------------------------
    $('#bulk_add_btn').on('click', function () {

        var qty = parseInt($('#bulk_qty').val() || 1, 10);
        qty = Math.max(1, Math.min(500, qty));

        var catId = $bulkCat.val();

        var manVal  = $bulkMan.val();
        var manText = $bulkMan.find('option:selected').text();

        var modelVal  = $bulkModel.val();
        var modelText = $bulkModel.find('option:selected').text();

        for (var i = 0; i < qty; i++) {
            var key = uid();

            var $tr = $(`
                <tr data-row="new">
                    <td><em>new</em></td>

                    <td><input class="form-control" name="new_items[${key}][qty]" value="1"></td>

                    <td>
                        <select class="form-control categorySel" name="new_items[${key}][category_id]">
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </td>

                    <td>
                        <select class="form-control manSel" name="new_items[${key}][manufacturer_id]" style="width:100%"></select>
                        <input type="hidden" class="manText" name="new_items[${key}][manufacturer_text]" value="">
                    </td>

                    <td>
                        <select class="form-control modelSel" name="new_items[${key}][product_model_id]" style="width:100%"></select>
                        <input type="hidden" class="modelText" name="new_items[${key}][model_text]" value="">
                    </td>

                    <td><input class="form-control" name="new_items[${key}][serial_number]" value=""></td>
                    <td><input class="form-control" name="new_items[${key}][asset_tags]" value=""></td>
                    <td><input class="form-control" name="new_items[${key}][dimensions]" value=""></td>
                    <td><input class="form-control" name="new_items[${key}][weight_kg]" value="0"></td>
                    <td class="text-center"><input type="checkbox" name="new_items[${key}][erasure_required]" value="1"></td>

                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger removeNew" title="Remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `);

            $('#itemsTbody').append($tr);

            // set category from top bar
            $tr.find('.categorySel').val(catId);

            // init select2 & behavior
            initRow($tr);

            // prefill manufacturer
            if (manVal && isNumeric(manVal)) {
                var op = new Option(manText, manVal, true, true);
                $tr.find('.manSel').append(op).trigger('change.select2');
            } else if (manVal && !isNumeric(manVal)) {
                var opt = new Option(manText, manVal, true, true);
                $tr.find('.manSel').append(opt).trigger('change.select2');
                $tr.find('.manText').val(manText);
            }

            // prefill model
            if (modelVal && isNumeric(modelVal)) {
                var opm = new Option(modelText, modelVal, true, true);
                $tr.find('.modelSel').append(opm).trigger('change.select2');
            } else if (modelVal && !isNumeric(modelVal)) {
                var optm = new Option(modelText, modelVal, true, true);
                $tr.find('.modelSel').append(optm).trigger('change.select2');
                $tr.find('.modelText').val(modelText);
            }

            // remove new row
            $tr.find('.removeNew').on('click', function () {
                $(this).closest('tr').remove();
            });
        }
    });

});
</script>

@endpush
