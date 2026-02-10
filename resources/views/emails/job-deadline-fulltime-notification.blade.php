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
        <div style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 30px; text-align: center;">
            <h1 style="margin: 0; font-size: 24px; font-weight: 600;">{{ $setting->process_name }}</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;">{{ $reminder['description'] }}</p>
        </div>

        <!-- Content -->
        <div style="padding: 30px;">
            <!-- Job Information -->
            <div style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #dc3545;">
                <h3 style="margin: 0 0 15px 0; color: #dc3545; font-size: 18px;">📋 Informasi Job</h3>
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

            <!-- Deadline Information -->
            <div style="background-color: #fff3cd; border-radius: 8px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #ffc107;">
                <h3 style="margin: 0 0 15px 0; color: #856404; font-size: 18px;">⏰ Informasi Deadline</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057; width: 30%;">Job Deadline:</td>
                        <td style="padding: 8px 0; color: #212529; font-weight: 600; color: #dc3545;">{{ $jobData['job_deadline'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057;">Sisa Waktu:</td>
                        <td style="padding: 8px 0; color: #212529; font-weight: 600; color: #dc3545;">{{ $jobData['days_left'] }} hari</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057;">Status Job:</td>
                        <td style="padding: 8px 0; color: #212529;">{{ $jobData['status_job'] }}</td>
                    </tr>
                </table>
            </div>

            <!-- Progress Information -->
            <div style="background-color: #e3f2fd; border-radius: 8px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #2196f3;">
                <h3 style="margin: 0 0 15px 0; color: #1976d2; font-size: 18px;">📊 Progress Development</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057; width: 30%;">Progress:</td>
                        <td style="padding: 8px 0; color: #212529;">{{ $jobData['progress_percentage'] }}%</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057;">Last Update:</td>
                        <td style="padding: 8px 0; color: #212529;">{{ $jobData['last_updated'] }}</td>
                    </tr>
                </table>
            </div>

            <!-- Alert Message -->
            @if($jobData['days_left'] <= 0)
            <div style="background-color: #f8d7da; border-radius: 8px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #dc3545;">
                <h4 style="margin: 0 0 10px 0; color: #721c24; font-size: 16px;">🚨 DEADLINE SUDAH TERLEWAT!</h4>
                <p style="margin: 0; color: #721c24; line-height: 1.6;">Job ini sudah melewati deadline. Segera selesaikan atau koordinasikan dengan tim terkait.</p>
            </div>
            @elseif($jobData['days_left'] <= 5)
            <div style="background-color: #fff3cd; border-radius: 8px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #ffc107;">
                <h4 style="margin: 0 0 10px 0; color: #856404; font-size: 16px;">⚠️ DEADLINE SANGAT DEKAT!</h4>
                <p style="margin: 0; color: #856404; line-height: 1.6;">Job ini akan segera mencapai deadline. Pastikan semua proses sudah selesai.</p>
            </div>
            @else
            <div style="background-color: #d1ecf1; border-radius: 8px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #17a2b8;">
                <h4 style="margin: 0 0 10px 0; color: #0c5460; font-size: 16px;">ℹ️ REMINDER DEADLINE</h4>
                <p style="margin: 0; color: #0c5460; line-height: 1.6;">Job ini memiliki deadline yang perlu diperhatikan. Pastikan progress berjalan sesuai rencana.</p>
            </div>
            @endif

            <!-- Action Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ config('app.url') }}/sipo/development/rnd-workspace/{{ $jobData['id'] }}" 
                   style="display: inline-block; background: linear-gradient(135deg, #dc3545, #c82333); color: white; text-decoration: none; padding: 15px 30px; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);">
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
                    <a href="{{ config('app.url') }}/sipo" style="color: #dc3545; text-decoration: none;">SiP Krisanthium</a> | 
                    <a href="{{ config('app.url') }}/sipo/development/rnd-workspace" style="color: #dc3545; text-decoration: none;">Development Workspace</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
