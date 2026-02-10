<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Development Job Reminder</title>
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
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            padding: 25px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0 0 8px 0;
            font-size: 22px;
            font-weight: 600;
            color: white;
        }
        .email-header p {
            margin: 0 0 15px 0;
            font-size: 15px;
            color: rgba(255, 255, 255, 0.9);
        }
        .reminder-badge {
            display: inline-block;
            background-color: {{ $daysBefore > 0 ? '#ffc107' : '#dc3545' }};
            color: {{ $daysBefore > 0 ? '#000' : '#fff' }};
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin: 0;
        }
        .content-section {
            padding: 20px;
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
            gap: 8px;
        }
        .section-title i {
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
            padding: 12px 8px;
            text-align: center;
            border-right: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
            font-size: 13px;
        }
        .status-table td:last-child {
            border-right: none;
        }
        .status-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .status-table tbody tr:hover {
            background-color: #e9ecef;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
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
        .days-overdue {
            color: #dc3545;
            font-weight: 600;
        }
        .days-warning {
            color: #ffc107;
            font-weight: 600;
        }
        .days-normal {
            color: #28a745;
            font-weight: 600;
        }
        .reminder-badge {
            display: inline-block;
            background-color: {{ $daysBefore > 0 ? '#ff9800' : '#f44336' }};
            color: white;
            padding: 12px 20px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            margin: 10px 0;
        }
        .urgent-notice {
            background-color: #ffebee;
            border: 1px solid #f44336;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .urgent-notice h4 {
            margin: 0 0 10px 0;
            color: #d32f2f;
        }
        .status-info {
            margin-top: 20px;
        }
        .status-info h3 {
            color: #495057;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .status-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .status-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-draft { background-color: #e9ecef; color: #495057; }
        .status-open { background-color: #cce5ff; color: #004085; }
        .status-in-progress { background-color: #d1ecf1; color: #0c5460; }
        .status-in-progress-prepress { background-color: #d1ecf1; color: #0c5460; }
        .status-finish-prepress { background-color: #d4edda; color: #155724; }
        .status-meeting-opp { background-color: #fff3cd; color: #856404; }
        .status-customer-approved { background-color: #d4edda; color: #155724; }
        .status-customer-rejected { background-color: #f8d7da; color: #721c24; }
        .status-ready-for-customer { background-color: #e2e3e5; color: #383d41; }
        .status-scheduled-for-production { background-color: #e2e3e5; color: #383d41; }
        .status-production-approved-by-rnd { background-color: #d1ecf1; color: #0c5460; }
        .status-production-completed { background-color: #d4edda; color: #155724; }
        .status-sales-order-created { background-color: #d4edda; color: #155724; }
        .status-completed { background-color: #d4edda; color: #155724; }
        .responsible-division {
            color: #495057;
            font-size: 14px;
        }
        .status-description-text {
            color: #6c757d;
            font-size: 14px;
            line-height: 1.5;
        }
        .status-description-text p {
            margin: 0;
        }
        .job-count {
            background-color: rgba(255, 255, 255, 0.3);
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 10px;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="email-container">

        <!-- Content -->
        <div class="content-section">
            <div class="">
                <h3>Dear Bapak/Ibu {{ $user->name }}</h3>
                <p>Berikut adalah List Progress Job Development yang Memerlukan Perhatian Anda</p>
            </div>

            <table style="width: 100%; border-collapse: collapse; border: 1px solid #000; margin-bottom: 20px;">
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th style="border: 1px solid #000; padding: 8px; text-align: left; font-weight: bold; width: 30%;">Job Code</th>
                        <th style="border: 1px solid #000; padding: 8px; text-align: left; font-weight: bold; width: 25%;">Customer</th>
                        <th style="border: 1px solid #000; padding: 8px; text-align: left; font-weight: bold; width: 25%;">Product</th>
                        <th style="border: 1px solid #000; padding: 8px; text-align: left; font-weight: bold; width: 10%;">Status</th>
                        <th style="border: 1px solid #000; padding: 8px; text-align: left; font-weight: bold; width: 10%;">H-</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($jobs) && is_array($jobs))
                        @foreach($jobs as $jobItem)
                        <tr>
                            <td style="border: 1px solid #000; padding: 8px;">{{ $jobItem->job_code }}</td>
                            <td style="border: 1px solid #000; padding: 8px;">{{ $jobItem->customer }}</td>
                            <td style="border: 1px solid #000; padding: 8px;">{{ $jobItem->product }}</td>
                            <td style="border: 1px solid #000; padding: 8px;">{{ $jobItem->status_job }}</td>
                            <td style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold; color: {{ $daysBefore <= 1 ? '#d32f2f' : '#ff9800' }};">
                                {{ $daysBefore }}
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td style="border: 1px solid #000; padding: 8px;">{{ $job->job_code }}</td>
                            <td style="border: 1px solid #000; padding: 8px;">{{ $job->customer }}</td>
                            <td style="border: 1px solid #000; padding: 8px;">{{ $job->product }}</td>
                            <td style="border: 1px solid #000; padding: 8px;">{{ $job->status_job }}</td>
                            <td style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: bold; color: {{ $daysBefore <= 1 ? '#d32f2f' : '#ff9800' }};">
                                {{ $daysBefore }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <div class="status-info">
                <h3>Detail Informasi Status</h3>
                <div class="status-description">
                    @php
                        $statusMapping = [
                            'DRAFT' => ['divisi' => 'RnD', 'deskripsi' => 'Job sedang dalam tahap draft dan perlu diselesaikan oleh divisi RnD'],
                            'OPEN' => ['divisi' => 'Prepress', 'deskripsi' => 'Job telah dikirim ke Prepress dan menunggu proses prepress'],
                            'IN_PROGRESS' => ['divisi' => 'Prepress', 'deskripsi' => 'Job sedang dalam proses prepress'],
                            'IN_PROGRESS_PREPRESS' => ['divisi' => 'Prepress', 'deskripsi' => 'Job sedang dalam proses prepress'],
                            'FINISH_PREPRESS' => ['divisi' => 'Marketing', 'deskripsi' => 'Prepress selesai, menunggu tindakan dari divisi Marketing'],
                            'MEETING_OPP' => ['divisi' => 'Marketing', 'deskripsi' => 'Job sedang dalam tahap meeting OPP dengan customer'],
                            'CUSTOMER_APPROVED' => ['divisi' => 'Marketing', 'deskripsi' => 'Customer telah approve, menunggu tindakan dari divisi Marketing'],
                            'CUSTOMER_REJECTED' => ['divisi' => 'Marketing', 'deskripsi' => 'Customer reject, perlu tindakan dari divisi Marketing'],
                            'READY_FOR_CUSTOMER' => ['divisi' => 'PPIC', 'deskripsi' => 'Job siap untuk customer, menunggu tindakan dari divisi PPIC'],
                            'SCHEDULED_FOR_PRODUCTION' => ['divisi' => 'Production', 'deskripsi' => 'Job telah dijadwalkan untuk diproses oleh Produksi'],
                            'PRODUCTION_APPROVED_BY_RND' => ['divisi' => 'Production', 'deskripsi' => 'Job telah disetujui RnD dan siap untuk produksi'],
                            'PRODUCTION_COMPLETED' => ['divisi' => 'Production', 'deskripsi' => 'Produksi telah selesai'],
                            'SALES_ORDER_CREATED' => ['divisi' => 'RnD', 'deskripsi' => 'Sales order telah dibuat'],
                            'COMPLETED' => ['divisi' => 'Completed', 'deskripsi' => 'Job telah selesai sepenuhnya']
                        ];
                        
                        // Ambil status dari jobs yang ada
                        $jobsToProcess = isset($jobs) && is_array($jobs) ? $jobs : [$job];
                        $statusCounts = [];
                        $uniqueStatuses = [];
                        
                        foreach($jobsToProcess as $jobItem) {
                            $currentStatus = $jobItem->status_job ?? 'DRAFT';
                            if (!isset($statusCounts[$currentStatus])) {
                                $statusCounts[$currentStatus] = 0;
                                $uniqueStatuses[] = $currentStatus;
                            }
                            $statusCounts[$currentStatus]++;
                        }
                    @endphp
                    
                    @foreach($uniqueStatuses as $currentStatus)
                        @php
                            $statusInfo = $statusMapping[$currentStatus] ?? ['divisi' => 'Unknown', 'deskripsi' => 'Status tidak diketahui'];
                            $count = $statusCounts[$currentStatus];
                        @endphp
                        
                        <div class="status-card">
                            <div class="status-header">
                                <span class="status-badge status-{{ strtolower(str_replace('_', '-', $currentStatus)) }}">
                                    {{ $currentStatus }}
                                    @if($count > 1)
                                        <span class="job-count">({{ $count }} jobs)</span>
                                    @endif
                                </span>
                                <span class="responsible-division">
                                    <strong>Divisi Bertanggung Jawab:</strong> {{ $statusInfo['divisi'] }}
                                </span>
                            </div>
                            <div class="status-description-text">
                                <p><strong>Penjelasan:</strong> {{ $statusInfo['deskripsi'] }}</p>
                                @if($count > 1)
                                    <p><strong>Jumlah Job:</strong> {{ $count }} job dengan status {{ $currentStatus }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</body>
</html>
