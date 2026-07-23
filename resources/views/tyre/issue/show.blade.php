<!-- resources/views/tyre/issue/show.blade.php -->
@extends('tyre.layouts.app')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div class="page-title">
        <h4><i class="fas fa-eye me-2"></i>Issue Note Details</h4>
        <h6>{{ $issueNote->issue_note_number }}</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('tyre.issue.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
        <a href="{{ route('tyre.issue.edit', $issueNote->id) }}" class="btn btn-primary">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <a href="{{ route('tyre.issue.pdf', $issueNote->id) }}" class="btn btn-danger" target="_blank">
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
                        <th>Tyre Serial No</th>
                        <th>Vehicle No</th>
                        <th>Consumed Mileage (km)</th>
                        <th>Tyre Size</th>
                        <th>Tyre Brand</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($issueNote->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <a href="{{ route('tyre.inventory.show', $item->tire_id) }}" class="text-primary">
                                {{ $item->tyre->serial_number }}
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
                        <td>{{ $item->tyre->size }}</td>
                        <td>{{ $item->tyre->brand }}</td>
                        <td>{{ $item->remark ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection