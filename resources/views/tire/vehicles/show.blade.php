<!-- resources/views/tire/vehicles/show.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-truck me-2"></i>Vehicle Details</h4>
        <h6>{{ $vehicle->lorry_number }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.vehicles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
        <a href="{{ route('tire.vehicles.edit', $vehicle->id) }}" class="btn btn-primary">
            <i class="fas fa-edit me-1"></i> Edit Vehicle
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Vehicle Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Vehicle Number:</th>
                        <td><strong>{{ $vehicle->lorry_number }}</strong></td>
                    </tr>
                    <tr>
                        <th>Status:</th>
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
                    </tr>
                    <tr>
                        <th>Registration Date:</th>
                        <td>{{ $vehicle->created_at ? $vehicle->created_at->format('d M Y') : 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Vehicle Statistics</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Total Tires Allocated:</th>
                        <td>
                            <span class="badge badge-soft-primary">{{ $vehicle->tireAllocations->count() }} Tires</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Current Tires on Vehicle:</th>
                        <td>
                            <span class="badge badge-soft-success">{{ $currentTires->count() }} Tires</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Historical Tires:</th>
                        <td>
                            <span class="badge badge-soft-info">{{ $historyTires->count() }} Tires</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Current Tires on Vehicle</h5>
            @if($currentTires->count() > 0)
                <span class="badge badge-soft-success">{{ $currentTires->count() }} Active Tires</span>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Serial Number</th>
                        <th>Brand</th>
                        <th>Size</th>
                        <th>Type</th>
                        <th>Position</th>
                        <th>Installation Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($currentTires as $allocation)
                    <tr>
                        <td>
                            <a href="{{ route('tire.inventory.show', $allocation->tire_id) }}" class="text-primary">
                                <strong>{{ $allocation->tire->serial_number }}</strong>
                            </a>
                        </td>
                        <td>{{ $allocation->tire->brand ?? 'N/A' }}</td>
                        <td>{{ $allocation->tire->size ?? 'N/A' }}</td>
                        <td>{{ ucfirst($allocation->tire->type ?? 'N/A') }}</td>
                        <td>
                            <span class="badge badge-soft-info">{{ $allocation->position ?? 'Not Specified' }}</span>
                        </td>
                        <td>{{ $allocation->installation_date ? date('d M Y', strtotime($allocation->installation_date)) : 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-info-circle me-2"></i> No tires currently allocated to this vehicle
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($historyTires->count() > 0)
<div class="card mt-4">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Historical Tires (Removed)</h5>
            <span class="badge badge-soft-secondary">{{ $historyTires->count() }} Historical Tires</span>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Serial Number</th>
                        <th>Brand</th>
                        <th>Size</th>
                        <th>Removal Reason</th>
                        <th>Removal Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historyTires as $allocation)
                    <tr>
                        <td>
                            <a href="{{ route('tire.inventory.show', $allocation->tire_id) }}" class="text-primary">
                                {{ $allocation->tire->serial_number }}
                            </a>
                        </td>
                        <td>{{ $allocation->tire->brand ?? 'N/A' }}</td>
                        <td>{{ $allocation->tire->size ?? 'N/A' }}</td>
                        <td>{{ $allocation->removal_reason ?? 'N/A' }}</td>
                        <td>{{ $allocation->removal_date ? date('d M Y', strtotime($allocation->removal_date)) : 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    $(document).ready(function() {
        $('[title]').tooltip();
    });
</script>
@endpush
@endsection