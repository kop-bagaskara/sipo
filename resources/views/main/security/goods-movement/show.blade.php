@extends('main.layouts.main')

@section('title')
    Detail Keluar/Masuk Barang
@endsection

@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

    <style>
        .cust-col {
            white-space: nowrap;
        }
    </style>
@endsection

@section('page-title')
    Detail Keluar/Masuk Barang
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Detail Keluar/Masuk Barang</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Security</a></li>
                <li class="breadcrumb-item active">Detail Keluar/Masuk Barang</li>
            </ol>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <div>
                    <a href="{{ route('security.goods-movement.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> Kembali
                    </a>
                    <a href="{{ route('security.goods-movement.edit', $movement->id) }}" class="btn btn-warning">
                        <i class="mdi mdi-pencil"></i> Edit
                    </a>
                </div>
                <div>
                    <button onclick="window.print()" class="btn btn-info">
                        <i class="mdi mdi-printer"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Header Information -->
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4 class="card-title mb-0 text-white">
                        <i class="mdi mdi-package-variant"></i> Keluar/Masuk Barang #{{ $movement->no_urut }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="avatar-lg mx-auto mb-3">
                                    <div class="avatar-title bg-soft-{{ $movement->movement_color }} text-{{ $movement->movement_color }} rounded-circle font-size-24">
                                        <i class="mdi mdi-{{ $movement->jenis_movement == 'masuk' ? 'arrow-down-bold' : 'arrow-up-bold' }}"></i>
                                    </div>
                                </div>
                                <h5 class="font-size-16 mb-1">{{ $movement->nama_pengunjung }}</h5>
                                @if($movement->perusahaan_asal)
                                    <p class="text-muted mb-2">{{ $movement->perusahaan_asal }}</p>
                                @endif
                                <span class="badge bg-{{ $movement->movement_color }} font-size-12">{{ ucfirst($movement->jenis_movement) }}</span>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Tanggal</label>
                                        <p class="mb-0 fw-bold">{{ $movement->tanggal_formatted }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Shift</label>
                                        <p class="mb-0">
                                            <span class="badge bg-{{ $movement->shift == 'pagi' ? 'warning' : ($movement->shift == 'siang' ? 'info' : 'dark') }}">
                                                {{ ucfirst($movement->shift) }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Petugas Security</label>
                                        <p class="mb-0">{{ $movement->petugas_security }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Dibuat</label>
                                        <p class="mb-0">{{ $movement->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Lokasi</label>
                                        <p class="mb-0">
                                            @switch($movement->lokasi)
                                                @case('1') Lokasi 19 (KRISANTHIUM) @break
                                                @case('2') Lokasi 23 (KRISANTHIUM) @break
                                                @case('3') Lokasi 15 (BERBEK) @break
                                                @default -
                                            @endswitch
                                        </p>
                                    </div>
                                    @if($movement->updated_at != $movement->created_at)
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Diupdate</label>
                                            <p class="mb-0">{{ $movement->updated_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Data Barang -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="mdi mdi-package-variant"></i> Data Barang</h5>
                </div>
                <div class="card-body">
                    @if($movement->barang_items && count($movement->barang_items) > 0)
                        <!-- Multiple Items Display -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="25%">Jenis Barang</th>
                                        <th width="15%">Jumlah</th>
                                        <th width="10%">Satuan</th>
                                        <th width="10%">Berat (kg)</th>
                                        <th width="35%">Deskripsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($movement->barang_items as $index => $item)
                                        <tr>
                                            <td class="text-center">
                                                <span class="badge bg-primary">{{ $index + 1 }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $item['jenis_barang'] ?? '-' }}</strong>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold">{{ number_format($item['jumlah'] ?? 0) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $item['satuan'] ?? '-' }}</span>
                                            </td>
                                            <td class="text-end">
                                                @if(isset($item['berat']) && $item['berat'])
                                                    {{ number_format($item['berat'], 2) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($item['deskripsi_barang']) && $item['deskripsi_barang'])
                                                    {{ $item['deskripsi_barang'] }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="2" class="text-end fw-bold">Total:</td>
                                        <td class="text-end fw-bold">{{ number_format($movement->total_jumlah_barang) }}</td>
                                        <td class="text-center">-</td>
                                        <td class="text-end fw-bold">
                                            @if($movement->total_berat_barang > 0)
                                                {{ number_format($movement->total_berat_barang, 2) }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <!-- Fallback untuk data lama (backward compatibility) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Jenis Barang</label>
                                    <h6 class="mb-0">{{ $movement->jenis_barang ?? '-' }}</h6>
                                </div>
                                @if($movement->deskripsi_barang)
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Deskripsi</label>
                                        <p class="mb-0">{{ $movement->deskripsi_barang }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if($movement->jumlah)
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Jumlah</label>
                                        <h6 class="mb-0">{{ number_format($movement->jumlah) }} {{ $movement->satuan }}</h6>
                                    </div>
                                @endif
                                @if($movement->berat)
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Berat</label>
                                        <h6 class="mb-0">{{ number_format($movement->berat, 2) }} kg</h6>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Waktu & Lokasi -->

    </div>

    <div class="row">
        <!-- Data Pengunjung -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="mdi mdi-account"></i> Data Pengunjung</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Nama</label>
                        <h6 class="mb-0">{{ $movement->nama_pengunjung }}</h6>
                    </div>
                    @if($movement->perusahaan_asal)
                        <div class="mb-3">
                            <label class="form-label text-muted">Perusahaan</label>
                            <p class="mb-0">{{ $movement->perusahaan_asal }}</p>
                        </div>
                    @endif
                    @if($movement->no_telepon)
                        <div class="mb-3">
                            <label class="form-label text-muted">No. Telepon</label>
                            <p class="mb-0">{{ $movement->no_telepon }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Data Kendaraan -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="mdi mdi-truck"></i> Data Kendaraan</h5>
                </div>
                <div class="card-body">
                    @if($movement->jenis_kendaraan || $movement->no_polisi || $movement->nama_driver)
                        @if($movement->jenis_kendaraan)
                            <div class="mb-3">
                                <label class="form-label text-muted">Jenis Kendaraan</label>
                                <p class="mb-0">{{ $movement->jenis_kendaraan }}</p>
                            </div>
                        @endif
                        @if($movement->no_polisi)
                            <div class="mb-3">
                                <label class="form-label text-muted">No. Polisi</label>
                                <p class="mb-0">{{ $movement->no_polisi }}</p>
                            </div>
                        @endif
                        @if($movement->nama_driver)
                            <div class="mb-3">
                                <label class="form-label text-muted">Nama Driver</label>
                                <p class="mb-0">{{ $movement->nama_driver }}</p>
                            </div>
                        @endif
                    @else
                        <p class="text-muted">Tidak ada data kendaraan</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="mdi mdi-clock-outline"></i> Waktu & Lokasi</h5>
                </div>
                <div class="card-body">
                    @if($movement->jenis_movement == 'masuk')
                        @if($movement->jam_masuk)
                            <div class="mb-3">
                                <label class="form-label text-muted">Jam Masuk</label>
                                <h6 class="mb-0 text-success">{{ \Carbon\Carbon::parse($movement->jam_masuk)->format('H:i') }}</h6>
                            </div>
                        @endif
                        @if($movement->asal)
                            <div class="mb-3">
                                <label class="form-label text-muted">Asal Barang</label>
                                <p class="mb-0">{{ $movement->asal }}</p>
                            </div>
                        @endif
                    @else
                        @if($movement->jam_keluar)
                            <div class="mb-3">
                                <label class="form-label text-muted">Jam Keluar</label>
                                <h6 class="mb-0 text-primary">{{ \Carbon\Carbon::parse($movement->jam_keluar)->format('H:i') }}</h6>
                            </div>
                        @endif
                        @if($movement->tujuan)
                            <div class="mb-3">
                                <label class="form-label text-muted">Tujuan Barang</label>
                                <p class="mb-0">{{ $movement->tujuan }}</p>
                            </div>
                        @endif
                    @endif
                    @if($movement->alamat)
                        <div class="mb-3">
                            <label class="form-label text-muted">Alamat</label>
                            <p class="mb-0">{{ $movement->alamat }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Dokumen -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="mdi mdi-file-document"></i> Dokumen</h5>
                </div>
                <div class="card-body">
                    @if($movement->no_surat_jalan || $movement->no_invoice || $movement->dokumen_pendukung)
                        @if($movement->no_surat_jalan)
                            <div class="mb-3">
                                <label class="form-label text-muted">No. Surat Jalan</label>
                                <p class="mb-0">{{ $movement->no_surat_jalan }}</p>
                            </div>
                        @endif
                        @if($movement->no_invoice)
                            <div class="mb-3">
                                <label class="form-label text-muted">No. Invoice</label>
                                <p class="mb-0">{{ $movement->no_invoice }}</p>
                            </div>
                        @endif
                        @if($movement->dokumen_pendukung)
                            <div class="mb-3">
                                <label class="form-label text-muted">Dokumen Pendukung</label>
                                <p class="mb-0">{{ $movement->dokumen_pendukung }}</p>
                            </div>
                        @endif
                    @else
                        <p class="text-muted">Tidak ada dokumen pendukung</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Keterangan & Approval -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="mdi mdi-note-text"></i> Keterangan & Approval</h5>
                </div>
                <div class="card-body">
                    @if($movement->keterangan)
                        <div class="mb-3">
                            <label class="form-label text-muted">Keterangan</label>
                            <p class="mb-0">{{ $movement->keterangan }}</p>
                        </div>
                    @endif
                    @if($movement->catatan_security)
                        <div class="mb-3">
                            <label class="form-label text-muted">Catatan Security</label>
                            <p class="mb-0">{{ $movement->catatan_security }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// SweetAlert untuk success message
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        toast: true,
        position: 'top-end'
    });
@endif

// SweetAlert untuk error message
@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '{{ session('error') }}',
        confirmButtonText: 'OK',
        confirmButtonColor: '#d33'
    });
@endif

// Print styles
window.addEventListener('beforeprint', function() {
    document.body.classList.add('print-mode');
});

window.addEventListener('afterprint', function() {
    document.body.classList.remove('print-mode');
});
</script>

<style>
@media print {
    .btn, .breadcrumb, .page-title-box, .modal {
        display: none !important;
    }

    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
        margin-bottom: 20px !important;
    }

    .card-header {
        background-color: #f8f9fa !important;
        color: #000 !important;
        border-bottom: 1px solid #000 !important;
    }

    .text-primary, .text-success, .text-info, .text-warning, .text-danger {
        color: #000 !important;
    }

    .badge {
        border: 1px solid #000 !important;
        color: #000 !important;
        background-color: #fff !important;
    }
}
</style>
@endsection
