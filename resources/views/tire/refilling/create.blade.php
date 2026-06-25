<!-- resources/views/tire/refilling/create.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-sync-alt me-2"></i>Create Refilling Order</h4>
        <h6>Send tires for refilling/retreading</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.refilling.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Orders
        </a>
    </div>
</div>

<form action="{{ route('tire.refilling.store') }}" method="POST" id="refillingForm">
    @csrf
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Order Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Order Number *</label>
                        <input type="text" name="order_number" class="form-control" value="{{ $orderNumber }}" readonly required>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <div class="d-flex">
                            <label>Vendor *</label>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addVendorModal" style="padding: 0px 10px; margin-left: 5px; margin-bottom: 5px; margin-top: -5px;">
                                <i class="fas fa-plus"></i> 
                            </button>
                        </div>
                        <div class="input-group">
                            <select name="vendor_id" class="form-control select2" id="vendorSelect" required>
                                <option value="">Select Vendor</option>
                                @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }} - {{ $vendor->contact_person }} ({{ $vendor->phone }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Sent Date *</label>
                        <input type="date" name="sent_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>Select Tires *</label>
                        <select name="tire_ids[]" class="form-control select2" id="tireSelect" multiple required>
                            @foreach($availableTires as $tire)
                            <option value="{{ $tire->id }}" {{ isset($selectedTireId) && $selectedTireId == $tire->id ? 'selected' : '' }}>
                                {{ $tire->serial_number }} - {{ $tire->brand }} ({{ $tire->size }}) - {{ ucfirst($tire->status) }}
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">You can select multiple tires for this order</small>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Additional notes about this refilling order..."></textarea>
                    </div>
                </div>
            </div>
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Order
                </button>
                <a href="{{ route('tire.refilling.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </div>
    </div>
</form>

<!-- Add Vendor Modal -->
<div class="modal fade" id="addVendorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-building me-2"></i>Add New Vendor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addVendorForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Company Name *</label>
                        <input type="text" name="name" id="vendorName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Contact Person *</label>
                        <input type="text" name="contact_person" id="vendorContactPerson" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="text" name="phone" id="vendorPhone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" id="vendorEmail" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Address *</label>
                        <textarea name="address" id="vendorAddress" class="form-control" rows="2" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveVendorBtn">
                        <i class="fas fa-save"></i> Save Vendor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2 for Vendor dropdown with search
        $('#vendorSelect').select2({
            placeholder: "Search for a vendor...",
            allowClear: true,
            width: '100%'
        });
        
        // Initialize Select2 for Tires dropdown with search (multiple)
        $('#tireSelect').select2({
            placeholder: "Search and select tires...",
            allowClear: true,
            width: '100%',
            closeOnSelect: false
        });
        
        // Handle Add Vendor Form submission via AJAX
        $('#addVendorForm').on('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            let formData = {
                name: $('#vendorName').val(),
                contact_person: $('#vendorContactPerson').val(),
                phone: $('#vendorPhone').val(),
                email: $('#vendorEmail').val(),
                address: $('#vendorAddress').val(),
                _token: '{{ csrf_token() }}'
            };
            
            // Validate
            if (!formData.name || !formData.contact_person || !formData.phone || !formData.address) {
                Swal.fire('Error', 'Please fill in all required fields', 'error');
                return;
            }
            
            // Show loading state
            $('#saveVendorBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
            
            // Send AJAX request
            $.ajax({
                url: '{{ route("tire.refilling.vendors.store") }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // Close modal
                        $('#addVendorModal').modal('hide');
                        
                        // Reset form
                        $('#addVendorForm')[0].reset();
                        
                        // Show success message
                        Swal.fire('Success!', response.message, 'success');
                        
                        // Add new option to select dropdown
                        let newOption = new Option(response.vendor_display, response.vendor_id, true, true);
                        $('#vendorSelect').append(newOption).trigger('change');
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                    
                    // Enable button
                    $('#saveVendorBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Save Vendor');
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to add vendor';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', errorMsg, 'error');
                    $('#saveVendorBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Save Vendor');
                }
            });
        });
        
        // Reset modal form when closed
        $('#addVendorModal').on('hidden.bs.modal', function() {
            $('#addVendorForm')[0].reset();
            $('#saveVendorBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Save Vendor');
        });
    });
</script>
@endpush
@endsection