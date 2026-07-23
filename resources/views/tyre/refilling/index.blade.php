<!-- resources/views/tyre/refilling/index.blade.php -->
@extends('tyre.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-sync-alt me-2"></i>Refilling Orders</h4>
        <h6>Manage tyre refilling/retreading orders</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tyre.refilling.create') }}" class="btn btn-added">
            <i class="fas fa-plus-circle" style="margin-right: 5px;"></i> New Order
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <!-- Search Bar -->
        <div class="search-container" style="margin-bottom: 20px;">
            <div class="search-input" style="flex: 1; position: relative;">
                <input type="text" id="searchInput" placeholder="Search by Order No, Vendor, Tyre Serial, Status..." value="{{ request('search') }}" 
                       style="width: 100%; padding: 10px 45px 10px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
                <button class="clear-search" id="clearSearch" onclick="clearSearch()" 
                        style="position: absolute; right: 40px; top: 50%; transform: translateY(-50%); color: #e74c3c; cursor: pointer; display: {{ request('search') ? 'block' : 'none' }}; background: none; border: none; font-size: 16px;">✕</button>
                <span class="search-icon" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #95a5a6;">
                    <i class="fas fa-search"></i>
                </span>
            </div>
            <div class="search-select" style="margin-left: 10px;">
                <select id="paymentStatusFilter" onchange="performSearch(true)"
                        style="padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; background: white; cursor: pointer; color: #333; min-width: 150px; outline: none; transition: border-color 0.2s;">
                    <option value="">-- All Payments --</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <button class="btn-search" onclick="performSearch()" 
                    style="background: #1b2850; color: white; border: none; padding: 10px 25px; border-radius: 8px; cursor: pointer; margin-left: 10px; white-space: nowrap;">
                <i class="fas fa-search" style="margin-right: 8px;"></i> Search
            </button>
            <a href="{{ route('tyre.refilling.index') }}" class="btn-reset" 
               style="background: #95a5a6; color: white; border: none; padding: 10px 25px; border-radius: 8px; cursor: pointer; margin-left: 10px; white-space: nowrap; text-decoration: none; display: inline-block;">
                <i class="fas fa-undo" style="margin-right: 8px;"></i> Reset
            </a>
        </div>

        <div id="table-container">
            @if(request('search'))
                <div class="search-info" style="color: #7f8c8d; font-size: 13px; margin-bottom: 15px;">
                <i class="fas fa-info-circle"></i> 
                Showing results for: <strong>"{{ request('search') }}"</strong>
                ({{ $orders->total() }} results found)
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Vendor</th>
                        <th>Sent Date</th>
                        <th>Status</th>
                        <th>Total Cost</th>
                        <th>Payment Status</th>
                        <th>Paid Amount</th>
                        <th>Payment Method</th>
                        <th>Payment Date</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('tyre.refilling.show', $order->id) }}" class="text-primary fw-bold">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td>{{ $order->vendor->name }}</td>
                        <td>{{ $order->sent_date->format('d-m-Y') }}</td>
                        <td>
                            <span class="badge badge-soft-{{ $order->status == 'sent' ? 'warning' : ($order->status == 'processing' ? 'info' : 'success') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>Rs.{{ number_format($order->total_cost ?? 0, 2) }}</td>
                        <td>
                            @php
                                $payBadge = 'badge-soft-danger';
                                if($order->payment_status == 'paid') $payBadge = 'badge-soft-success';
                                elseif($order->payment_status == 'partial') $payBadge = 'badge-soft-warning';
                            @endphp
                            <span class="badge {{ $payBadge }}">
                                @if($order->payment_status == 'paid')
                                    Paid
                                @elseif($order->payment_status == 'partial')
                                    Partial
                                @else
                                    Pending
                                @endif
                            </span>
                        </td>
                        <td>Rs.{{ number_format($order->paid_amount ?? 0, 2) }}</td>
                        <td>
                            @if($order->payment_method == 'cash')
                                Cash
                            @elseif($order->payment_method == 'online')
                                Bank Transfer
                            @elseif($order->payment_method == 'check')
                                Cheque
                            @else
                                {{ $order->payment_method ?? '-' }}
                            @endif
                        </td>
                        <td>{{ $order->payment_date ? \Carbon\Carbon::parse($order->payment_date)->format('d-m-Y') : '-' }}</td>
                        <td>
                            <div class="action-buttons">
                                <!-- View Button -->
                                <a href="{{ route('tyre.refilling.show', $order->id) }}" class="action-btn action-btn-view" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- PDF Button -->
                                <a href="{{ route('tyre.refilling.pdf', $order->id) }}" class="action-btn action-btn-download" title="Download PDF" target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                
                                <!-- Receive Order Button (only for sent orders) -->
                                @if($order->status == 'sent')
                                <a href="{{ route('tyre.refilling.receive', $order->id) }}" class="action-btn action-btn-success" title="Receive Order">
                                    <i class="fas fa-check-circle"></i>
                                </a>
                                @endif

                                <!-- Record Payment Button -->
                                @if($order->status == 'received' && $order->payment_status != 'paid')
                                <button type="button" class="action-btn action-btn-info btn-record-payment" 
                                    data-id="{{ $order->id }}" 
                                    data-number="{{ $order->order_number }}" 
                                    data-total="{{ $order->total_cost }}" 
                                    data-paid="{{ $order->paid_amount ?? 0 }}"
                                    title="Record Payment">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4">
                            <i class="fas fa-search fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No refilling orders found matching your search criteria.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $orders->links() }}
        </div>
        </div> <!-- /#table-container -->
    </div>
</div>

@push('styles')
<style>
    .search-container {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }
    .search-input {
        flex: 1;
        min-width: 250px;
    }
    @media (max-width: 768px) {
        .search-container {
            flex-direction: column;
        }
        .search-input {
            width: 100%;
            min-width: unset;
        }
        .btn-search, .btn-reset {
            width: 100%;
            margin-left: 0 !important;
        }
    }
    #recordPaymentModal {
        z-index: 1060 !important;
    }
    #recordPaymentModal .modal-dialog {
        max-width: 800px !important;
        width: 800px !important;
        margin: 1.75rem auto !important;
        display: flex !important;
        justify-content: center !important;
    }
    #recordPaymentModal .modal-content {
        border-radius: 12px !important;
        overflow: hidden !important;
        width: 800px !important;
    }
    #recordPaymentModal .btn, #recordPaymentModal .form-control, #recordPaymentModal select {
        border-radius: 8px !important;
    }
    @media (max-width: 820px) {
        #recordPaymentModal .modal-dialog, #recordPaymentModal .modal-content {
            width: 95% !important;
            max-width: 95% !important;
            margin: 1rem auto !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    var debounceTimeout;

    function performSearch(instant = false) {
        var searchValue = document.getElementById('searchInput').value.trim();
        var statusValue = document.getElementById('paymentStatusFilter').value;
        
        var params = [];
        if (searchValue) {
            params.push('search=' + encodeURIComponent(searchValue));
        }
        if (statusValue) {
            params.push('payment_status=' + encodeURIComponent(statusValue));
        }
        
        var url = '{{ route("tyre.refilling.index") }}' + (params.length ? '?' + params.join('&') : '');
        
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
        document.getElementById('clearSearch').style.display = 'none';
        performSearch(true);
    }

    // Show/hide clear button based on input
    document.addEventListener('DOMContentLoaded', function() {
        var searchInput = document.getElementById('searchInput');
        var clearBtn = document.getElementById('clearSearch');
        
        if (searchInput.value.length > 0) {
            clearBtn.style.display = 'block';
        }
        
        searchInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                clearBtn.style.display = 'block';
            } else {
                clearBtn.style.display = 'none';
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

<!-- Record Payment Modal -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-labelledby="recordPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="recordPaymentForm" method="POST" action="">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="recordPaymentModalLabel"><i class="fas fa-hand-holding-usd me-2"></i>Record Refilling Payment</h5>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; color: white; font-size: 20px; cursor: pointer;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Order Number</label>
                            <input type="text" id="modalOrderNumber" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Total Cost (Rs.)</label>
                            <input type="text" id="modalTotalCost" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold">Paid Amount *</label>
                            <input type="number" step="0.01" name="paid_amount" id="modalPaidAmount" class="form-control" required min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Payment Method *</label>
                            <select name="payment_method" id="modalPaymentMethod" class="form-control" required>
                                <option value="">-- Select Method --</option>
                                <option value="cash">Cash</option>
                                <option value="online">Bank Transfer</option>
                                <option value="check">Cheque</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="modalPaymentReferenceGroup" style="display: none;">
                            <label class="form-label fw-bold" id="modalPaymentReferenceLabel">Payment Reference</label>
                            <input type="text" name="payment_reference" id="modalPaymentReference" class="form-control" placeholder="Cheque # / Trans ID">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Payment Date *</label>
                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Notes</label>
                        <textarea name="payment_notes" class="form-control" rows="2" placeholder="Any additional details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Payment</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Payment modal listener
        $('.btn-record-payment').on('click', function() {
            let id = $(this).data('id');
            let orderNumber = $(this).data('number');
            let total = parseFloat($(this).data('total')) || 0;
            let paid = parseFloat($(this).data('paid')) || 0;
            
            $('#modalOrderNumber').val(orderNumber);
            $('#modalTotalCost').val('Rs. ' + total.toFixed(2));
            $('#modalPaidAmount').val((total - paid).toFixed(2));
            
            let actionUrl = '{{ route("tyre.refilling.payment.store", ":id") }}'.replace(':id', id);
            $('#recordPaymentForm').attr('action', actionUrl);
            
            // Reset reference block
            $('#modalPaymentMethod').val('').trigger('change');
            
            $('#recordPaymentModal').modal('show');
        });

        $('#modalPaymentMethod').on('change', function() {
            let method = $(this).val();
            if (method) {
                $('#modalPaymentReferenceGroup').show();
                if (method === 'online') {
                    $('#modalPaymentReferenceLabel').html('Transaction Reference *');
                    $('#modalPaymentReference').attr('placeholder', 'Enter bank transaction reference').attr('required', 'required');
                } else if (method === 'check') {
                    $('#modalPaymentReferenceLabel').html('Cheque Number *');
                    $('#modalPaymentReference').attr('placeholder', 'Enter cheque number').attr('required', 'required');
                } else {
                    $('#modalPaymentReferenceLabel').html('Payment Reference');
                    $('#modalPaymentReference').attr('placeholder', 'Optional reference info').removeAttr('required');
                }
            } else {
                $('#modalPaymentReferenceGroup').hide();
                $('#modalPaymentReference').removeAttr('required').val('');
            }
        });
    });
</script>
@endpush
@endsection