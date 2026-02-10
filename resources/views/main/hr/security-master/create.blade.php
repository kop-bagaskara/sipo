@extends('main.layouts.main')

@section('title')
    Tambah User Security
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection

@section('page-title')
    Tambah User Security
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Tambah User Security</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.security-master.index') }}">Master Security</a></li>
                <li class="breadcrumb-item active">Tambah User Security</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Tambah User Security</h4>
                    <p class="card-title-desc">Isi data user security baru</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('hr.security-master.store') }}" method="POST" id="userForm">
                        @csrf

                        <!-- Personal Information -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0"><i class="mdi mdi-account"></i> Informasi Personal</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                           name="name" value="{{ old('name') }}" required>
                                                    @error('name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                           name="email" value="{{ old('email') }}" required>
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Information -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0"><i class="mdi mdi-account-key"></i> Informasi Akun</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Username <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                                           name="username" value="{{ old('username') }}" required>
                                                    @error('username')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Password <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('password') is-invalid @enderror"
                                                           name="password" required>
                                                    @error('password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control @error('password_confirmation') is-invalid @enderror"
                                                           name="password_confirmation" required>
                                                    @error('password_confirmation')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Job Information -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0"><i class="mdi mdi-briefcase"></i> Informasi Jabatan</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                                    <select class="form-control @error('jabatan') is-invalid @enderror" name="jabatan" required>
                                                        <option value="">Pilih Jabatan</option>
                                                        @foreach(\App\Models\Jabatan::all() as $jabatan)
                                                            <option value="{{ $jabatan->id }}" {{ (old('jabatan') == $jabatan->id || $jabatan->id == 6) ? 'selected' : '' }}>
                                                                {{ $jabatan->jabatan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('jabatan')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Level <span class="text-danger">*</span></label>
                                                    <select class="form-control @error('level') is-invalid @enderror" name="level" required>
                                                        <option value="">Pilih Level</option>
                                                        @foreach(\App\Models\Level::all() as $level)
                                                            <option value="{{ $level->id }}" {{ old('level') == $level->id ? 'selected' : '' }}>
                                                                {{ $level->level }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('level')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
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
                                        <button type="submit" class="btn btn-primary btn-lg me-2">
                                            <i class="mdi mdi-content-save"></i> Simpan User
                                        </button>
                                        <a href="{{ route('hr.security-master.index') }}" class="btn btn-secondary btn-lg">
                                            <i class="mdi mdi-arrow-left"></i> Kembali
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
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

        // SweetAlert untuk validation errors
        @if($errors->any())
            let errorMessages = '';
            @foreach($errors->all() as $error)
                errorMessages += '• {{ $error }}\n';
            @endforeach

            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: errorMessages,
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33'
            });
        @endif

        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('userForm');
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Konfirmasi simpan
                Swal.fire({
                    title: 'Yakin ingin menyimpan?',
                    text: "User security baru akan ditambahkan!",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'Menyimpan Data...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit form
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
