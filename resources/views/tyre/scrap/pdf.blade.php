<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scrapped Tyres - {{ ucfirst($category) }}</title>
    <style>
        @page {
            margin: 15mm 15mm 20mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.4;
            font-size: 11px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 20px;
        }
        .header-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .logo-title h1 {
            color: #1b2850;
            font-size: 22px;
            font-weight: bold;
            margin: 0 0 4px 0;
        }
        .logo-title p {
            color: #7f8c8d;
            font-size: 12px;
            margin: 0;
        }
        .company-info {
            text-align: right;
            color: #555;
            font-size: 11px;
            line-height: 1.5;
        }
        .divider {
            border-bottom: 2px solid #1b2850;
            margin-bottom: 20px;
        }
        .report-title {
            font-size: 16px;
            font-weight: bold;
            color: #1b2850;
            margin-bottom: 20px;
            text-transform: uppercase;
            border-left: 4px solid #1b2850;
            padding-left: 10px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .data-table th {
            background-color: #1b2850;
            color: white;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 10px;
            border: 1px solid #1b2850;
            text-align: left;
        }
        .data-table td {
            padding: 7px 10px;
            border: 1px solid #e1e8ed;
            font-size: 10px;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f9fbfd;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
            border: none;
            page-break-inside: avoid;
        }
        .signature-table td {
            border: none;
            padding: 0;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-line {
            border-top: 1px solid #7f8c8d;
            width: 180px;
            margin: 0 auto;
            padding-top: 6px;
            font-size: 11px;
            color: #555;
        }
        .footer {
            position: fixed;
            bottom: -10mm;
            left: 0;
            right: 0;
            height: 10mm;
            border-top: 1px solid #e1e8ed;
            padding-top: 5px;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        .footer-table td {
            border: none;
            padding: 0;
            font-size: 9px;
            color: #95a5a6;
        }
        .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <div class="logo-title">
                    <h1>IFFS TRANSPORT</h1>
                    <p>Tyre Management System</p>
                </div>
            </td>
            <td style="width: 50%;">
                <div class="company-info">
                    <strong>Generated Date:</strong> {{ date('d-m-Y h:i A') }}<br>
                    <strong>Operator:</strong> {{ Auth::user()->name ?? 'System Admin' }}
                </div>
            </td>
        </tr>
    </table>
    
    <div class="divider"></div>
    
    <div class="report-title">
        @if($category === 'store')
            Scrapped Tyres in Store Report (Total: {{ $scrapTyres->count() }})
        @elseif($category === 'kurunagala')
            Scrapped Tyres in Kurunagala Report (Total: {{ $scrapTyres->count() }})
        @else
            Sold Scrapped Tyres Report (Total: {{ $scrapTyres->count() }})
        @endif
    </div>

    <table class="data-table">
        <thead>
            @if($category === 'sold')
                <tr>
                    <th>Serial Number</th>
                    <th>Brand</th>
                    <th>Size</th>
                    <th>Sold Date</th>
                    <th>Sale Price</th>
                    <th>Payment Method</th>
                    <th>Customer Details</th>
                </tr>
            @else
                <tr>
                    <th>Serial Number</th>
                    <th>Brand</th>
                    <th>Size</th>
                    <th>Casing Type</th>
                    <th>Scrap Date</th>
                    <th>Scrap Reason</th>
                    <th>Final Mileage</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @forelse($scrapTyres as $tyre)
                @if($category === 'sold')
                    <tr>
                        <td><strong>{{ $tyre->serial_number }}</strong></td>
                        <td>{{ $tyre->brand }}</td>
                        <td>{{ $tyre->size }}</td>
                        <td>{{ $tyre->scrapRecord && $tyre->scrapRecord->sold_date ? \Carbon\Carbon::parse($tyre->scrapRecord->sold_date)->format('d-m-Y') : 'N/A' }}</td>
                        <td>Rs.{{ number_format($tyre->scrapRecord->sale_price ?? 0, 2) }}</td>
                        <td>{{ ucfirst($tyre->scrapRecord->payment_method ?? 'N/A') }}</td>
                        <td>
                            <strong>{{ $tyre->scrapRecord->customer_name ?? 'N/A' }}</strong><br>
                            <span style="font-size: 9px; color: #555;">{{ $tyre->scrapRecord->customer_phone ?? '' }}</span>
                        </td>
                    </tr>
                @else
                    <tr>
                        <td><strong>{{ $tyre->serial_number }}</strong></td>
                        <td>{{ $tyre->brand }}</td>
                        <td>{{ $tyre->size }}</td>
                        <td>{{ ucfirst($tyre->type) }}</td>
                        <td>{{ $tyre->scrapRecord && $tyre->scrapRecord->scrap_date ? \Carbon\Carbon::parse($tyre->scrapRecord->scrap_date)->format('d-m-Y') : 'N/A' }}</td>
                        <td>{{ $tyre->scrapRecord->scrap_reason ?? 'N/A' }}</td>
                        <td>{{ number_format($tyre->scrapRecord->final_mileage ?? 0) }} km</td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="{{ $category === 'sold' ? 7 : 7 }}" style="text-align: center; color: #7f8c8d; padding: 20px 0;">No tyres found in this category.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <table class="signature-table">
        <tr>
            <td style="width: 40%;">
                <div style="height: 50px;"></div>
                <div class="signature-line">Prepared By</div>
            </td>
            <td style="width: 20%;"></td>
            <td style="width: 40%;">
                <div style="height: 50px;"></div>
                <div class="signature-line">Authorized By</div>
            </td>
        </tr>
    </table>
    
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td style="width: 50%;">IFFS TRANSPORT - Confidential Document</td>
                <td style="width: 50%; text-align: right;">Page <span class="page-number"></span></td>
            </tr>
        </table>
    </div>
</body>
</html>
