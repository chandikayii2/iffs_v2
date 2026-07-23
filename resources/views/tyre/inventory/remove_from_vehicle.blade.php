<!-- resources/views/tyre/inventory/remove_from_vehicle.blade.php -->
@extends('tyre.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-tools me-2"></i>Remove Tyre from Vehicle</h4>
        <h6>{{ $tyre->serial_number }} - {{ $tyre->brand }} {{ $tyre->size }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tyre.inventory.show', $tyre->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Tyre Details
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
                    <label class="col-lg-4 col-form-label">Current Vehicle:</label>
                    <div class="col-lg-8">
                        <p><strong>{{ $currentVehicle->lorry_number ?? 'Unknown' }}</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <form action="{{ route('tyre.inventory.process-removal', $tyre->id) }}" method="POST">
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
                        <small class="text-muted">The total distance this tyre traveled while on the vehicle</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Removal Reason *</label>
                        <select name="removal_reason" id="removalReasonSelect" class="form-control" required>
                            <option value="">Select Reason</option>
                            <option value="Worn out">Worn out</option>
                            <option value="Damage">Damage</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group" id="customDamageReasonContainer" style="display: none;">
                        <label class="fw-bold">Custom Removal damage Reason <span class="text-danger">*</span></label>
                        <textarea name="custom_removal_damage_reason" id="customDamageReasonInput" class="form-control" placeholder="Type custom damage removal reason..."></textarea>
                    </div>

                    <div class="form-group" id="customReasonContainer" style="display: none;">
                        <label class="fw-bold">Custom Removal Reason <span class="text-danger">*</span></label>
                        <textarea name="custom_removal_reason" id="customReasonInput" class="form-control" placeholder="Type custom removal reason..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Action *</label>
                        <select name="action" class="form-control" required>
                            <option value="">Select Action</option>
                            <option value="store">Can Use As It Is</option>
                            <option value="send_refill">To Be Send to Dag</option>
                            <option value="scrap">Scrap Tyre</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check-circle"></i> Process Removal
                    </button>
                    <a href="{{ route('tyre.inventory.show', $tyre->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#removalReasonSelect').on('change', function() {
            var val = $(this).val();
            
            $('#customDamageReasonContainer').hide();
            $('#customDamageReasonInput').removeAttr('required');
            $('#customReasonContainer').hide();
            $('#customReasonInput').removeAttr('required');
            
            if (val === 'Damage') {
                $('#customDamageReasonContainer').slideDown();
                $('#customDamageReasonInput').attr('required', 'required');
            } else if (val === 'Other') {
                $('#customReasonContainer').slideDown();
                $('#customReasonInput').attr('required', 'required');
            }
        });
    });
</script>
@endpush
@endsection