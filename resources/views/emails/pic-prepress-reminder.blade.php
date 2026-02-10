<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder PIC Prepress - {{ $jobData['job_code'] ?? 'Job' }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">

        <!-- Header -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 24px; font-weight: bold;">
                {{ $additionalData['reminder_type'] ?? 'Reminder' }} - PIC Prepress
            </h1>
            <p style="color: #e0e7ff; margin: 10px 0 0 0; font-size: 16px;">
                SIPO Development System
            </p>
        </div>

        <!-- Content -->
        <div style="padding: 30px;">
            <!-- Greeting -->
            <div style="margin-bottom: 25px;">
                <h2 style="color: #2d3748; margin: 0 0 10px 0; font-size: 20px;">
                    Halo {{ $currentUser->name ?? 'PIC Prepress' }}! 👋
                </h2>
                <p style="color: #4a5568; margin: 0; font-size: 16px; line-height: 1.5;">
                    Anda memiliki job prepress yang memerlukan perhatian segera. Berikut detail job yang perlu diselesaikan:
                </p>
            </div>

            <!-- Job Details Card -->
            <div style="background: #f8f9fc; border-radius: 10px; padding: 25px; margin-bottom: 25px; border-left: 4px solid #667eea;">
                <h3 style="color: #2d3748; margin: 0 0 20px 0; font-size: 18px; font-weight: bold;">
                    📋 Detail Job
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div>
                        <p style="margin: 0 0 5px 0; color: #718096; font-size: 14px; font-weight: 500;">Job Code</p>
                        <p style="margin: 0; color: #2d3748; font-size: 16px; font-weight: bold;">{{ $jobData['job_code'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p style="margin: 0 0 5px 0; color: #718096; font-size: 14px; font-weight: 500;">Job Name</p>
                        <p style="margin: 0; color: #2d3748; font-size: 16px; font-weight: bold;">{{ $jobData['job_name'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p style="margin: 0 0 5px 0; color: #718096; font-size: 14px; font-weight: 500;">Customer</p>
                        <p style="margin: 0; color: #2d3748; font-size: 16px; font-weight: bold;">{{ $jobData['customer'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p style="margin: 0 0 5px 0; color: #718096; font-size: 14px; font-weight: 500;">Product</p>
                        <p style="margin: 0; color: #2d3748; font-size: 16px; font-weight: bold;">{{ $jobData['product'] ?? '-' }}</p>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <p style="margin: 0 0 5px 0; color: #718096; font-size: 14px; font-weight: 500;">Qty Order</p>
                        <p style="margin: 0; color: #2d3748; font-size: 16px; font-weight: bold;">{{ $jobData['qty_order_estimation'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p style="margin: 0 0 5px 0; color: #718096; font-size: 14px; font-weight: 500;">Prioritas</p>
                        <p style="margin: 0; color: #2d3748; font-size: 16px; font-weight: bold;">{{ $jobData['prioritas_job'] ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Deadline Alert -->
            <div style="background: #fef5e7; border: 1px solid #f6ad55; border-radius: 10px; padding: 20px; margin-bottom: 25px;">
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <div style="background: #f6ad55; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <span style="color: white; font-size: 16px;">⏰</span>
                    </div>
                    <h3 style="color: #c05621; margin: 0; font-size: 18px; font-weight: bold;">
                        Deadline Alert
                    </h3>
                </div>
                <p style="color: #c05621; margin: 0; font-size: 16px; font-weight: bold;">
                    Deadline: {{ isset($jobData['prepress_deadline']) ? \Carbon\Carbon::parse($jobData['prepress_deadline'])->format('d F Y') : '-' }}
                </p>
                <p style="color: #9c4221; margin: 10px 0 0 0; font-size: 14px;">
                    {{ $additionalData['reminder_type'] ?? 'Reminder' }} - Job ini perlu segera diselesaikan!
                </p>
            </div>

            <!-- Action Button -->
            <div style="text-align: center; margin-bottom: 25px;">
                <a href="{{ config('app.url') }}/prepress/plan-selected"
                   style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                          color: white; text-decoration: none; padding: 15px 30px; border-radius: 25px;
                          font-weight: bold; font-size: 16px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                    📋 Lihat Job Prepress
                </a>
            </div>

            <!-- Additional Info -->
            @if(isset($jobData['catatan']) && $jobData['catatan'])
            <div style="background: #f7fafc; border-radius: 10px; padding: 20px; margin-bottom: 25px;">
                <h4 style="color: #2d3748; margin: 0 0 10px 0; font-size: 16px; font-weight: bold;">📝 Catatan</h4>
                <p style="color: #4a5568; margin: 0; font-size: 14px; line-height: 1.5;">{{ $jobData['catatan'] }}</p>
            </div>
            @endif

            <!-- Footer Message -->
            <div style="background: #edf2f7; border-radius: 10px; padding: 20px; text-align: center;">
                <p style="color: #4a5568; margin: 0; font-size: 14px; line-height: 1.5;">
                    <strong>💡 Tips:</strong> Pastikan untuk memperbarui status job secara berkala agar tim dapat memantau progress dengan baik.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div style="background-color: #f9fafb; padding: 24px; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 14px;">Email ini dikirim otomatis oleh sistem SIPO Development</p>
            <div>
                <a href="{{ config('app.url') }}/prepress/plan-selected" style="color: #059669; text-decoration: none; font-weight: 500; font-size: 14px; margin-right: 16px;">Prepress Plan</a>
                <a href="{{ config('app.url') }}/development/marketing-jobs" style="color: #6b7280; text-decoration: none; font-weight: 500; font-size: 14px;">Marketing Jobs</a>
            </div>
        </div>
    </div>
</body>
</html>
