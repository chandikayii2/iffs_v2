<!-- resources/views/tyre/inventory/allocate.blade.php -->
@extends('tyre.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-truck me-2"></i>Allocate Tyre to Vehicle</h4>
        <h6>{{ $tyre->serial_number }} - {{ $tyre->brand }} {{ $tyre->size }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tyre.inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Inventory
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
                <table class="table table-borderless">
                    <tr><th width="40%">Serial Number:</th><td><strong>{{ $tyre->serial_number }}</strong></td></tr>
                    <tr><th>Brand:</th><td>{{ $tyre->brand }}</td></tr>
                    <tr><th>Size:</th><td>{{ $tyre->size }}</td></tr>
                    <tr><th>Type:</th><td>{{ $tyre->type }}</td></tr>
                    <tr><th>Status:</th><td><span class="badge badge-soft-success">{{ ucfirst($tyre->status) }}</span></td></tr>
                    <tr><th>Vendor:</th><td>{{ $tyre->vendor ? $tyre->vendor->name : 'N/A' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <form action="{{ route('tyre.inventory.allocate-to-vehicle.process', $tyre->id) }}" method="POST">
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
                                <!-- ({{ $vehicle->currentTyres->count() }} tyres) -->
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Remark / Notes</label>
                        <textarea name="remark" class="form-control" rows="3" placeholder="Any remarks about this allocation..."></textarea>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check-circle me-1"></i> Allocate Tyre
                    </button>
                    <a href="{{ route('tyre.inventory.index') }}" class="btn btn-secondary">
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