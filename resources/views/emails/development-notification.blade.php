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
        <div style="background-color: #2563eb; color: #ffffff; padding: 24px; text-align: center;">
            <h1 style="margin: 0 0 8px 0; font-size: 24px; font-weight: bold;">{{ $setting->process_name }}</h1>
            <p style="margin: 0; color: #dbeafe; font-size: 16px;">{{ $setting->description }}</p>
        </div>

        <!-- Body -->
        <div style="padding: 24px;">
            <!-- Reminder Info -->
            <div style="background-color: #dbeafe; border-left: 4px solid #2563eb; padding: 16px; margin-bottom: 24px;">
                <h3 style="margin: 0 0 8px 0; color: #1e40af; font-size: 18px; font-weight: 600;">{{ $reminder['description'] }}</h3>
                <p style="margin: 0; color: #374151; font-size: 14px;">Job development baru telah diinput dan memerlukan perhatian Anda.</p>
            </div>

            <!-- Job Information -->
            <div style="background-color: #ffffff; border: 1px solid #e5e7eb; margin-bottom: 24px; overflow: hidden;">
                <div style="background-color: #374151; color: #ffffff; padding: 16px 24px;">
                    <h4 style="margin: 0; font-size: 18px; font-weight: 600;">Detail Job Development</h4>
                </div>
                <table style="width: 100%; border-collapse: collapse;">
                    <tbody>
                        <tr>
                            <td style="padding: 12px 24px; background-color: #f9fafb; font-weight: 600; color: #374151; width: 30%; border-bottom: 1px solid #e5e7eb;">Customer</td>
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
                            <td style="padding: 12px 24px; background-color: #f9fafb; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Warna</td>
                            <td style="padding: 12px 24px; color: #111827; border-bottom: 1px solid #e5e7eb;">
                                @if(isset($jobData['colors']) && is_array($jobData['colors']))
                                    @foreach($jobData['colors'] as $index => $color)
                                        @if($color)
                                            <span style="background-color: #dbeafe; color: #1e40af; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 500; margin-right: 4px; display: inline-block; margin-bottom: 4px;">
                                                {{ $index }}. {{ $color }}
                                            </span>
                                        @endif
                                    @endforeach
                                @else
                                    <span style="color: #6b7280;">N/A</span>
                                @endif
                            </td>
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
                            <td style="padding: 12px 24px; background-color: #f9fafb; font-weight: 600; color: #374151; border-bottom: 1px solid #e5e7eb;">Job Deadline</td>
                            <td style="padding: 12px 24px; color: #111827; border-bottom: 1px solid #e5e7eb;">{{ isset($jobData['job_deadline']) ? \Carbon\Carbon::parse($jobData['job_deadline'])->format('d M Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 24px; background-color: #f9fafb; font-weight: 600; color: #374151;">Catatan</td>
                            <td style="padding: 12px 24px; color: #111827;">{{ $jobData['catatan'] ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Material Khusus untuk Produk Baru -->
            @if(isset($jobData['job_type']) && $jobData['job_type'] === 'new')
                <div style="background-color: #fef3c7; border: 1px solid #f59e0b; margin-bottom: 24px; overflow: hidden;">
                    <div style="background-color: #f59e0b; color: #ffffff; padding: 16px 24px;">
                        <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Material Khusus</h5>
                    </div>
                    <div style="padding: 24px;">
                        @if(isset($jobData['kertas_khusus']) && $jobData['kertas_khusus'])
                            <div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 16px; margin-bottom: 16px;">
                                <h6 style="margin: 0 0 8px 0; color: #92400e; font-size: 14px; font-weight: 600;">Kertas Khusus</h6>
                                <p style="margin: 0; color: #92400e; font-size: 13px;">{{ $jobData['kertas_khusus_detail'] ?? 'Tidak ada detail' }}</p>
                            </div>
                        @endif

                        @if(isset($jobData['tinta_khusus']) && $jobData['tinta_khusus'])
                            <div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 16px; margin-bottom: 16px;">
                                <h6 style="margin: 0 0 8px 0; color: #92400e; font-size: 14px; font-weight: 600;">Tinta Khusus</h6>
                                <p style="margin: 0; color: #92400e; font-size: 13px;">{{ $jobData['tinta_khusus_detail'] ?? 'Tidak ada detail' }}</p>
                            </div>
                        @endif

                        @if(isset($jobData['foil_khusus']) && $jobData['foil_khusus'])
                            <div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 16px; margin-bottom: 16px;">
                                <h6 style="margin: 0 0 8px 0; color: #92400e; font-size: 14px; font-weight: 600;">Foil Khusus</h6>
                                <p style="margin: 0; color: #92400e; font-size: 13px;">{{ $jobData['foil_khusus_detail'] ?? 'Tidak ada detail' }}</p>
                            </div>
                        @endif

                        @if(isset($jobData['pale_tooling_khusus']) && $jobData['pale_tooling_khusus'])
                            <div style="background-color: #fef3c7; border: 1px solid #f59e0b; padding: 16px;">
                                <h6 style="margin: 0 0 8px 0; color: #92400e; font-size: 14px; font-weight: 600;">Pale Tooling Khusus</h6>
                                <p style="margin: 0; color: #92400e; font-size: 13px;">{{ $jobData['pale_tooling_khusus_detail'] ?? 'Tidak ada detail' }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Detail Perubahan untuk Produk Repeat -->
            @if(isset($jobData['job_type']) && $jobData['job_type'] === 'repeat')
                <div style="background-color: #dbeafe; border: 1px solid #3b82f6; margin-bottom: 24px; overflow: hidden;">
                    <div style="background-color: #3b82f6; color: #ffffff; padding: 16px 24px;">
                        <h5 style="margin: 0; font-size: 18px; font-weight: 600;">Detail Perubahan</h5>
                    </div>
                    <div style="padding: 24px;">
                        @if(isset($jobData['change_percentage']))
                            <div style="background-color: #dbeafe; border: 1px solid #3b82f6; padding: 16px; margin-bottom: 16px;">
                                <h6 style="margin: 0 0 8px 0; color: #1e40af; font-size: 14px; font-weight: 600;">Persentase Perubahan</h6>
                                <p style="margin: 0; color: #1e40af; font-size: 13px;">{{ $jobData['change_percentage'] }}% perubahan dari produk sebelumnya</p>
                            </div>
                        @endif

                        @if(isset($jobData['change_details']) && is_array($jobData['change_details']))
                            <div style="background-color: #dbeafe; border: 1px solid #3b82f6; padding: 16px;">
                                <h6 style="margin: 0 0 8px 0; color: #1e40af; font-size: 14px; font-weight: 600;">Detail Perubahan</h6>
                                <p style="margin: 0; color: #1e40af; font-size: 13px;">
                                    @foreach($jobData['change_details'] as $detail)
                                        {{ ucfirst(str_replace('_', ' ', $detail)) }}@if(!$loop->last), @endif
                                    @endforeach
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Action Button -->
            <div style="text-align: center; margin-bottom: 24px;">
                <a href="{{ $additionalData['action_url'] }}" style="background-color: #2563eb; color: #ffffff; padding: 12px 32px; text-decoration: none; border-radius: 6px; font-weight: 600; display: inline-block; font-size: 16px;">
                    {{ $additionalData['action_text'] }}
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div style="background-color: #f9fafb; padding: 24px; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 14px;">Email ini dikirim otomatis oleh sistem SIPO Development</p>
            <div>
                <a href="{{ route('development.marketing-jobs.list') }}" style="color: #2563eb; text-decoration: none; font-weight: 500; font-size: 14px; margin-right: 16px;">Dashboard Development</a>
                <a href="{{ route('development.development-input.form') }}" style="color: #6b7280; text-decoration: none; font-weight: 500; font-size: 14px;">Input Job Baru</a>
            </div>
        </div>
    </div>
</body>
</html>
