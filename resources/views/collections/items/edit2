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

    .item-row-top td,
    .item-row-bottom td {
        vertical-align: top !important;
        padding: 8px !important;
    }

    .item-row-bottom {
        background: #fafbfc;
    }

    .item-row-bottom label.small {
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 4px;
    }

    #itemsTbody .select2-container {
        width: 100% !important;
    }

    .table td .form-control-sm,
    .table td .select2-container--default .select2-selection--single {
        min-height: 31px;
    }

    .item-delete-cell {
        vertical-align: middle !important;
        text-align: center;
    }

    .code-cell {
        min-width: 90px;
    }

    .main-table {
        table-layout: fixed;
    }

    .main-table td,
    .main-table th {
        word-wrap: break-word;
    }
    
</style>
@endpush

@php $isCollect = ($mode ?? 'edit') === 'collect'; @endphp

<div class="container-fluid">
    <div class="d-flex align-items-center mb-2 flex-wrap">

        <h5 class="mb-0 mr-3">
            Collection {{ $collection->collection_number }}
        </h5>

        <small class="text-muted">
            <a href="{{ route('collections.show',$collection) }}">Collection</a>
            <span class="mx-1">/</span>

            <a href="{{ route('collections.items.edit',$collection) }}">Edit</a>
            <span class="mx-1">/</span>

            <a href="{{ route('collections.collect.form',$collection) }}">Collect</a>
            <span class="mx-1">/</span>

            <a href="{{ route('collections.process.index',$collection) }}">Process</a>

            
        </small>

    </div>

    <div class="card">
        <div class="card-body">

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
                                    data-is_erasure="{{ $c->is_erasure }}"
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

            <form method="POST" action="{{ route('collections.items.update',$collection) }}" id="itemsForm">
                @csrf
                @method('PUT')

                <div class="table-responsive">
                    <table class="table table-bordered table-sm main-table">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:90px;">#</th>
                                <th style="width:80px;">Qty</th>
                                <th style="width:340px;">Category</th>
                                <th style="width:180px;">Manufacturer</th>
                                <th style="width:180px;">Model</th>
                                <th style="width:110px; display:none;">Weight</th>
                                <th style="width:110px;display:none;">EWC</th>
                                @if($isCollect)
                                <th style="width:90px;" class="text-center">
                                    Collected
                                    <br>
                                    <input type="checkbox" id="checkAllCollected">
                                </th>
                                @else
                                    <th style="width:90px;"></th>
                                @endif
                                <th style="width:110px;" class="text-center">Erasure Required
                                    <br>
                                    <input type="checkbox" id="checkAllErasure">
                                </th>
                                <th style="width:70px;"></th>
                            </tr>
                        </thead>

                        <tbody id="itemsTbody">
                        @foreach($collection->items->sortBy('item_code') as $index => $it)
                         @php $bg = $index % 2 ? 'bg-white' : 'bg-light'; @endphp
                            <tr data-row="existing" data-id="{{ $it->id }}" class="item-row-top {{ $bg }}">
                                <td rowspan="2" class="code-cell" style="">
                                    <!-- @if($it->codes->count())
                                        @foreach($it->codes->sortBy('seq') as $code)
                                            <span class="badge badge-info mr-1 mb-1 d-inline-block">
                                                {{ $code->item_prefix }}/{{ str_pad($code->seq,3,'0',STR_PAD_LEFT) }}
                                                <i class="fas fa-times text-white ml-1 delete-code"
                                                   style="cursor:pointer;"
                                                   data-url="{{ route('collection-items.codes.destroy',$code->id) }}"></i>
                                            </span>
                                        @endforeach
                                    @else
                                        ____
                                    @endif

                                    <div class="mt-1">
                                        <a href="#"
                                           class="assign-code"
                                           data-id="{{ $it->id }}"
                                           title="Assign Code">
                                            <i class="fas fa-hashtag text-primary"></i>
                                        </a>
                                    </div> --> {{$it->item_code}}
                                </td>

                                <td>
                                    <input class="form-control form-control-sm"
                                           name="items[{{ $it->id }}][qty]"
                                           value="{{ old("items.$it->id.qty", $it->qty) }}">
                                </td>

                                <td>
                                    <div class="d-flex">
                                        <select class="form-control form-control-sm categorySel"
                                                name="items[{{ $it->id }}][category_id]">
                                            @foreach($categories as $c)
                                                <option value="{{ $c->id }}"
                                                        data-name="{{ $c->name }}"
                                                        data-ewc="{{ $c->ewc_code }}"
                                                        data-is_erasure="{{ $c->is_erasure }}"
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

                                        <input class="form-control form-control-sm ml-2 categoryNameInput"
                                               name="items[{{ $it->id }}][category_name]"
                                               value="{{ $it->category_name ?? $it->category?->name }}">
                                    </div>
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

                                <td style="display:none;">
                                    <input class="form-control form-control-sm weightInput"
                                           name="items[{{ $it->id }}][weight_kg]"
                                           value="{{ old("items.$it->id.weight_kg", $it->weight_kg ?? '') }}">
                                </td>

                                <td style="display:none;">
                                    <input class="form-control form-control-sm ewcInput"
                                           name="items[{{ $it->id }}][ewc_code]"
                                           value="{{ $it->ewc_code ?? $it->category?->ewc_code }}">
                                </td>

                                @if($isCollect)
                                    <td class="text-center">
                                        <input type="checkbox" class="collected-checkbox"
                                               name="items[{{ $it->id }}][is_collected]"
                                               value="1"
                                               {{ old("items.$it->id.is_collected", $it->is_collected) ? 'checked' : '' }}>
                                    </td>

                                @else
                                    <td></td>
                                @endif
                                <td class="text-center">
                                    <input type="checkbox" class="erasure-checkbox erasureCheckbox"
                                        name="items[{{ $it->id }}][erasure_required]"
                                        value="1"
                                        {{ old("items.$it->id.erasure_required", $it->erasure_required) ? 'checked' : '' }}>
                                </td>
                                <td rowspan="2" class="item-delete-cell">
                                    <div class="d-flex flex-column align-items-center">
                                        <input type="number"
                                            class="form-control form-control-sm duplicate-count mb-2"
                                            value="1"
                                            min="1"
                                            max="100"
                                            style="width:70px; display:none;"
                                            title="Number of duplicates">

                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary duplicate-row mb-2"
                                                title="Duplicate row">
                                            <i class="fas fa-copy"></i>
                                        </button>

                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger delete-item"
                                                data-id="{{ $it->id }}"
                                                data-url="{{ route('collection-items.destroy',$it->id) }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr data-row="existing" data-id="{{ $it->id }}" class="item-row-bottomcomment {{ $bg }}">
                                <td colspan="2">
                                    <div class="row no-gutters">
                                        <div class="col-md-4 pr-2">
                                            <label class="small">Serial No</label>
                                            <input class="form-control form-control-sm"
                                                   name="items[{{ $it->id }}][serial_number]"
                                                   value="{{ old("items.$it->id.serial_number", $it->serial_number) }}">
                                        </div>
                                        <div class="col-md-4 pr-2">
                                            <label class="small">Asset Tag</label>
                                            <input class="form-control form-control-sm"
                                                   name="items[{{ $it->id }}][asset_tags]"
                                                   value="{{ old("items.$it->id.asset_tags", $it->asset_tags) }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="small">Our Asset Tracking #</label>
                                            <input class="form-control form-control-sm"
                                                   name="items[{{ $it->id }}][our_asset_number]"
                                                   value="{{ old("items.$it->id.our_asset_number", $it->our_asset_number) }}">
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <label class="small">Storage Serial</label>
                                    <input class="form-control form-control-sm"
                                           name="items[{{ $it->id }}][storage_serial_number]"
                                           value="{{ old("items.$it->id.storage_serial_number", $it->storage_serial_number) }}">
                                </td>

                                <td>
                                    <label class="small">Hard Disks</label><br>

                                    <button type="button"
                                            class="btn btn-sm btn-primary openHddModal"
                                            data-item-type="items"
                                            data-item-id="{{ $it->id }}">
                                        Add / View HDD
                                    </button>

                                    <span class="badge badge-info hdd-count ml-1">
                                        {{ $it->hdds->count() ?? 0 }}
                                    </span>

                                    <div class="hdd-hidden-holder"
                                        data-item-type="items"
                                        data-item-id="{{ $it->id }}">

                                        @foreach($it->hdds ?? [] as $hdd)
                                            <div class="hdd-data-row" data-hdd-key="{{ $hdd->id }}">
                                                <input type="hidden" name="items[{{ $it->id }}][hdds][{{ $hdd->id }}][serial]" value="{{ $hdd->serial }}">
                                                <input type="hidden" name="items[{{ $it->id }}][hdds][{{ $hdd->id }}][size]" value="{{ $hdd->size }}">
                                                <input type="hidden" name="items[{{ $it->id }}][hdds][{{ $hdd->id }}][status]" value="{{ $hdd->status ?? 'not_processed' }}">
                                                <input type="hidden" name="items[{{ $it->id }}][hdds][{{ $hdd->id }}][notes]" value="{{ $hdd->notes }}">
                                                <input type="hidden" name="items[{{ $it->id }}][hdds][{{ $hdd->id }}][delete]" value="0">
                                            </div>
                                        @endforeach

                                    </div>
                                </td>

                                <td style="display:none;">
                                    <label class="small">Component</label>
                                    <input class="form-control form-control-sm componentInput"
                                           name="items[{{ $it->id }}][component]"
                                           value="{{ $it->component ?? $it->category?->component }}">
                                </td>

                                <td style="display:none;">
                                    <label class="small">Concentration</label>
                                    <input class="form-control form-control-sm concentrationInput"
                                           name="items[{{ $it->id }}][concentration]"
                                           value="{{ $it->concentration ?? $it->category?->concentration }}">
                                </td>

                                <td colspan="2" style="display:none;">
                                    <div class="row no-gutters">
                                        <div class="col-md-6 pr-2">
                                            <label class="small">Form</label>
                                            <input class="form-control form-control-sm formInput"
                                                   name="items[{{ $it->id }}][physical_form]"
                                                   value="{{ $it->physical_form ?? $it->category?->physical_form }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small">Hazard</label>
                                            <input class="form-control form-control-sm hazardInput"
                                                   name="items[{{ $it->id }}][hazard_codes]"
                                                   value="{{ $it->hazard_codes ?? $it->category?->hazard_codes }}">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="item-gap">
                                <td colspan="200%"></td>
                            </tr>
                            
                        @endforeach
                        </tbody>
                    </table>
                </div>

                @if($isCollect)
                    <input type="hidden" name="mode_type" value="collect">

                    <div class="row mt-3">
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
<div class="modal fade" id="hddModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Hard Disks</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="currentItemId">
                <input type="hidden" id="currentItemType">
                <table class="table table-sm table-bordered" id="modalHddTable">
                    <thead class="thead-light">
                        <tr>
                            <th>Serial</th>
                            <th>Size</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th width="60"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <button type="button" class="btn btn-sm btn-primary" id="addModalHddRow">
                    Add HDD
                </button>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm" id="saveHddModalBtn">
                    Done
                </button>
            </div>

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
        var $erasrecheckbox = $row.find('.erasureCheckbox');
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
        $row.find('.erasureCheckbox').val(meta.is_erasure);
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

    $('#itemsTbody tr.item-row-top').each(function () {
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

    // duplcates start
    function getItemRows($anyRowInItem) {
        let $topRow = $anyRowInItem.hasClass('item-row-top') ? $anyRowInItem : $anyRowInItem.prev('.item-row-top');
        let $bottomRow = $topRow.next('.item-row-bottomcomment');
        return { $topRow, $bottomRow };
    }

    function getItemData($topRow, $bottomRow) {
        return {
            qty: $topRow.find('input[name*="[qty]"]').val() || 1,
            category_id: $topRow.find('.categorySel').val() || '',
            category_name: $topRow.find('.categoryNameInput').val() || '',

            manufacturer_id: $topRow.find('.manSel').val() || '',
            manufacturer_text: $topRow.find('.manText').val() || '',
            manufacturer_label: $topRow.find('.manSel option:selected').text() || '',

            product_model_id: $topRow.find('.modelSel').val() || '',
            model_text: $topRow.find('.modelText').val() || '',
            model_label: $topRow.find('.modelSel option:selected').text() || '',

            weight_kg: $topRow.find('.weightInput').val() || '',
            ewc_code: $topRow.find('.ewcInput').val() || '',
            is_erasure: $topRow.find('.erasureCheckbox').val() || '',
            serial_number: $bottomRow.find('input[name*="[serial_number]"]').val() || '',
            asset_tags: $bottomRow.find('input[name*="[asset_tags]"]').val() || '',
            storage_serial_number: $bottomRow.find('input[name*="[storage_serial_number]"]').val() || '',
            second_storage_serial_number: $bottomRow.find('input[name*="[second_storage_serial_number]"]').val() || '',
            our_asset_number: $bottomRow.find('input[name*="[our_asset_number]"]').val() || '',

            component: $bottomRow.find('.componentInput').val() || '',
            concentration: $bottomRow.find('.concentrationInput').val() || '',
            physical_form: $bottomRow.find('.formInput').val() || '',
            hazard_codes: $bottomRow.find('.hazardInput').val() || '',

            is_collected: $topRow.find('input[name*="[is_collected]"]').is(':checked'),
            erasure_required: $topRow.find('input[name*="[erasure_required]"]').is(':checked')
        };
    }

    function appendDuplicatedItemRow(data, $insertAfterRow = null) {
        var key = uid();

        var collectedTopCell = IS_COLLECT
            ? `<td class="text-center"><input class="collected-checkbox" type="checkbox" name="new_items[${key}][is_collected]" value="1" ${data.is_collected ? 'checked' : ''}></td>`
            : `<td></td>`;

        var erasureTopCell = `<td class="text-center">
            <input type="checkbox" name="new_items[${key}][erasure_required]" value="1" ${data.erasure_required ? 'checked' : ''}>
        </td>`;
        var html = `
            <tr data-row="new" class="item-row-top">
                <td rowspan="2" class="code-cell"><em>new</em></td>

                <td>
                    <input class="form-control form-control-sm"
                        name="new_items[${key}][qty]" value="${data.qty}">
                </td>

                <td>
                    <div class="d-flex">
                        <select class="form-control form-control-sm categorySel"
                                name="new_items[${key}][category_id]">
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}"
                                        data-name="{{ $c->name }}"
                                        data-ewc="{{ $c->ewc_code }}"
                                        data-is_erasure="{{ $c->is_erasure }}"
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

                        <input class="form-control form-control-sm ml-2 categoryNameInput"
                            name="new_items[${key}][category_name]" value="${data.category_name}">
                    </div>
                </td>

                <td>
                    <select class="form-control form-control-sm manSel"
                            name="new_items[${key}][manufacturer_id]"
                            style="width:100%"></select>
                    <input type="hidden" class="manText"
                        name="new_items[${key}][manufacturer_text]" value="${data.manufacturer_text}">
                </td>

                <td>
                    <select class="form-control form-control-sm modelSel"
                            name="new_items[${key}][product_model_id]"
                            style="width:100%"></select>
                    <input type="hidden" class="modelText"
                        name="new_items[${key}][model_text]" value="${data.model_text}">
                </td>

                <td style="display:none;">
                    <input class="form-control form-control-sm weightInput"
                        name="new_items[${key}][weight_kg]" value="${data.weight_kg}">
                </td>

                <td style="display:none;">
                    <input class="form-control form-control-sm ewcInput"
                        name="new_items[${key}][ewc_code]" value="${data.ewc_code}">
                </td>
                
                ${collectedTopCell}
                ${erasureTopCell}
                <td rowspan="2" class="item-delete-cell">
                    <div class="d-flex flex-column align-items-center">
                        <input type="number"
                            class="form-control form-control-sm duplicate-count mb-2"
                            value="1"
                            min="1"
                            max="100"
                            style="width:70px; display:none;"
                            title="Number of duplicates">

                        <button type="button"
                                class="btn btn-sm btn-outline-secondary duplicate-row mb-2"
                                title="Duplicate row">
                            <i class="fas fa-copy"></i>
                        </button>

                        <button type="button" class="btn btn-sm btn-outline-danger removeNew">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </td>
            </tr>

            <tr data-row="new" class="item-row-bottomcomment">
                <td colspan="2">
                    <div class="row no-gutters">
                        <div class="col-md-4 pr-2">
                            <label class="small">Serial No</label>
                            <input class="form-control form-control-sm"
                                name="new_items[${key}][serial_number]" value="${data.serial_number}">
                        </div>
                        <div class="col-md-4 pr-2">
                            <label class="small">Asset Tag</label>
                            <input class="form-control form-control-sm"
                                name="new_items[${key}][asset_tags]" value="${data.asset_tags}">
                        </div>
                        <div class="col-md-4">
                            <label class="small">Our Asset Tracking #</label>
                            <input class="form-control form-control-sm"
                                name="new_items[${key}][our_asset_number]" value="${data.our_asset_number}">
                        </div>
                    </div>
                </td>

                <td>
                    <label class="small">Storage Serial</label>
                    <input class="form-control form-control-sm"
                        name="new_items[${key}][storage_serial_number]" value="${data.storage_serial_number}">
                </td>

                <td>
                    <label class="small">Hard Disks</label><br>

                    <button type="button"
                            class="btn btn-sm btn-primary openHddModal"
                            data-item-type="new_items"
                            data-item-id="${key}">
                        Add / View HDD
                    </button>

                    <span class="badge badge-info hdd-count ml-1">0</span>

                    <div class="hdd-hidden-holder"
                        data-item-type="new_items"
                        data-item-id="${key}">
                    </div>
                </td>

                <td style="display:none;">
                    <label class="small">Component</label>
                    <input class="form-control form-control-sm componentInput"
                        name="new_items[${key}][component]" value="${data.component}">
                </td>

                <td style="display:none;">
                    <label class="small">Concentration</label>
                    <input class="form-control form-control-sm concentrationInput"
                        name="new_items[${key}][concentration]" value="${data.concentration}">
                </td>

                <td colspan="2" style="display:none;">
                    <div class="row no-gutters">
                        <div class="col-md-6 pr-2">
                            <label class="small">Form</label>
                            <input class="form-control form-control-sm formInput"
                                name="new_items[${key}][physical_form]" value="${data.physical_form}">
                        </div>
                        <div class="col-md-6">
                            <label class="small">Hazard</label>
                            <input class="form-control form-control-sm hazardInput"
                                name="new_items[${key}][hazard_codes]" value="${data.hazard_codes}">
                        </div>
                    </div>
                </td>
            </tr>
        `;

        if ($insertAfterRow && $insertAfterRow.length) {
            $insertAfterRow.after(html);
        } else {
            $('#itemsTbody').append(html);
        }

        let $topRow = ($insertAfterRow && $insertAfterRow.length)
            ? $insertAfterRow.nextAll('tr.item-row-top').first()
            : $('#itemsTbody tr.item-row-top').last();

        $topRow.find('.categorySel').val(data.category_id);

        initRow($topRow);

        if (data.manufacturer_id) {
            var manOption = new Option(data.manufacturer_label || data.manufacturer_text || data.manufacturer_id, data.manufacturer_id, true, true);
            $topRow.find('.manSel').append(manOption).trigger('change.select2');
        }
        if (data.manufacturer_text && !isNumeric(data.manufacturer_id)) {
            $topRow.find('.manText').val(data.manufacturer_text);
        }

        if (data.product_model_id) {
            var modelOption = new Option(data.model_label || data.model_text || data.product_model_id, data.product_model_id, true, true);
            $topRow.find('.modelSel').append(modelOption).trigger('change.select2');
        }
        if (data.model_text && !isNumeric(data.product_model_id)) {
            $topRow.find('.modelText').val(data.model_text);
        }

        $topRow.find('.weightInput').val(data.weight_kg);
        $topRow.find('.ewcInput').val(data.ewc_code);

        return $topRow.next('.item-row-bottomcomment'); // return last inserted bottom row
    }
    // duplcates end
    $(document).on('click', '.duplicate-row', function () {
        let $clickedRow = $(this).closest('tr');
        let rows = getItemRows($clickedRow);
        let data = getItemData(rows.$topRow, rows.$bottomRow);

        let count = parseInt(data.qty || 1, 10);
        count = Math.max(1, Math.min(100, count));

        if (count <= 1) return;

        // Set original qty to 1
        rows.$topRow.find('input[name*="[qty]"]').val(1);

        // New rows should also have qty = 1
        data.qty = 1;

        // Start inserting after this item's bottom row
        let $insertAfter = rows.$bottomRow;

        for (let i = 1; i < count; i++) {
            $insertAfter = appendDuplicatedItemRow(data, $insertAfter);
        }
    });
    $('#bulk_add_btn').on('click', function () {
        var qty = parseInt($('#bulk_qty').val() || 1, 10);
        qty = Math.max(1, Math.min(500, qty));
        
        var bulkCatId = $bulkCat.val();
        var is_erasure = $bulkCat.find('option:selected').data('is_erasure');
        var manVal  = $bulkMan.val();
        var manText = $bulkMan.find('option:selected').text();
        var modelVal  = $bulkModel.val();
        var modelText = $bulkModel.find('option:selected').text();

        for (var i = 0; i < qty; i++) {
            var key = uid();

            var collectedTopCell = IS_COLLECT
                ? `<td class="text-center"><input class="collected-checkbox" type="checkbox" name="new_items[${key}][is_collected]" value="1"></td>`
                : `<td></td>`;
            var erasureTopCell = `<td class="text-center">
                <input class="erasure-checkbox erasureCheckbox" type="checkbox" name="new_items[${key}][erasure_required]" value="1" ${is_erasure == 1 ? 'checked' : ''}>
            </td>`;

            var html = `
                <tr data-row="new" class="item-row-top">
                    <td rowspan="2" class="code-cell"><em>new</em></td>

                    <td>
                        <input class="form-control form-control-sm"
                               name="new_items[${key}][qty]" value="1">
                    </td>

                    <td>
                        <div class="d-flex">
                            <select class="form-control form-control-sm categorySel"
                                    name="new_items[${key}][category_id]">
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}"
                                            data-name="{{ $c->name }}"
                                            data-ewc="{{ $c->ewc_code }}"
                                            data-is_erasure="{{ $c->is_erasure }}"
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

                            <input class="form-control form-control-sm ml-2 categoryNameInput"
                                   name="new_items[${key}][category_name]" value="">
                        </div>
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

                    <td style="display:none;">
                        <input class="form-control form-control-sm weightInput"
                               name="new_items[${key}][weight_kg]" value="">
                    </td>

                    <td style="display:none;">
                        <input class="form-control form-control-sm ewcInput"
                               name="new_items[${key}][ewc_code]" value="">
                    </td>

                    ${collectedTopCell}
                    ${erasureTopCell}
                    <td rowspan="2" class="item-delete-cell">
                        <div class="d-flex flex-column align-items-center">
                            <input type="number"
                                class="form-control form-control-sm duplicate-count mb-2"
                                value="1"
                                min="1"
                                max="100"
                                style="width:70px;display:none;"
                                title="Number of duplicates">

                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary duplicate-row mb-2"
                                    title="Duplicate row">
                                <i class="fas fa-copy"></i>
                            </button>

                            <button type="button" class="btn btn-sm btn-outline-danger removeNew">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <tr data-row="new" class="item-row-bottomcomment">
                    <td colspan="2">
                        <div class="row no-gutters">
                            <div class="col-md-4 pr-2">
                                <label class="small">Serial No</label>
                                <input class="form-control form-control-sm"
                                       name="new_items[${key}][serial_number]" value="">
                            </div>
                            <div class="col-md-4 pr-2">
                                <label class="small">Asset Tag</label>
                                <input class="form-control form-control-sm"
                                       name="new_items[${key}][asset_tags]" value="">
                            </div>
                            <div class="col-md-4">
                                <label class="small">Our Asset Tracking #</label>
                                <input class="form-control form-control-sm"
                                       name="new_items[${key}][our_asset_number]" value="">
                            </div>
                        </div>
                    </td>

                    <td>
                        <label class="small">Storage Serial</label>
                        <input class="form-control form-control-sm"
                               name="new_items[${key}][storage_serial_number]" value="">
                    </td>

                    <td>
                        <label class="small">Hard Disks</label><br>

                        <button type="button"
                                class="btn btn-sm btn-primary openHddModal"
                                data-item-type="new_items"
                                data-item-id="${key}">
                            Add / View HDD
                        </button>

                        <span class="badge badge-info hdd-count ml-1">0</span>

                        <div class="hdd-hidden-holder"
                            data-item-type="new_items"
                            data-item-id="${key}">
                        </div>
                    </td>

                    <td style="display:none;">
                        <label class="small">Component</label>
                        <input class="form-control form-control-sm componentInput"
                               name="new_items[${key}][component]" value="">
                    </td>

                    <td style="display:none;">
                        <label class="small">Concentration</label>
                        <input class="form-control form-control-sm concentrationInput"
                               name="new_items[${key}][concentration]" value="">
                    </td>

                    <td colspan="2" style="display:none;">
                        <div class="row no-gutters">
                            <div class="col-md-6 pr-2">
                                <label class="small">Form</label>
                                <input class="form-control form-control-sm formInput"
                                       name="new_items[${key}][physical_form]" value="">
                            </div>
                            <div class="col-md-6">
                                <label class="small">Hazard</label>
                                <input class="form-control form-control-sm hazardInput"
                                       name="new_items[${key}][hazard_codes]" value="">
                            </div>
                        </div>
                    </td>
                </tr>
            `;

            $('#itemsTbody').append(html);

            var $topRow = $('#itemsTbody tr.item-row-top').last();
            $topRow.find('.categorySel').val(bulkCatId);

            initRow($topRow);

            if (manVal && isNumeric(manVal)) {
                var op = new Option(manText, manVal, true, true);
                $topRow.find('.manSel').append(op).trigger('change.select2');
            } else if (manVal && !isNumeric(manVal)) {
                var opt = new Option(manText, manVal, true, true);
                $topRow.find('.manSel').append(opt).trigger('change.select2');
                $topRow.find('.manText').val(manText);
            }

            if (modelVal && isNumeric(modelVal)) {
                var opm = new Option(modelText, modelVal, true, true);
                $topRow.find('.modelSel').append(opm).trigger('change.select2');
            } else if (modelVal && !isNumeric(modelVal)) {
                var optm = new Option(modelText, modelVal, true, true);
                $topRow.find('.modelSel').append(optm).trigger('change.select2');
                $topRow.find('.modelText').val(modelText);
            }

            fillEmptyFieldsFromCategory($topRow);
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

    let $topRow = $(this).closest('tr');
    let $bottomRow = $topRow.next('tr');
    let url = $(this).data('url');

    $.ajax({
        url:url,
        type:'DELETE',
        data:{
            _token:'{{ csrf_token() }}'
        },
        success:function(){
            $bottomRow.remove();
            $topRow.remove();
        }
    });
});

$(document).on('click','.removeNew',function(){
    let $topRow = $(this).closest('tr');
    let $bottomRow = $topRow.next('tr');

    $bottomRow.remove();
    $topRow.remove();
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
$(document).on('change', '#checkAllCollected', function () {
    $('.collected-checkbox').prop('checked', $(this).is(':checked'));
});
$(document).on('change', '#checkAllErasure', function () {
    $('.erasure-checkbox').prop('checked', $(this).is(':checked'));
});




//
$(function () {

    function uid() {
        return 'n' + Math.random().toString(16).slice(2);
    }

    let currentItemType = null;
    let currentItemId = null;

    $(document).on('click', '.openHddModal', function () {
        currentItemType = $(this).data('item-type');
        currentItemId = $(this).data('item-id');

        $('#modalHddTable tbody').html('');

        let holder = getHolder();

        holder.find('.hdd-data-row').each(function () {
            let key = $(this).data('hdd-key');

            let serial = $(this).find('input[name$="[serial]"]').val() || '';
            let size = $(this).find('input[name$="[size]"]').val() || '';
            let status = $(this).find('input[name$="[status]"]').val() || 'not_processed';
            let notes = $(this).find('input[name$="[notes]"]').val() || '';
            let del = $(this).find('input[name$="[delete]"]').val() || '0';

            if (del !== '1') {
                addModalRow(key, serial, size, status, notes);
            }
        });

        $('#hddModal').modal('show');
    });

    $('#addModalHddRow').on('click', function () {
        addModalRow(uid(), '', '', 'not_processed', '');
    });

    $(document).on('click', '.removeModalHdd', function () {
        $(this).closest('tr').remove();
    });

    $('#saveHddModalBtn').on('click', function () {
        saveHddsToHiddenInputs();
        $('#hddModal').modal('hide');
    });

    function getHolder() {
        return $('.hdd-hidden-holder[data-item-type="' + currentItemType + '"][data-item-id="' + currentItemId + '"]');
    }

    function addModalRow(key, serial, size, status, notes) {
        let row = `
            <tr data-hdd-key="${key}">
                <td>
                    <input class="form-control form-control-sm hdd-serial" value="${escapeHtml(serial)}">
                </td>

                <td>
                    <input class="form-control form-control-sm hdd-size" value="${escapeHtml(size)}" placeholder="500GB / 1TB">
                </td>

                <td>
                    <select class="form-control form-control-sm hdd-status">
                        <option value="not_processed" ${status === 'not_processed' ? 'selected' : ''}>Not Processed</option>
                        <option value="erased" ${status === 'erased' ? 'selected' : ''}>Erased</option>
                        <option value="failed" ${status === 'failed' ? 'selected' : ''}>Failed</option>
                        <option value="shredding" ${status === 'shredding' ? 'selected' : ''}>Shredding</option>
                    </select>
                </td>

                <td>
                    <input class="form-control form-control-sm hdd-notes" value="${escapeHtml(notes)}">
                </td>

                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger removeModalHdd">&times;</button>
                </td>
            </tr>
        `;

        $('#modalHddTable tbody').append(row);
    }

    function saveHddsToHiddenInputs() {
        let holder = getHolder();
        holder.html('');

        let count = 0;

        $('#modalHddTable tbody tr').each(function () {
            let key = $(this).data('hdd-key') || uid();

            let serial = $(this).find('.hdd-serial').val() || '';
            let size = $(this).find('.hdd-size').val() || '';
            let status = $(this).find('.hdd-status').val() || 'not_processed';
            let notes = $(this).find('.hdd-notes').val() || '';

            if (!serial && !size && !notes) {
                return;
            }

            count++;

            holder.append(`
                <div class="hdd-data-row" data-hdd-key="${key}">
                    <input type="hidden" name="${currentItemType}[${currentItemId}][hdds][${key}][serial]" value="${escapeAttr(serial)}">
                    <input type="hidden" name="${currentItemType}[${currentItemId}][hdds][${key}][size]" value="${escapeAttr(size)}">
                    <input type="hidden" name="${currentItemType}[${currentItemId}][hdds][${key}][status]" value="${escapeAttr(status)}">
                    <input type="hidden" name="${currentItemType}[${currentItemId}][hdds][${key}][notes]" value="${escapeAttr(notes)}">
                    <input type="hidden" name="${currentItemType}[${currentItemId}][hdds][${key}][delete]" value="0">
                </div>
            `);
        });

        holder.closest('td').find('.hdd-count').text(count);
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function escapeAttr(value) {
        return escapeHtml(value);
    }

});

$(function () {
    const $form = $('#itemsForm');
    const STORAGE_KEY = 'offline_collection_updates';

    // 1. Intercept form submission
    $form.on('submit', function (e) {
        // If online, let the form submit normally
        if (navigator.onLine) {
            return true; 
        }

        e.preventDefault(); // Stop standard form redirect

        // Determine which submit button was clicked (save vs send_pdf)
        const modeValue = document.activeElement ? document.activeElement.value : 'save';

        // Serialize all form data into a lookup object
        const formData = {};
        $form.serializeArray().forEach(item => {
            if (formData[item.name]) {
                if (!Array.isArray(formData[item.name])) {
                    formData[item.name] = [formData[item.name]];
                }
                formData[item.name].push(item.value);
            } else {
                formData[item.name] = item.value;
            }
        });
        
        // Add explicit action details
        formData['_action_mode'] = modeValue;
        formData['_collection_id'] = "{{ $collection->id }}";
        formData['_timestamp'] = new Date().getTime();

        // 2. Save payload to LocalStorage queue
        let queue = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
        
        // Remove prior offline items matching this specific collection to avoid stale updates
        queue = queue.filter(item => item._collection_id !== formData._collection_id);
        queue.push(formData);
        
        localStorage.setItem(STORAGE_KEY, JSON.stringify(queue));

        alert('⚠️ You are offline! Data has been securely cached on your device. It will automatically upload once your internet connection is restored.');
        
        // Optionally redirect them back to the show page safely
        window.location.href = "{{ route('collections.show', $collection) }}";
    });

    // 3. Automated Background Sync Routine when back online
    async function syncOfflineData() {
        if (!navigator.onLine) return;

        let queue = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
        if (queue.length === 0) return;

        console.log(`Found ${queue.length} offline forms to sync...`);

        for (let i = 0; i < queue.length; i++) {
            const payload = queue[i];
            
            try {
                const response = await fetch($form.attr('action'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': payload._token
                    },
                    body: new URLSearchParams(payload).toString()
                });

                if (response.ok || response.status === 302) {
                    // Remove successfully synchronized element
                    queue.splice(i, 1);
                    i--; 
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(queue));
                }
            } catch (error) {
                console.error('Failed to sync item, retrying later:', error);
                break; // Stop loop if the server is still unreachable
            }
        }
        
        if (queue.length === 0) {
            console.log('All offline data synchronized successfully!');
        }
    }

    // Monitor connectivity status shifts
    window.addEventListener('online', syncOfflineData);
    
    // Attempt an initial run on page load just in case they just recovered connectivity
    syncOfflineData();

    // Register Service Worker for offline page loading
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => console.log('Service Worker registered successfully!'))
            .catch(err => console.log('Service Worker registration failed:', err));
    });
}


});

</script>
@endpush