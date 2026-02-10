@extends('main.layouts.main')
@section('title')
    Daftar Surat Perintah Lembur (SPL)
@endsection
@section('page-title')
    Daftar Surat Perintah Lembur (SPL)
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Daftar Surat Perintah Lembur (SPL)</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item active">SPL</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar SPL</h4>
                    @if(auth()->user()->canApprove() || (int)(auth()->user()->jabatan ?? 0) === 5)
                        <a href="{{ route('hr.spl.create') }}" class="btn btn-primary float-end">
                            <i class="mdi mdi-plus"></i> Buat SPL Baru
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No. SPL</th>
                                    <th>Tanggal</th>
                                    <th>Shift</th>
                                    <th>Mesin</th>
                                    <th>Jumlah Karyawan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($splRequests as $spl)
                                    <tr>
                                        <td>{{ $spl->spl_number }}</td>
                                        <td>{{ $spl->request_date->format('d/m/Y') }}</td>
                                        <td>{{ $spl->shift }}</td>
                                        <td>{{ $spl->mesin ?? '-' }}</td>
                                        <td>{{ $spl->employees->count() }}</td>
                                        <td>
                                            @php
                                                $badgeClass = [
                                                    'draft' => 'secondary',
                                                    'submitted' => 'info',
                                                    'signed' => 'warning',
                                                    'approved_hrd' => 'success',
                                                    'rejected' => 'danger'
                                                ][$spl->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $badgeClass }}">{{ $spl->status_label }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('hr.spl.show', $spl->id) }}" class="btn btn-sm btn-info">
                                                <i class="mdi mdi-eye"></i> Detail
                                            </a>
                                            @if($spl->status !== 'rejected')
                                                <a href="{{ route('hr.spl.print', $spl->id) }}" class="btn btn-sm btn-primary" target="_blank">
                                                    <i class="mdi mdi-printer"></i> Cetak
                                                </a>
                                            @endif
                                            @if($spl->supervisor_id === auth()->id() && in_array($spl->status, ['submitted', 'signed']) && !$spl->signed_document_path)
                                                <a href="{{ route('hr.spl.show', $spl->id) }}#upload-section" class="btn btn-sm btn-warning" title="Upload Dokumen yang Sudah Ditandatangani">
                                                    <i class="mdi mdi-upload"></i> Upload Hasil
                                                </a>
                                            @elseif($spl->signed_document_path)
                                                <a href="{{ asset('storage/' . $spl->signed_document_path) }}" target="_blank" class="btn btn-sm btn-success" title="Lihat Dokumen yang Sudah Diupload">
                                                    <i class="mdi mdi-file-check"></i> Lihat Hasil
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data SPL</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $splRequests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

