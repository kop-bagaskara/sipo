<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $setting->process_name }} - SIPO Krisan</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: Arial, sans-serif;">
    <div style="max-width: 800px; margin: 0 auto; background-color: #ffffff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <!-- Header -->
        <div style="background-color: #059669; color: #ffffff; padding: 24px; text-align: center;">
            <h1 style="margin: 0 0 8px 0; font-size: 24px; font-weight: bold;">{{ $setting->process_name }}</h1>
            <p style="margin: 0; color: #d1fae5; font-size: 16px;">{{ $setting->description }}</p>
        </div>

        <!-- Body -->
        <div style="padding: 24px;">
            <!-- Reminder Info -->
            <div style="background-color: #d1fae5; border-left: 4px solid #059669; padding: 16px; margin-bottom: 24px;">
                <h3 style="margin: 0 0 8px 0; color: #047857; font-size: 18px; font-weight: 600;">{{ $reminder['description'] }}</h3>
                <p style="margin: 0; color: #374151; font-size: 14px;">
                    @if($additionalData['notification_type'] === 'prepress')
                        Job development telah dikirim ke Prepress dan memerlukan perhatian Anda.
                    @elseif($additionalData['notification_type'] === 'prepress_reminder')
                        @if($additionalData['reminder_type'] === 'H-2')
                            Job prepress akan segera mendekati deadline (H-2). Silakan periksa progress dan pastikan dapat diselesaikan tepat waktu.
                        @elseif($additionalData['reminder_type'] === 'H-1')
                            Job prepress akan mendekati deadline besok (H-1). Segera selesaikan job ini untuk menghindari keterlambatan.
                        @endif
                    @endif
                </p>
            </div>

            <!-- Job Information -->
            <div style="background-color: #ffffff; border: 1px solid #e5e7eb; margin-bottom: 24px; overflow: hidden;">
                <div style="background-color: #374151; color: #ffffff; padding: 16px 24px;">
                    <h4 style="margin: 0; font-size: 18px; font-weight: 600;">Detail Job Development</h4>
                </div>
                <table style="width: 100%; border-collapse: collapse;">
                    <tbody>
                        <tr>
                            <td style="padding: 12px 24px; background-color: #f9fafb; font-weight: 600; color: #374151; width: 30%; border-bottom: 1px solid #e5e7eb;">Job Code</td>
                            <td style="padding: 12px 24px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ $jobData['job_code'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 24px; background-color: #f9fafb; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Job Name</td>
                            <td style="padding: 12px 24px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ $jobData['job_name'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 24px; background-color: #f9fafb; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Customer</td>
                            <td style="padding: 12px 24px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ $jobData['customer'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 24px; background-color: #f9fafb; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Product</td>
                            <td style="padding: 12px 24px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ $jobData['product'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 24px; background-color: #f9fafb; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Kode Design</td>
                            <td style="padding: 12px 24px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ $jobData['kode_design'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 24px; background-color: #f9fafb; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Dimension</td>
                            <td style="padding: 12px 24px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ $jobData['dimension'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 24px; background-color: #f9fafb; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Material</td>
                            <td style="padding: 12px 24px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ $jobData['material'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 24px; background-color: #f9fafb; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Total Color</td>
                            <td style="padding: 12px 24px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ $jobData['total_color'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 24px; background-color: #f9fafb; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Qty Order</td>
                            <td style="padding: 12px 24px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ $jobData['qty_order_estimation'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 24px; background-color: #f9fafb; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Job Type</td>
                            <td style="padding: 12px 24px; color: #111827; border-bottom: 1px solid #e5e7eb;">
                                @if(isset($jobData['job_type']))
                                    @if($jobData['job_type'] === 'new')
                                        <span style="background-color: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 16px; font-size: 12px; font-weight: 500;">
                                            Produk Baru
                                        </span>
                                    @else
                                        <span style="background-color: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 16px; font-size: 12px; font-weight: 500;">
                                            Produk Repeat
                                        </span>
                                    @endif
                                @else
                                    <span style="color: #6b7280;">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 24px; background-color: #f9fafb; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Prioritas</td>
                            <td style="padding: 12px 24px; color: #111827; border-bottom: 1px solid #e5e7eb;">
                                @if(isset($jobData['prioritas_job']))
                                    @if(strtolower($jobData['prioritas_job']) === 'urgent')
                                        <span style="background-color: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 16px; font-size: 12px; font-weight: 500;">
                                            {{ $jobData['prioritas_job'] }}
                                        </span>
                                    @else
                                        <span style="background-color: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 16px; font-size: 12px; font-weight: 500;">
                                            {{ $jobData['prioritas_job'] }}
                                        </span>
                                    @endif
                                @else
                                    <span style="color: #6b7280;">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 24px; background-color: #f9fafb; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Tanggal Input</td>
                            <td style="padding: 12px 24px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ isset($jobData['tanggal']) ? \Carbon\Carbon::parse($jobData['tanggal'])->format('d M Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 24px; background-color: #f9fafb; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Prepress Deadline</td>
                            <td style="padding: 12px 24px; color: #111827; border-bottom: 1px solid #e5e7eb;">
                                @if(isset($jobData['prepress_deadline']))
                                    {{ \Carbon\Carbon::parse($jobData['prepress_deadline'])->format('d M Y') }}
                                    @if($additionalData['notification_type'] === 'prepress_reminder')
                                        @php
                                            $deadline = \Carbon\Carbon::parse($jobData['prepress_deadline']);
                                            $now = \Carbon\Carbon::now();
                                            $daysLeft = $now->diffInDays($deadline, false);
                                        @endphp
                                        <br><small style="color: {{ $daysLeft < 0 ? '#dc2626' : ($daysLeft <= 1 ? '#f59e0b' : '#059669') }};">
                                            @if($daysLeft < 0)
                                                Terlambat {{ abs($daysLeft) }} hari
                                            @elseif($daysLeft == 0)
                                                Deadline hari ini!
                                            @else
                                                {{ $daysLeft }} hari lagi
                                            @endif
                                        </small>
                                    @endif
                                @else
                                    <span style="color: #6b7280;">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 24px; background-color: #f9fafb; font-weight: 600; color: #374151;">Catatan</td>
                            <td style="padding: 12px 24px; color: #111827;">{{ $jobData['catatan'] ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Job Order Details -->
            @if(isset($jobData['job_order']) && is_array($jobData['job_order']) && count($jobData['job_order']) > 0)
                <div style="background-color: #fef3c7; border: 1px solid #f59e0b; margin-bottom: 24px; overflow: hidden;">
                    <div style="background-color: #f59e0b; color: #ffffff; padding: 16px 24px;">
                        <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Job Order Details</h5>
                    </div>
                    <div style="padding: 24px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background-color: #fef3c7;">
                                    <th style="padding: 12px; text-align: left; border: 1px solid #f59e0b; color: #92400e; font-weight: 600;">No</th>
                                    <th style="padding: 12px; text-align: left; border: 1px solid #f59e0b; color: #92400e; font-weight: 600;">Jenis Pekerjaan</th>
                                    <th style="padding: 12px; text-align: left; border: 1px solid #f59e0b; color: #92400e; font-weight: 600;">Unit Job</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jobData['job_order'] as $index => $order)
                                    <tr>
                                        <td style="padding: 12px; border: 1px solid #f59e0b; color: #92400e;">{{ $index + 1 }}</td>
                                        <td style="padding: 12px; border: 1px solid #f59e0b; color: #92400e;">{{ $order['jenis_pekerjaan'] ?? $order }}</td>
                                        <td style="padding: 12px; border: 1px solid #f59e0b; color: #92400e;">{{ $order['unit_job'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Action Button -->
            {{-- <div style="text-align: center; margin-bottom: 24px;">
                <a href="{{ $additionalData['action_url'] }}" style="background-color: #059669; color: #ffffff; padding: 12px 32px; text-decoration: none; border-radius: 6px; font-weight: 600; display: inline-block; font-size: 16px;">
                    {{ $additionalData['action_text'] }}
                </a>
            </div> --}}
        </div>

        <!-- Footer -->
        <div style="background-color: #f9fafb; padding: 24px; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 14px;">Email ini dikirim otomatis oleh sistem SIPO Development</p>
            <div>
                <a href="{{ config('app.url') }}/development/rnd-workspace" style="color: #059669; text-decoration: none; font-weight: 500; font-size: 14px; margin-right: 16px;">RnD Workspace</a>
                <a href="{{ config('app.url') }}/development/marketing-jobs" style="color: #6b7280; text-decoration: none; font-weight: 500; font-size: 14px;">Marketing Jobs</a>
            </div>
        </div>
    </div>
</body>
</html>
