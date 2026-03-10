@extends('adminlte::page')
@section('title', 'Edit Items')
@section('plugins.Select2', true) 
@section('content')
@push('css')
<style>
  canvas { display:block; }
</style>
@endpush
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
                                {{ $c->name }} / {{ str_replace('_', ' ', $c->type)}}
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

                            <td style="display:flex; width: 350px;">

                                <select class="form-control form-control-sm categorySel"
                                        name="items[{{ $it->id }}][category_id]">

                                @foreach($categories as $c)

                                <option value="{{ $c->id }}"
                                        data-name="{{ $c->name }}"
                                        data-ewc="{{ $c->ewc_code }}"
                                        data-def-weight="{{ $c->default_weight_kg }}"
                                        data-component="{{ $c->component }}"
                                        data-concentration="{{ $c->concentration }}"
                                        data-form="{{ $c->physical_form }}"
                                        data-hazard="{{ $c->hazard_codes }}"
                                        data-type="{{ $c->type }}"
                                        {{ $it->category_id == $c->id ? 'selected':'' }}>

                                {{ $c->name }} / {{ str_replace('_',' ',$c->type) }}

                                </option>

                                @endforeach

                                </select>
                                &nbsp;&nbsp;
                                <input class="form-control form-control-sm categoryNameInput"
                                    name="items[{{ $it->id }}][category_name]"
                                    value="{{ $it->category_name ?? $it->category?->name }}">

                                </td>

                            <td>
                                <input class="form-control form-control-sm weightInput"
                                    name="items[{{ $it->id }}][weight_kg]"
                                    value="{{ old("items.$it->id.weight_kg", $it->weight_kg ?? '') }}">
                            </td>

                            {{-- Read-only category info columns --}}
                            <td>
                                <input class="form-control form-control-sm ewcInput"
                                    name="items[{{ $it->id }}][ewc_code]"
                                    value="{{ $it->ewc_code ?? $it->category?->ewc_code }}">
                                </td>

                                <td>
                                <input class="form-control form-control-sm componentInput"
                                    name="items[{{ $it->id }}][component]"
                                    value="{{ $it->component ?? $it->category?->component }}">
                                </td>

                                <td>
                                <input class="form-control form-control-sm concentrationInput"
                                    name="items[{{ $it->id }}][concentration]"
                                    value="{{ $it->concentration ?? $it->category?->concentration }}">
                                </td>

                                <td>
                                <input class="form-control form-control-sm formInput"
                                    name="items[{{ $it->id }}][physical_form]"
                                    value="{{ $it->physical_form ?? $it->category?->physical_form }}">
                                </td>

                                <td>
                                <input class="form-control form-control-sm hazardInput"
                                    name="items[{{ $it->id }}][hazard_codes]"
                                    value="{{ $it->hazard_codes ?? $it->category?->hazard_codes }}">
                                </td>

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
                @if($isCollect)
                <input type="hidden" name="mode" value="collect">

                <div class="row mt-3">

                    {{-- CLIENT SIGNATURE --}}
                    <div class="col-md-6">
                        <label>Client Signature</label>

                        <input type="hidden" name="client_signature"
                            id="client_signature"
                            value="{{ old('client_signature', $collection->client_signature) }}">

                        <div class="border rounded bg-white p-2">
                            <canvas id="clientCanvas" height="160" style="width:100%;"></canvas>
                        </div>

                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                    id="clientClear">Clear</button>
                        </div>

                        <div class="mt-2">
                            <label>Print Name</label>
                            <input class="form-control"
                                name="client_print_name"
                                value="{{ old('client_print_name', $collection->client_print_name) }}">
                        </div>
                    </div>


                    {{-- DRIVER SIGNATURE --}}
                    <div class="col-md-6">
                        <label>Driver Signature</label>

                        <input type="hidden" name="driver_signature" id="driver_signature"
                            value="{{ old('driver_signature',
                                $collection->driver_signature ?: ($collection->user->signature ?? null)
                            ) }}">

                        <div class="border rounded bg-white p-2">
                            <canvas id="driverCanvas" height="160" style="width:100%;"></canvas>
                        </div>

                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="driverClear">Clear</button>
                        </div>

                        <div class="mt-2">
                            <label>Print Name</label>
                            <input class="form-control" name="driver_print_name"
                                value="{{ old('driver_print_name',
                                        $collection->driver_print_name
                                        ?: ($collection->user->signature_print_name ?? $collection->user->name ?? auth()->user()->name)
                                ) }}">
                        </div>
                    </div>

                </div>
                @endif
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
(function () {

  function drawFromHidden(canvas, hiddenValue) {
    if (!hiddenValue || !hiddenValue.startsWith('data:image')) return;

    const ctx = canvas.getContext('2d');
    const img = new Image();
    img.onload = function () {
      // Clear then draw scaled to canvas size
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
    };
    img.src = hiddenValue;
  }

  function initPad(canvasId, hiddenId, clearBtnId) {
    const canvas = document.getElementById(canvasId);
    const hidden = document.getElementById(hiddenId);
    const clearBtn = document.getElementById(clearBtnId);

    if (!canvas || !hidden) return;

    const ctx = canvas.getContext('2d');
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#000';

    // Resize canvas to match its displayed size (VERY IMPORTANT)
    function resizeAndRedraw() {
      const rect = canvas.getBoundingClientRect();
      const w = Math.max(1, Math.floor(rect.width)); // prevent 0
      const h = 160;

      // resizing clears canvas, so save existing hidden value first
      const existing = hidden.value;

      canvas.width = w;
      canvas.height = h;

      // restore drawing styles after resize
      ctx.lineWidth = 2;
      ctx.lineCap = 'round';
      ctx.strokeStyle = '#000';

      drawFromHidden(canvas, existing);
    }

    // Delay init to allow AdminLTE layout to finish
    setTimeout(resizeAndRedraw, 150);
    window.addEventListener('resize', () => setTimeout(resizeAndRedraw, 150));

    // ---- drawing logic (mouse + touch) ----
    let drawing = false;
    let last = {x:0, y:0};

    function getPos(e) {
      const r = canvas.getBoundingClientRect();
      const clientX = e.touches ? e.touches[0].clientX : e.clientX;
      const clientY = e.touches ? e.touches[0].clientY : e.clientY;
      return { x: clientX - r.left, y: clientY - r.top };
    }

    function start(e) {
      drawing = true;
      last = getPos(e);
      e.preventDefault();
    }

    function move(e) {
      if (!drawing) return;
      const p = getPos(e);
      ctx.beginPath();
      ctx.moveTo(last.x, last.y);
      ctx.lineTo(p.x, p.y);
      ctx.stroke();
      last = p;
      e.preventDefault();
    }

    function end(e) {
      if (!drawing) return;
      drawing = false;
      hidden.value = canvas.toDataURL('image/png'); // save
      e && e.preventDefault();
    }

    canvas.addEventListener('mousedown', start);
    canvas.addEventListener('mousemove', move);
    window.addEventListener('mouseup', end);

    canvas.addEventListener('touchstart', start, {passive:false});
    canvas.addEventListener('touchmove', move, {passive:false});
    window.addEventListener('touchend', end, {passive:false});

    // Clear button
    if (clearBtn) {
      clearBtn.addEventListener('click', function () {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hidden.value = '';
      });
    }
  }

  // Init both canvases
  initPad('clientCanvas', 'client_signature', 'clientClear');
  initPad('driverCanvas', 'driver_signature', 'driverClear');

})();


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
        name: $opt.data('name') || '',
        ewc: $opt.data('ewc') || '',
        defWeight: $opt.data('def-weight') || '',
        component: $opt.data('component') || '',
        concentration: $opt.data('concentration') || '',
        form: $opt.data('form') || '',
        hazard: $opt.data('hazard') || '',
        type: $opt.data('type') || ''
    };
}

// only fill empty fields
function fillEmptyFieldsFromCategory($row) {
    var meta = getCategoryMeta($row.find('.categorySel'));

    var $categoryName = $row.find('.categoryNameInput');
    var $ewc = $row.find('.ewcInput');
    var $weight = $row.find('.weightInput');
    var $component = $row.find('.componentInput');
    var $concentration = $row.find('.concentrationInput');
    var $form = $row.find('.formInput');
    var $hazard = $row.find('.hazardInput');

    if (!$categoryName.val()) $categoryName.val(meta.name);
    if (!$ewc.val()) $ewc.val(meta.ewc);
    if (!$weight.val() && meta.defWeight !== '') $weight.val(meta.defWeight);
    if (!$component.val()) $component.val(meta.component);
    if (!$concentration.val()) $concentration.val(meta.concentration);
    if (!$form.val()) $form.val(meta.form);
    if (!$hazard.val()) $hazard.val(meta.hazard);
}

// overwrite fields when user changes category
function overwriteFieldsFromCategory($row) {
    var meta = getCategoryMeta($row.find('.categorySel'));

    $row.find('.categoryNameInput').val(meta.name);
    $row.find('.ewcInput').val(meta.ewc);
    $row.find('.weightInput').val(meta.defWeight);
    $row.find('.componentInput').val(meta.component);
    $row.find('.concentrationInput').val(meta.concentration);
    $row.find('.formInput').val(meta.form);
    $row.find('.hazardInput').val(meta.hazard);
}

function initRow($row) {
    // existing rows: keep DB values, only fill blanks
    fillEmptyFieldsFromCategory($row);

    // when category changes manually, overwrite with selected category defaults
    $row.find('.categorySel').on('change', function () {
        overwriteFieldsFromCategory($row);
    });
}

    // Fill info columns in a row
    function applyCategoryMetaToRow($row) {

        var $opt = $row.find('.categorySel option:selected');

        var name = $opt.data('name') || '';
        var ewc = $opt.data('ewc') || '';
        var weight = $opt.data('def-weight') || '';
        var component = $opt.data('component') || '';
        var concentration = $opt.data('concentration') || '';
        var form = $opt.data('form') || '';
        var hazard = $opt.data('hazard') || '';

        $row.find('.categoryNameInput').val(name);
        $row.find('.ewcInput').val(ewc);
        $row.find('.componentInput').val(component);
        $row.find('.concentrationInput').val(concentration);
        $row.find('.formInput').val(form);
        $row.find('.hazardInput').val(hazard);

        var $weight = $row.find('.weightInput');

        if (!$weight.val() && weight) {
            $weight.val(weight);
        }
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

                <td style="display:flex; width: 350px;">

                <select class="form-control form-control-sm categorySel"
                        name="new_items[${key}][category_id]">

                @foreach($categories as $c)

                <option value="{{ $c->id }}"
                        data-name="{{ $c->name }}"
                        data-ewc="{{ $c->ewc_code }}"
                        data-def-weight="{{ $c->default_weight_kg }}"
                        data-component="{{ $c->component }}"
                        data-concentration="{{ $c->concentration }}"
                        data-form="{{ $c->physical_form }}"
                        data-hazard="{{ $c->hazard_codes }}">

                {{ $c->name }} / {{ str_replace('_', ' ', $c->type) }}

                </option>

                @endforeach

                </select>
                &nbsp;&nbsp;
                <input class="form-control form-control-sm categoryNameInput"
                    name="new_items[${key}][category_name]" value="">

                </td>

                <td>
                <input class="form-control form-control-sm weightInput"
                    name="new_items[${key}][weight_kg]" value="">
                </td>

                <td>
                <input class="form-control form-control-sm ewcInput"
                    name="new_items[${key}][ewc_code]" value="">
                </td>

                <td>
                <input class="form-control form-control-sm componentInput"
                    name="new_items[${key}][component]" value="">
                </td>

                <td>
                <input class="form-control form-control-sm concentrationInput"
                    name="new_items[${key}][concentration]" value="">
                </td>

                <td>
                <input class="form-control form-control-sm formInput"
                    name="new_items[${key}][physical_form]" value="">
                </td>

                <td>
                <input class="form-control form-control-sm hazardInput"
                    name="new_items[${key}][hazard_codes]" value="">
                </td>

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