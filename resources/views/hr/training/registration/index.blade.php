@extends('main.layouts.main')
@section('title')
    Registrasi Training
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

    <style>
        .cust-col { white-space: nowrap; }
        .stat-card {
            border: 0;
            border-radius: .85rem;
            color: #fff;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,.08);
            transition: transform .2s ease, box-shadow .2s ease;
            background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
            position: relative;
        }
        .stat-card .stat-body { padding: 1.25rem 1.25rem 1rem 1.25rem; }
        .stat-card .stat-icon {
            position: absolute; right: 12px; bottom: 8px; font-size: 2.5rem; opacity: .2;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 14px 26px rgba(0,0,0,.12); }
        .bg-grad-primary { background: linear-gradient(135deg, #0061f2 0%, #60a5fa 100%); }
        .bg-grad-success { background: linear-gradient(135deg, #16a34a 0%, #34d399 100%); }
        .bg-grad-warning { background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%); }
        .bg-grad-danger  { background: linear-gradient(135deg, #dc2626 0%, #f87171 100%); }
        .stat-link { display:flex; align-items:center; justify-content:space-between; padding:.6rem 1.25rem; background:rgba(255,255,255,.08); color:#fff; text-decoration:none; }
        .stat-link:hover { background:rgba(255,255,255,.14); color:#fff; text-decoration:none; }
        .stat-value { font-size:2.1rem; font-weight:700; margin:0; }
        .stat-label { margin:0; opacity:.9; letter-spacing:.3px; }
    </style>
@endsection
@section('page-title')
    Registrasi Training
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Registrasi Training</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">Registrasi Training</li>
                </ol>
            </div>
        </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="mdi mdi-user-graduate mr-2"></i>
                        Registrasi Training
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('hr.training.registration.history') }}" class="btn btn-info btn-sm">
                            <i class="mdi mdi-history mr-1"></i>
                            Riwayat Training
                        </a>
                        <a href="{{ route('hr.training.registration.statistics') }}" class="btn btn-success btn-sm">
                            <i class="mdi mdi-chart-bar mr-1"></i>
                            Statistik
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('hr.training.registration.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="department_id">Departemen</label>
                                    <select name="department_id" id="department_id" class="form-control">
                                        <option value="">Semua Departemen</option>
                                        @foreach(\App\Models\Divisi::all() as $department)
                                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->divisi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="training_type">Tipe Training</label>
                                    <select name="training_type" id="training_type" class="form-control">
                                        <option value="">Semua Tipe</option>
                                        <option value="mandatory" {{ request('training_type') == 'mandatory' ? 'selected' : '' }}>Mandatory</option>
                                        <option value="optional" {{ request('training_type') == 'optional' ? 'selected' : '' }}>Optional</option>
                                        <option value="certification" {{ request('training_type') == 'certification' ? 'selected' : '' }}>Certification</option>
                                        <option value="skill_development" {{ request('training_type') == 'skill_development' ? 'selected' : '' }}>Skill Development</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="search">Pencarian</label>
                                    <input type="text" name="search" id="search" class="form-control" 
                                           value="{{ request('search') }}" placeholder="Nama atau kode training">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-search mr-1"></i>
                                    Filter
                                </button>
                                <a href="{{ route('hr.training.registration.index') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-times mr-1"></i>
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Available Trainings -->
                    <div class="row">
                        @forelse($trainings as $training)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="card-title mb-0">{{ $training->training_name }}</h6>
                                        <span class="badge badge-info">{{ $training->training_code }}</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="mdi mdi-clock mr-1"></i>
                                                {{ $training->duration_hours }} jam
                                            </small>
                                        </div>
                                        
                                        @if($training->description)
                                            <p class="card-text text-muted small">
                                                {{ Str::limit($training->description, 100) }}
                                            </p>
                                        @endif

                                        <div class="mb-2">
                                            @switch($training->training_type)
                                                @case('mandatory')
                                                    <span class="badge badge-danger">Mandatory</span>
                                                    @break
                                                @case('optional')
                                                    <span class="badge badge-success">Optional</span>
                                                    @break
                                                @case('certification')
                                                    <span class="badge badge-warning">Certification</span>
                                                    @break
                                                @case('skill_development')
                                                    <span class="badge badge-primary">Skill Development</span>
                                                    @break
                                            @endswitch
                                            
                                            @switch($training->training_method)
                                                @case('classroom')
                                                    <span class="badge badge-secondary">Kelas</span>
                                                    @break
                                                @case('online')
                                                    <span class="badge badge-info">Online</span>
                                                    @break
                                                @case('hybrid')
                                                    <span class="badge badge-warning">Hybrid</span>
                                                    @break
                                                @case('workshop')
                                                    <span class="badge badge-success">Workshop</span>
                                                    @break
                                                @case('seminar')
                                                    <span class="badge badge-primary">Seminar</span>
                                                    @break
                                            @endswitch
                                        </div>

                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="mdi mdi-users mr-1"></i>
                                                Peserta: {{ $training->participants_count }}
                                                @if($training->max_participants)
                                                    / {{ $training->max_participants }}
                                                @endif
                                            </small>
                                        </div>

                                        @if($training->instructor_name)
                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <i class="mdi mdi-chalkboard-teacher mr-1"></i>
                                                    {{ $training->instructor_name }}
                                                </small>
                                            </div>
                                        @endif

                                        @if($training->cost_per_participant)
                                            <div class="mb-2">
                                                <small class="text-muted">
                                                    <i class="mdi mdi-dollar-sign mr-1"></i>
                                                    Rp {{ number_format($training->cost_per_participant, 0, ',', '.') }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-footer">
                                        @php
                                            $existingParticipant = $training->participants->first();
                                        @endphp
                                        
                                        @if($existingParticipant)
                                            @switch($existingParticipant->registration_status)
                                                @case('registered')
                                                    <span class="badge badge-warning">Menunggu Persetujuan</span>
                                                    @break
                                                @case('approved')
                                                    <span class="badge badge-success">Disetujui</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="badge badge-danger">Ditolak</span>
                                                    @break
                                                @case('attended')
                                                    <span class="badge badge-info">Sedang Berlangsung</span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge badge-success">Selesai</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge badge-secondary">Dibatalkan</span>
                                                    @break
                                            @endswitch
                                        @else
                                            <a href="{{ route('hr.training.registration.show', $training->id) }}" 
                                               class="btn btn-primary btn-sm btn-block">
                                                <i class="mdi mdi-user-plus mr-1"></i>
                                                Daftar Training
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <i class="mdi mdi-graduation-cap fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Tidak ada training yang tersedia</h5>
                                    <p class="text-muted">Belum ada training yang dapat Anda daftarkan saat ini.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($trainings->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $trainings->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Trainings -->
    @if($userTrainings->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="mdi mdi-history mr-2"></i>
                            Training Terbaru Anda
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Training</th>
                                        <th>Status</th>
                                        <th>Tanggal Daftar</th>
                                        <th>Tanggal Update</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($userTrainings as $participant)
                                        <tr>
                                            <td>
                                                <strong>{{ $participant->training->training_name }}</strong>
                                                <br><small class="text-muted">{{ $participant->training->training_code }}</small>
                                            </td>
                                            <td>
                                                @switch($participant->registration_status)
                                                    @case('registered')
                                                        <span class="badge badge-warning">Menunggu Persetujuan</span>
                                                        @break
                                                    @case('approved')
                                                        <span class="badge badge-success">Disetujui</span>
                                                        @break
                                                    @case('rejected')
                                                        <span class="badge badge-danger">Ditolak</span>
                                                        @break
                                                    @case('attended')
                                                        <span class="badge badge-info">Sedang Berlangsung</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge badge-success">Selesai</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge badge-secondary">Dibatalkan</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>{{ $participant->registered_at ? $participant->registered_at->format('d/m/Y H:i') : '-' }}</td>
                                            <td>{{ $participant->updated_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('hr.training.registration.show', $participant->training->id) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
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
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form on filter change
    $('#department_id, #training_type').change(function() {
        $(this).closest('form').submit();
    });
});
</script>
@endpush
