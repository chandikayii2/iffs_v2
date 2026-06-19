<!-- resources/views/tire/issue/show.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-eye me-2"></i>Issue Note Details</h4>
        <h6>{{ $issueNote->issue_note_number }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tire.issue.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
        <a href="{{ route('tire.issue.edit', $issueNote->id) }}" class="btn btn-primary">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <a href="{{ route('tire.issue.pdf', $issueNote->id) }}" class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf me-1"></i> PDF
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Issue Note No</label>
                    <p><strong>{{ $issueNote->issue_note_number }}</strong></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Issue Date</label>
                    <p><strong>{{ $issueNote->issue_date->format('d-m-Y') }}</strong></p>
                </div>
            </div>
        </div>

        <hr>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tire Serial No</th>
                        <th>Vehicle No</th>
                        <th>Consumed Mileage (km)</th>
                        <th>Tire Size</th>
                        <th>Tire Brand</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($issueNote->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <a href="{{ route('tire.inventory.show', $item->tire_id) }}" class="text-primary">
                                {{ $item->tire->serial_number }}
                            </a>
                        </td>
                        <td>
                            @if($item->vehicle)
                                <span class="badge badge-soft-primary">{{ $item->vehicle->lorry_number }}</span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>{{ number_format($item->consumed_mileage) }}</td>
                        <td>{{ $item->tire->size }}</td>
                        <td>{{ $item->tire->brand }}</td>
                        <td>{{ $item->remark ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection