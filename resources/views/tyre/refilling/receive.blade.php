<!-- resources/views/tyre/refilling/receive.blade.php -->
@extends('tyre.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-check-circle me-2"></i>Receive Refilling Order</h4>
        <h6>{{ $order->order_number }} from {{ $order->vendor->name }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tyre.refilling.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Orders
        </a>
    </div>
</div>

<form action="{{ route('tyre.refilling.receive.process', $order->id) }}" method="POST">
    @csrf
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Receiving Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Received Date *</label>
                        <input type="date" name="received_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Total Cost *</label>
                        <input type="number" step="0.01" name="total_cost" id="totalCostInput" class="form-control" readonly required>
                        <small class="text-muted">Calculated automatically from individual tyre costs</small>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>Tyre Details</label>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Serial Number</th>
                                        <th>Brand</th>
                                        <th>Size</th>
                                        <th>Refilling Cost</th>
                                        <th>Refill Count Increase</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->tyres as $index => $tyre)
                                    <tr>
                                        <td>{{ $tyre->serial_number }}</td>
                                        <td>{{ $tyre->brand }}</td>
                                        <td>{{ $tyre->size }}</td>
                                        <td>
                                            <input type="number" step="0.01" name="refilling_costs[]" class="form-control refill-cost-input" required>
                                        </td>
                                        <td>
                                            <input type="number" name="refill_counts[]" class="form-control" value="1" min="1" required readonly>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">Process Receipt</button>
                <a href="{{ route('tyre.refilling.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.refill-cost-input').on('input', function() {
            let total = 0;
            $('.refill-cost-input').each(function() {
                let val = parseFloat($(this).val()) || 0;
                total += val;
            });
            $('#totalCostInput').val(total.toFixed(2));
        });
    });
</script>
@endpush
@endsection