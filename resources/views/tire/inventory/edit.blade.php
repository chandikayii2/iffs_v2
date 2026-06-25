<!-- resources/views/tire/inventory/edit.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-edit me-2"></i>Edit Tire</h4>
        <h6>Update tire information - {{ $tire->serial_number }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.inventory.show', $tire->id) }}" class="btn btn-info">
            <i class="fas fa-eye me-1"></i> View Details
        </a>
        <a href="{{ route('tire.inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Edit Tire Information</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('tire.inventory.update', $tire->id) }}" method="POST" id="editTireForm">
            @csrf
            @method('PUT')
            <div class="row">
                <!-- Serial Number - Now Editable -->
                <div class="col-lg-6 col-sm-12">
                    <div class="form-group">
                        <label>Serial Number <span class="text-danger">*</span></label>
                        <input type="text" name="serial_number" id="serial_number" 
                               class="form-control @error('serial_number') is-invalid @enderror" 
                               value="{{ old('serial_number', $tire->serial_number) }}" required>
                        <small class="text-muted">Unique identifier for the tire</small>
                        @error('serial_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Status - Readonly -->
                <div class="col-lg-6 col-sm-12">
                    <div class="form-group">
                        <label>Status</label>
                        <p>
                            @php
                                $badgeClass = 'badge-soft-success';
                                if($tire->status == 'new') $badgeClass = 'badge-soft-success';
                                elseif($tire->status == 'in_use') $badgeClass = 'badge-soft-primary';
                                elseif($tire->status == 'used') $badgeClass = 'badge-soft-warning';
                                elseif($tire->status == 'at_vendor') $badgeClass = 'badge-soft-danger';
                                elseif($tire->status == 'scrap') $badgeClass = 'badge-soft-dark';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $tire->status)) }}</span>
                        </p>
                        <small class="text-muted">Status changes automatically based on actions</small>
                    </div>
                </div>

                <!-- Brand -->
                <div class="col-lg-4 col-sm-12">
                    <div class="form-group">
                        <div class="d-flex">
                            <label>Brand <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addBrandModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <select name="brand" class="form-control select2 @error('brand') is-invalid @enderror" id="brandSelect" required>
                            <option value="">Select or type new brand</option>
                            @foreach($brands ?? [] as $brand)
                                <option value="{{ $brand }}" {{ $tire->brand == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                            @endforeach
                        </select>
                        @error('brand')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Size -->
                <div class="col-lg-4 col-sm-12">
                    <div class="form-group">
                        <div class="d-flex">
                            <label>Size <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSizeModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <select name="size" class="form-control select2 @error('size') is-invalid @enderror" id="sizeSelect" required>
                            <option value="">Select or type new size</option>
                            @foreach($sizes ?? [] as $size)
                                <option value="{{ $size }}" {{ $tire->size == $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                        @error('size')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Type -->
                <div class="col-lg-4 col-sm-12">
                    <div class="form-group">
                        <div class="d-flex">
                            <label>Type <span class="text-danger">*</span></label>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTypeModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <select name="type" class="form-control select2 @error('type') is-invalid @enderror" id="typeSelect" required>
                            <option value="">Select or type new type</option>
                            @foreach($types ?? [] as $type)
                                <option value="{{ $type }}" {{ $tire->type == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Vendor -->
                <div class="col-lg-6 col-sm-12">
                    <div class="form-group">
                        <div class="d-flex">
                            <label>Vendor/Supplier</label>
                            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addVendorModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <select name="vendor_id" class="form-control select2" id="vendorSelect">
                            <option value="">-- Select Vendor --</option>
                            @foreach($vendors ?? [] as $vendor)
                                <option value="{{ $vendor->id }}" {{ $tire->vendor_id == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->name }} - {{ $vendor->contact_person }} ({{ $vendor->phone }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">The vendor/supplier of this tire</small>
                    </div>
                </div>

                <!-- Max Refills -->
                <div class="col-lg-6 col-sm-12">
                    <div class="form-group">
                        <label>Max Refills</label>
                        <input type="number" name="max_refills" id="max_refills" class="form-control" 
                               value="{{ $tire->max_refills }}" min="{{ $tire->refill_count }}" max="10">
                        <small class="text-muted">
                            Current refill count: <strong>{{ $tire->refill_count }}</strong> 
                            (Max refills cannot be less than current refill count)
                        </small>
                        @if($tire->refill_count > 0)
                            <div class="text-warning small">
                                <i class="fas fa-exclamation-triangle"></i> 
                                Minimum allowed: {{ $tire->refill_count }} (already refilled {{ $tire->refill_count }} times)
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Purchase Date - Now Editable -->
                <div class="col-lg-6 col-sm-12">
                    <div class="form-group">
                        <label>Purchase Date <span class="text-danger">*</span></label>
                        <input type="date" name="purchase_date" id="purchase_date" 
                               class="form-control @error('purchase_date') is-invalid @enderror" 
                               value="{{ old('purchase_date', $tire->purchase_date ? $tire->purchase_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                        @error('purchase_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Purchase Price - Now Editable -->
                <div class="col-lg-6 col-sm-12">
                    <div class="form-group">
                        <label>Purchase Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rs</span>
                            <input type="number" step="0.01" name="purchase_price" id="purchase_price" 
                                   class="form-control @error('purchase_price') is-invalid @enderror" 
                                   value="{{ old('purchase_price', $tire->purchase_price) }}" required>
                        </div>
                        @error('purchase_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Consumed Mileage - Now Editable -->
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>Consumed Mileage (km)</label>
                        <input type="number" name="consumption_mileage" id="consumption_mileage" 
                               class="form-control @error('consumption_mileage') is-invalid @enderror" 
                               value="{{ old('consumption_mileage', $tire->consumption_mileage ?? 0) }}" min="0">
                        <small class="text-muted">Total kilometers this tire has traveled</small>
                        @error('consumption_mileage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Notes -->
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ $tire->notes }}</textarea>
                    </div>
                </div>
            </div>
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save me-1"></i> Update Tire
                </button>
                <a href="{{ route('tire.inventory.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Add Brand Modal -->
<div class="modal fade" id="addBrandModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add New Brand</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="newBrand" class="form-control" placeholder="Enter brand name">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addNewOption('brand')">Add Brand</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Size Modal -->
<div class="modal fade" id="addSizeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add New Size</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="newSize" class="form-control" placeholder="Enter size">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addNewOption('size')">Add Size</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Type Modal -->
<div class="modal fade" id="addTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add New Type</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="newType" class="form-control" placeholder="Enter type">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addNewOption('type')">Add Type</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Vendor Modal -->
<div class="modal fade" id="addVendorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add New Vendor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Company Name *</label>
                    <input type="text" id="newVendorName" class="form-control">
                </div>
                <div class="form-group">
                    <label>Contact Person *</label>
                    <input type="text" id="newVendorContact" class="form-control">
                </div>
                <div class="form-group">
                    <label>Phone *</label>
                    <input type="text" id="newVendorPhone" class="form-control">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="newVendorEmail" class="form-control">
                </div>
                <div class="form-group">
                    <label>Address *</label>
                    <textarea id="newVendorAddress" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addNewVendor()">Add Vendor</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #ddd;
        border-radius: 6px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 with proper placeholder
        $('#brandSelect, #sizeSelect, #typeSelect, #vendorSelect').select2({
            tags: true,
            placeholder: "Select or type new",
            allowClear: true,
            width: '100%'
        });

        // Validate max_refills on form submit
        $('#editTireForm').on('submit', function(e) {
            var maxRefills = parseInt($('#max_refills').val());
            var currentRefillCount = parseInt('{{ $tire->refill_count }}');
            
            if (maxRefills < currentRefillCount) {
                e.preventDefault();
                Swal.fire({
                    title: 'Invalid Max Refills',
                    text: 'Max refills cannot be less than current refill count (' + currentRefillCount + '). Please set a value of ' + currentRefillCount + ' or higher.',
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
                $('#max_refills').focus();
                return false;
            }
            return true;
        });

        // Real-time validation on max_refills input
        $('#max_refills').on('change keyup', function() {
            var value = parseInt($(this).val());
            var currentRefillCount = parseInt('{{ $tire->refill_count }}');
            
            if (value < currentRefillCount) {
                $(this).addClass('is-invalid');
                $(this).siblings('small').html('<span class="text-danger">Max refills cannot be less than ' + currentRefillCount + ' (current refill count)</span>');
            } else {
                $(this).removeClass('is-invalid');
                $(this).siblings('small').html('Current refill count: <strong>{{ $tire->refill_count }}</strong> (Max refills cannot be less than current refill count)');
            }
        });
    });
    
    function addNewOption(type) {
        let newValue = '';
        let selectElement = '';
        
        if (type === 'brand') {
            newValue = $('#newBrand').val();
            selectElement = '#brandSelect';
        } else if (type === 'size') {
            newValue = $('#newSize').val();
            selectElement = '#sizeSelect';
        } else if (type === 'type') {
            newValue = $('#newType').val();
            selectElement = '#typeSelect';
        }
        
        if (newValue.trim() === '') {
            Swal.fire('Error', 'Please enter a value', 'error');
            return;
        }
        
        var newOption = new Option(newValue, newValue, true, true);
        $(selectElement).append(newOption).trigger('change');
        
        $(`#add${type.charAt(0).toUpperCase() + type.slice(1)}Modal`).modal('hide');
        $(`#new${type.charAt(0).toUpperCase() + type.slice(1)}`).val('');
        
        Swal.fire('Success', `${type.charAt(0).toUpperCase() + type.slice(1)} added successfully!`, 'success');
    }
    
    function addNewVendor() {
        let vendorData = {
            name: $('#newVendorName').val(),
            contact_person: $('#newVendorContact').val(),
            phone: $('#newVendorPhone').val(),
            email: $('#newVendorEmail').val(),
            address: $('#newVendorAddress').val(),
            _token: '{{ csrf_token() }}'
        };
        
        if (!vendorData.name || !vendorData.contact_person || !vendorData.phone || !vendorData.address) {
            Swal.fire('Error', 'Please fill in all required fields', 'error');
            return;
        }
        
        Swal.fire({
            title: 'Adding Vendor...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: '{{ route("tire.refilling.vendors.store") }}',
            type: 'POST',
            data: vendorData,
            success: function(response) {
                Swal.close();
                if (response.success) {
                    var newOption = new Option(response.vendor_display, response.vendor_id, true, true);
                    $('#vendorSelect').append(newOption).trigger('change');
                    $('#addVendorModal').modal('hide');
                    $('#newVendorName, #newVendorContact, #newVendorPhone, #newVendorEmail, #newVendorAddress').val('');
                    Swal.fire('Success', response.message, 'success');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr) {
                Swal.close();
                let errorMsg = 'Failed to add vendor';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error', errorMsg, 'error');
            }
        });
    }
</script>
@endpush
@endsection