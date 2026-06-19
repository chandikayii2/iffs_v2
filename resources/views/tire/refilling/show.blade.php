<!-- resources/views/tire/refilling/show.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-sync-alt me-2"></i>Refilling Order Details</h4>
        <h6>{{ $order->order_number }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.refilling.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Orders
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Order Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label>Order Number</label>
                    <p><strong>{{ $order->order_number }}</strong></p>
                </div>
                <div class="form-group">
                    <label>Vendor</label>
                    <p>{{ $order->vendor->name }}</p>
                </div>
                <div class="form-group">
                    <label>Sent Date</label>
                    <p>{{ $order->sent_date->format('d-m-Y') }}</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label>Expected Return Date</label>
                    <p>{{ $order->expected_return_date ? $order->expected_return_date->format('d-m-Y') : 'N/A' }}</p>
                </div>
                <div class="form-group">
                    <label>Received Date</label>
                    <p>{{ $order->received_date ? $order->received_date->format('d-m-Y') : 'Not received yet' }}</p>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <p>
                        <span class="badge badge-soft-{{ $order->status == 'sent' ? 'warning' : ($order->status == 'processing' ? 'info' : 'success') }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Tires in this Order</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Serial Number</th>
                        <th>Brand</th>
                        <th>Size</th>
                        <th>Refilling Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->tires as $tire)
                    <tr>
                        <td>{{ $tire->serial_number }}</td>
                        <td>{{ $tire->brand }}</td>
                        <td>{{ $tire->size }}</td>
                        <td>${{ number_format($tire->pivot->refilling_cost ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection