<!-- resources/views/tyre/vehicles/index.blade.php -->
@extends('tyre.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-truck me-2"></i>Vehicle Management</h4>
        <h6>Manage all vehicles in the fleet</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tyre.vehicles.create') }}" class="btn btn-added">
            <i class="fas fa-plus-circle me-1"></i> Add Vehicle
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Vehicle Number</th>
                        <th>Status</th>
                        <th>Current Tyres</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vehicles as $vehicle)
                    <tr data-bs-toggle="collapse" data-bs-target="#collapse-{{ $vehicle->id }}" style="cursor: pointer;">
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
                            <span class="badge badge-soft-primary">{{ $vehicle->currentTyres->count() }} Tyres</span>
                        </td>
                        <td onclick="event.stopPropagation()">
                            <div class="action-buttons" style="display: inline;">
                                <!-- View Button -->
                                <a href="{{ route('tyre.vehicles.show', $vehicle->id) }}" class="action-btn action-btn-view" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- Edit Button -->
                                <a href="{{ route('tyre.vehicles.edit', $vehicle->id) }}" class="action-btn action-btn-edit" title="Edit Vehicle">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <!-- Delete Button -->
                                <button type="button" class="action-btn action-btn-delete" title="Delete Vehicle" onclick="deleteVehicle({{ $vehicle->id }})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Expandable Details Row -->
                    <tr id="collapse-{{ $vehicle->id }}" class="collapse bg-light">
                        <td colspan="4" class="p-3">
                            <div class="p-3 bg-white border rounded">
                                <div class="tree-root mb-3 d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                    <h5 class="fw-bold mb-0 text-dark">Vehicle: {{ $vehicle->lorry_number }}</h5>
                                </div>
                                
                                <div class="tree-container">
                                    
                                    <!-- Current Tyres Folder -->
                                    <div class="tree-branch mb-3">
                                        <div class="tree-branch-header d-flex align-items-center text-success fw-bold p-2 bg-light-success rounded cursor-pointer" data-bs-toggle="collapse" data-bs-target="#current-tyres-folder-{{ $vehicle->id }}" style="cursor: pointer;">
                                            <i class="fas fa-folder-open me-2"></i> Current Tyres (Click to expand)
                                        </div>
                                        
                                        <div class="collapse show mt-2 ms-4 border-start-dashed ps-3" id="current-tyres-folder-{{ $vehicle->id }}">
                                            @forelse($vehicle->tyreAllocations->whereNull('removal_date') as $index => $allocation)
                                                @php
                                                    $tyre = $allocation->tyre;
                                                @endphp
                                                <div class="tree-tyre-node mb-3">
                                                    <div class="tree-tyre-header d-flex align-items-center justify-content-between p-2 border rounded bg-white shadow-xs cursor-pointer" data-bs-toggle="collapse" data-bs-target="#tyre-details-{{ $allocation->id }}" style="cursor: pointer;">
                                                        <div>
                                                            <i class="fas fa-circle-notch text-primary me-2"></i>
                                                            <span class="fw-bold text-dark">Tyre {{ $index + 1 }}: {{ $tyre->serial_number ?? 'N/A' }}</span>
                                                            <span class="text-muted ms-2">| Brand: {{ $tyre->brand ?? 'N/A' }} | Size: {{ $tyre->size ?? 'N/A' }}</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Tyre Sub-details -->
                                                    <div class="collapse mt-2 ms-3 p-3 bg-light rounded border" id="tyre-details-{{ $allocation->id }}">
                                                        <div class="row">
                                                            <!-- Purchase & Issue History -->
                                                            <div class="col-md-6 border-end">
                                                                <div class="mb-3">
                                                                    <h6 class="fw-bold text-dark small"><i class="fas fa-shopping-cart text-info me-1"></i> Purchase History</h6>
                                                                    <div class="ps-3 text-muted small">
                                                                        Date: {{ $tyre->purchase_date ? $tyre->purchase_date->format('d-m-Y') : 'N/A' }} <br>
                                                                        Supplier: {{ $tyre->vendor->name ?? 'Unknown' }}
                                                                    </div>
                                                                </div>
                                                                <div class="mb-0">
                                                                    <h6 class="fw-bold text-dark small"><i class="fas fa-file-invoice text-primary me-1"></i> Issue History</h6>
                                                                    <div class="ps-3 text-muted small">
                                                                        @php
                                                                            $issueItems = \App\Models\TyreIssueNoteItem::with('tyreIssueNote')
                                                                                ->where('tire_id', $tyre->id)
                                                                                ->get();
                                                                        @endphp
                                                                        @forelse($issueItems as $item)
                                                                            Issue: {{ $item->tyreIssueNote->issue_number ?? 'N/A' }} <br>
                                                                            Date: {{ $item->tyreIssueNote->issue_date ? \Carbon\Carbon::parse($item->tyreIssueNote->issue_date)->format('d-m-Y') : 'N/A' }}
                                                                        @empty
                                                                            No issue history found.
                                                                        @endforelse
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Removal & Refill History -->
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <h6 class="fw-bold text-dark small"><i class="fas fa-wrench text-danger me-1"></i> Removal History</h6>
                                                                    <div class="ps-3 text-muted small">
                                                                        @php
                                                                            $removalAllocations = \App\Models\TyreAllocation::where('tire_id', $tyre->id)
                                                                                ->whereNotNull('removal_date')
                                                                                ->get();
                                                                        @endphp
                                                                        @forelse($removalAllocations as $remAlloc)
                                                                            Reason: {{ $remAlloc->removal_reason ?? 'N/A' }} <br>
                                                                            Date: {{ \Carbon\Carbon::parse($remAlloc->removal_date)->format('d-m-Y') }}
                                                                        @empty
                                                                            No removal history found.
                                                                        @endforelse
                                                                    </div>
                                                                </div>
                                                                <div class="mb-0">
                                                                    <h6 class="fw-bold text-dark small"><i class="fas fa-sync text-warning me-1"></i> Refill History</h6>
                                                                    <div class="ps-3 text-muted small">
                                                                        @forelse($tyre->refillingOrders as $order)
                                                                            Vendor: {{ $order->vendor->name ?? 'N/A' }} <br>
                                                                            Date: {{ $order->sent_date ? \Carbon\Carbon::parse($order->sent_date)->format('d-m-Y') : 'N/A' }}
                                                                        @empty
                                                                            No refill history found.
                                                                        @endforelse
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-3 text-end">
                                                            <a href="{{ route('tyre.inventory.show', $tyre->id) }}" class="btn btn-xs btn-outline-primary" style="font-size: 11px;">
                                                                <i class="fas fa-passport me-1"></i> View Full Passport
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-muted small ms-2 my-2">No tyres currently installed.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                    

                                    
                                </div>
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
                form.action = '{{ route("tyre.vehicles.delete", "") }}/' + vehicleId;
                
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

@push('styles')
<style>
    .bg-light-success { background-color: rgba(46, 204, 113, 0.1) !important; }
    .cursor-pointer { cursor: pointer; }
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .border-start-dashed { border-left: 2px dashed #cbd5e1 !important; }
    .tree-tyre-header:hover {
        background-color: #f8fafc !important;
        border-color: #94a3b8 !important;
    }
    .tree-branch-header:hover {
        opacity: 0.95;
    }
</style>
@endpush
@endsection