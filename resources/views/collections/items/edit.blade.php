@extends('adminlte::page')
@section('title', 'Edit Items')
@section('plugins.Select2', true) 
@section('content')
@php $isCollect = ($mode ?? 'edit') === 'collect'; @endphp
<div class="container-fluid">
    <div class="d-flex align-items-center mb-2">
        <h1 class="mb-0">Collection {{ $collection->collection_number }}</h1>
        <a class="ml-2" href="{{ route('collections.show',$collection) }}">Back</a>
    </div>

    <div class="card">
        <div class="card-body">

            {{-- TOP BAR: adds rows only (no DB) --}}
            @php $isCollect = ($mode ?? 'edit') === 'collect'; @endphp

            <div class="form-row align-items-end mb-3">
                <div class="col-md-2">
                    <label>Qty</label>
                    <input class="form-control" type="number" id="bulk_qty" value="1" min="1" max="500">
                </div>

                <div class="col-md-6">
                    <label>Category</label>
                    <select class="form-control" id="bulk_category">
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}"
                                    data-ewc="{{ $c->ewc_code }}"
                                    data-def-weight="{{ $c->default_weight_kg }}"
                                    data-component="{{ $c->component }}"
                                    data-concentration="{{ $c->concentration }}"
                                    data-form="{{ $c->physical_form }}"
                                    data-hazard="{{ $c->hazard_codes }}"
                                    data-type="{{ $c->type }}">
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-secondary btn-block" id="bulk_add_btn">
                        <i class="fas fa-clone"></i> Add Rows
                    </button>
                </div>
            </div>

            {{-- SAVE FORM (updates existing + creates new) --}}
            <form method="POST" action="{{ route('collections.items.update',$collection) }}" id="itemsForm">
                @csrf
                @method('PUT')

                <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                    <tr>
                        <th style="width:90px;">#</th>
                        <th style="width:80px;">Qty</th>
                        <th style="width:260px;">Category</th>
                        <th style="width:110px;">Weight (kg)</th>

                        {{-- category info --}}
                        <th style="width:110px;">EWC</th>
                        <th style="width:160px;">Component</th>
                        <th style="width:140px;">Concentration</th>
                        <th style="width:110px;">Form</th>
                        <th style="width:90px;">Hazard</th>

                        @if($isCollect)
                            <th style="width:90px;" class="text-center">Collected</th>
                        @endif

                        <th style="width:70px;"></th>
                    </tr>
                    </thead>

                    <tbody id="itemsTbody">
                    {{-- Existing rows --}}
                    @foreach($collection->items->sortBy('item_code') as $it)
                        <tr data-row="existing" data-id="{{ $it->id }}">
                            <td>{{ $it->item_code ?? $collection->collection_number }}</td>

                            <td>
                                <input class="form-control form-control-sm"
                                    name="items[{{ $it->id }}][qty]"
                                    value="{{ old("items.$it->id.qty", $it->qty) }}">
                            </td>

                            <td>
                                <select class="form-control form-control-sm categorySel"
                                        name="items[{{ $it->id }}][category_id]">
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}"
                                                data-ewc="{{ $c->ewc_code }}"
                                                data-def-weight="{{ $c->default_weight_kg }}"
                                                data-component="{{ $c->component }}"
                                                data-concentration="{{ $c->concentration }}"
                                                data-form="{{ $c->physical_form }}"
                                                data-hazard="{{ $c->hazard_codes }}"
                                                data-type="{{ $c->type }}"
                                                {{ $it->category_id == $c->id ? 'selected' : '' }}>
                                            {{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <input class="form-control form-control-sm weightInput"
                                    name="items[{{ $it->id }}][weight_kg]"
                                    value="{{ old("items.$it->id.weight_kg", $it->weight_kg ?? '') }}">
                            </td>

                            {{-- Read-only category info columns --}}
                            <td class="ewcCell"></td>
                            <td class="componentCell"></td>
                            <td class="concentrationCell"></td>
                            <td class="formCell"></td>
                            <td class="hazardCell"></td>

                            @if($isCollect)
                                <td class="text-center">
                                    <input type="checkbox"
                                        name="items[{{ $it->id }}][is_collected]"
                                        value="1"
                                        {{ old("items.$it->id.is_collected", $it->is_collected) ? 'checked' : '' }}>
                                </td>
                            @endif

                            <td class="text-center">
                                {{-- keep empty or add delete later --}}
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
@push('css')
<style>
    .select2-container .select2-selection--single { height: calc(2.25rem + 2px); }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 2.25rem; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 2.25rem; }
</style>
@endpush
@push('js')
<script>
$(function () {

    var IS_COLLECT = @json($isCollect);

    // Generate unique keys for new rows
    function uid() {
        return 'n' + Math.random().toString(16).slice(2);
    }

    // Read category data-* from selected option
    function getCategoryMeta($select) {
        var $opt = $select.find('option:selected');
        return {
            ewc: $opt.data('ewc') || '',
            defWeight: $opt.data('def-weight') || '',
            component: $opt.data('component') || '',
            concentration: $opt.data('concentration') || '',
            form: $opt.data('form') || '',
            hazard: $opt.data('hazard') || '',
            type: $opt.data('type') || ''
        };
    }

    // Fill info columns in a row
    function applyCategoryMetaToRow($row) {
        var meta = getCategoryMeta($row.find('.categorySel'));

        $row.find('.ewcCell').text(meta.ewc);
        $row.find('.componentCell').text(meta.component);
        $row.find('.concentrationCell').text(meta.concentration);
        $row.find('.formCell').text(meta.form);
        $row.find('.hazardCell').text(meta.hazard);

        // If weight is empty, set default category weight
        var $weight = $row.find('.weightInput');
        if (!$weight.val() && meta.defWeight !== '') {
            $weight.val(meta.defWeight);
        }
    }

    // Init one row
    function initRow($row) {
        // initial fill
        applyCategoryMetaToRow($row);

        // when category changes, update info & maybe default weight
        $row.find('.categorySel').on('change', function () {
            applyCategoryMetaToRow($row);
        });
    }

    // Init all existing rows on page load
    $('#itemsTbody tr').each(function () {
        initRow($(this));
    });

    // Add rows from top bar
    $('#bulk_add_btn').on('click', function () {

        var qty = parseInt($('#bulk_qty').val() || 1, 10);
        qty = Math.max(1, Math.min(500, qty));

        var $bulkCat = $('#bulk_category');
        var bulkCatId = $bulkCat.val();

        for (var i = 0; i < qty; i++) {

            var key = uid();

            var collectedTd = IS_COLLECT
                ? `<td class="text-center"><input type="checkbox" name="new_items[${key}][is_collected]" value="1"></td>`
                : '';

            var $tr = $(`
                <tr data-row="new">
                    <td><em>new</em></td>

                    <td>
                        <input class="form-control form-control-sm"
                               name="new_items[${key}][qty]" value="1">
                    </td>

                    <td>
                        <select class="form-control form-control-sm categorySel"
                                name="new_items[${key}][category_id]">
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}"
                                        data-ewc="{{ $c->ewc_code }}"
                                        data-def-weight="{{ $c->default_weight_kg }}"
                                        data-component="{{ $c->component }}"
                                        data-concentration="{{ $c->concentration }}"
                                        data-form="{{ $c->physical_form }}"
                                        data-hazard="{{ $c->hazard_codes }}"
                                        data-type="{{ $c->type }}">
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    <td>
                        <input class="form-control form-control-sm weightInput"
                               name="new_items[${key}][weight_kg]" value="">
                    </td>

                    <td class="ewcCell"></td>
                    <td class="componentCell"></td>
                    <td class="concentrationCell"></td>
                    <td class="formCell"></td>
                    <td class="hazardCell"></td>

                    ${collectedTd}

                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger removeNew">
                            <i class="fas fa-times"></i>
                        </button>
                    </td>
                </tr>
            `);

            $('#itemsTbody').append($tr);

            // set category from bulk select
            $tr.find('.categorySel').val(bulkCatId);

            // init behavior
            initRow($tr);

            // remove button
            $tr.find('.removeNew').on('click', function () {
                $(this).closest('tr').remove();
            });
        }
    });

});
</script>
@endpush