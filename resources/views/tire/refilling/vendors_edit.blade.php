<!-- resources/views/tire/refilling/vendors_edit.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-edit me-2"></i>Edit Vendor</h4>
        <h6>Update vendor information - {{ $vendor->name }}</h6>
    </div>
    <div class="page-btn">
         <a href="{{ route('tire.refilling.vendors.manage') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Orders
        </a>
        <!-- <a href="{{ route('tire.refilling.vendors.show', $vendor->id) }}" class="btn btn-info">
            <i class="fas fa-eye me-1"></i> View Details
        </a> -->
    </div>
</div>

<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">Edit Vendor Form</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('tire.refilling.vendors.update', $vendor->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $vendor->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Contact Person <span class="text-danger">*</span></label>
                        <input type="text" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" 
                               value="{{ old('contact_person', $vendor->contact_person) }}" required>
                        @error('contact_person')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                               value="{{ old('phone', $vendor->phone) }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $vendor->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>Address <span class="text-danger">*</span></label>
                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" 
                                  rows="2" required>{{ old('address', $vendor->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Update Vendor
                </button>
                <a href="{{ route('tire.refilling.vendors') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection