@extends('main.layouts.app')

@section('title', 'Detail Training')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-graduation-cap mr-2"></i>
                        {{ $training->training_name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('hr.training.registration.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Training Details -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Detail Training</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless table-sm">
                                                <tr>
                                                    <td><strong>Kode Training:</strong></td>
                                                    <td><span class="badge badge-info">{{ $training->training_code }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Tipe Training:</strong></td>
                                                    <td>
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
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Metode Training:</strong></td>
                                                    <td>
                                                        @switch($training->training_method)
                                                            @case('classroom')
                                                                <i class="fas fa-chalkboard-teacher"></i> Kelas
                                                                @break
                                                            @case('online')
                                                                <i class="fas fa-laptop"></i> Online
                                                                @break
                                                            @case('hybrid')
                                                                <i class="fas fa-users"></i> Hybrid
                                                                @break
                                                            @case('workshop')
                                                                <i class="fas fa-tools"></i> Workshop
                                                                @break
                                                            @case('seminar')
                                                                <i class="fas fa-microphone"></i> Seminar
                                                                @break
                                                        @endswitch
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Durasi:</strong></td>
                                                    <td>{{ $training->duration_hours }} jam</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless table-sm">
                                                <tr>
                                                    <td><strong>Maksimal Peserta:</strong></td>
                                                    <td>{{ $training->max_participants ?? 'Tidak terbatas' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Minimal Peserta:</strong></td>
                                                    <td>{{ $training->min_participants }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Biaya per Peserta:</strong></td>
                                                    <td>
                                                        @if($training->cost_per_participant)
                                                            Rp {{ number_format($training->cost_per_participant, 0, ',', '.') }}
                                                        @else
                                                            Gratis
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Status:</strong></td>
                                                    <td>
                                                        @switch($training->status)
                                                            @case('draft')
                                                                <span class="badge badge-secondary">Draft</span>
                                                                @break
                                                            @case('published')
                                                                <span class="badge badge-success">Published</span>
                                                                @break
                                                            @case('ongoing')
                                                                <span class="badge badge-warning">Ongoing</span>
                                                                @break
                                                            @case('completed')
                                                                <span class="badge badge-info">Completed</span>
                                                                @break
                                                            @case('cancelled')
                                                                <span class="badge badge-danger">Cancelled</span>
                                                                @break
                                                        @endswitch
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    @if($training->description)
                                        <div class="mt-3">
                                            <h6>Deskripsi:</h6>
                                            <p class="text-muted">{{ $training->description }}</p>
                                        </div>
                                    @endif

                                    @if($training->objectives)
                                        <div class="mt-3">
                                            <h6>Tujuan Training:</h6>
                                            <p class="text-muted">{{ $training->objectives }}</p>
                                        </div>
                                    @endif

                                    @if($training->prerequisites)
                                        <div class="mt-3">
                                            <h6>Prasyarat:</h6>
                                            <p class="text-muted">{{ $training->prerequisites }}</p>
                                        </div>
                                    @endif

                                    @if($training->instructor_name)
                                        <div class="mt-3">
                                            <h6>Instruktur:</h6>
                                            <p class="text-muted">
                                                <i class="fas fa-chalkboard-teacher mr-1"></i>
                                                {{ $training->instructor_name }}
                                                @if($training->instructor_contact)
                                                    <br><small class="text-muted">{{ $training->instructor_contact }}</small>
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Target Departments -->
                            @if($training->departments->count() > 0)
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Departemen Target</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($training->departments as $dept)
                                                <div class="col-md-6 mb-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>{{ $dept->divisi }}</span>
                                                        <span class="badge badge-primary">Target</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <!-- Registration Status -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Status Pendaftaran</h5>
                                </div>
                                <div class="card-body">
                                    @if($existingParticipant)
                                        <div class="text-center">
                                            @switch($existingParticipant->registration_status)
                                                @case('registered')
                                                    <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                                                    <h5 class="text-warning">Menunggu Persetujuan</h5>
                                                    <p class="text-muted">Pendaftaran Anda sedang menunggu persetujuan dari HR.</p>
                                                    <p class="text-muted small">
                                                        Daftar pada: {{ $existingParticipant->registered_at->format('d/m/Y H:i') }}
                                                    </p>
                                                    @break
                                                @case('approved')
                                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                                    <h5 class="text-success">Disetujui</h5>
                                                    <p class="text-muted">Pendaftaran Anda telah disetujui.</p>
                                                    <p class="text-muted small">
                                                        Disetujui pada: {{ $existingParticipant->approved_at->format('d/m/Y H:i') }}
                                                    </p>
                                                    @break
                                                @case('rejected')
                                                    <i class="fas fa-times-circle fa-3x text-danger mb-3"></i>
                                                    <h5 class="text-danger">Ditolak</h5>
                                                    <p class="text-muted">Pendaftaran Anda ditolak.</p>
                                                    @if($existingParticipant->rejection_reason)
                                                        <div class="alert alert-danger">
                                                            <strong>Alasan:</strong> {{ $existingParticipant->rejection_reason }}
                                                        </div>
                                                    @endif
                                                    @break
                                                @case('attended')
                                                    <i class="fas fa-user-check fa-3x text-info mb-3"></i>
                                                    <h5 class="text-info">Sedang Berlangsung</h5>
                                                    <p class="text-muted">Training sedang berlangsung.</p>
                                                    @break
                                                @case('completed')
                                                    <i class="fas fa-graduation-cap fa-3x text-success mb-3"></i>
                                                    <h5 class="text-success">Selesai</h5>
                                                    <p class="text-muted">Training telah selesai.</p>
                                                    @if($existingParticipant->certificate_issued)
                                                        <div class="alert alert-success">
                                                            <i class="fas fa-certificate mr-1"></i>
                                                            Sertifikat telah diterbitkan
                                                        </div>
                                                    @endif
                                                    @break
                                                @case('cancelled')
                                                    <i class="fas fa-ban fa-3x text-secondary mb-3"></i>
                                                    <h5 class="text-secondary">Dibatalkan</h5>
                                                    <p class="text-muted">Pendaftaran dibatalkan.</p>
                                                    @if($existingParticipant->cancellation_reason)
                                                        <div class="alert alert-warning">
                                                            <strong>Alasan:</strong> {{ $existingParticipant->cancellation_reason }}
                                                        </div>
                                                    @endif
                                                    @break
                                            @endswitch
                                        </div>
                                    @else
                                        <div class="text-center">
                                            <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                                            <h5 class="text-primary">Belum Terdaftar</h5>
                                            <p class="text-muted">Anda belum terdaftar untuk training ini.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Training Statistics -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Statistik Training</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h4 class="text-primary">{{ $participantsCount }}</h4>
                                            <small class="text-muted">Total Peserta</small>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success">{{ $approvedCount }}</h4>
                                            <small class="text-muted">Disetujui</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Registration Form -->
                            @if(!$existingParticipant && $canRegister)
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Daftar Training</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('hr.training.registration.register', $training->id) }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label for="registration_type">Tipe Pendaftaran</label>
                                                <select name="registration_type" id="registration_type" class="form-control" required>
                                                    <option value="voluntary">Sukarela</option>
                                                    <option value="recommended">Direkomendasikan</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="notes">Catatan (Opsional)</label>
                                                <textarea name="notes" id="notes" class="form-control" rows="3" 
                                                          placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-block">
                                                <i class="fas fa-user-plus mr-1"></i>
                                                Daftar Training
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @elseif($existingParticipant && in_array($existingParticipant->registration_status, ['registered', 'approved']))
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Aksi</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('hr.training.registration.cancel', $existingParticipant->id) }}" 
                                              method="POST" onsubmit="return confirm('Yakin ingin membatalkan pendaftaran?')">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-block">
                                                <i class="fas fa-times mr-1"></i>
                                                Batalkan Pendaftaran
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
