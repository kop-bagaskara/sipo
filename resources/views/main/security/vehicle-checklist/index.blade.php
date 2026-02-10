@extends('main.layouts.main')

@section('title')
    Laporan Checklist Kendaraan
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

        /* Simple Table Styling */
        .table-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .table-card .table {
            margin-bottom: 0;
        }

        .table-card .table thead th {
            /* background: primary; */
            color: white;
            border: none;
            font-weight: 600;
            padding: 15px 10px;
            text-align: center;
            font-size: 0.9rem;
        }

        .table-card .table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid #e9ecef;
        }

        .table-card .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .table-card .table tbody td {
            padding: 12px 10px;
            vertical-align: middle;
            border: none;
            font-size: 0.9rem;
        }

        .time-badge {
            background: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.8rem;
            display: inline-block;
        }

        .btn-group .btn {
            border-radius: 6px;
            margin: 0 2px;
            padding: 6px 10px;
            transition: all 0.2s ease;
        }

        .btn-outline-info {
            border-color: #17a2b8;
            color: #17a2b8;
        }

        .btn-outline-info:hover {
            background: #17a2b8;
            border-color: #17a2b8;
            color: white;
        }

        .btn-outline-warning {
            border-color: #ffc107;
            color: #ffc107;
        }

        .btn-outline-warning:hover {
            background: #ffc107;
            border-color: #ffc107;
            color: white;
        }

        .btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
        }

        .btn-outline-danger:hover {
            background: #dc3545;
            border-color: #dc3545;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .pagination-wrapper {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 10px 10px;
        }

        .table-responsive {
            border-radius: 10px;
        }

        .driver-name {
            font-weight: 600;
            color: #495057;
        }

        .model-text {
            font-weight: 500;
            color: #6c757d;
        }

        .tujuan-text {
            color: #495057;
            line-height: 1.4;
        }

        .keterangan-text {
            color: #6c757d;
            font-style: italic;
        }

        .no-urut {
            font-weight: 700;
            color: #007bff;
            font-size: 1.1rem;
        }

        .date-info {
            line-height: 1.3;
        }

        .date-day {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 500;
        }

        .date-date {
            font-weight: 600;
            color: #495057;
        }

        .bbm-km-value {
            font-weight: 600;
            color: #495057;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .table-card .table thead th,
            .table-card .table tbody td {
                padding: 8px 5px;
                font-size: 0.8rem;
            }

            .btn-group .btn {
                padding: 4px 8px;
                font-size: 0.8rem;
            }
        }
    </style>
@endsection

@section('page-title')
    Laporan Checklist Kendaraan
@endsection

@section('body')

    <body data-sidebar="colored">
    @endsection

    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Laporan Checklist Kendaraan</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Security</a></li>
                    <li class="breadcrumb-item active">Checklist Kendaraan</li>
                </ol>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('security.vehicle-checklist.index') }}" class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Tanggal Dari</label>
                                <input type="date" class="form-control" name="tanggal_dari"
                                    value="{{ request('tanggal_dari', date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tanggal Sampai</label>
                                <input type="date" class="form-control" name="tanggal_sampai"
                                    value="{{ request('tanggal_sampai', date('Y-m-d')) }}">
                            </div>
                            {{-- <div class="col-md-2">
                                <label class="form-label">Shift</label>
                                <select class="form-select form-control" name="shift">
                                    <option value="">Semua Shift</option>
                                    <option value="pagi" {{ request('shift') == 'pagi' ? 'selected' : '' }}>Pagi</option>
                                    <option value="siang" {{ request('shift') == 'siang' ? 'selected' : '' }}>Siang
                                    </option>
                                    <option value="malam" {{ request('shift') == 'malam' ? 'selected' : '' }}>Malam
                                    </option>
                                </select>
                            </div> --}}
                            {{-- <div class="col-md-2">
                                <label class="form-label">Driver</label>
                                <select class="form-select form-control" name="driver">
                                    <option value="">Semua Driver</option>
                                    @foreach ($drivers as $driver)
                                        <option value="{{ $driver }}"
                                            {{ request('driver') == $driver ? 'selected' : '' }}>
                                            {{ $driver }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> --}}
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select class="form-select form-control" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="keluar" {{ request('status') == 'awal_keluar' ? 'selected' : '' }}>Keluar
                                    </option>
                                    <option value="awal_masuk" {{ request('status') == 'awal_masuk' ? 'selected' : '' }}>Masuk
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-info">
                                        <i class="mdi mdi-filter"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="{{ route('security.vehicle-checklist.create') }}" class="btn btn-success">
                            <i class="mdi mdi-plus"></i> Tambah Checklist
                        </a>
                        <a href="{{ route('security.vehicle-checklist.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
                            class="btn btn-info" target="_blank">
                            <i class="mdi mdi-file-download"></i> Export
                        </a>
                    </div>
                    <div>
                        <button class="btn btn-info fs-6" type="button" style="color: white;">Total: {{ $checklists->total() }}
                            data</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="row">
            <div class="col-12">
                <div class="card table-card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="bg-info">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="12%">Hari/Tanggal</th>
                                        <th width="12%">Nama Driver</th>
                                        <th width="10%">Model</th>
                                        <th width="10%">Checklist</th>
                                        <th width="8%">In/Out</th>
                                        <th width="10%">BBM</th>
                                        <th width="10%">KM</th>
                                        <th width="15%">Tujuan</th>
                                        <th width="10%">Ket</th>
                                        <th width="8%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($checklists as $index => $checklist)
                                        <tr>
                                            <td class="text-center">
                                                <span class="no-urut">{{ $checklist->no_urut }}</span>
                                            </td>
                                            <td>
                                                <div class="date-info">
                                                    <div class="date-day">{{ \Carbon\Carbon::parse($checklist->tanggal)->locale('id')->isoFormat('dddd') }}</div>
                                                    <div class="date-date">{{ \Carbon\Carbon::parse($checklist->tanggal)->format('d/m/Y') }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="driver-name">{{ $checklist->nama_driver }}</span>
                                            </td>
                                            <td>
                                                <span class="model-text">{{ $checklist->model_kendaraan }}</span>
                                            </td>
                                            <td>
                                                @if ($checklist->checklist_pada == 'awal_masuk')
                                                    <span class="checklist-text driver-name text-success">MASUK</span>
                                                @else
                                                    <span class="checklist-text driver-name text-danger">KELUAR</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($checklist->jam_out)
                                                    <span class="time-badge">{{ \Carbon\Carbon::parse($checklist->jam_out)->format('H:i') }}</span>
                                                @else
                                                    <span class="time-badge">{{ $checklist->jam_in ? \Carbon\Carbon::parse($checklist->jam_in)->format('H:i') : '-' }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if ($checklist->bbm_awal)
                                                    <span class="bbm-km-value">{{ number_format($checklist->bbm_awal, 2) }}%</span>
                                                @else
                                                    <span class="bbm-km-value">{{ $checklist->bbm_akhir ? number_format($checklist->bbm_akhir, 2) : '-' }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if ($checklist->km_awal)
                                                    <span class="bbm-km-value">{{ number_format($checklist->km_awal) }}</span>
                                                @else
                                                    <span class="bbm-km-value">{{ $checklist->km_akhir ? number_format($checklist->km_akhir) : '-' }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="tujuan-text">
                                                    {{ strlen($checklist->tujuan) > 50 ? substr($checklist->tujuan, 0, 50) . '...' : $checklist->tujuan }}
                                                </div>
                                            </td>
                                            <td>
                                                @if ($checklist->keterangan)
                                                    <div class="keterangan-text">
                                                        {{ strlen($checklist->keterangan) > 30 ? substr($checklist->keterangan, 0, 30) . '...' : $checklist->keterangan }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">{{ $checklist->keterangan ? (strlen($checklist->keterangan) > 40 ? substr($checklist->keterangan, 0, 40) . '...' : $checklist->keterangan) : '-' }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('security.vehicle-checklist.show', $checklist->id) }}"
                                                        class="btn btn-sm btn-outline-info" title="Detail">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('security.vehicle-checklist.edit', $checklist->id) }}"
                                                        class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                    {{-- <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="confirmDelete({{ $checklist->id }})" title="Hapus">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button> --}}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5">
                                                <div class="empty-state">
                                                    <i class="mdi mdi-car-off"></i>
                                                    <h5 class="mt-3 mb-2">Tidak ada data checklist kendaraan</h5>
                                                    <p class="text-muted">Belum ada data checklist kendaraan untuk ditampilkan</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($checklists->hasPages())
                            <div class="pagination-wrapper">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">
                                            Menampilkan {{ $checklists->firstItem() }} sampai {{ $checklists->lastItem() }}
                                            dari {{ $checklists->total() }} data
                                        </small>
                                    </div>
                                    <div>
                                        {{ $checklists->withQueryString()->links() }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal Input Kembali -->
        <div class="modal fade" id="returnModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="returnForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Input Data Kembali</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Jam Masuk <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" name="jam_in" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">KM Akhir <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="km_akhir" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">BBM Akhir <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control" name="bbm_akhir"
                                            required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Keterangan Tambahan</label>
                                        <textarea class="form-control" name="keterangan_masuk" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Form -->
        <form id="deleteForm" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endsection

    @section('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // SweetAlert untuk success message
            @if (session('success'))
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
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                });
            @endif

            function showReturnModal(id) {
                const form = document.getElementById('returnForm');
                form.action = `{{ route('security.vehicle-checklist.index') }}/${id}/return`;

                const modal = new bootstrap.Modal(document.getElementById('returnModal'));
                modal.show();
            }

            function confirmDelete(id) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data checklist kendaraan akan dihapus permanen!",
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
                        fetch(`{{ route('security.vehicle-checklist.index') }}/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content'),
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => {
                                if (response.ok) {
                                    return response.text();
                                }
                                throw new Error('Network response was not ok');
                            })
                            .then(data => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Data checklist kendaraan berhasil dihapus',
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#28a745'
                                }).then(() => {
                                    location.reload();
                                });
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
            }

            // Auto refresh setiap 5 menit
            setInterval(function() {
                location.reload();
            }, 300000);
        </script>
    @endsection
