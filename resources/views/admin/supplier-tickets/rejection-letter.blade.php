<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SURAT PENOLAKAN - {{ $supplierTicket->ticket_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px; /* Set default font size */
            margin: 0;
            /* padding: 20px; */
            background-color: #f5f5f5;
        }

        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .letterhead-top {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 15px 0;
        }
        .company-info {
            display: flex;
            align-items: center;
            flex: 1;
        }
        .company-logo {
            width: 100%;
            max-height: 140px;
            margin: 0;
        }
        .company-logo img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }
        .company-name {
            flex: 1;
        }
        .company-name h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            color: #ffd700;
            /* text-shadow: 2px 2px 0px #000; */
            font-style: italic;
        }
        .company-name .service {
            font-size: 14px;
            font-style: italic;
            color: #333;
            margin-top: 5px;
        }
        .company-name .underline {
            height: 3px;
            background: #ffd700;
            margin-top: 5px;
            width: 200px;
        }
        .contact-info {
            text-align: center;
            font-size: 12px;
            color: #333;
            margin-top: -10px;
            /* padding: 10px 0; */
            /* border-bottom: 1px solid #ccc; */
        }
        .contact-info a {
            color: #0066cc;
            text-decoration: underline;
        }
        .certifications {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 12px;
        }
        .kan-logo {
            width: 160px;
            height: 60px;
        }
        .kan-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .certified-bar {
            background: #dc3545;
            color: white;
            padding: 8px 5px;
            writing-mode: vertical-rl;
            text-orientation: mixed;
            font-size: 10px;
            font-weight: bold;
            width: 20px;
            text-align: center;
        }
        .iso-cert {
            font-size: 10px;
            color: #333;
            text-align: right;
        }
        .letter-title {
            text-align: center;
            font-size: 18px; /* Reduced from 24px */
            font-weight: bold;
            color: #e74c3c;
            text-transform: uppercase;
            /* margin: 20px 0; */
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        .letter-title-ref {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            /* color: #e74c3c; */
            text-transform: uppercase;
            /* margin: 20px 0; */
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        .document-info {
            font-size: 12px;
            color: #666;
            margin-top: 10px;
        }
        .letter-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            font-size: 15px;
        }
        .info-left, .info-right {
            flex: 1;
        }
        .info-right {
            text-align: right;
        }
        .recipient {
            margin-bottom: 30px;
        }
        .recipient h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        .rejection-details {
            margin-bottom: 30px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        .details-table .label {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 30%;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 4px;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .rejected-items {
            margin-bottom: 30px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .items-table th,
        .items-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 11px; /* Smaller font for table */
        }
        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .signature-section {
            margin-top: 40px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            padding: 25px 15px;
            /* border: 1px solid #ddd; */
            text-align: center;
            vertical-align: top;
            width: 33.33%;
            /* background: #fafafa; */
        }
        .signature-line {
            border-bottom: 1px solid #333;
            margin: 50px 0 15px 0;
            height: 1px;
        }
        .signature-name {
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }
        .signature-title {
            font-size: 11px;
            color: #666;
            margin-bottom: 20px;
            font-style: italic;
        }
        .distribution {
            margin-top: 10px;
            font-size: 12px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .footer p {
            margin: 5px 0;
        }
        .letter-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }
        .print-button:hover {
            background: #0056b3;
        }
        @media (max-width: 768px) {
            .letterhead-top {
                flex-direction: column;
                align-items: flex-start;
            }
            .company-info {
                margin-bottom: 15px;
            }
            .certifications {
                align-items: flex-start;
            }
        }
        @media print {
            body {
                background: white;
                padding: 0;
                font-size: 11px; /* Smaller font for PDF */
            }
            .letter-container {
                box-shadow: none;
                border: none;
                padding: 20px;
            }
            .print-button {
                display: none;
            }
            .letterhead-top {
                break-inside: avoid;
            }
        }
        
        /* PDF-specific styles */
        @isset($isPdf)
            body {
                font-size: 11px !important;
            }
            .letter-title {
                font-size: 16px !important;
            }
            .items-table th,
            .items-table td {
                font-size: 10px !important;
                padding: 6px !important;
            }
        @endisset
    </style>
</head>
<body>
    <div class="letter-container">
        <!-- Header -->
        <div class="header">
            <!-- Company Letterhead (single banner image) -->
            <div class="company-logo">
                <img src="{{ asset('sipo_krisan/public/assets/images/A4 - 1.png')}}" alt="Kop Surat" />
            </div>
            
            <!-- Contact Information -->
            <div class="contact-info">
                Jl. Rungkut Industri III / 19, Surabaya - Telp. (031) 8438096, 8438182 - Fax. (031) 8432186, e-mail: <a href="mailto:printing@krisanthium.com">printing@krisanthium.com</a>
            </div>
            
            <!-- Document Title -->
            
        </div>

        
        
        <div class="letter-title">SURAT PENOLAKAN</div>
        <div class="letter-title-ref">
            <strong>No. SP:</strong> {{ $rejectionNumber ?? 'SP-' . str_pad($supplierTicket->id, 3, '0', STR_PAD_LEFT) . '/' . strtolower(date('M')) . '/' . date('Y') }}<br>

            <strong>Ref PO No.:</strong> {{ $supplierTicket->po_number }}
        </div>
        <br>
        <br>

        <!-- Letter Info -->
        <div class="letter-info">
            <div class="info-left">
                <strong>Tanggal:</strong> {{ \Carbon\Carbon::now()->format('d F Y') }}
            </div>
            <div class="info-right">
            </div>
        </div>
        

        <!-- Recipient -->
        <div class="recipient">
            <h3>Kepada Yth. </h3>
            <p><strong>{{ $supplierTicket->supplier_name }}</strong></p>
            @if($supplierTicket->supplier_address)
                <p>{{ $supplierTicket->supplier_address }}</p>
            @endif
        </div>

        <!-- Rejection Details -->
        <div class="rejection-details">
            <p>Dengan hormat,</p>
            <p>Bersama ini kami sampaikan bahwa barang yang dikirim tidak sesuai dengan spesifikasi yang telah disepakati. Berikut detail penolakan:</p>
            
            <table class="details-table">
                <tr>
                    <td class="label">Deskripsi Barang/Produk:</td>
                    <td>{{ $supplierTicket->po_number }} - {{ $supplierTicket->supplier_name }}</td>
                </tr>
                <tr>
                    <td class="label">No./Tgl. Surat Jalan:</td>
                    <td>{{ $supplierTicket->supplier_delivery_doc ?? '-' }} / {{ $supplierTicket->delivery_date ? \Carbon\Carbon::parse($supplierTicket->delivery_date)->format('d/m/Y') : '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Alasan:</td>
                    <td>{{ $supplierTicket->rejection_reason }}</td>
                </tr>
            </table>
        </div>

        <!-- Rejected Items -->
        @if($supplierTicket->hasRejectedItems())
        <div class="rejected-items">
            <h4>Detail Item yang Di Reject:</h4>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Material</th>
                        {{-- <th>Material Name</th> --}}
                        <th>Unit</th>
                        <th>Qty Rejected</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($supplierTicket->getRejectedItems() as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                        @php
                            $poDetails = $supplierTicket->getPODetails();
                            $unit = 'PCS';
                            if (!empty($poDetails) && isset($poDetails[0])) {
                                $unit = is_object($poDetails[0]) ? $poDetails[0]->unit : $poDetails[0]['unit'];
                            } elseif ($supplierTicket->hasRejectedItems()) {
                                $rejectedItems = $supplierTicket->getRejectedItems();
                                $unit = $rejectedItems[0]['unit'] ?? 'PCS';
                            }
                        @endphp
                        {{ $poDetails[0]->materialName }}
                        </td>
                        {{-- <td>{{ $item['materialName'] ?? 'N/A' }}</td> --}}
                        <td>{{$unit}}</td>
                        <td class="text-right">
                            <span>{{ number_format($item['rejected_qty'])  }}</span>
                        </td>
                        <td>{{ $item['reason'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section" style="border: none;">
            <table class="signature-table" style="border: none;">
                <tr>
                    <td>
                        <div class="signature-name">Mengetahui,</div>
                        <div class="signature-title">Dept.Purchasing dan Gudang</div>
                    </td>
                    <td>
                        <div class="signature-name">Diperiksa dan Disetujui Oleh</div>
                        <div class="signature-title">Quality</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Dokumen ini dibuat secara otomatis oleh sistem SiPO Krisanthium</strong></p>
            <p>Tanggal cetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
            <p>Untuk informasi lebih lanjut, hubungi bagian Purchasing PT Krisanthium Offset Printing</p>
        </div>
    </div>
</body>
</html>
