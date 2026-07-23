<!-- resources/views/tyre/inventory/create.blade.php -->
@extends('tyre.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-plus-circle me-2"></i>Add New Tyre</h4>
        <h6>Register a new tyre in the inventory system</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tyre.inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Tyre Information Form</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('tyre.inventory.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <!-- Serial Number & Brand, Size, Type Row -->
                <!-- Multi-Serial Number Textarea -->
                <div class="col-md-6 mb-3">
        <div class="form-group">
            <label class="form-label fw-bold">Serial Number(s) <span class="text-danger">*</span></label>
            <textarea name="serial_number" id="serialNumberInput" class="form-control @error('serial_number') is-invalid @enderror" 
                   placeholder="Enter serial numbers separated by commas or press Enter for a new line" rows="2" required>{{ old('serial_number') }}</textarea>
            <small class="text-muted">Unique identifier(s). For multiple tyres, separate them with commas or write one per line.</small>
            @error('serial_number')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <!-- Brand -->
    <div class="col-md-2 mb-3">
        <div class="form-group">
            <div class="d-flex">
                <label class="form-label fw-bold">Brand <span class="text-danger">*</span></label>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addBrandModal" title="Add New Brand" style="margin-left: 5px; padding: 0 4px;">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <select name="brand" class="form-control select2 @error('brand') is-invalid @enderror" id="brandSelect" required>
                <option value="">-- Select brand --</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand }}" {{ old('brand') == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                @endforeach
            </select>
            @error('brand')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <!-- Size -->
    <div class="col-md-2 mb-3">
        <div class="form-group">
            <div class="d-flex">
                <label class="form-label fw-bold">Size <span class="text-danger">*</span></label>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSizeModal" title="Add New Size" style="margin-left: 5px; padding: 0 4px;">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <select name="size" class="form-control select2 @error('size') is-invalid @enderror" id="sizeSelect" required>
                <option value="">-- Select size --</option>
                @foreach($sizes as $size)
                    <option value="{{ $size }}" {{ old('size') == $size ? 'selected' : '' }}>{{ $size }}</option>
                @endforeach
            </select>
            @error('size')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>
    
    <!-- Type -->
    <div class="col-md-2 mb-3">
        <div class="form-group">
            <div class="d-flex">
                <label class="form-label fw-bold">Type <span class="text-danger">*</span></label>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTypeModal" title="Add New Type" style="margin-left: 5px; padding: 0 4px;">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <select name="type" class="form-control select2 @error('type') is-invalid @enderror" id="typeSelect" required>
                <option value="">-- Select type --</option>
                @foreach($types as $type)
                    <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
            @error('type')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<!-- Tyre Type / Checkboxes -->
<div class="row">
    <div class="col-12 mb-4">
        <label class="form-label fw-bold">Tyre Configuration <span class="text-danger">*</span></label>
        <div class="d-flex gap-4 flex-wrap">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tyre_config" id="chkb1" value="original" checked>
                <label class="form-check-label" for="chkb1">
                    <strong>Original Tyre</strong> <span class="text-muted">(Brand new, never refilled)</span>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tyre_config" id="chkb2" value="original_casing">
                <label class="form-check-label" for="chkb2">
                    <strong>Use Original Casing</strong> <span class="text-muted">(Tyre using original casing)</span>
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="tyre_config" id="chkb3" value="dag_used">
                <label class="form-check-label" for="chkb3">
                    <strong>Dag Used</strong> <span class="text-muted">(Refilled tyre with history)</span>
                </label>
            </div>
        </div>
    </div>
</div>

<!-- Dag Used Fields (Conditionally Shown) -->
<div id="dagUsedFields" style="display: none;" class="border p-3 rounded mb-4 bg-light">
    <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-history me-2"></i>Dag Used History Information</h6>
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="form-group">
                <label class="form-label fw-bold">Total Refill Count</label>
                <input type="number" name="total_refill_count" id="total_refill_count" class="form-control" value="0" min="0">
                <small class="text-muted">Total number of refills done</small>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="form-group">
                <label class="form-label fw-bold">Rounds Finished</label>
                <input type="number" name="rounds_finished" id="rounds_finished" class="form-control" value="0" min="0">
                <small class="text-muted">How many rounds completed</small>
            </div>
        </div>
    </div>
    
    <div class="row" id="roundKmsInputsContainer">
        <!-- Dynamic R_ KM Consumed fields will be appended here by JS -->
    </div>
</div>

                <!-- Row 1: Vendor and Max Refills -->
                <div class="row">
                    <!-- Vendor Selection -->
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <div class="d-flex">
                                <label class="form-label fw-bold">Vendor/Supplier</label>
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addVendorModal" title="Add New Vendor" style="margin-left: 5px;">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <select name="vendor_id" class="form-control select2" id="vendorSelect">
                                <option value="">-- Select Vendor --</option>
                                @foreach($vendors ?? [] as $vendor)
                                    <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->name }} - {{ $vendor->contact_person }} ({{ $vendor->phone }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">The vendor/supplier of this tyre</small>
                        </div>
                    </div>

                    <!-- Max Refills -->
                    <div class="col-md-6 mb-3" id="maxRefillsContainer">
                        <div class="form-group">
                            <label class="form-label fw-bold">Max Refills</label>
                            <input type="number" name="max_refills" id="max_refills" class="form-control" value="3" min="0" max="10">
                            <small class="text-muted">Maximum number of times tyre can be refilled</small>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Purchase Date and Price -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-bold">Purchase Date <span class="text-danger">*</span></label>
                            <input type="date" name="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" 
                                   value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                            @error('purchase_date')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="form-group">
                            <label class="form-label fw-bold">Purchase Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rs</span>
                                <input type="number" step="0.01" name="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror" 
                                       placeholder="0.00" value="{{ old('purchase_price') }}" required>
                            </div>
                            @error('purchase_price')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Notes Row -->
                <div class="col-12 mb-3">
                    <div class="form-group">
                        <label class="form-label fw-bold">Additional Notes</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Enter any additional information about the tyre...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="text-end">
                <button type="reset" class="btn btn-secondary me-2">
                    <i class="fas fa-undo me-1"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save Tyre(s)
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Brand Modal -->
<div class="modal fade" id="addBrandModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-tag me-2"></i>Add New Brand</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Brand Name</label>
                    <input type="text" id="newBrand" class="form-control" placeholder="Enter brand name" autocomplete="off">
                </div>
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
                <h5 class="modal-title"><i class="fas fa-ruler me-2"></i>Add New Size</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Size Format</label>
                    <input type="text" id="newSize" class="form-control" placeholder="e.g., 205/55R16, 225/45R17" autocomplete="off">
                    <small class="text-muted">Enter tyre size in standard format</small>
                </div>
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
                <h5 class="modal-title"><i class="fas fa-layer-group me-2"></i>Add New Type</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Type Name</label>
                    <input type="text" id="newType" class="form-control" placeholder="e.g., Radial, Bias, Tubeless" autocomplete="off">
                </div>
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
                <h5 class="modal-title"><i class="fas fa-building me-2"></i>Add New Vendor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Company Name *</label>
                    <input type="text" id="newVendorName" class="form-control" placeholder="Enter company name">
                </div>
                <div class="form-group">
                    <label>Contact Person *</label>
                    <input type="text" id="newVendorContact" class="form-control" placeholder="Enter contact person name">
                </div>
                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="text" id="newVendorPhone" class="form-control" placeholder="Enter phone number">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="newVendorEmail" class="form-control" placeholder="Enter email address">
                </div>
                <div class="form-group">
                    <label>Address *</label>
                    <textarea id="newVendorAddress" class="form-control" rows="2" placeholder="Enter address"></textarea>
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
        $('#brandSelect, #sizeSelect, #typeSelect, #vendorSelect').select2({
            tags: true,
            placeholder: "Select or type new",
            allowClear: true,
            width: '100%'
        });

        // Dynamic Rounds Finished handler to show/hide corresponding R_ KM inputs
        function updateRoundKmsVisibility() {
            var rounds = parseInt($('#rounds_finished').val()) || 0;
            if (rounds < 0) rounds = 0;
            
            var container = $('#roundKmsInputsContainer');
            // Get current inputs values so we don't lose typed data
            var currentValues = {};
            container.find('input').each(function() {
                var name = $(this).attr('name');
                currentValues[name] = $(this).val();
            });
            
            container.empty();
            for (var i = 1; i <= rounds; i++) {
                var name = 'round_' + i + '_km';
                var val = currentValues[name] !== undefined ? currentValues[name] : '0';
                
                var html = `
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold small text-nowrap">R${i} KM Consumed</label>
                        <input type="number" name="${name}" class="form-control" value="${val}" min="0">
                    </div>
                `;
                container.append(html);
            }
        }

        $('#rounds_finished').on('input change', function() {
            updateRoundKmsVisibility();
        });

        // Synchronize Total Refill Count and Max Refills in Dag Used config
        $('#total_refill_count').on('input change', function() {
            if ($('input[name="tyre_config"]:checked').val() === 'dag_used') {
                $('#max_refills').val($(this).val());
            }
        });
        $('#max_refills').on('input change', function() {
            if ($('input[name="tyre_config"]:checked').val() === 'dag_used') {
                $('#total_refill_count').val($(this).val());
            }
        });

        // Toggle Tyre Config / Dag Used Fields
        $('input[name="tyre_config"]').on('change', function() {
            if ($(this).val() === 'dag_used') {
                $('#dagUsedFields').slideDown();
                $('#total_refill_count, #rounds_finished').attr('required', 'required');
                
                // Hide Max Refills container
                $('#maxRefillsContainer').hide();
                
                // Synchronize values on toggle
                $('#max_refills').val($('#total_refill_count').val());
                updateRoundKmsVisibility();
            } else {
                $('#dagUsedFields').slideUp();
                $('#total_refill_count, #rounds_finished').removeAttr('required');
                
                // Show Max Refills container
                $('#maxRefillsContainer').show();
            }
        });

        // Run on initial load to match old inputs
        if ($('input[name="tyre_config"]:checked').val() === 'dag_used') {
            $('#dagUsedFields').show();
            $('#maxRefillsContainer').hide();
            $('#max_refills').val($('#total_refill_count').val());
            updateRoundKmsVisibility();
        } else {
            $('#maxRefillsContainer').show();
        }
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
        
        $.ajax({
            url: '{{ route("tyre.refilling.vendors.store") }}',
            type: 'POST',
            data: vendorData,
            success: function(response) {
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