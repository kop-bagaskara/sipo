<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Approval Supplier Ticket - {{ $supplierTicket->ticket_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        .ticket-info {
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #28a745;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #495057;
        }
        .info-value {
            flex: 1;
            color: #212529;
        }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .next-steps {
            background-color: #e7f3ff;
            border: 1px solid #b3d7ff;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .footer {
            background-color: #6c757d;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 0 0 8px 8px;
            font-size: 12px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Approval Supplier Ticket</h1>
        <p>Supplier ticket telah disetujui dan siap untuk diproses</p>
    </div>

    <div class="content">
        <h2>Detail Supplier Ticket</h2>
        
        <div class="ticket-info">
            <div class="info-row">
                <div class="info-label">Nomor Ticket:</div>
                <div class="info-value"><strong>{{ $supplierTicket->ticket_number }}</strong></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Supplier:</div>
                <div class="info-value">{{ $supplierTicket->supplier_name }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Nomor PO:</div>
                <div class="info-value">{{ $supplierTicket->po_number }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="status status-approved">
                        {{ ucfirst($supplierTicket->status) }}
                    </span>
                </div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Tanggal Approval:</div>
                <div class="info-value">{{ $supplierTicket->processed_at->format('d/m/Y H:i') }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Disetujui oleh:</div>
                <div class="info-value">{{ $supplierTicket->processor->name ?? 'N/A' }}</div>
            </div>
            
            @if($supplierTicket->description)
            <div class="info-row">
                <div class="info-label">Deskripsi:</div>
                <div class="info-value">{{ $supplierTicket->description }}</div>
            </div>
            @endif
        </div>

        <div class="next-steps">
            <h3 style="color: #007bff; margin-bottom: 15px;">Langkah Selanjutnya</h3>
            <p>Supplier ticket telah disetujui. Tim gudang akan membuat dokumen:</p>
            <ul>
                <li><strong>GRD (Good Receipt Document)</strong> - untuk material dengan kode berawalan BP</li>
                <li><strong>PQC (Purchase Quality Control)</strong> - untuk kiriman atas POM</li>
            </ul>
            <p>Dokumen akan dibuat sesuai dengan ketentuan yang berlaku.</p>
        </div>

        <div style="text-align: center; margin: 20px 0;">
            <a href="{{ route('admin.supplier-tickets.show', $supplierTicket->id) }}" class="btn">
                Lihat Detail Ticket
            </a>
        </div>

        <p style="margin-top: 20px; font-size: 14px; color: #6c757d;">
            Email ini dikirim secara otomatis oleh sistem SIPO. 
            Silakan login ke sistem untuk melihat detail lengkap dan melakukan tindakan yang diperlukan.
        </p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} SIPO - Sistem Informasi Produksi Offset</p>
        <p>Email ini dikirim pada {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
