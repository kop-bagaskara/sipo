@extends('main.layouts.main')

@section('title')
    Detail Pelamar - {{ $applicant->nama_lengkap }}
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .info-card {
            margin-bottom: 20px;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            margin-bottom: 5px;
        }
        .info-value {
            color: #333;
            margin-bottom: 15px;
        }
        .test-result-card {
            border-left: 4px solid #4ecdc4;
            margin-bottom: 15px;
        }
        .test-result-card.success {
            border-left-color: #28a745;
        }
        .test-result-card.warning {
            border-left-color: #ffc107;
        }
        .test-result-card.danger {
            border-left-color: #dc3545;
        }
        .profile-photo {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            border: 3px solid #4ecdc4;
        }
    </style>
@endsection

@section('page-title')
    Detail Pelamar
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Detail Pelamar</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item"><a href="{{ route('public.staff-applicant.index') }}">Data Pelamar</a></li>
                <li class="breadcrumb-item active">Detail Pelamar</li>
            </ol>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Action Buttons -->
        <div class="col-12 mb-3">
            <div class="d-flex justify-content-between">
                <a href="{{ route('public.staff-applicant.index') }}" class="btn btn-secondary">
                    <i class="mdi mdi-arrow-left"></i> Kembali
                </a>
                <div>
                    <a href="{{ route('public.staff-applicant.edit', $applicant) }}" class="btn btn-info">
                        <i class="mdi mdi-pencil"></i> Edit
                    </a>
                    <a href="{{ route('public.staff-applicant.tests', $applicant) }}" class="btn btn-success">
                        <i class="mdi mdi-clipboard-text"></i> Test
                    </a>
                    <a href="{{ route('public.staff-applicant.test.results', $applicant) }}" class="btn btn-primary">
                        <i class="mdi mdi-chart-line"></i> Hasil Test
                    </a>
                </div>
            </div>
        </div>

        <!-- Profile Section -->
        <div class="col-md-4">
            <div class="card info-card">
                <div class="card-body text-center">
                    @if($applicant->foto)
                        <img src="{{ asset('sipo_krisan/storage/' . $applicant->foto) }}"
                             alt="Foto"
                             class="profile-photo mb-3"
                             onerror="this.src='{{ asset('sipo_krisan/public/assets/images/default-avatar.png') }}'; this.onerror=null;">
                    @else
                        <img src="{{ asset('sipo_krisan/public/assets/images/default-avatar.png') }}"
                             alt="Foto"
                             class="profile-photo mb-3">
                    @endif

                    <h4 class="mb-1">{{ $applicant->nama_lengkap }}</h4>
                    <p class="text-muted mb-2">{{ $applicant->posisi_dilamar }}</p>

                    <span class="badge badge-{{ $applicant->status == 'accepted' ? 'success' : ($applicant->status == 'rejected' ? 'danger' : 'info') }} badge-lg">
                        {{ $applicant->status_formatted }}
                    </span>

                    <hr>

                    <div class="text-left">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $applicant->email ?? '-' }}</div>

                        <div class="info-label">No. Handphone</div>
                        <div class="info-value">{{ $applicant->no_handphone ?? '-' }}</div>

                        <div class="info-label">Tanggal Melamar</div>
                        <div class="info-value">{{ $applicant->tanggal_melamar ? $applicant->tanggal_melamar->format('d/m/Y') : '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Files Section -->
            <div class="card info-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Dokumen</h5>
                </div>
                <div class="card-body">
                    @if($applicant->cv_file)
                        <a href="{{ asset('sipo_krisan/storage/' . $applicant->cv_file) }}"
                           target="_blank"
                           class="btn btn-info btn-block mb-2">
                            <i class="mdi mdi-file-document"></i> Download CV
                        </a>
                    @else
                        <button class="btn btn-secondary btn-block mb-2" disabled>
                            <i class="mdi mdi-file-document"></i> CV Tidak Tersedia
                        </button>
                    @endif

                    @if($applicant->ttd_signature)
                        <a href="{{ asset('sipo_krisan/storage/' . $applicant->ttd_signature) }}"
                           target="_blank"
                           class="btn btn-info btn-block">
                            <i class="mdi mdi-pen"></i> Lihat Tanda Tangan
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Personal Information -->
            <div class="card info-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Pribadi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-label">Nama Lengkap</div>
                            <div class="info-value">{{ $applicant->nama_lengkap ?? '-' }}</div>

                            <div class="info-label">Alias</div>
                            <div class="info-value">{{ $applicant->alias ?? '-' }}</div>

                            <div class="info-label">Jenis Kelamin</div>
                            <div class="info-value">{{ $applicant->jenis_kelamin_formatted ?? '-' }}</div>

                            <div class="info-label">Tempat, Tanggal Lahir</div>
                            <div class="info-value">
                                {{ $applicant->tempat_lahir ?? '-' }},
                                {{ $applicant->tanggal_lahir ? $applicant->tanggal_lahir->format('d/m/Y') : '-' }}
                            </div>

                            <div class="info-label">Agama</div>
                            <div class="info-value">{{ $applicant->agama ?? '-' }}</div>

                            <div class="info-label">Kebangsaan</div>
                            <div class="info-value">{{ $applicant->kebangsaan ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">No. KTP</div>
                            <div class="info-value">{{ $applicant->no_ktp ?? '-' }}</div>

                            <div class="info-label">No. NPWP</div>
                            <div class="info-value">{{ $applicant->no_npwp ?? '-' }}</div>

                            <div class="info-label">No. Handphone</div>
                            <div class="info-value">{{ $applicant->no_handphone ?? '-' }}</div>

                            <div class="info-label">Email</div>
                            <div class="info-value">{{ $applicant->email ?? '-' }}</div>

                            <div class="info-label">BPJS Kesehatan</div>
                            <div class="info-value">{{ $applicant->bpjs_kesehatan ?? '-' }}</div>

                            <div class="info-label">Kontak Darurat</div>
                            <div class="info-value">
                                {{ $applicant->kontak_darurat ?? '-' }}
                                @if($applicant->hubungan_kontak_darurat)
                                    ({{ $applicant->hubungan_kontak_darurat }})
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="card info-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Alamat</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-label">Alamat KTP</div>
                            <div class="info-value">{{ $applicant->alamat_ktp ?? '-' }}</div>
                            <div class="info-label">Kode Pos KTP</div>
                            <div class="info-value">{{ $applicant->kode_pos_ktp ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Alamat Domisili</div>
                            <div class="info-value">{{ $applicant->alamat_domisili ?? '-' }}</div>
                            <div class="info-label">Kode Pos Domisili</div>
                            <div class="info-value">{{ $applicant->kode_pos_domisili ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Position Information -->
            <div class="card info-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Posisi & Jabatan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-label">Posisi Dilamar</div>
                            <div class="info-value">{{ $applicant->posisi_dilamar ?? '-' }}</div>

                            <div class="info-label">Gaji Terakhir</div>
                            <div class="info-value">{{ $applicant->gaji_terakhir }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Mulai Kerja</div>
                            <div class="info-value">{{ $applicant->mulai_kerja ? $applicant->mulai_kerja->format('d/m/Y') : '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

