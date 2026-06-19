<!-- resources/views/tire/refilling/index.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-sync-alt me-2"></i>Refilling Orders</h4>
        <h6>Manage tire refilling/retreading orders</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.refilling.create') }}" class="btn btn-added">
            <i class="fas fa-plus-circle"></i> New Order
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">All Refilling Orders</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Vendor</th>
                        <th>Sent Date</th>
                        <th>Expected Return</th>
                        <th>Status</th>
                        <th>Total Cost</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->vendor->name }}</td>
                        <td>{{ $order->sent_date->format('d-m-Y') }}</td>
                        <td>{{ $order->expected_return_date ? $order->expected_return_date->format('d-m-Y') : 'N/A' }}</td>
                        <td>
                            <span class="badge badge-soft-{{ $order->status == 'sent' ? 'warning' : ($order->status == 'processing' ? 'info' : 'success') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>${{ number_format($order->total_cost ?? 0, 2) }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('tire.refilling.show', $order->id) }}" class="action-btn action-btn-view" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($order->status == 'sent')
                                <a href="{{ route('tire.refilling.receive', $order->id) }}" class="action-btn action-btn-success" title="Receive Order">
                                    <i class="fas fa-check-circle"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $orders->links() }}
    </div>
</div>
@endsection