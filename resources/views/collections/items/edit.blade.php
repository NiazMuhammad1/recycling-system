@extends('adminlte::page')
@section('title', 'Edit Items')
@section('plugins.Select2', true)

@section('content')
@push('css')
<style>
    canvas { display:block; }

    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px);
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 2.25rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 2.25rem;
    }
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
            <div class="form-row align-items-end mb-3">
                <div class="col-md-2">
                    <label>Qty</label>
                    <input class="form-control" type="number" id="bulk_qty" value="1" min="1" max="500">
                </div>

                <div class="col-md-3">
                    <label>Category</label>
                    <select class="form-control" id="bulk_category">
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}"
                                    data-name="{{ $c->name }}"
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
                    <label>Manufacturer</label>
                    <select class="form-control" id="bulk_manufacturer" style="width:100%"></select>
                </div>

                <div class="col-md-2">
                    <label>Model</label>
                    <select class="form-control" id="bulk_model" style="width:100%"></select>
                </div>

                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-secondary btn-block" id="bulk_add_btn">
                        <i class="fas fa-clone"></i> Add Rows
                    </button>
                </div>
            </div>

            {{-- SAVE FORM --}}
            <form method="POST" action="{{ route('collections.items.update',$collection) }}" id="itemsForm">
                @csrf
                @method('PUT')

                <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                    <tr>
                        <th style="width:90px;">#</th>
                        <th style="width:80px;">Qty</th>
                        <th style="width:260px;">Category</th>
                        <th style="width:180px;">Manufacturer</th>
                        <th style="width:180px;">Model</th>
                        <th style="width:110px;">Weight (kg)</th>
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
                            <td>
                                @if($it->codes->count())
                                    @foreach($it->codes->sortBy('seq') as $code)
                                        <span class="badge badge-info mr-1">
                                            {{ $code->item_prefix }}/{{ str_pad($code->seq,3,'0',STR_PAD_LEFT) }}
                                            <i class="fas fa-times text-white ml-1 delete-code"
                                               style="cursor:pointer;"
                                               data-url="{{ route('collection-items.codes.destroy',$code->id) }}">
                                            </i>
                                        </span>
                                    @endforeach
                                @else
                                    ____
                                @endif

                                <a href="#"
                                   class="assign-code ml-1"
                                   data-id="{{ $it->id }}"
                                   title="Assign Code">
                                    <i class="fas fa-hashtag text-primary"></i>
                                </a>
                            </td>

                            <td>
                                <input class="form-control form-control-sm"
                                       name="items[{{ $it->id }}][qty]"
                                       value="{{ old("items.$it->id.qty", $it->qty) }}">
                            </td>

                            <td style="display:flex; width:350px;">
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
                                <select class="form-control form-control-sm manSel"
                                        name="items[{{ $it->id }}][manufacturer_id]"
                                        style="width:100%">
                                    @if($it->manufacturerRel)
                                        <option value="{{ $it->manufacturerRel->id }}" selected>
                                            {{ $it->manufacturerRel->name }}
                                        </option>
                                    @elseif(!empty($it->manufacturer_text))
                                        <option value="{{ $it->manufacturer_text }}" selected>
                                            {{ $it->manufacturer_text }}
                                        </option>
                                    @endif
                                </select>
                                <input type="hidden"
                                       class="manText"
                                       name="items[{{ $it->id }}][manufacturer_text]"
                                       value="{{ old("items.$it->id.manufacturer_text", $it->manufacturer_text) }}">
                            </td>

                            <td>
                                <select class="form-control form-control-sm modelSel"
                                        name="items[{{ $it->id }}][product_model_id]"
                                        style="width:100%">
                                    @if($it->productModel)
                                        <option value="{{ $it->productModel->id }}" selected>
                                            {{ $it->productModel->name }}
                                        </option>
                                    @elseif(!empty($it->model_text))
                                        <option value="{{ $it->model_text }}" selected>
                                            {{ $it->model_text }}
                                        </option>
                                    @endif
                                </select>
                                <input type="hidden"
                                       class="modelText"
                                       name="items[{{ $it->id }}][model_text]"
                                       value="{{ old("items.$it->id.model_text", $it->model_text) }}">
                            </td>

                            <td>
                                <input class="form-control form-control-sm weightInput"
                                       name="items[{{ $it->id }}][weight_kg]"
                                       value="{{ old("items.$it->id.weight_kg", $it->weight_kg ?? '') }}">
                            </td>

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
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger delete-item"
                                        data-id="{{ $it->id }}"
                                        data-url="{{ route('collection-items.destroy',$it->id) }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                @if($isCollect)
                    <input type="hidden" name="mode_type" value="collect">

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
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="clientClear">Clear</button>
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

                            <input type="hidden" name="driver_signature"
                                   id="driver_signature"
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

                <button type="submit" name="mode" value="save" class="btn btn-primary">
                    Save Data
                </button>

                @if($isCollect)
                    <button type="submit" name="mode" value="send_pdf" class="btn btn-success">
                        Save Data and Send PDFs
                    </button>
                @endif

                <a class="btn btn-link" href="{{ route('collections.show',$collection) }}">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
(function () {
    function drawFromHidden(canvas, hiddenValue) {
        if (!hiddenValue || !hiddenValue.startsWith('data:image')) return;

        const ctx = canvas.getContext('2d');
        const img = new Image();
        img.onload = function () {
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

        function resizeAndRedraw() {
            const rect = canvas.getBoundingClientRect();
            const w = Math.max(1, Math.floor(rect.width));
            const h = 160;
            const existing = hidden.value;

            canvas.width = w;
            canvas.height = h;

            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#000';

            drawFromHidden(canvas, existing);
        }

        setTimeout(resizeAndRedraw, 150);
        window.addEventListener('resize', () => setTimeout(resizeAndRedraw, 150));

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
            hidden.value = canvas.toDataURL('image/png');
            e && e.preventDefault();
        }

        canvas.addEventListener('mousedown', start);
        canvas.addEventListener('mousemove', move);
        window.addEventListener('mouseup', end);

        canvas.addEventListener('touchstart', start, {passive:false});
        canvas.addEventListener('touchmove', move, {passive:false});
        window.addEventListener('touchend', end, {passive:false});

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                hidden.value = '';
            });
        }
    }

    initPad('clientCanvas', 'client_signature', 'clientClear');
    initPad('driverCanvas', 'driver_signature', 'driverClear');
})();

$(function () {
    var IS_COLLECT = @json($isCollect);

    function uid() {
        return 'n' + Math.random().toString(16).slice(2);
    }

    function isNumeric(v) {
        return /^\d+$/.test((v || '') + '');
    }

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

    function initModel($select, getCategoryIdFn, getManufacturerValFn) {
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

    function bindTagBehavior($row) {
        $row.find('.manSel').on('change', function () {
            var val = $(this).val();
            var text = $(this).find('option:selected').text();

            if (val && !isNumeric(val)) {
                $row.find('.manText').val(text);
            } else {
                $row.find('.manText').val('');
            }

            $row.find('.modelSel').val(null).trigger('change.select2');
            $row.find('.modelText').val('');
        });

        $row.find('.modelSel').on('change', function () {
            var val = $(this).val();
            var text = $(this).find('option:selected').text();

            if (val && !isNumeric(val)) {
                $row.find('.modelText').val(text);
            } else {
                $row.find('.modelText').val('');
            }
        });

        $row.find('.categorySel').on('change', function () {
            overwriteFieldsFromCategory($row);

            $row.find('.manSel').val(null).trigger('change.select2');
            $row.find('.modelSel').val(null).trigger('change.select2');
            $row.find('.manText').val('');
            $row.find('.modelText').val('');
        });
    }

    function initRow($row) {
        fillEmptyFieldsFromCategory($row);

        var getCat = function () { return $row.find('.categorySel').val(); };
        var getMan = function () { return $row.find('.manSel').val(); };

        initManufacturer($row.find('.manSel'), getCat);
        initModel($row.find('.modelSel'), getCat, getMan);
        bindTagBehavior($row);
    }

    $('#itemsTbody tr').each(function () {
        initRow($(this));
    });

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

    $('#bulk_add_btn').on('click', function () {
        var qty = parseInt($('#bulk_qty').val() || 1, 10);
        qty = Math.max(1, Math.min(500, qty));

        var bulkCatId = $bulkCat.val();

        var manVal  = $bulkMan.val();
        var manText = $bulkMan.find('option:selected').text();

        var modelVal  = $bulkModel.val();
        var modelText = $bulkModel.find('option:selected').text();

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

                    <td style="display:flex; width:350px;">
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
                                        data-hazard="{{ $c->hazard_codes }}"
                                        data-type="{{ $c->type }}">
                                    {{ $c->name }} / {{ str_replace('_', ' ', $c->type) }}
                                </option>
                            @endforeach
                        </select>
                        &nbsp;&nbsp;
                        <input class="form-control form-control-sm categoryNameInput"
                               name="new_items[${key}][category_name]" value="">
                    </td>

                    <td>
                        <select class="form-control form-control-sm manSel"
                                name="new_items[${key}][manufacturer_id]"
                                style="width:100%"></select>
                        <input type="hidden" class="manText"
                               name="new_items[${key}][manufacturer_text]" value="">
                    </td>

                    <td>
                        <select class="form-control form-control-sm modelSel"
                                name="new_items[${key}][product_model_id]"
                                style="width:100%"></select>
                        <input type="hidden" class="modelText"
                               name="new_items[${key}][model_text]" value="">
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

            $tr.find('.categorySel').val(bulkCatId);

            initRow($tr);

            if (manVal && isNumeric(manVal)) {
                var op = new Option(manText, manVal, true, true);
                $tr.find('.manSel').append(op).trigger('change.select2');
            } else if (manVal && !isNumeric(manVal)) {
                var opt = new Option(manText, manVal, true, true);
                $tr.find('.manSel').append(opt).trigger('change.select2');
                $tr.find('.manText').val(manText);
            }

            if (modelVal && isNumeric(modelVal)) {
                var opm = new Option(modelText, modelVal, true, true);
                $tr.find('.modelSel').append(opm).trigger('change.select2');
            } else if (modelVal && !isNumeric(modelVal)) {
                var optm = new Option(modelText, modelVal, true, true);
                $tr.find('.modelSel').append(optm).trigger('change.select2');
                $tr.find('.modelText').val(modelText);
            }

            $tr.find('.removeNew').on('click', function () {
                $(this).closest('tr').remove();
            });
        }
    });
});

let assignUrlTemplate = "{{ route('collection-items.assignCode', ':id') }}";

$(document).on('click','.assign-code',function(e){
    e.preventDefault();

    let id = $(this).data('id');
    let url = assignUrlTemplate.replace(':id', id);

    $.post(url,{
        _token:'{{ csrf_token() }}'
    },function(res){
        location.reload();
    });
});

$(document).on('click','.delete-item',function(){
    if(!confirm('Delete this item?')) return;

    let url = $(this).data('url');
    let row = $(this).closest('tr');

    $.ajax({
        url:url,
        type:'DELETE',
        data:{
            _token:'{{ csrf_token() }}'
        },
        success:function(){
            row.remove();
        }
    });
});

$(document).on('click','.delete-code',function(e){
    e.stopPropagation();

    if(!confirm('Delete this code?')) return;

    let url = $(this).data('url');
    let badge = $(this).closest('.badge');

    $.ajax({
        url:url,
        type:'DELETE',
        data:{
            _token:'{{ csrf_token() }}'
        },
        success:function(){
            badge.remove();
        }
    });
});
</script>
@endpush