<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meeting OPP Notification</title>
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
            background: linear-gradient(135deg, #9c27b0, #7b1fa2);
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
        .meeting-badge {
            display: inline-block;
            background-color: #9c27b0;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin: 10px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-berjalan {
            background-color: #ff9800;
            color: white;
        }
        .status-selesai {
            background-color: #4caf50;
            color: white;
        }
        .status-belum_berjalan {
            background-color: #9e9e9e;
            color: white;
        }
        .job-details {
            background-color: #f8f9fa;
            border-left: 4px solid #9c27b0;
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
        .meeting-info {
            background-color: #f3e5f5;
            border: 1px solid #ce93d8;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .meeting-info h4 {
            margin: 0 0 10px 0;
            color: #7b1fa2;
        }
        .customer-response {
            background-color: #e8f5e8;
            border: 1px solid #4caf50;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .customer-response h4 {
            margin: 0 0 10px 0;
            color: #2e7d32;
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
            background-color: #9c27b0;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: 600;
        }
        .btn:hover {
            background-color: #7b1fa2;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>👥 Meeting OPP {{ $meetingNumber }} Notification</h1>
            <p>Notifikasi otomatis dari sistem SiP Krisanthium</p>
        </div>

        <p>Halo <strong>{{ $user->name }}</strong>,</p>

        <p>Meeting OPP {{ $meetingNumber }} untuk job development telah diupdate dan memerlukan perhatian Anda.</p>

        <div class="meeting-badge">Meeting OPP {{ $meetingNumber }}</div>

        <div class="job-details">
            <h3 style="margin-top: 0; color: #9c27b0;">📋 Detail Job Development</h3>

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
        </div>

        <div class="meeting-info">
            <h4>📅 Meeting Information</h4>

            <div class="detail-row">
                <span class="detail-label">Meeting Date:</span>
                <span class="detail-value">
                    <strong>{{ \Carbon\Carbon::parse($meetingData['meeting_date'])->format('d/m/Y') }}</strong>
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">
                    <span class="status-badge status-{{ $meetingData['status'] }}">
                        {{ ucfirst(str_replace('_', ' ', $meetingData['status'])) }}
                    </span>
                </span>
            </div>

            <div class="detail-row">
                <span class="detail-label">Customer Response:</span>
                <span class="detail-value">
                    <span style="background-color: {{ $meetingData['customer_response'] === 'acc' ? '#4caf50' : ($meetingData['customer_response'] === 'reject' ? '#f44336' : '#ff9800') }}; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px;">
                        {{ strtoupper($meetingData['customer_response']) }}
                    </span>
                </span>
            </div>
        </div>

        @if(isset($meetingData['customer_notes']) && $meetingData['customer_notes'])
        <div class="customer-response">
            <h4>💬 Customer Notes</h4>
            <p style="margin: 0; font-style: italic;">"{{ $meetingData['customer_notes'] }}"</p>
        </div>
        @endif

        @if(isset($meetingData['marketing_notes']) && $meetingData['marketing_notes'])
        <div class="job-details">
            <h4 style="margin-top: 0; color: #9c27b0;">📝 Marketing Notes</h4>
            <p style="margin: 0;">{{ $meetingData['marketing_notes'] }}</p>
        </div>
        @endif

        @if(isset($meetingData['rnd_notes']) && $meetingData['rnd_notes'])
        <div class="job-details">
            <h4 style="margin-top: 0; color: #9c27b0;">🔬 RnD Notes</h4>
            <p style="margin: 0;">{{ $meetingData['rnd_notes'] }}</p>
        </div>
        @endif

        @if($meetingData['customer_response'] === 'reject')
        <div style="background-color: #ffebee; border: 1px solid #f44336; border-radius: 8px; padding: 15px; margin: 20px 0;">
            <h4 style="margin: 0 0 10px 0; color: #d32f2f;">⚠️ Customer Reject</h4>
            <p style="margin: 0;">Customer telah menolak hasil Meeting OPP {{ $meetingNumber }}. Job akan dikembalikan ke Prepress untuk revisi.</p>
        </div>
        @elseif($meetingData['customer_response'] === 'acc')
        <div style="background-color: #e8f5e8; border: 1px solid #4caf50; border-radius: 8px; padding: 15px; margin: 20px 0;">
            <h4 style="margin: 0 0 10px 0; color: #2e7d32;">✅ Customer Approved</h4>
            <p style="margin: 0;">Customer telah menyetujui hasil Meeting OPP {{ $meetingNumber }}. Job dapat dilanjutkan ke tahap berikutnya.</p>
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
