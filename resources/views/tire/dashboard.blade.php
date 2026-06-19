<!-- resources/views/tire/dashboard.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-tachometer-alt me-2"></i>Tire Lifecycle Dashboard</h4>
        <h6>Monitor your tire inventory and operations</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.reports.analytics') }}" class="btn btn-info">
            <i class="fas fa-chart-line"></i> View Analytics
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="row">
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="dash-count">
            <div class="dash-counts">
                <h4>{{ $stats['total_tires'] ?? 0 }}</h4>
                <h5>Total Tires</h5>
            </div>
            <div class="dash-imgs">
                <i class="fas fa-circle-notch"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="dash-count das1">
            <div class="dash-counts">
                <h4>{{ $stats['new_tires'] ?? 0 }}</h4>
                <h5>New Tires</h5>
            </div>
            <div class="dash-imgs">
                <i class="fas fa-star"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="dash-count das2">
            <div class="dash-counts">
                <h4>{{ $stats['in_use_tires'] ?? 0 }}</h4>
                <h5>Tires In Use</h5>
            </div>
            <div class="dash-imgs">
                <i class="fas fa-car"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="dash-count das3">
            <div class="dash-counts">
                <h4>{{ $stats['used_tires'] ?? 0 }}</h4>
                <h5>Used Tires</h5>
            </div>
            <div class="dash-imgs">
                <i class="fas fa-history"></i>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="dash-count das4">
            <div class="dash-counts">
                <h4>{{ $stats['at_vendor_tires'] ?? 0 }}</h4>
                <h5>At Vendor/Refill</h5>
            </div>
            <div class="dash-imgs">
                <i class="fas fa-truck"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="dash-count das5">
            <div class="dash-counts">
                <h4>{{ $stats['scrap_tires'] ?? 0 }}</h4>
                <h5>Scrap Tires</h5>
            </div>
            <div class="dash-imgs">
                <i class="fas fa-trash"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="dash-count das6">
            <div class="dash-counts">
                <h4>{{ $stats['active_vehicles'] ?? 0 }}</h4>
                <h5>Active Vehicles</h5>
            </div>
            <div class="dash-imgs">
                <i class="fas fa-bus"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="dash-count das7">
            <div class="dash-counts">
                <h4>{{ $stats['pending_refilling'] ?? 0 }}</h4>
                <h5>Pending Refilling</h5>
            </div>
            <div class="dash-imgs">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<!-- <div class="row mt-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Tire Status Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Monthly Activity</h5>
            </div>
            <div class="card-body">
                <canvas id="activityChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div> -->

<!-- Recent Activity Tables -->
<div class="row mt-4">
    <div class="col-lg-6 col-sm-12">
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Tire Allocations</h5>
                <a href="{{ route('tire.vehicles.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tire Serial</th>
                                <th>Vehicle</th>
                                <th>Installation Date</th>
                                <th>Mileage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAllocations as $allocation)
                            <tr>
                                <td>
                                    <a href="{{ route('tire.inventory.show', $allocation->tire_id) }}" class="text-primary">
                                        {{ $allocation->tire->serial_number }}
                                    </a>
                                </td>
                                <td>{{ $allocation->vehicle->lorry_number }}</td>
                                <td>{{ $allocation->installation_date->format('d-m-Y') }}</td>
                                <td>{{ number_format($allocation->mileage_at_installation) }} km</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center">No recent allocations found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-sm-12">
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Tires Added</h5>
                <a href="{{ route('tire.inventory.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Serial Number</th>
                                <th>Brand</th>
                                <th>Size</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTires as $tire)
                            <tr>
                                <td>
                                    <a href="{{ route('tire.inventory.show', $tire->id) }}" class="text-primary">
                                        {{ $tire->serial_number }}
                                    </a>
                                </td>
                                <td>{{ $tire->brand }}</td>
                                <td>{{ $tire->size }}</td>
                                <td>
                                    @php
                                        $badgeClass = 'badge-soft-success';
                                        if($tire->status == 'new') $badgeClass = 'badge-soft-success';
                                        elseif($tire->status == 'in_use') $badgeClass = 'badge-soft-primary';
                                        elseif($tire->status == 'used') $badgeClass = 'badge-soft-warning';
                                        elseif($tire->status == 'at_vendor') $badgeClass = 'badge-soft-danger';
                                        elseif($tire->status == 'scrap') $badgeClass = 'badge-soft-dark';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ ucfirst(str_replace('_', ' ', $tire->status)) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center">No tires found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('tire.inventory.create') }}" class="btn btn-primary w-100">
                            <i class="fas fa-plus-circle"></i> Add New Tire
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('tire.vehicles.create') }}" class="btn btn-success w-100">
                            <i class="fas fa-truck"></i> Register Vehicle
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('tire.refilling.create') }}" class="btn btn-warning w-100">
                            <i class="fas fa-sync-alt"></i> Create Refill Order
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('tire.passport.search') }}" class="btn btn-info w-100">
                            <i class="fas fa-passport"></i> Search Tire Passport
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['New', 'In Use', 'Used (Store)', 'At Vendor', 'Scrap'],
            datasets: [{
                data: [
                    {{ $stats['new_tires'] ?? 0 }},
                    {{ $stats['in_use_tires'] ?? 0 }},
                    {{ $stats['used_tires'] ?? 0 }},
                    {{ $stats['at_vendor_tires'] ?? 0 }},
                    {{ $stats['scrap_tires'] ?? 0 }}
                ],
                backgroundColor: ['#2ECC71', '#3498DB', '#F39C12', '#E74C3C', '#95A5A6'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
    
    // Monthly Activity Chart
    const activityCtx = document.getElementById('activityChart').getContext('2d');
    new Chart(activityCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [
                {
                    label: 'Tires Allocated',
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    borderColor: '#2ECC71',
                    backgroundColor: 'rgba(46, 204, 113, 0.1)',
                    fill: true
                },
                {
                    label: 'Tires Scrapped',
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    borderColor: '#E74C3C',
                    backgroundColor: 'rgba(231, 76, 60, 0.1)',
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            }
        }
    });
</script>
@endpush
@endsection