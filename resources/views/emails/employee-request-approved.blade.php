<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permohonan Disetujui</title>
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
            border-bottom: 3px solid #2d7a3e;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        @if($isFinalApproval)
        .header {
            border-bottom-color: #f5a623;
        }
        @endif
        .header h1 {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 18px;
            font-weight: 600;
            color: #2d7a3e;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
            text-align: center;
        }
        @if($isFinalApproval)
        .header h1 {
            color: #f5a623;
        }
        @endif
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
        .message.success {
            background-color: #f0f9f4;
            border-left: 4px solid #2d7a3e;
            padding: 20px;
            margin: 24px 0;
        }
        @if($isFinalApproval)
        .message.success {
            background-color: #fff8e7;
            border-left-color: #f5a623;
        }
        @endif
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
        .approval-info {
            background-color: #e8f4f0;
            border: 1px solid #2d7a3e;
            padding: 20px;
            margin: 24px 0;
        }
        @if($isFinalApproval)
        .approval-info {
            background-color: #fff8e7;
            border-color: #f5a623;
        }
        @endif
        .approval-info-title {
            font-weight: 600;
            font-size: 11pt;
            color: #2d7a3e;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        @if($isFinalApproval)
        .approval-info-title {
            color: #f5a623;
        }
        @endif
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
            <h1>{{ $isFinalApproval ? 'Permohonan Disetujui Sepenuhnya' : 'Permohonan Disetujui' }}</h1>
            <div class="header-subtitle">Sistem Informasi Penjadwalan & Operasional - Krisanthium</div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Greeting -->
            <p class="greeting"><strong>Yth. Bapak/Ibu {{ $requester->name }},</strong></p>

            @if($isFinalApproval)
            <!-- Success Message - Final -->
            <div class="message success">
                <p style="margin: 0; font-size: 12pt;">
                    <strong>Selamat! Permohonan Anda telah disetujui oleh seluruh level persetujuan yang berwenang.</strong><br><br>
                    Permohonan Anda dengan nomor <strong>{{ $employeeRequest->request_number }}</strong> telah melalui seluruh proses approval dan dinyatakan <strong>Disetujui</strong>.
                </p>
            </div>
            @else
            <!-- Success Message - Intermediate -->
            <div class="message success">
                <p style="margin: 0; font-size: 12pt;">
                    Permohonan Anda dengan nomor <strong>{{ $employeeRequest->request_number }}</strong> telah disetujui oleh {{ $approver->name }}.<br><br>
                    Saat ini permohonan sedang diproses ke level persetujuan berikutnya. Anda akan menerima notifikasi lebih lanjut setelah melalui seluruh tahapan approval.
                </p>
            </div>
            @endif

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

            <!-- Approval Info -->
            <div class="approval-info">
                <div class="approval-info-title">Informasi Persetujuan</div>
                <p>
                    <strong>Disetujui oleh:</strong> {{ $approver->name }}<br>
                    <strong>Jabatan:</strong> {{ $approver->jabatanObj->nama_jabatan ?? '-' }}<br>
                    <strong>Waktu Persetujuan:</strong> {{ now()->format('d F Y') }} pukul {{ now()->format('H:i') }} WIB<br>
                    @if($currentLevel)
                    <strong>Level:</strong> {{ ucfirst(str_replace('_', ' ', $currentLevel)) }}
                    @endif
                </p>
            </div>

            @if(!$isFinalApproval && $nextApprovers && $nextApprovers->isNotEmpty())
            <!-- Next Approvers Info -->
            <div style="margin: 24px 0;">
                <div style="background-color: #e3f2fd; border: 1px solid #1976d2; padding: 20px;">
                    <div style="font-weight: 600; font-size: 11pt; color: #1976d2; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 1px;">
                        Menunggu Persetujuan Berikutnya
                    </div>
                    <p style="margin: 0 0 12px 0; line-height: 1.8; font-size: 11pt;">
                        Permohonan Anda sedang diproses ke level persetujuan berikutnya. Berikut adalah approver yang akan memproses permohonan Anda:
                    </p>
                    <table style="width: 100%; border-collapse: collapse; margin: 0; font-size: 11pt;">
                        @foreach($nextApprovers as $index => $nextApprover)
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #bbdefb; vertical-align: top;">
                                <span style="font-weight: 600; color: #333;">{{ $index + 1 }}. {{ $nextApprover->name }}</span>
                                @if($nextApprover->jabatanObj)
                                <span style="color: #666; margin-left: 8px;">({{ $nextApprover->jabatanObj->nama_jabatan }})</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            @endif

            @if(!$isFinalApproval)
            <!-- Next Approval Notice -->
            <p style="margin-top: 24px; font-size: 11pt; text-align: justify;">
                Permohonan Anda saat ini sedang menunggu persetujuan dari level berikutnya sesuai dengan alur approval yang berlaku. Proses ini memerlukan waktu tergantung dari ketersediaan approver yang berwenang. Anda akan menerima notifikasi email setelah ada update status permohonan Anda.
            </p>
            @endif

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
