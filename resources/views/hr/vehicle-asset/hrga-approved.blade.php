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
        .cust-col {
            white-space: nowrap;
        }
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

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Disetujui HRGA - {{ $type === 'vehicle' ? 'Kendaraan' : 'Inventaris' }}</h5>
                        <a href="{{ route('hr.vehicle-asset.hrga-pending', ['type' => $type]) }}" class="btn btn-secondary btn-sm">
                            <i class="mdi mdi-arrow-left"></i> Kembali ke Pending HRGA
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>Nama Karyawan</th>
                                        <th>Bagian</th>
                                        <th>{{ $type === 'vehicle' ? 'Jenis Kendaraan' : 'Kategori' }}</th>
                                        <th>Keperluan</th>
                                        <th>Tujuan</th>
                                        <th>Periode</th>
                                        <th>Catatan HRGA</th>
                                        <th>Disetujui Oleh</th>
                                        <th>Tanggal Disetujui</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($requests as $request)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $request->request_date->format('d/m/Y') }}</td>
                                            <td>{{ $request->employee_name }}</td>
                                            <td>{{ $request->department }}</td>
                                            <td>{{ $type === 'vehicle' ? $request->vehicle_type : $request->asset_category }}</td>
                                            <td>{{ $request->purpose_type }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($request->destination, 30) }}</td>
                                            <td>{{ $request->start_date->format('d/m/Y') }} - {{ $request->end_date->format('d/m/Y') }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($request->hrga_notes, 30) ?: '-' }}</td>
                                            <td>{{ $request->hrga->name ?? 'N/A' }}</td>
                                            <td>{{ $request->hrga_at ? $request->hrga_at->format('d/m/Y H:i') : '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="mdi mdi-information-outline fs-1"></i>
                                                    <p class="mt-2">Belum ada data {{ $type === 'vehicle' ? 'kendaraan' : 'inventaris' }} yang disetujui HRGA</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($requests->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $requests->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endsection
