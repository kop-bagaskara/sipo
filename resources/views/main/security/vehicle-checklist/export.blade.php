<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Checklist Kendaraan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0;
            font-size: 12px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 5px;
            vertical-align: top;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
            font-size: 10px;
        }

        .main-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .main-table td.text-left {
            text-align: left;
        }

        .main-table td.text-right {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
        }

        @media print {
            body {
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN CHECKLIST KENDARAAN</h1>
        <p>PT. KRISANTHIUM OFFSET</p>
        <p>Periode: {{ request('tanggal_dari') ? \Carbon\Carbon::parse(request('tanggal_dari'))->format('d/m/Y') : 'Semua' }}
           - {{ request('tanggal_sampai') ? \Carbon\Carbon::parse(request('tanggal_sampai'))->format('d/m/Y') : 'Semua' }}</p>
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Total Data:</strong> {{ $checklists->count() }}</td>
            <td><strong>Filter Shift:</strong> {{ request('shift') ? ucfirst(request('shift')) : 'Semua' }}</td>
            <td><strong>Filter Driver:</strong> {{ request('driver') ?: 'Semua' }}</td>
            <td><strong>Filter Status:</strong> {{ request('status') ? ucfirst(request('status')) : 'Semua' }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="8%">Tanggal</th>
                <th width="12%">Nama Driver</th>
                <th width="10%">Model</th>
                <th width="6%">Out</th>
                <th width="6%">In</th>
                <th width="7%">BBM Awal</th>
                <th width="7%">BBM Akhir</th>
                <th width="7%">KM Awal</th>
                <th width="7%">KM Akhir</th>
                <th width="15%">Tujuan</th>
                <th width="12%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($checklists as $index => $checklist)
                <tr>
                    <td>{{ $checklist->no_urut }}</td>
                    <td>{{ \Carbon\Carbon::parse($checklist->tanggal)->format('d/m/Y') }}</td>
                    <td class="text-left">{{ $checklist->nama_driver }}</td>
                    <td class="text-left">{{ $checklist->model_kendaraan }}</td>
                    <td>{{ $checklist->jam_out ? \Carbon\Carbon::parse($checklist->jam_out)->format('H:i') : '-' }}</td>
                    <td>{{ $checklist->jam_in ? \Carbon\Carbon::parse($checklist->jam_in)->format('H:i') : '-' }}</td>
                    <td class="text-right">{{ $checklist->bbm_awal ? number_format($checklist->bbm_awal, 2) : '-' }}</td>
                    <td class="text-right">{{ $checklist->bbm_akhir ? number_format($checklist->bbm_akhir, 2) : '-' }}</td>
                    <td class="text-right">{{ $checklist->km_awal ? number_format($checklist->km_awal) : '-' }}</td>
                    <td class="text-right">{{ $checklist->km_akhir ? number_format($checklist->km_akhir) : '-' }}</td>
                    <td class="text-left">{{ strlen($checklist->tujuan) > 50 ? substr($checklist->tujuan, 0, 50) . '...' : $checklist->tujuan }}</td>
                    <td class="text-left">{{ $checklist->keterangan ? (strlen($checklist->keterangan) > 40 ? substr($checklist->keterangan, 0, 40) . '...' : $checklist->keterangan) : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" style="text-align: center; padding: 20px;">
                        Tidak ada data checklist kendaraan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($checklists->count() > 0)
        <div style="margin-top: 20px;">
            <h4>Ringkasan:</h4>
            <table style="width: 50%;">
                <tr>
                    <td><strong>Total Kendaraan Keluar:</strong></td>
                    <td>{{ $checklists->where('status', 'keluar')->count() + $checklists->where('status', 'selesai')->count() }}</td>
                </tr>
                <tr>
                    <td><strong>Sudah Kembali:</strong></td>
                    <td>{{ $checklists->where('status', 'selesai')->count() }}</td>
                </tr>
                <tr>
                    <td><strong>Belum Kembali:</strong></td>
                    <td>{{ $checklists->where('status', 'keluar')->count() }}</td>
                </tr>
                <tr>
                    <td><strong>Total KM Tempuh:</strong></td>
                    <td>{{ number_format($checklists->sum('selisih_km')) }} km</td>
                </tr>
                <tr>
                    <td><strong>Total Konsumsi BBM:</strong></td>
                    <td>{{ number_format($checklists->sum('selisih_bbm'), 2) }} liter</td>
                </tr>
            </table>
        </div>
    @endif

    <div class="footer">
        <div class="signature">
            <p>Dibuat Oleh:</p>
            <div class="signature-line">
                <strong>Petugas Security</strong>
            </div>
        </div>

        <div class="signature">
            <p>Mengetahui:</p>
            <div class="signature-line">
                <strong>Supervisor</strong>
            </div>
        </div>

        <div class="signature">
            <p>Menyetujui:</p>
            <div class="signature-line">
                <strong>Manager</strong>
            </div>
        </div>
    </div>

    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px;">Print Laporan</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; margin-left: 10px;">Tutup</button>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            // Uncomment line below if you want auto print
            // window.print();
        }
    </script>
</body>
</html>
