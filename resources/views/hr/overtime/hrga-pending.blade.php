@extends('main.layouts.main')
@section('title')
    Permohonan Data Karyawan
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
    Permohonan Data Karyawan
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Permohonan Data Karyawan</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">Permohonan Data Karyawan</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Persetujuan HRGA - Data Lembur (Final Approval)</h5>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>Lokasi</th>
                                        <th>Nama Karyawan</th>
                                        <th>Bagian</th>
                                        <th>Jam Mulai</th>
                                        <th>Jam Selesai</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($entries as $entry)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $entry->request_date->format('d/m/Y') }}</td>
                                            <td>{{ $entry->location }}</td>
                                            <td>{{ $entry->employee_name }}</td>
                                            <td>{{ $entry->department }}</td>
                                            <td>{{ $entry->start_time->format('H:i') }}</td>
                                            <td>{{ $entry->end_time->format('H:i') }}</td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 150px;"
                                                    title="{{ $entry->job_description }}">
                                                    {{ \Illuminate\Support\Str::limit($entry->job_description, 50) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-info"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#detailModal{{ $entry->id }}">
                                                        <i class="mdi mdi-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-success"
                                                        onclick="approveEntry({{ $entry->id }})">
                                                        <i class="mdi mdi-check"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Detail Modal -->
                                        <div class="modal fade" id="detailModal{{ $entry->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Detail Data Lembur</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <strong>Tanggal:</strong>
                                                                {{ $entry->request_date->format('d/m/Y') }}<br>
                                                                <strong>Lokasi:</strong> {{ $entry->location }}<br>
                                                                <strong>Nama Karyawan:</strong>
                                                                {{ $entry->employee_name }}<br>
                                                                <strong>Bagian:</strong> {{ $entry->department }}<br>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <strong>Jam Mulai:</strong>
                                                                {{ $entry->start_time->format('H:i') }}<br>
                                                                <strong>Jam Selesai:</strong>
                                                                {{ $entry->end_time->format('H:i') }}<br>
                                                                <strong>Status:</strong>
                                                                <span class="badge bg-primary">Disetujui Head</span><br>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <strong>Keterangan Pekerjaan:</strong><br>
                                                                <p class="mt-2">{{ $entry->job_description }}</p>
                                                            </div>
                                                        </div>

                                                        @if ($entry->spv_notes)
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <strong>Catatan SPV:</strong><br>
                                                                    <p class="mt-2 text-muted">{{ $entry->spv_notes }}</p>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if ($entry->head_notes)
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <strong>Catatan Head:</strong><br>
                                                                    <p class="mt-2 text-muted">{{ $entry->head_notes }}</p>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="mdi mdi-information-outline fs-1"></i>
                                                    <p class="mt-2">Tidak ada data lembur yang menunggu persetujuan HRGA
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($entries->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $entries->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


        <!-- Approve Modal -->
        <div class="modal fade" id="approveModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Final Approval - Data Lembur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="approveForm" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="mdi mdi-information"></i>
                                <strong>Final Approval:</strong> Setelah disetujui, data lembur akan selesai dan tidak dapat
                                diubah lagi.
                            </div>
                            <div class="mb-3">
                                <label for="approve_notes" class="form-label">Catatan HRGA (Opsional)</label>
                                <textarea class="form-control" id="approve_notes" name="notes" rows="3"
                                    placeholder="Masukkan catatan HRGA jika diperlukan"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">
                                <i class="mdi mdi-check-circle"></i> Final Approval
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            // Approve individual entry
            window.approveEntry = function(entryId) {
                const form = document.getElementById('approveForm');
                form.action = `/sipo/hr/overtime/${entryId}/hrga-approve`;
                const modal = new bootstrap.Modal(document.getElementById('approveModal'));
                modal.show();
            };
        </script>
    @endsection
