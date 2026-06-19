<!-- resources/views/tire/passport/search.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-passport me-2"></i>Tire Passport Search</h4>
        <h6>Search for tire by serial number</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Search Tire</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('tire.passport.lookup') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-10">
                    <div class="form-group">
                        <label>Enter Tire Serial Number</label>
                        <input type="text" name="serial_number" class="form-control form-control-lg" 
                               placeholder="e.g., TIRE-001" required>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">About Tire Passport</h5>
    </div>
    <div class="card-body">
        <p>The Tire Passport provides complete lifecycle history of any tire including:</p>
        <ul>
            <li><i class="fas fa-check-circle text-success me-2"></i>Purchase information (date, price)</li>
            <li><i class="fas fa-check-circle text-success me-2"></i>All vehicle allocations and removals</li>
            <li><i class="fas fa-check-circle text-success me-2"></i>Mileage tracking at installation and removal</li>
            <li><i class="fas fa-check-circle text-success me-2"></i>Refilling/retreading history</li>
            <li><i class="fas fa-check-circle text-success me-2"></i>Scrap records if applicable</li>
        </ul>
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle me-2"></i>
            Enter a tire serial number above to view its complete lifecycle history.
        </div>
    </div>
</div>
@endsection