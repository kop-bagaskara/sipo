@extends('main.layouts.main')

@section('title')
    Dashboard HRD - Data Pelamar
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .stat-card {
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .dashboard-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
    </style>
@endsection

@section('page-title')
    Dashboard HRD - Data Pelamar
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Dashboard HRD - Data Pelamar</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item active">Dashboard Pelamar</li>
            </ol>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card bg-light text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title text-dark">{{ $totalApplicants ?? 0 }}</h4>
                            <p class="card-text">Total Pelamar</p>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-account-multiple dashboard-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card stat-card bg-light text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title text-dark">{{ $newApplicantsThisMonth ?? 0 }}</h4>
                            <p class="card-text">Pelamar Bulan Ini</p>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-account-plus dashboard-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card stat-card bg-light text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title text-dark">{{ $applicantsInProcess ?? 0 }}</h4>
                            <p class="card-text">Sedang Proses</p>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-clock-outline dashboard-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card stat-card bg-light text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title text-dark">{{ $completedTests ?? 0 }}</h4>
                            <p class="card-text">Test Selesai</p>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-check-circle dashboard-icon"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Aksi Cepat</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('hr.applicants.create') }}" class="btn btn-success btn-lg btn-block">
                                <i class="mdi mdi-plus-circle mr-2"></i>
                                Tambah Data Pelamar Baru
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('hr.applicants.index') }}" class="btn btn-primary btn-lg btn-block">
                                <i class="mdi mdi-format-list-bulleted mr-2"></i>
                                Lihat Daftar Pelamar
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('hr.applicants.index', ['status' => 'test']) }}" class="btn btn-warning btn-lg btn-block">
                                <i class="mdi mdi-test-tube mr-2"></i>
                                Pelamar Sedang Test
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Applicants -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Pelamar Terbaru</h4>
                </div>
                <div class="card-body">
                    @if(isset($recentApplicants) && $recentApplicants->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Posisi</th>
                                        <th>Status</th>
                                        <th>Tanggal Melamar</th>
                                        <th>Progress Test</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentApplicants as $applicant)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($applicant->foto)
                                                        <img src="{{ Storage::url($applicant->foto) }}" alt="Foto" class="rounded-circle me-2" width="40" height="40">
                                                    @else
                                                        <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <i class="mdi mdi-account text-white"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $applicant->nama_lengkap }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $applicant->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $applicant->posisi_dilamar }}</td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'secondary',
                                                        'test' => 'warning',
                                                        'interview' => 'info',
                                                        'accepted' => 'success',
                                                        'rejected' => 'danger'
                                                    ];
                                                @endphp
                                                <span class="badge badge-{{ $statusColors[$applicant->status] ?? 'secondary' }}">
                                                    {{ ucfirst($applicant->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $applicant->tanggal_melamar->format('d/m/Y') }}</td>
                                            <td>
                                                @php
                                                    $completedTests = $applicant->testResults->count();
                                                    $totalTests = 5;
                                                    $percentage = ($completedTests / $totalTests) * 100;
                                                @endphp
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%">
                                                        {{ $completedTests }}/{{ $totalTests }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('hr.applicants.show', $applicant) }}" class="btn btn-info btn-sm">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="mdi mdi-information-outline" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="text-muted mt-2">Belum ada data pelamar</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // SweetAlert untuk success message
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end'
                });
            @endif

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
        });
    </script>
@endsection
