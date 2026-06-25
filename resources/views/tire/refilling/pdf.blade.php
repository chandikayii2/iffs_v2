<!-- resources/views/tire/refilling/pdf.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refilling Order - {{ $order->order_number }}</title>
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
        
        .btn-download {
            background: #E74C3C;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-download:hover {
            background: #C0392B;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
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
            margin-left: 10px;
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
        }
        
        .btn-close:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
        }
        
        .pdf-wrapper {
            max-width: 900px;
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
            border-bottom: 3px solid #1b2850;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        
        .company-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .company-logo-wrapper {
            flex-shrink: 0;
        }
        
        .company-logo-img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }
        
        .company-details h2 {
            color: #1b2850;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .company-details p {
            margin: 2px 0;
            color: #555;
            font-size: 13px;
            line-height: 1.5;
        }
        
        .doc-number {
            text-align: right;
            min-width: 180px;
        }
        
        .doc-number .label {
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
        }
        
        .doc-number .number {
            font-size: 20px;
            font-weight: 700;
            color: #1b2850;
            border: 2px solid #1b2850;
            padding: 6px 15px;
            border-radius: 6px;
            margin-top: 5px;
            display: inline-block;
            background: #f8f9fa;
        }
        
        /* Title */
        .title-section {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .title-section h1 {
            font-size: 28px;
            color: #1b2850;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        .title-section .subtitle {
            color: #888;
            font-size: 14px;
            margin-top: 3px;
        }
        
        .title-section .date {
            color: #999;
            font-size: 13px;
            margin-top: 3px;
        }
        
        /* Details Table */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0 25px;
        }
        
        .details-table td {
            padding: 10px 15px;
            border: 1px solid #e0e0e0;
        }
        
        .details-table .label {
            font-weight: 600;
            color: #1b2850;
            background: #f8f9fa;
            width: 20%;
        }
        
        .details-table .value {
            color: #333;
            width: 30%;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0 25px;
        }
        
        .items-table th {
            background: #1b2850;
            color: white;
            padding: 10px 12px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
        }
        
        .items-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .items-table tr:hover td {
            background: #f8f9fa;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        
        .status-badge.sent { background: #fff3cd; color: #856404; }
        .status-badge.processing { background: #cce5ff; color: #004085; }
        .status-badge.received { background: #d4edda; color: #155724; }
        
        /* Footer */
        .footer {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
        }
        
        .footer p {
            color: #999;
            font-size: 11px;
            margin: 2px 0;
        }
        
        .footer .timestamp {
            color: #bbb;
            font-size: 10px;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .pdf-wrapper {
                box-shadow: none;
                border-radius: 0;
                padding: 30px;
                margin: 0;
            }
        }
        
        @media (max-width: 768px) {
            .pdf-wrapper {
                padding: 20px;
                margin: 10px;
            }
            .top-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            .company-info {
                flex-direction: row;
                width: 100%;
            }
            .company-logo-img {
                width: 50px;
                height: 50px;
            }
            .company-details h2 {
                font-size: 16px;
            }
            .doc-number {
                text-align: left;
                width: 100%;
            }
            .details-table td {
                padding: 6px 10px;
            }
            .title-section h1 {
                font-size: 22px;
            }
            .no-print {
                display: flex;
                flex-direction: column;
                gap: 10px;
                align-items: center;
            }
            .btn-download, .btn-print, .btn-close {
                width: 100%;
                max-width: 250px;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Action Buttons - No Print -->
    <div class="no-print">
        <button onclick="downloadPDF()" class="btn-download" id="downloadBtn">
            Download PDF
        </button>
        <button onclick="window.print()" class="btn-print">
            Print Order
        </button>
        <button onclick="window.close()" class="btn-close">
            Close
        </button>
    </div>
    
    <!-- PDF Content -->
    <div class="pdf-wrapper" id="pdfContent">
        <div class="pdf-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <div class="company-info">
                    <!-- <div class="company-logo-wrapper">
                        <img src="{{ asset('assets/admin/img/ilogo.jpg') }}" alt="IFFS Logo" class="company-logo-img">
                    </div> -->
                    <div class="company-details">
                        <h2>Inter Freight Forwarding Service (Pvt) Ltd</h2>
                        <p>No. 789, Samurdhi Mawatha, Heiyanthuduwa, Sapugaskanda, Sri Lanka</p>
                        <p>No. 12, 2nd Floor, Keyzer Street, Colombo 11, Sri Lanka</p>
                        <p>Website: www.iffs.idealsoft.us</p>
                    </div>
                </div>
                <div class="doc-number">
                    <span class="label">Order No.</span>
                    <span class="number">{{ $order->order_number }}</span>
                </div>
            </div>
            
            <!-- Title -->
            <div class="title-section">
                <h1>Refilling Order</h1>
                <div class="subtitle">Tire Refilling/Retreading Order</div>
                <div class="date">Date: {{ date('l, F d, Y') }}</div>
            </div>
            
            <!-- Details -->
            <table class="details-table">
                <tr>
                    <td class="label">Order Number</td>
                    <td class="value"><strong>{{ $order->order_number }}</strong></td>
                    <td class="label">Status</td>
                    <td class="value">
                        <span class="status-badge {{ $order->status }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Vendor</td>
                    <td class="value"><strong>{{ $order->vendor->name }}</strong></td>
                    <td class="label">Contact</td>
                    <td class="value">{{ $order->vendor->contact_person }} ({{ $order->vendor->phone }})</td>
                </tr>
                <tr>
                    <td class="label">Sent Date</td>
                    <td class="value">{{ $order->sent_date->format('d-m-Y') }}</td>
                    <td class="label">Received Date</td>
                    <td class="value">{{ $order->received_date ? $order->received_date->format('d-m-Y') : 'Not Received' }}</td>
                </tr>
                @if($order->received_date)
                <tr>
                    <td class="label">Total Cost</td>
                    <td class="value" colspan="3"><strong>Rs. {{ number_format($order->total_cost ?? 0, 2) }}</strong></td>
                </tr>
                @endif
            </table>
            
            <!-- Tires Table -->
            <h4 style="color: #1b2850; margin-bottom: 10px;">Tires for Refilling</h4>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tire Serial No</th>
                        <th>Brand</th>
                        <th>Size</th>
                        <th>Type</th>
                        <th>Refill Count</th>
                        <th>Refilling Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->tires as $index => $tire)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $tire->serial_number }}</strong></td>
                        <td>{{ $tire->brand }}</td>
                        <td>{{ $tire->size }}</td>
                        <td>{{ $tire->type }}</td>
                        <td>
                            <span style="background: #e8f4fd; padding: 2px 10px; border-radius: 12px; font-size: 12px;">
                                {{ $tire->refill_count }} / {{ $tire->max_refills }}
                            </span>
                        </td>
                        <td>Rs. {{ number_format($tire->pivot->refilling_cost ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            @if($order->notes)
            <div style="margin: 15px 0; padding: 12px 18px; background: #f8f9fa; border-left: 4px solid #1b2850;">
                <p style="color: #555; font-size: 13px; margin: 0;">
                    <strong>Notes:</strong> {{ $order->notes }}
                </p>
            </div>
            @endif
            
            <!-- Footer -->
            <div class="footer">
                <p>This is a computer-generated document. Please verify the tire details before processing.</p>
                <div class="timestamp">Generated on: {{ date('Y-m-d H:i:s') }}</div>
            </div>
        </div>
    </div>

    <!-- html2pdf library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <script>
        function downloadPDF() {
            const element = document.getElementById('pdfContent');
            const btn = document.getElementById('downloadBtn');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = 'Generating PDF...';
            btn.disabled = true;
            
            const opt = {
                margin: [10, 10, 10, 10],
                filename: 'RefillingOrder_{{ $order->order_number }}_{{ date('Ymd') }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 2,
                    useCORS: true,
                    logging: false
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: 'a4', 
                    orientation: 'portrait' 
                }
            };
            
            html2pdf()
                .set(opt)
                .from(element)
                .save()
                .then(function() {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                })
                .catch(function(err) {
                    console.error('PDF generation error:', err);
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    alert('Failed to generate PDF. Please try printing instead.');
                });
        }
        
        @if(request()->has('download'))
            window.onload = function() {
                setTimeout(downloadPDF, 1000);
            };
        @endif
    </script>
</body>
</html>