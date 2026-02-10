@extends('main.layouts.main')

@section('title')
    Laporan Keluar/Masuk Barang
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

        /* DataTable Custom Styling */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px 12px;
            margin-left: 8px;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 4px 8px;
            margin: 0 8px;
        }

        .table.dataTable thead th {
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.5rem 0.75rem;
            margin: 0 2px;
            border-radius: 4px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #007bff !important;
            color: white !important;
            border: 1px solid #007bff !important;
        }
    </style>
@endsection

@section('page-title')
    Laporan Keluar/Masuk Barang
@endsection

@section('body')

    <body data-sidebar="colored">
    @endsection

    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Laporan Keluar/Masuk Barang</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Security</a></li>
                    <li class="breadcrumb-item active">Keluar/Masuk Barang</li>
                </ol>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-filter"></i> Filter Data
                        </h5>
                        <small class="text-muted">Atau gunakan search box di tabel untuk pencarian real-time</small>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('security.goods-movement.index') }}" class="row g-3">
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
                            <div class="col-md-2">
                                <label class="form-label">Nama Pengunjung</label>
                                <input type="text" class="form-control" name="nama_pengunjung"
                                    value="{{ request('nama_pengunjung') }}" placeholder="Cari nama...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Perusahaan</label>
                                <input type="text" class="form-control" name="perusahaan_asal"
                                    value="{{ request('perusahaan_asal') }}" placeholder="Cari perusahaan...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Lokasi</label>
                                <select class="form-select form-control" name="lokasi_filter">
                                    <option value="">Semua Lokasi</option>
                                    <option value="1" {{ request('lokasi_filter') == '1' ? 'selected' : '' }}>Lokasi 19 (KRISANTHIUM)</option>
                                    <option value="2" {{ request('lokasi_filter') == '2' ? 'selected' : '' }}>Lokasi 23 (KRISANTHIUM)</option>
                                    <option value="3" {{ request('lokasi_filter') == '3' ? 'selected' : '' }}>Lokasi 15 (BERBEK)</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select class="form-select form-control" name="status_filter">
                                    <option value="">Semua Status</option>
                                    <option value="belum_keluar" {{ request('status_filter') == 'belum_keluar' ? 'selected' : '' }}>Belum Keluar</option>
                                    <option value="sudah_keluar" {{ request('status_filter') == 'sudah_keluar' ? 'selected' : '' }}>Sudah Keluar</option>
                                    <option value="lengkap" {{ request('status_filter') == 'lengkap' ? 'selected' : '' }}>Lengkap</option>
                                </select>
                            </div>
                            <div class="col">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
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
                        <a href="{{ route('security.goods-movement.create') }}" class="btn btn-success">
                            <i class="mdi mdi-plus"></i> Tambah Data
                        </a>
                        <a href="{{ route('security.goods-movement.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
                            class="btn btn-info" target="_blank">
                            <i class="mdi mdi-file-pdf"></i> Export PDF
                        </a>
                        <a href="{{ route('security.goods-movement.export-excel') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
                            class="btn btn-success">
                            <i class="mdi mdi-file-excel"></i> Export Excel
                        </a>
                    </div>
                    <div>
                        <span class="badge bg-info fs-6" style="color: white;">Total: {{ $movements->total() }} data</span>
                        @php
                            $totalIn = $movements->where('status_laporan', 'IN')->whereNull('jam_keluar')->count();
                            $totalOut = $movements->where('status_laporan', 'OUT')->count();
                            $totalComplete = $movements->where('status_laporan', 'IN')->whereNotNull('jam_keluar')->count();
                        @endphp
                        <span class="badge bg-warning fs-6 ms-2" style="color: white;">Belum Keluar: {{ $totalIn }}</span>
                        <span class="badge bg-success fs-6 ms-2" style="color: white;">Lengkap: {{ $totalComplete + $totalOut }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="goodsMovementTable" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Nama</th>
                                        <th>Perusahaan</th>
                                        <th>Status</th>
                                        <th>Barang</th>
                                        <th>Jumlah</th>
                                        <th>Jam</th>
                                        <th>Kendaraan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($movements as $index => $movement)
                                        <tr>
                                            <td class="text-center">{{ $movement->no_urut }}</td>
                                            <td>
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($movement->tanggal)->locale('id')->isoFormat('dddd') }}</small><br>
                                                {{ \Carbon\Carbon::parse($movement->tanggal)->format('d/m/Y') }}
                                            </td>
                                            <td>
                                                <strong>{{ $movement->nama_pengunjung }}</strong>
                                            </td>
                                            <td>
                                                @if ($movement->perusahaan_asal)
                                                    <small class="text-muted">{{ $movement->perusahaan_asal }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($movement->status_laporan == 'IN' && !$movement->jam_keluar)
                                                    <span class="badge bg-warning text-white">
                                                        <i class="mdi mdi-clock-in"></i> IN - Belum Keluar
                                                    </span>
                                                    <br><small class="text-muted">Perlu update jam keluar</small>
                                                @elseif ($movement->status_laporan == 'IN' && $movement->jam_keluar)
                                                    <span class="badge bg-info text-white">
                                                        <i class="mdi mdi-clock-in"></i> IN - Sudah Keluar
                                                    </span>
                                                    <br><small class="text-muted">Lengkap</small>
                                                @elseif ($movement->status_laporan == 'OUT')
                                                    <span class="badge bg-info text-white">
                                                        <i class="mdi mdi-clock-out"></i> OUT - Lengkap
                                                    </span>
                                                    <br><small class="text-muted">Sudah keluar</small>
                                                @else
                                                    <span class="badge bg-secondary text-white">
                                                        <i class="mdi mdi-help-circle"></i> Status Tidak Jelas
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small><strong>{{ strlen($movement->jenis_barang) > 30 ? substr($movement->jenis_barang, 0, 30) . '...' : $movement->jenis_barang }}</strong></small>
                                                @if ($movement->deskripsi_barang)
                                                    <br><small
                                                        class="text-muted">{{ strlen($movement->deskripsi_barang) > 40 ? substr($movement->deskripsi_barang, 0, 40) . '...' : $movement->deskripsi_barang }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($movement->jumlah)
                                                    {{ number_format($movement->jumlah) }}
                                                    @if ($movement->satuan)
                                                        <br><small class="text-muted">{{ $movement->satuan }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($movement->jam_masuk)
                                                    <div class="mb-1">
                                                        <span class="badge bg-success text-white">
                                                            <i class="mdi mdi-clock-in"></i> {{ \Carbon\Carbon::parse($movement->jam_masuk)->format('H:i') }}
                                                        </span>
                                                        <br><small class="text-muted">Masuk</small>
                                                    </div>
                                                @endif

                                                @if ($movement->jam_keluar)
                                                    <div>
                                                        <span class="badge bg-info text-white">
                                                            <i class="mdi mdi-clock-out"></i> {{ \Carbon\Carbon::parse($movement->jam_keluar)->format('H:i') }}
                                                        </span>
                                                        <br><small class="text-muted">Keluar</small>
                                                    </div>
                                                @elseif ($movement->status_laporan == 'IN')
                                                    <div class="mt-1">
                                                        <span class="badge bg-warning text-white">
                                                            <i class="mdi mdi-clock-alert"></i> Belum Keluar
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($movement->jenis_kendaraan)
                                                    <small>{{ $movement->jenis_kendaraan }}</small>
                                                    @if ($movement->no_polisi)
                                                        <br><small class="text-muted">{{ $movement->no_polisi }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('security.goods-movement.show', $movement->id) }}"
                                                        class="btn btn-sm btn-outline-info" title="Detail">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('security.goods-movement.edit', $movement->id) }}"
                                                        class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                    @if (!$movement->jam_keluar && $movement->status_laporan == 'IN')
                                                        <a href="{{ route('security.goods-movement.edit', $movement->id) }}?mode=update-jam-keluar"
                                                            class="btn btn-sm btn-outline-success" title="Update Jam Keluar">
                                                            <i class="mdi mdi-clock"></i>
                                                        </a>
                                                    @endif
                                                    {{-- <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="confirmDelete({{ $movement->id }})" title="Hapus">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button> --}}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="mdi mdi-information-outline fs-1"></i>
                                                    <p class="mt-2">Tidak ada data keluar/masuk barang</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Delete Form -->
        <form id="deleteForm" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    @endsection

    @section('script')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- DataTables JS -->
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

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

            // Initialize DataTable
            $(document).ready(function() {
                $('#goodsMovementTable').DataTable({
                    "responsive": true,
                    "autoWidth": false,
                    "pageLength": 25,
                    "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json",
                        "search": "Cari nama, perusahaan, barang:",
                        "searchPlaceholder": "Ketik nama atau perusahaan..."
                    },
                    "order": [[0, "desc"]], // Sort by No Urut descending
                    "columnDefs": [
                        {
                            "targets": [0, 4, 6, 7, 9], // No, Status, Jumlah, Jam, Aksi columns
                            "className": "text-center"
                        },
                        {
                            "targets": [9], // Aksi column
                            "orderable": false,
                            "searchable": false
                        }
                    ],
                    "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                           '<"row"<"col-sm-12"tr>>' +
                           '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    "initComplete": function() {
                        // Custom search untuk status
                        this.api().columns([4]).every(function() {
                            var column = this;
                            var select = $('<select class="form-control form-control-sm"><option value="">Semua Status</option><option value="IN - Belum Keluar">IN - Belum Keluar</option><option value="IN - Sudah Keluar">IN - Sudah Keluar</option><option value="OUT - Lengkap">OUT - Lengkap</option></select>')
                                .appendTo($(column.header()).empty())
                                .on('change', function() {
                                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                    column.search(val ? '^' + val + '$' : '', true, false).draw();
                                });
                        });
                    }
                });
            });

            function confirmDelete(id) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data keluar/masuk barang akan dihapus permanen!",
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
                        fetch(`{{ route('security.goods-movement.index') }}/${id}`, {
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
                                    text: 'Data keluar/masuk barang berhasil dihapus',
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
