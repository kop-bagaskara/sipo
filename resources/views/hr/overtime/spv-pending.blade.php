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
                        <h5 class="card-title mb-0">Rekap Data Lembur - Menunggu Persetujuan SPV</h5>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Bulk Actions -->
                        <div class="bulk-actions" id="bulkActions" style="display: none;">
                            <form id="bulkApproveForm" action="{{ route('hr.overtime.spv-bulk-approve') }}" method="POST">
                                @csrf
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <strong>Bulk Actions:</strong>
                                        <span id="selectedCount">0</span> item dipilih
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-success btn-sm"
                                                onclick="submitBulkAction('approve')">
                                                <i class="mdi mdi-check"></i> Setujui Semua
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm"
                                                onclick="clearSelection()">
                                                <i class="mdi mdi-close"></i> Batal
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <textarea name="notes" class="form-control form-control-sm"
                                            placeholder="Catatan untuk semua item yang dipilih (opsional)" rows="2"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                            <label for="selectAll" class="form-check-label visually-hidden">Pilih
                                                Semua</label>
                                        </th>
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
                                            <td>
                                                <input type="checkbox" class="form-check-input entry-checkbox"
                                                    value="{{ $entry->id }}" id="entry_{{ $entry->id }}">
                                                <label for="entry_{{ $entry->id }}"
                                                    class="form-check-label visually-hidden">Pilih</label>
                                            </td>
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
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="rejectEntry({{ $entry->id }})">
                                                        <i class="mdi mdi-close"></i>
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
                                                                <span class="badge bg-warning">Menunggu SPV</span><br>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <strong>Keterangan Pekerjaan:</strong><br>
                                                                <p class="mt-2">{{ $entry->job_description }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Tutup</button>
                                                        <button type="button" class="btn btn-success"
                                                            onclick="approveEntry({{ $entry->id }})">
                                                            <i class="mdi mdi-check"></i> Setujui
                                                        </button>
                                                        <button type="button" class="btn btn-danger"
                                                            onclick="rejectEntry({{ $entry->id }})">
                                                            <i class="mdi mdi-close"></i> Tolak
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="mdi mdi-information-outline fs-1"></i>
                                                    <p class="mt-2">Tidak ada data lembur yang menunggu persetujuan SPV
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
                        <h5 class="modal-title">Setujui Data Lembur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="approveForm" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="approve_notes" class="form-label">Catatan (Opsional)</label>
                                <textarea class="form-control" id="approve_notes" name="notes" rows="3"
                                    placeholder="Masukkan catatan jika diperlukan"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Setujui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div class="modal fade" id="rejectModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tolak Data Lembur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="rejectForm" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="reject_notes" class="form-label">Alasan Penolakan</label>
                                <textarea class="form-control" id="reject_notes" name="notes" rows="3"
                                    placeholder="Masukkan alasan penolakan" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Tolak</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectAllCheckbox = document.getElementById('selectAll');
                const entryCheckboxes = document.querySelectorAll('.entry-checkbox');
                const bulkActions = document.getElementById('bulkActions');
                const selectedCount = document.getElementById('selectedCount');

                // Select All functionality
                selectAllCheckbox.addEventListener('change', function() {
                    entryCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateBulkActions();
                });

                // Individual checkbox change
                entryCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        updateBulkActions();
                    });
                });

                function updateBulkActions() {
                    const checkedBoxes = document.querySelectorAll('.entry-checkbox:checked');
                    const totalBoxes = entryCheckboxes.length;

                    selectedCount.textContent = checkedBoxes.length;

                    if (checkedBoxes.length > 0) {
                        bulkActions.style.display = 'block';
                    } else {
                        bulkActions.style.display = 'none';
                    }

                    // Update select all checkbox state
                    if (checkedBoxes.length === 0) {
                        selectAllCheckbox.indeterminate = false;
                        selectAllCheckbox.checked = false;
                    } else if (checkedBoxes.length === totalBoxes) {
                        selectAllCheckbox.indeterminate = false;
                        selectAllCheckbox.checked = true;
                    } else {
                        selectAllCheckbox.indeterminate = true;
                    }
                }

                // Clear selection
                window.clearSelection = function() {
                    entryCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                    updateBulkActions();
                };

                // Bulk approve
                window.submitBulkAction = function(action) {
                    const checkedBoxes = document.querySelectorAll('.entry-checkbox:checked');
                    if (checkedBoxes.length === 0) {
                        alert('Pilih minimal satu item');
                        return;
                    }

                    const form = document.getElementById('bulkApproveForm');
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids';
                    input.value = Array.from(checkedBoxes).map(cb => cb.value).join(',');
                    form.appendChild(input);

                    form.submit();
                };
            });

            // Approve individual entry
            window.approveEntry = function(entryId) {
                const form = document.getElementById('approveForm');
                form.action = `/sipo/hr/overtime/${entryId}/spv-approve`;
                const modal = new bootstrap.Modal(document.getElementById('approveModal'));
                modal.show();
            };

            // Reject individual entry
            window.rejectEntry = function(entryId) {
                const form = document.getElementById('rejectForm');
                form.action = `/sipo/hr/overtime/${entryId}/spv-reject`;
                const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
                modal.show();
            };
        </script>
    @endsection
