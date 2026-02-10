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
                    <div class="card-header">
                        <h4 class="card-title">Data {{ $type === 'vehicle' ? 'Kendaraan' : 'Inventaris' }}</h4>
                        <p class="card-subtitle">Rekap data {{ $type === 'vehicle' ? 'kendaraan' : 'inventaris' }} divisi
                            {{ Auth::user()->divisi }}</p>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('hr.vehicle-asset.index', ['type' => 'vehicle']) }}"
                                        class="btn {{ $type === 'vehicle' ? 'btn-info' : 'btn-outline-info' }}">
                                        Kendaraan
                                    </a>
                                    <a href="{{ route('hr.vehicle-asset.index', ['type' => 'asset']) }}"
                                        class="btn {{ $type === 'asset' ? 'btn-info' : 'btn-outline-info' }}">
                                        Inventaris
                                    </a>
                                </div>
                            </div>
                            {{-- <div class="col-md-6 text-right">
                                @if (Auth::user()->canApprove())
                                    <a href="{{ route('hr.approval.manager-pending') }}" class="btn btn-warning btn-sm">
                                        <i class="mdi mdi-account-tie"></i> Pending Manager
                                    </a>
                                @endif
                                @if (Auth::user()->isHR())
                                    <a href="{{ route('hr.approval.hrga-pending') }}" class="btn btn-primary btn-sm">
                                        <i class="mdi mdi-account-tie"></i> Pending HRGA
                                    </a>
                                    <a href="{{ route('hr.vehicle-asset.hrga-approved', ['type' => $type]) }}" class="btn btn-success btn-sm">
                                        <i class="mdi mdi-check-circle"></i> Approved HRGA
                                    </a>
                                @endif
                                <a href="{{ route('hr.vehicle-asset.create', ['type' => $type]) }}" class="btn btn-primary">
                                    <i class="mdi mdi-plus"></i> Tambah Data {{ $type === 'vehicle' ? 'Kendaraan' : 'Inventaris' }}
                                </a>
                            </div> --}}
                        </div>

                        <!-- Manager Pending Card -->
                        @if (Auth::user()->canApprove())
                            @php
                                // PENTING: General Manager (divisi 13) melihat request yang dibuat oleh Manager (jabatan 3)
                                // Filter berdasarkan general_id, bukan divisi_id
                                if ((int) Auth::user()->divisi === 13) {
                                    // General Manager melihat request yang memiliki general_id = user.id dan belum di-approve/reject
                                    $pendingManager = \App\Models\VehicleAssetRequest::where(function ($q) {
                                        $q->where('general_id', Auth::user()->id)->orWhere(
                                            'manager_id',
                                            Auth::user()->id,
                                        ); // Backward compatibility
                                    })
                                        ->whereNull('general_approved_at')
                                        ->whereNull('general_rejected_at')
                                        ->where('status', \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER)
                                        ->where('request_type', $type)
                                        ->limit(5)
                                        ->get();
                                } else {
                                    // Manager/Head/SPV melihat request dari divisi mereka
                                    $pendingManager = \App\Models\VehicleAssetRequest::forDivisi(Auth::user()->divisi)
                                        ->where('request_type', $type)
                                        ->pendingManager()
                                        ->limit(5)
                                        ->get();
                                }
                            @endphp
                            @if ($pendingManager->count() > 0)
                                <div class="card mb-4">
                                    <div class="card-header bg-info">
                                        <h5 class="card-title mb-0 text-white">
                                            <i class="mdi mdi-clock-outline"></i> Menunggu Persetujuan
                                            {{ (int) Auth::user()->divisi === 13 ? 'General Manager' : 'Manager' }}
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Tanggal</th>
                                                        <th>Nama</th>
                                                        <th>{{ $type === 'vehicle' ? 'Jenis Kendaraan' : 'Kategori' }}</th>
                                                        <th>Tujuan</th>
                                                        <th>Periode</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($pendingManager as $request)
                                                        <tr>
                                                            <td>{{ $request->request_date->format('d/m/Y') }}</td>
                                                            <td>{{ $request->employee_name }}</td>
                                                            <td>{{ $type === 'vehicle' ? $request->vehicle_type : $request->asset_category }}
                                                            </td>
                                                            <td>{{ \Illuminate\Support\Str::limit($request->purpose, 30) }}
                                                            </td>
                                                            <td>{{ $request->start_date->format('d/m/Y') }} -
                                                                {{ $request->end_date->format('d/m/Y') }}</td>
                                                            <td>
                                                                @if (Auth::user()->jabatan == 4 && Auth::user()->divisi == 4)
                                                                    <form method="POST"
                                                                        action="{{ route('hr.vehicle-asset.manager-approve', $request->id) }}"
                                                                        class="d-inline">
                                                                        @csrf
                                                                        <button type="submit"
                                                                            class="btn btn-success btn-sm" disabled>
                                                                            <i class="mdi mdi-check"></i> Sedang Diajukan ke
                                                                            General Manager
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    {{-- IF SELAIN MANAGER DAN GENERAL MANAGER --}}

                                                                    @if (Auth::user()->jabatan == 3)
                                                                        @if ($request->employee_id == Auth::user()->id)
                                                                            <button type="submit"
                                                                                class="btn btn-success btn-sm" disabled>
                                                                                <i class="mdi mdi-check"></i> Sedang
                                                                                Diajukan ke General
                                                                                Manager
                                                                            </button>
                                                                        @else
                                                                            <form method="POST"
                                                                                action="{{ route('hr.vehicle-asset.manager-approve', $request->id) }}"
                                                                                class="d-inline">
                                                                                @csrf
                                                                                <button type="submit"
                                                                                    class="btn btn-success btn-sm">
                                                                                    <i class="mdi mdi-check"></i> Setujui
                                                                                </button>
                                                                            </form>
                                                                            <button type="button"
                                                                                class="btn btn-danger btn-sm"
                                                                                onclick="rejectRequest({{ $request->id }})">
                                                                                <i class="mdi mdi-close"></i> Tolak
                                                                            </button>
                                                                        @endif
                                                                    @else
                                                                        <button type="submit"
                                                                            class="btn btn-success btn-sm" disabled>
                                                                            <i class="mdi mdi-check"></i> Sedang Diajukan ke
                                                                            Manager
                                                                        </button>
                                                                    @endif
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-center mt-2">
                                            <a href="{{ route('hr.approval.manager-pending') }}"
                                                class="btn btn-info btn-sm">
                                                Lihat Semua ({{ $pendingManager->count() }})
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <!-- HRGA Pending Card -->
                        @if (Auth::user()->isHR())
                            @php
                                $pendingHrga = \App\Models\VehicleAssetRequest::where('request_type', $type)
                                    ->pendingHrga()
                                    ->limit(5)
                                    ->get();
                            @endphp
                            @if ($pendingHrga->count() > 0)
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="mdi mdi-clock-outline"></i> Menunggu Persetujuan HRGA
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Tanggal</th>
                                                        <th>Nama</th>
                                                        <th>{{ $type === 'vehicle' ? 'Jenis Kendaraan' : 'Kategori' }}</th>
                                                        <th>Tujuan</th>
                                                        <th>Periode</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($pendingHrga as $request)
                                                        <tr>
                                                            <td>{{ $request->request_date->format('d/m/Y') }}</td>
                                                            <td>{{ $request->employee_name }}</td>
                                                            <td>{{ $type === 'vehicle' ? $request->vehicle_type : $request->asset_category }}
                                                            </td>
                                                            <td>{{ \Illuminate\Support\Str::limit($request->purpose, 30) }}
                                                            </td>
                                                            <td>{{ $request->start_date->format('d/m/Y') }} -
                                                                {{ $request->end_date->format('d/m/Y') }}</td>
                                                            <td>
                                                                <form method="POST"
                                                                    action="{{ route('hr.vehicle-asset.hrga-approve', $request->id) }}"
                                                                    class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-success btn-sm">
                                                                        <i class="mdi mdi-check"></i> Setujui
                                                                    </button>
                                                                </form>
                                                                <button type="button" class="btn btn-danger btn-sm"
                                                                    onclick="rejectHrgaRequest({{ $request->id }})">
                                                                    <i class="mdi mdi-close"></i> Tolak
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-center mt-2">
                                            <a href="{{ route('hr.approval.hrga-pending') }}"
                                                class="btn btn-primary btn-sm">
                                                Lihat Semua ({{ $pendingHrga->count() }})
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>{{ $type === 'vehicle' ? 'Jenis Kendaraan' : 'Kategori' }}</th>
                                        <th>Nama Karyawan</th>
                                        <th>Bagian</th>
                                        <th>Keperluan</th>
                                        <th>Tujuan</th>
                                        <th>Periode</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($requests as $request)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $request->request_date->format('d/m/Y') }}</td>
                                            <td>{{ $type === 'vehicle' ? $request->vehicle_type : $request->asset_category }}
                                            </td>
                                            <td>{{ $request->employee_name }}</td>
                                            <td>{{ $request->department }}</td>
                                            <td>{{ $request->purpose_type }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($request->destination, 30) }}</td>
                                            <td>{{ $request->start_date->format('d/m/Y') }} -
                                                {{ $request->end_date->format('d/m/Y') }}</td>
                                            <td>
                                                @php
                                                    $statusMap = [
                                                        'pending_manager' => [
                                                            'class' => 'warning',
                                                            'text' => 'Pending Manager',
                                                        ],
                                                        'manager_approved' => [
                                                            'class' => 'info',
                                                            'text' => 'Disetujui Manager',
                                                        ],
                                                        'manager_rejected' => [
                                                            'class' => 'danger',
                                                            'text' => 'Ditolak Manager',
                                                        ],
                                                        'hrga_approved' => [
                                                            'class' => 'success',
                                                            'text' => 'Disetujui HRGA',
                                                        ],
                                                        'hrga_rejected' => [
                                                            'class' => 'danger',
                                                            'text' => 'Ditolak HRGA',
                                                        ],
                                                    ];
                                                    $status = $statusMap[$request->status] ?? [
                                                        'class' => 'secondary',
                                                        'text' => $request->status,
                                                    ];
                                                @endphp
                                                <span
                                                    class="badge badge-{{ $status['class'] }}">{{ $status['text'] }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $currentUser = Auth::user();
                                                    $canEdit = false;
                                                    $canDelete = false;

                                                    // Cek apakah bisa edit/hapus (hanya oleh pembuat request dan belum ada approval sama sekali)
                                                    if ($request->employee_id == $currentUser->id) {
                                                        $canEdit =
                                                            is_null($request->manager_at) &&
                                                            is_null($request->general_approved_at) &&
                                                            is_null($request->general_rejected_at) &&
                                                            is_null($request->hrga_at) &&
                                                            $request->status ===
                                                                \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER;
                                                        $canDelete = $canEdit; // Bisa hapus jika bisa edit
                                                    }
                                                @endphp

                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('hr.vehicle-asset.show', $request->id) }}"
                                                        class="btn btn-sm btn-outline-info" title="Detail">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>

                                                    @if ($canEdit)
                                                        <a href="{{ route('hr.vehicle-asset.edit', $request->id) }}"
                                                            class="btn btn-sm btn-outline-warning" title="Edit">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </a>
                                                    @endif

                                                    @if ($canDelete)
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="confirmDelete({{ $request->id }})" title="Hapus">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    @endif

                                                    @if ($request->status === 'manager_approved' && $currentUser->canApprove())
                                                        <form method="POST"
                                                            action="{{ route('hr.vehicle-asset.hrga-approve', $request->id) }}"
                                                            class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-primary">
                                                                <i class="mdi mdi-check"></i> Approve HRGA
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="mdi mdi-information-outline fs-1"></i>
                                                    <p class="mt-2">Belum ada data
                                                        {{ $type === 'vehicle' ? 'kendaraan' : 'inventaris' }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($requests->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $requests->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
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
                        form.action = `/sipo/hr/vehicle-asset/${id}/manager-reject`;

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);

                        const notesInput = document.createElement('input');
                        notesInput.type = 'hidden';
                        notesInput.name = 'manager_notes';
                        notesInput.value = result.value;
                        form.appendChild(notesInput);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }

            function rejectHrgaRequest(id) {
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

            function confirmDelete(id) {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Apakah Anda yakin ingin menghapus pengajuan ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `{{ url('hr/vehicle-asset') }}/${id}`;

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);

                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        form.appendChild(methodInput);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        </script>
    @endsection
