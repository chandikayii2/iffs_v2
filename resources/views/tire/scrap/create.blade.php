<!-- resources/views/tire/scrap/create.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-trash-alt me-2"></i>Scrap Tire</h4>
        <h6>{{ $tire->serial_number }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Inventory
        </a>
    </div>
</div>

<form action="{{ route('tire.scrap.process', $tire->id) }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Scrap Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Tire Serial Number</label>
                        <p><strong>{{ $tire->serial_number }}</strong></p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Brand</label>
                        <p>{{ $tire->brand }}</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Size</label>
                        <p>{{ $tire->size }}</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Current Status</label>
                        <p>
                            @php
                                $badgeClass = 'badge-soft-warning';
                                if($tire->status == 'new') $badgeClass = 'badge-soft-success';
                                elseif($tire->status == 'in_use') $badgeClass = 'badge-soft-primary';
                                elseif($tire->status == 'used') $badgeClass = 'badge-soft-warning';
                                elseif($tire->status == 'at_vendor') $badgeClass = 'badge-soft-danger';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $tire->status)) }}</span>
                        </p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Scrap Reason *</label>
                        <select name="scrap_reason" class="form-control" required>
                            <option value="">Select Reason</option>
                            <option value="Worn out beyond repair">Worn out beyond repair</option>
                            <option value="Sidewall damage">Sidewall damage</option>
                            <option value="Burst">Burst</option>
                            <option value="Maximum refills reached">Maximum refills reached</option>
                            <option value="Age (expired)">Age (expired)</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Final Mileage (if known)</label>
                        <input type="number" name="final_mileage" class="form-control" placeholder="Optional">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Disposal Method</label>
                        <select name="disposal_method" class="form-control">
                            <option value="">Select Method</option>
                            <option value="Recycled">Recycled</option>
                            <option value="Landfill">Landfill</option>
                            <option value="Sold as scrap">Sold as scrap</option>
                            <option value="Retreaded">Retreaded</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>Additional Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash-alt"></i> Confirm Scrap
                </button>
                <a href="{{ route('tire.inventory.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection