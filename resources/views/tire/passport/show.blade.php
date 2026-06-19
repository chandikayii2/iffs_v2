<!-- resources/views/tire/passport/show.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-passport me-2"></i>Tire Passport</h4>
        <h6>Complete lifecycle history - {{ $tire->serial_number }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.passport.pdf', $tire->id) }}" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf"></i> Download PDF
        </a>
        <a href="{{ route('tire.inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<!-- Tire Information Card -->
<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Tire Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="text-center mb-3">
                    <i class="fas fa-circle-notch fa-4x text-primary"></i>
                    <h5 class="mt-2">{{ $tire->serial_number }}</h5>
                    <span class="badge {{ $tire->status == 'new' ? 'badge-soft-success' : ($tire->status == 'in_use' ? 'badge-soft-primary' : 'badge-soft-warning') }}">
                        {{ ucfirst(str_replace('_', ' ', $tire->status)) }}
                    </span>
                </div>
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-4">
                        <label>Brand:</label>
                        <p><strong>{{ $tire->brand }}</strong></p>
                    </div>
                    <div class="col-md-4">
                        <label>Size:</label>
                        <p><strong>{{ $tire->size }}</strong></p>
                    </div>
                    <div class="col-md-4">
                        <label>Type:</label>
                        <p><strong>{{ $tire->type }}</strong></p>
                    </div>
                    <div class="col-md-4">
                        <label>Purchase Date:</label>
                        <p>{{ $tire->purchase_date->format('d-m-Y') }}</p>
                    </div>
                    <div class="col-md-4">
                        <label>Purchase Price:</label>
                        <p>${{ number_format($tire->purchase_price, 2) }}</p>
                    </div>
                    <div class="col-md-4">
                        <label>Refill Count:</label>
                        <p>{{ $tire->refill_count }}/{{ $tire->max_refills }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lifecycle Timeline -->
<div class="card mt-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Lifecycle Timeline</h5>
    </div>
    <div class="card-body">
        <div class="timeline">
            @foreach($lifecycleHistory as $index => $event)
            <div class="timeline-item">
                <div class="timeline-badge">
                    @if($event['type'] == 'Installation')
                        <i class="fas fa-truck text-success"></i>
                    @elseif($event['type'] == 'Removal')
                        <i class="fas fa-tools text-warning"></i>
                    @elseif($event['type'] == 'Sent for Refilling')
                        <i class="fas fa-sync-alt text-info"></i>
                    @elseif($event['type'] == 'Received from Refilling')
                        <i class="fas fa-check-circle text-success"></i>
                    @elseif($event['type'] == 'Scrapped')
                        <i class="fas fa-trash-alt text-danger"></i>
                    @endif
                </div>
                <div class="timeline-content">
                    <div class="timeline-date">{{ \Carbon\Carbon::parse($event['date'])->format('d-m-Y') }}</div>
                    <h6>{{ $event['type'] }}</h6>
                    <p>{{ $event['details'] }}</p>
                    @if(isset($event['position']) && $event['position'])
                        <small class="text-muted">Position: {{ $event['position'] }}</small>
                    @endif
                    @if(isset($event['mileage']) && $event['mileage'])
                        <small class="text-muted">Mileage: {{ number_format($event['mileage']) }} km</small>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        padding: 20px 0;
    }
    .timeline-item {
        position: relative;
        padding-left: 50px;
        margin-bottom: 30px;
    }
    .timeline-badge {
        position: absolute;
        left: 0;
        top: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f8f9fa;
        border: 2px solid #e5e5e5;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .timeline-content {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
    }
    .timeline-date {
        font-size: 12px;
        color: #7f8c8d;
        margin-bottom: 5px;
    }
</style>
@endsection