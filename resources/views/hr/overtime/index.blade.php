@extends('main.layouts.main')
@section('title')
    Permohonan Data Karyawan
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
        .status-badge { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
    </style>
@endsection
@section('page-title')
    Permohonan Data Karyawan
@endsection
@section('body')

    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Permohonan Data Karyawan</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item active">Permohonan Data Karyawan</li>
            </ol>
        </div>
    </div>

    {{-- SPV Approval Card (visible if $spvPending exists) --}}
    @if(!empty($spvPending) && $spvPending->count())
        <div class="row">
            <div class="col-12">
                <div class="card border border-warning">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <strong>Menunggu Persetujuan SPV</strong>
                        <a href="{{ route('hr.overtime.spv-pending') }}" class="btn btn-light btn-sm">
                            <i class="mdi mdi-open-in-new"></i> Buka Halaman SPV
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>Nama</th>
                                        <th>Bagian</th>
                                        <th>Lokasi</th>
                                        <th>Mulai</th>
                                        <th>Selesai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($spvPending as $row)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $row->request_date->format('d/m/Y') }}</td>
                                            <td>{{ $row->employee_name }}</td>
                                            <td>{{ $row->department }}</td>
                                            <td>{{ $row->location }}</td>
                                            <td>{{ $row->start_time->format('H:i') }}</td>
                                            <td>{{ $row->end_time->format('H:i') }}</td>
                                            <td class="cust-col">
                                                <form action="{{ route('hr.overtime.spv-approve', $row->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="mdi mdi-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('hr.overtime.spv-reject', $row->id) }}" method="POST" class="d-inline ms-1">
                                                    @csrf
                                                    <input type="hidden" name="notes" value="Ditolak oleh SPV">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="mdi mdi-close"></i>
                                                    </button>
                                                </form>
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

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Data Lembur</h5>
                        @if (auth()->user()->canApprove())
                            <small class="text-muted">Rekap data lembur divisi {{ auth()->user()->divisi }}</small>
                        @else
                            <small class="text-muted">Data lembur Anda</small>
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        @if (auth()->user()->canApprove())
                            <a href="{{ route('hr.overtime.spv-pending') }}" class="btn btn-warning btn-sm">
                                <i class="mdi mdi-account-check"></i> Pending SPV
                            </a>
                            <a href="{{ route('hr.overtime.head-pending') }}" class="btn btn-info btn-sm">
                                <i class="mdi mdi-account-star"></i> Pending Head
                            </a>
                        @endif
                        <a href="{{ route('hr.overtime.hrga-pending') }}" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-account-tie"></i> Pending HRGA
                        </a>
                        <a href="{{ route('hr.overtime.hrga-approved') }}" class="btn btn-success btn-sm">
                            <i class="mdi mdi-check-circle"></i> Approved HRGA
                        </a>
                        <a href="{{ route('hr.overtime.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus"></i> Tambah Data Lembur
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal</th>
                                    <th>Lokasi</th>
                                    <th>Nama Karyawan</th>
                                    <th>Bagian</th>
                                    <th>Jam Mulai</th>
                                    <th>Jam Selesai</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($entries as $entry)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $entry->request_date->format('d/m/Y') }}</td>
                                        <td>{{ $entry->location }}</td>
                                        <td>{{ $entry->employee_name }}</td>
                                        <td>{{ $entry->department }}</td>
                                        <td>{{ $entry->start_time->format('H:i') }}</td>
                                        <td>{{ $entry->end_time->format('H:i') }}</td>
                                        <td>
                                            @php
                                                $statusClassMap = [
                                                    'pending_spv' => 'warning',
                                                    'spv_approved' => 'info',
                                                    'spv_rejected' => 'danger',
                                                    'head_approved' => 'primary',
                                                    'head_rejected' => 'danger',
                                                    'hrga_approved' => 'success',
                                                    'hrga_rejected' => 'danger',
                                                ];
                                                $statusTextMap = [
                                                    'pending_spv' => 'Menunggu SPV',
                                                    'spv_approved' => 'Disetujui SPV',
                                                    'spv_rejected' => 'Ditolak SPV',
                                                    'head_approved' => 'Disetujui Head',
                                                    'head_rejected' => 'Ditolak Head',
                                                    'hrga_approved' => 'Disetujui HRGA',
                                                    'hrga_rejected' => 'Ditolak HRGA',
                                                ];
                                                $statusClass = isset($statusClassMap[$entry->status]) ? $statusClassMap[$entry->status] : 'secondary';
                                                $statusText = isset($statusTextMap[$entry->status]) ? $statusTextMap[$entry->status] : 'Unknown';
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }} status-badge">{{ $statusText }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailModal{{ $entry->id }}">
                                                    <i class="mdi mdi-eye"></i>
                                                </button>
                                                @if(auth()->user()->canApprove() && $entry->status === 'spv_approved')
                                                    <form action="{{ route('hr.overtime.head-approve', $entry->id) }}" method="POST" class="d-inline ms-1">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success" title="Approve Head">
                                                            <i class="mdi mdi-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Detail Modal -->
                                    <div class="modal fade" id="detailModal{{ $entry->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Detail Data Lembur</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <strong>Tanggal:</strong> {{ $entry->request_date->format('d/m/Y') }}<br>
                                                            <strong>Lokasi:</strong> {{ $entry->location }}<br>
                                                            <strong>Nama Karyawan:</strong> {{ $entry->employee_name }}<br>
                                                            <strong>Bagian:</strong> {{ $entry->department }}<br>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Jam Mulai:</strong> {{ $entry->start_time->format('H:i') }}<br>
                                                            <strong>Jam Selesai:</strong> {{ $entry->end_time->format('H:i') }}<br>
                                                            <strong>Status:</strong> 
                                                            <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span><br>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <strong>Keterangan Pekerjaan:</strong><br>
                                                            <p class="mt-2">{{ $entry->job_description }}</p>
                                                        </div>
                                                    </div>
                                                    
                                                    @if($entry->spv_notes)
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <strong>Catatan SPV:</strong><br>
                                                                <p class="mt-2 text-muted">{{ $entry->spv_notes }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($entry->head_notes)
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <strong>Catatan Head:</strong><br>
                                                                <p class="mt-2 text-muted">{{ $entry->head_notes }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($entry->hrga_notes)
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <strong>Catatan HRGA:</strong><br>
                                                                <p class="mt-2 text-muted">{{ $entry->hrga_notes }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="mdi mdi-information-outline fs-1"></i>
                                                <p class="mt-2">Belum ada data lembur</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($entries->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $entries->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
