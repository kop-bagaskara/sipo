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
                        <h5 class="card-title mb-0">Menunggu Persetujuan HRGA - {{ $type === 'vehicle' ? 'Kendaraan' : 'Inventaris' }}</h5>
                        <a href="{{ route('hr.vehicle-asset.index', ['type' => $type]) }}" class="btn btn-secondary btn-sm">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="card-body">
                        @if($requests->count() > 0)
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
                                            <th>Catatan Manager</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($requests as $request)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $request->request_date->format('d/m/Y') }}</td>
                                                <td>{{ $request->employee_name }}</td>
                                                <td>{{ $request->department }}</td>
                                                <td>{{ $type === 'vehicle' ? $request->vehicle_type : $request->asset_category }}</td>
                                                <td>{{ $request->purpose_type }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit($request->destination, 30) }}</td>
                                                <td>{{ $request->start_date->format('d/m/Y') }} - {{ $request->end_date->format('d/m/Y') }}</td>
                                                <td>{{ \Illuminate\Support\Str::limit($request->manager_notes, 30) ?: '-' }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <form method="POST" action="{{ route('hr.vehicle-asset.hrga-approve', $request->id) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm" 
                                                                    onclick="return confirm('Setujui permintaan ini?')">
                                                                <i class="mdi mdi-check"></i> Setujui
                                                            </button>
                                                        </form>
                                                        <button type="button" class="btn btn-danger btn-sm" 
                                                                onclick="rejectRequest({{ $request->id }})">
                                                            <i class="mdi mdi-close"></i> Tolak
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="mdi mdi-information-outline fs-1"></i>
                                    <p class="mt-2">Tidak ada permintaan {{ $type === 'vehicle' ? 'kendaraan' : 'inventaris' }} yang menunggu persetujuan HRGA</p>
                                </div>
                            </div>
                        @endif

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

    @section('js')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
        <script>
            function rejectRequest(id) {
                Swal.fire({
                    title: 'Tolak Request',
                    input: 'textarea',
                    inputLabel: 'Alasan Penolakan',
                    inputPlaceholder: 'Masukkan alasan penolakan...',
                    inputAttributes: {
                        'aria-label': 'Masukkan alasan penolakan'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Tolak',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/sipo/hr/vehicle-asset/${id}/hrga-reject`;
                        
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);
                        
                        const notesInput = document.createElement('input');
                        notesInput.type = 'hidden';
                        notesInput.name = 'hrga_notes';
                        notesInput.value = result.value;
                        form.appendChild(notesInput);
                        
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        </script>
    @endsection
