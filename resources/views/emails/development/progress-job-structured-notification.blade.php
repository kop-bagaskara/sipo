<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $setting->process_name }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            line-height: 1.6;
        }
        .email-container {
            max-width: 100%;
            margin: 0;
            background-color: #ffffff;
            border-radius: 0;
            overflow: hidden;
            box-shadow: none;
        }
        .email-header {
            background: none;
            color: #5e5e5e;
            padding: 20px 0;
            text-align: left;
            border-bottom: 2px solid #ddd;
        }
        .email-header h1 {
            margin: 0 0 5px 0;
            font-size: 20px;
            font-weight: 600;
            color: #5e5e5e;
        }
        .email-header p {
            margin: 0;
            font-size: 14px;
            color: #5e5e5e;
        }
        .content-section {
            padding: 30px;
        }
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .info-table td {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
            vertical-align: top;
        }
        .info-table td:first-child {
            font-weight: 600;
            color: #5e5e5e;
            width: 30%;
        }
        .info-table td:last-child {
            color: #5e5e5e;
        }
        .section-title {
            color: #5e5e5e;
            font-size: 18px;
            font-weight: 600;
            margin: 0 0 15px 0;
            display: flex;
            align-items: center;
        }
        .section-title i {
            margin-right: 8px;
            font-size: 20px;
        }
        .status-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 0;
            overflow: hidden;
            box-shadow: none;
        }
        .status-table th {
            background-color: #5e5e5e;
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            font-size: 13px;
            border-right: 1px solid #5e5e5e;
        }
        .status-table th:last-child {
            border-right: none;
        }
        .status-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #dee2e6;
            border-right: 1px solid #f8f9fa;
            font-size: 13px;
            vertical-align: middle;
        }
        .status-table td:last-child {
            border-right: none;
        }
        .status-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .status-table tr:hover {
            background-color: #e8f5e8;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-in-progress {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-overdue {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        .days-overdue {
            color: #dc3545;
            font-weight: 600;
        }
        .days-warning {
            color: #fd7e14;
            font-weight: 600;
        }
        .days-normal {
            color: #28a745;
            font-weight: 600;
        }
        .escalation-section {
            background-color: #fff3cd;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            border-left: 4px solid #ffc107;
        }
        .escalation-section h3 {
            color: #856404;
            margin: 0 0 15px 0;
            font-size: 18px;
        }
        .escalation-section p {
            margin: 8px 0;
            color: #856404;
        }
        .escalation-list {
            margin: 15px 0;
        }
        .escalation-list li {
            margin: 5px 0;
            color: #856404;
        }
        .legend {
            background-color: #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        .legend h4 {
            margin: 0 0 15px 0;
            color: #495057;
            font-size: 16px;
        }
        .legend-item {
            display: inline-block;
            margin: 5px 15px 5px 0;
            font-size: 14px;
        }
        .legend-color {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 2px;
            margin-right: 8px;
            vertical-align: middle;
        }
        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #2E7D32, #1B5E20);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
            margin: 20px 0;
        }
        .footer {
            border-top: 1px solid #e9ecef;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            background-color: #f8f9fa;
        }
        .footer a {
            color: #2E7D32;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>Dear Bapak/Ibu {{ $recipient->name ?? 'User' }}</h1>
            <p>Berikut adalah List Progress Job Development yang Memerlukan Perhatian Anda:</p>
        </div>

        <!-- Content -->
        <div class="content-section" style="padding: 20px;">
            @if(isset($additionalData['notification_type']) && $additionalData['notification_type'] === 'input_awal_structured')
                <!-- Job Information untuk Input Awal -->
                <div class="info-box" style="margin-bottom: 20px;">
                    <h3 class="section-title">
                        <i>📋</i> Informasi Job
                    </h3>
                    <table class="info-table">
                        <tr>
                            <td>Job Code:</td>
                            <td><strong>{{ $jobData['job_code'] }}</strong></td>
                        </tr>
                        <tr>
                            <td>Job Name:</td>
                            <td>{{ $jobData['job_name'] ?? $jobData['product'] }}</td>
                        </tr>
                        <tr>
                            <td>Customer:</td>
                            <td>{{ $jobData['customer'] }}</td>
                        </tr>
                        <tr>
                            <td>Product:</td>
                            <td>{{ $jobData['product'] }}</td>
                        </tr>
                        <tr>
                            <td>Kode Design:</td>
                            <td>{{ $jobData['kode_design'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Dimension:</td>
                            <td>{{ $jobData['dimension'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Material:</td>
                            <td>{{ $jobData['material'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Total Color:</td>
                            <td>{{ $jobData['total_color'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Qty Order:</td>
                            <td>{{ number_format((float)$jobData['qty_order_estimation']) }} pcs</td>
                        </tr>
                        <tr>
                            <td>Job Type:</td>
                            <td>
                                @if(isset($jobData['job_type']))
                                    @if($jobData['job_type'] === 'new')
                                        <span class="status-badge status-completed">Produk Baru</span>
                                    @else
                                        <span class="status-badge status-pending">Produk Repeat</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Prioritas:</td>
                            <td>
                                @if(isset($jobData['prioritas_job']))
                                    @if(strtolower($jobData['prioritas_job']) === 'urgent')
                                        <span class="status-badge status-overdue">{{ $jobData['prioritas_job'] }}</span>
                                    @else
                                        <span class="status-badge status-completed">{{ $jobData['prioritas_job'] }}</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Job Deadline:</td>
                            <td>{{ $jobData['job_deadline'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Sisa Waktu:</td>
                            <td>
                                @if(isset($jobData['days_left']))
                                    @if($jobData['days_left'] < 0)
                                        <span class="days-overdue">{{ abs($jobData['days_left']) }} hari terlambat</span>
                                    @elseif($jobData['days_left'] <= 1)
                                        <span class="days-warning">{{ $jobData['days_left'] }} hari tersisa</span>
                                    @else
                                        <span class="days-normal">{{ $jobData['days_left'] }} hari tersisa</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Material Khusus untuk Produk Baru -->
                @if(isset($jobData['job_type']) && $jobData['job_type'] === 'new')
                    @if((isset($jobData['kertas_khusus']) && $jobData['kertas_khusus']) || 
                        (isset($jobData['tinta_khusus']) && $jobData['tinta_khusus']) || 
                        (isset($jobData['foil_khusus']) && $jobData['foil_khusus']) || 
                        (isset($jobData['pale_tooling_khusus']) && $jobData['pale_tooling_khusus']))
                    <div class="info-box" style="background-color: #fff3cd; border-left: 4px solid #ffc107; margin-bottom: 20px;">
                        <h3 class="section-title" style="color: #856404;">
                            <i>🔧</i> Material Khusus
                        </h3>
                        @if(isset($jobData['kertas_khusus']) && $jobData['kertas_khusus'])
                            <div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 12px; margin-bottom: 10px; border-radius: 4px;">
                                <strong style="color: #92400e;">Kertas Khusus:</strong>
                                <span style="color: #92400e;">{{ $jobData['kertas_khusus_detail'] ?? 'Tidak ada detail' }}</span>
                            </div>
                        @endif
                        @if(isset($jobData['tinta_khusus']) && $jobData['tinta_khusus'])
                            <div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 12px; margin-bottom: 10px; border-radius: 4px;">
                                <strong style="color: #92400e;">Tinta Khusus:</strong>
                                <span style="color: #92400e;">{{ $jobData['tinta_khusus_detail'] ?? 'Tidak ada detail' }}</span>
                            </div>
                        @endif
                        @if(isset($jobData['foil_khusus']) && $jobData['foil_khusus'])
                            <div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 12px; margin-bottom: 10px; border-radius: 4px;">
                                <strong style="color: #92400e;">Foil Khusus:</strong>
                                <span style="color: #92400e;">{{ $jobData['foil_khusus_detail'] ?? 'Tidak ada detail' }}</span>
                            </div>
                        @endif
                        @if(isset($jobData['pale_tooling_khusus']) && $jobData['pale_tooling_khusus'])
                            <div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 12px; border-radius: 4px;">
                                <strong style="color: #92400e;">Pale Tooling Khusus:</strong>
                                <span style="color: #92400e;">{{ $jobData['pale_tooling_khusus_detail'] ?? 'Tidak ada detail' }}</span>
                            </div>
                        @endif
                    </div>
                    @endif
                @endif

                <!-- Catatan -->
                @if(isset($jobData['notes']) && $jobData['notes'])
                <div class="info-box" style="margin-bottom: 20px;">
                    <h3 class="section-title">
                        <i>📝</i> Catatan
                    </h3>
                    <p style="margin: 0; color: #495057; line-height: 1.6;">{{ $jobData['notes'] }}</p>
                </div>
                @endif
            @else
                <!-- Production Status Table untuk Reminder -->
                <div class="info-box">
                    <h3 class="section-title">
                        <i>🔄</i> Status Produksi
                    </h3>
                    <table class="status-table" style="width: 100%; margin: 0;">
                        <thead>
                            <tr>
                                <th style="width: 8%;">No</th>
                                <th style="width: 20%;">Proses</th>
                                <th style="width: 15%;">Status Produksi</th>
                                <th style="width: 15%;">Status RnD Approval</th>
                                <th style="width: 18%;">Tanggal Produksi</th>
                                <th style="width: 12%;">Deadline</th>
                                <th style="width: 12%;">Selisih Hari (H-)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($jobData['production_schedules']) && count($jobData['production_schedules']) > 0)
                                @foreach($jobData['production_schedules'] as $index => $schedule)
                                <tr>
                                    <td style="text-align: center; font-weight: 600;">{{ $index + 1 }}</td>
                                    <td style="font-weight: 500;">{{ $schedule['proses'] ?? 'Process' }}</td>
                                    <td style="text-align: center;">
                                        <span class="status-badge status-{{ $schedule['status'] }}">
                                            {{ $schedule['status_label'] ?? ucfirst($schedule['status']) }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="status-badge status-{{ $schedule['rnd_approval_status'] ?? 'pending' }}">
                                            {{ $schedule['rnd_approval_status_label'] ?? 'Pending' }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">{{ $schedule['production_date_time'] ?? '-' }}</td>
                                    <td style="text-align: center;">{{ $schedule['deadline'] ?? '-' }}</td>
                                    <td style="text-align: center;">
                                        @if(isset($schedule['days_difference']))
                                            @if($schedule['days_difference'] < 0)
                                                <span class="days-overdue" style="font-weight: 600;">H{{ $schedule['days_difference'] }}</span>
                                            @elseif($schedule['days_difference'] <= 1)
                                                <span class="days-warning" style="font-weight: 600;">H{{ $schedule['days_difference'] }}</span>
                                            @else
                                                <span class="days-normal" style="font-weight: 600;">H-{{ $schedule['days_difference'] }}</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 40px; color: #6c757d; background-color: #f8f9fa;">
                                        <i style="font-size: 24px; margin-bottom: 8px; display: block;">📋</i>
                                        <strong>Belum Ada Production Schedule</strong><br>
                                        <small>Production schedule akan muncul setelah job masuk ke tahap produksi</small>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</body>
</html>
