@extends('main.layouts.main')
@section('title')
    Detail Surat Perintah Lembur (SPL)
@endsection
@section('page-title')
    Detail Surat Perintah Lembur (SPL)
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Detail Surat Perintah Lembur (SPL)</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.spl.index') }}">SPL</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Detail SPL: {{ $splRequest->spl_number }}</h4>
                    <div class="float-end">
                        @php
                            $badgeClass = [
                                'draft' => 'secondary',
                                'submitted' => 'info',
                                'signed' => 'warning',
                                'approved_hrd' => 'success',
                                'rejected' => 'danger'
                            ][$splRequest->status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $badgeClass }} text-white">{{ $splRequest->status_label }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Informasi Umum -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary">Informasi Umum</h5>
                            <hr>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>No. SPL:</strong><br>
                            {{ $splRequest->spl_number }}
                        </div>
                        <div class="col-md-3">
                            <strong>Tanggal:</strong><br>
                            {{ $splRequest->request_date->format('d/m/Y') }}
                        </div>
                        <div class="col-md-3">
                            <strong>Shift:</strong><br>
                            {{ $splRequest->shift }}
                        </div>
                        <div class="col-md-3">
                            <strong>Mesin:</strong><br>
                            {{ $splRequest->mesin ?? '-' }}
                        </div>
                    </div>
                    @php
                        $eligibleForEdit = ($splRequest->supervisor_id === auth()->id()) && !$splRequest->head_approved_at && !$splRequest->manager_approved_at && !$splRequest->hrd_approved_at && $splRequest->status !== 'rejected';
                    @endphp
                    @if($eligibleForEdit)
                        <div class="row mb-3">
                            <div class="col-12 text-end">
                                <a href="{{ route('hr.spl.edit', $splRequest->id) }}" class="btn btn-warning">
                                    <i class="mdi mdi-pencil"></i> Edit SPL
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Jam mulai/selesai sekarang per karyawan, tidak ditampilkan di header --}}

                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Keperluan:</strong><br>
                            {{ $splRequest->keperluan }}
                        </div>
                    </div>

                    <!-- Daftar Karyawan -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary">Daftar Karyawan Lembur</h5>
                            <hr>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIP</th>
                                    <th>Nama Karyawan</th>
                                    <th>Jam Mulai</th>
                                    <th>Jam Selesai</th>
                                    {{-- <th>Status TTD</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($splRequest->employees as $index => $employee)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $employee->nip ?? '-' }}</td>
                                        <td>{{ $employee->employee_name }}</td>
                                        <td>{{ $employee->start_time ? $employee->start_time->format('H:i') : ($splRequest->start_time ? $splRequest->start_time->format('H:i') : '-') }}</td>
                                        <td>{{ $employee->end_time ? $employee->end_time->format('H:i') : ($splRequest->end_time ? $splRequest->end_time->format('H:i') : '-') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Upload Signed Document -->
                    @if($splRequest->supervisor_id === auth()->id() && in_array($splRequest->status, ['submitted', 'signed']))
                        <div class="row mb-4" id="upload-section">
                            <div class="col-12">
                                <h5 class="text-primary">Upload Dokumen SPL yang Sudah Ditandatangani</h5>
                                <hr>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                @if($splRequest->signed_document_path)
                                    <div class="alert alert-info">
                                        <strong>Dokumen sudah diupload:</strong><br>
                                        <a href="{{ asset('storage/' . $splRequest->signed_document_path) }}" target="_blank" class="btn btn-sm btn-primary mt-2">
                                            <i class="fas fa-eye"></i> Lihat Dokumen
                                        </a>
                                    </div>
                                @endif

                                <form action="{{ route('hr.spl.upload-signed-document', $splRequest->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Upload Foto SPL yang Sudah Ditandatangani <span class="text-danger">*</span></label>
                                        <input type="file" name="signed_document" class="form-control @error('signed_document') is-invalid @enderror" accept="image/jpeg,image/jpg,image/png" required>
                                        <small class="text-muted">Format: JPG, PNG. Maksimal 5MB</small>
                                        @error('signed_document')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload"></i> Upload Dokumen
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Approval Form (jika user bisa approve) -->
                    @if($canApprove ?? false)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-info">
                                        <h5 class="card-title mb-0 text-white">
                                            <i class="mdi mdi-check-circle me-2"></i>Form Approval
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('hr.spl.process-approval', $splRequest->id) }}" method="POST" id="approvalForm">
                                            @csrf
                                            <input type="hidden" name="action" id="approvalAction" value="approve">

                                            <div class="mb-3">
                                                <label class="form-label">Catatan <span class="text-danger">*</span> (Wajib diisi untuk penolakan)</label>
                                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" id="approvalNotes" placeholder="Masukkan catatan approval..."></textarea>
                                                @error('notes')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="d-flex gap-2 justify-content-end">
                                                <button type="button" class="btn btn-danger" onclick="submitApproval('reject')">
                                                    <i class="mdi mdi-close-circle me-2"></i>Tolak
                                                </button>
                                                <button type="button" class="btn btn-success" onclick="submitApproval('approve')">
                                                    <i class="mdi mdi-check-circle me-2"></i>Setujui
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    {{-- <div class="row mt-4">
                        <div class="col-12 text-end">
                            <a href="{{ route('hr.spl.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                            @if($splRequest->status !== 'rejected')
                                <a href="{{ route('hr.spl.print', $splRequest->id) }}" class="btn btn-primary" target="_blank">
                                    <i class="mdi mdi-printer"></i> Cetak
                                </a>
                            @endif
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    function submitApproval(action) {
        const form = document.getElementById('approvalForm');
        const actionInput = document.getElementById('approvalAction');
        const notesInput = document.getElementById('approvalNotes');

        actionInput.value = action;

        // Validasi untuk reject
        if (action === 'reject' && !notesInput.value.trim()) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Catatan wajib diisi untuk penolakan!',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'OK'
            }).then(() => {
                notesInput.focus();
            });
            return;
        }

        // Konfirmasi dengan SweetAlert
        const actionText = action === 'approve' ? 'menyetujui' : 'menolak';
        const actionIcon = action === 'approve' ? 'question' : 'warning';
        const actionColor = action === 'approve' ? '#28a745' : '#dc3545';

        Swal.fire({
            icon: actionIcon,
            title: 'Konfirmasi',
            text: `Apakah Anda yakin ingin ${actionText} SPL ini?`,
            showCancelButton: true,
            confirmButtonText: 'Ya, ' + (action === 'approve' ? 'Setujui' : 'Tolak'),
            cancelButtonText: 'Batal',
            confirmButtonColor: actionColor,
            cancelButtonColor: '#6c757d',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                form.submit();
            }
        });
    }
</script>
@endsection
