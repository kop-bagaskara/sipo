<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Checklist Kendaraan Security</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10pt;
        }
        .page-break {
            page-break-after: always;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            border: 2px solid #000;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
            color: #000;
        }
        .header h2, .header h4, .header h5, .header p {
            margin: 0;
            color: #000;
        }
        .header .text-right {
            text-align: right;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .report-table th, .report-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .report-table th {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-muted {
            color: #6c757d;
        }
        .badge {
            display: inline-block;
            padding: .35em .65em;
            font-size: .75em;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
        }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #212529;}
        .badge-info { background-color: #17a2b8; }
        .badge-primary { background-color: #007bff; }
        .summary-section {
            border: 1px solid #000;
            padding: 15px;
            margin-top: 20px;
            background-color: #f8f9fa;
        }
        .summary-section h6 {
            margin-bottom: 10px;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table width="100%">
                <tr>
                    <td width="65%">
                        <h2>PT. KRISANTHIUM O.P.</h2>
                        <p>Jl. Rungkut Industri III / No. 19</p>
                        <h4>LAPORAN CHECKLIST KENDARAAN SECURITY</h4>
                    </td>
                    <td width="35%" class="text-right">
                        <h5>Tanggal Cetak : {{ now()->format('d F Y H:i') }}</h5>
                        <h5>Total Data : {{ $checklists->count() }} record</h5>
                    </td>
                </tr>
            </table>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">Tanggal</th>
                    <th width="8%">No. Urut</th>
                    <th width="12%">Driver</th>
                    <th width="12%">Model Kendaraan</th>
                    <th width="10%">No. Polisi</th>
                    <th width="8%">Shift</th>
                    <th width="8%">Jam Out</th>
                    <th width="8%">Jam In</th>
                    <th width="8%">Status</th>
                    <th width="11%">Petugas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($checklists as $index => $checklist)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $checklist->tanggal->format('d/m/Y') }}</td>
                        <td>{{ $checklist->no_urut }}</td>
                        <td>{{ $checklist->nama_driver }}</td>
                        <td>{{ $checklist->model_kendaraan }}</td>
                        <td>{{ $checklist->no_polisi }}</td>
                        <td class="text-center">{{ ucfirst($checklist->shift) }}</td>
                        <td class="text-center">{{ $checklist->jam_out ? \Carbon\Carbon::parse($checklist->jam_out)->format('H:i') : '-' }}</td>
                        <td class="text-center">{{ $checklist->jam_in ? \Carbon\Carbon::parse($checklist->jam_in)->format('H:i') : '-' }}</td>
                        <td class="text-center">{{ ucfirst($checklist->status) }}</td>
                        <td>{{ $checklist->petugas_security }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted">Tidak ada data checklist kendaraan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="summary-section">
            <h6>Ringkasan Laporan</h6>
            <table width="100%">
                <tr>
                    <td width="50%">
                        <p><strong>Total Checklist:</strong> {{ $checklists->count() }}</p>
                        <p><strong>Status Keluar:</strong> {{ $checklists->where('status', 'keluar')->count() }}</p>
                        <p><strong>Status Selesai:</strong> {{ $checklists->where('status', 'selesai')->count() }}</p>
                    </td>
                    <td width="50%">
                        <p><strong>Shift Pagi:</strong> {{ $checklists->where('shift', 'pagi')->count() }}</p>
                        <p><strong>Shift Siang:</strong> {{ $checklists->where('shift', 'siang')->count() }}</p>
                        <p><strong>Shift Malam:</strong> {{ $checklists->where('shift', 'malam')->count() }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="text-right" style="margin-top: 30px;">
            <p>Surabaya, {{ now()->format('d F Y') }}</p>
            <p><strong>HR Department</strong></p>
        </div>
    </div>
</body>
</html>
