<!-- resources/views/tire/inventory/gate_pass.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate Pass - {{ $tire->serial_number }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
        
        .gate-pass-wrapper {
            width: 900px !important;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 40px;
            position: relative;
        }
        
        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .btn-print, .btn-download, .btn-close {
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            margin: 0 5px;
            transition: all 0.3s;
            border: none;
            color: white;
        }
        
        .btn-print {
            background: #3498DB;
        }
        
        .btn-print:hover {
            background: #2980B9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .btn-download {
            background: #1b2850;
        }
        
        .btn-download:hover {
            background: #2a3d7a;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(27, 40, 80, 0.3);
        }
        
        .btn-close {
            background: #95a5a6;
        }
        
        .btn-close:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
        }
        
        .btn-download:disabled, .btn-print:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
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
            .signature-line {
                page-break-inside: avoid;
            }
            .watermark {
                opacity: 0.05 !important;
            }
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
            font-size: 80px;
            color: rgba(27, 40, 80, 0.05);
            font-weight: 900;
            letter-spacing: 10px;
            pointer-events: none;
            white-space: nowrap;
            z-index: 0;
        }
        
        /* Top Bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #1b2850;
            padding-bottom: 15px;
            margin-bottom: 25px;
            position: relative;
            z-index: 1;
        }
        
.company-info {
    display: flex;
    align-items: center;
    gap: 15px;
    width: 100%;
}
        
        .company-logo-wrapper {
            flex-shrink: 0;
        }
        
        .company-logo-img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        
        .company-details h2 {
            color: #1b2850;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .company-details p {
            margin: 2px 0;
            color: #444;
            font-size: 13px;
            line-height: 1.4;
        }
        
        .gate-pass-number {
            text-align: right;
            min-width: 120px;
            flex-shrink: 0;
        }
        
        .gate-pass-number .label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
        }
        
        .gate-pass-number .number {
            font-size: 20px;
            font-weight: 600;
            color: #1b2850;
            border: 2px solid #1b2850;
            padding: 8px 10px;
            border-radius: 6px;
            margin-top: 5px;
            display: inline-block;
            background: #f8f9fa;
        }
        
        /* Title Section */
        .title-section {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }
        
        .title-section h1 {
            font-size: 32px;
            color: #1b2850;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        .title-section .subtitle {
            color: #7f8c8d;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .title-section .date {
            color: #95a5a6;
            font-size: 13px;
            margin-top: 3px;
        }
        
        /* Details Table */
        .details-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.details-table .label {
    width: 18%;
    font-weight: 600;
    background: #f8f9fa;
    color: #1b2850;
}

.details-table .value {
    width: 32%;
    color: #2c3e50;
}

.details-table td {
    padding: 10px 12px;
    border: 1px solid #dee2e6;
    word-wrap: break-word;
}
        
        .details-table .label {
            font-weight: 600;
            color: #1b2850;
            width: 35%;
            background: #f8f9fa;
        }
        
        .details-table .value {
            color: #2c3e50;
            font-weight: 500;
        }
        
        .details-table .value strong {
            color: #1b2850;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        
        .status-badge.new { background: #d4edda; color: #155724; }
        .status-badge.in_use { background: #cce5ff; color: #004085; }
        .status-badge.used { background: #fff3cd; color: #856404; }
        .status-badge.at_vendor { background: #f8d7da; color: #721c24; }
        .status-badge.scrap { background: #e2e3e5; color: #383d41; }
        
        /* Info Box */
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #1b2850;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 4px;
            position: relative;
            z-index: 1;
        }
        
        .info-box p {
            color: #444;
            font-size: 14px;
            margin: 0;
        }
        
        .info-box .info-icon {
            margin-right: 10px;
            color: #1b2850;
        }
        
        /* Signatures Section */
        .signatures-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #e9ecef;
            gap: 20px;
            position: relative;
            z-index: 1;
        }
        
        .signature-box {
            flex: 1;
            text-align: center;
        }
        
        .signature-box .signature-icon {
            font-size: 28px;
            color: #95a5a6;
            margin-bottom: 5px;
        }
        
        .signature-box .signature-line {
            border-bottom: 2px solid #1b2850;
            margin: 30px 0 10px;
            min-height: 50px;
        }
        
        .signature-box .signature-label {
            font-weight: 600;
            color: #1b2850;
            font-size: 14px;
        }
        
        .signature-box .signature-sub {
            color: #7f8c8d;
            font-size: 12px;
            margin-top: 2px;
        }
        
        /* Footer */
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        
        .footer p {
            color: #95a5a6;
            font-size: 11px;
            margin: 2px 0;
        }
        
        .footer .timestamp {
            color: #bdc3c7;
            font-size: 10px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .gate-pass-wrapper {
                padding: 20px;
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
                width: 65px;
                height: 65px;
            }
            
            .company-details h2 {
                font-size: 18px;
            }
            
            .gate-pass-number {
                text-align: left;
                width: 100%;
            }
            
            .signatures-section {
                flex-direction: column;
                gap: 30px;
            }
            
            .details-table td {
                padding: 8px 12px;
            }
            
            .title-section h1 {
                font-size: 24px;
            }
            
            .watermark {
                font-size: 50px;
            }
        }
        
        @media (max-width: 480px) {
            .no-print {
                display: flex;
                flex-direction: column;
                gap: 10px;
                align-items: center;
            }
            
            .btn-print, .btn-download, .btn-close {
                width: 100%;
                max-width: 200px;
            }
        }
        #gatePassContent {
    width: 100%;
    max-width: 100%;
}

@media print {

    .watermark {
        display: none !important;
    }

    .top-bar {
        display: table;
        width: 100%;
    }

    .company-info {
        display: table-cell;
        width: 75%;
        vertical-align: top;
    }

    .gate-pass-number {
        display: table-cell;
        width: 25%;
        text-align: right;
        vertical-align: top;
    }

    .signatures-section {
        display: table;
        width: 100%;
    }

    .signature-box {
        display: table-cell;
        width: 33%;
    }
}
    </style>
</head>
<body>
    <!-- Action Buttons - No Print -->
    <div class="no-print">
        <button onclick="downloadPDF()" class="btn-download" id="downloadBtn">
            <i class="fas fa-download"></i> Download PDF
        </button>
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print"></i> Print Gate Pass
        </button>
        <button onclick="window.close()" class="btn-close">
            <i class="fas fa-times"></i> Close
        </button>
    </div>
    
    <!-- Gate Pass Content -->
    <div class="gate-pass-wrapper" id="gatePassContent">
        <div class="gate-pass">
            <!-- Watermark -->
            <div class="watermark">GATE PASS</div>
            
            <!-- Top Bar -->
            <div class="top-bar">
                <div class="company-info">
                    <!-- <div class="company-logo-wrapper">
                        <img src="{{ asset('assets/admin/img/ilogo.jpg') }}" alt="IFFS Logo" class="company-logo-img">
                    </div> -->
                    <div class="company-details">
                        <h2>Inter Freight Forwarding Service</h2>
                        <p>
                            
                            No. 789, Samurdhi Mawatha, Heiyanthuduwa, Sapugaskanda, Sri Lanka
                        </p>
                        <p>
                          
                            No. 12, 2nd Floor, Keyzer Street, Colombo 11, Sri Lanka
                        </p>
                        <p>
                            
                            Website: www.iffs.idealsoft.us
                        </p>
                    </div>
                </div>
                <div class="gate-pass-number">
                    <span class="label">Gate Pass No.</span>
                    <span class="number">{{ $gatePassNumber }}</span>
                </div>
            </div>
            
            <!-- Title Section -->
            <div class="title-section">
                <h1>Gate Pass</h1>
                <div class="subtitle">Tire Movement Authorization</div>
                <div class="date">
                    
                    Date: {{ date('l, F d, Y') }}
                </div>
            </div>
            
            <table class="details-table">
    <tr>
        <td class="label">Serial Number</td>
        <td class="value">{{ $tire->serial_number }}</td>

        <td class="label">Brand</td>
        <td class="value">{{ $tire->brand }}</td>
    </tr>

    <tr>
        <td class="label">Size</td>
        <td class="value">{{ $tire->size }}</td>

        <td class="label">Type</td>
        <td class="value">{{ $tire->type }}</td>
    </tr>

    <tr>
        <td class="label">Status</td>
        <td class="value">
            <span class="status-badge {{ str_replace('_', '', $tire->status) }}">
                {{ ucfirst(str_replace('_', ' ', $tire->status)) }}
            </span>
        </td>

        <td class="label">Vendor</td>
        <td class="value">
            {{ $tire->vendor ? $tire->vendor->name : 'N/A' }}
        </td>
    </tr>

    <tr>
        <td class="label">Refill Count</td>
        <td class="value">
            {{ $tire->refill_count }} / {{ $tire->max_refills }}
        </td>

        <td class="label">Mileage</td>
        <td class="value">
            {{ number_format($tire->consumption_mileage) }} km
        </td>
    </tr>

    <tr>
        <td class="label">Current Location</td>
        <td class="value">
            @php
                $locationText = ucfirst(str_replace('_', ' ', $tire->current_location ?? 'Store'));

                if(
                    $tire->status == 'in_use' &&
                    $tire->currentAllocation &&
                    $tire->currentAllocation->vehicle
                ){
                    $locationText = 'Vehicle: ' .
                    $tire->currentAllocation->vehicle->lorry_number;
                }
            @endphp

            {{ $locationText }}
        </td>

        <td class="label">Purpose</td>
        <td class="value">
            Tire Movement / Transfer
        </td>
    </tr>
</table>
            
            <!-- Info Box -->
            <div class="info-box">
                <p>
                    <i class="fas fa-info-circle info-icon"></i>
                    <strong>Important:</strong> This gate pass authorizes the movement of the above-mentioned tire.
                    Please ensure all details are correct before signing.
                </p>
            </div>
            
            <!-- Signatures Section -->
            <div class="signatures-section">
                <div class="signature-box">
                  
                    <div class="signature-line"></div>
                    <div class="signature-label">Store Manager</div>
                    <div class="signature-sub">(Authorized Signature)</div>
                </div>
                
                <div class="signature-box">
                    
                    <div class="signature-line"></div>
                    <div class="signature-label">Security</div>
                    <div class="signature-sub">(Security Check)</div>
                </div>
                
                <div class="signature-box">
                    
                    <div class="signature-line"></div>
                    <div class="signature-label">Customer / Driver</div>
                    <div class="signature-sub">(Recipient Signature)</div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="footer">
                <p>
                    
                    This is a computer-generated gate pass. Please verify the tire details before signing.
                </p>
                <p>
                    
                    For any discrepancies, please contact the store manager immediately.
                </p>
                <div class="timestamp">
                    
                    Generated on: {{ date('Y-m-d H:i:s') }}
                </div>
            </div>
        </div>
    </div>

    <!-- html2pdf library for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <script>
        function downloadPDF() {
            const element = document.getElementById('gatePassContent');
            const btn = document.getElementById('downloadBtn');
            const originalText = btn.innerHTML;
            
            // Show loading state
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating PDF...';
            btn.disabled = true;
            
            const opt = {
    margin: [8, 8, 8, 8],

    filename: 'GatePass_{{ $tire->serial_number }}.pdf',

    image: {
        type: 'jpeg',
        quality: 1
    },

    html2canvas: {
        scale: 2,
        useCORS: true,
        scrollY: 0
    },

    jsPDF: {
        unit: 'mm',
        format: 'a4',
        orientation: 'portrait'
    },

    pagebreak: {
        mode: ['avoid-all']
    }
};
            
            // Save the element
            html2pdf()
                .set(opt)
                .from(element)
                .save()
                .then(function() {
                    // Reset button
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
        
        // Auto-download when page loads with ?download parameter
        @if(request()->has('download'))
            window.onload = function() {
                setTimeout(downloadPDF, 1000);
            };
        @endif
        
        // Handle print event for better print quality
        window.onbeforeprint = function() {
            // Add any print-specific adjustments here
        };
    </script>
</body>
</html>