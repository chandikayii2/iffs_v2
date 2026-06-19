<!-- resources/views/tire/vehicles/remove.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-trash-alt me-2"></i>Remove Tire from Vehicle</h4>
        <h6>{{ $allocation->tire->serial_number }} from {{ $allocation->vehicle->lorry_number }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.vehicles.show', $allocation->vehicle_id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Vehicle
        </a>
    </div>
</div>

<form action="{{ route('tire.vehicles.remove.process', $allocation->id) }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Removal Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Tire Serial Number</label>
                        <p><strong>{{ $allocation->tire->serial_number }}</strong></p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Vehicle</label>
                        <p><strong>{{ $allocation->vehicle->lorry_number }}</strong></p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Current Mileage *</label>
                        <input type="number" name="mileage" class="form-control" value="{{ $allocation->vehicle->current_mileage }}" required>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Removal Reason *</label>
                        <select name="removal_reason" class="form-control" required>
                            <option value="">Select Reason</option>
                            <option value="Worn out">Worn out</option>
                            <option value="Damage">Damage</option>
                            <option value="Puncture">Puncture</option>
                            <option value="Regular replacement">Regular replacement</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>Action *</label>
                        <select name="action" class="form-control" required>
                            <option value="">Select Action</option>
                            <option value="store">Store in Inventory</option>
                            <option value="send_refill">Send for Refilling</option>
                            <option value="scrap">Scrap Tire</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary">Process Removal</button>
                <a href="{{ route('tire.vehicles.show', $allocation->vehicle_id) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection