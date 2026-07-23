<!-- resources/views/tyre/scrap/pdf.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scrapped Tyres - {{ ucfirst($category) }}</title>
    <!-- Include FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }
        
        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .btn-print {
            background: #2ECC71;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-print:hover {
            background: #27AE60;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.3);
        }
        
        .btn-close {
            background: #95a5a6;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            margin-left: 10px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-close:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
        }
        
        .pdf-wrapper {
            max-width: 1000px;
            margin: 20px auto 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 40px;
        }
        
        .pdf-content {
            position: relative;
        }
        
        /* Top Bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #34495e;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .logo-title h1 {
            color: #2c3e50;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .logo-title p {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .company-info {
            text-align: right;
            color: #7f8c8d;
            font-size: 13px;
            line-height: 1.5;
        }
        
        .report-title {
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Table Styling */
        .table-responsive {
            margin-bottom: 30px;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        
        .table th, .table td {
            border: 1px solid #bdc3c7;
            padding: 10px 12px;
            text-align: left;
        }
        
        .table th {
            background-color: #f8f9fa;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .table tr:nth-child(even) {
            background-color: #fcfcfc;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 4px;
        }
        
        .bg-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        /* Footer */
        .footer {
            margin-top: 50px;
            border-top: 1px solid #bdc3c7;
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #7f8c8d;
        }
        
        .signature-block {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-line {
            width: 200px;
            border-top: 1px solid #000;
            text-align: center;
            padding-top: 5px;
            font-size: 12px;
            color: #2c3e50;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
            
            .pdf-wrapper {
                box-shadow: none;
                border-radius: 0;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print me-1"></i> Print / Save as PDF
        </button>
        <a href="{{ route('tyre.scrap.index', ['category' => $category]) }}" class="btn-close">
            <i class="fas fa-times me-1"></i> Close
        </a>
    </div>
    
    <div class="pdf-wrapper">
        <div class="pdf-content">
            <div class="top-bar">
                <div class="logo-title">
                    <h1>IFFS LOGISTICS</h1>
                    <p>Tyre Management System</p>
                </div>
                <div class="company-info">
                    <strong>Generated Date:</strong> {{ date('d-m-Y H:i A') }}<br>
                    <strong>Operator:</strong> {{ Auth::user()->name ?? 'System Admin' }}
                </div>
            </div>
            
            <div class="report-title">
                @if($category === 'store')
                    Scrapped Tyres in Store Report
                @elseif($category === 'kurunagala')
                    Scrapped Tyres in Kurunagala Report
                @else
                    Sold Scrapped Tyres Report
                @endif
            </div>
            
            <div class="table-responsive">
                <table class="table">
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
                                    <td>{{ $tyre->scrapRecord->sale_date ? \Carbon\Carbon::parse($tyre->scrapRecord->sale_date)->format('d-m-Y') : 'N/A' }}</td>
                                    <td>Rs.{{ number_format($tyre->scrapRecord->sale_price ?? 0, 2) }}</td>
                                    <td><span class="badge bg-secondary">{{ ucfirst($tyre->scrapRecord->sale_payment_method ?? 'N/A') }}</span></td>
                                    <td>{{ $tyre->scrapRecord->buyer_name ?? 'N/A' }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td><strong>{{ $tyre->serial_number }}</strong></td>
                                    <td>{{ $tyre->brand }}</td>
                                    <td>{{ $tyre->size }}</td>
                                    <td>{{ $tyre->scrapRecord->scrap_date ? \Carbon\Carbon::parse($tyre->scrapRecord->scrap_date)->format('d-m-Y') : 'N/A' }}</td>
                                    <td>{{ $tyre->scrapRecord->scrap_reason ?? 'N/A' }}</td>
                                    <td>{{ number_format($tyre->scrapRecord->final_mileage ?? 0) }} km</td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="{{ $category === 'sold' ? 7 : 6 }}" style="text-align: center;">No tyres found in this category.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="signature-block">
                <div class="signature-line">
                    Prepared By
                </div>
                <div class="signature-line">
                    Authorized By
                </div>
            </div>
            
            <div class="footer">
                <div>IFFS Logistics - Confidential Document</div>
                <div>Page 1 of 1</div>
            </div>
        </div>
    </div>
</body>
</html>
