<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permohonan Ditolak</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            line-height: 1.6;
            color: #1a1a1a;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        .page {
            max-width: 100%;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 40px 60px;
        }
        .header {
            border-bottom: 3px solid #c82333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 18px;
            font-weight: 600;
            color: #c82333;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            text-align: center;
        }
        .header-subtitle {
            text-align: center;
            font-size: 11px;
            color: #666;
            margin-top: 8px;
            font-style: italic;
        }
        .content {
            font-size: 12pt;
        }
        .greeting {
            font-size: 12pt;
            margin-bottom: 16px;
            color: #333;
        }
        .greeting strong {
            font-weight: 600;
        }
        .message {
            margin-bottom: 24px;
            text-align: justify;
        }
        .message.rejection {
            background-color: #fef2f2;
            border-left: 4px solid #c82333;
            padding: 20px;
            margin: 24px 0;
        }
        .message.rejection p {
            margin: 0;
            line-height: 1.8;
            color: #333;
        }
        .info-section {
            background-color: #f9f9f9;
            border: 1px solid #d0d0d0;
            padding: 20px;
            margin: 24px 0;
        }
        .info-section-title {
            font-weight: 600;
            font-size: 13pt;
            color: #1e3a5f;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            font-size: 11pt;
        }
        .details-table td {
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: top;
        }
        .details-table tr:last-child td {
            border-bottom: none;
        }
        .details-table td:first-child {
            font-weight: 600;
            color: #333;
            width: 35%;
            padding-right: 20px;
        }
        .details-table td:last-child {
            color: #1a1a1a;
        }
        .rejection-reason {
            background-color: #fff8e7;
            border: 1px solid #f5a623;
            padding: 20px;
            margin: 24px 0;
        }
        .rejection-reason-title {
            font-weight: 600;
            font-size: 11pt;
            color: #f5a623;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .rejection-reason-content {
            font-style: italic;
            color: #666;
            padding: 12px;
            background-color: #ffffff;
            border-left: 3px solid #f5a623;
            margin: 0;
        }
        .approval-info {
            background-color: #fef2f2;
            border: 1px solid #c82333;
            padding: 20px;
            margin: 24px 0;
        }
        .approval-info-title {
            font-weight: 600;
            font-size: 11pt;
            color: #c82333;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .approval-info p {
            margin: 0;
            line-height: 1.8;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #1e3a5f;
            text-align: center;
        }
        .footer p {
            font-size: 9pt;
            color: #666;
            margin: 0;
            line-height: 1.4;
        }
        .company-info {
            text-align: center;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 13pt;
            font-weight: 600;
            color: #1e3a5f;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        @media print {
            .page {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <h1>Permohonan Ditolak</h1>
            <div class="header-subtitle">Sistem Informasi Penjadwalan & Operasional - Krisanthium</div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Greeting -->
            <p class="greeting"><strong>Yth. Bapak/Ibu {{ $requester->name }},</strong></p>

            <!-- Rejection Message -->
            <div class="message rejection">
                <p>
                    Mohon maaf, kami informasikan bahwa permohonan Anda dengan nomor <strong>{{ $employeeRequest->request_number }}</strong> tidak dapat disetujui.
                </p>
            </div>

            <!-- Info Section -->
            <div class="info-section">
                <div class="info-section-title">Informasi Permohonan</div>
                <table class="details-table">
                    <tr>
                        <td>Nomor Permohonan</td>
                        <td>: {{ $employeeRequest->request_number }}</td>
                    </tr>
                    <tr>
                        <td>Tipe Permohonan</td>
                        <td>: {{ $employeeRequest->request_type_label }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Pengajuan</td>
                        <td>: {{ $employeeRequest->created_at->format('d F Y') }} pukul {{ $employeeRequest->created_at->format('H:i') }} WIB</td>
                    </tr>
                </table>
            </div>

            <!-- Rejection Reason -->
            @if($rejectionReason)
            <div class="rejection-reason">
                <div class="rejection-reason-title">Alasan Penolakan</div>
                <p class="rejection-reason-content">
                    "{{ $rejectionReason }}"
                </p>
            </div>
            @endif

            <!-- Approval Info -->
            <div class="approval-info">
                <div class="approval-info-title">Informasi Penolakan</div>
                <p>
                    <strong>Ditolak oleh:</strong> {{ $approver->name }}<br>
                    <strong>Jabatan:</strong> {{ $approver->jabatanObj->nama_jabatan ?? '-' }}<br>
                    <strong>Waktu Penolakan:</strong> {{ now()->format('d F Y') }} pukul {{ now()->format('H:i') }} WIB<br>
                    @if($currentLevel)
                    <strong>Level:</strong> {{ ucfirst(str_replace('_', ' ', $currentLevel)) }}
                    @endif
                </p>
            </div>

            <!-- Additional Information -->
            <p style="margin-top: 24px; font-size: 11pt; text-align: justify;">
                Apabila Bapak/Ibu memiliki pertanyaan mengenai penolakan ini atau ingin mengajukan ulang permohonan, dimohon untuk menghubungi atasan langsung atau departemen HRD untuk mendapatkan informasi lebih lanjut.
            </p>

            <!-- Closing -->
            <p style="margin-top: 24px; font-size: 11pt;">
                Pesan ini dikirim secara otomatis oleh sistem pada {{ now()->format('d F Y H:i') }} WIB<br>
                <strong>Sistem Informasi Penjadwalan & Operasional</strong>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="company-info">
                <div class="company-name">PT. KRISANTHIUM OFFSET PRINTING</div>
                <p style="font-size: 9pt; color: #666; margin-top: 8px;">
                    Email: printing@krisanthium.com | Website: krisanthium.com
                </p>
            </div>
            <p>
                Email ini dikirim secara otomatis oleh sistem pada {{ now()->format('d F Y H:i') }} WIB<br>
                Mohon tidak membalas email ini. Untuk informasi lebih lanjut, hubungi bagian IT.
            </p>
            <p style="margin-top: 12px; border-top: 1px solid #e0e0e0; padding-top: 12px;">
                &copy; {{ date('Y') }} PT. Krisanthium Offset Printing. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
