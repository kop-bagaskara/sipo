<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Prepress Selesai - {{ $jobData['job_code'] ?? 'Job' }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">

        <!-- Header -->
        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 30px; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 24px; font-weight: bold;">
                🎉 Job Prepress Selesai
            </h1>
            <p style="color: #d1fae5; margin: 10px 0 0 0; font-size: 16px;">
                SIPO Development System
            </p>
        </div>

        <!-- Content -->
        <div style="padding: 30px;">
            <!-- Greeting -->
            <div style="margin-bottom: 25px;">
                <h2 style="color: #2d3748; margin: 0 0 10px 0; font-size: 20px;">
                    Halo {{ $currentUser->name ?? 'Team' }}! 👋
                </h2>
                <p style="color: #4a5568; margin: 0; font-size: 16px; line-height: 1.5;">
                    Job prepress telah berhasil diselesaikan. Berikut detail job yang telah selesai:
                </p>
            </div>

            <!-- Job Details Card -->
            <div style="background: #f0fdf4; border-radius: 10px; padding: 25px; margin-bottom: 25px; border-left: 4px solid #10b981;">
                <h3 style="color: #2d3748; margin: 0 0 20px 0; font-size: 18px; font-weight: bold;">
                    📋 Detail Job Selesai
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

            <!-- Completion Info -->
            <div style="background: #ecfdf5; border: 1px solid #10b981; border-radius: 10px; padding: 20px; margin-bottom: 25px;">
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <div style="background: #10b981; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                        <span style="color: white; font-size: 16px;">✅</span>
                    </div>
                    <h3 style="color: #065f46; margin: 0; font-size: 18px; font-weight: bold;">
                        Job Prepress Selesai
                    </h3>
                </div>
                <p style="color: #065f46; margin: 0; font-size: 16px; font-weight: bold;">
                    Selesai pada: {{ isset($jobData['finished_at']) ? \Carbon\Carbon::parse($jobData['finished_at'])->format('d F Y H:i') : now()->format('d F Y H:i') }}
                </p>
                <p style="color: #047857; margin: 10px 0 0 0; font-size: 14px;">
                    Job prepress telah berhasil diselesaikan dan siap untuk tahap selanjutnya.
                </p>
            </div>

            <!-- PIC Info -->
            @if(isset($jobData['pic_name']) && $jobData['pic_name'])
            <div style="background: #f8fafc; border-radius: 10px; padding: 20px; margin-bottom: 25px;">
                <h4 style="color: #2d3748; margin: 0 0 10px 0; font-size: 16px; font-weight: bold;">👤 PIC yang Menyelesaikan</h4>
                <p style="color: #4a5568; margin: 0; font-size: 14px; line-height: 1.5;">
                    <strong>{{ $jobData['pic_name'] }}</strong>
                    @if(isset($jobData['pic_email']) && $jobData['pic_email'])
                        <br><span style="color: #6b7280;">{{ $jobData['pic_email'] }}</span>
                    @endif
                </p>
            </div>
            @endif

            <!-- Action Button -->
            <div style="text-align: center; margin-bottom: 25px;">
                <a href="{{ config('app.url') }}/prepress/plan-selected"
                   style="display: inline-block; background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                          color: white; text-decoration: none; padding: 15px 30px; border-radius: 25px;
                          font-weight: bold; font-size: 16px; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);">
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

            <!-- Next Steps -->
            <div style="background: #eff6ff; border-radius: 10px; padding: 20px; text-align: center;">
                <h4 style="color: #1e40af; margin: 0 0 10px 0; font-size: 16px; font-weight: bold;">🚀 Langkah Selanjutnya</h4>
                <p style="color: #1e40af; margin: 0; font-size: 14px; line-height: 1.5;">
                    Job prepress telah selesai. Silakan lanjutkan ke tahap berikutnya sesuai dengan workflow yang telah ditentukan.
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
