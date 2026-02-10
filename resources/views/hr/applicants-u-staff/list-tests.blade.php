@extends('main.layouts.main')

@section('title')
    Daftar Test - {{ $applicant->nama_lengkap }}
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection

@section('page-title')
    Daftar Test
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Daftar Test</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('public.staff-applicant.index') }}">HR</a></li>
                <li class="breadcrumb-item"><a href="{{ route('public.staff-applicant.index') }}">Data Pelamar</a></li>
                <li class="breadcrumb-item active">Daftar Test</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">Test untuk {{ $applicant->nama_lengkap }}</h4>
                            <p class="text-muted mb-0">{{ $applicant->posisi_dilamar }}</p>
                        </div>
                        <div class="col d-flex justify-content-end">
                            <a href="{{ route('public.staff-applicant.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Progress Overview -->
                    @php
                        $completedTests = $applicant->testResults->count();
                        $totalTests = 2; // Staff level hanya 2 test (matematika & buta warna)
                        $percentage = ($completedTests / $totalTests) * 100;
                    @endphp
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5 class="card-title">Progress Test</h5>
                                    <div class="progress mb-2" style="height: 30px;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                             role="progressbar"
                                             style="width: {{ $percentage }}%"
                                             aria-valuenow="{{ $completedTests }}"
                                             aria-valuemin="0"
                                             aria-valuemax="{{ $totalTests }}">
                                            {{ $completedTests }}/{{ $totalTests }} Test Selesai
                                        </div>
                                    </div>
                                    <p class="text-muted mb-0">
                                        <strong>{{ $completedTests }}</strong> dari <strong>{{ $totalTests }}</strong> test telah dikerjakan
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Test List -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Test</th>
                                    <th style="width: 120px;">Status</th>
                                    <th style="width: 120px;">Skor</th>
                                    <th style="width: 180px;">Tanggal</th>
                                    <th style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $testIndex = 0;
                                @endphp
                                @foreach($testTypes as $testType => $testName)
                                    @php
                                        $hasTest = $applicant->testResults->where('test_type', $testType)->first();
                                        $testResult = $hasTest;
                                        $isCompleted = $hasTest !== null;
                                        $testIndex++;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $testIndex }}</td>
                                        <td>
                                            <strong>
                                                <i class="mdi {{ $isCompleted ? 'mdi-check-circle text-success' : 'mdi-circle-outline text-muted' }}"></i>
                                                {{ $testName }}
                                            </strong>
                                        </td>
                                        <td class="text-center">
                                            @if($isCompleted)
                                                <span class="badge badge-success">
                                                    <i class="mdi mdi-check"></i> Selesai
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">
                                                    Belum Dikerjakan
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($isCompleted)
                                                <span class="badge badge-{{ $testResult->score_percentage >= 80 ? 'success' : ($testResult->score_percentage >= 60 ? 'warning' : 'danger') }}" style="font-size: 14px; padding: 8px 12px;">
                                                    {{ $testResult->score }}/{{ $testResult->max_score }}
                                                    <br>
                                                    <small>({{ $testResult->score_percentage }}%)</small>
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isCompleted && $testResult->test_date)
                                                <small>
                                                    <i class="mdi mdi-calendar"></i>
                                                    {{ $testResult->test_date->format('d/m/Y H:i') }}
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isCompleted)
                                                <button type="button"
                                                        class="btn btn-primary btn-sm"
                                                        disabled
                                                        title="Test sudah selesai dikerjakan">
                                                    <i class="mdi mdi-play"></i> Mulai Test
                                                </button>
                                            @else
                                                <a href="{{ route('public.staff-applicant.test.start', [$applicant, $testType]) }}"
                                                   class="btn btn-primary btn-sm">
                                                    <i class="mdi mdi-play"></i> Mulai Test
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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

            // SweetAlert untuk info message
            @if(session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Info',
                    text: '{{ session('info') }}',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
            @endif
        });
    </script>
@endsection

