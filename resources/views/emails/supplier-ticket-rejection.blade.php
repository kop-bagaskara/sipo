<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SURAT PENOLAKAN - {{ $supplierTicket->ticket_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .letter-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 20px;
        }
        .letter-title {
            font-size: 24px;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 10px;
        }
        .letter-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            width: 200px;
            color: #495057;
        }
        .info-value {
            flex: 1;
            color: #212529;
        }
        .rejection-details {
            background-color: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .rejection-reason {
            background-color: #fef2f2;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            height: 50px;
            margin-bottom: 10px;
        }
        .footer {
            background-color: #6c757d;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
            margin-top: 30px;
            font-size: 12px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="letter-header">
        <div class="letter-title">SURAT PENOLAKAN</div>
        <p>PM.740.F.02.0.00.E - Edisi 1 Revisi 2</p>
    </div>

    <div class="letter-info">
        <div class="info-row">
            <div class="info-label">No. SP:</div>
            <div class="info-value">{{ $rejectionLetter['rejection_number'] }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Ref PO No:</div>
            <div class="info-value">{{ $supplierTicket->po_number }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal:</div>
            <div class="info-value">{{ $rejectionLetter['date']->format('d F Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Kepada Yth:</div>
            <div class="info-value">{{ $supplierTicket->supplier_name }}</div>
        </div>
    </div>

    <div class="rejection-details">
        <h3 style="color: #dc3545; margin-bottom: 20px;">Detail Barang yang Ditolak</h3>
        
        <div class="info-row">
            <div class="info-label">Deskripsi Barang/Produk:</div>
            <div class="info-value">{{ $supplierTicket->description ?? 'N/A' }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">No./Tgl. Surat Jalan:</div>
            <div class="info-value">{{ $supplierTicket->supplier_delivery_doc }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Tanggal Pengiriman:</div>
            <div class="info-value">{{ $supplierTicket->delivery_date->format('d/m/Y') }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Jumlah yang Diterima:</div>
            <div class="info-value">{{ $supplierTicket->accepted_quantity ?? 'N/A' }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Jumlah yang Ditolak:</div>
            <div class="info-value">{{ $supplierTicket->rejected_quantity ?? 'N/A' }}</div>
        </div>
        
        <div class="rejection-reason">
            <strong>Alasan Penolakan:</strong><br>
            {{ $supplierTicket->rejection_reason }}
        </div>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <p><strong>Supir</strong></p>
            <p>No. Mobil: _______________</p>
        </div>
        
        <div class="signature-box">
            <div class="signature-line"></div>
            <p><strong>Mengetahui</strong></p>
            <p>WH Spv/Mgr</p>
            <p>{{ $rejectionLetter['date']->format('d/m') }}</p>
        </div>
        
        <div class="signature-box">
            <div class="signature-line"></div>
            <p><strong>Diperiksa dan disetujui oleh</strong></p>
            <p>QC/MT/RD Spv/Mgr</p>
            <p>{{ $rejectionLetter['date']->format('d/m') }}</p>
        </div>
    </div>

    <div style="margin-top: 30px; font-size: 12px;">
        <p><strong>Distribusi:</strong></p>
        <p>1. Supplier</p>
        <p>2. Purchasing</p>
        <p>3. Warehouse</p>
        <p>4. QC/MT</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} SIPO - Sistem Informasi Produksi Offset</p>
        <p>Surat penolakan ini dikirim secara otomatis pada {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Untuk informasi lebih lanjut, silakan hubungi tim QC/MT/RD</p>
    </div>
</body>
</html>
