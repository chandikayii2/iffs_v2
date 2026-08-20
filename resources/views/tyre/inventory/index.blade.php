<!-- resources/views/tyre/inventory/index.blade.php -->
@extends('tyre.layouts.app')

<style>
    .action-buttons {
        display: inline-flex !important;
        gap: 0px !important;
        flex-wrap: nowrap !important;
        align-items: center;
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
    
    .location-refilling {
        background: rgba(155, 89, 182, 0.15);
        color: #8E44AD;
    }
    .search-container {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .search-container .search-input {
        flex: 1;
        position: relative;
    }
    
    .search-container .search-input input {
        width: 100%;
        padding: 10px 45px 10px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .search-container .search-input input:focus {
        border-color: #2ECC71;
        box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.1);
        outline: none;
    }
    
    .search-container .search-input .search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #95a5a6;
    }
    
    .search-container .search-input .clear-search {
        position: absolute;
        right: 40px;
        top: 50%;
        transform: translateY(-50%);
        color: #e74c3c;
        cursor: pointer;
        display: none;
        background: none;
        border: none;
        font-size: 16px;
    }
    
    .search-container .search-input .clear-search.show {
        display: block;
    }
    
    .search-container .btn-search {
        background: #1b2850;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        white-space: nowrap;
    }
    
    .search-container .btn-search:hover {
        background: #2a3d7a;
    }
    
    .search-container .btn-reset {
        background: #95a5a6;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        white-space: nowrap;
    }
    
    .search-container .btn-reset:hover {
        background: #7f8c8d;
    }
    
    .search-info {
        color: #7f8c8d;
        font-size: 13px;
        margin-top: 5px;
    }
    
    @media (max-width: 768px) {
        .search-container {
            flex-direction: column;
        }
        .search-container .search-input {
            width: 100%;
        }
        .search-container .btn-search,
        .search-container .btn-reset {
            width: 100%;
        }
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        text-align: center;
        transition: transform 0.3s;
        cursor: pointer;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .stat-card .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .stat-card .stat-label {
        font-size: 13px;
        color: #7f8c8d;
        font-weight: 500;
    }

    .stat-card .stat-icon {
        font-size: 35px;
        opacity: 0.3;
        margin-bottom: 5px;
    }

    .stat-card.das1 { background: linear-gradient(135deg, #3498DB, #2980B9); color: white; }
    .stat-card.das1 .stat-number, .stat-card.das1 .stat-label { color: white; }
    .stat-card.das1 .stat-icon { color: white; opacity: 0.5; }

    .stat-card.das2 { background: linear-gradient(135deg, #2ECC71, #27AE60); color: white; }
    .stat-card.das2 .stat-number, .stat-card.das2 .stat-label { color: white; }
    .stat-card.das2 .stat-icon { color: white; opacity: 0.5; }

    .stat-card.das3 { background: linear-gradient(135deg, #F39C12, #E67E22); color: white; }
    .stat-card.das3 .stat-number, .stat-card.das3 .stat-label { color: white; }
    .stat-card.das3 .stat-icon { color: white; opacity: 0.5; }

    .stat-card.das4 { background: linear-gradient(135deg, #9B59B6, #8E44AD); color: white; }
    .stat-card.das4 .stat-number, .stat-card.das4 .stat-label { color: white; }
    .stat-card.das4 .stat-icon { color: white; opacity: 0.5; }

    .stat-card.das5 { background: linear-gradient(135deg, #E74C3C, #C0392B); color: white; }
    .stat-card.das5 .stat-number, .stat-card.das5 .stat-label { color: white; }
    .stat-card.das5 .stat-icon { color: white; opacity: 0.5; }

    .stat-card.das6 { background: linear-gradient(135deg, #1ABC9C, #16A085); color: white; }
    .stat-card.das6 .stat-number, .stat-card.das6 .stat-label { color: white; }
    .stat-card.das6 .stat-icon { color: white; opacity: 0.5; }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    .action-btn-delete {
        background: rgba(231, 76, 60, 0.15) !important;
        color: #e74c3c !important;
    }
    .action-btn-delete:hover {
        background: #e74c3c !important;
        color: white !important;
    }
</style>

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Tyre Inventory Management</h4>
        <h6>Manage and track all tyres in your fleet</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tyre.inventory.create') }}" class="btn btn-added">
            <i class="fas fa-plus-circle" style="margin-right: 10px;"></i>Add New Tyre
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <!-- New Tyres -->
    <div class="stat-card das1">
        <div class="stat-icon"><i class="fas fa-star"></i></div>
        <div class="stat-number">{{ $stats['new'] }}</div>
        <div class="stat-label">New Tyres</div>
    </div>

    <!-- In Use -->
    <div class="stat-card das2">
        <div class="stat-icon"><i class="fas fa-car"></i></div>
        <div class="stat-number">{{ $stats['in_use'] }}</div>
        <div class="stat-label">In Use</div>
    </div>

    <!-- Used (Stock) -->
    <div class="stat-card das3">
        <div class="stat-icon"><i class="fas fa-warehouse"></i></div>
        <div class="stat-number">{{ $stats['used_stock'] }}</div>
        <div class="stat-label">Used (Stock)</div>
    </div>

    <!-- Refilled Stock -->
    <div class="stat-card das4">
        <div class="stat-icon"><i class="fas fa-sync-alt"></i></div>
        <div class="stat-number">{{ $stats['refilled_stock'] }}</div>
        <div class="stat-label">Refilled Stock</div>
    </div>

    <!-- Refilling -->
    <div class="stat-card das5">
        <div class="stat-icon"><i class="fas fa-truck"></i></div>
        <div class="stat-number">{{ $stats['at_vendor'] }}</div>
        <div class="stat-label">Refilling</div>
    </div>

    <!-- Scrap -->
    <div class="stat-card das6">
        <div class="stat-icon"><i class="fas fa-trash-alt"></i></div>
        <div class="stat-number">{{ $stats['scrap'] }}</div>
        <div class="stat-label">Scrap</div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Tyre Inventory List</h4>
                <p class="card-text">Complete list of all tyres with their current status</p>
            </div>
            <div class="card-body">
                <!-- Search Bar -->
                <div class="search-container">
                    <div class="search-input">
                        <input type="text" id="searchInput" placeholder="Search by Serial No, Brand, Vendor, Size, Type, Location, Vehicle No..." value="{{ request('search') }}">
                        <button class="clear-search" id="clearSearch" onclick="clearSearch()">✕</button>
                        <span class="search-icon"><i class="fas fa-search"></i></span>
                    </div>
                    <button class="btn-search" onclick="performSearch()">
                        <i class="fas fa-search" style="margin-right: 8px;"></i> Search
                    </button>
                    <a href="{{ route('tyre.inventory.index') }}" class="btn-reset">
                        <i class="fas fa-undo" style="margin-right: 8px;"></i> Reset
                    </a>
                </div>

                <div id="table-container">
                    @if(request('search'))
                        <div class="search-info">
                        <i class="fas fa-info-circle"></i> 
                        Showing results for: <strong>"{{ request('search') }}"</strong>
                        ({{ $tyres->total() }} results found)
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover datatable">
                        <thead>
                            <tr>
                                <th>Serial Number</th>
                                <th>Brand</th>
                                <th>Size</th>
                                <th>Type</th>
                                <th>Vendor</th>
                                <!-- <th>Status</th> -->
                                <th>Refill Count</th>
                                <th>Consumed Mileage</th>
                                <th>Current Location</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tyres as $tyre)
                            <tr>
                                <td>
                                    <a href="{{ route('tyre.inventory.show', $tyre->id) }}" class="text-primary fw-bold">
                                        {{ $tyre->serial_number }}
                                    </a>
                                </td>
                                <td>{{ $tyre->brand }}</td>
                                <td>{{ $tyre->size }}</td>
                                <td>{{ $tyre->type }}</td>
                                <td>{{ $tyre->vendor ? $tyre->vendor->name : 'N/A' }}</td>
                                <!-- <td>
                                    @php
                                        $badgeClass = 'badge-soft-success';
                                        $statusText = ucfirst(str_replace('_', ' ', $tyre->status));
                                        if($tyre->status == 'new') {
                                            $badgeClass = 'badge-soft-success';
                                        } elseif($tyre->status == 'in_use') {
                                            $badgeClass = 'badge-soft-primary';
                                        } elseif($tyre->status == 'used') {
                                            if($tyre->refill_count > 0) {
                                                $badgeClass = 'badge-soft-info';
                                                $statusText = 'Refilled';
                                            } else {
                                                $badgeClass = 'badge-soft-warning';
                                                $statusText = 'Used';
                                            }
                                        } elseif($tyre->status == 'at_vendor') {
                                            $badgeClass = 'badge-soft-danger';
                                        } elseif($tyre->status == 'scrap') {
                                            $badgeClass = 'badge-soft-dark';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td> -->
                                <td>
                                    <span class="badge {{ $tyre->refill_count >= $tyre->max_refills ? 'badge-soft-danger' : 'badge-soft-info' }}">
                                        {{ $tyre->refill_count }} / {{ $tyre->max_refills }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-soft-primary">
                                        {{ number_format($tyre->consumption_mileage) }} km
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $locationIcon = 'fa-map-marker-alt';
                                        $locationText = '';
                                        $locationClass = 'location-store';
                                        
                                        if($tyre->status == 'in_use') {
                                            $locationIcon = 'fa-truck';
                                            $locationClass = 'location-vehicle';
                                            
                                            // Try to get vehicle from currentAllocation
                                            $vehicle = null;
                                            if($tyre->currentAllocation && $tyre->currentAllocation->vehicle) {
                                                $vehicle = $tyre->currentAllocation->vehicle;
                                            } else {
                                                // Try to get from allocations directly
                                                $allocation = $tyre->allocations()->whereNull('removal_date')->with('vehicle')->first();
                                                if($allocation && $allocation->vehicle) {
                                                    $vehicle = $allocation->vehicle;
                                                }
                                            }
                                            
                                            if($vehicle) {
                                                $locationText = $vehicle->lorry_number;
                                            } else {
                                                $locationText = 'In Use';
                                            }
                                        } elseif($tyre->status == 'at_vendor') {
                                             if(in_array($tyre->current_location, ['store', 'pending_refill'])) {
                                                 $locationIcon = 'fa-warehouse';
                                                 $locationClass = 'location-store';
                                                 $locationText = 'To Be Send to Dag';
                                             } else {
                                                 $locationIcon = 'fa-sync-alt';
                                                 $locationClass = 'location-refilling';
                                                 $locationText = 'Refilling';
                                             }
                                        } elseif($tyre->status == 'scrap') {
                                            $locationIcon = 'fa-trash';
                                            $locationClass = 'location-scrap';
                                            $locationText = 'Scrap Yard';
                                        } elseif($tyre->status == 'used') {
                                             if($tyre->refill_count > 0) {
                                                 $locationIcon = 'fa-sync-alt';
                                                 $locationClass = 'location-refilling';
                                                 $locationText = 'Available for Use / Stock';
                                             } else {
                                                 $locationIcon = 'fa-warehouse';
                                                 $locationClass = 'location-store';
                                                 $locationText = 'In Stock';
                                             }
                                        } elseif($tyre->status == 'new') {
                                            if ($tyre->tire_type === 'original_casing') {
                                                $locationIcon = 'fa-box';
                                                $locationClass = 'location-store';
                                                $locationText = 'Used Casing Stock';
                                            } else {
                                                $locationIcon = 'fa-box';
                                                $locationClass = 'location-store';
                                                $locationText = 'New Stock';
                                            }
                                        } else {
                                            $locationText = ucfirst(str_replace('_', ' ', $tyre->current_location ?? 'Unknown'));
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
                                        <a href="{{ route('tyre.inventory.show', $tyre->id) }}" class="action-btn action-btn-view" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <!-- Edit Button -->
                                        <a href="{{ route('tyre.inventory.edit', $tyre->id) }}" class="action-btn action-btn-edit" title="Edit Tyre">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <!-- Send for Refill Button -->
                                        @if($tyre->canRefill() && (in_array($tyre->status, ['used', 'new']) || ($tyre->status == 'at_vendor' && in_array($tyre->current_location, ['store', 'pending_refill']))))
                                        <a href="{{ route('tyre.inventory.send-refill', $tyre->id) }}" class="action-btn action-btn-refill" title="Send for Refill">
                                            <i class="fas fa-sync-alt"></i>
                                        </a>
                                        @endif
                                        
                                        <!-- Scrap Button -->
                                        @if($tyre->status != 'scrap')
                                        <button type="button" class="action-btn action-btn-scrap" title="Scrap Tyre" onclick="scrapTyre({{ $tyre->id }})">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        @endif
                                        
                                        <!-- Delete Button -->
                                        @if(!in_array($tyre->status, ['in_use', 'at_vendor']))
                                        <button type="button" class="action-btn action-btn-delete" title="Delete Tyre" onclick="deleteTyre({{ $tyre->id }}, '{{ $tyre->serial_number }}')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">No tyres found matching your search criteria.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $tyres->links() }}
                </div>
                </div> <!-- /#table-container -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function submitScrap(tyreId, reason) {
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("tyre.scrap.process", "") }}/' + tyreId;
        
        let csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        let reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'scrap_reason';
        reasonInput.value = reason;
        form.appendChild(reasonInput);
        
        document.body.appendChild(form);
        form.submit();
    }

    function scrapTyre(tyreId) {
        Swal.fire({
            title: 'Scrap Tyre',
            html: `
                <p>This action will mark the tyre as scrap. Are you sure?</p>
                <div class="form-group text-start">
                    <label class="form-label fw-bold">Scrap Reason</label>
                    <select id="swal-scrap-reason" class="form-control mb-3">
                        <option value="">-- Select Reason --</option>
                        <option value="Damage">Damage</option>
                        <option value="Maximum refills reached">Maximum refills reached</option>
                        <option value="Age (expired)">Age (expired)</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group text-start" id="swal-custom-damage-group" style="display: none;">
                    <label class="form-label fw-bold">Custom Scrap damage Reason <span class="text-danger">*</span></label>
                    <input type="text" id="swal-custom-damage" class="form-control" placeholder="Enter damage details">
                </div>
                <div class="form-group text-start" id="swal-custom-reason-group" style="display: none;">
                    <label class="form-label fw-bold">Custom Scrap Reason <span class="text-danger">*</span></label>
                    <input type="text" id="swal-custom-reason" class="form-control" placeholder="Enter custom scrap reason">
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, scrap it!',
            didOpen: () => {
                const select = document.getElementById('swal-scrap-reason');
                const customDamageGroup = document.getElementById('swal-custom-damage-group');
                const customReasonGroup = document.getElementById('swal-custom-reason-group');
                
                select.addEventListener('change', () => {
                    customDamageGroup.style.display = 'none';
                    customReasonGroup.style.display = 'none';
                    
                    if (select.value === 'Damage') {
                        customDamageGroup.style.display = 'block';
                    } else if (select.value === 'Other') {
                        customReasonGroup.style.display = 'block';
                    }
                });
            },
            preConfirm: () => {
                const select = document.getElementById('swal-scrap-reason');
                const customDamageInput = document.getElementById('swal-custom-damage');
                const customInput = document.getElementById('swal-custom-reason');
                
                let reason = select.value;
                if (!reason) {
                    Swal.showValidationMessage('Please select a scrap reason');
                    return false;
                }
                if (reason === 'Damage') {
                    const dmgReason = customDamageInput.value.trim();
                    if (!dmgReason) {
                        Swal.showValidationMessage('Please enter custom scrap damage reason');
                        return false;
                    }
                    reason = 'Damage: ' + dmgReason;
                } else if (reason === 'Other') {
                    const otherReason = customInput.value.trim();
                    if (!otherReason) {
                        Swal.showValidationMessage('Please enter custom scrap reason');
                        return false;
                    }
                    reason = otherReason;
                }
                return reason;
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                submitScrap(tyreId, result.value);
            }
        });
    }

    function deleteTyre(tyreId, serialNumber) {
        Swal.fire({
            title: 'Delete Tyre',
            text: 'Are you sure you want to permanently delete tyre "' + serialNumber + '"? This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    url: '{{ route("tyre.inventory.delete", "") }}/' + tyreId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.status == 200) {
                            Swal.fire('Deleted!', response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        let errorMsg = 'Failed to delete tyre';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error', errorMsg, 'error');
                    }
                });
            }
        });
    }

    // Search functionality
    var debounceTimeout;

    function performSearch(instant = false) {
        var searchValue = document.getElementById('searchInput').value.trim();
        var url = '{{ route("tyre.inventory.index") }}' + (searchValue ? '?search=' + encodeURIComponent(searchValue) : '');
        
        if (instant) {
            $.get(url, function(data) {
                $('#table-container').html($(data).find('#table-container').html());
                window.history.replaceState({path: url}, '', url);
            });
        } else {
            window.location.href = url;
        }
    }

    function clearSearch() {
        document.getElementById('searchInput').value = '';
        document.getElementById('clearSearch').classList.remove('show');
        performSearch(true);
    }

    // Show/hide clear button based on input
    document.addEventListener('DOMContentLoaded', function() {
        var searchInput = document.getElementById('searchInput');
        var clearBtn = document.getElementById('clearSearch');
        
        if (searchInput.value.length > 0) {
            clearBtn.classList.add('show');
        }
        
        searchInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                clearBtn.classList.add('show');
            } else {
                clearBtn.classList.remove('show');
            }
            
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(function() {
                performSearch(true);
            }, 300);
        });

        // Enter key to search
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                clearTimeout(debounceTimeout);
                performSearch(false);
            }
        });
    });
</script>
@endpush
@endsection