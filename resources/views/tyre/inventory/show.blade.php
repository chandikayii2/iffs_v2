<!-- resources/views/tyre/inventory/show.blade.php -->
@extends('tyre.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-info-circle me-2"></i>Tyre Details</h4>
        <h6>Complete tyre information and history - {{ $tyre->serial_number }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tyre.inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
        <a href="{{ route('tyre.inventory.edit', $tyre->id) }}" class="btn btn-primary" title="Edit Tyre">
            <i class="fas fa-edit"></i>
        </a>
        <a href="{{ route('tyre.inventory.gate-pass', $tyre->id) }}" class="btn btn-info" target="_blank" title="Gate Pass">
            <i class="fas fa-passport"></i>
        </a>
        <a href="{{ route('tyre.passport.pdf', $tyre->id) }}" class="btn btn-danger" target="_blank" title="Download PDF">
            <i class="fas fa-file-pdf"></i>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Tyre Information</h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Serial Number:</label>
                    <div class="col-lg-8">
                        <p class="form-control-static"><strong>{{ $tyre->serial_number }}</strong></p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Brand:</label>
                    <div class="col-lg-8">
                        <p>{{ $tyre->brand }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Size:</label>
                    <div class="col-lg-8">
                        <p>{{ $tyre->size }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Type:</label>
                    <div class="col-lg-8">
                        <p>{{ $tyre->type }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Vendor/Supplier:</label>
                    <div class="col-lg-8">
                        @if($tyre->vendor)
                            <p>
                                <strong>{{ $tyre->vendor->name }}</strong><br>
                                Contact: {{ $tyre->vendor->contact_person }}<br>
                                Phone: {{ $tyre->vendor->phone }}
                            </p>
                        @else
                            <p>N/A</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Status & Usage</h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Status:</label>
                    <div class="col-lg-8">
                        @php
                            $badgeClass = 'badge-soft-success';
                            if($tyre->status == 'new') $badgeClass = 'badge-soft-success';
                            elseif($tyre->status == 'in_use') $badgeClass = 'badge-soft-primary';
                            elseif($tyre->status == 'used') $badgeClass = 'badge-soft-warning';
                            elseif($tyre->status == 'at_vendor') $badgeClass = 'badge-soft-danger';
                            elseif($tyre->status == 'scrap') $badgeClass = 'badge-soft-dark';
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $tyre->status)) }}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Refill Count:</label>
                    <div class="col-lg-8">
                        <p>
                            <span class="badge {{ $tyre->refill_count >= $tyre->max_refills ? 'badge-soft-danger' : 'badge-soft-info' }}">
                                {{ $tyre->refill_count }} / {{ $tyre->max_refills }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Consumed Mileage:</label>
                    <div class="col-lg-8">
                        <p><strong>{{ number_format($totalConsumedMileage ?? $tyre->consumption_mileage) }} km</strong></p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Purchase Date:</label>
                    <div class="col-lg-8">
                        <p>{{ $tyre->purchase_date->format('d-m-Y') }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Purchase Price:</label>
                    <div class="col-lg-8">
                        <p>Rs.{{ number_format($tyre->purchase_price, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap">
        <h5 class="mb-0 me-3">Tyre Lifecycle History</h5>
        <div class="d-flex align-items-center gap-3">
            @if($tyre->status == 'in_use')
                <a href="{{ route('tyre.inventory.remove-from-vehicle', $tyre->id) }}" class="btn btn-warning btn-sm" title="Remove from Vehicle">
                    <i class="fas fa-tools"></i>
                </a>
            @endif
            <a href="{{ route('tyre.passport.pdf', $tyre->id) }}" class="btn btn-danger btn-sm" target="_blank" title="Download PDF">
                <i class="fas fa-file-pdf"></i>
            </a>
            <ul class="nav nav-pills" id="historyTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active btn-sm" id="timeline-tab" data-bs-toggle="pill" data-bs-target="#timeline-view" type="button" role="tab">
                        <i class="fas fa-stream me-1"></i> Timeline
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link btn-sm" id="table-tab" data-bs-toggle="pill" data-bs-target="#table-view" type="button" role="tab">
                        <i class="fas fa-table me-1"></i> Table List
                    </button>
                </li>
            </ul>
        </div>
    </div>
    <div class="card-body">
        <div class="tab-content" id="historyTabContent">
            <!-- Visual Timeline View -->
            <div class="tab-pane fade show active" id="timeline-view" role="tabpanel" aria-labelledby="timeline-tab">
                <div class="timeline-container">
                    @forelse($lifecycleHistory as $history)
                        @php
                            $cleanedType = str_replace(' ', '-', $history['type']);
                            $icon = 'fa-history';
                            $colorClass = 'text-primary';
                            if ($history['type'] == 'Installation') { $icon = 'fa-truck-loading'; $colorClass = 'text-primary'; }
                            elseif ($history['type'] == 'Removal') { $icon = 'fa-wrench'; $colorClass = 'text-danger'; }
                            elseif ($history['type'] == 'Sent for Refilling') { $icon = 'fa-paper-plane'; $colorClass = 'text-warning'; }
                            elseif ($history['type'] == 'Received from Refilling') { $icon = 'fa-check-double'; $colorClass = 'text-success'; }
                            elseif ($history['type'] == 'Scrapped') { $icon = 'fa-trash-alt'; $colorClass = 'text-dark'; }
                            elseif ($history['type'] == 'Purchase') { $icon = 'fa-shopping-cart'; $colorClass = 'text-info'; }
                            elseif (strpos($history['type'], 'Dag Round') !== false) { $icon = 'fa-sync-alt'; $colorClass = 'text-info'; }
                        @endphp
                        <div class="timeline-item {{ $cleanedType }}">
                            <div class="timeline-badge bg-white shadow-sm {{ $colorClass }}">
                                <i class="fas {{ $icon }}"></i>
                            </div>
                            <div class="timeline-date">{{ \Carbon\Carbon::parse($history['date'])->format('d M Y') }}</div>
                            <div class="timeline-title">{{ $history['type'] }}</div>
                            <div class="timeline-content">
                                <p class="mb-1">{{ $history['details'] }}</p>
                                <small class="text-muted d-block">
                                    @if(isset($history['mileage']))
                                        <i class="fas fa-tachometer-alt me-1"></i>Consumed: {{ number_format($history['mileage']) }} km
                                    @elseif(isset($history['cost']))
                                        <i class="fas fa-money-bill-wave me-1"></i>Cost: Rs.{{ number_format($history['cost'], 2) }}
                                    @elseif(isset($history['info']))
                                        <i class="fas fa-info-circle me-1"></i>{{ $history['info'] }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-history text-muted fa-2x mb-2"></i>
                            <p class="mb-0">No history events recorded yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Table View -->
            <div class="tab-pane fade" id="table-view" role="tabpanel" aria-labelledby="table-tab">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Details</th>
                                <th>Mileage/Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lifecycleHistory as $history)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($history['date'])->format('d-m-Y') }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $history['type'] }}</span></td>
                                <td>{{ $history['details'] }}</td>
                                <td>
                                    @if(isset($history['mileage']))
                                        {{ number_format($history['mileage']) }} km
                                    @elseif(isset($history['cost']))
                                        Rs.{{ number_format($history['cost'], 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No history records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Visual Timeline Styles */
    .timeline-container {
        position: relative;
        padding-left: 35px;
        margin-left: 15px;
        border-left: 2px dashed #dee2e6;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
    }
    .timeline-item:last-child {
        margin-bottom: 0;
    }
    .timeline-badge {
        position: absolute;
        left: -53px;
        top: 2px;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #dee2e6;
        z-index: 2;
    }
    .timeline-date {
        font-size: 0.85rem;
        font-weight: 700;
        color: #8c9099;
        margin-bottom: 2px;
    }
    .timeline-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #2c3038;
        margin-bottom: 8px;
    }
    .timeline-content {
        background-color: #f8f9fa;
        padding: 12px 15px;
        border-radius: 8px;
        border-left: 4px solid #dee2e6;
    }
    
    /* Event Colors */
    .timeline-item.Installation .timeline-content { border-left-color: #0d6efd; }
    .timeline-item.Installation .timeline-badge { border-color: #cfe2ff; }
    
    .timeline-item.Removal .timeline-content { border-left-color: #dc3545; }
    .timeline-item.Removal .timeline-badge { border-color: #f8d7da; }
    
    .timeline-item.Sent-for-Refilling .timeline-content { border-left-color: #fd7e14; }
    .timeline-item.Sent-for-Refilling .timeline-badge { border-color: #ffe5d0; }
    
    .timeline-item.Received-from-Refilling .timeline-content { border-left-color: #198754; }
    .timeline-item.Received-from-Refilling .timeline-badge { border-color: #d1e7dd; }
    
    .timeline-item.Scrapped .timeline-content { border-left-color: #212529; }
    .timeline-item.Scrapped .timeline-badge { border-color: #e2e3e5; }
    
    .timeline-item.Purchase .timeline-content { border-left-color: #0dcaf0; }
    .timeline-item.Purchase .timeline-badge { border-color: #cff4fc; }

    /* Print Styles */
    @media print {
        header, .header, .sidebar, .page-header, .btn, .btn-print, footer, .page-btn, #historyTab, .card-header-tabs {
            display: none !important;
        }
        .page-wrapper {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
            margin-bottom: 20px !important;
        }
        .tab-content > .tab-pane {
            display: block !important;
            opacity: 1 !important;
        }
        #table-view {
            display: none !important; /* Prefer showing the beautiful timeline on print */
        }
        body {
            background: white !important;
            color: black !important;
        }
        .timeline-content {
            background-color: #fff !important;
            border: 1px solid #dee2e6 !important;
            border-left: 4px solid #dee2e6 !important;
        }
    }
</style>
@endpush

<div class="card mt-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Notes</h5>
    </div>
    <div class="card-body">
        <p>{{ $tyre->notes ?? 'No additional notes' }}</p>
    </div>
</div>
@endsection