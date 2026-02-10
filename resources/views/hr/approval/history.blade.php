@extends('main.layouts.main')

@section('title', 'Riwayat Approval')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Riwayat Approval</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('hr.dashboard') }}">HR</a></li>
                        <li class="breadcrumb-item active">Riwayat Approval</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Options -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('hr.approval.history') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="supervisor_approved" {{ request('status') == 'supervisor_approved' ? 'selected' : '' }}>Disetujui Atasan</option>
                                    <option value="supervisor_rejected" {{ request('status') == 'supervisor_rejected' ? 'selected' : '' }}>Ditolak Atasan</option>
                                    <option value="hr_approved" {{ request('status') == 'hr_approved' ? 'selected' : '' }}>Disetujui HR</option>
                                    <option value="hr_rejected" {{ request('status') == 'hr_rejected' ? 'selected' : '' }}>Ditolak HR</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jenis Pengajuan</label>
                                <select name="type" class="form-select">
                                    <option value="">Semua Jenis</option>
                                    <option value="shift_change" {{ request('type') == 'shift_change' ? 'selected' : '' }}>Permohonan Tukar Shift</option>
                                    <option value="absence" {{ request('type') == 'absence' ? 'selected' : '' }}>Permohonan Tidak Masuk Kerja</option>
                                    <option value="overtime" {{ request('type') == 'overtime' ? 'selected' : '' }}>Surat Perintah Lembur</option>
                                    <option value="vehicle_asset" {{ request('type') == 'vehicle_asset' ? 'selected' : '' }}>Permintaan Kendaraan/Inventaris</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ $requests->whereIn('status', ['supervisor_approved', 'hr_approved'])->count() }}</h3>
                    <p class="text-muted mb-0">Total Disetujui</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-danger">{{ $requests->whereIn('status', ['supervisor_rejected', 'hr_rejected'])->count() }}</h3>
                    <p class="text-muted mb-0">Total Ditolak</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-info">{{ $requests->where('created_at', '>=', now()->subDays(30))->count() }}</h3>
                    <p class="text-muted mb-0">30 Hari Terakhir</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning">{{ $requests->where('created_at', '>=', now()->subDays(7))->count() }}</h3>
                    <p class="text-muted mb-0">7 Hari Terakhir</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Riwayat Approval</h4>
                </div>
                <div class="card-body">
                    @if($requests->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No. Pengajuan</th>
                                        <th>Jenis</th>
                                        <th>Pemohon</th>
                                        <th>Status</th>
                                        <th>Tanggal Approval</th>
                                        <th>Approver</th>
                                        <th>Catatan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($requests as $request)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $request->request_number }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $request->request_type_label }}</span>
                                        </td>
                                        <td>{{ $request->employee->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge {{ $request->status_badge_class }}">{{ $request->status_label }}</span>
                                        </td>
                                        <td>
                                            @if($request->supervisor_approved_at)
                                                {{ $request->supervisor_approved_at->format('d/m/Y H:i') }}
                                            @elseif($request->supervisor_rejected_at)
                                                {{ $request->supervisor_rejected_at->format('d/m/Y H:i') }}
                                            @elseif($request->hr_approved_at)
                                                {{ $request->hr_approved_at->format('d/m/Y H:i') }}
                                            @elseif($request->hr_rejected_at)
                                                {{ $request->hr_rejected_at->format('d/m/Y H:i') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($request->supervisor_approved_at || $request->supervisor_rejected_at)
                                                {{ $request->supervisor->name ?? 'N/A' }}
                                            @elseif($request->hr_approved_at || $request->hr_rejected_at)
                                                {{ $request->hr->name ?? 'N/A' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($request->supervisor_notes)
                                                <span class="text-muted" title="{{ $request->supervisor_notes }}">
                                                    {{ Str::limit($request->supervisor_notes, 30) }}
                                                </span>
                                            @elseif($request->hr_notes)
                                                <span class="text-muted" title="{{ $request->hr_notes }}">
                                                    {{ Str::limit($request->hr_notes, 30) }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('hr.requests.show', $request->id) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $requests->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-history text-muted" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mt-3">Tidak ada riwayat approval</h5>
                            <p class="text-muted">Belum ada pengajuan yang telah diproses.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
