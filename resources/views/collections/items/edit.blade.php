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

            {{-- QUICK ADD (qty) --}}
            <form method="POST" action="{{ route('collections.items.bulkStore',$collection) }}" class="mb-3">
                @csrf
                <div class="form-row align-items-end">
                    <div class="col-md-2">
                        <label>Qty</label>
                        <input class="form-control form-control-sm" type="number" name="qty" value="1" min="1">
                    </div>

                    <div class="col-md-3">
                        <label>Category</label>
                        <select class="form-control form-control-sm" name="category_id" id="bulk_category" required>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Manufacturer</label>
                        <select class="form-control form-control-sm" name="manufacturer_id" id="bulk_manufacturer"></select>
                    </div>

                    <div class="col-md-3">
                        <label>Model</label>
                        <select class="form-control form-control-sm" name="product_model_id" id="bulk_model"></select>
                    </div>

                    <div class="col-md-1">
                        <button class="btn btn-sm btn-outline-secondary btn-block" title="Add Qty items">
                            <i class="fas fa-clone"></i>
                        </button>
                    </div>
                </div>
            </form>

            {{-- GRID SAVE --}}
            <form method="POST" action="{{ route('collections.items.update',$collection) }}">
                @csrf
                @method('PUT')

                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th style="width:60px;">Qty</th>
                        <th>Category</th>
                        <th>Manufacturer</th>
                        <th>Model</th>
                        <th>Serial</th>
                        <th>Asset Tag(s)</th>
                        <th>Dimensions</th>
                        <th style="width:90px;">Weight</th>
                        <th style="width:70px;">Erase</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($collection->items->sortBy('item_number') as $it)
                        <tr>
                            <td>{{ $it->item_number }}</td>
                            <td><input class="form-control form-control-sm" name="items[{{ $it->id }}][qty]" value="{{ $it->qty }}"></td>

                            <td>
                                <select class="form-control form-control-sm" name="items[{{ $it->id }}][category_id]">
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}" {{ $it->category_id==$c->id?'selected':'' }}>
                                            {{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td><input class="form-control form-control-sm" name="items[{{ $it->id }}][manufacturer_text]" value="{{ $it->manufacturer?->name ?? $it->manufacturer_text }}"></td>
                            <td><input class="form-control form-control-sm" name="items[{{ $it->id }}][model_text]" value="{{ $it->productModel?->name ?? $it->model_text }}"></td>

                            <td><input class="form-control form-control-sm" name="items[{{ $it->id }}][serial_number]" value="{{ $it->serial_number }}"></td>
                            <td><input class="form-control form-control-sm" name="items[{{ $it->id }}][asset_tags]" value="{{ $it->asset_tags }}"></td>
                            <td><input class="form-control form-control-sm" name="items[{{ $it->id }}][dimensions]" value="{{ $it->dimensions }}"></td>
                            <td><input class="form-control form-control-sm" name="items[{{ $it->id }}][weight_kg]" value="{{ $it->weight_kg }}"></td>

                            <td class="text-center">
                                <input type="checkbox" name="items[{{ $it->id }}][erasure_required]" value="1" {{ $it->erasure_required?'checked':'' }}>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <button class="btn btn-primary btn-sm">Save</button>
                <a class="btn btn-link btn-sm" href="{{ route('collections.show',$collection) }}">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
(function () {
    const $cat = document.getElementById('bulk_category');
    const $man = document.getElementById('bulk_manufacturer');
    const $mod = document.getElementById('bulk_model');

    function fillSelect(sel, options, placeholder='--') {
        sel.innerHTML = '';
        const opt0 = document.createElement('option');
        opt0.value = '';
        opt0.textContent = placeholder;
        sel.appendChild(opt0);

        (options || []).forEach(o => {
            const op = document.createElement('option');
            op.value = o.id;
            op.textContent = o.text;
            sel.appendChild(op);
        });
    }

    async function loadManufacturersByCategory(categoryId) {
        if (!categoryId) {
            fillSelect($man, [], '-- Manufacturer --');
            fillSelect($mod, [], '-- Model --');
            return;
        }
        const url = "{{ route('ajax.categories.manufacturers', ':id') }}".replace(':id', categoryId);
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        const mans = await res.json();
        fillSelect($man, mans, '-- Manufacturer --');
        fillSelect($mod, [], '-- Model --');
    }

    async function loadModels(manufacturerId) {
        const categoryId = $cat.value;

        if (!manufacturerId) {
            fillSelect($mod, [], '-- Model --');
            return;
        }

        let url = "{{ route('ajax.manufacturers.models', ':id') }}".replace(':id', manufacturerId);

        // pass category_id so models are filtered to selected category
        if (categoryId) {
            url += '?category_id=' + encodeURIComponent(categoryId);
        }

        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        const models = await res.json();
        fillSelect($mod, models, '-- Model --');
    }

    // when category changes -> reload manufacturers
    $cat.addEventListener('change', function () {
        loadManufacturersByCategory(this.value);
    });

    // when manufacturer changes -> reload models
    $man.addEventListener('change', function () {
        loadModels(this.value);
    });

    // initial load (when page opens)
    loadManufacturersByCategory($cat.value);
})();
</script>
@endpush
