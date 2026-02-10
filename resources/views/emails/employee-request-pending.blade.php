<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permohonan Baru Menunggu Approval</title>
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
            border-bottom: 3px solid #1e3a5f;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 18px;
            font-weight: 600;
            color: #1e3a5f;
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
            font-weight: bold;
        }
        .opening {
            margin-bottom: 20px;
            text-align: justify;
        }
        .info-section {
            background-color: #f9f9f9;
            border: 1px solid #d0d0d0;
            padding: 20px;
            margin: 24px 0;
        }
        .info-section-title {
            font-weight: bold;
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
            font-weight: bold;
            color: #333;
            width: 35%;
            padding-right: 20px;
        }
        .details-table td:last-child {
            color: #1a1a1a;
        }
        .detail-section {
            margin: 24px 0;
        }
        .detail-section h3 {
            font-size: 12pt;
            font-weight: bold;
            color: #1e3a5f;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .approval-flow {
            background-color: #f9f9f9;
            border: 1px solid #d0d0d0;
            padding: 16px;
            margin: 24px 0;
        }
        .approval-flow-title {
            font-weight: bold;
            font-size: 11pt;
            color: #1e3a5f;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .approval-step {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e8e8e8;
        }
        .approval-step:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .step-number {
            min-width: 28px;
            height: 28px;
            background-color: #1e3a5f;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
            margin-right: 16px;
            font-family: 'Arial', sans-serif;
        }
        .step-number.pending {
            background-color: #b0b0b0;
        }
        .step-number.completed {
            background-color: #4a7c4c;
        }
        .step-label {
            font-size: 11pt;
            color: #333;
        }
        .step-label.current {
            color: #1e3a5f;
            font-weight: bold;
        }
        .step-label small {
            color: #666;
            font-style: italic;
            margin-left: 8px;
        }
        .action-section {
            margin: 32px 0;
            padding: 20px;
            background-color: #fff8e7;
            border-left: 4px solid #f5a623;
        }
        .action-section p {
            margin: 0;
            font-size: 11pt;
            color: #333;
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
            <h1>Permohonan Menunggu Approval</h1>
            <div class="header-subtitle">Sistem Informasi Penjadwalan & Operasional - Krisanthium</div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Greeting -->
            <p class="greeting"><strong>Yth. Bapak/Ibu {{ $approver->name }},</strong></p>

            <!-- Opening -->
            <p class="opening">
                Bersama ini kami sampaikan bahwa terdapat permohonan baru yang memerlukan persetujuan dari Bapak/Ibu.
            </p>

            <!-- Main Info Section -->
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
                        <td>Nama Pemohon</td>
                        <td>: {{ $requester->name }}</td>
                    </tr>
                    <tr>
                        <td>Bagian/Divisi</td>
                        <td>: {{ $requester->divisi && $requester->divisiObj ? $requester->divisiObj->nama_divisi : '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Pengajuan</td>
                        <td>: {{ $employeeRequest->created_at->format('d F Y') }} pukul {{ $employeeRequest->created_at->format('H:i') }} WIB</td>
                    </tr>
                </table>
            </div>

            <!-- Detail Section -->
            @if(!empty($employeeRequest->formatted_request_data))
            <div class="detail-section">
                <h3>Detail Permohonan</h3>
                <table class="details-table">
                    @foreach($employeeRequest->formatted_request_data as $label => $value)
                        @if(!in_array($label, ['Nama', 'Bagian']))
                        <tr>
                            <td>{{ $label }}</td>
                            <td>: {{ is_array($value) ? (is_object(reset($value)) || is_array(reset($value)) ? json_encode($value) : implode(', ', $value)) : $value }}</td>
                        </tr>
                        @endif
                    @endforeach
                </table>
            </div>
            @endif

            <!-- Approval Flow -->
            @if(!empty($approvalChain))
            <div class="approval-flow">
                <div class="approval-flow-title">Alur Persetujuan</div>
                @php
                    $currentOrder = $employeeRequest->current_approval_order ?? 0;
                @endphp
                @foreach($approvalChain as $level => $approverData)
                    @php
                        $order = $approverData['approval_order'] ?? 0;
                        $isCurrent = $order == ($currentOrder + 1);
                        $isPast = $order <= $currentOrder;
                    @endphp
                    <div class="approval-step">
                        <div class="step-number {{ $isPast ? 'completed' : ($isCurrent ? '' : 'pending') }}">
                            {{ $isPast ? '✓' : $order }}
                        </div>
                        <div class="step-label {{ $isCurrent ? 'current' : '' }}">
                            {{ $approverData['level_name'] }}
                            @if($isCurrent)
                                <small>(Persetujuan Bapak/Ibu diperlukan)</small>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

            <!-- Action Section -->
            <div class="action-section">
                <p>
                    <strong>Mohon untuk melakukan approval pada permohonan ini dengan login ke sistem SIPO.</strong><br>
                    Terima kasih atas perhatian dan kerjasama Bapak/Ibu.
                </p>
            </div>

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
