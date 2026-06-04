@extends('adminlte::page')
@section('title', 'Collection '.$collection->collection_number)

@section('content')
<div class="container-fluid">
    @if (session('success'))
    <div id="success-alert" class="alert alert-success alert-dismissible fade show" role="alert">
        <h5><i class="icon fas fa-check"></i> Success!</h5>
        {{ session('success') }}
        <!-- Added text-white class below -->
        <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color:white">
            <span aria-hidden="true" style="color:white">&times;</span>
        </button>
    </div>

    <script>
        setTimeout(function() {
            let alert = document.getElementById('success-alert');
            if (alert) {
                if (typeof $ !== 'undefined') {
                    $(alert).fadeOut('slow');
                } else {
                    alert.style.transition = "opacity 0.5s ease";
                    alert.style.opacity = 0;
                    setTimeout(() => alert.remove(), 500);
                }
            }
        }, 3000); 
    </script>
    @endif
    <div class="d-flex align-items-center mb-2">
        <h1 class="mb-0">Collection {{ $collection->collection_number }}</h1>
        @can('collections.modify')
        <a class="ml-2" href="{{ route('collections.edit',$collection) }}">[edit]</a>
        @endcan
        &nbsp;&nbsp;
        <a class="btn btn-sm btn-outline-primary"
        target="_blank"
        href="{{ route('collections.pdf.duty_of_care', $collection) }}">
        Duty of Care PDF
        </a>&nbsp;&nbsp;
        <a class="btn btn-sm btn-outline-danger"
        target="_blank"
        href="{{ route('collections.pdf.hazardous', $collection) }}">
        Hazardous PDF
        </a>&nbsp;&nbsp;
        <a class="btn btn-sm btn-outline-primary"
        target="_blank"
        href="{{ route('collections.pdf.data_destruction', $collection) }}">
        Data Destruction PDF
        </a>&nbsp;&nbsp;
        <a class="btn btn-sm btn-outline-primary"
        target="_blank"
        href="{{ route('collections.pdf.audit_report', $collection) }}">
        Audit Report PDF
        </a>
        &nbsp;&nbsp;
        <a class="btn btn-sm btn-outline-primary"
        target="_blank"
        href="{{ route('collections.pdf.weee_disposal', $collection) }}">
        Wee Disposal PDF
        </a>
        &nbsp;&nbsp;
        <a class="btn btn-sm btn-outline-primary"
        target="_blank"
        href="{{ route('collections.pdf-emails.index', $collection) }}">
        Send to Client
        </a>
        &nbsp;&nbsp;
        <form action="{{ route('collections.send-pdfs', $collection) }}"
            method="POST"
            class="d-inline">
            @csrf

            <button type="submit" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-envelope"></i>
                Email PDFs
            </button>
        </form>
    </div>
    
    <div class="mb-3 text-muted">Status: {{ ucfirst($collection->status) }}</div>
    <div class="mb-3 text-right">
        <a class="btn btn-sm btn-primary mr-2" href="{{ route('collections.items.edit',$collection) }}">[edit]</a>
        <a class="btn btn-sm btn-primary mr-2" href="{{ route('collections.collect.form',$collection) }}">[collect]</a>
        <a class="btn btn-sm btn-primary mr-2" href="{{ route('collections.process.index',$collection) }}">[process]</a>
    </div>
    
    <div class="card">
        <div class="card-body">
            {{-- top summary --}}
            <div class="row">
                <div class="col-md-6">
                    <h5>Client Details & Location</h5>
                    <table class="table table-borderless table-sm mb-0">
                        <tr><td style="width:200px;">Client</td><td>{{ $collection->client?->name }}</td></tr>
                        <tr><td>Collection Date</td><td>{{ optional($collection->collection_date)->format('d/m/Y H:i') }}</td></tr>
                        <tr><td>Address Line 1</td><td>{{ $collection->address_line_1 }}</td></tr>
                        <tr><td>Address Line 2</td><td>{{ $collection->address_line_2 }}</td></tr>
                        <tr><td>Town</td><td>{{ $collection->town }}</td></tr>
                        <tr><td>County</td><td>{{ $collection->county }}</td></tr>
                        <tr><td>Country</td><td>{{ $collection->country }}</td></tr>
                        <tr><td>Postcode</td><td>{{ $collection->postcode }}</td></tr>
                        <tr><td>Service Cost</td><td> &pound; {{ $collection->service_cost }}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Contact Details</h5>
                    <table class="table table-borderless table-sm mb-0">
                        <tr><td style="width:200px;">Contact Name</td><td>{{ $collection->contact_name }}</td></tr>
                        <tr><td>Contact Email's</td><td>{{ $collection->contact_email }} / {{ $collection->sec_contact_email }} </td></tr>
                        <tr><td>Contact Number</td><td>{{ $collection->contact_number }}</td></tr>
                        <tr><td>On Site Contact Name</td><td>{{ $collection->on_site_contact_name }}</td></tr>
                        <tr><td>On Site Contact Number</td><td>{{ $collection->on_site_contact_number }}</td></tr>
                    </table>
                </div>
            </div>
            <hr>
            <div class="row mt-3">
                <div class="col-md-6">
                    <h5>Logistics & Access</h5>
                    <table class="table table-borderless table-sm mb-0">
                        <tr><td style="width:200px;">Collection Type</td><td>{{ $collection->collection_type ?? '-' }}</td></tr>
                        <tr><td>Logistics</td><td>{{ $collection->logistics ?? '-' }}</td></tr>
                        <tr><td>Equipment Location</td><td>{{ $collection->equipment_location ?? '-' }}</td></tr>
                        <tr><td>Access Elevator</td><td>{{ $collection->access_elevator ?? '-'  }}</td></tr>
                        <tr><td>Route Restrictions</td><td>{{ $collection->route_restrictions ?? '-'  }}</td></tr>
                        <tr><td>Other Information</td><td>{{ $collection->other_information ?? '-' }}</td></tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h5>Vehicle Used</h5>
                    <table class="table table-borderless table-sm mb-0">
                        <tr><td style="width:200px;">Vehicles Used</td><td>{{ $collection->vehicles_used ?? '-' }}</td></tr>
                        <tr><td>Driver Name</td><td>{{ $collection->client?->name ?? '-' }}</td></tr>
                        <tr><td>Created By</td><td>{{ $collection->user?->name ?? '-' }}</td></tr>
                        <tr><td>ADISA / DIAL Rating</td><td>{{ $collection->adisa_dial_rating ?? '-' }}</td></tr>
                    </table>
                </div>
            </div>
            <hr>
            <div class="row mt-3">
                <div class="col-md-6">
                    <h5>Compliance & Audit</h5>
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td style="width:200px;">Data Sanitisation</td>
                            <td>{{ $collection->data_sanitisation ?? '-'  }}</td>
                        </tr>
                        <tr>
                            <td>Pre-Collection Audit</td>
                            <td>{{ $collection->pre_collection_audit ?? '-'  }}</td>
                        </tr>
                        
                    </table>
                </div>

                <div class="col-md-6">
                    <h5>Equipment Classification</h5>
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td style="width:200px;">Equipment Classification</td>
                            <td>{{ $collection->equipment_classification ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>ADISA / DIAL Rating</td>
                            <td>{{ $collection->adisa_dial_rating ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <hr>
            <div class="row mt-4">
                <div class="col-md-6">
                    <h5>Client Sign-off</h5>
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td style="width:200px;">Client Name</td>
                            <td>{{ $collection->client_print_name ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td>Job Title</td>
                            <td>{{ $collection->client_job_title ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td>Signature</td>
                            <td>
                                @if($collection->client_signature)
                                    <img src="{{ asset('storage/'.$collection->client_signature) }}"
                                        alt="Client Signature"
                                        style="max-height:80px;">
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h5>Driver Sign-off</h5>
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td style="width:200px;">Driver Name</td>
                            <td>{{ $collection->driver_print_name ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td>Job Title</td>
                            <td>{{ $collection->driver_job_title ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td>Signature</td>
                            <td>
                                @if($collection->driver_signature)
                                    <img src="{{ asset('storage/'.$collection->driver_signature) }}"
                                        alt="Driver Signature"
                                        style="max-height:80px;">
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>


            <hr>

            {{-- Tabs --}}
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tab_items" role="tab">Items</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab_tasks" role="tab">Tasks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab_docs" role="tab">Documents</a>
                </li>
            </ul>

            <div class="tab-content border border-top-0 p-3">
                <div class="tab-pane fade show active" id="tab_items" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">ITEMS</h5>
                        <div>
                            <a class="btn btn-sm btn-primary mr-2" href="{{ route('collections.items.edit',$collection) }}">[edit]</a>
                            <a class="btn btn-sm btn-primary mr-2" href="{{ route('collections.collect.form',$collection) }}">[collect]</a>
                            <a class="btn btn-sm btn-primary mr-2" href="{{ route('collections.process.index',$collection) }}">[process]</a>
                        </div>
                    </div>

                    <style>
                        .item-card {
                            border: 1px solid #e5e7eb;
                            border-radius: 8px;
                            margin-bottom: 12px;
                            background: #fff;
                            transition: 0.2s;
                        }

                        .item-card:hover {
                            background: #f9fafb;
                        }

                        /* Header */
                        .item-header {
                            padding: 10px 15px;
                            border-bottom: 1px solid #eee;
                            background: #f8f9fa;
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                        }

                        .item-title {
                            font-weight: 600;
                        }

                        .item-sub {
                            font-size: 12px;
                            color: #6c757d;
                        }

                        /* Body grid */
                        .item-body {
                            padding: 12px 15px;
                        }

                        .item-grid {
                            display: grid;
                            grid-template-columns: repeat(6, 1fr);
                            gap: 10px 15px;
                        }

                        .label {
                            font-size: 13px;
                            color: #6c757d;
                        }

                        .value {
                            font-size: 15px;
                            font-weight: 500;
                            color: #212529;
                        }

                        /* Responsive */
                        @media (max-width: 1200px) {
                            .item-grid {
                                grid-template-columns: repeat(3, 1fr);
                            }
                        }

                        @media (max-width: 768px) {
                            .item-grid {
                                grid-template-columns: repeat(2, 1fr);
                            }
                        }
                        </style>


                        @foreach($collection->items->sortBy('item_code') as $it)

                        <div class="item-card">

                            {{-- 🔹 HEADER --}}
                            <div class="item-header">
                                <div>
                                    <div class="item-title">#{{ $it->item_code }}</div>
                                    <div class="item-sub">Qty: {{ $it->qty }}</div>
                                </div>

                                <div>
                                    @if($it->status === 'added_to_stock')
                                        <span class="badge badge-success">Added</span>
                                    @elseif($it->status === 'collected')
                                        <span class="badge badge-primary">Collected</span>
                                    @elseif($it->status === 'processed')
                                        <span class="badge badge-dark">Processed</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($it->status) }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- 🔹 BODY --}}
                            <div class="item-body">
                                <div class="item-grid">

                                    <div>
                                        <div class="label">Category</div>
                                        <div class="value">{{ $it->category_name ?? $it->category?->name }}</div>
                                    </div>

                                    <div>
                                        <div class="label">Manufacturer</div>
                                        <div class="value">{{ $it->manufacturerRel?->name ?? $it->manufacturer_text }}</div>
                                    </div>

                                    <div>
                                        <div class="label">Model</div>
                                        <div class="value">{{ $it->productModel?->name ?? $it->model_text }}</div>
                                    </div>

                                    <div>
                                        <div class="label">Serial</div>
                                        <div class="value">{{ $it->serial_number ?? '-' }}</div>
                                    </div>

                                    <div>
                                        <div class="label">Asset Tag</div>
                                        <div class="value">{{ $it->asset_tags ?? '-' }}</div>
                                    </div>

                                    <div>
                                        <div class="label">Our Asset</div>
                                        <div class="value">{{ $it->our_asset_number ?? '-' }}</div>
                                    </div>

                                    <div>
                                        <div class="label">Storage</div>
                                        <div class="value">{{ $it->storage_serial_number ?? '-' }}</div>
                                    </div>

                                    <div>
                                        <div class="label">Second Storage</div>
                                        <div class="value">{{ $it->second_storage_serial_number ?? '-' }}</div>
                                    </div>

                                    <div>
                                        <div class="label">Weight</div>
                                        <div class="value">{{ $it->weight_kg ?? $it->category?->weight_kg ?? '-' }} kg</div>
                                    </div>

                                    <div>
                                        <div class="label">EWC</div>
                                        <div class="value">{{ $it->ewc_code ?? $it->category?->ewc_code ?? '-' }}</div>
                                    </div>

                                    <div>
                                        <div class="label">Component</div>
                                        <div class="value">{{ $it->component ?? $it->category?->component ?? '-' }}</div>
                                    </div>

                                    <div>
                                        <div class="label">Concentration</div>
                                        <div class="value">{{ $it->concentration ?? $it->category?->concentration ?? '-' }}</div>
                                    </div>

                                    <div>
                                        <div class="label">Form</div>
                                        <div class="value">{{ $it->physical_form ?? $it->category?->physical_form ?? '-' }}</div>
                                    </div>

                                    <div>
                                        <div class="label">Hazard</div>
                                        <div class="value">{{ $it->hazard_codes ?? $it->category?->hazard_codes ?? '-' }}</div>
                                    </div>

                                    <div>
                                        <div class="label">Erasure</div>
                                        <div class="value">
                                            <span class="badge {{ $it->erasure_required ? 'badge-danger' : 'badge-secondary' }}">
                                                {{ $it->erasure_required ? 'Yes' : 'No' }}
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                        @endforeach


                        {{-- Footer --}}
                        <div class="text-right mt-2">
                            <strong>Total Weight:</strong>
                            {{ number_format($collection->total_weight,2) }} Kg
                        </div>
                </div>

                <div class="tab-pane fade" id="tab_tasks" role="tabpanel">
                    <div class="text-muted">Tasks module next.</div>
                </div>

                <div class="tab-pane fade" id="tab_docs" role="tabpanel">
                    <div class="text-muted">Documents module next.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
