<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keluar/Masuk Barang</title>
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
            padding: 4px;
            text-align: center;
            font-size: 9px;
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
        
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .badge-masuk {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .badge-keluar {
            background-color: #cce5ff;
            color: #004085;
            border: 1px solid #b8daff;
        }
        
        .badge-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .badge-approved {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .badge-completed {
            background-color: #cce5ff;
            color: #004085;
            border: 1px solid #b8daff;
        }
        
        .badge-rejected {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
        <h1>LAPORAN KELUAR/MASUK BARANG</h1>
        <p>PT. KRISANTHIUM OFFSET</p>
        <p>Periode: {{ request('tanggal_dari') ? \Carbon\Carbon::parse(request('tanggal_dari'))->format('d/m/Y') : 'Semua' }} 
           - {{ request('tanggal_sampai') ? \Carbon\Carbon::parse(request('tanggal_sampai'))->format('d/m/Y') : 'Semua' }}</p>
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Total Data:</strong> {{ $movements->count() }}</td>
            <td><strong>Filter Jenis:</strong> {{ request('jenis_movement') ? ucfirst(request('jenis_movement')) : 'Semua' }}</td>
            <td><strong>Filter Shift:</strong> {{ request('shift') ? ucfirst(request('shift')) : 'Semua' }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="7%">Tanggal</th>
                <th width="12%">Nama</th>
                <th width="8%">Perusahaan</th>
                <th width="6%">Jenis</th>
                <th width="15%">Barang</th>
                <th width="6%">Jumlah</th>
                <th width="6%">Jam</th>
                <th width="10%">Kendaraan</th>
                <th width="8%">Dokumen</th>
                <th width="19%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $index => $movement)
                <tr>
                    <td>{{ $movement->no_urut }}</td>
                    <td>{{ \Carbon\Carbon::parse($movement->tanggal)->format('d/m/Y') }}</td>
                    <td class="text-left">{{ $movement->nama_pengunjung }}</td>
                    <td class="text-left">{{ $movement->perusahaan_asal ?: '-' }}</td>
                    <td>
                        <span class="badge badge-{{ $movement->jenis_movement }}">
                            {{ ucfirst($movement->jenis_movement) }}
                        </span>
                    </td>
                    <td class="text-left">
                        <strong>{{ strlen($movement->jenis_barang) > 25 ? substr($movement->jenis_barang, 0, 25) . '...' : $movement->jenis_barang }}</strong>
                        @if($movement->deskripsi_barang)
                            <br><small>{{ strlen($movement->deskripsi_barang) > 30 ? substr($movement->deskripsi_barang, 0, 30) . '...' : $movement->deskripsi_barang }}</small>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($movement->jumlah)
                            {{ number_format($movement->jumlah) }}
                            @if($movement->satuan)
                                <br><small>{{ $movement->satuan }}</small>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($movement->jenis_movement == 'masuk' && $movement->jam_masuk)
                            {{ \Carbon\Carbon::parse($movement->jam_masuk)->format('H:i') }}
                        @elseif($movement->jenis_movement == 'keluar' && $movement->jam_keluar)
                            {{ \Carbon\Carbon::parse($movement->jam_keluar)->format('H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-left">
                        @if($movement->jenis_kendaraan)
                            {{ $movement->jenis_kendaraan }}
                            @if($movement->no_polisi)
                                <br><small>{{ $movement->no_polisi }}</small>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-left">
                        @if($movement->no_surat_jalan)
                            SJ: {{ $movement->no_surat_jalan }}
                        @endif
                        @if($movement->no_invoice)
                            <br>INV: {{ $movement->no_invoice }}
                        @endif
                        @if(!$movement->no_surat_jalan && !$movement->no_invoice)
                            -
                        @endif
                    </td>
                    <td class="text-left">
                        @if($movement->keterangan)
                            {{ strlen($movement->keterangan) > 50 ? substr($movement->keterangan, 0, 50) . '...' : $movement->keterangan }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align: center; padding: 20px;">
                        Tidak ada data keluar/masuk barang
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($movements->count() > 0)
        <div style="margin-top: 20px;">
            <h4>Ringkasan:</h4>
            <table style="width: 60%;">
                <tr>
                    <td><strong>Total Barang Masuk:</strong></td>
                    <td>{{ $movements->where('jenis_movement', 'masuk')->count() }}</td>
                </tr>
                <tr>
                    <td><strong>Total Barang Keluar:</strong></td>
                    <td>{{ $movements->where('jenis_movement', 'keluar')->count() }}</td>
                </tr>
                @php
                    $totalBerat = $movements->sum('berat');
                    $totalJumlah = $movements->sum('jumlah');
                @endphp
                @if($totalBerat > 0)
                    <tr>
                        <td><strong>Total Berat:</strong></td>
                        <td>{{ number_format($totalBerat, 2) }} kg</td>
                    </tr>
                @endif
                @if($totalJumlah > 0)
                    <tr>
                        <td><strong>Total Jumlah:</strong></td>
                        <td>{{ number_format($totalJumlah) }} item</td>
                    </tr>
                @endif
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
