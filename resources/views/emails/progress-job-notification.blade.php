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
        <div style="background: linear-gradient(135deg, #17a2b8, #138496); color: white; padding: 30px; text-align: center;">
            <h1 style="margin: 0; font-size: 24px; font-weight: 600;">{{ $setting->process_name }}</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;">{{ $reminder['description'] }}</p>
        </div>

        <!-- Content -->
        <div style="padding: 30px;">
            <!-- Job Information -->
            <div style="background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #17a2b8;">
                <h3 style="margin: 0 0 15px 0; color: #17a2b8; font-size: 18px;">📋 Informasi Job</h3>
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

            <!-- Status Change Information -->
            <div style="background-color: #d1ecf1; border-radius: 8px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #17a2b8;">
                <h3 style="margin: 0 0 15px 0; color: #0c5460; font-size: 18px;">🔄 Perubahan Status</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057; width: 30%;">Status Sebelum:</td>
                        <td style="padding: 8px 0; color: #212529;">
                            <span style="background-color: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 4px; font-size: 12px;">{{ $jobData['status_before_desc'] ?? $jobData['status_before'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057;">Status Sekarang:</td>
                        <td style="padding: 8px 0; color: #212529;">
                            <span style="background-color: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 12px;">{{ $jobData['status_after_desc'] ?? $jobData['status_after'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057;">Waktu Perubahan:</td>
                        <td style="padding: 8px 0; color: #212529;">{{ $jobData['change_time'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057;">Diubah Oleh:</td>
                        <td style="padding: 8px 0; color: #212529;">{{ $jobData['changed_by'] }}</td>
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
                        <td style="padding: 8px 0; font-weight: 600; color: #495057;">Job Deadline:</td>
                        <td style="padding: 8px 0; color: #212529;">{{ $jobData['job_deadline'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: 600; color: #495057;">Sisa Waktu:</td>
                        <td style="padding: 8px 0; color: #212529;">{{ $jobData['days_left'] }} hari</td>
                    </tr>
                </table>
            </div>

            <!-- Action Details -->
            @if(isset($jobData['action_description']) && $jobData['action_description'])
            <div style="background-color: #fff3cd; border-radius: 8px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #ffc107;">
                <h3 style="margin: 0 0 15px 0; color: #856404; font-size: 18px;">📝 Detail Aksi</h3>
                <p style="margin: 0; color: #856404; line-height: 1.6;">{{ $jobData['action_description'] }}</p>
            </div>
            @endif

            <!-- Notes -->
            @if(isset($jobData['notes']) && $jobData['notes'])
            <div style="background-color: #f8d7da; border-radius: 8px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #dc3545;">
                <h3 style="margin: 0 0 15px 0; color: #721c24; font-size: 18px;">📋 Catatan</h3>
                <p style="margin: 0; color: #721c24; line-height: 1.6;">{{ $jobData['notes'] }}</p>
            </div>
            @endif

            <!-- Action Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ config('app.url') }}/sipo/development/rnd-workspace/{{ $jobData['id'] }}" 
                   style="display: inline-block; background: linear-gradient(135deg, #17a2b8, #138496); color: white; text-decoration: none; padding: 15px 30px; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);">
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
                    <a href="{{ config('app.url') }}/sipo" style="color: #17a2b8; text-decoration: none;">SiP Krisanthium</a> | 
                    <a href="{{ config('app.url') }}/sipo/development/rnd-workspace" style="color: #17a2b8; text-decoration: none;">Development Workspace</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
