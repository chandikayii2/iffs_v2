<!-- resources/views/tyre/refilling/show.blade.php -->
@extends('tyre.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-sync-alt me-2"></i>Refilling Order Details</h4>
        <h6>{{ $order->order_number }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tyre.refilling.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Orders
        </a>
        <a href="{{ route('tyre.refilling.pdf', $order->id) }}" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf me-1"></i> PDF
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
                    <p><strong>{{ $order->vendor->name }}</strong></p>
                    <p style="font-size: 13px; color: #666;">
                        Contact: {{ $order->vendor->contact_person }} | Phone: {{ $order->vendor->phone }}
                    </p>
                </div>
                <div class="form-group">
                    <label>Sent Date</label>
                    <p>{{ $order->sent_date->format('d-m-Y') }}</p>
                </div>
            </div>
            <div class="col-lg-6">
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
                <div class="form-group">
                    <label>Total Cost</label>
                    <p><strong>Rs. {{ number_format($order->total_cost ?? 0, 2) }}</strong></p>
                </div>
            </div>
        </div>
        @if($order->notes)
        <div class="row">
            <div class="col-lg-12">
                <div class="form-group">
                    <label>Notes</label>
                    <p>{{ $order->notes }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@if($order->status == 'received')
<div class="card mt-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Payment Details</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">
                    <label>Payment Status</label>
                    <p>
                        @php
                            $payBadge = 'badge-soft-danger';
                            if($order->payment_status == 'paid') $payBadge = 'badge-soft-success';
                            elseif($order->payment_status == 'partial') $payBadge = 'badge-soft-warning';
                        @endphp
                        <span class="badge {{ $payBadge }}">{{ ucfirst($order->payment_status ?? 'unpaid') }}</span>
                    </p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <label>Paid Amount</label>
                    <p><strong>Rs. {{ number_format($order->paid_amount ?? 0, 2) }}</strong></p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <label>Payment Method</label>
                    <p>{{ $order->payment_method ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <label>Payment Date</label>
                    <p>{{ $order->payment_date ? \Carbon\Carbon::parse($order->payment_date)->format('d-m-Y') : 'N/A' }}</p>
                </div>
            </div>
            @if($order->payment_notes)
            <div class="col-lg-12 mt-2">
                <div class="form-group">
                    <label>Payment Notes</label>
                    <p class="mb-0 text-muted">{{ $order->payment_notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

<div class="card mt-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Tyres in this Order</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Serial Number</th>
                        <th>Brand</th>
                        <th>Size</th>
                        <th>Type</th>
                        <th>Refill Count</th>
                        <th>Refilling Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->tyres as $index => $tyre)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <a href="{{ route('tyre.inventory.show', $tyre->id) }}" class="text-primary">
                                {{ $tyre->serial_number }}
                            </a>
                        </td>
                        <td>{{ $tyre->brand }}</td>
                        <td>{{ $tyre->size }}</td>
                        <td>{{ $tyre->type }}</td>
                        <td>
                            <span class="badge badge-soft-info">
                                {{ $tyre->refill_count }} / {{ $tyre->max_refills }}
                            </span>
                        </td>
                        <td>Rs. {{ number_format($tyre->pivot->refilling_cost ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($order->status == 'sent')
<div class="card mt-4">
    <div class="card-body text-center">
        <a href="{{ route('tyre.refilling.receive', $order->id) }}" class="btn btn-success">
            <i class="fas fa-check-circle me-1"></i> Receive Order
        </a>
    </div>
</div>
@endif
@endsection