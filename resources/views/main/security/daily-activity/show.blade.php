@extends('main.layouts.main')

@section('title')
    Detail Laporan Aktivitas Harian
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .activity-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .log-header {
            /* background: linear-gradient(135deg, #007bff 0%, #007bff 100%); */
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .signature-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
    </style>
@endsection

@section('page-title')
    Detail Laporan Aktivitas Harian
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Detail Laporan Aktivitas Harian</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Security</a></li>
                <li class="breadcrumb-item"><a href="{{ route('security.daily-activity.index') }}">Laporan Aktivitas Harian</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Header Information -->
            <div class="log-header bg-info" >
                <div class="row" style="color: white;">
                    <div class="col-md-8">
                        <h2 class="mb-2" style="color: white;">PT. KRISANTHIUM O.P.</h2>
                        <p class="mb-1" style="color: white;">Jl. Rungkut Industri III / No. 19</p>
                        <h4 class="mb-0" style="color: white;">LAPORAN AKTIVITAS HARIAN SECURITY</h4>
                    </div>
                    <div class="col-md-4 text-right" style="color: white;">
                        <h5 class="mb-1" style="color: white;">Hari / Tanggal : {{ $log->hari_formatted }}, {{ $log->tanggal->format('d F Y') }}</h5>
                        <h5 class="mb-1" style="color: white;">Shift / Jam : {{ $log->shift_formatted }} / {{ $log->jam_mulai_formatted }} - {{ $log->jam_selesai_formatted }}</h5>
                        <h5 class="mb-0" style="color: white;">Personil Jaga : {{ $log->personil_jaga }}</h5>
                    </div>
                </div>
            </div>

            <!-- Activity Log Table -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Aktivitas</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered activity-table">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">IN</th>
                                    <th width="15%">OUT</th>
                                    <th width="65%">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($log->activityEntries as $entry)
                                    <tr>
                                        <td class="text-center">{{ $entry->urutan }}</td>
                                        <td class="text-center">
                                            @if($entry->time_in_formatted)
                                                <span class="badge badge-info">{{ $entry->time_in_formatted }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($entry->time_out_formatted)
                                                <span class="badge badge-success">{{ $entry->time_out_formatted }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $entry->keterangan }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            <i class="mdi mdi-information"></i> Tidak ada data aktivitas
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Summary Information -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Kondisi Awal</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $log->kondisi_awal ?: 'Tidak ada catatan kondisi awal' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Kondisi Akhir</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $log->kondisi_akhir ?: 'Tidak ada catatan kondisi akhir' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signature Section -->
            <div class="signature-section">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <h6 class="mb-3">Menyerahkan</h6>
                        <div style="height: 60px; border-bottom: 1px solid #333; margin-bottom: 10px;"></div>
                        <p class="mb-0"><strong>{{ $log->menyerahkan_by ?: '________________' }}</strong></p>
                    </div>
                    <div class="col-md-4 text-center">
                        <h6 class="mb-3">Diterima</h6>
                        <div style="height: 60px; border-bottom: 1px solid #333; margin-bottom: 10px;"></div>
                        <p class="mb-0"><strong>{{ $log->diterima_by ?: '________________' }}</strong></p>
                    </div>
                    <div class="col-md-4 text-center">
                        <h6 class="mb-3">Diketahui</h6>
                        <div style="height: 60px; border-bottom: 1px solid #333; margin-bottom: 10px;"></div>
                        <p class="mb-0"><strong>{{ $log->diketahui_by ?: '________________' }}</strong></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12 text-right">
                        <p class="mb-0">Surabaya, {{ $log->tanggal->format('d F Y') }}</p>
                        <p class="mb-0"><strong>PAPERLINE</strong></p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <a href="{{ route('security.daily-activity.edit', $log->id) }}" class="btn btn-warning btn-lg me-2">
                                <i class="mdi mdi-pencil"></i> Edit Laporan
                            </a>
                            <a href="{{ route('security.daily-activity.index') }}" class="btn btn-secondary btn-lg me-2">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                            <a href="{{ route('security.daily-activity.export.single', $log->id) }}" class="btn btn-success btn-lg me-2" target="_blank">
                                <i class="mdi mdi-file-pdf"></i> Export PDF
                            </a>
                            <button type="button" class="btn btn-info btn-lg" onclick="window.print()">
                                <i class="mdi mdi-printer"></i> Cetak
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
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
        function printPage() {
            window.print();
        }
    </script>

    <style media="print">
        .btn, .breadcrumb, .page-titles {
            display: none !important;
        }
        .log-header {
            background: #f8f9fa !important;
            color: #000 !important;
            border: 2px solid #000;
        }
        .card {
            border: 1px solid #000 !important;
        }
        .activity-table th {
            background-color: #e9ecef !important;
        }
        .signature-section {
            background-color: #f8f9fa !important;
            border: 1px solid #000 !important;
        }
    </style>
@endsection
