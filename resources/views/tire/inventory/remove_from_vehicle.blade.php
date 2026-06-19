<!-- resources/views/tire/inventory/remove_from_vehicle.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-tools me-2"></i>Remove Tire from Vehicle</h4>
        <h6>{{ $tire->serial_number }} - {{ $tire->brand }} {{ $tire->size }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.inventory.show', $tire->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Tire Details
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
                    <label class="col-lg-4 col-form-label">Current Vehicle:</label>
                    <div class="col-lg-8">
                        <p><strong>{{ $currentVehicle->lorry_number ?? 'Unknown' }}</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <form action="{{ route('tire.inventory.process-removal', $tire->id) }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Removal Information</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Consumed Mileage (km) *</label>
                        <input type="number" name="consumed_mileage" class="form-control" 
                               placeholder="Enter kilometers consumed during this allocation" required min="0">
                        <small class="text-muted">The total distance this tire traveled while on the vehicle</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Removal Reason *</label>
                        <select name="removal_reason" class="form-control" required>
                            <option value="">Select Reason</option>
                            <option value="Worn out">Worn out</option>
                            <option value="Damage">Damage</option>
                            <option value="Puncture">Puncture</option>
                            <option value="Regular replacement">Regular replacement</option>
                            <option value="Sidewall damage">Sidewall damage</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Action *</label>
                        <select name="action" class="form-control" required>
                            <option value="">Select Action</option>
                            <option value="store">Store in Inventory (Used)</option>
                            <option value="send_refill">Send for Refilling</option>
                            <option value="scrap">Scrap Tire</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check-circle"></i> Process Removal
                    </button>
                    <a href="{{ route('tire.inventory.show', $tire->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection