<!-- resources/views/tire/vehicles/index.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-truck me-2"></i>Vehicle Management</h4>
        <h6>Manage all vehicles in the fleet</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.vehicles.create') }}" class="btn btn-added">
            <i class="fas fa-plus-circle me-1"></i> Add Vehicle
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Vehicle Number</th>
                        <th>Status</th>
                        <th>Current Tires</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vehicles as $vehicle)
                    <tr>
                        <td>
                            <strong>{{ $vehicle->lorry_number }}</strong>
                        </td>
                        <td>
                            @php
                                $statusColors = [
                                    'active' => 'success',
                                    'inactive' => 'danger',
                                    'maintenance' => 'warning'
                                ];
                                $color = $statusColors[$vehicle->status] ?? 'secondary';
                            @endphp
                            <span class="badge badge-soft-{{ $color }}">
                                {{ ucfirst($vehicle->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-soft-primary">{{ $vehicle->currentTires->count() }} Tires</span>
                        </td>
                        <td>
                            <div class="action-buttons" style="display: inline;">
                                <!-- View Button -->
                                <a href="{{ route('tire.vehicles.show', $vehicle->id) }}" class="action-btn action-btn-view" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- Edit Button -->
                                <a href="{{ route('tire.vehicles.edit', $vehicle->id) }}" class="action-btn action-btn-edit" title="Edit Vehicle">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <!-- Delete Button -->
                                <button type="button" class="action-btn action-btn-delete" title="Delete Vehicle" onclick="deleteVehicle({{ $vehicle->id }})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if(method_exists($vehicles, 'links'))
            <div class="d-flex justify-content-end mt-3">
                {{ $vehicles->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function deleteVehicle(vehicleId) {
        Swal.fire({
            title: 'Delete Vehicle',
            text: "Are you sure you want to delete this vehicle? This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("tire.vehicles.delete", "") }}/' + vehicleId;
                
                let csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);
                
                let methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endpush
@endsection