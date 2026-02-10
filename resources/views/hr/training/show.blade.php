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
                        <a href="{{ route('hr.training.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Kembali
                        </a>
                        <a href="{{ route('hr.training.edit', $training->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit mr-1"></i>
                            Edit
                        </a>
                        @if($training->status == 'draft')
                            <form action="{{ route('hr.training.publish', $training->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm" 
                                        onclick="return confirm('Yakin ingin mempublish training ini?')">
                                    <i class="fas fa-paper-plane mr-1"></i>
                                    Publish
                                </button>
                            </form>
                        @endif
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
                                                                <i class="fas fa-chalkboard-teacher"></i> Kelas Tatap Muka
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
                                                        @if(!$training->is_active)
                                                            <br><span class="badge badge-dark">Inactive</span>
                                                        @endif
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

                                    @if($training->notes)
                                        <div class="mt-3">
                                            <h6>Catatan Tambahan:</h6>
                                            <p class="text-muted">{{ $training->notes }}</p>
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
                            <!-- Training Statistics -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Statistik Training</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <h4 class="text-primary">{{ $training->participants_count }}</h4>
                                            <small class="text-muted">Total Peserta</small>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-success">{{ $training->approved_participants_count }}</h4>
                                            <small class="text-muted">Disetujui</small>
                                        </div>
                                    </div>
                                    <div class="row text-center mt-3">
                                        <div class="col-6">
                                            <h4 class="text-info">{{ $training->completed_participants_count }}</h4>
                                            <small class="text-muted">Selesai</small>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-warning">{{ $training->participants_count - $training->approved_participants_count }}</h4>
                                            <small class="text-muted">Pending</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Training Info -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Informasi Training</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless table-sm">
                                        <tr>
                                            <td><strong>Dibuat Oleh:</strong></td>
                                            <td>{{ $training->creator->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Dibuat:</strong></td>
                                            <td>{{ $training->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        @if($training->updater)
                                            <tr>
                                                <td><strong>Diupdate Oleh:</strong></td>
                                                <td>{{ $training->updater->name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tanggal Update:</strong></td>
                                                <td>{{ $training->updated_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Participants List -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Daftar Peserta</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>Departemen</th>
                                                    <th>Jabatan</th>
                                                    <th>Tipe Pendaftaran</th>
                                                    <th>Status</th>
                                                    <th>Tanggal Daftar</th>
                                                    <th>Tanggal Update</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($participants as $participant)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $participant->employee->name }}</strong>
                                                            <br><small class="text-muted">{{ $participant->employee->email }}</small>
                                                        </td>
                                                        <td>{{ $participant->employee->divisiUser->divisi ?? 'N/A' }}</td>
                                                        <td>{{ $participant->employee->jabatanUser->jabatan ?? 'N/A' }}</td>
                                                        <td>
                                                            @switch($participant->registration_type)
                                                                @case('mandatory')
                                                                    <span class="badge badge-danger">Wajib</span>
                                                                    @break
                                                                @case('voluntary')
                                                                    <span class="badge badge-success">Sukarela</span>
                                                                    @break
                                                                @case('recommended')
                                                                    <span class="badge badge-warning">Direkomendasikan</span>
                                                                    @break
                                                            @endswitch
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
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">
                                                            <div class="py-4">
                                                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                                                <p class="text-muted">Belum ada peserta yang terdaftar.</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    @if($participants->hasPages())
                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $participants->links() }}
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
</div>
@endsection
