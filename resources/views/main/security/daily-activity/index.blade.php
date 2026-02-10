@extends('main.layouts.main')

@section('title')
    Laporan Aktivitas Harian Security
@endsection

@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection

@section('page-title')
    Laporan Aktivitas Harian Security
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Laporan Aktivitas Harian Security</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Security</a></li>
                <li class="breadcrumb-item active">Laporan Aktivitas Harian</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">Daftar Laporan Aktivitas Harian</h4>
                            {{-- <p class="card-title-desc">Kelola laporan aktivitas harian security</p> --}}
                        </div>
                        <div class="col d-flex justify-content-end">
                            <a href="{{ route('security.daily-activity.export', request()->all()) }}" class="btn btn-success me-2" target="_blank">
                                <i class="mdi mdi-file-pdf"></i> Export PDF
                            </a>
                            <a href="{{ route('security.daily-activity.create') }}" class="btn btn-info" style="color: white;">
                                <i class="mdi mdi-plus"></i> Tambah Laporan
                            </a>
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('security.daily-activity.index') }}" class="form-inline">
                                <div class="form-group mr-3">
                                    <label class="mr-2">Tanggal:</label>
                                    <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
                                </div>
                                <div class="form-group mr-3">
                                    <label class="mr-2">Shift:</label>
                                    <select name="shift" class="form-control">
                                        <option value="">Semua Shift</option>
                                        @foreach($shifts as $key => $value)
                                            <option value="{{ $key }}" {{ request('shift') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mr-3">
                                    <label class="mr-2">Lokasi:</label>
                                    <select name="lokasi" class="form-control">
                                        <option value="">Semua Lokasi</option>
                                        @foreach($lokasis as $lokasi)
                                            <option value="{{ $lokasi }}" {{ request('lokasi') == $lokasi ? 'selected' : '' }}>
                                                {{ $lokasi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mr-3">
                                    <label class="mr-2">Personil:</label>
                                    <select name="personil" class="form-control">
                                        <option value="">Semua Personil</option>
                                        @foreach($personils as $personil)
                                            <option value="{{ $personil }}" {{ request('personil') == $personil ? 'selected' : '' }}>
                                                {{ $personil }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-info">
                                    <i class="mdi mdi-magnify"></i> Filter
                                </button>
                                <a href="{{ route('security.daily-activity.index') }}" class="btn btn-secondary ml-2">
                                    <i class="mdi mdi-refresh"></i> Reset
                                </a>
                            </form>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Lokasi</th>
                                    <th>Tanggal</th>
                                    <th>Hari</th>
                                    <th>Shift</th>

                                    {{-- <th>Personil Jaga</th> --}}
                                    {{-- <th>Jumlah Aktivitas</th> --}}
                                    <th>Petugas</th>
                                    <th>Isi Laporan</th>
                                    <th>Created At</th>

                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $index => $log)
                                    <tr>
                                        <td>{{ $logs->firstItem() + $index }}</td>
                                        <td>{{ $log->lokasi }}</td>
                                        <td>{{ $log->tanggal->format('d/m/Y') }}</td>
                                        <td>{{ $log->hari_formatted }}</td>
                                        <td>
                                            <span class="badge badge-{{ $log->shift == 'I' ? 'success' : ($log->shift == 'II' ? 'warning' : 'info') }}">
                                                {{ $log->shift_formatted }}
                                            </span>
                                        </td>

                                        {{-- <td>{{ $log->jam_mulai->format('H:i') }} - {{ $log->jam_selesai->format('H:i') }}</td>
                                        <td>{{ $log->personil_jaga }}</td> --}}
                                        {{-- <td>
                                            <span class="badge badge-primary">{{ $log->activityEntries->count() }} aktivitas</span>
                                        </td> --}}
                                        <td>{{ $log->petugas_security }}</td>
                                        <td style="max-width: 300px; word-wrap: break-word;">
                                            @if($log->activityEntries->count() > 0)
                                                @php
                                                    $keterangan = $log->activityEntries->pluck('keterangan')->implode(', ');
                                                    $keteranganLimited = strlen($keterangan) > 100 ? substr($keterangan, 0, 100) . '...' : $keterangan;
                                                @endphp
                                                <span title="{{ $keterangan }}">
                                                    {{ $keteranganLimited }}
                                                </span>
                                            @else
                                                <span class="text-muted">Tidak ada aktivitas</span>
                                            @endif
                                        </td>
                                        <td>{{ $log->created_at->format('H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('security.daily-activity.show', $log->id) }}"
                                                   class="btn btn-info btn-sm" title="Lihat Detail">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="{{ route('security.daily-activity.edit', $log->id) }}"
                                                   class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                {{-- <a href="{{ route('security.daily-activity.export', $log->id) }}"
                                                   class="btn btn-success btn-sm" title="Export PDF" target="_blank">
                                                    <i class="mdi mdi-file-pdf"></i>
                                                </a> --}}
                                                @php
                                                    $currentUser = auth()->user();
                                                    $isAuthorized = false;

                                                    // Check created_by field first (if exists), then fallback to petugas_security
                                                    if (isset($log->created_by) && $log->created_by) {
                                                        $isAuthorized = $log->created_by === $currentUser->id ||
                                                                       $log->created_by === $currentUser->name ||
                                                                       $log->created_by === $currentUser->username;
                                                    } else {
                                                        // Fallback to petugas_security field
                                                        $isAuthorized = $log->petugas_security === $currentUser->name ||
                                                                       $log->petugas_security === $currentUser->username;
                                                    }
                                                @endphp
                                                @if($isAuthorized)
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $log->id }})" title="Hapus">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-danger btn-sm" disabled title="Hanya pembuat laporan yang dapat menghapus">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <div class="alert alert-info">
                                                <i class="mdi mdi-information"></i>
                                                Tidak ada data laporan aktivitas harian
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($logs->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <p class="text-muted mb-0">
                                    Menampilkan {{ $logs->firstItem() ?? 0 }} sampai {{ $logs->lastItem() ?? 0 }}
                                    dari {{ $logs->total() }} data
                                </p>
                            </div>
                            <div>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination pagination-sm mb-0">
                                        {{-- Previous Page Link --}}
                                        @if($logs->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link">
                                                    <i class="mdi mdi-chevron-left"></i> Sebelumnya
                                                </span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $logs->appends(request()->query())->previousPageUrl() }}" rel="prev">
                                                    <i class="mdi mdi-chevron-left"></i> Sebelumnya
                                                </a>
                                            </li>
                                        @endif

                                        {{-- Pagination Elements --}}
                                        @php
                                            $currentPage = $logs->currentPage();
                                            $lastPage = $logs->lastPage();
                                            $startPage = max(1, $currentPage - 2);
                                            $endPage = min($lastPage, $currentPage + 2);
                                        @endphp

                                        {{-- First Page --}}
                                        @if($startPage > 1)
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $logs->appends(request()->query())->url(1) }}">1</a>
                                            </li>
                                            @if($startPage > 2)
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif
                                        @endif

                                        {{-- Page Numbers --}}
                                        @for($page = $startPage; $page <= $endPage; $page++)
                                            @if($page == $currentPage)
                                                <li class="page-item active">
                                                    <span class="page-link">{{ $page }}</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $logs->appends(request()->query())->url($page) }}">{{ $page }}</a>
                                                </li>
                                            @endif
                                        @endfor

                                        {{-- Last Page --}}
                                        @if($endPage < $lastPage)
                                            @if($endPage < $lastPage - 1)
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            @endif
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $logs->appends(request()->query())->url($lastPage) }}">{{ $lastPage }}</a>
                                            </li>
                                        @endif

                                        {{-- Next Page Link --}}
                                        @if($logs->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $logs->appends(request()->query())->nextPageUrl() }}" rel="next">
                                                    Selanjutnya <i class="mdi mdi-chevron-right"></i>
                                                </a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link">
                                                    Selanjutnya <i class="mdi mdi-chevron-right"></i>
                                                </span>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    @else
                        <div class="mt-3">
                            <p class="text-muted mb-0">
                                Menampilkan {{ $logs->count() }} dari {{ $logs->total() }} data
                            </p>
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
        $(document).ready(function() {
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
        });

        // Define confirmDelete function globally
        window.confirmDelete = function(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Laporan aktivitas harian akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // AJAX Delete
                    fetch(`{{ route('security.daily-activity.index') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        }
                        throw new Error('Network response was not ok');
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message || 'Laporan aktivitas harian berhasil dihapus',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: data.message || 'Terjadi kesalahan saat menghapus data',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#d33'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Terjadi kesalahan saat menghapus data',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        });
                    });
                }
            });
        };

        // Alternative: Event delegation approach (backup)
        $(document).on('click', 'button[onclick*="confirmDelete"]', function(e) {
            e.preventDefault();
            var onclick = $(this).attr('onclick');
            var id = onclick.match(/confirmDelete\((\d+)\)/)[1];
            if (id) {
                window.confirmDelete(id);
            }
        });
    </script>
@endsection
