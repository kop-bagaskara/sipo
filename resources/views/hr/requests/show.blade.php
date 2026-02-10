@extends('main.layouts.main')
@section('title')
    Detail Permohonan
@endsection
@section('css')
    <style>
        .cust-col {
            white-space: nowrap;
        }
    </style>
@endsection
@section('page-title')
    Detail Permohonan
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Detail Permohonan</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">Detail Permohonan</li>
                </ol>
            </div>
        </div>

        <!-- Request Info -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Informasi Pengajuan</h4>
                        <div>
                            <span class="badge {{ $request->status_badge_class }} fs-6">{{ $request->status_label }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%"><strong>No. Pengajuan:</strong></td>
                                        <td>{{ $request->request_number }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jenis Pengajuan:</strong></td>
                                        <td>{{ $request->request_type_label }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Pemohon:</strong></td>
                                        <td>{{ $request->employee->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Bagian:</strong></td>
                                        @php
                                            $divisi = $request->employee->divisi;
                                            $dataDivisi = \App\Models\Divisi::find($divisi);
                                        @endphp
                                        <td>{{ $dataDivisi->divisi ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%"><strong>Tanggal Dibuat:</strong></td>
                                        <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Lama Pengajuan:</strong></td>
                                        <td>{{ $request->days_since_created }} hari</td>
                                    </tr>
                                    @if ($request->supervisor)
                                        <tr>
                                            <td><strong>Atasan:</strong></td>
                                            <td>{{ $request->supervisor->name }}</td>
                                        </tr>
                                    @endif
                                    @if ($request->hr)
                                        <tr>
                                            <td><strong>HR:</strong></td>
                                            <td>{{ $request->hr->name }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Details -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Detail Pengajuan</h4>
                    </div>
                    <div class="card-body">
                        @if ($request->request_type == 'overtime' && $request->overtimeEmployees->count() > 0)
                            <!-- Overtime Employees Table -->
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Karyawan</th>
                                            <th>Bagian</th>
                                            <th>Jam Kerja</th>
                                            <th>Keterangan Pekerjaan</th>
                                            <th>Tanda Tangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($request->overtimeEmployees as $index => $employee)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $employee->employee_name }}</td>
                                                <td>{{ $employee->department }}</td>
                                                <td>{{ $employee->time_range }}</td>
                                                <td>{{ $employee->job_description }}</td>
                                                <td>
                                                    @if ($employee->is_signed)
                                                        <span class="badge bg-success">Sudah Ditandatangani</span>
                                                    @else
                                                        <span class="badge bg-warning">Belum Ditandatangani</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <!-- Regular Request Data -->
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tbody>
                                        @foreach ($request->formatted_request_data as $key => $value)
                                            <tr>
                                                <td width="30%"><strong>{{ $key }}:</strong></td>
                                                <td>{{ $value }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        @if ($request->notes)
                            <div class="mt-3">
                                <h6><strong>Catatan Tambahan:</strong></h6>
                                <p class="text-muted">{{ $request->notes }}</p>
                            </div>
                        @endif

                        @if ($request->attachment_path)
                            <div class="mt-3">
                                <h6><strong>Lampiran:</strong></h6>
                                <a href="{{ asset('storage/' . $request->attachment_path) }}" target="_blank"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-download me-2"></i>Download Lampiran
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval History -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="mdi mdi-history me-2"></i>Riwayat Approval
                        </h4>
                    </div>
                    <div class="card-body">
                        @if (isset($approvalHistory) && count($approvalHistory) > 0)
                            <div class="horizontal-timeline p-4" style="background: #f8f9fa; border-radius: 12px;">
                                {{-- Container horizontal dengan line connector --}}
                                <div class="d-flex justify-content-between align-items-start position-relative"
                                    style="overflow-x: auto;">
                                    {{-- Line connecting all steps --}}
                                    <div
                                        style="position: absolute; top: 26px; left: 50px; right: 50px; height: 4px; background: #dee2e6; z-index: 0;">
                                    </div>

                                    @foreach ($approvalHistory as $index => $history)
                                        {{-- Calculate progress width --}}
                                        @php
                                            if ($index === 0) {
                                                $progressWidth = '0%';
                                            } elseif ($history['status'] === 'completed') {
                                                $progressWidth = ($index / (count($approvalHistory) - 1)) * 100;
                                            } else {
                                                $progressWidth = (($index - 1) / (count($approvalHistory) - 1)) * 100;
                                            }
                                        @endphp

                                        {{-- Progress line (only for completed) --}}
                                        @if ($history['status'] === 'completed')
                                            <div
                                                style="position: absolute; top: 26px; left: 50px; height: 4px; background: linear-gradient(to right, #28a745, #20c997); width: {{ $progressWidth }}%; z-index: 1;">
                                            </div>
                                        @endif

                                        {{-- Timeline Item --}}
                                        <div class="flex-shrink-0 text-center"
                                            style="flex: 1; min-width: 140px; max-width: 200px; z-index: 2;">
                                            {{-- Icon/Marker --}}
                                            <div class="mb-3">
                                                @if ($history['status'] === 'completed')
                                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mx-auto shadow-sm"
                                                        style="width: 56px; height: 56px; background: linear-gradient(135deg, #28a745, #20c997);">
                                                        <i class="mdi {{ $history['icon'] }} text-white"
                                                            style="font-size: 26px;"></i>
                                                    </div>
                                                @elseif ($history['status'] === 'rejected')
                                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mx-auto shadow-sm"
                                                        style="width: 56px; height: 56px; background: linear-gradient(135deg, #dc3545, #c82333);">
                                                        <i class="mdi mdi-close text-white" style="font-size: 26px;"></i>
                                                    </div>
                                                @else
                                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mx-auto bg-light border shadow-sm"
                                                        style="width: 56px; height: 56px; border-width: 3px !important;">
                                                        <i class="mdi mdi-clock-outline text-muted"
                                                            style="font-size: 26px;"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Content Card --}}
                                            <div class="card shadow-sm border-0 mb-2">
                                                <div class="card-body p-2">
                                                    @if ($history['status'] === 'completed')
                                                        <div class="mb-1">
                                                            <span class="badge bg-success"
                                                                style="font-size: 10px;">Selesai</span>
                                                        </div>
                                                        <h6 class="mb-1"
                                                            style="color: #28a745; font-size: 12px; line-height: 1.3;">
                                                            <i class="mdi mdi-check-circle me-1"></i>
                                                            {{ $history['title'] }}
                                                        </h6>
                                                    @elseif ($history['status'] === 'rejected')
                                                        <div class="mb-1">
                                                            <span class="badge bg-danger"
                                                                style="font-size: 10px;">Ditolak</span>
                                                        </div>
                                                        <h6 class="mb-1"
                                                            style="color: #dc3545; font-size: 12px; line-height: 1.3;">
                                                            <i class="mdi mdi-close-circle me-1"></i>
                                                            {{ $history['title'] }}
                                                        </h6>
                                                    @else
                                                        <div class="mb-1">
                                                            <span class="badge bg-warning text-dark"
                                                                style="font-size: 10px;">Pending</span>
                                                        </div>
                                                        <h6 class="mb-1 text-muted"
                                                            style="font-size: 12px; line-height: 1.3;">
                                                            <i class="mdi mdi-clock-outline me-1"></i>
                                                            {{ $history['title'] }}
                                                        </h6>
                                                    @endif

                                                    @if ($history['approver'])
                                                        <p class="mb-1" style="font-size: 11px;">
                                                            <strong>{{ $history['approver'] }}</strong>
                                                        </p>
                                                    @endif

                                                    @if ($history['timestamp'])
                                                        <p class="mb-0 text-muted" style="font-size: 10px;">
                                                            {{ $history['timestamp']->format('d/m/y H:i') }}
                                                        </p>
                                                    @endif

                                                    @if ($history['notes'])
                                                        <div class="alert alert-info mt-2 py-1 px-2"
                                                            style="font-size: 10px;">
                                                            <small class="mb-0 d-block"
                                                                style="max-height: 40px; overflow: hidden; text-overflow: ellipsis;">
                                                                <strong>Catatan:</strong>
                                                                {{ \Illuminate\Support\Str::limit($history['notes'], 40) }}
                                                            </small>
                                                        </div>
                                                    @endif

                                                    {{-- Pelaksana Tugas (only for HEAD approval) --}}
                                                    @if (
                                                        $history['title'] === 'Approval HEAD DIVISI' &&
                                                            $request->replacement_person_name &&
                                                            $history['status'] === 'completed')
                                                        <div class="alert alert-warning py-1 px-2 mt-2"
                                                            style="font-size: 10px;">
                                                            <strong><i class="mdi mdi-account-switch"></i></strong>
                                                            <span class="d-block mt-1"
                                                                style="max-height: 30px; overflow: hidden;">
                                                                {{ \Illuminate\Support\Str::limit($request->replacement_person_name, 25) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Legend --}}
                                <div class="d-flex justify-content-center gap-4 mt-4 pt-3 border-top">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle me-2"
                                            style="width: 14px; height: 14px; background: linear-gradient(135deg, #28a745, #20c997);">
                                        </div>
                                        <small class="text-muted">Selesai</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light border me-2"
                                            style="width: 14px; height: 14px; border-width: 2px !important;"></div>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle me-2"
                                            style="width: 14px; height: 14px; background: linear-gradient(135deg, #dc3545, #c82333);">
                                        </div>
                                        <small class="text-muted">Ditolak</small>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="mdi mdi-information me-2"></i>
                                Tidak ada riwayat approval yang ditemukan.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('hr.requests.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left me-2"></i>Kembali
                            </a>

                            <div>
                                @if ($request->employee_id == auth()->id() && $request->status == 'pending')
                                    <a href="{{ route('hr.requests.edit', $request->id) }}" class="btn btn-warning me-2">
                                        <i class="mdi mdi-pencil me-2"></i>Edit
                                    </a>
                                    <form method="POST" action="{{ route('hr.requests.cancel', $request->id) }}"
                                        class="d-inline"
                                        onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">
                                            <i class="mdi mdi-delete me-2"></i>Batalkan
                                        </button>
                                    </form>
                                @endif

                                @if (auth()->user()->is_hr && $request->canBeApprovedByHR())
                                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal"
                                        data-bs-target="#approveModal">
                                        <i class="mdi mdi-check me-2"></i>Setujui
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#rejectModal">
                                        <i class="mdi mdi-delete me-2"></i>Tolak
                                    </button>
                                @elseif((int) auth()->user()->jabatan === 5 && isset($canApprove) && $canApprove)
                                    {{-- SPV (jabatan 5) --}}
                                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal"
                                        data-bs-target="#approveModal">
                                        <i class="mdi mdi-check me-2"></i>Setujui
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#rejectModal">
                                        <i class="mdi mdi-delete me-2"></i>Tolak
                                    </button>
                                @elseif((int) auth()->user()->jabatan === 4 && isset($canApprove) && $canApprove)
                                    {{-- HEAD (jabatan 4) --}}
                                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal"
                                        data-bs-target="#approveModal">
                                        <i class="mdi mdi-check me-2"></i>Setujui
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#rejectModal">
                                        <i class="mdi mdi-delete me-2"></i>Tolak
                                    </button>
                                @elseif((int) auth()->user()->jabatan === 3 && isset($canApprove) && $canApprove)
                                    {{-- MANAGER (jabatan 3) --}}
                                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal"
                                        data-bs-target="#approveModal">
                                        <i class="mdi mdi-check me-2"></i>Setujui
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#rejectModal">
                                        <i class="mdi mdi-delete me-2"></i>Tolak
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve Modal -->
        <div class="modal fade" id="approveModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('hr.requests.approve', $request->id) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Setujui Pengajuan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Catatan (Opsional)</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Catatan approval"></textarea>
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
                    <form method="POST" action="{{ route('hr.requests.reject', $request->id) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Tolak Pengajuan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Alasan penolakan" required></textarea>
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

    @endsection
