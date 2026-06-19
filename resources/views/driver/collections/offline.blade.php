@extends('adminlte::page')

@section('title', 'Driver Offline Collection')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4>Driver Offline Collection</h4>
            <strong>{{ $collection->collection_number }}</strong>
        </div>

        <span id="onlineStatus" class="badge badge-secondary">Checking...</span>
    </div>

    <div class="alert alert-info">
        Before driver leaves office, click <strong>Download Offline Data</strong>.
        Then the collection can be completed without internet.
    </div>

    <div class="mb-3">
        <button type="button" id="downloadBtn" class="btn btn-primary">Download Offline Data</button>
        <button type="button" id="syncBtn" class="btn btn-success">Sync Now</button>
        <button type="button" id="addItemBtn" class="btn btn-outline-primary">Add New Item</button>
        <button type="button" id="saveOfflineBtn" class="btn btn-primary mt-4">Save Offline</button>
    </div>

    <div id="syncMessage"></div>

    <form id="offlineCollectionForm">
        <div id="itemsArea"></div>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <label>Client Signature</label>
                <input type="hidden" id="client_signature">

                <div class="border bg-white p-2">
                    <canvas id="clientCanvas" height="160" style="width:100%;"></canvas>
                </div>

                <button type="button" class="btn btn-sm btn-outline-secondary mt-2"
                        onclick="clearCanvas('clientCanvas', 'client_signature')">
                    Clear
                </button>

                <label class="mt-2">Client Print Name</label>
                <input class="form-control" id="client_print_name">
            </div>

            <div class="col-md-6">
                <label>Driver Signature</label>
                <input type="hidden" id="driver_signature">

                <div class="border bg-white p-2">
                    <canvas id="driverCanvas" height="160" style="width:100%;"></canvas>
                </div>

                <button type="button" class="btn btn-sm btn-outline-secondary mt-2"
                        onclick="clearCanvas('driverCanvas', 'driver_signature')">
                    Clear
                </button>

                <label class="mt-2">Driver Print Name</label>
                <input class="form-control" id="driver_print_name">
            </div>
        </div>

        <button type="button" class="btn btn-primary mt-4" onclick="saveOfflineData()">
            Save Offline
        </button>
    </form>
</div>
@endsection

@section('js')
<script>
const COLLECTION_ID = "{{ $collection->id }}";
const DATA_URL = "{{ route('driver.collections.offline.data', $collection) }}";
const MASTER_DATA_URL = "{{ route('driver.offline.master-data') }}";
const SYNC_URL = "{{ route('driver.collections.sync', $collection) }}";
const CSRF_TOKEN = "{{ csrf_token() }}";

const STORAGE_KEY = "offline_collection_" + COLLECTION_ID;
const MASTER_KEY = "offline_master_data";

let collectionData = null;
let masterData = {
    categories: [],
    manufacturers: [],
    models: []
};

function setMessage(message, type = 'info') {
    document.getElementById('syncMessage').innerHTML =
        `<div class="alert alert-${type}">${message}</div>`;
}

function updateOnlineStatus() {
    const badge = document.getElementById('onlineStatus');

    if (navigator.onLine) {
        badge.className = 'badge badge-success';
        badge.innerText = 'Online';
    } else {
        badge.className = 'badge badge-danger';
        badge.innerText = 'Offline';
    }
}

window.addEventListener('online', updateOnlineStatus);
window.addEventListener('offline', updateOnlineStatus);

async function downloadOfflineData() {
    try {
        const masterRes = await fetch(MASTER_DATA_URL);
        masterData = await masterRes.json();
        localStorage.setItem(MASTER_KEY, JSON.stringify(masterData));

        const collectionRes = await fetch(DATA_URL);
        collectionData = await collectionRes.json();
        localStorage.setItem(STORAGE_KEY, JSON.stringify(collectionData));

        renderCollection(collectionData);
        loadSignatureValues(collectionData);

        setMessage('Offline data downloaded successfully.', 'success');
    } catch (e) {
        setMessage('Download failed. Trying saved offline data.', 'warning');
        loadOfflineData();
    }
}

function loadOfflineData() {
    const savedMaster = localStorage.getItem(MASTER_KEY);
    const savedCollection = localStorage.getItem(STORAGE_KEY);

    if (savedMaster) {
        masterData = JSON.parse(savedMaster);
    }

    if (savedCollection) {
        collectionData = JSON.parse(savedCollection);
        renderCollection(collectionData);
        loadSignatureValues(collectionData);
        setMessage('Loaded saved offline data.', 'success');
    } else {
        setMessage('No offline data found. Download first while internet is available.', 'danger');
    }
}

function safe(value) {
    return value === null || value === undefined ? '' : String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;');
}

function categoryOptions(selectedId = '') {
    return masterData.categories.map(c => `
        <option value="${c.id}" ${String(c.id) === String(selectedId) ? 'selected' : ''}>
            ${safe(c.name)} / ${safe(c.type || '')}
        </option>
    `).join('');
}

function manufacturerOptions(selectedId = '') {
    let html = `<option value="">-- Manufacturer --</option>`;

    masterData.manufacturers.forEach(m => {
        html += `
            <option value="${m.id}" ${String(m.id) === String(selectedId) ? 'selected' : ''}>
                ${safe(m.name)}
            </option>
        `;
    });

    html += `<option value="manual">+ Add Manual Manufacturer</option>`;

    return html;
}

function modelOptions(manufacturerId = '', selectedId = '') {
    let html = `<option value="">-- Model --</option>`;

    masterData.models
        .filter(m => !manufacturerId || String(m.manufacturer_id) === String(manufacturerId))
        .forEach(m => {
            html += `
                <option value="${m.id}" ${String(m.id) === String(selectedId) ? 'selected' : ''}>
                    ${safe(m.name)}
                </option>
            `;
        });

    html += `<option value="manual">+ Add Manual Model</option>`;

    return html;
}

function renderCollection(data) {
    let html = '';

    data.items.forEach((item, index) => {
        html += `
            <div class="card mb-3 item-card existing-item-card" data-index="${index}">
                <div class="card-header d-flex justify-content-between">
                    <strong>${safe(item.item_code || 'Item')} - ${safe(item.category_name || '')}</strong>

                    <label>
                        <input type="checkbox" class="item-collected" ${item.is_collected ? 'checked' : ''}>
                        Collected
                    </label>
                </div>

                <div class="card-body">
                    <input type="hidden" class="item-id" value="${safe(item.id)}">

                    <div class="row">
                        <div class="col-md-2">
                            <label>Qty</label>
                            <input class="form-control item-qty" value="${safe(item.qty)}" readonly>
                        </div>

                        <div class="col-md-3">
                            <label>Category</label>
                            <input class="form-control" value="${safe(item.category_name)}" readonly>
                        </div>

                        <div class="col-md-3">
                            <label>Manufacturer</label>
                            <input class="form-control" value="${safe(item.manufacturer)}" readonly>
                        </div>

                        <div class="col-md-4">
                            <label>Model</label>
                            <input class="form-control" value="${safe(item.model)}" readonly>
                        </div>

                        <div class="col-md-3 mt-2">
                            <label>Serial No</label>
                            <input class="form-control item-serial" value="${safe(item.serial_number)}">
                        </div>

                        <div class="col-md-3 mt-2">
                            <label>Asset Tag</label>
                            <input class="form-control item-asset" value="${safe(item.asset_tags)}">
                        </div>

                        <div class="col-md-3 mt-2">
                            <label>Our Asset Tracking #</label>
                            <input class="form-control item-our-asset" value="${safe(item.our_asset_number)}">
                        </div>

                        <div class="col-md-3 mt-2">
                            <label>Storage Serial</label>
                            <input class="form-control item-storage" value="${safe(item.storage_serial_number)}">
                        </div>
                    </div>

                    <hr>

                    <h6>Hard Disks</h6>
                    <div class="hdd-area">
                        ${(item.hdds || []).map(hdd => hddRowHtml(hdd)).join('')}
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addHddToCard(this)">
                        Add HDD
                    </button>
                </div>
            </div>
        `;
    });

    document.getElementById('itemsArea').innerHTML = html;
}

function addNewItem() {
    if (!collectionData) {
        setMessage('Download offline data first.', 'warning');
        return;
    }

    const key = 'new_' + Date.now();

    const html = `
        <div class="card mb-3 item-card new-item-card" data-new-key="${key}">
            <div class="card-header d-flex justify-content-between">
                <strong>New Item</strong>

                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.item-card').remove()">
                    Remove
                </button>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <label>Qty</label>
                        <input class="form-control item-qty" value="1" type="number" min="1">
                    </div>

                    <div class="col-md-4">
                        <label>Category</label>
                        <select class="form-control item-category" onchange="fillCategoryData(this)">
                            ${categoryOptions()}
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Manufacturer</label>
                        <select class="form-control item-manufacturer" onchange="manufacturerChanged(this)">
                            ${manufacturerOptions()}
                        </select>

                        <input class="form-control mt-2 item-manufacturer-text d-none" placeholder="Manual Manufacturer">
                    </div>

                    <div class="col-md-3">
                        <label>Model</label>
                        <select class="form-control item-model" onchange="modelChanged(this)">
                            ${modelOptions()}
                        </select>

                        <input class="form-control mt-2 item-model-text d-none" placeholder="Manual Model">
                    </div>

                    <div class="col-md-3 mt-2">
                        <label>Serial No</label>
                        <input class="form-control item-serial">
                    </div>

                    <div class="col-md-3 mt-2">
                        <label>Asset Tag</label>
                        <input class="form-control item-asset">
                    </div>

                    <div class="col-md-3 mt-2">
                        <label>Our Asset Tracking #</label>
                        <input class="form-control item-our-asset">
                    </div>

                    <div class="col-md-3 mt-2">
                        <label>Storage Serial</label>
                        <input class="form-control item-storage">
                    </div>

                    <div class="col-md-3 mt-3">
                        <label>
                            <input type="checkbox" class="item-collected" checked>
                            Collected
                        </label>
                    </div>

                    <div class="col-md-3 mt-3">
                        <label>
                            <input type="checkbox" class="item-erasure">
                            Erasure Required
                        </label>
                    </div>
                </div>

                <input type="hidden" class="item-category-name">
                <input type="hidden" class="item-ewc-code">
                <input type="hidden" class="item-weight">
                <input type="hidden" class="item-component">
                <input type="hidden" class="item-concentration">
                <input type="hidden" class="item-form">
                <input type="hidden" class="item-hazard">

                <hr>

                <h6>Hard Disks</h6>
                <div class="hdd-area"></div>

                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addHddToCard(this)">
                    Add HDD
                </button>
            </div>
        </div>
    `;

    document.getElementById('itemsArea').insertAdjacentHTML('afterbegin', html);
setMessage('New item added. Fill details then click Save Offline.', 'success');
window.scrollTo({ top: 0, behavior: 'smooth' });

    const card = document.querySelector(`[data-new-key="${key}"]`);
    fillCategoryData(card.querySelector('.item-category'));
}

function fillCategoryData(select) {
    const card = select.closest('.item-card');
    const category = masterData.categories.find(c => String(c.id) === String(select.value));

    if (!category) return;

    card.querySelector('.item-category-name').value = category.name || '';
    card.querySelector('.item-ewc-code').value = category.ewc_code || '';
    card.querySelector('.item-weight').value = category.default_weight_kg || 0;
    card.querySelector('.item-component').value = category.component || '';
    card.querySelector('.item-concentration').value = category.concentration || '';
    card.querySelector('.item-form').value = category.physical_form || '';
    card.querySelector('.item-hazard').value = category.hazard_codes || '';

    const erasure = card.querySelector('.item-erasure');
    if (erasure) {
        erasure.checked = Number(category.is_erasure) === 1;
    }
}

function manufacturerChanged(select) {
    const card = select.closest('.item-card');
    const manual = card.querySelector('.item-manufacturer-text');
    const modelSelect = card.querySelector('.item-model');

    if (select.value === 'manual') {
        manual.classList.remove('d-none');
        modelSelect.innerHTML = modelOptions('');
    } else {
        manual.classList.add('d-none');
        manual.value = '';
        modelSelect.innerHTML = modelOptions(select.value);
    }
}

function modelChanged(select) {
    const card = select.closest('.item-card');
    const manual = card.querySelector('.item-model-text');

    if (select.value === 'manual') {
        manual.classList.remove('d-none');
    } else {
        manual.classList.add('d-none');
        manual.value = '';
    }
}

function hddRowHtml(hdd = {}) {
    return `
        <div class="row hdd-row mb-2" data-hdd-id="${safe(hdd.id || '')}">
            <div class="col-md-3">
                <input class="form-control hdd-serial" placeholder="HDD Serial" value="${safe(hdd.serial)}">
            </div>

            <div class="col-md-2">
                <input class="form-control hdd-size" placeholder="500GB / 1TB" value="${safe(hdd.size)}">
            </div>

            <div class="col-md-3">
                <select class="form-control hdd-status">
                    <option value="not_processed" ${hdd.status === 'not_processed' ? 'selected' : ''}>Not Processed</option>
                    <option value="erased" ${hdd.status === 'erased' ? 'selected' : ''}>Erased</option>
                    <option value="failed" ${hdd.status === 'failed' ? 'selected' : ''}>Failed</option>
                    <option value="shredding" ${hdd.status === 'shredding' ? 'selected' : ''}>Shredding</option>
                </select>
            </div>

            <div class="col-md-3">
                <input class="form-control hdd-notes" placeholder="Notes" value="${safe(hdd.notes)}">
            </div>

            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.hdd-row').remove()">X</button>
            </div>
        </div>
    `;
}

function addHddToCard(button) {
    const card = button.closest('.item-card');
    const area = card.querySelector('.hdd-area');
    area.insertAdjacentHTML('beforeend', hddRowHtml());
}

function collectFormData() {
    const cards = document.querySelectorAll('.item-card');

    let items = [];
    let newItems = [];

    cards.forEach(card => {
        let hdds = [];

        card.querySelectorAll('.hdd-row').forEach(row => {
            hdds.push({
                id: row.dataset.hddId || null,
                serial: row.querySelector('.hdd-serial')?.value || '',
                size: row.querySelector('.hdd-size')?.value || '',
                status: row.querySelector('.hdd-status')?.value || 'not_processed',
                notes: row.querySelector('.hdd-notes')?.value || '',
            });
        });

        if (card.classList.contains('new-item-card')) {
            const manufacturerSelect = card.querySelector('.item-manufacturer');
            const modelSelect = card.querySelector('.item-model');

            newItems.push({
                qty: card.querySelector('.item-qty').value || 1,
                category_id: card.querySelector('.item-category').value || null,
                category_name: card.querySelector('.item-category-name').value || '',
                weight_kg: card.querySelector('.item-weight').value || 0,
                ewc_code: card.querySelector('.item-ewc-code').value || '',
                component: card.querySelector('.item-component').value || '',
                concentration: card.querySelector('.item-concentration').value || '',
                physical_form: card.querySelector('.item-form').value || '',
                hazard_codes: card.querySelector('.item-hazard').value || '',

                manufacturer_id: manufacturerSelect.value === 'manual' ? null : manufacturerSelect.value,
                manufacturer_text: card.querySelector('.item-manufacturer-text').value || '',
                product_model_id: modelSelect.value === 'manual' ? null : modelSelect.value,
                model_text: card.querySelector('.item-model-text').value || '',

                serial_number: card.querySelector('.item-serial').value || '',
                asset_tags: card.querySelector('.item-asset').value || '',
                our_asset_number: card.querySelector('.item-our-asset').value || '',
                storage_serial_number: card.querySelector('.item-storage').value || '',

                is_collected: card.querySelector('.item-collected').checked,
                erasure_required: card.querySelector('.item-erasure').checked,

                hdds: hdds
            });
        } else {
            items.push({
                id: card.querySelector('.item-id').value,
                is_collected: card.querySelector('.item-collected').checked,
                serial_number: card.querySelector('.item-serial').value || '',
                asset_tags: card.querySelector('.item-asset').value || '',
                our_asset_number: card.querySelector('.item-our-asset').value || '',
                storage_serial_number: card.querySelector('.item-storage').value || '',
                hdds: hdds
            });
        }
    });

    return {
        collection_id: COLLECTION_ID,
        items: items,
        new_items: newItems,
        client_signature: document.getElementById('client_signature').value,
        driver_signature: document.getElementById('driver_signature').value,
        client_print_name: document.getElementById('client_print_name').value,
        driver_print_name: document.getElementById('driver_print_name').value,
        saved_at: new Date().toISOString()
    };
}

function saveOfflineData() {
    if (!collectionData) {
        setMessage('No collection data loaded.', 'danger');
        return;
    }

    const payload = collectFormData();
    localStorage.setItem(STORAGE_KEY + '_payload', JSON.stringify(payload));

    setMessage('Saved offline on this device.', 'success');
}

async function syncCollection() {
    saveOfflineData();

    if (!navigator.onLine) {
        setMessage('You are offline. Data saved. Sync when internet returns.', 'warning');
        return;
    }

    const payload = collectFormData();

    try {
        const res = await fetch(SYNC_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify(payload)
        });

        if (!res.ok) {
            throw new Error('Sync failed');
        }

        const json = await res.json();

        localStorage.removeItem(STORAGE_KEY + '_payload');

        setMessage(json.message || 'Synced successfully.', 'success');

    } catch (e) {
        setMessage('Sync failed. Offline data is still saved.', 'danger');
    }
}

function loadSignatureValues(data) {
    document.getElementById('client_signature').value = data.collection.client_signature || '';
    document.getElementById('driver_signature').value = data.collection.driver_signature || '';
    document.getElementById('client_print_name').value = data.collection.client_print_name || '';
    document.getElementById('driver_print_name').value = data.collection.driver_print_name || '';
}

function initSignaturePad(canvasId, hiddenId) {
    const canvas = document.getElementById(canvasId);
    const hidden = document.getElementById(hiddenId);
    const ctx = canvas.getContext('2d');

    function resize() {
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width;
        canvas.height = 160;
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';

        if (hidden.value) {
            const img = new Image();
            img.onload = function () {
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            };
            img.src = hidden.value;
        }
    }

    setTimeout(resize, 200);

    let drawing = false;
    let last = {x: 0, y: 0};

    function pos(e) {
        const rect = canvas.getBoundingClientRect();
        const touch = e.touches ? e.touches[0] : e;

        return {
            x: touch.clientX - rect.left,
            y: touch.clientY - rect.top
        };
    }

    function start(e) {
        drawing = true;
        last = pos(e);
        e.preventDefault();
    }

    function move(e) {
        if (!drawing) return;

        const p = pos(e);

        ctx.beginPath();
        ctx.moveTo(last.x, last.y);
        ctx.lineTo(p.x, p.y);
        ctx.stroke();

        last = p;
        hidden.value = canvas.toDataURL('image/png');

        e.preventDefault();
    }

    function end() {
        drawing = false;
        hidden.value = canvas.toDataURL('image/png');
    }

    canvas.addEventListener('mousedown', start);
    canvas.addEventListener('mousemove', move);
    window.addEventListener('mouseup', end);

    canvas.addEventListener('touchstart', start, {passive: false});
    canvas.addEventListener('touchmove', move, {passive: false});
    window.addEventListener('touchend', end);
}

function clearCanvas(canvasId, hiddenId) {
    const canvas = document.getElementById(canvasId);
    const ctx = canvas.getContext('2d');

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    document.getElementById(hiddenId).value = '';
}

window.downloadOfflineData = downloadOfflineData;
window.syncCollection = syncCollection;
window.addNewItem = addNewItem;
window.saveOfflineData = saveOfflineData;
window.addHddToCard = addHddToCard;
window.fillCategoryData = fillCategoryData;
window.manufacturerChanged = manufacturerChanged;
window.modelChanged = modelChanged;
window.clearCanvas = clearCanvas;

document.addEventListener('DOMContentLoaded', function () {
    updateOnlineStatus();
    initSignaturePad('clientCanvas', 'client_signature');
    initSignaturePad('driverCanvas', 'driver_signature');
    loadOfflineData();
});
document.addEventListener('DOMContentLoaded', function () {
    updateOnlineStatus();

    initSignaturePad('clientCanvas', 'client_signature');
    initSignaturePad('driverCanvas', 'driver_signature');

    document.getElementById('downloadBtn').addEventListener('click', downloadOfflineData);
    document.getElementById('syncBtn').addEventListener('click', syncCollection);
    document.getElementById('addItemBtn').addEventListener('click', addNewItem);
    document.getElementById('saveOfflineBtn').addEventListener('click', saveOfflineData);

    loadOfflineData();
});
</script>
@stop