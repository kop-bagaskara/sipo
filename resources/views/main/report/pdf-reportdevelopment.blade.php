<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Development</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        
        .header h2 {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 16px;
            font-weight: normal;
        }
        
        .filter-info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        
        .filter-info p {
            margin: 5px 0;
            font-size: 11px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-in-progress {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORT DEVELOPMENT</h1>
        <h2>Sistem Informasi Proses Operasional Krisan</h2>
    </div>
    
    <div class="filter-info">
        <p><strong>Periode:</strong> 
            @if($start_date && $end_date)
                {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}
            @else
                Semua Data
            @endif
        </p>
        @if($status_filter)
            <p><strong>Status:</strong> {{ ucfirst(str_replace('-', ' ', $status_filter)) }}</p>
        @endif
        <p><strong>Tanggal Generate:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 8%;">Job Code</th>
                <th style="width: 15%;">Job Name</th>
                <th style="width: 12%;">Customer</th>
                <th style="width: 12%;">Product</th>
                <th style="width: 10%;">Marketing</th>
                <th style="width: 8%;">Tanggal Mulai</th>
                <th style="width: 8%;">Deadline</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 6%;">Progress</th>
                <th style="width: 6%;">Prioritas</th>
                <th style="width: 6%;">Job Type</th>
                <th style="width: 8%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $row->job_code }}</td>
                    <td>{{ $row->job_name }}</td>
                    <td>{{ $row->customer ?? '-' }}</td>
                    <td>{{ $row->product ?? '-' }}</td>
                    <td>{{ $row->marketingUser->name ?? '-' }}</td>
                    <td class="text-center">{{ $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">{{ $row->job_deadline ? \Carbon\Carbon::parse($row->job_deadline)->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">
                        @php
                            $statusMap = [
                                'DRAFT' => ['class' => 'status-pending', 'text' => 'Draft'],
                                'OPEN' => ['class' => 'status-in-progress', 'text' => 'Open'],
                                'IN_PROGRESS_PREPRESS' => ['class' => 'status-in-progress', 'text' => 'In Progress Prepress'],
                                'FINISH_PREPRESS' => ['class' => 'status-in-progress', 'text' => 'Finish Prepress'],
                                'MEETING_OPP' => ['class' => 'status-in-progress', 'text' => 'Meeting OPP'],
                                'READY_FOR_CUSTOMER' => ['class' => 'status-in-progress', 'text' => 'Ready for Customer'],
                                'SCHEDULED_FOR_PRODUCTION' => ['class' => 'status-in-progress', 'text' => 'Scheduled for Production'],
                                'PRODUCTION_COMPLETED' => ['class' => 'status-in-progress', 'text' => 'Production Completed'],
                                'PRODUCTION_APPROVED_BY_RND' => ['class' => 'status-in-progress', 'text' => 'Production Approved by R&D'],
                                'WAITING_MPP' => ['class' => 'status-in-progress', 'text' => 'Waiting MPP'],
                                'MPP_APPROVED' => ['class' => 'status-in-progress', 'text' => 'MPP Approved'],
                                'SALES_ORDER_CREATED' => ['class' => 'status-in-progress', 'text' => 'Sales Order Created'],
                                'COMPLETED' => ['class' => 'status-completed', 'text' => 'Completed']
                            ];
                            $statusInfo = $statusMap[$row->status_job] ?? ['class' => 'status-pending', 'text' => $row->status_job];
                        @endphp
                        <span class="status-badge {{ $statusInfo['class'] }}">{{ $statusInfo['text'] }}</span>
                    </td>
                    <td class="text-center">
                        @php
                            $statusProgress = [
                                'DRAFT' => 10,
                                'OPEN' => 20,
                                'IN_PROGRESS_PREPRESS' => 30,
                                'FINISH_PREPRESS' => 40,
                                'MEETING_OPP' => 50,
                                'READY_FOR_CUSTOMER' => 60,
                                'SCHEDULED_FOR_PRODUCTION' => 70,
                                'PRODUCTION_COMPLETED' => 80,
                                'PRODUCTION_APPROVED_BY_RND' => 90,
                                'WAITING_MPP' => 95,
                                'MPP_APPROVED' => 98,
                                'SALES_ORDER_CREATED' => 99,
                                'COMPLETED' => 100
                            ];
                            $progress = $statusProgress[$row->status_job] ?? 0;
                        @endphp
                        {{ $progress }}%
                    </td>
                    <td class="text-center">{{ $row->prioritas_job ?? '-' }}</td>
                    <td class="text-center">
                        @if($row->job_type === 'new')
                            Produk Baru
                        @elseif($row->job_type === 'repeat')
                            Produk Repeat
                        @else
                            {{ $row->job_type ?? '-' }}
                        @endif
                    </td>
                    <td>{{ $row->catatan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center">Tidak ada data yang ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dokumen ini dibuat secara otomatis oleh sistem pada {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
        <p>Total Data: {{ count($data) }} record</p>
    </div>
</body>
</html>
