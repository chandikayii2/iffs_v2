@extends('tyre.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-chart-pie me-2"></i>Tyres Subcategory Breakdown</h4>
        <h6>Round-by-round casing and retread status overview</h6>
    </div>
    <div class="page-btn d-flex align-items-center gap-2">
        <a href="{{ route('tyre.breakdown.pdf') }}" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>
        <a href="{{ route('tyre.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>
</div>

<!-- Category Tabs -->
<ul class="nav nav-tabs mb-4" id="breakdownCategoryTab" role="tablist" style="flex-wrap: wrap;">
    <li class="nav-item">
        <a class="nav-link {{ $category === 0 ? 'active fw-bold' : '' }}" href="{{ route('tyre.breakdown', ['refill_count' => 0]) }}">
            <i class="fas fa-tag me-1 text-success"></i> Brand New ({{ $stats['brand_new_0'] ?? 0 }})
        </a>
    </li>
    @for($i = 1; $i <= $maxRounds; $i++)
        @php
            $colors = ['text-primary', 'text-info', 'text-warning', 'text-danger', 'text-secondary', 'text-success', 'text-dark'];
            $color = $colors[($i - 1) % count($colors)];
            
            $suffix = 'th';
            if ($i % 10 == 1 && $i % 100 != 11) $suffix = 'st';
            elseif ($i % 10 == 2 && $i % 100 != 12) $suffix = 'nd';
            elseif ($i % 10 == 3 && $i % 100 != 13) $suffix = 'rd';
        @endphp
        <li class="nav-item">
            <a class="nav-link {{ $category === $i ? 'active fw-bold' : '' }}" href="{{ route('tyre.breakdown', ['refill_count' => $i]) }}">
                <i class="fas fa-sync me-1 {{ $color }}"></i> {{ $i }}{{ $suffix }} Round Dag ({{ $stats['round_' . $i] ?? 0 }})
            </a>
        </li>
    @endfor
</ul>

<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            @if($category === 0)
                Brand New Tyres List (0 refills)
            @else
                @php
                    $suffix = 'th';
                    if ($category % 10 == 1 && $category % 100 != 11) $suffix = 'st';
                    elseif ($category % 10 == 2 && $category % 100 != 12) $suffix = 'nd';
                    elseif ($category % 10 == 3 && $category % 100 != 13) $suffix = 'rd';
                @endphp
                {{ $category }}{{ $suffix }} Round Retreaded Tyres List
            @endif
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Serial Number</th>
                        <th>Brand</th>
                        <th>Size</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Current Location</th>
                        <th>Max Refills</th>
                        <th>Action</th>
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
                        <td>{{ ucfirst($tyre->type) }}</td>
                        <td>
                            @if($tyre->status === 'new')
                                <span class="badge bg-success">New</span>
                            @elseif($tyre->status === 'in_use')
                                <span class="badge bg-primary">In Use</span>
                            @elseif($tyre->status === 'used')
                                <span class="badge bg-warning text-dark">Used</span>
                            @elseif($tyre->status === 'at_vendor')
                                <span class="badge bg-info">At Vendor</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($tyre->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-soft-primary">
                                {{ $tyre->current_location ?? 'Store' }}
                            </span>
                        </td>
                        <td>{{ $tyre->max_refills }}</td>
                        <td>
                            <a href="{{ route('tyre.inventory.show', $tyre->id) }}" class="action-btn action-btn-view" title="View Passport">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-circle-notch fa-2x text-muted mb-2"></i>
                            <p class="text-muted">No tyres found in this category.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $tyres->links() }}
    </div>
</div>
@endsection
