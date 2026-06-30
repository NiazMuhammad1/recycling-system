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
<script src="https://cdn.jsdelivr.net/npm/idb@8/build/umd.js"></script>
@push('js')
<script>
const COLLECTION_ID = "{{ $collection->id }}";
const DATA_URL = "/driver/collections/{{ $collection->id }}/offline-data";
const MASTER_DATA_URL = "/driver/offline-master-data";
const SYNC_URL = "{{ route('driver.collections.sync', $collection) }}";
const CSRF_TOKEN = "{{ csrf_token() }}";

const DB_NAME = 'itad-offline';
const COLLECTION_STORE = 'collections';
const ITEM_STORE = 'items';
const MASTER_STORE = 'master';

let dbPromise = idb.openDB(DB_NAME, 1, {
    upgrade(db) {
        if(!db.objectStoreNames.contains(COLLECTION_STORE)) db.createObjectStore(COLLECTION_STORE, { keyPath: 'id' });
        if(!db.objectStoreNames.contains(ITEM_STORE)) db.createObjectStore(ITEM_STORE, { keyPath: 'temp_id', autoIncrement:true });
        if(!db.objectStoreNames.contains(MASTER_STORE)) db.createObjectStore(MASTER_STORE, { keyPath: 'id' });
    }
});

function setMessage(msg,type='info'){
    document.getElementById('syncMessage').innerHTML = `<div class="alert alert-${type}">${msg}</div>`;
}

function updateOnlineStatus(){
    const badge = document.getElementById('onlineStatus');
    badge.className = navigator.onLine ? 'badge badge-success' : 'badge badge-danger';
    badge.innerText = navigator.onLine ? 'Online' : 'Offline';
}
window.addEventListener('online', updateOnlineStatus);
window.addEventListener('offline', updateOnlineStatus);

// --- Offline data download ---
async function downloadOfflineData() {
    try {
        const master = await (await fetch(MASTER_DATA_URL)).json();
        const db = await dbPromise;
        await db.put(MASTER_STORE, { id:'master', ...master });

        const collectionData = await (await fetch(DATA_URL)).json();
        await db.put(COLLECTION_STORE, collectionData.collection);
        for(const item of collectionData.items) await db.put(ITEM_STORE,{...item, collection_id:COLLECTION_ID});

        renderCollection(collectionData.items);
        loadSignatureValues(collectionData.collection);
        setMessage('Offline data downloaded successfully','success');
    } catch(e){
        setMessage('Download failed. Loading previous offline data if exists.','warning');
        loadOfflineData();
    }
}

// --- Load offline data ---
async function loadOfflineData(){
    const db = await dbPromise;
    const collection = await db.get(COLLECTION_STORE, COLLECTION_ID);
    const items = await db.getAll(ITEM_STORE);
    if(!collection || !items.length){ setMessage('No offline data found. Download first.','danger'); return; }
    renderCollection(items);
    loadSignatureValues(collection);
    setMessage('Loaded offline data.','success');
}

// --- Render collection ---
function renderCollection(items){
    const area = document.getElementById('itemsArea');
    area.innerHTML='';
    items.forEach((item,index)=>{
        const card=document.createElement('div');
        card.className='card mb-3 item-card';
        card.dataset.tempId = item.temp_id || item.id;
        card.innerHTML = `
            <div class="card-header d-flex justify-content-between">
                <strong>${item.item_code || 'Item'} - ${item.category_name || ''}</strong>
                <label><input type="checkbox" class="item-collected" ${item.is_collected?'checked':''}> Collected</label>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3"><label>Serial</label><input class="form-control item-serial" value="${item.serial_number||''}"></div>
                    <div class="col-md-3"><label>Asset</label><input class="form-control item-asset" value="${item.asset_tags||''}"></div>
                    <div class="col-md-3"><label>Our Asset #</label><input class="form-control item-our-asset" value="${item.our_asset_number||''}"></div>
                    <div class="col-md-3"><label>Storage Serial</label><input class="form-control item-storage" value="${item.storage_serial_number||''}"></div>
                </div>
                <hr>
                <h6>Hard Disks</h6>
                <div class="hdd-area">${(item.hdds||[]).map(hdd=>hddRowHtml(hdd)).join('')}</div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addHddToCard(this)">Add HDD</button>
            </div>
        `;
        area.appendChild(card);
    });
}

// --- HDD row HTML ---
function hddRowHtml(hdd={}){
    return `<div class="row hdd-row mb-2" data-hdd-id="${hdd.id||''}">
        <div class="col-md-3"><input class="form-control hdd-serial" value="${hdd.serial||''}" placeholder="HDD Serial"></div>
        <div class="col-md-2"><input class="form-control hdd-size" value="${hdd.size||''}" placeholder="500GB/1TB"></div>
        <div class="col-md-3">
            <select class="form-control hdd-status">
                <option value="not_processed" ${hdd.status==='not_processed'?'selected':''}>Not Processed</option>
                <option value="erased" ${hdd.status==='erased'?'selected':''}>Erased</option>
                <option value="failed" ${hdd.status==='failed'?'selected':''}>Failed</option>
                <option value="shredding" ${hdd.status==='shredding'?'selected':''}>Shredding</option>
            </select>
        </div>
        <div class="col-md-3"><input class="form-control hdd-notes" value="${hdd.notes||''}" placeholder="Notes"></div>
        <div class="col-md-1"><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.hdd-row').remove()">X</button></div>
    </div>`;
}

// --- Add HDD ---
function addHddToCard(button){
    const card = button.closest('.item-card');
    card.querySelector('.hdd-area').insertAdjacentHTML('beforeend',hddRowHtml());
}

// --- Add new item ---
function addNewItem(){
    const key='new_'+Date.now();
    const html=`<div class="card mb-3 item-card new-item-card" data-new-key="${key}">
        <div class="card-header d-flex justify-content-between"><strong>New Item</strong>
            <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.item-card').remove()">Remove</button>
        </div>
        <div class="card-body"><div class="row"><div class="col-md-3"><label>Serial</label><input class="form-control item-serial"></div></div></div>
    </div>`;
    document.getElementById('itemsArea').insertAdjacentHTML('afterbegin',html);
    setMessage('New item added. Fill details then Save Offline.','success');
}

// --- Collect form data ---
function collectFormData(){
    const cards=document.querySelectorAll('.item-card');
    const items=[],newItems=[];
    cards.forEach(card=>{
        const hdds=Array.from(card.querySelectorAll('.hdd-row')).map(row=>({
            id: row.dataset.hddId||null,
            serial: row.querySelector('.hdd-serial').value,
            size: row.querySelector('.hdd-size').value,
            status: row.querySelector('.hdd-status').value,
            notes: row.querySelector('.hdd-notes').value
        }));
        if(card.classList.contains('new-item-card')){
            newItems.push({
                serial_number: card.querySelector('.item-serial').value,
                hdds
            });
        } else {
            items.push({
                id: card.querySelector('.item-id')?.value,
                serial_number: card.querySelector('.item-serial').value,
                asset_tags: card.querySelector('.item-asset')?.value||'',
                our_asset_number: card.querySelector('.item-our-asset')?.value||'',
                storage_serial_number: card.querySelector('.item-storage')?.value||'',
                hdds
            });
        }
    });
    return {collection_id:COLLECTION_ID,items,new_items:newItems,
        client_signature:document.getElementById('client_signature').value,
        driver_signature:document.getElementById('driver_signature').value,
        client_print_name:document.getElementById('client_print_name').value,
        driver_print_name:document.getElementById('driver_print_name').value};
}

// --- Save offline ---
async function saveOfflineData(){
    const payload=collectFormData();
    const db=await dbPromise;
    for(const i of payload.items) await db.put(ITEM_STORE,i);
    for(const i of payload.new_items) await db.put(ITEM_STORE,{...i,temp_id:Date.now()});
    await db.put(COLLECTION_STORE,{id:COLLECTION_ID,...payload});
    setMessage('Offline data saved locally','success');
}

// --- Sync ---
async function syncCollection(){
    const payload=collectFormData();
    if(!navigator.onLine){ setMessage('Offline. Sync later','warning'); return; }
    try{
        const res=await fetch(SYNC_URL,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},body:JSON.stringify(payload)});
        const json=await res.json();
        setMessage(json.message||'Synced successfully','success');
    }catch(e){ setMessage('Sync failed. Offline data still saved.','danger'); }
}

// --- Signatures helper ---
function loadSignatureValues(col){['client_signature','driver_signature'].forEach(id=>{
    document.getElementById(id).value=col[id]||'';
})}

// --- Init ---
document.addEventListener('DOMContentLoaded',async()=>{
    updateOnlineStatus();
    loadOfflineData();
    document.getElementById('downloadBtn').addEventListener('click',downloadOfflineData);
    document.getElementById('syncBtn').addEventListener('click',syncCollection);
    document.getElementById('addItemBtn').addEventListener('click',addNewItem);
    document.getElementById('saveOfflineBtn').addEventListener('click',saveOfflineData);
});

// --- Service Worker ---
if('serviceWorker' in navigator){
    navigator.serviceWorker.register('/service-worker.js')
        .then(()=>console.log('Service Worker registered'))
        .catch(err=>console.error(err));
}
</script>
@endpush
