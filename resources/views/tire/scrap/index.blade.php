<!-- resources/views/tire/scrap/index.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-trash-alt me-2"></i>Scrap Management</h4>
        <h6>Manage scrapped tires</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.inventory.index') }}" class="btn btn-secondary">
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

<div class="card mt-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">Scrapped Tires List</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Serial Number</th>
                        <th>Brand</th>
                        <th>Size</th>
                        <th>Scrap Date</th>
                        <th>Reason</th>
                        <th>Final Mileage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($scrapTires as $tire)
                    <tr>
                        <td>{{ $tire->serial_number }}</td>
                        <td>{{ $tire->brand }}</td>
                        <td>{{ $tire->size }}</td>
                        <td>{{ $tire->scrapRecord->scrap_date ?? 'N/A' }}</td>
                        <td>{{ $tire->scrapRecord->scrap_reason ?? 'N/A' }}</td>
                        <td>{{ number_format($tire->scrapRecord->final_mileage ?? 0) }} km</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $scrapTires->links() }}
    </div>
</div>
@endsection