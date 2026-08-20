<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tyres Subcategory Breakdown Report</title>
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
            margin-bottom: 25px;
            text-transform: uppercase;
            border-left: 4px solid #1b2850;
            padding-left: 10px;
        }
        .category-section {
            margin-bottom: 30px;
        }
        .category-title {
            font-size: 13px;
            font-weight: bold;
            color: #1b2850;
            margin-bottom: 10px;
            background: #f4f6f8;
            padding: 6px 10px;
            border-left: 3px solid #2ecc71;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
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
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 3px;
            text-transform: capitalize;
            text-align: center;
        }
        .bg-success {
            background-color: #e8f8f5;
            color: #27ae60;
        }
        .bg-primary {
            background-color: #ebf5fb;
            color: #2980b9;
        }
        .bg-warning {
            background-color: #fef9e7;
            color: #d35400;
        }
        .bg-info {
            background-color: #eaf2f8;
            color: #2471a3;
        }
        .badge-location {
            background-color: #f2f4f4;
            color: #7f8c8d;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
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
                    <h1>IFFS LOGISTICS</h1>
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
        Tyres Subcategory Breakdown Report
    </div>

    @php $hasData = false; @endphp

    @for($i = 0; $i <= $maxRounds; $i++)
        @php 
            $tyres = $groupedTyres[$i] ?? collect();
            $count = $tyres->count();
        @endphp
        
        @if($count > 0)
            @php $hasData = true; @endphp
            <div class="category-section">
                <div class="category-title">
                    @if($i === 0)
                        Brand New Tyres (0 refills) - Total: {{ $count }}
                    @else
                        @php
                            $suffix = 'th';
                            if ($i % 10 == 1 && $i % 100 != 11) $suffix = 'st';
                            elseif ($i % 10 == 2 && $i % 100 != 12) $suffix = 'nd';
                            elseif ($i % 10 == 3 && $i % 100 != 13) $suffix = 'rd';
                        @endphp
                        {{ $i }}{{ $suffix }} Round Dag - Total: {{ $count }}
                    @endif
                </div>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Serial Number</th>
                            <th>Brand</th>
                            <th>Size</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Current Location</th>
                            <th>Max Refills</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tyres as $tyre)
                            <tr>
                                <td><strong>{{ $tyre->serial_number }}</strong></td>
                                <td>{{ $tyre->brand }}</td>
                                <td>{{ $tyre->size }}</td>
                                <td>{{ ucfirst($tyre->type) }}</td>
                                <td>
                                    @if($tyre->status === 'new')
                                        <span class="badge bg-success">New</span>
                                    @elseif($tyre->status === 'in_use')
                                        <span class="badge bg-primary">In Use</span>
                                    @elseif($tyre->status === 'used')
                                        <span class="badge bg-warning">Used</span>
                                    @elseif($tyre->status === 'at_vendor')
                                        <span class="badge bg-info">At Vendor</span>
                                    @else
                                        <span class="badge badge-location">{{ ucfirst($tyre->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-location">
                                        @if($tyre->status === 'new' && $tyre->tire_type === 'original_casing')
                                            Used Casing Stock
                                        @elseif($tyre->status === 'used' && $tyre->refill_count > 0)
                                            Available for Use / Stock
                                        @else
                                            {{ $tyre->current_location ?? 'Store' }}
                                        @endif
                                    </span>
                                </td>
                                <td>{{ $tyre->max_refills }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endfor

    @if(!$hasData)
        <div style="text-align: center; padding: 40px 0; color: #7f8c8d; font-size: 13px;">
            No tyres found in breakdown subcategories.
        </div>
    @endif
    
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
                <td style="width: 50%;">IFFS Logistics - Confidential Document</td>
                <td style="width: 50%; text-align: right;">Page <span class="page-number"></span></td>
            </tr>
        </table>
    </div>
</body>
</html>
