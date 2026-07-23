<!-- resources/views/tyre/scrap/index.blade.php -->
@extends('tyre.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-trash-alt me-2"></i>Scrap Management</h4>
        <h6>Manage scrapped tyres</h6>
    </div>
    <div class="page-btn d-flex align-items-center gap-2">
        <a href="{{ route('tyre.scrap.export-pdf', ['category' => request('category', 'store')]) }}" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>
        <a href="{{ route('tyre.inventory.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Inventory
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 col-sm-6 col-12">
        <div class="dash-count das4">
            <div class="dash-counts">
                <h4>{{ $stats['total_scrap'] ?? 0 }}</h4>
                <h5>Total Scrap</h5>
            </div>
            <div class="dash-imgs">
                <i class="fas fa-trash"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-sm-6 col-12">
        <div class="dash-count das5">
            <div class="dash-counts">
                <h4>{{ $stats['scrap_this_month'] ?? 0 }}</h4>
                <h5>This Month</h5>
            </div>
            <div class="dash-imgs">
                <i class="fas fa-calendar"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-sm-6 col-12">
        <div class="dash-count das6">
            <div class="dash-counts">
                <h4>{{ number_format($stats['avg_life_km'] ?? 0) }} km</h4>
                <h5>Average Life</h5>
            </div>
            <div class="dash-imgs">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
</div>

<!-- Category Tabs -->
<ul class="nav nav-tabs mb-4" id="scrapCategoryTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ request('category', 'store') === 'store' ? 'active fw-bold' : '' }}" href="{{ route('tyre.scrap.index', ['category' => 'store']) }}">
            <i class="fas fa-warehouse me-1 text-primary"></i> Store ({{ $stats['store_count'] ?? 0 }})
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ request('category') === 'kurunagala' ? 'active fw-bold' : '' }}" href="{{ route('tyre.scrap.index', ['category' => 'kurunagala']) }}">
            <i class="fas fa-shipping-fast me-1 text-warning"></i> Kurunagala ({{ $stats['kurunagala_count'] ?? 0 }})
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ request('category') === 'sold' ? 'active fw-bold' : '' }}" href="{{ route('tyre.scrap.index', ['category' => 'sold']) }}">
            <i class="fas fa-hand-holding-usd me-1 text-success"></i> Sold ({{ $stats['sold_count'] ?? 0 }})
        </a>
    </li>
</ul>

<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            @if(request('category', 'store') === 'store')
                Scrapped Tyres in Store
            @elseif(request('category') === 'kurunagala')
                Scrapped Tyres in Kurunagala
            @else
                Sold Scrapped Tyres
            @endif
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    @if(request('category', 'store') === 'sold')
                        <tr>
                            <th>Serial Number</th>
                            <th>Brand</th>
                            <th>Size</th>
                            <th>Sold Date</th>
                            <th>Sale Price</th>
                            <th>Payment Method</th>
                            <th>Customer Details</th>
                        </tr>
                    @else
                        <tr>
                            <th>Serial Number</th>
                            <th>Brand</th>
                            <th>Size</th>
                            <th>Scrap Date</th>
                            <th>Reason</th>
                            <th>Final Mileage</th>
                            @if(request('category', 'store') === 'store')
                                <th width="280">Actions</th>
                            @endif
                        </tr>
                    @endif
                </thead>
                <tbody>
                    @forelse($scrapTyres as $tyre)
                    <tr>
                        <td>{{ $tyre->serial_number }}</td>
                        <td>{{ $tyre->brand }}</td>
                        <td>{{ $tyre->size }}</td>
                        
                        @if(request('category', 'store') === 'sold')
                            <td>{{ $tyre->scrapRecord->sale_date ? \Carbon\Carbon::parse($tyre->scrapRecord->sale_date)->format('d-m-Y') : 'N/A' }}</td>
                            <td><strong>Rs.{{ number_format($tyre->scrapRecord->sale_price ?? 0, 2) }}</strong></td>
                            <td><span class="badge bg-secondary">{{ ucfirst($tyre->scrapRecord->sale_payment_method ?? 'N/A') }}</span></td>
                            <td>{{ $tyre->scrapRecord->buyer_name ?? 'N/A' }}</td>
                        @else
                            <td>{{ $tyre->scrapRecord->scrap_date ? \Carbon\Carbon::parse($tyre->scrapRecord->scrap_date)->format('d-m-Y') : 'N/A' }}</td>
                            <td>{{ $tyre->scrapRecord->scrap_reason ?? 'N/A' }}</td>
                            <td>{{ number_format($tyre->scrapRecord->final_mileage ?? 0) }} km</td>
                            @if(request('category', 'store') === 'store')
                                <td>
                                    <!-- Send to Kurunagala Action -->
                                    <form action="{{ route('tyre.scrap.send-kurunagala', $tyre->scrapRecord->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to send this tyre to Kurunagala?')">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm">
                                            <i class="fas fa-shipping-fast"></i> Send to Kurunagala
                                        </button>
                                    </form>
                                    
                                    <!-- Sell Action -->
                                    <button type="button" class="btn btn-success btn-sm btn-sell" data-id="{{ $tyre->scrapRecord->id }}" data-serial="{{ $tyre->serial_number }}">
                                        <i class="fas fa-hand-holding-usd"></i> Sell
                                    </button>
                                </td>
                            @endif
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-trash fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No tyres found in this category.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $scrapTyres->links() }}
    </div>
</div>

<!-- Sell Scrap Modal -->
<div class="modal fade" id="sellScrapModal" tabindex="-1" aria-labelledby="sellScrapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="sellScrapForm" method="POST" action="">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="sellScrapModalLabel"><i class="fas fa-hand-holding-usd me-2"></i>Record Scrap Tyre Sale</h5>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; color: white; font-size: 20px; cursor: pointer;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tyre Serial Number</label>
                            <input type="text" id="modalTyreSerial" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Sale Price (Rs.) *</label>
                            <input type="number" step="0.01" name="sale_price" class="form-control" placeholder="Enter sale amount" required min="0">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Sold Date *</label>
                            <input type="date" name="sold_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Payment Method *</label>
                            <select name="sale_payment_method" id="modalSalePaymentMethod" class="form-control" required>
                                <option value="">Select Payment Method</option>
                                <option value="cash">Cash</option>
                                <option value="online">Online / Bank Transfer</option>
                                <option value="check">Cheque</option>
                            </select>
                        </div>
                    </div>
                    <div class="row" id="salePaymentReferenceRow" style="display: none;">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-bold" id="salePaymentReferenceLabel">Payment Reference</label>
                            <input type="text" name="sale_reference" id="modalSalePaymentReference" class="form-control" placeholder="Cheque # / Trans ID">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Customer Details *</label>
                        <textarea name="customer_details" class="form-control" rows="3" placeholder="Enter customer name, phone, company, etc..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm Sale</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.btn-sell').on('click', function() {
            let id = $(this).data('id');
            let serial = $(this).data('serial');
            $('#modalTyreSerial').val(serial);
            $('#sellScrapForm').attr('action', '{{ route("tyre.scrap.sell", "") }}/' + id);
            
            // Reset payment method and reference toggles
            $('#modalSalePaymentMethod').val('').trigger('change');
            
            $('#sellScrapModal').modal('show');
        });

        $('#modalSalePaymentMethod').on('change', function() {
            let method = $(this).val();
            if (method) {
                $('#salePaymentReferenceRow').show();
                if (method === 'online') {
                    $('#salePaymentReferenceLabel').html('Transaction Reference *');
                    $('#modalSalePaymentReference').attr('placeholder', 'Enter bank transaction reference').attr('required', 'required');
                } else if (method === 'check') {
                    $('#salePaymentReferenceLabel').html('Cheque Number *');
                    $('#modalSalePaymentReference').attr('placeholder', 'Enter cheque number').attr('required', 'required');
                } else {
                    $('#salePaymentReferenceLabel').html('Payment Reference');
                    $('#modalSalePaymentReference').attr('placeholder', 'Optional reference info').removeAttr('required');
                }
            } else {
                $('#salePaymentReferenceRow').hide();
                $('#modalSalePaymentReference').removeAttr('required').val('');
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    #sellScrapModal {
        z-index: 1060 !important;
    }
    #sellScrapModal .modal-dialog {
        max-width: 800px !important;
        width: 800px !important;
        margin: 1.75rem auto !important;
        display: flex !important;
        justify-content: center !important;
    }
    #sellScrapModal .modal-content {
        border-radius: 12px !important;
        overflow: hidden !important;
        width: 800px !important;
    }
    #sellScrapModal .btn, #sellScrapModal .form-control, #sellScrapModal select {
        border-radius: 8px !important;
    }
    @media (max-width: 820px) {
        #sellScrapModal .modal-dialog, #sellScrapModal .modal-content {
            width: 95% !important;
            max-width: 95% !important;
            margin: 1rem auto !important;
        }
    }
</style>
@endpush
@endsection