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
            <i class="fas fa-plus-circle" style="margin-right: 5px;"></i> New Order
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <!-- Search Bar -->
        <div class="search-container" style="margin-bottom: 20px;">
            <div class="search-input" style="flex: 1; position: relative;">
                <input type="text" id="searchInput" placeholder="Search by Order No, Vendor, Tire Serial, Status..." value="{{ request('search') }}" 
                       style="width: 100%; padding: 10px 45px 10px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
                <button class="clear-search" id="clearSearch" onclick="clearSearch()" 
                        style="position: absolute; right: 40px; top: 50%; transform: translateY(-50%); color: #e74c3c; cursor: pointer; display: {{ request('search') ? 'block' : 'none' }}; background: none; border: none; font-size: 16px;">✕</button>
                <span class="search-icon" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #95a5a6;">
                    <i class="fas fa-search"></i>
                </span>
            </div>
            <button class="btn-search" onclick="performSearch()" 
                    style="background: #1b2850; color: white; border: none; padding: 10px 25px; border-radius: 8px; cursor: pointer; margin-left: 10px; white-space: nowrap;">
                <i class="fas fa-search" style="margin-right: 8px;"></i> Search
            </button>
            <a href="{{ route('tire.refilling.index') }}" class="btn-reset" 
               style="background: #95a5a6; color: white; border: none; padding: 10px 25px; border-radius: 8px; cursor: pointer; margin-left: 10px; white-space: nowrap; text-decoration: none; display: inline-block;">
                <i class="fas fa-undo" style="margin-right: 8px;"></i> Reset
            </a>
        </div>

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
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('tire.refilling.show', $order->id) }}" class="text-primary fw-bold">
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
                            <div class="action-buttons">
                                <!-- View Button -->
                                <a href="{{ route('tire.refilling.show', $order->id) }}" class="action-btn action-btn-view" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- PDF Button -->
                                <a href="{{ route('tire.refilling.pdf', $order->id) }}" class="action-btn action-btn-download" title="Download PDF" target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                
                                <!-- Receive Order Button (only for sent orders) -->
                                @if($order->status == 'sent')
                                <a href="{{ route('tire.refilling.receive', $order->id) }}" class="action-btn action-btn-success" title="Receive Order">
                                    <i class="fas fa-check-circle"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
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
    </div>
</div>

@push('styles')
<style>
    .action-btn-download {
        color: #E74C3C;
    }
    .action-btn-download:hover {
        background: rgba(231, 76, 60, 0.1);
        color: #C0392B;
    }
    .action-btn-success {
        color: #2ECC71;
    }
    .action-btn-success:hover {
        background: rgba(46, 204, 113, 0.1);
        color: #27AE60;
    }
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
</style>
@endpush

@push('scripts')
<script>
    function performSearch() {
        var searchValue = document.getElementById('searchInput').value.trim();
        if (searchValue) {
            window.location.href = '{{ route("tire.refilling.index") }}?search=' + encodeURIComponent(searchValue);
        } else {
            window.location.href = '{{ route("tire.refilling.index") }}';
        }
    }

    function clearSearch() {
        document.getElementById('searchInput').value = '';
        document.getElementById('clearSearch').style.display = 'none';
        window.location.href = '{{ route("tire.refilling.index") }}';
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
        });

        // Enter key to search
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    });
</script>
@endpush
@endsection