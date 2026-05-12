<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $product->product_name }} Chart</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .footer { margin-top: 20px; font-size: 10px; text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $product->product_name }} Chart</h2>
        <h3>Date Range: {{ $fromDate }} to {{ $toDate }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>Balance</th>
                <th>Stock In Date</th>
                <th>Stock In Amount</th>
                <th>Company/Info</th>
                <th>Balance</th>
                <th>Date</th>
                <th>Vehicle Number Issued</th>
                <th>Amount ({{ $reportData[0]['unit'] ?? 'L' }}) Issued</th>
                <th>Remarks (KM /Note etc)</th>
                <th>End Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $row)
            <tr>
                <td>{{ $row['opening_balance'] ?? '-' }}</td>
                <td>{{ $row['stock_in_date'] ?? '-' }}</td>
                <td>{{ $row['stock_in_amount'] ?? '-' }}</td>
                <td>{{ $row['company_info'] ?? '-' }}</td>
                <td>{{ $row['balance'] ?? '-' }}</td>
                <td>{{ $row['issue_date'] ?? '-' }}</td>
                <td>{{ $row['vehicle'] ?? '-' }}</td>
                <td>{{ $row['issue_amount'] ?? '-' }}</td>
                <td>{{ $row['remarks'] ?? '-' }}</td>
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

    <div class="footer">
        <p>Generated on: {{ now()->format('d.m.Y H:i') }}</p>
    </div>
</body>
</html>