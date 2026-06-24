<!-- resources/views/tire/inventory/show.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-info-circle me-2"></i>Tire Details</h4>
        <h6>Complete tire information and history - {{ $tire->serial_number }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
        <a href="{{ route('tire.inventory.edit', $tire->id) }}" class="btn btn-primary">
            <i class="fas fa-edit me-1"></i> Edit Tire
        </a>
        <a href="{{ route('tire.inventory.gate-pass', $tire->id) }}" class="btn btn-info" target="_blank">
            <i class="fas fa-passport me-1"></i> Gate Pass
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Tire Information</h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Serial Number:</label>
                    <div class="col-lg-8">
                        <p class="form-control-static"><strong>{{ $tire->serial_number }}</strong></p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Brand:</label>
                    <div class="col-lg-8">
                        <p>{{ $tire->brand }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Size:</label>
                    <div class="col-lg-8">
                        <p>{{ $tire->size }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Type:</label>
                    <div class="col-lg-8">
                        <p>{{ $tire->type }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Vendor/Supplier:</label>
                    <div class="col-lg-8">
                        @if($tire->vendor)
                            <p>
                                <strong>{{ $tire->vendor->name }}</strong><br>
                                Contact: {{ $tire->vendor->contact_person }}<br>
                                Phone: {{ $tire->vendor->phone }}
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
                            if($tire->status == 'new') $badgeClass = 'badge-soft-success';
                            elseif($tire->status == 'in_use') $badgeClass = 'badge-soft-primary';
                            elseif($tire->status == 'used') $badgeClass = 'badge-soft-warning';
                            elseif($tire->status == 'at_vendor') $badgeClass = 'badge-soft-danger';
                            elseif($tire->status == 'scrap') $badgeClass = 'badge-soft-dark';
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $tire->status)) }}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Refill Count:</label>
                    <div class="col-lg-8">
                        <p>
                            <span class="badge {{ $tire->refill_count >= $tire->max_refills ? 'badge-soft-danger' : 'badge-soft-info' }}">
                                {{ $tire->refill_count }} / {{ $tire->max_refills }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Consumed Mileage:</label>
                    <div class="col-lg-8">
                        <p><strong>{{ number_format($totalConsumedMileage ?? $tire->consumption_mileage) }} km</strong></p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Purchase Date:</label>
                    <div class="col-lg-8">
                        <p>{{ $tire->purchase_date->format('d-m-Y') }}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-4 col-form-label">Purchase Price:</label>
                    <div class="col-lg-8">
                        <p>Rs.{{ number_format($tire->purchase_price, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Tire Lifecycle History</h5>
        @if($tire->status == 'in_use')
            <a href="{{ route('tire.inventory.remove-from-vehicle', $tire->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-tools"></i> Remove from Vehicle
            </a>
        @endif
    </div>
    <div class="card-body">
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
                        <td>{{ $history['type'] }}</td>
                        <td>{{ $history['details'] }}</td>
                        <td>
                            @if(isset($history['mileage']))
                                {{ number_format($history['mileage']) }} km
                            @elseif(isset($history['position']))
                                Position: {{ $history['position'] }}
                            @elseif(isset($history['cost']))
                                ${{ number_format($history['cost'], 2) }}
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

<div class="card mt-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Notes</h5>
    </div>
    <div class="card-body">
        <p>{{ $tire->notes ?? 'No additional notes' }}</p>
    </div>
</div>
@endsection