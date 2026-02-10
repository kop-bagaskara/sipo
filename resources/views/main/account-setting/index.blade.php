@extends('main.layouts.main')

@section('title', 'Account Setting')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Account Setting</h4>
                    <p class="card-subtitle">Kelola informasi akun dan keamanan Anda</p>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Profile Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Informasi Profil</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('account.setting.profile') }}" method="POST">
                                        @csrf
                                        @method('PATCH')

                                        <div class="form-group">
                                            <label for="name">Nama Lengkap</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name', $user->name) }}"
                                                required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                id="email" name="email" value="{{ old('email', $user->email) }}"
                                                required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>Jabatan</label>
                                            <input type="text" class="form-control"
                                                value="{{ $user->jabatanUser->jabatan ?? 'N/A' }}" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label>Divisi</label>
                                            <input type="text" class="form-control"
                                                value="{{ $user->divisiUser->divisi ?? 'N/A' }}" readonly>
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="mdi mdi-content-save"></i> Update Profil
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Avatar Upload -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Foto Profil</h5>
                                </div>
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        @if ($user->avatar)
                                            <img src="{{ Storage::url($user->avatar) }}" alt="Avatar"
                                                class="rounded-circle" width="120" height="120"
                                                style="object-fit: cover;">
                                        @else
                                            <img src="{{ asset('sipo_krisan/public/assets/images/logo-kop.png') }}"
                                                alt="Default Avatar" class="rounded-circle" width="120" height="120"
                                                style="object-fit: cover;">
                                        @endif
                                    </div>

                                    <form action="{{ route('account.setting.avatar') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PATCH')

                                        <div class="form-group">
                                            <label for="avatar">Pilih Foto Profil</label>
                                            <input type="file"
                                                class="form-control-file @error('avatar') is-invalid @enderror"
                                                id="avatar" name="avatar" accept="image/*">
                                            @error('avatar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Format: JPG, PNG, GIF. Maksimal 2MB
                                            </small>
                                        </div>

                                        <button type="submit" class="btn btn-info">
                                            <i class="mdi mdi-upload"></i> Upload Foto
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Ganti Password</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('account.setting.password') }}" method="POST">
                                        @csrf
                                        @method('PATCH')

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="current_password">Password Saat Ini</label>
                                                    <input type="password"
                                                        class="form-control @error('current_password') is-invalid @enderror"
                                                        id="current_password" name="current_password" required>
                                                    @error('current_password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="password">Password Baru</label>
                                                    <input type="password"
                                                        class="form-control @error('password') is-invalid @enderror"
                                                        id="password" name="password" required>
                                                    @error('password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="password_confirmation">Konfirmasi Password Baru</label>
                                                    <input type="password" class="form-control"
                                                        id="password_confirmation" name="password_confirmation" required>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-warning">
                                            <i class="mdi mdi-key"></i> Ganti Password
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Account Security Info -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Informasi Keamanan</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            {{-- <p><strong>Terakhir Login:</strong> {{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : 'Belum pernah login' }}</p> --}}
                                            <p><strong>Akun Dibuat:</strong>
                                                {{ $user->created_at ? $user->created_at->format('d M Y H:i') : 'Tidak diketahui' }}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Status Akun:</strong>
                                                <span class="badge badge-success">Aktif</span>
                                            </p>
                                            <p><strong>Email Terverifikasi:</strong>
                                                @if ($user->email_verified_at)
                                                    <span class="badge badge-success">Ya</span>
                                                @else
                                                    <span class="badge badge-warning">Belum</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .btn {
            border-radius: 5px;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        }

        .alert {
            border-radius: 5px;
        }

        .badge {
            font-size: 0.8em;
        }
    </style>
@endsection
