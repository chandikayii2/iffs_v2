<!-- resources/views/tire/issue/edit.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-edit me-2"></i>Edit Issue Note</h4>
        <h6>{{ $issueNote->issue_note_number }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.issue.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
        <a href="{{ route('tire.issue.show', $issueNote->id) }}" class="btn btn-info">
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
                            <th>Tire Serial No</th>
                            <th>Vehicle No</th>
                            <th>Consumed Mileage (km)</th>
                            <th>Remark</th>
                            <th width="80">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="editTableBody">
                        @foreach($issueNote->items as $index => $item)
                        <tr id="row-{{ $index }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <input type="hidden" name="item_ids[]" value="{{ $item->id }}">
                                <select name="tire_ids[]" class="form-control form-control-sm tire-select" required>
                                    @foreach($tires as $tire)
                                        <option value="{{ $tire->id }}" {{ $item->tire_id == $tire->id ? 'selected' : '' }}>
                                            {{ $tire->serial_number }} ({{ $tire->status }})
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
                                       value="{{ $item->consumed_mileage }}" min="0" required>
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
                        <i class="fas fa-plus"></i> Add Tire
                    </button>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12 text-end">
                    <button type="button" id="update-btn" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                    <a href="{{ route('tire.issue.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    var rowCount = {{ $issueNote->items->count() }};
    var allTires = @json($tires);
    var allVehicles = @json($vehicles);

    // Initialize Select2
    $('.tire-select, .vehicle-select').select2({
        placeholder: "Select...",
        allowClear: true,
        width: '100%'
    });

    // Add new row
    $('#add-row-btn').click(function() {
        var tireOptions = '<option value="">Select Tire</option>';
        allTires.forEach(function(t) {
            tireOptions += `<option value="${t.id}">${t.serial_number} (${t.status})</option>`;
        });

        var vehicleOptions = '<option value="">Select Vehicle</option>';
        allVehicles.forEach(function(v) {
            vehicleOptions += `<option value="${v.id}">${v.lorry_number}</option>`;
        });

        var row = `
            <tr id="row-${rowCount}">
                <td>${rowCount + 1}</td>
                <td>
                    <select name="tire_ids[]" class="form-control form-control-sm tire-select" required>
                        ${tireOptions}
                    </select>
                </td>
                <td>
                    <select name="vehicle_ids[]" class="form-control form-control-sm vehicle-select" required>
                        ${vehicleOptions}
                    </select>
                </td>
                <td>
                    <input type="number" name="consumed_mileages[]" class="form-control form-control-sm" value="0" min="0" required>
                </td>
                <td>
                    <input type="text" name="remarks[]" class="form-control form-control-sm" placeholder="Enter remark...">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-row" data-index="${rowCount}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#editTableBody').append(row);
        
        // Initialize Select2 for new row
        $(`#row-${rowCount} .tire-select, #row-${rowCount} .vehicle-select`).select2({
            placeholder: "Select...",
            allowClear: true,
            width: '100%'
        });
        
        rowCount++;
    });

    // Remove row
    $(document).on('click', '.remove-row', function() {
        var index = $(this).data('index');
        $(`#row-${index}`).remove();
    });

    // Update
    $('#update-btn').click(function() {
        var formData = $('#editIssueForm').serialize();
        formData += '&_token={{ csrf_token() }}';
        formData += '&_method=PUT';

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
                    url: '{{ route("tire.issue.update", $issueNote->id) }}',
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