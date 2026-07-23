<!-- resources/views/tyre/scrap/create.blade.php -->
@extends('tyre.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-trash-alt me-2"></i>Scrap Tyre</h4>
        <h6>{{ $tyre->serial_number }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tyre.inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Inventory
        </a>
    </div>
</div>

<form action="{{ route('tyre.scrap.process', $tyre->id) }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Scrap Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Tyre Serial Number</label>
                        <p><strong>{{ $tyre->serial_number }}</strong></p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Brand</label>
                        <p>{{ $tyre->brand }}</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Size</label>
                        <p>{{ $tyre->size }}</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Current Status</label>
                        <p>
                            @php
                                $badgeClass = 'badge-soft-warning';
                                if($tyre->status == 'new') $badgeClass = 'badge-soft-success';
                                elseif($tyre->status == 'in_use') $badgeClass = 'badge-soft-primary';
                                elseif($tyre->status == 'used') $badgeClass = 'badge-soft-warning';
                                elseif($tyre->status == 'at_vendor') $badgeClass = 'badge-soft-danger';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $tyre->status)) }}</span>
                        </p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Scrap Reason *</label>
                        <select name="scrap_reason" id="scrapReasonSelect" class="form-control" required>
                            <option value="">Select Reason</option>
                            <option value="Damage">Damage</option>
                            <option value="Maximum refills reached">Maximum refills reached</option>
                            <option value="Age (expired)">Age (expired)</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-lg-6" id="customScrapDamageReasonContainer" style="display: none;">
                    <div class="form-group">
                        <label class="fw-bold">Custom Scrap damage Reason <span class="text-danger">*</span></label>
                        <textarea name="custom_scrap_damage_reason" id="customScrapDamageReasonInput" class="form-control" rows="2" placeholder="Type custom scrap damage reason..."></textarea>
                    </div>
                </div>
                
                <div class="col-lg-6" id="customScrapReasonContainer" style="display: none;">
                    <div class="form-group">
                        <label class="fw-bold">Custom Scrap Reason <span class="text-danger">*</span></label>
                        <textarea name="custom_scrap_reason" id="customScrapReasonInput" class="form-control" rows="2" placeholder="Type custom scrap reason..."></textarea>
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
                <a href="{{ route('tyre.inventory.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#scrapReasonSelect').on('change', function() {
            var val = $(this).val();
            
            $('#customScrapDamageReasonContainer').hide();
            $('#customScrapDamageReasonInput').removeAttr('required');
            $('#customScrapReasonContainer').hide();
            $('#customScrapReasonInput').removeAttr('required');
            
            if (val === 'Damage') {
                $('#customScrapDamageReasonContainer').slideDown();
                $('#customScrapDamageReasonInput').attr('required', 'required');
            } else if (val === 'Other') {
                $('#customScrapReasonContainer').slideDown();
                $('#customScrapReasonInput').attr('required', 'required');
            }
        });
    });
</script>
@endpush
@endsection