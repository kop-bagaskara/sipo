<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Development Job Update</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .process-badge {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin: 10px 0;
        }
        .job-details {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
            min-width: 120px;
        }
        .detail-value {
            color: #212529;
            text-align: right;
        }
        .status-change {
            background-color: #e3f2fd;
            border: 1px solid #90caf9;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .status-change h4 {
            margin: 0 0 10px 0;
            color: #1976d2;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: 600;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🔄 Development Job Update</h1>
            <p>Notifikasi otomatis dari sistem SiP Krisanthium</p>
        </div>

        <p>Halo <strong>{{ $user->name }}</strong>,</p>

        <p>Job development telah mengalami perubahan status dan memerlukan perhatian Anda.</p>

        <div class="process-badge">{{ $processName }}</div>

        <div class="job-details">
            <h3 style="margin-top: 0; color: #007bff;">📋 Detail Job Development</h3>

            <div class="detail-row">
                <span class="detail-label">Job Code:</span>
                <span class="detail-value"><strong>{{ $job->job_code }}</strong></span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Job Name:</span>
                <span class="detail-value">{{ $job->job_name }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Customer:</span>
                <span class="detail-value">{{ $job->customer }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Product:</span>
                <span class="detail-value">{{ $job->product }}</span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">
                    <span style="background-color: #28a745; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px;">
                        {{ $job->status_job }}
                    </span>
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Priority:</span>
                <span class="detail-value">
                    <span style="background-color: {{ $job->prioritas_job === 'Urgent' ? '#dc3545' : '#28a745' }}; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px;">
                        {{ $job->prioritas_job }}
                    </span>
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Updated By:</span>
                <span class="detail-value">{{ auth()->user()->name ?? 'System' }}</span>
            </div>
        </div>

        @if($statusChange)
        <div class="status-change">
            <h4>🔄 Status Change Information</h4>
            <p><strong>Previous Status:</strong> {{ $statusChange['from'] ?? 'N/A' }}</p>
            <p><strong>Current Status:</strong> {{ $statusChange['to'] ?? 'N/A' }}</p>
            <p><strong>Change Time:</strong> {{ now()->format('d/m/Y H:i') }}</p>
            @if(isset($statusChange['reason']))
                <p><strong>Reason:</strong> {{ $statusChange['reason'] }}</p>
            @endif
        </div>
        @endif

        @if(!empty($additionalInfo))
        <div class="job-details">
            <h4 style="margin-top: 0; color: #007bff;">ℹ️ Additional Information</h4>
            @foreach($additionalInfo as $key => $value)
                <p><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</p>
            @endforeach
        </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ route('development.rnd-workspace.show', $job->id) }}" class="btn">Lihat Detail Job</a>
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh sistem SiP Krisanthium.</p>
            <p>Jika ada pertanyaan, silakan hubungi tim IT.</p>
            <p><small>© {{ date('Y') }} SiP Krisanthium. All rights reserved.</small></p>
        </div>
    </div>
</body>
</html>
