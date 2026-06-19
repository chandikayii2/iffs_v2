<!-- resources/views/tire/scrap/report.blade.php -->
@extends('tire.layouts.app')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Scrap Report</h4>
        <h6>Detailed scrap tires report</h6>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Scrap by Reason</h4>
            </div>
            <div class="card-body">
                <canvas id="reasonChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Scrap by Month</h4>
            </div>
            <div class="card-body">
                <canvas id="monthChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h4>Scrap Records</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Serial Number</th>
                        <th>Brand</th>
                        <th>Size</th>
                        <th>Reason</th>
                        <th>Final Mileage</th>
                        <th>Disposal Method</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($scrapRecords as $record)
                    <tr>
                        <td>{{ $record->scrap_date->format('d-m-Y') }}</td>
                        <td>{{ $record->tire->serial_number }}</td>
                        <td>{{ $record->tire->brand }}</td>
                        <td>{{ $record->tire->size }}</td>
                        <td>{{ $record->scrap_reason }}</td>
                        <td>{{ number_format($record->final_mileage ?? 0) }} km</td>
                        <td>{{ $record->disposal_method ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $scrapRecords->links() }}
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Scrap by reason chart
    const reasonCtx = document.getElementById('reasonChart').getContext('2d');
    const reasonData = @json($summary['by_reason']);
    new Chart(reasonCtx, {
        type: 'pie',
        data: {
            labels: reasonData.map(item => item.scrap_reason),
            datasets: [{
                data: reasonData.map(item => item.count),
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
            }]
        }
    });

    // Scrap by month chart
    const monthCtx = document.getElementById('monthChart').getContext('2d');
    const monthData = @json($summary['by_month']);
    new Chart(monthCtx, {
        type: 'bar',
        data: {
            labels: monthData.map(item => item.month),
            datasets: [{
                label: 'Tires Scrapped',
                data: monthData.map(item => item.count),
                backgroundColor: '#36A2EB'
            }]
        }
    });
</script>
@endpush
@endsection