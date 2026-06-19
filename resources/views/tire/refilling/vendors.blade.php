<!-- resources/views/tire/refilling/vendors.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-building me-2"></i>Manage Vendors</h4>
        <h6>Manage refilling/retreading vendors</h6>
    </div>
    <div class="page-btn">
        <button type="button" class="btn btn-added" data-bs-toggle="modal" data-bs-target="#addVendorModal">
            <i class="fas fa-plus-circle"></i> Add Vendor
        </button>
       
    </div>
</div>

<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Vendors List</h5>
    </div>
    <div class="card-body">
        @if($vendors->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Company Name</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Orders</th>
                        <th width="130">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vendors as $vendor)
                    <tr>
                        <td>{{ $vendor->id }}</td>
                        <td><strong>{{ $vendor->name }}</strong></td>
                        <td>{{ $vendor->contact_person }}</td>
                        <td>{{ $vendor->phone }}</td>
                        <td>{{ $vendor->email ?? 'N/A' }}</td>
                        <td>{{ Str::limit($vendor->address, 40) }}</td>
                        <td>
                            <span class="badge badge-soft-info">{{ $vendor->refilling_orders_count }} orders</span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <!-- Show Button -->
                                <a href="{{ route('tire.refilling.vendors.show', $vendor->id) }}" class="action-btn action-btn-view" title="View Vendor Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- Edit Button -->
                                <a href="{{ route('tire.refilling.vendors.edit', $vendor->id) }}" class="action-btn action-btn-edit" title="Edit Vendor">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <!-- Delete Button -->
                                <button type="button" class="action-btn action-btn-delete" onclick="deleteVendor({{ $vendor->id }})" title="Delete Vendor">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $vendors->links() }}
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-building fa-4x text-muted mb-3"></i>
            <h5>No Vendors Found</h5>
            <p class="text-muted">Click "Add Vendor" to create your first refilling vendor.</p>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVendorModal">
                <i class="fas fa-plus-circle"></i> Add Your First Vendor
            </button>
        </div>
        @endif
    </div>
</div>

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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Edit Vendor - Now uses named route
    function editVendor(vendorId) {
        window.location.href = '{{ url("tire/refilling/vendors") }}/' + vendorId + '/edit';
    }
    
    // Delete Vendor
    function deleteVendor(vendorId) {
        Swal.fire({
            title: 'Delete Vendor',
            text: "Are you sure you want to delete this vendor? This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("tire/refilling/vendors") }}/' + vendorId + '/delete',
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Deleted!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Failed to delete vendor';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error!', errorMsg, 'error');
                    }
                });
            }
        });
    }
    
    // Add Vendor Form Submission
    $('#addVendorForm').on('submit', function(e) {
        e.preventDefault();
        
        let formData = {
            name: $('#vendorName').val(),
            contact_person: $('#vendorContactPerson').val(),
            phone: $('#vendorPhone').val(),
            email: $('#vendorEmail').val(),
            address: $('#vendorAddress').val(),
            _token: '{{ csrf_token() }}'
        };
        
        if (!formData.name || !formData.contact_person || !formData.phone || !formData.address) {
            Swal.fire('Error', 'Please fill in all required fields', 'error');
            return;
        }
        
        $('#saveVendorBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: '{{ route("tire.refilling.vendors.store") }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#addVendorModal').modal('hide');
                    $('#addVendorForm')[0].reset();
                    Swal.fire('Success!', response.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
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
</script>
@endpush
@endsection