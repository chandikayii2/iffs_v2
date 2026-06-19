<!-- resources/views/tire/inventory/allocate.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-truck me-2"></i>Allocate Tire to Vehicle</h4>
        <h6>{{ $tire->serial_number }} - {{ $tire->brand }} {{ $tire->size }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Inventory
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
                <table class="table table-borderless">
                    <tr><th width="40%">Serial Number:</th><td><strong>{{ $tire->serial_number }}</strong></td></tr>
                    <tr><th>Brand:</th><td>{{ $tire->brand }}</td></tr>
                    <tr><th>Size:</th><td>{{ $tire->size }}</td></tr>
                    <tr><th>Type:</th><td>{{ $tire->type }}</td></tr>
                    <tr><th>Status:</th><td><span class="badge badge-soft-success">{{ ucfirst($tire->status) }}</span></td></tr>
                    <tr><th>Vendor:</th><td>{{ $tire->vendor ? $tire->vendor->name : 'N/A' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <form action="{{ route('tire.inventory.allocate-to-vehicle.process', $tire->id) }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Vehicle Assignment</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Select Vehicle *</label>
                        <select name="vehicle_id" class="form-control select2" id="vehicleSelect" required>
                            <option value="">Select Vehicle</option>
                            @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">
                                {{ $vehicle->lorry_number }} 
                                @if($vehicle->driver_name) - {{ $vehicle->driver_name }} @endif
                                <!-- ({{ $vehicle->currentTires->count() }} tires) -->
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Position on Vehicle</label>
                        <select name="position" class="form-control">
                            <option value="">Select Position</option>
                            <option value="Front Left">Front Left</option>
                            <option value="Front Right">Front Right</option>
                            <option value="Rear Left">Rear Left</option>
                            <option value="Rear Right">Rear Right</option>
                            <option value="Spare">Spare</option>
                            <option value="Trailer Left">Trailer Left</option>
                            <option value="Trailer Right">Trailer Right</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Remark / Notes</label>
                        <textarea name="remark" class="form-control" rows="3" placeholder="Any remarks about this allocation..."></textarea>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check-circle me-1"></i> Allocate Tire
                    </button>
                    <a href="{{ route('tire.inventory.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#vehicleSelect').select2({
            placeholder: "Search for a vehicle...",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
@endsection