@include('layouts.header')
<div class="page-wrapper">
    <div class="content">
    <button class="btn btn-secondary back-button d-flex align-items-center"
                onclick="window.location.href='{{ route('reports.product.form') }}'">
                <i class="fa fa-arrow-left me-1"></i> <!-- Added me-1 for margin between icon and text -->
                Back
            </button>
        <h2 class="text-center">{{ $product->product_name ?? '' }} Chart</h2>
        <h4 class="text-center mb-2">Date Range: {{ $fromDate ?? '' }} to {{ $toDate ?? '' }}</h4>
        
        <div class="mb-3 text-center">
        
            <a href="{{ route('reports.product.download', [
                'product_id' => $product->id ?? '',
                'from_date' => request('from_date', ''),
                'to_date' => request('to_date', ''),
                'date_range' => request('date_range', 'this_week'),
                'download' => 1
            ]) }}" class="btn btn-success">Download PDF</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Balance</th>
                        <th>Stock In Date</th>
                        <th>Stock In Amount</th>
                        <th>Company/Info</th>
                        <th>Balance</th>
                        <th>Date</th>
                        <th>Issue D & T</th>
                        <th>Vehicle Number Issued</th>
                        <th>Amount ({{ $reportData[0]['unit'] ?? 'L' }}) Issued</th>
                        <th>Remarks (KM /Note etc)</th>
                        <th>End Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData ?? [] as $row)
                    <tr>
                        <td>{{ $row['opening_balance'] ?? '' }}</td>
                        <td>{{ $row['stock_in_date'] ?? '' }}</td>
                        <td>{{ $row['stock_in_amount'] ?? '' }}</td>
                        <td>{{ $row['company_info'] ?? '' }}</td>
                        <td>{{ $row['balance'] ?? '' }}</td>
                        <td>{{ $row['issue_date'] ?? '' }}</td>
                        <td>{{ $row['created_at'] ?? '' }}</td>
                        <td>{{ $row['vehicle'] ?? '' }}</td>
                        <td>{{ $row['issue_amount'] ?? '' }}</td>
                        <td>{{ $row['remarks'] ?? '' }}</td>
                        <td>
            @php
                $opening = floatval($row['opening_balance'] ?? 0);
                $stockIn = floatval($row['stock_in_amount_num'] ?? 0);
                $issue = floatval($row['issue_amount_num'] ?? 0);
                $endBalance = $opening + $stockIn - $issue;
                echo $endBalance . $row['unit'];
            @endphp
        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center">No transactions found for selected period</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>