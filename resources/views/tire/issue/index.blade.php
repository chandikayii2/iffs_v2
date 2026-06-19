<!-- resources/views/tire/issue/index.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-list me-2"></i>Issue Tire List</h4>
        <h6>Manage all tire issue notes</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.issue.create') }}" class="btn btn-added">
            <i class="fas fa-plus-circle me-1"></i> New Issue
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Issue No</th>
                        <th>Issue Date</th>
                        <th>Tire Serial No</th>
                        <th>Vehicle No</th>
                        <th>Remark</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($issueNotes as $note)
                    <tr>
                        <td>
                            <a href="{{ route('tire.issue.show', $note->id) }}" class="text-primary fw-bold">
                                {{ $note->issue_note_number }}
                            </a>
                        </td>
                        <td>{{ $note->issue_date->format('d-m-Y') }}</td>
                        <td>
                            @php
                                $tireSerials = $note->items->pluck('tire.serial_number')->implode(', ');
                            @endphp
                            @if($tireSerials)
                                <span class="badge badge-soft-info">{{ $tireSerials }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $vehicleNos = $note->items->pluck('vehicle.lorry_number')->implode(', ');
                            @endphp
                            @if($vehicleNos)
                                <span class="badge badge-soft-primary">{{ $vehicleNos }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $remarks = $note->items->pluck('remark')->filter()->implode(', ');
                            @endphp
                            @if($remarks)
                                <span class="badge badge-soft-warning">{{ $remarks }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <!-- View Button -->
                                <a href="{{ route('tire.issue.show', $note->id) }}" class="action-btn action-btn-view" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- Edit Button -->
                                <a href="{{ route('tire.issue.edit', $note->id) }}" class="action-btn action-btn-edit" title="Edit Issue Note">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <!-- PDF Button -->
<a href="{{ route('tire.issue.pdf', $note->id) }}" class="action-btn action-btn-download" title="Download PDF" target="_blank">
    <i class="fas fa-file-pdf"></i>
</a>

<!-- Gate Pass Button -->
<a href="{{ route('tire.issue.gate-pass', $note->id) }}" class="action-btn action-btn-info" title="Gate Pass" target="_blank">
    <i class="fas fa-passport"></i>
</a>
                                
                                <!-- Delete Button -->
                                <button type="button" class="action-btn action-btn-delete" onclick="deleteIssueNote({{ $note->id }})" title="Delete">
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
            {{ $issueNotes->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteIssueNote(id) {
    Swal.fire({
        title: 'Delete Issue Note',
        text: "Are you sure you want to delete this issue note? This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("tire.issue.delete", "") }}/' + id,
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
                    let errorMsg = 'Failed to delete issue note';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', errorMsg, 'error');
                }
            });
        }
    });
}
</script>
@endpush
@endsection