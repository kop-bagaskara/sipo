@extends('main.layouts.main')
@section('title')
    Pengajuan Cuti Berhasil
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .success-container {
            text-align: center;
            padding: 40px 20px;
        }
        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .success-title {
            font-size: 28px;
            color: #28a745;
            margin-bottom: 15px;
        }
        .success-message {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 30px;
        }
        .request-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        .info-value {
            color: #6c757d;
        }
    </style>
@endsection
@section('page-title')
    Pengajuan Cuti Berhasil
@endsection
@section('body')
    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="success-container">
                            <div class="success-icon">
                                <i class="mdi mdi-check-circle"></i>
                            </div>
                            <h2 class="success-title">Pengajuan Cuti Berhasil!</h2>
                            <p class="success-message">
                                Pengajuan cuti Anda telah berhasil dikirim dan sedang menunggu persetujuan.
                            </p>

                            <div class="request-info">
                                <h5>Detail Pengajuan</h5>
                                <div class="info-row">
                                    <span class="info-label">ID Pengajuan:</span>
                                    <span class="info-value">#{{ $leaveRequest->id }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Nama:</span>
                                    <span class="info-value">{{ $leaveRequest->employee->nama_lengkap }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Jenis Cuti:</span>
                                    <span class="info-value">{{ $leaveRequest->leave_type_formatted }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Tanggal Mulai:</span>
                                    <span class="info-value">{{ $leaveRequest->start_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Tanggal Selesai:</span>
                                    <span class="info-value">{{ $leaveRequest->end_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Durasi:</span>
                                    <span class="info-value">{{ $leaveRequest->duration_days }} hari</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Status:</span>
                                    <span class="info-value">
                                        <span class="badge bg-warning">{{ $leaveRequest->status_formatted }}</span>
                                    </span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Tanggal Pengajuan:</span>
                                    <span class="info-value">{{ $leaveRequest->submitted_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>

                            <div class="mt-4">
                                <p class="text-muted">
                                    <i class="mdi mdi-information me-2"></i>
                                    Anda akan menerima notifikasi melalui email atau SMS ketika status pengajuan berubah.
                                </p>
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('hr.leave-request.form', ['id' => $leaveRequest->employee_id]) }}"
                                   class="btn btn-primary me-2">
                                    <i class="mdi mdi-plus me-2"></i>Ajukan Cuti Lagi
                                </a>
                                <button onclick="window.print()" class="btn btn-outline-secondary">
                                    <i class="mdi mdi-printer me-2"></i>Cetak
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                // Auto print after 3 seconds
                setTimeout(function() {
                    if (confirm('Apakah Anda ingin mencetak bukti pengajuan?')) {
                        window.print();
                    }
                }, 3000);
            </script>
        @endpush
    @endsection
