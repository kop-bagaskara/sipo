@extends('main.layouts.main')

@section('title')
    {{ isset($employee) ? 'Edit' : 'Tambah' }} Data Karyawan
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">{{ isset($employee) ? 'Edit' : 'Tambah' }} Data Karyawan</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('hr.employee-data.index') }}">Data Karyawan</a></li>
                                <li class="breadcrumb-item active">{{ isset($employee) ? 'Edit' : 'Tambah' }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Form Data Karyawan</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ isset($employee) ? route('hr.employee-data.update', $employee->id) : route('hr.employee-data.store') }}" method="POST" enctype="multipart/form-data" id="employeeForm">
                                @csrf
                                @if(isset($employee))
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nip" class="form-label">NIP <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nip') is-invalid @enderror"
                                            id="nip" name="nip" value="{{ old('nip', $employee->nip ?? '') }}" required>
                                        @error('nip')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="nama_karyawan" class="form-label">Nama Karyawan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nama_karyawan') is-invalid @enderror"
                                            id="nama_karyawan" name="nama_karyawan" value="{{ old('nama_karyawan', $employee->nama_karyawan ?? '') }}" required>
                                        @error('nama_karyawan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="lp" class="form-label">LP</label>
                                        <input type="text" class="form-control @error('lp') is-invalid @enderror"
                                            id="lp" name="lp" value="{{ old('lp', $employee->lp ?? '') }}">
                                        @error('lp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="lvl" class="form-label">LVL</label>
                                        <input type="text" class="form-control @error('lvl') is-invalid @enderror"
                                            id="lvl" name="lvl" value="{{ old('lvl', $employee->lvl ?? '') }}">
                                        @error('lvl')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="dept" class="form-label">DEPT</label>
                                        <input type="text" class="form-control @error('dept') is-invalid @enderror"
                                            id="dept" name="dept" value="{{ old('dept', $employee->dept ?? '') }}">
                                        @error('dept')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="bagian" class="form-label">BAGIAN</label>
                                        <input type="text" class="form-control @error('bagian') is-invalid @enderror"
                                            id="bagian" name="bagian" value="{{ old('bagian', $employee->bagian ?? '') }}">
                                        @error('bagian')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="tgl_masuk" class="form-label">TGL MASUK</label>
                                        <input type="date" class="form-control @error('tgl_masuk') is-invalid @enderror"
                                            id="tgl_masuk" name="tgl_masuk" value="{{ old('tgl_masuk', $employee->tgl_masuk ?? '') }}">
                                        @error('tgl_masuk')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="status_update" class="form-label">STATUS UPDATE</label>
                                        <input type="text" class="form-control @error('status_update') is-invalid @enderror"
                                            id="status_update" name="status_update" value="{{ old('status_update', $employee->status_update ?? '') }}">
                                        @error('status_update')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="masa_kerja" class="form-label">MASA KERJA</label>
                                        <input type="text" class="form-control @error('masa_kerja') is-invalid @enderror"
                                            id="masa_kerja" name="masa_kerja" value="{{ old('masa_kerja', $employee->masa_kerja ?? '') }}" readonly>
                                        <small class="text-muted">Akan dihitung otomatis dari TGL MASUK</small>
                                        @error('masa_kerja')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="tanggal_awal" class="form-label">TANGGAL AWAL</label>
                                        <input type="date" class="form-control @error('tanggal_awal') is-invalid @enderror"
                                            id="tanggal_awal" name="tanggal_awal" value="{{ old('tanggal_awal', $employee->tanggal_awal ?? '') }}">
                                        @error('tanggal_awal')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="tanggal_berakhir" class="form-label">TANGGAL BERAKHIR</label>
                                        <input type="date" class="form-control @error('tanggal_berakhir') is-invalid @enderror"
                                            id="tanggal_berakhir" name="tanggal_berakhir" value="{{ old('tanggal_berakhir', $employee->tanggal_berakhir ?? '') }}">
                                        @error('tanggal_berakhir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="foto" class="form-label">Foto Karyawan</label>
                                        <input type="file" class="form-control @error('foto') is-invalid @enderror"
                                            id="foto" name="foto" accept="image/*" onchange="previewImage(this)">
                                        @error('foto')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if(isset($employee) && $employee->foto_path)
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/' . $employee->foto_path) }}" alt="Foto Karyawan" class="preview-image" id="preview">
                                            </div>
                                        @else
                                            <div class="mt-2" id="preview-container" style="display: none;">
                                                <img id="preview" class="preview-image" alt="Preview">
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <hr>
                                <h5 class="mb-3">Data Pribadi</h5>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="tempat_lahir" class="form-label">TEMPAT LAHIR</label>
                                        <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror"
                                            id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $employee->tempat_lahir ?? '') }}">
                                        @error('tempat_lahir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="tgl_lahir" class="form-label">TGL LAHIR</label>
                                        <input type="date" class="form-control @error('tgl_lahir') is-invalid @enderror"
                                            id="tgl_lahir" name="tgl_lahir" value="{{ old('tgl_lahir', $employee->tgl_lahir ?? '') }}" onchange="calculateUsia()">
                                        @error('tgl_lahir')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="usia" class="form-label">USIA</label>
                                        <input type="number" class="form-control @error('usia') is-invalid @enderror"
                                            id="usia" name="usia" value="{{ old('usia', $employee->usia ?? '') }}" readonly>
                                        <small class="text-muted">Akan dihitung otomatis dari TGL LAHIR</small>
                                        @error('usia')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="alamat_ktp" class="form-label">ALAMAT KTP</label>
                                        <textarea class="form-control @error('alamat_ktp') is-invalid @enderror"
                                            id="alamat_ktp" name="alamat_ktp" rows="3">{{ old('alamat_ktp', $employee->alamat_ktp ?? '') }}</textarea>
                                        @error('alamat_ktp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="alamat_domisili" class="form-label">ALAMAT DOMISILI</label>
                                        <textarea class="form-control @error('alamat_domisili') is-invalid @enderror"
                                            id="alamat_domisili" name="alamat_domisili" rows="3">{{ old('alamat_domisili', $employee->alamat_domisili ?? '') }}</textarea>
                                        @error('alamat_domisili')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email', $employee->email ?? '') }}">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="no_hp" class="form-label">No HP</label>
                                        <input type="text" class="form-control @error('no_hp') is-invalid @enderror"
                                            id="no_hp" name="no_hp" value="{{ old('no_hp', $employee->no_hp ?? '') }}">
                                        @error('no_hp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="nomor_kontak_darurat" class="form-label">NOMOR KONTAK DARURAT</label>
                                        <input type="text" class="form-control @error('nomor_kontak_darurat') is-invalid @enderror"
                                            id="nomor_kontak_darurat" name="nomor_kontak_darurat" value="{{ old('nomor_kontak_darurat', $employee->nomor_kontak_darurat ?? '') }}">
                                        @error('nomor_kontak_darurat')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="agama" class="form-label">AGAMA</label>
                                        <input type="text" class="form-control @error('agama') is-invalid @enderror"
                                            id="agama" name="agama" value="{{ old('agama', $employee->agama ?? '') }}">
                                        @error('agama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="pendidikan" class="form-label">PENDIDIKAN</label>
                                        <input type="text" class="form-control @error('pendidikan') is-invalid @enderror"
                                            id="pendidikan" name="pendidikan" value="{{ old('pendidikan', $employee->pendidikan ?? '') }}">
                                        @error('pendidikan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="jurusan" class="form-label">JURUSAN</label>
                                        <input type="text" class="form-control @error('jurusan') is-invalid @enderror"
                                            id="jurusan" name="jurusan" value="{{ old('jurusan', $employee->jurusan ?? '') }}">
                                        @error('jurusan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('hr.employee-data.index') }}" class="btn btn-secondary">
                                                <i class="mdi mdi-arrow-left me-2"></i>Kembali
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="mdi mdi-content-save me-2"></i>Simpan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script>
        // Preview image
        function previewImage(input) {
            const previewContainer = document.getElementById('preview-container');
            const preview = document.getElementById('preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    if (previewContainer) {
                        previewContainer.style.display = 'block';
                    }
                }

                reader.readAsDataURL(input.files[0]);
            } else {
                if (previewContainer) {
                    previewContainer.style.display = 'none';
                }
            }
        }

        // Calculate usia from tgl_lahir
        function calculateUsia() {
            const tglLahir = document.getElementById('tgl_lahir').value;
            if (tglLahir) {
                const today = new Date();
                const birthDate = new Date(tglLahir);
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                document.getElementById('usia').value = age;
            } else {
                document.getElementById('usia').value = '';
            }
        }

        // Calculate masa kerja from tgl_masuk
        document.getElementById('tgl_masuk').addEventListener('change', function() {
            const tglMasuk = this.value;
            if (tglMasuk) {
                const today = new Date();
                const masukDate = new Date(tglMasuk);
                let masaKerja = today.getFullYear() - masukDate.getFullYear();
                const monthDiff = today.getMonth() - masukDate.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < masukDate.getDate())) {
                    masaKerja--;
                }
                document.getElementById('masa_kerja').value = masaKerja + ' tahun';
            } else {
                document.getElementById('masa_kerja').value = '';
            }
        });

        // Form submission with AJAX
        document.getElementById('employeeForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

            fetch(form.action, {
                method: form.method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(async response => {
                if (response.ok) {
                    const data = await response.json();
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message || 'Data karyawan berhasil disimpan',
                        icon: 'success',
                        confirmButtonText: 'OK',
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = "{{ route('hr.employee-data.index') }}";
                    });
                    return;
                }

                if (response.status === 422) {
                    const data = await response.json();
                    let errors = '';
                    if (data.errors) {
                        Object.values(data.errors).forEach((msgs) => {
                            errors += msgs.join('<br>') + '<br>';
                        });
                    }
                    Swal.fire({
                        title: 'Validasi Error',
                        html: errors || data.message || 'Terjadi kesalahan validasi',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                const data = await response.json().catch(() => ({}));
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Terjadi kesalahan saat menyimpan data.',
                    confirmButtonText: 'OK'
                });
            })
            .catch(error => {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat menyimpan data.',
                    confirmButtonText: 'OK'
                });
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    </script>
@endsection

