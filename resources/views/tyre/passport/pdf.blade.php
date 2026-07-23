<!-- resources/views/tyre/passport/pdf.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tyre Passport - {{ $tyre->serial_number }}</title>
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
            margin-bottom: 30px;
        }
        
        .title-area h2 {
            color: #1b2850;
            font-size: 26px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .title-area p {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        /* Section Heading */
        .section-title {
            font-size: 16px;
            color: #1b2850;
            border-bottom: 2px solid #ebedef;
            padding-bottom: 8px;
            margin: 30px 0 15px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Grid Layout */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }
        
        .info-card {
            background: #f8f9fa;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
        }
        
        .info-item {
            margin-bottom: 10px;
        }
        
        .info-item:last-child {
            margin-bottom: 0;
        }
        
        .info-label {
            font-size: 12px;
            color: #718096;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 2px;
        }
        
        .info-value {
            font-size: 15px;
            color: #2d3748;
            font-weight: 700;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-new { background: #def7ec; color: #03543f; }
        .badge-in_use { background: #e1effe; color: #1e429f; }
        .badge-used { background: #fef08a; color: #713f12; }
        .badge-at_vendor { background: #f3f4f6; color: #1f2937; }
        .badge-scrapped { background: #fde8e8; color: #9b1c1c; }
        
        /* Timeline style for PDF */
        .pdf-timeline {
            margin-top: 15px;
        }
        
        .pdf-timeline-item {
            display: flex;
            margin-bottom: 20px;
            border-left: 2px solid #cbd5e0;
            padding-left: 20px;
            position: relative;
        }
        
        .pdf-timeline-item:last-child {
            margin-bottom: 0;
        }
        
        .pdf-timeline-bullet {
            position: absolute;
            left: -7px;
            top: 2px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #4a5568;
            border: 2px solid white;
        }
        
        .pdf-timeline-date {
            font-weight: 600;
            color: #4a5568;
            font-size: 13px;
            width: 110px;
            flex-shrink: 0;
        }
        
        .pdf-timeline-content {
            flex-grow: 1;
        }
        
        .pdf-timeline-title {
            font-weight: 700;
            color: #2d3748;
            font-size: 14px;
            margin-bottom: 4px;
        }
        
        .pdf-timeline-desc {
            color: #718096;
            font-size: 13px;
        }
        
        .pdf-timeline-meta {
            margin-top: 4px;
            font-size: 12px;
            color: #4a5568;
            font-style: italic;
        }
        
        .footer {
            margin-top: 50px;
            border-top: 2px solid #ebedef;
            padding-top: 15px;
            text-align: center;
            color: #7f8c8d;
            font-size: 11px;
        }
        
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
                margin: 0;
                padding: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <!-- Action Buttons - No Print -->
    <div class="no-print">
        <button onclick="downloadPDF()" class="btn-download" id="downloadBtn">
            <i class="fas fa-file-pdf me-1"></i> Download PDF
        </button>
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print me-1"></i> Print Passport
        </button>
        <button onclick="window.close()" class="btn-close">
            <i class="fas fa-times me-1"></i> Close
        </button>
    </div>
    
    <!-- PDF Content -->
    <div class="pdf-wrapper" id="pdfContent">
        <div class="pdf-content">
            <!-- Top Bar -->
            <div class="top-bar">
                <div class="title-area">
                    <h2>Tyre Passport</h2>
                    <p>Complete Lifecycle History & Status</p>
                </div>
                <div class="company-logo">
                    <span style="font-weight: 700; font-size: 20px; color: #1b2850;">IFFS SYSTEM</span>
                </div>
            </div>
            
            <!-- Tyre Metadata -->
            <div class="section-title">Tyre Specifications</div>
            <div class="grid-3">
                <div class="info-card">
                    <div class="info-item">
                        <div class="info-label">Serial Number</div>
                        <div class="info-value">{{ $tyre->serial_number }}</div>
                    </div>
                    <div class="info-item" style="margin-top: 10px;">
                        <div class="info-label">Current Status</div>
                        <div>
                            <span class="badge badge-{{ $tyre->status }}">
                                {{ ucfirst(str_replace('_', ' ', $tyre->status)) }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="info-card">
                    <div class="info-item">
                        <div class="info-label">Brand</div>
                        <div class="info-value">{{ $tyre->brand }}</div>
                    </div>
                    <div class="info-item" style="margin-top: 10px;">
                        <div class="info-label">Size</div>
                        <div class="info-value">{{ $tyre->size }}</div>
                    </div>
                </div>
                
                <div class="info-card">
                    <div class="info-item">
                        <div class="info-label">Type</div>
                        <div class="info-value">{{ $tyre->type }}</div>
                    </div>
                    <div class="info-item" style="margin-top: 10px;">
                        <div class="info-label">Refills Count</div>
                        <div class="info-value">{{ $tyre->refill_count }} / {{ $tyre->max_refills }}</div>
                    </div>
                </div>
            </div>
            
            <div class="section-title">Purchase Details</div>
            <div class="grid-2">
                <div class="info-card">
                    <div class="info-item">
                        <div class="info-label">Vendor / Supplier</div>
                        <div class="info-value">{{ $tyre->vendor->name ?? 'Unknown Vendor' }}</div>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-item">
                        <div class="info-label">Purchase Date & Price</div>
                        <div class="info-value">
                            {{ $tyre->purchase_date->format('d-m-Y') }} - Rs. {{ number_format($tyre->purchase_price, 2) }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lifecycle Timeline -->
            <div class="section-title">Lifecycle Events</div>
            <div class="pdf-timeline">
                @forelse($lifecycleHistory as $event)
                <div class="pdf-timeline-item">
                    <div class="pdf-timeline-bullet"></div>
                    <div class="pdf-timeline-date">
                        {{ \Carbon\Carbon::parse($event['date'])->format('d-m-Y') }}
                    </div>
                    <div class="pdf-timeline-content">
                        <div class="pdf-timeline-title">{{ $event['type'] }}</div>
                        <div class="pdf-timeline-desc">{{ $event['details'] }}</div>
                        @if(isset($event['mileage']) && $event['mileage'])
                            <div class="pdf-timeline-meta">Mileage: {{ number_format($event['mileage']) }} km</div>
                        @endif
                    </div>
                </div>
                @empty
                <div style="text-align: center; color: #718096; padding: 20px;">
                    No lifecycle events recorded for this tyre.
                </div>
                @endforelse
            </div>
            
            <!-- Footer -->
            <div class="footer">
                <p>This is a computer-generated Tyre Passport document.</p>
                <div style="margin-top: 5px;">Generated on: {{ date('Y-m-d H:i:s') }}</div>
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
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Generating PDF...';
            btn.disabled = true;
            
            const opt = {
                margin: [10, 10, 10, 10],
                filename: 'TyrePassport_{{ $tyre->serial_number }}_{{ date('Ymd') }}.pdf',
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
        
        window.onload = function() {
            // Auto download on page load
            setTimeout(downloadPDF, 1000);
        };
    </script>
</body>
</html>
