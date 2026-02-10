@extends('main.layouts.main')
@section('title')
    General Manager Pending Approval
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .cust-col { white-space: nowrap; }
    </style>
@endsection
@section('page-title')
    General Manager Pending Approval
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">General Manager Pending Approval</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item active">General Manager Pending Approval</li>
            </ol>
        </div>
    </div>

    @php
        $totalPending = $formRequests->count() + $vehicleRequests->count() + $assetRequests->count();
    @endphp
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card"><div class="card-body text-center"><h3 class="text-primary">{{ $totalPending }}</h3><p class="text-muted mb-0">Total Menunggu</p></div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body text-center"><h3 class="text-info">{{ $formRequests->count() }}</h3><p class="text-muted mb-0">Form Karyawan</p></div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body text-center"><h3 class="text-success">{{ $vehicleRequests->count() }}</h3><p class="text-muted mb-0">Kendaraan</p></div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body text-center"><h3 class="text-warning">{{ $assetRequests->count() }}</h3><p class="text-muted mb-0">Inventaris</p></div></div>
        </div>
    </div>

    <div class="row"><div class="col-12"><div class="card"><div class="card-header"><h4 class="card-title">Daftar Pengajuan Menunggu Approval General Manager</h4></div><div class="card-body">
        <ul class="nav nav-tabs mb-4" id="requestTabs" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" id="forms-tab" data-toggle="tab" data-target="#forms" type="button" role="tab"><i class="mdi mdi-file-document me-1"></i> Form Karyawan <span class="badge badge-info">{{ $formRequests->count() }}</span></button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="vehicle-tab" data-toggle="tab" data-target="#vehicle" type="button" role="tab"><i class="mdi mdi-car me-1"></i> Kendaraan <span class="badge badge-success">{{ $vehicleRequests->count() }}</span></button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="asset-tab" data-toggle="tab" data-target="#asset" type="button" role="tab"><i class="mdi mdi-package-variant me-1"></i> Inventaris <span class="badge badge-warning">{{ $assetRequests->count() }}</span></button></li>
        </ul>

        <div class="tab-content" id="requestTabsContent">
            <div class="tab-pane fade show active" id="forms" role="tabpanel">
                @if($formRequests->count() > 0)
                    <div class="table-responsive"><table class="table table-hover"><thead><tr><th>No. Pengajuan</th><th>Jenis</th><th>Pemohon</th><th>Divisi</th><th>Tanggal Dibuat</th><th>Aksi</th></tr></thead><tbody>
                    @foreach($formRequests as $request)
                        <tr>
                            <td><span class="fw-bold">{{ $request->request_number }}</span></td>
                            <td><span class="badge bg-info text-white">{{ $request->request_type_label }}</span></td>
                            <td>{{ $request->employee->name ?? 'N/A' }}</td>
                            <td>{{ $request->employee->divisiUser->divisi ?? $request->employee->divisiUser->nama_divisi ?? 'Divisi ' . $request->employee->divisi }}</td>
                            <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                            <td><a href="{{ route('hr.approval.show', $request->id) }}" class="btn btn-sm btn-outline-primary"><i class="mdi mdi-eye"></i> Lihat</a></td>
                        </tr>
                    @endforeach
                    </tbody></table></div>
                @else
                    <div class="text-center py-5"><i class="mdi mdi-check-circle text-success" style="font-size: 4rem;"></i><h5 class="text-muted mt-3">Tidak ada form pengajuan yang menunggu approval</h5></div>
                @endif
            </div>

            <div class="tab-pane fade" id="vehicle" role="tabpanel">
                @if($vehicleRequests->count() > 0)
                    <div class="table-responsive"><table class="table table-hover"><thead><tr><th>Tanggal</th><th>Nama</th><th>Jenis</th><th>Tujuan</th><th>Aksi</th></tr></thead><tbody>
                    @foreach($vehicleRequests as $request)
                        <tr>
                            <td>{{ $request->request_date->format('d/m/Y') }}</td>
                            <td>{{ $request->employee_name }}</td>
                            <td>{{ $request->vehicle_type }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($request->destination, 30) }}</td>
                            <td><a href="{{ route('hr.vehicle-asset.index', ['type' => 'vehicle']) }}" class="btn btn-sm btn-outline-success"><i class="mdi mdi-eye"></i> Lihat</a></td>
                        </tr>
                    @endforeach
                    </tbody></table></div>
                @else
                    <div class="text-center py-5"><i class="mdi mdi-check-circle text-success" style="font-size: 4rem;"></i><h5 class="text-muted mt-3">Tidak ada permintaan kendaraan yang menunggu approval</h5></div>
                @endif
            </div>

            <div class="tab-pane fade" id="asset" role="tabpanel">
                @if($assetRequests->count() > 0)
                    <div class="table-responsive"><table class="table table-hover"><thead><tr><th>Tanggal</th><th>Nama</th><th>Kategori</th><th>Tujuan</th><th>Aksi</th></tr></thead><tbody>
                    @foreach($assetRequests as $request)
                        <tr>
                            <td>{{ $request->request_date->format('d/m/Y') }}</td>
                            <td>{{ $request->employee_name }}</td>
                            <td>{{ $request->asset_category }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($request->destination, 30) }}</td>
                            <td><a href="{{ route('hr.vehicle-asset.index', ['type' => 'asset']) }}" class="btn btn-sm btn-outline-info"><i class="mdi mdi-eye"></i> Lihat</a></td>
                        </tr>
                    @endforeach
                    </tbody></table></div>
                @else
                    <div class="text-center py-5"><i class="mdi mdi-check-circle text-success" style="font-size: 4rem;"></i><h5 class="text-muted mt-3">Tidak ada permintaan inventaris yang menunggu approval</h5></div>
                @endif
            </div>
        </div>
    </div></div></div></div></div>
@endsection
@section('scripts')
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
@endsection

