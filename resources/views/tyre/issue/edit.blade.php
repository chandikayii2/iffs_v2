<!-- resources/views/tyre/issue/edit.blade.php -->
@extends('tyre.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-edit me-2"></i>Edit Issue Note</h4>
        <h6>{{ $issueNote->issue_note_number }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tyre.issue.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
        <a href="{{ route('tyre.issue.show', $issueNote->id) }}" class="btn btn-info">
            <i class="fas fa-eye me-1"></i> View Details
        </a>
    </div>
</div>

<form id="editIssueForm">
    @csrf
    @method('PUT')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Issue Note No</label>
                        <input type="text" class="form-control" value="{{ $issueNote->issue_note_number }}" readonly>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Issue Date *</label>
                        <input type="date" name="issue_date" class="form-control" 
                               value="{{ $issueNote->issue_date->format('Y-m-d') }}" required>
                    </div>
                </div>
            </div>

            <hr>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tyre Serial No</th>
                            <th>Vehicle No</th>
                            <th>Consumed Mileage (km)</th>
                            <th>Remark</th>
                            <th width="80">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="editTableBody">
                        @foreach($issueNote->items as $index => $item)
                        <tr id="row-{{ $index }}">
                            <td class="row-number">{{ $index + 1 }}</td>
                            <td>
                                <input type="hidden" name="item_ids[]" value="{{ $item->id }}">
                                <select name="tyre_ids[]" class="form-control form-control-sm tyre-select" required>
                                    <option value="">Select Tyre</option>
                                    @foreach($tyres as $tyre)
                                        <option value="{{ $tyre->id }}" {{ $item->tire_id == $tyre->id ? 'selected' : '' }}>
                                            {{ $tyre->serial_number }} ({{ $tyre->status }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="vehicle_ids[]" class="form-control form-control-sm vehicle-select" required>
                                    <option value="">Select Vehicle</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" {{ $item->vehicle_id == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->lorry_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" name="consumed_mileages[]" class="form-control form-control-sm" 
                                       value="{{ $item->consumed_mileage }}" min="0" readonly>
                            </td>
                            <td>
                                <input type="text" name="remarks[]" class="form-control form-control-sm" 
                                       value="{{ $item->remark }}" placeholder="Enter remark...">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-row" data-index="{{ $index }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row mt-3">
                <div class="col-lg-12">
                    <button type="button" id="add-row-btn" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Tyre
                    </button>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12 text-end">
                    <button type="button" id="update-btn" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                    <a href="{{ route('tyre.issue.index') }}" class="btn btn-secondary">
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
    input[readonly] {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Get initial row count
    var rowCount = $('#editTableBody tr').length;
    
    var allTyres = @json($tyres);
    var allVehicles = @json($vehicles);

    // Initialize Select2 for existing rows
    $('.tyre-select, .vehicle-select').select2({
        placeholder: "Select...",
        allowClear: true,
        width: '100%'
    });

    // Add new row
    $('#add-row-btn').click(function() {
        // Only show available tyres (new, used - not in_use)
        var tyreOptions = '<option value="">Select Tyre</option>';
        allTyres.forEach(function(t) {
            // Exclude tyres that are 'in_use'
            if (t.status !== 'in_use') {
                tyreOptions += `<option value="${t.id}">${t.serial_number} (${t.status})</option>`;
            }
        });

        var vehicleOptions = '<option value="">Select Vehicle</option>';
        allVehicles.forEach(function(v) {
            vehicleOptions += `<option value="${v.id}">${v.lorry_number}</option>`;
        });

        var newIndex = rowCount;
        var row = `
            <tr id="row-${newIndex}">
                <td class="row-number">${newIndex + 1}</td>
                <td>
                    <select name="tyre_ids[]" class="form-control form-control-sm tyre-select" required>
                        ${tyreOptions}
                    </select>
                </td>
                <td>
                    <select name="vehicle_ids[]" class="form-control form-control-sm vehicle-select" required>
                        ${vehicleOptions}
                    </select>
                </td>
                <td>
                    <input type="number" name="consumed_mileages[]" class="form-control form-control-sm" value="0" min="0" readonly>
                </td>
                <td>
                    <input type="text" name="remarks[]" class="form-control form-control-sm" placeholder="Enter remark...">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row" data-index="${newIndex}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#editTableBody').append(row);
        
        // Initialize Select2 for new row
        $(`#row-${newIndex} .tyre-select, #row-${newIndex} .vehicle-select`).select2({
            placeholder: "Select...",
            allowClear: true,
            width: '100%'
        });
        
        // Update row numbers
        updateRowNumbers();
        rowCount++;
    });

    // Remove row
    $(document).on('click', '.remove-row', function() {
        var index = $(this).data('index');
        var row = $(`#row-${index}`);
        
        // Check if this row has an item_id (existing record)
        var hasItemId = row.find('input[name="item_ids[]"]').length > 0 && row.find('input[name="item_ids[]"]').val() != '';
        
        Swal.fire({
            title: 'Remove Tyre',
            text: hasItemId ? 'This tyre will be removed from the issue note. Are you sure?' : 'Are you sure you want to remove this tyre?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $(`#row-${index}`).remove();
                updateRowNumbers();
                Swal.fire('Removed', 'Tyre removed from list', 'success');
            }
        });
    });

    // Update row numbers
    function updateRowNumbers() {
        $('#editTableBody tr').each(function(idx, row) {
            $(row).find('.row-number').text(idx + 1);
            $(row).attr('id', `row-${idx}`);
            $(row).find('.remove-row').data('index', idx);
        });
    }

    // Update
    $('#update-btn').click(function() {
        // Collect all form data
        var formData = $('#editIssueForm').serialize();
        formData += '&_token={{ csrf_token() }}';
        formData += '&_method=PUT';

        // Validate: Check if all rows have vehicle selected
        var hasMissingVehicle = false;
        $('#editTableBody select[name="vehicle_ids[]"]').each(function() {
            if (!$(this).val()) {
                hasMissingVehicle = true;
            }
        });

        if (hasMissingVehicle) {
            Swal.fire('Error', 'Please select a vehicle for all tyres', 'error');
            return;
        }

        // Validate: Check if all rows have tyre selected
        var hasMissingTyre = false;
        $('#editTableBody select[name="tyre_ids[]"]').each(function() {
            if (!$(this).val()) {
                hasMissingTyre = true;
            }
        });

        if (hasMissingTyre) {
            Swal.fire('Error', 'Please select a tyre for all rows', 'error');
            return;
        }

        Swal.fire({
            title: 'Confirmation',
            text: 'Are you sure you want to update this issue note?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Update!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("tyre.issue.update", $issueNote->id) }}',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success').then(() => {
                                window.location.href = '{{ route("tyre.issue.index") }}';
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = 'Failed to update issue note';
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