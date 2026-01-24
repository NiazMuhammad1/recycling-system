@extends('adminlte::page')
@section('title', 'Edit Items')

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
                    @foreach($collection->items->sortBy('seq') as $it)
                        <tr data-row="existing" data-id="{{ $it->id }}">
                            <td>{{ $it->item_number ?? $collection->collection_number.'-'.str_pad($it->seq ?? 0, 3, '0', STR_PAD_LEFT) }}</td>

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
                                    @if($it->manufacturer)
                                        <option value="{{ $it->manufacturer->id }}" selected>{{ $it->manufacturer->name }}</option>
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

                <button class="btn btn-primary">Save</button>
                <a class="btn btn-link" href="{{ route('collections.show',$collection) }}">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<style>
    .select2-container .select2-selection--single { height: calc(2.25rem + 2px); }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 2.25rem; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 2.25rem; }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
(function () {
    const $tbody = document.getElementById('itemsTbody');

    // ------- helpers -------
    function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m])); }
    function uid(){ return 'n' + Math.random().toString(16).slice(2); }

    function initManufacturerSelect($select, getCategoryIdFn) {
  $($select).select2({
    placeholder: '-- Manufacturer --',
    allowClear: true,
    tags: true,
    width: '100%',
    ajax: {
      url: function () {
        const catId = getCategoryIdFn();
        return "{{ route('ajax.categories.manufacturers', ':id') }}".replace(':id', catId || 0);
      },
      dataType: 'json',
      delay: 200,
      data: function (params) {
        return { q: params.term || '' };
      },
      processResults: function (data) {
        // IMPORTANT: your endpoint already returns {results:[...]}
        return data;
      }
    },
    // this guarantees Add "xxxx" appears even if ajax returns empty
    createTag: function (params) {
      const term = (params.term || '').trim();
      if (!term) return null;
      return { id: term, text: term, newTag: true };
    }
  });
}


    function initModelSelect($select, getCategoryIdFn, getManufacturerValFn){
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


    function bindRowDependentReload(tr){
        const catSel = tr.querySelector('.categorySel');
        if (!catSel) return;

        catSel.addEventListener('change', function(){
            // wipe manufacturer + model when category changes
            const manSel = tr.querySelector('.manSel');
            const modelSel = tr.querySelector('.modelSel');

            $(manSel).val(null).trigger('change');
            $(modelSel).val(null).trigger('change');

            tr.querySelector('.manText').value = '';
            tr.querySelector('.modelText').value = '';
        });
    }

    function initRow(tr){
        const getCat = () => tr.querySelector('.categorySel')?.value || '';
        const getMan = () => tr.querySelector('.manSel') ? $(tr.querySelector('.manSel')).val() : '';

        initManufacturerSelect(tr.querySelector('.manSel'), getCat);
        initModelSelect(tr.querySelector('.modelSel'), getCat, () => {
            const v = $(tr.querySelector('.manSel')).val();
            return v && !isNaN(Number(v)) ? v : '';
        });
        bindRowDependentReload(tr);
    }

    // init existing rows
    document.querySelectorAll('tr[data-row="existing"]').forEach(initRow);

    // ------- TOP BAR select2 -------
    const bulkCategory = document.getElementById('bulk_category');
    const bulkMan = document.getElementById('bulk_manufacturer');
    const bulkModel = document.getElementById('bulk_model');

    // bulk manufacturer depends on bulk category
    $(bulkMan).select2({
        placeholder: '-- Manufacturer --',
        allowClear: true,
        tags: true,
        width: '100%',
        ajax: {
            transport: async (params, success, failure) => {
                try {
                    const categoryId = bulkCategory.value;
                    if (!categoryId) return success({results: []});

                    const url = "{{ route('ajax.categories.manufacturers', ':id') }}".replace(':id', categoryId);
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();

                    const term = (params.data?.term || '').toLowerCase();
                    const filtered = term ? data.filter(x => (x.text||'').toLowerCase().includes(term)) : data;

                    success({ results: filtered });
                } catch(e){ failure(e); }
            },
            delay: 150,
            processResults: (data) => data
        }
    });

    $(bulkModel).select2({
        placeholder: '-- Model --',
        allowClear: true,
        tags: true,
        width: '100%',
        ajax: {
            transport: async (params, success, failure) => {
                try {
                    const categoryId = bulkCategory.value;
                    const manId = $(bulkMan).val();
                    if (!categoryId || !manId || isNaN(Number(manId))) return success({results: []});

                    let url = "{{ route('ajax.manufacturers.models', ':id') }}".replace(':id', manId);
                    url += '?category_id=' + encodeURIComponent(categoryId);

                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();

                    const term = (params.data?.term || '').toLowerCase();
                    const filtered = term ? data.filter(x => (x.text||'').toLowerCase().includes(term)) : data;

                    success({ results: filtered });
                } catch(e){ failure(e); }
            },
            delay: 150,
            processResults: (data) => data
        }
    });

    bulkCategory.addEventListener('change', function(){
        $(bulkMan).val(null).trigger('change');
        $(bulkModel).val(null).trigger('change');
    });

    $(bulkMan).on('change', function(){
        $(bulkModel).val(null).trigger('change');
    });

    // ------- add rows -------
    document.getElementById('bulk_add_btn').addEventListener('click', function(){
        const qty = Math.max(1, Math.min(500, parseInt(document.getElementById('bulk_qty').value || '1', 10)));
        const catId = bulkCategory.value;
        const catText = bulkCategory.options[bulkCategory.selectedIndex].text;

        // manufacturer could be existing id or tag text
        const bulkManVal = $(bulkMan).val();
        const bulkManText = $(bulkMan).find('option:selected').text();
        const manId = (bulkManVal && !isNaN(Number(bulkManVal))) ? bulkManVal : '';
        const manText = (!manId && bulkManText) ? bulkManText : '';

        const bulkModelVal = $(bulkModel).val();
        const bulkModelText = $(bulkModel).find('option:selected').text();
        const modelId = (bulkModelVal && !isNaN(Number(bulkModelVal))) ? bulkModelVal : '';
        const modelText = (!modelId && bulkModelText) ? bulkModelText : '';

        for(let i=0;i<qty;i++){
            const key = uid();

            const tr = document.createElement('tr');
            tr.dataset.row = 'new';
            tr.innerHTML = `
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
            `;

            $tbody.appendChild(tr);

            // set category default from top bar
            const catSel = tr.querySelector('.categorySel');
            catSel.value = catId;

            // init select2 behavior on row
            initRow(tr);

            // set manufacturer/model from top bar if provided
            if (manId) {
                const op = new Option(bulkManText, manId, true, true);
                $(tr.querySelector('.manSel')).append(op).trigger('change');
            } else if (manText) {
                tr.querySelector('.manText').value = manText;
            }

            if (modelId) {
                const opm = new Option(bulkModelText, modelId, true, true);
                $(tr.querySelector('.modelSel')).append(opm).trigger('change');
            } else if (modelText) {
                tr.querySelector('.modelText').value = modelText;
            }

            tr.querySelector('.removeNew').addEventListener('click', function(){
                tr.remove();
            });
        }
    });

})();
</script>
@endpush
