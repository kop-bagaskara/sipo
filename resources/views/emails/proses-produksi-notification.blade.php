<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $setting->process_name }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 30px; text-align: center;">
            <h1 style="margin: 0; font-size: 24px; font-weight: 600;">{{ $setting->process_name }}</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;">{{ $reminder['description'] }}</p>
        </div>

        <!-- Content -->
        <div style="padding: 30px;">
            <!-- Job Information -->
            <div style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #28a745;">
                <h3 style="margin: 0 0 15px 0; color: #28a745; font-size: 18px;">📋 Informasi Job</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057; width: 30%;">Job Code:</td>
                        <td style="padding: 8px 0; color: #212529;">{{ $jobData['job_code'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057;">Job Name:</td>
                        <td style="padding: 8px 0; color: #212529;">{{ $jobData['job_name'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057;">Customer:</td>
                        <td style="padding: 8px 0; color: #212529;">{{ $jobData['customer'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057;">Product:</td>
                        <td style="padding: 8px 0; color: #212529;">{{ $jobData['product'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057;">Qty Order:</td>
                        <td style="padding: 8px 0; color: #212529;">{{ number_format((float)$jobData['qty_order_estimation']) }} pcs</td>
                    </tr>
                </table>
            </div>

            <!-- Lead Time Information -->
            <div style="background-color: #e3f2fd; border-radius: 8px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #2196f3;">
                <h3 style="margin: 0 0 15px 0; color: #1976d2; font-size: 18px;">⏰ Lead Time Configuration</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057; width: 30%;">Total Lead Time:</td>
                        <td style="padding: 8px 0; color: #212529; font-weight: 600; color: #1976d2;">{{ $jobData['total_lead_time_days'] }} hari</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057;">Development:</td>
                        <td style="padding: 8px 0; color: #212529;">14 hari</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057;">Material Lead Time:</td>
                        <td style="padding: 8px 0; color: #212529;">{{ $jobData['max_lead_time_days'] }} hari</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057;">Produksi Hours:</td>
                        <td style="padding: 8px 0; color: #212529;">{{ $jobData['produksi_hours'] }} jam</td>
                    </tr>
                </table>
            </div>

            <!-- Timeline Information -->
            <div style="background-color: #fff3cd; border-radius: 8px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #ffc107;">
                <h3 style="margin: 0 0 15px 0; color: #856404; font-size: 18px;">📅 Timeline Produksi</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057; width: 30%;">Dimulai:</td>
                        <td style="padding: 8px 0; color: #212529;">{{ $jobData['lead_time_started_at'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057;">Deadline:</td>
                        <td style="padding: 8px 0; color: #212529; font-weight: 600; color: #dc3545;">{{ $jobData['production_deadline'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057;">Sisa Waktu:</td>
                        <td style="padding: 8px 0; color: #212529; font-weight: 600; color: #dc3545;">{{ $jobData['days_left'] }} hari</td>
                    </tr>
                </table>
            </div>

            <!-- Material Information -->
            @if(isset($jobData['materials']) && count($jobData['materials']) > 0)
            <div style="background-color: #f8d7da; border-radius: 8px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #dc3545;">
                <h3 style="margin: 0 0 15px 0; color: #721c24; font-size: 18px;">📦 Material Khusus</h3>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach($jobData['materials'] as $material)
                    <li style="margin-bottom: 8px; color: #721c24;">
                        <strong>{{ $material['name'] }}:</strong> {{ $material['days'] }} hari
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Action Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ config('app.url') }}/sipo/development/rnd-workspace/{{ $jobData['id'] }}" 
                   style="display: inline-block; background: linear-gradient(135deg, #28a745, #20c997); color: white; text-decoration: none; padding: 15px 30px; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);">
                    {{ $additionalData['action_text'] ?? 'Lihat Detail Job' }}
                </a>
            </div>

            <!-- Additional Information -->
            @if(isset($additionalData['notes']) && $additionalData['notes'])
            <div style="background-color: #e9ecef; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                <h4 style="margin: 0 0 10px 0; color: #495057; font-size: 16px;">📝 Catatan Tambahan</h4>
                <p style="margin: 0; color: #6c757d; line-height: 1.6;">{{ $additionalData['notes'] }}</p>
            </div>
            @endif

            <!-- Footer -->
            <div style="border-top: 1px solid #e9ecef; padding-top: 20px; text-align: center; color: #6c757d; font-size: 14px;">
                <p style="margin: 0 0 10px 0;">Email ini dikirim secara otomatis oleh sistem SiP Krisanthium</p>
                <p style="margin: 0;">
                    <a href="{{ config('app.url') }}/sipo" style="color: #28a745; text-decoration: none;">SiP Krisanthium</a> | 
                    <a href="{{ config('app.url') }}/sipo/development/rnd-workspace" style="color: #28a745; text-decoration: none;">Development Workspace</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
