<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Laporan Aktivitas Harian Security</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .company-address {
            font-size: 12px;
            margin-bottom: 10px;
        }

        .report-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
        }

        .report-info {
            margin: 20px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .report-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-info td {
            padding: 5px;
            vertical-align: top;
        }

        .report-info .label {
            font-weight: bold;
            width: 30%;
        }

        .activity-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .activity-table th,
        .activity-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .activity-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }

        .activity-table .no-col {
            width: 5%;
            text-align: center;
        }

        .activity-table .time-col {
            width: 15%;
            text-align: center;
        }

        .activity-table .desc-col {
            width: 65%;
        }

        .summary-section {
            margin: 30px 0;
        }

        .summary-card {
            border: 1px solid #333;
            margin-bottom: 15px;
            padding: 10px;
        }

        .summary-card h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: bold;
        }

        .signature-section {
            margin-top: 40px;
            border-top: 1px solid #333;
            padding-top: 20px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }

        .signature-line {
            height: 60px;
            border-bottom: 1px solid #333;
            margin-bottom: 10px;
        }

        .page-break {
            page-break-before: always;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
        }

        @media print {
            body {
                margin: 0;
                padding: 15px;
            }

            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    @foreach($logs as $index => $log)
        @if($index > 0)
            <div class="page-break"></div>
        @endif

        <!-- Header Information -->
        <div class="header">
            <div class="company-name">PT. KRISANTHIUM O.P.</div>
            <div class="company-address">Jl. Rungkut Industri III / No. 19</div>
            <div class="report-title">LAPORAN AKTIVITAS HARIAN SECURITY</div>
        </div>

        <!-- Report Information -->
        <div class="report-info">
            <table>
                <tr>
                    <td class="label">Hari / Tanggal:</td>
                    <td>{{ $log->hari_formatted }}, {{ $log->tanggal->format('d F Y') }}</td>
                    <td class="label">Shift / Jam:</td>
                    <td>{{ $log->shift_formatted }} / {{ $log->jam_mulai_formatted }} - {{ $log->jam_selesai_formatted }}</td>
                </tr>
                <tr>
                    <td class="label">Personil Jaga:</td>
                    <td>{{ $log->personil_jaga }}</td>
                    <td class="label">Petugas:</td>
                    <td>{{ $log->petugas_security }}</td>
                </tr>
            </table>
        </div>

        <!-- Activity Log Table -->
        <table class="activity-table">
            <thead>
                <tr>
                    <th class="no-col">No</th>
                    <th class="time-col">IN</th>
                    <th class="time-col">OUT</th>
                    <th class="desc-col">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($log->activityEntries as $entry)
                    <tr>
                        <td class="text-center">{{ $entry->urutan }}</td>
                        <td class="text-center">
                            @if($entry->time_in_formatted)
                                {{ $entry->time_in_formatted }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @if($entry->time_out_formatted)
                                {{ $entry->time_out_formatted }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $entry->keterangan }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            Tidak ada data aktivitas
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Summary Information -->
        <div class="summary-section">
            <div class="row">
                <div class="col-md-6">
                    <div class="summary-card">
                        <h4>Kondisi Awal</h4>
                        <p>{{ $log->kondisi_awal ?: 'Tidak ada catatan kondisi awal' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="summary-card">
                        <h4>Kondisi Akhir</h4>
                        <p>{{ $log->kondisi_akhir ?: 'Tidak ada catatan kondisi akhir' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td>
                        <h4>Menyerahkan</h4>
                        <div class="signature-line"></div>
                        <p><strong>{{ $log->menyerahkan_by ?: '________________' }}</strong></p>
                    </td>
                    <td>
                        <h4>Diterima</h4>
                        <div class="signature-line"></div>
                        <p><strong>{{ $log->diterima_by ?: '________________' }}</strong></p>
                    </td>
                    <td>
                        <h4>Diketahui</h4>
                        <div class="signature-line"></div>
                        <p><strong>{{ $log->diketahui_by ?: '________________' }}</strong></p>
                    </td>
                </tr>
            </table>

            <div class="footer">
                <p>Surabaya, {{ $log->tanggal->format('d F Y') }}</p>
                <p><strong>PAPERLINE</strong></p>
            </div>
        </div>
    @endforeach
</body>
</html>
