@extends('tyre.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-circle-notch me-2"></i>{{ $title }}</h4>
        <h6>{{ $description }}</h6>
    </div>
    <div class="page-btn d-flex align-items-center gap-2">
        <a href="{{ route('tyre.category.pdf', $type) }}" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </a>
        <a href="{{ route('tyre.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">{{ $title }}</h5>
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
                        <th>Refills Done</th>
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
                                {{ $tyre->getLocationText() }}
                            </span>
                        </td>
                        <td>{{ $tyre->refill_count }}</td>
                        <td>{{ $tyre->max_refills }}</td>
                        <td>
                            <a href="{{ route('tyre.inventory.show', $tyre->id) }}" class="action-btn action-btn-view" title="View Passport">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
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
