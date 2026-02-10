@extends('main.layouts.main')

@section('title')
    Detail User Security
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection

@section('page-title')
    Detail User Security
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Detail User Security</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.security-master.index') }}">Master Security</a></li>
                <li class="breadcrumb-item active">Detail User Security</li>
            </ol>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <div>
                    <a href="{{ route('hr.security-master.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back"></i> Kembali
                    </a>
                    <a href="{{ route('hr.security-master.edit', $securityMaster->id) }}" class="btn btn-warning">
                        <i class="bx bx-edit"></i> Edit
                    </a>
                </div>
                <div>
                    <button onclick="window.print()" class="btn btn-info">
                        <i class="bx bx-printer"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- User Information -->
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0 text-white">
                        <i class="bx bx-user"></i> Informasi User Security
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="avatar-lg mx-auto mb-3">
                                    <div class="avatar-title bg-soft-primary text-primary rounded-circle font-size-24">
                                        {{ substr($securityMaster->name, 0, 1) }}
                                    </div>
                                </div>
                                <h5 class="font-size-16 mb-1">{{ $securityMaster->name }}</h5>
                                <p class="text-muted mb-2">{{ $securityMaster->email }}</p>
                                <span class="badge bg-info font-size-12">User Security</span>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Nama Lengkap</label>
                                        <p class="mb-0 fw-bold">{{ $securityMaster->name }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Email</label>
                                        <p class="mb-0">{{ $securityMaster->email }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Divisi</label>
                                        <p class="mb-0">
                                            <span class="badge bg-primary font-size-12">
                                                {{ $securityMaster->divisiUser->divisi ?? 'Security' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Jabatan</label>
                                        <p class="mb-0">{{ $securityMaster->jabatanUser->jabatan ?? '-' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Level</label>
                                        <p class="mb-0">{{ $securityMaster->levelUser->level ?? '-' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Dibuat</label>
                                        <p class="mb-0">{{ $securityMaster->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    @if($securityMaster->updated_at != $securityMaster->created_at)
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Diupdate</label>
                                            <p class="mb-0">{{ $securityMaster->updated_at->format('d/m/Y H:i') }}</p>
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

    <!-- Additional Information -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bx bx-shield"></i> Informasi Keamanan</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Username</label>
                        <p class="mb-0">{{ $securityMaster->username ?? $securityMaster->email }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Email Verified</label>
                        <p class="mb-0">
                            @if($securityMaster->email_verified_at)
                                <span class="badge bg-success">Verified</span>
                                <small class="text-muted d-block">{{ $securityMaster->email_verified_at->format('d/m/Y H:i') }}</small>
                            @else
                                <span class="badge bg-warning">Not Verified</span>
                            @endif
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Last Login</label>
                        <p class="mb-0">{{ $securityMaster->last_login_at ?? 'Never' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bx bx-info-circle"></i> Informasi Sistem</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">User ID</label>
                        <p class="mb-0 font-monospace">{{ $securityMaster->id }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Created At</label>
                        <p class="mb-0">{{ $securityMaster->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Updated At</label>
                        <p class="mb-0">{{ $securityMaster->updated_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <a href="{{ route('hr.security-master.edit', $securityMaster->id) }}" class="btn btn-warning btn-lg me-2">
                        <i class="mdi mdi-pencil"></i> Edit User
                    </a>
                    <button type="button" class="btn btn-danger btn-lg" onclick="deleteUser({{ $securityMaster->id }}, '{{ $securityMaster->name }}')">
                        <i class="mdi mdi-delete"></i> Hapus User
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function deleteUser(userId, userName) {
            Swal.fire({
                title: 'Yakin ingin menghapus user ini?',
                text: `User "${userName}" akan dihapus secara permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/hr/security-master/${userId}`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';

                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

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
            .btn, .breadcrumb, .page-title-box {
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
