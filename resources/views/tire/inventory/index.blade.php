<!-- resources/views/tire/inventory/index.blade.php -->
@extends('tire.layouts.app')

<style>
    .action-buttons {
        display: inline-flex !important;
        gap: 5px;
        align-items: center;
    }
    
    .action-btn {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        transition: all 0.3s;
        text-decoration: none;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .location-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
    }
    
    .location-vehicle {
        background: rgba(46, 204, 113, 0.15);
        color: #27AE60;
    }
    
    .location-store {
        background: rgba(52, 152, 219, 0.15);
        color: #2980B9;
    }
    
    .location-vendor {
        background: rgba(243, 156, 18, 0.15);
        color: #E67E22;
    }
    
    .location-scrap {
        background: rgba(231, 76, 60, 0.15);
        color: #C0392B;
    }
    
    .action-btn-edit {
        color: #F39C12;
    }
    .action-btn-edit:hover {
        background: rgba(243, 156, 18, 0.1);
        color: #E67E22;
    }
    .action-btn-refill {
        color: #9B59B6;
    }
    .action-btn-refill:hover {
        background: rgba(155, 89, 182, 0.1);
        color: #8E44AD;
    }
    .action-btn-scrap {
        color: #E74C3C;
    }
    .action-btn-scrap:hover {
        background: rgba(231, 76, 60, 0.1);
        color: #C0392B;
    }
    .action-btn-view {
        color: #3498DB;
    }
    .action-btn-view:hover {
        background: rgba(52, 152, 219, 0.1);
        color: #2980B9;
    }
</style>

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Tire Inventory Management</h4>
        <h6>Manage and track all tires in your fleet</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.inventory.create') }}" class="btn btn-added">
            <i class="fas fa-plus-circle"></i> Add New Tire
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 col-sm-6 col-6">
        <div class="dash-count">
            <div class="dash-counts">
                <h4>{{ $stats['new'] }}</h4>
                <h5>New Tires</h5>
            </div>
            <div class="dash-imgs">
                <i class="fas fa-star"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="dash-count das1">
            <div class="dash-counts">
                <h4>{{ $stats['in_use'] }}</h4>
                <h5>In Use</h5>
            </div>
            <div class="dash-imgs">
                <i class="fas fa-car"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-sm-6 col-12">
        <div class="dash-count das2">
            <div class="dash-counts">
                <h4>{{ $stats['used'] }}</h4>
                <h5>Used (Store)</h5>
            </div>
            <div class="dash-imgs">
                <i class="fas fa-warehouse"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-sm-6 col-12">
        <div class="dash-count das3">
            <div class="dash-counts">
                <h4>{{ $stats['at_vendor'] }}</h4>
                <h5>Refill</h5>
            </div>
            <div class="dash-imgs">
                <i class="fas fa-truck"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-sm-6 col-12">
        <div class="dash-count das4">
            <div class="dash-counts" >
                <h4>{{ $stats['scrap'] }}</h4>
                <h5>Scrap</h5>
            </div>
            <div class="dash-imgs">
                <i class="fas fa-trash-alt"></i>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Tire Inventory List</h4>
                <p class="card-text">Complete list of all tires with their current status</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th>Serial Number</th>
                                <th>Brand</th>
                                <th>Size</th>
                                <th>Type</th>
                                <th>Vendor</th>
                                <th>Status</th>
                                <th>Refill Count</th>
                                <th>Consumed Mileage</th>
                                <th>Current Location</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tires as $tire)
                            <tr>
                                <td>
                                    <a href="{{ route('tire.inventory.show', $tire->id) }}" class="text-primary fw-bold">
                                        {{ $tire->serial_number }}
                                    </a>
                                </td>
                                <td>{{ $tire->brand }}</td>
                                <td>{{ $tire->size }}</td>
                                <td>{{ $tire->type }}</td>
                                <td>{{ $tire->vendor ? $tire->vendor->name : 'N/A' }}</td>
                                <td>
                                    @php
                                        $badgeClass = 'badge-soft-success';
                                        $statusText = ucfirst(str_replace('_', ' ', $tire->status));
                                        if($tire->status == 'new') {
                                            $badgeClass = 'badge-soft-success';
                                        } elseif($tire->status == 'in_use') {
                                            $badgeClass = 'badge-soft-primary';
                                        } elseif($tire->status == 'used') {
                                            $badgeClass = 'badge-soft-warning';
                                        } elseif($tire->status == 'at_vendor') {
                                            $badgeClass = 'badge-soft-danger';
                                        } elseif($tire->status == 'scrap') {
                                            $badgeClass = 'badge-soft-dark';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $tire->refill_count >= $tire->max_refills ? 'badge-soft-danger' : 'badge-soft-info' }}">
                                        {{ $tire->refill_count }} / {{ $tire->max_refills }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-primary">
                                        {{ number_format($tire->consumption_mileage) }} km
                                    </span>
                                </td>
                                <!-- resources/views/tire/inventory/index.blade.php - Location column section -->
<td>
    @php
        $locationIcon = 'fa-map-marker-alt';
        $locationText = '';
        $locationClass = 'location-store';
        
        if($tire->status == 'in_use') {
            $locationIcon = 'fa-truck';
            $locationClass = 'location-vehicle';
            
            // Try to get vehicle from currentAllocation
            $vehicle = null;
            if($tire->currentAllocation && $tire->currentAllocation->vehicle) {
                $vehicle = $tire->currentAllocation->vehicle;
            } else {
                // Try to get from allocations directly
                $allocation = $tire->allocations()->whereNull('removal_date')->with('vehicle')->first();
                if($allocation && $allocation->vehicle) {
                    $vehicle = $allocation->vehicle;
                }
            }
            
            if($vehicle) {
                $locationText = 'Vehicle: ' . $vehicle->lorry_number;
            } else {
                $locationText = 'In Use';
            }
        } elseif($tire->status == 'at_vendor') {
            $locationIcon = 'fa-store';
            $locationClass = 'location-vendor';
            $locationText = 'At Vendor';
        } elseif($tire->status == 'scrap') {
            $locationIcon = 'fa-trash';
            $locationClass = 'location-scrap';
            $locationText = 'Scrap Yard';
        } elseif($tire->status == 'used') {
            $locationIcon = 'fa-warehouse';
            $locationClass = 'location-store';
            $locationText = 'In Store';
        } elseif($tire->status == 'new') {
            $locationIcon = 'fa-box';
            $locationClass = 'location-store';
            $locationText = 'New Stock';
        } else {
            $locationText = ucfirst(str_replace('_', ' ', $tire->current_location ?? 'Unknown'));
        }
    @endphp
    <span class="location-badge {{ $locationClass }}">
        <i class="fas {{ $locationIcon }}"></i>
        {{ $locationText }}
    </span>
</td>
                                <td>
                                    <div class="action-buttons">
                                        <!-- View Button -->
                                        <a href="{{ route('tire.inventory.show', $tire->id) }}" class="action-btn action-btn-view" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <!-- Edit Button -->
                                        <a href="{{ route('tire.inventory.edit', $tire->id) }}" class="action-btn action-btn-edit" title="Edit Tire">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <!-- Send for Refill Button -->
                                        @if($tire->canRefill() && in_array($tire->status, ['used', 'new']))
                                        <a href="{{ route('tire.inventory.send-refill', $tire->id) }}" class="action-btn action-btn-refill" title="Send for Refill">
                                            <i class="fas fa-sync-alt"></i>
                                        </a>
                                        @endif
                                        
                                        <!-- Scrap Button -->
                                        @if($tire->status != 'scrap')
                                        <button type="button" class="action-btn action-btn-scrap" title="Scrap Tire" onclick="scrapTire({{ $tire->id }})">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $tires->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function scrapTire(tireId) {
        Swal.fire({
            title: 'Scrap Tire',
            text: "This action will mark the tire as scrap. Are you sure?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, scrap it!',
            showDenyButton: true,
            denyButtonText: 'Cancel',
            input: 'select',
            inputOptions: {
                'Worn out': 'Worn out',
                'Damage': 'Damage',
                'Puncture': 'Puncture', 
                'Maximum refills reached': 'Maximum refills reached',
                'Sidewall damage': 'Sidewall damage',
                'Age (expired)': 'Age (expired)',
                'Other': 'Other'
            },
            inputPlaceholder: 'Select scrap reason',
            preConfirm: (reason) => {
                if (!reason) {
                    Swal.showValidationMessage('Please select a reason');
                }
                return reason;
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("tire.scrap.process", "") }}/' + tireId;
                let csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);
                
                let reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'scrap_reason';
                reasonInput.value = result.value;
                form.appendChild(reasonInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endpush
@endsection