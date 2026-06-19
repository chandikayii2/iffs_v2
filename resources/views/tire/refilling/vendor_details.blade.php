<!-- resources/views/tire/refilling/vendor_details.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-building me-2"></i>Vendor Details</h4>
        <h6>Complete vendor information and details</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.refilling.vendors.manage') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Vendors
        </a>
        <!-- <a href="{{ route('tire.refilling.vendors.edit', $vendor->id) }}" class="btn btn-primary">
            <i class="fas fa-edit me-1"></i> Edit Vendor
        </a> -->
    </div>
</div>

<div class="row">
    <!-- Vendor Information Card -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Vendor Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="35%">Company Name:</th>
                        <td><strong>{{ $vendor->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Contact Person:</th>
                        <td>{{ $vendor->contact_person }}</td>
                    </tr>
                    <tr>
                        <th>Phone Number:</th>
                        <td>
                            <a href="tel:{{ $vendor->phone }}" class="text-primary">
                                <i class="fas fa-phone me-1"></i> {{ $vendor->phone }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Email Address:</th>
                        <td>
                            @if($vendor->email)
                                <a href="mailto:{{ $vendor->email }}" class="text-primary">
                                    <i class="fas fa-envelope me-1"></i> {{ $vendor->email }}
                                </a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Address:</th>
                        <td>
                            <i class="fas fa-map-marker-alt me-1 text-danger"></i> 
                            {{ $vendor->address }}
                        </td>
                    </tr>
                    <tr>
                        <th>Total Orders:</th>
                        <td>
                            <span class="badge badge-soft-info">
                                <i class="fas fa-sync-alt me-1"></i> 
                                {{ $vendor->refilling_orders_count ?? $vendor->refillingOrders->count() }} orders
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Created At:</th>
                        <td>
                            <i class="fas fa-calendar-plus me-1 text-success"></i> 
                            {{ $vendor->created_at->format('d-m-Y H:i:s') }}
                        </td>
                    </tr>
                    <tr>
                        <th>Last Updated:</th>
                        <td>
                            <i class="fas fa-calendar-edit me-1 text-warning"></i> 
                            {{ $vendor->updated_at->format('d-m-Y H:i:s') }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Statistics Card -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="dash-count das1" style="background: linear-gradient(135deg, #3498DB, #2980B9);">
                            <div class="dash-counts">
                                <h4 style="color: white;">{{ $vendor->refillingOrders->count() }}</h4>
                                <h5 style="color: white;">Total Orders</h5>
                            </div>
                            <div class="dash-imgs">
                                <i class="fas fa-sync-alt" style="color: white; opacity: 0.5;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="dash-count das2" style="background: linear-gradient(135deg, #F39C12, #E67E22);">
                            <div class="dash-counts">
                                <h4 style="color: white;">
                                    {{ $vendor->refillingOrders->where('status', 'received')->count() }}
                                </h4>
                                <h5 style="color: white;">Completed Orders</h5>
                            </div>
                            <div class="dash-imgs">
                                <i class="fas fa-check-circle" style="color: white; opacity: 0.5;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="dash-count das3" style="background: linear-gradient(135deg, #9B59B6, #8E44AD);">
                            <div class="dash-counts">
                                <h4 style="color: white;">
                                    {{ $vendor->refillingOrders->where('status', 'sent')->count() }}
                                </h4>
                                <h5 style="color: white;">Pending Orders</h5>
                            </div>
                            <div class="dash-imgs">
                                <i class="fas fa-clock" style="color: white; opacity: 0.5;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="dash-count das4" style="background: linear-gradient(135deg, #E74C3C, #C0392B);">
                            <div class="dash-counts">
                                <h4 style="color: white;">
                                    ${{ number_format($vendor->refillingOrders->sum('total_cost'), 2) }}
                                </h4>
                                <h5 style="color: white;">Total Value</h5>
                            </div>
                            <div class="dash-imgs">
                                <i class="fas fa-dollar-sign" style="color: white; opacity: 0.5;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="card mt-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Recent Orders</h5>
        <a href="{{ route('tire.refilling.index') }}" class="btn btn-sm btn-outline-primary">
            View All Orders <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body">
        @if($vendor->refillingOrders->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Sent Date</th>
                        <th>Expected Return</th>
                        <th>Received Date</th>
                        <th>Status</th>
                        <th>Total Cost</th>
                        <th>Tires</th>
                        <th width="80">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vendor->refillingOrders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('tire.refilling.show', $order->id) }}" class="text-primary fw-bold">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td>{{ $order->sent_date->format('d-m-Y') }}</td>
                        <td>{{ $order->expected_return_date ? $order->expected_return_date->format('d-m-Y') : 'N/A' }}</td>
                        <td>{{ $order->received_date ? $order->received_date->format('d-m-Y') : 'Not Received' }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'sent' => 'warning',
                                    'processing' => 'info',
                                    'received' => 'success',
                                    'cancelled' => 'danger'
                                ];
                                $color = $statusColors[$order->status] ?? 'secondary';
                            @endphp
                            <span class="badge badge-soft-{{ $color }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>${{ number_format($order->total_cost ?? 0, 2) }}</td>
                        <td>
                            <span class="badge badge-soft-primary">
                                {{ $order->tires->count() }} tires
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('tire.refilling.show', $order->id) }}" class="action-btn action-btn-view" title="View Order">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
            <h5>No Orders Found</h5>
            <p class="text-muted">This vendor has no refilling orders yet.</p>
        </div>
        @endif
    </div>
</div>
@endsection