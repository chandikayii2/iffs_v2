<!-- resources/views/tire/issue/create.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-exchange-alt me-2"></i>Issue Tire</h4>
        <h6>Issue tires to vehicles</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.issue.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>
</div>

<form id="issueForm">
    @csrf
    <div class="card">
        <div class="card-body">
            <!-- Header Section - Only Issue Note No and Issue Date -->
            <div class="row">
                <div class="col-lg-6 col-sm-6 col-12">
                    <div class="form-group">
                        <label>Issue Note No</label>
                        <input type="text" name="issue_note_number" id="issue_note_number"
                            value="{{ $issueNoteNumber }}" class="form-control" readonly>
                    </div>
                </div>

                <div class="col-lg-6 col-sm-6 col-12">
                    <div class="form-group">
                        <label>Issue Date *</label>
                        <input type="date" name="issue_date" id="issue_date" class="form-control" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Add Tires Section -->
            <div class="row">
                <div class="col-lg-12">
                    <h5 class="mb-3">Add Tires to Issue</h5>
                </div>
                
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="form-group">
                        <label>Serial Number *</label>
                        <select class="form-control select2" id="tire_serial" name="tire_serial">
                            <option value="">Select Tire</option>
                            @foreach($tires as $tire)
                                <option value="{{ $tire->id }}">
                                    {{ $tire->serial_number }} ({{ $tire->status }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-2 col-sm-6 col-12">
                    <div class="form-group">
                        <label>Brand</label>
                        <input type="text" id="tire_brand" class="form-control" readonly>
                    </div>
                </div>

                <div class="col-lg-2 col-sm-6 col-12">
                    <div class="form-group">
                        <label>Size</label>
                        <input type="text" id="tire_size" class="form-control" readonly>
                    </div>
                </div>

                <div class="col-lg-2 col-sm-6 col-12">
                    <div class="form-group">
                        <label>Type</label>
                        <input type="text" id="tire_type" class="form-control" readonly>
                    </div>
                </div>

                <div class="col-lg-2 col-sm-6 col-12">
                    <div class="form-group">
                        <label>Consumed Mileage (km)</label>
                        <input type="number" id="consumed_mileage" class="form-control" readonly>
                    </div>
                </div>

                <div class="col-lg-1 col-sm-6 col-12 d-flex align-items-end">
                    <div class="form-group">
                        <button type="button" id="add-tire-btn" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Section with Vehicle and Remark Fields -->
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tire-table">
                            <thead>
                                <tr>
                                    <th>Tire Serial No</th>
                                    <th>Vehicle No</th>
                                    <th>Consumed Mileage (km)</th>
                                    <th>Tire Size</th>
                                    <th>Tire Brand</th>
                                    <th>Remark</th>
                                    <th width="80">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tireTableBody">
                                <!-- Dynamic rows will be added here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12 text-end">
                    <button type="button" id="submit-btn" class="btn btn-success">
                        <i class="fas fa-check-circle me-1"></i> Submit
                    </button>
                    <a href="{{ route('tire.issue.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

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
    .table td {
        vertical-align: middle;
    }
    .vehicle-select, .mileage-input, .item-remark {
        min-width: 120px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('#tire_serial').select2({
        placeholder: "Search tire...",
        allowClear: true,
        width: '100%'
    });

    // Store added tires
    var addedTires = [];
    var vehicles = @json($vehicles);

    // Get tire data when serial number is selected
    $('#tire_serial').change(function() {
        var tireId = $(this).val();
        
        if (tireId) {
            $.ajax({
                url: '{{ route("tire.issue.get-tire-data", "") }}/' + tireId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        $('#tire_brand').val(data.brand);
                        $('#tire_size').val(data.size);
                        $('#tire_type').val(data.type);
                        $('#consumed_mileage').val(data.consumption_mileage);
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Failed to fetch tire data', 'error');
                }
            });
        } else {
            $('#tire_brand, #tire_size, #tire_type, #consumed_mileage').val('');
        }
    });

// Add tire to table
$('#add-tire-btn').click(function() {
    var tireId = $('#tire_serial').val();
    var tireSerial = $('#tire_serial option:selected').text().trim();
    var consumedMileage = $('#consumed_mileage').val();
    var brand = $('#tire_brand').val();
    var size = $('#tire_size').val();
    var type = $('#tire_type').val();

    // Validate
    if (!tireId) {
        Swal.fire('Error', 'Please select a tire', 'error');
        return;
    }

    if (!brand || !size || !type) {
        Swal.fire('Error', 'Please wait for tire data to load', 'error');
        return;
    }

    // Check if tire already added
    if (addedTires.some(item => item.tireId == tireId)) {
        Swal.fire('Error', 'This tire is already added to the list', 'error');
        return;
    }

    // Add to array with default values
    addedTires.push({
        tireId: tireId,
        tireSerial: tireSerial,
        consumedMileage: consumedMileage || 0,
        brand: brand,
        size: size,
        type: type,
        vehicleId: '',
        vehicleNo: '',
        remark: ''
    });

    // Add row to table
    addRowToTable(addedTires.length - 1);

    // Clear fields
    $('#tire_serial').val('').trigger('change');
    $('#tire_brand, #tire_size, #tire_type, #consumed_mileage').val('');
});

function addRowToTable(index) {
    var tire = addedTires[index];
    
    // Build vehicle options
    var vehicleOptions = '<option value="">Select Vehicle</option>';
    vehicles.forEach(function(v) {
        vehicleOptions += `<option value="${v.id}">${v.lorry_number}</option>`;
    });
    
    var row = `
        <tr id="row-${index}">
            <td><strong>${tire.tireSerial}</strong></td>
            <td>
                <select class="form-control form-control-sm vehicle-select" data-index="${index}" style="min-width: 140px;">
                    ${vehicleOptions}
                </select>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm mileage-input" 
                       data-index="${index}" value="${tire.consumedMileage}" min="0" style="width: 120px;" readonly>
            </td>
            <td>${tire.size}</td>
            <td>${tire.brand}</td>
            <td>
                <input type="text" class="form-control form-control-sm item-remark" 
                       data-index="${index}" placeholder="Enter remark..." style="min-width: 150px;">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-tire" data-index="${index}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `;
    $('#tireTableBody').append(row);

    // Initialize Select2 for vehicle dropdown in row
    $(`#row-${index} .vehicle-select`).select2({
        placeholder: "Select Vehicle",
        allowClear: true,
        width: '100%'
    });

    // Handle vehicle selection change
    $(`#row-${index} .vehicle-select`).on('change', function() {
        var idx = $(this).data('index');
        var selectedOption = $(this).find('option:selected');
        addedTires[idx].vehicleId = $(this).val();
        addedTires[idx].vehicleNo = selectedOption.text();
    });

    // Handle remark change
    $(`#row-${index} .item-remark`).on('change', function() {
        var idx = $(this).data('index');
        addedTires[idx].remark = $(this).val();
    });
}

    // Remove tire from table
    $(document).on('click', '.remove-tire', function() {
        var index = $(this).data('index');
        Swal.fire({
            title: 'Remove Tire',
            text: 'Are you sure you want to remove this tire?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                addedTires.splice(index, 1);
                $(`#row-${index}`).remove();
                // Re-index rows
                $('#tireTableBody tr').each(function(idx, row) {
                    $(row).attr('id', `row-${idx}`);
                    $(row).find('.remove-tire').data('index', idx);
                    $(row).find('.vehicle-select').data('index', idx);
                    $(row).find('.mileage-input').data('index', idx);
                    $(row).find('.item-remark').data('index', idx);
                });
                Swal.fire('Removed', 'Tire removed from list', 'success');
            }
        });
    });

    // Submit form
    $('#submit-btn').click(function() {
        if (addedTires.length === 0) {
            Swal.fire('Error', 'Please add at least one tire', 'error');
            return;
        }

        // Validate all tires have vehicle selected
        var missingVehicles = addedTires.filter(t => !t.vehicleId);
        if (missingVehicles.length > 0) {
            Swal.fire('Error', 'Please select a vehicle for all tires', 'error');
            return;
        }

        // Prepare data
        var tireIds = addedTires.map(t => t.tireId);
        var vehicleIds = addedTires.map(t => t.vehicleId);
        var consumedMileages = addedTires.map(t => t.consumedMileage);
        var remarks = addedTires.map(t => t.remark);

        var formData = {
            issue_note_number: $('#issue_note_number').val(),
            issue_date: $('#issue_date').val(),
            tire_ids: tireIds,
            vehicle_ids: vehicleIds,
            consumed_mileages: consumedMileages,
            remarks: remarks,
            _token: '{{ csrf_token() }}'
        };

        Swal.fire({
            title: 'Confirmation',
            text: 'Are you sure you want to issue these tires?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Submit!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("tire.issue.store") }}',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success').then(() => {
                                window.location.href = '{{ route("tire.issue.index") }}';
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = 'Failed to issue tires';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error!', errorMsg, 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
@endsection