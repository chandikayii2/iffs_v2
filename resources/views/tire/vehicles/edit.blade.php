<!-- resources/views/tire/vehicles/edit.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-edit me-2"></i>Edit Vehicle</h4>
        <h6>Update vehicle information - {{ $vehicle->lorry_number }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.vehicles.show', $vehicle->id) }}" class="btn btn-secondary">
            <i class="fas fa-eye me-1"></i> View Details
        </a>
        <a href="{{ route('tire.vehicles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Edit Vehicle Information</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('tire.vehicles.update', $vehicle->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>Vehicle Number *</label>
                        <input type="text" name="lorry_number" class="form-control @error('lorry_number') is-invalid @enderror" 
                               value="{{ old('lorry_number', $vehicle->lorry_number) }}" required>
                        @error('lorry_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status', $vehicle->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $vehicle->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="maintenance" {{ old('status', $vehicle->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Update Vehicle
                </button>
                <a href="{{ route('tire.vehicles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection