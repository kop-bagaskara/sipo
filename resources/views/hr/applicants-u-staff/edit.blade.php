@extends('main.layouts.main')

@section('title')
    Edit Data Pelamar Kerja
@endsection

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .nav-tabs {
            display: flex;
            width: 100%;
            border-bottom: 1px solid #dee2e6;
        }
        .nav-tabs .nav-item {
            flex: 1;
            text-align: center;
        }
        .nav-tabs .nav-link {
            border-radius: 0;
            font-weight: 500;
            width: 100%;
            border: 1px solid #dee2e6;
            border-bottom: none;
            padding: 12px 8px;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .nav-tabs .nav-link.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        .nav-tabs .nav-link:hover:not(.active) {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
        .tab-content {
            border: 1px solid #dee2e6;
            border-top: none;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .form-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .section-title {
            color: #007bff;
            border-bottom: 2px solid #007bff;
            padding-bottom: 8px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .tab-saved {
            background-color: #28a745 !important;
            color: white !important;
        }
        .signature-pad {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            background: white;
            cursor: crosshair;
        }
        .signature-pad canvas {
            border-radius: 6px;
        }
        .signature-controls {
            margin-top: 10px;
        }
        .signature-preview {
            max-width: 300px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
            background: white;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="mdi mdi-account-edit"></i> Edit Data Pelamar Kerja
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('public.staff-applicant.index') }}" class="btn btn-secondary btn-sm">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="applicantForm" method="POST" action="{{ route('public.staff-applicant.update', $applicant) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Hidden fields for tab management -->
                            <input type="hidden" id="currentTab" name="current_tab" value="posisi">
                            <input type="hidden" id="savedTabs" name="saved_tabs" value="">
                            <input type="hidden" id="applicantId" name="applicant_id" value="{{ $applicant->id }}">

                            <!-- Tab Navigation -->
                            <ul class="nav nav-tabs" id="applicantTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="posisi-tab" data-toggle="tab" data-target="#posisi" type="button" role="tab">
                                        Posisi
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="data-diri-tab" data-toggle="tab" data-target="#data-diri" type="button" role="tab">
                                        Data Diri
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pendidikan-tab" data-toggle="tab" data-target="#pendidikan" type="button" role="tab">
                                        Pendidikan
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="kursus-tab" data-toggle="tab" data-target="#kursus" type="button" role="tab">
                                        Kursus
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="pengalaman-tab" data-toggle="tab" data-target="#pengalaman" type="button" role="tab">
                                        Pengalaman
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="keluarga-tab" data-toggle="tab" data-target="#keluarga" type="button" role="tab">
                                        Keluarga
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="kemampuan-tab" data-toggle="tab" data-target="#kemampuan" type="button" role="tab">
                                        Kemampuan
                                    </button>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content" id="applicantTabContent">
                                <!-- Tab Posisi -->
                                <div class="tab-pane fade show active" id="posisi" role="tabpanel">
                                    <div class="form-section">
                                        <h5 class="section-title">Posisi & Jabatan</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="posisi_dilamar">Posisi yang Dilamar <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="posisi_dilamar" name="posisi_dilamar"
                                                           value="{{ old('posisi_dilamar', $applicant->posisi_dilamar) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="gaji_terakhir">Gaji Terakhir</label>
                                                    <input type="text" class="form-control" id="gaji_terakhir" name="gaji_terakhir"
                                                           value="{{ old('gaji_terakhir', $applicant->gaji_terakhir) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mulai_kerja">Mulai Kerja</label>
                                                    <input type="date" class="form-control" id="mulai_kerja" name="mulai_kerja"
                                                           value="{{ old('mulai_kerja', $applicant->mulai_kerja ? $applicant->mulai_kerja->format('Y-m-d') : '') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab Data Diri -->
                                <div class="tab-pane fade" id="data-diri" role="tabpanel">
                                    <div class="form-section">
                                        <h5 class="section-title">Data Pribadi</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="nama_lengkap">Nama Lengkap <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                                                           value="{{ old('nama_lengkap', $applicant->nama_lengkap) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="alias">Alias</label>
                                                    <input type="text" class="form-control" id="alias" name="alias"
                                                           value="{{ old('alias', $applicant->alias) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                                                        <option value="">Pilih Jenis Kelamin</option>
                                                        <option value="L" {{ old('jenis_kelamin', $applicant->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                                        <option value="P" {{ old('jenis_kelamin', $applicant->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir"
                                                           value="{{ old('tanggal_lahir', $applicant->tanggal_lahir ? $applicant->tanggal_lahir->format('Y-m-d') : '') }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tempat_lahir">Tempat Lahir</label>
                                                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir"
                                                           value="{{ old('tempat_lahir', $applicant->tempat_lahir) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="agama">Agama</label>
                                                    <input type="text" class="form-control" id="agama" name="agama"
                                                           value="{{ old('agama', $applicant->agama) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="kebangsaan">Kebangsaan</label>
                                                    <input type="text" class="form-control" id="kebangsaan" name="kebangsaan"
                                                           value="{{ old('kebangsaan', $applicant->kebangsaan) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="no_ktp">No. KTP</label>
                                                    <input type="text" class="form-control" id="no_ktp" name="no_ktp"
                                                           value="{{ old('no_ktp', $applicant->no_ktp) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-section">
                                        <h5 class="section-title">Alamat</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="alamat_ktp">Alamat KTP</label>
                                                    <textarea class="form-control" id="alamat_ktp" name="alamat_ktp" rows="3">{{ old('alamat_ktp', $applicant->alamat_ktp) }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="alamat_domisili">Alamat Domisili</label>
                                                    <textarea class="form-control" id="alamat_domisili" name="alamat_domisili" rows="3">{{ old('alamat_domisili', $applicant->alamat_domisili) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="kode_pos_ktp">Kode Pos KTP</label>
                                                    <input type="text" class="form-control" id="kode_pos_ktp" name="kode_pos_ktp"
                                                           value="{{ old('kode_pos_ktp', $applicant->kode_pos_ktp) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="kode_pos_domisili">Kode Pos Domisili</label>
                                                    <input type="text" class="form-control" id="kode_pos_domisili" name="kode_pos_domisili"
                                                           value="{{ old('kode_pos_domisili', $applicant->kode_pos_domisili) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-section">
                                        <h5 class="section-title">Kontak</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="no_handphone">No. Handphone <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="no_handphone" name="no_handphone"
                                                           value="{{ old('no_handphone', $applicant->no_handphone) }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email">Email <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" id="email" name="email"
                                                           value="{{ old('email', $applicant->email) }}" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="no_npwp">No. NPWP</label>
                                                    <input type="text" class="form-control" id="no_npwp" name="no_npwp"
                                                           value="{{ old('no_npwp', $applicant->no_npwp) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="bpjs_kesehatan">BPJS Kesehatan</label>
                                                    <input type="text" class="form-control" id="bpjs_kesehatan" name="bpjs_kesehatan"
                                                           value="{{ old('bpjs_kesehatan', $applicant->bpjs_kesehatan) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="kontak_darurat">Kontak Darurat</label>
                                                    <input type="text" class="form-control" id="kontak_darurat" name="kontak_darurat"
                                                           value="{{ old('kontak_darurat', $applicant->kontak_darurat) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="hubungan_kontak_darurat">Hubungan Kontak Darurat</label>
                                                    <input type="text" class="form-control" id="hubungan_kontak_darurat" name="hubungan_kontak_darurat"
                                                           value="{{ old('hubungan_kontak_darurat', $applicant->hubungan_kontak_darurat) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab Kemampuan -->
                                <div class="tab-pane fade" id="kemampuan" role="tabpanel">
                                    <div class="form-section">
                                        <h5 class="section-title">Kemampuan & Hobi</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="hobby">Hobby</label>
                                                    <input type="text" class="form-control" id="hobby" name="hobby"
                                                           value="{{ old('hobby', $applicant->hobby) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="lain_lain">Lain-lain</label>
                                                    <input type="text" class="form-control" id="lain_lain" name="lain_lain"
                                                           value="{{ old('lain_lain', $applicant->lain_lain) }}">
                                                </div>
                                            </div>
                                        </div>

                                        <h5 class="section-title mt-4">Deklarasi & Tanda Tangan</h5>
                                        <div class="form-group">
                                            <div class="alert alert-info">
                                                <strong>Deklarasi:</strong><br>
                                                Dengan ini saya menyatakan bahwa, keterangan yang saya berikan diatas adalah benar.
                                                Bilamana dikemudian hari keterangan tersebut ternyata tidak benar / palsu,
                                                maka perusahaan berhak untuk memberhentikan saya dengan segera tanpa syarat apapun.
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="tanggal_deklarasi">Surabaya, Tanggal</label>
                                                    <input type="date" class="form-control" id="tanggal_deklarasi" name="tanggal_deklarasi"
                                                           value="{{ old('tanggal_deklarasi', $applicant->tanggal_deklarasi ? $applicant->tanggal_deklarasi->format('Y-m-d') : '') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="ttd_pelamar">Tanda Tangan Pelamar</label>
                                                    <input type="text" class="form-control" id="ttd_pelamar" name="ttd_pelamar"
                                                           value="{{ old('ttd_pelamar', $applicant->ttd_pelamar) }}" placeholder="Nama Terang & Tanda Tangan">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Signature Pad -->
                                        <div class="form-group">
                                            <label>Tanda Tangan Digital</label>
                                            <div class="signature-pad" id="signaturePad">
                                                <canvas id="signatureCanvas" width="400" height="200"></canvas>
                                            </div>
                                            <div class="signature-controls">
                                                <button type="button" class="btn btn-sm btn-secondary" id="clearSignature">Clear</button>
                                                <button type="button" class="btn btn-sm btn-primary" id="saveSignature">Save Signature</button>
                                            </div>
                                            <input type="hidden" id="ttd_signature_image" name="ttd_signature_image">

                                            @if($applicant->ttd_signature)
                                                <div class="mt-3">
                                                    <label>Signature Saat Ini:</label>
                                                    <div class="signature-preview">
                                                        <img src="{{ Storage::url($applicant->ttd_signature) }}" alt="Current Signature" style="max-width: 100%;">
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Actions -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="form-group text-right">
                                        <a href="{{ route('public.staff-applicant.index') }}" class="btn btn-secondary">
                                            <i class="mdi mdi-arrow-left"></i> Kembali
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="mdi mdi-content-save"></i> Update Data
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
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Initialize signature pad
            const canvas = document.getElementById('signatureCanvas');
            const ctx = canvas.getContext('2d');
            let isDrawing = false;
            let lastX = 0;
            let lastY = 0;

            // Set canvas background
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Mouse events
            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseout', stopDrawing);

            // Touch events for mobile
            canvas.addEventListener('touchstart', handleTouch);
            canvas.addEventListener('touchmove', handleTouch);
            canvas.addEventListener('touchend', stopDrawing);

            function startDrawing(e) {
                isDrawing = true;
                const rect = canvas.getBoundingClientRect();
                lastX = e.clientX - rect.left;
                lastY = e.clientY - rect.top;
            }

            function draw(e) {
                if (!isDrawing) return;

                const rect = canvas.getBoundingClientRect();
                const currentX = e.clientX - rect.left;
                const currentY = e.clientY - rect.top;

                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(currentX, currentY);
                ctx.strokeStyle = '#000000';
                ctx.lineWidth = 2;
                ctx.lineCap = 'round';
                ctx.stroke();

                lastX = currentX;
                lastY = currentY;
            }

            function stopDrawing() {
                isDrawing = false;
            }

            function handleTouch(e) {
                e.preventDefault();
                const touch = e.touches[0];
                const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' :
                                                 e.type === 'touchmove' ? 'mousemove' : 'mouseup', {
                    clientX: touch.clientX,
                    clientY: touch.clientY
                });
                canvas.dispatchEvent(mouseEvent);
            }

            // Clear signature
            $('#clearSignature').click(function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                $('#ttd_signature_image').val('');
            });

            // Save signature
            $('#saveSignature').click(function() {
                const dataURL = canvas.toDataURL('image/png');
                $('#ttd_signature_image').val(dataURL);

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Tanda tangan berhasil disimpan',
                    timer: 2000,
                    showConfirmButton: false
                });
            });

            // Form submission
            $('#applicantForm').on('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Menyimpan...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                const formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.close();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data pelamar berhasil diperbarui',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = '{{ route("public.staff-applicant.show", $applicant) }}';
                        });
                    },
                    error: function(xhr) {
                        Swal.close();
                        let errorMessage = 'Terjadi kesalahan saat menyimpan data';

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            const errorList = [];
                            for (const field in errors) {
                                errorList.push(field + ': ' + errors[field][0]);
                            }
                            errorMessage = 'Validasi gagal:\n' + errorList.join('\n');
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>
@endsection
