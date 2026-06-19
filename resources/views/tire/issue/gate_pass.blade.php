<!-- resources/views/tire/issue/gate_pass.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate Pass - {{ $issueNote->issue_note_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 40px;
            background: #f5f6fa;
        }
        .gate-pass-wrapper {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 20px rgba(0,0,0,0.08);
        }
        
        /* Buttons */
        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-print, .btn-download {
            padding: 10px 25px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            color: #fff;
            transition: all 0.3s;
            margin: 0 5px;
        }
        .btn-print {
            background: #3498db;
        }
        .btn-print:hover {
            background: #2980b9;
        }
        .btn-download {
            background: #1b2850;
        }
        .btn-download:hover {
            background: #2a3d7a;
        }
        
        /* Gate Pass Content */
        .gate-pass {
            position: relative;
        }
        
        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 100px;
            color: rgba(27, 40, 80, 0.04);
            font-weight: 900;
            letter-spacing: 15px;
            pointer-events: none;
            z-index: 0;
        }
        
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #1b2850;
            padding-bottom: 15px;
            margin-bottom: 25px;
            position: relative;
            z-index: 1;
        }
        .company-details h2 {
            color: #1b2850;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .company-details p {
            color: #555;
            font-size: 12px;
            line-height: 1.6;
            margin: 2px 0;
        }
        .gate-pass-number {
            text-align: right;
            min-width: 150px;
            flex-shrink: 0;
        }
        .gate-pass-number .label {
            font-size: 11px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .gate-pass-number .number {
            font-size: 18px;
            font-weight: 700;
            color: #1b2850;
            border: 2px solid #1b2850;
            padding: 4px 14px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 4px;
        }
        
        /* Title */
        .title-section {
            text-align: center;
            margin-bottom: 25px;
            position: relative;
            z-index: 1;
        }
        .title-section h1 {
            font-size: 28px;
            color: #1b2850;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .title-section .subtitle {
            color: #888;
            font-size: 13px;
            margin-top: 3px;
        }
        .title-section .date {
            color: #999;
            font-size: 12px;
            margin-top: 4px;
        }
        
        /* Details Table */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0 20px;
            position: relative;
            z-index: 1;
            font-size: 13px;
        }
        .details-table td {
            padding: 10px 14px;
            border-bottom: 1px solid #eee;
        }
        .details-table .label {
            font-weight: 600;
            color: #1b2850;
            width: 140px;
            background: #f8f9fa;
        }
        .details-table .value {
            color: #333;
        }
        
        /* Tires Table */
        .tires-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0 25px;
            position: relative;
            z-index: 1;
            font-size: 13px;
        }
        .tires-table th {
            background: #f8f9fa;
            color: #1b2850;
            font-weight: 600;
            padding: 10px 12px;
            border: 1px solid #dee2e6;
            text-align: left;
        }
        .tires-table td {
            padding: 10px 12px;
            border: 1px solid #dee2e6;
        }
        .tires-table tr:nth-child(even) {
            background: #fafafa;
        }
        
        /* Signatures */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 35px;
            padding-top: 25px;
            border-top: 2px solid #e9ecef;
            position: relative;
            z-index: 1;
        }
        .signature-box {
            text-align: center;
            flex: 1;
        }
        .signature-box .line {
            border-bottom: 2px solid #1b2850;
            margin: 30px 20px 8px 20px;
            min-height: 40px;
        }
        .signature-box .label {
            font-weight: 600;
            color: #1b2850;
            font-size: 13px;
        }
        .signature-box .sub {
            color: #999;
            font-size: 11px;
        }
        
        /* Footer */
        .footer {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .footer p {
            color: #aaa;
            font-size: 11px;
            margin: 2px 0;
        }
        .footer .timestamp {
            color: #ccc;
            font-size: 10px;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .gate-pass-wrapper {
                box-shadow: none;
                border-radius: 0;
                padding: 30px;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
            .watermark {
                opacity: 0.5 !important;
            }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .gate-pass-wrapper {
                padding: 20px;
            }
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            .gate-pass-number {
                text-align: left;
                width: 100%;
            }
            .signatures {
                flex-direction: column;
                gap: 30px;
            }
            .signature-box .line {
                margin: 30px 0 8px 0;
            }
        }
    </style>
</head>
<body>
    <!-- Action Buttons - No Print -->
    <div class="no-print">
        <button onclick="downloadPDF()" class="btn-download">
            <i class="fas fa-download"></i> Download PDF
        </button>
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print"></i> Print Gate Pass
        </button>
        <button onclick="window.close()" class="btn-print" style="background: #95a5a6;">
            <i class="fas fa-times"></i> Close
        </button>
    </div>

    <div class="gate-pass-wrapper" id="gatePassContent">
        <div class="gate-pass">
            <!-- Watermark -->
            <div class="watermark">GATE PASS</div>

            <!-- Header -->
            <div class="header">
                <div class="company-details">
                    <h2>Inter Freight Forwarding Service (Pvt) Ltd</h2>
                    <p>No. 789, Samurdhi Mawatha, Heiyanthuduwa, Sapugaskanda, Sri Lanka</p>
                    <p>No. 12, 2nd Floor, Keyzer Street, Colombo 11, Sri Lanka</p>
                    <p>Website: www.iffs.idealsoft.us</p>
                </div>
                <div class="gate-pass-number">
                    <div class="label">Gate Pass No.</div>
                    <div class="number">{{ $gatePassNumber }}</div>
                </div>
            </div>

            <!-- Title -->
            <div class="title-section">
                <h1>Gate Pass</h1>
                <div class="subtitle">Tire Movement Authorization</div>
                <div class="date">Date: {{ date('l, F d, Y') }}</div>
            </div>

            <!-- Details -->
            <table class="details-table">
                <tr>
                    <td class="label">Issue Note No</td>
                    <td class="value">{{ $issueNote->issue_note_number }}</td>
                    <td class="label">Issue Date</td>
                    <td class="value">{{ $issueNote->issue_date->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Total Tires</td>
                    <td class="value">{{ $issueNote->items->count() }}</td>
                    <td class="label">Purpose</td>
                    <td class="value">Tire Movement / Transfer</td>
                </tr>
            </table>

            <!-- Tires Table -->
            <table class="tires-table">
                <thead>
                    <tr>
                        <th width="40">#</th>
                        <th>Tire Serial No</th>
                        <th>Vehicle No</th>
                        <th>Size</th>
                        <th>Brand</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($issueNote->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->tire->serial_number }}</td>
                        <td>{{ $item->vehicle ? $item->vehicle->lorry_number : 'N/A' }}</td>
                        <td>{{ $item->tire->size }}</td>
                        <td>{{ $item->tire->brand }}</td>
                        <td>{{ $item->remark ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Signatures -->
            <div class="signatures">
                <div class="signature-box">
                    <div class="line"></div>
                    <div class="label">Store Manager</div>
                    <div class="sub">Authorized Signature</div>
                </div>
                <div class="signature-box">
                    <div class="line"></div>
                    <div class="label">Security</div>
                    <div class="sub">Security Check</div>
                </div>
                <div class="signature-box">
                    <div class="line"></div>
                    <div class="label">Customer / Driver</div>
                    <div class="sub">Recipient Signature</div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>This is a computer-generated gate pass. Please verify all details before signing.</p>
                <p>For discrepancies, contact the store manager immediately.</p>
                <div class="timestamp">Generated on: {{ date('Y-m-d H:i:s') }}</div>
            </div>
        </div>
    </div>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- html2pdf Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <script>
        function downloadPDF() {
            const element = document.getElementById('gatePassContent');
            const btn = document.querySelector('.btn-download');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
            btn.disabled = true;
            
            const opt = {
                margin: [10, 10, 10, 10],
                filename: 'GatePass_{{ $issueNote->issue_note_number }}_{{ date('Ymd') }}.pdf',
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
    </script>
</body>
</html>