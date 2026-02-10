@extends('main.layouts.main')
@section('title')
    Detail Permohonan
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
    Detail Permohonan
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Detail Permohonan</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">Detail Permohonan</li>
                </ol>
            </div>
        </div>

        <!-- Request Info -->
        <div class="row mb-4">
            <div class="col-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-2">
                            <i class="mdi mdi-clipboard-text me-2"></i>Informasi Pengajuan
                        </h4>
                        <div>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            @if ($request->status === 'pending')
                                <button type="button" class="btn btn-warning fs-6 w-100">
                                    <i class="mdi mdi-clock me-1"></i>{{ $request->status_label }}
                                </button>
                            @elseif($request->status === 'hr_approved')
                                <button type="button" class="btn btn-success fs-6 w-100">
                                    <i class="mdi mdi-check-circle me-1"></i>{{ $request->status_label }}
                                </button>
                            @elseif(str_contains($request->status, 'rejected'))
                                <button type="button" class="btn btn-danger fs-6 w-100">
                                    <i class="mdi mdi-close-circle me-1"></i>{{ $request->status_label }}
                                </button>
                            @else
                                <button type="button" class="btn btn-info fs-6 w-100">
                                    <i class="mdi mdi-information me-1"></i>{{ $request->status_label }}
                                </button>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <tbody>
                                    <tr>
                                        <td width="40%" class="bg-light">
                                            <strong>No. Pengajuan</strong>
                                        </td>
                                        <td>{{ $request->request_number }}</td>
                                    </tr>
                                    <tr>
                                        <td width="40%" class="bg-light">
                                            <strong>Jenis Pengajuan</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-white">{{ $request->request_type_label }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="40%" class="bg-light">
                                            <strong>Pemohon</strong>
                                        </td>
                                        <td>{{ $request->employee->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td width="40%" class="bg-light">
                                            <strong>Jabatan</strong>
                                        </td>
                                        <td>{{ $request->employee->jabatanUser->jabatan ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td width="40%" class="bg-light">
                                            <strong>Bagian/Divisi</strong>
                                        </td>
                                        <td>{{ $request->employee->divisiUser->divisi ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td width="40%" class="bg-light">
                                            <strong>Tanggal Dibuat</strong>
                                        </td>
                                        <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td width="40%" class="bg-light">
                                            <strong>Lama Pengajuan</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $request->days_since_created }}
                                                hari</span>
                                        </td>
                                    </tr>
                                    @if ($request->supervisor)
                                        <tr>
                                            <td width="40%" class="bg-light">
                                                <strong>Atasan</strong>
                                            </td>
                                            <td>{{ $request->supervisor->name }}</td>
                                        </tr>
                                    @endif
                                    @if ($request->hr)
                                        <tr>
                                            <td width="40%" class="bg-light">
                                                <strong>HR</strong>
                                            </td>
                                            <td>{{ $request->hr->name }}</td>
                                        </tr>
                                    @endif
                                    @if ($request->replacement_person_name)
                                        <tr>
                                            <td width="40%" class="bg-light">
                                                <strong>Pelaksana Tugas</strong>
                                            </td>
                                            <td>
                                                {{ $request->replacement_person_name }}
                                                @if ($request->replacement_person_nip)
                                                    <small
                                                        class="text-muted">({{ $request->replacement_person_nip }})</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif

                                    {{-- Kode Group & Nama Group (khusus tukar shift skenario self) --}}
                                    @if (
                                        $request->request_type === 'shift_change' &&
                                            isset($request->request_data['scenario_type']) &&
                                            $request->request_data['scenario_type'] === 'self')
                                        @php
                                            $workGroupData = $request->employee
                                                ? $request->employee->getWorkGroupData()
                                                : null;
                                        @endphp
                                        @if ($workGroupData)
                                            <tr>
                                                <td width="40%" class="bg-light">
                                                    <strong>Kode Group</strong>
                                                </td>
                                                <td>
                                                    <p>{{ $workGroupData->{'Kode Group'} }}</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="40%" class="bg-light">
                                                    <strong>Nama Group</strong>
                                                </td>
                                                <td>
                                                    <p>{{ $workGroupData->{'Nama Group'} ?? '-' }}</p>
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Alur Approval -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">
                                    <i class="mdi mdi-route me-2"></i>Alur Approval
                                </h4>
                            </div>
                            <div class="card-body">
                                @if(isset($approvalFlow) && count($approvalFlow) > 0)
                                    <div class="d-flex flex-column gap-3">
                                        @foreach($approvalFlow as $index => $flow)
                                            <div class="d-flex align-items-start">
                                                <!-- Step Number -->
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                                                        style="width: 40px; height: 40px;
                                                        @if($index + 1 <= ($request->current_approval_order ?? 0))
                                                            background: linear-gradient(135deg, #28a745, #20c997);
                                                        @elseif($index + 1 === ($request->current_approval_order ?? 0) + 1)
                                                            background: linear-gradient(135deg, #ffc107, #ff9800);
                                                        @else
                                                            background: #e9ecef;
                                                        @endif
                                                        ">
                                                        <span class="fw-bold
                                                            @if($index + 1 <= ($request->current_approval_order ?? 0))
                                                                text-white
                                                            @elseif($index + 1 === ($request->current_approval_order ?? 0) + 1)
                                                                text-dark
                                                            @else
                                                                text-muted
                                                            @endif
                                                        ">{{ $index + 1 }}</span>
                                                    </div>
                                                </div>

                                                <!-- Step Content -->
                                                <div class="flex-grow-1">
                                                    <div class="card
                                                        @if($index + 1 <= ($request->current_approval_order ?? 0))
                                                            border-success
                                                        @elseif($index + 1 === ($request->current_approval_order ?? 0) + 1)
                                                            border-warning
                                                        @else
                                                            border-light
                                                        @endif
                                                    ">
                                                        <div class="card-body py-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <h6 class="mb-1
                                                                        @if($index + 1 <= ($request->current_approval_order ?? 0))
                                                                            text-success
                                                                        @elseif($index + 1 === ($request->current_approval_order ?? 0) + 1)
                                                                            text-warning
                                                                        @else
                                                                            text-muted
                                                                        @endif
                                                                    ">
                                                                        <i class="mdi
                                                                            @if($flow->role_key === 'spv_division')
                                                                                mdi-account-tie
                                                                            @elseif($flow->role_key === 'head_division')
                                                                                mdi-account-star
                                                                            @elseif($flow->role_key === 'manager')
                                                                                mdi-account-key
                                                                            @elseif($flow->role_key === 'hr')
                                                                                mdi-account-check
                                                                            @else
                                                                                mdi-account
                                                                            @endif
                                                                        me-1"></i>
                                                                        {{ $flow->description ?? strtoupper(str_replace('_', ' ', $flow->role_key)) }}
                                                                    </h6>
                                                                    <small class="text-muted">
                                                                        Order: {{ $flow->approval_order }}
                                                                        @if($flow->role_key) | Role: {{ $flow->role_key }} @endif
                                                                    </small>
                                                                </div>
                                                                <div>
                                                                    @if($index + 1 <= ($request->current_approval_order ?? 0))
                                                                        <span class="badge bg-success">Selesai</span>
                                                                    @elseif($index + 1 === ($request->current_approval_order ?? 0) + 1)
                                                                        <span class="badge bg-warning">Pending</span>
                                                                    @else
                                                                        <span class="badge bg-light text-dark">Menunggu</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="mdi mdi-information me-2"></i>
                                        Tidak ada alur approval yang ditemukan untuk jenis pengajuan ini.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="mdi mdi-clipboard-text me-2"></i>Detail Pengajuan
                        </h4>
                    </div>
                    <div class="card-body">
                        @if ($request->request_type == 'overtime' && $request->overtimeEmployees->count() > 0)
                            <!-- Overtime Employees Table -->
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="50">No</th>
                                            <th>Nama Karyawan</th>
                                            <th>Bagian</th>
                                            <th>Jam Kerja</th>
                                            <th>Keterangan Pekerjaan</th>
                                            <th width="150">Tanda Tangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($request->overtimeEmployees as $index => $employee)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td><strong>{{ $employee->employee_name }}</strong></td>
                                                <td>{{ $employee->department }}</td>
                                                <td><span
                                                        class="badge bg-light text-dark">{{ $employee->time_range }}</span>
                                                </td>
                                                <td>{{ $employee->job_description }}</td>
                                                <td>
                                                    @if ($employee->is_signed)
                                                        <span class="badge bg-success">
                                                            <i class="mdi mdi-check me-1"></i>Sudah
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning">
                                                            <i class="mdi mdi-clock me-1"></i>Belum
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <!-- Regular Request Data -->
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <tbody>
                                        @foreach ($request->formatted_request_data as $key => $value)
                                            <tr>
                                                <td width="35%" class="bg-light">
                                                    <strong><i
                                                            class="mdi mdi-chevron-right me-2 text-primary"></i>{{ $key }}</strong>
                                                </td>
                                                <td>{{ $value }}</td>
                                            </tr>
                                            {{-- Tambahkan Shift Baru setelah Jam Baru --}}
                                            @if (isset($newShiftData) && str_contains($key, 'Jam Baru'))
                                                <tr>
                                                    <td width="35%" class="bg-light">
                                                        {{-- <strong><i class="mdi mdi-information me-2 text-info"></i>Shift Baru (Sesuai Jam Baru)</strong> --}}
                                                    </td>
                                                    <td>
                                                        @if ($newShiftData)
                                                            {{ $newShiftData->{'Kode Shift'} ?? '-' }} -
                                                            <strong>{{ $newShiftData->{'Nama Shift'} ?? '-' }}</strong>
                                                            <span class="text-muted ms-2">
                                                                ({{ $newShiftData->{'Jam In'} ?? '-' }} -
                                                                {{ $newShiftData->{'Jam Out'} ?? '-' }})
                                                            </span>
                                                        @else
                                                            <span class="text-danger">
                                                                <i class="mdi mdi-alert-circle"></i>
                                                                Tidak ada shift yang sesuai dengan jam
                                                                {{ $request->request_data['new_start_time'] ?? '-' }} -
                                                                {{ $request->request_data['new_end_time'] ?? '-' }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                            {{-- Tambahkan Shift Info setelah Jam Pemohon (Exchange Scenario) --}}
                                            @if (isset($exchangeFromShiftData) && str_contains($key, 'Jam Pemohon'))
                                                <tr>
                                                    <td width="35%" class="bg-light"></td>
                                                    <td>
                                                        @if ($exchangeFromShiftData)
                                                            {{ $exchangeFromShiftData->{'Kode Shift'} ?? '-' }} -

                                                            <strong>{{ $exchangeFromShiftData->{'Nama Shift'} ?? '-' }}</strong>
                                                            <span class="text-muted ms-2">
                                                                ({{ $exchangeFromShiftData->{'Jam In'} ?? '-' }} -
                                                                {{ $exchangeFromShiftData->{'Jam Out'} ?? '-' }})
                                                            </span>
                                                        @else
                                                            <span class="text-danger">
                                                                <i class="mdi mdi-alert-circle"></i>
                                                                Tidak ada shift yang sesuai
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif

                                            {{-- Jadwal Pemohon Tersedia setelah Jam Pemohon --}}
                                            @if (isset($scheduleData) && str_contains($key, 'Jam Pemohon'))
                                                @if ($scheduleData)
                                                    <tr class="table-success">
                                                        <td width="35%">
                                                            <strong><i
                                                                    class="mdi mdi-calendar-check me-2 text-success"></i>Jadwal
                                                                Pemohon Tersedia</strong>
                                                        </td>
                                                        <td>
                                                            <ul class="list-unstyled mb-0">
                                                                <li><strong>Tanggal:</strong>
                                                                    {{ \Carbon\Carbon::parse($scheduleData->Tgl)->format('d/m/Y') }}
                                                                </li>
                                                                <li><strong>Kode Group:</strong>
                                                                    {{ $scheduleData->{'Kode Group'} }}</li>
                                                                <li><strong>Kode Shift:</strong>
                                                                    {{ $scheduleData->{'Kode Shift'} ?? '-' }}</li>
                                                                <li><strong>Jam Shift:</strong>
                                                                    {{ $scheduleData->{'Jam In'} ?? '-' }} -
                                                                    {{ $scheduleData->{'Jam Out'} ?? '-' }}</li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                @else
                                                    <tr class="table-warning">
                                                        <td width="35%">
                                                            <strong><i
                                                                    class="mdi mdi-calendar-remove me-2 text-warning"></i>Jadwal
                                                                Pemohon Tidak Ditemukan</strong>
                                                        </td>
                                                        <td>
                                                            <span class="text-danger">
                                                                <i class="mdi mdi-alert-circle"></i>
                                                                Tidak ada jadwal untuk Kode Group
                                                                <strong>{{ $requesterWorkGroupData ? $requesterWorkGroupData->{'Kode Group'} : '-' }}</strong>
                                                                pada tanggal
                                                                <strong>{{ \Carbon\Carbon::parse($request->request_data['date'])->format('d/m/Y') }}</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endif
                                            {{-- Tambahkan Shift Info setelah Jam Pengganti (Exchange Scenario) --}}
                                            @if (isset($exchangeToShiftData) && str_contains($key, 'Jam Pengganti'))
                                                <tr>
                                                    <td width="35%" class="bg-light"></td>
                                                    <td>
                                                        @if ($exchangeToShiftData)
                                                            {{ $exchangeToShiftData->{'Kode Shift'} ?? '-' }} -

                                                            <strong>{{ $exchangeToShiftData->{'Nama Shift'} ?? '-' }}</strong>
                                                            <span class="text-muted ms-2">
                                                                ({{ $exchangeToShiftData->{'Jam In'} ?? '-' }} -
                                                                {{ $exchangeToShiftData->{'Jam Out'} ?? '-' }})
                                                            </span>
                                                        @else
                                                            <span class="text-danger">
                                                                <i class="mdi mdi-alert-circle"></i>
                                                                Tidak ada shift yang sesuai
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif

                                            {{-- Jadwal Pengganti Tersedia setelah Keperluan Pengganti (baris terakhir exchange scenario) --}}
                                            @if (str_contains($key, 'Keperluan Pengganti'))
                                                @if (isset($partnerScheduleData) && $partnerScheduleData)
                                                    <tr class="table-info">
                                                        <td width="35%">
                                                            <strong><i
                                                                    class="mdi mdi-account-switch me-2 text-info"></i>Jadwal
                                                                Pengganti Tersedia</strong>
                                                        </td>
                                                        <td>
                                                            <ul class="list-unstyled mb-0">
                                                                <li><strong>Tanggal:</strong>
                                                                    {{ \Carbon\Carbon::parse($partnerScheduleData->Tgl)->format('d/m/Y') }}
                                                                </li>
                                                                <li><strong>Kode Group:</strong>
                                                                    {{ $partnerScheduleData->{'Kode Group'} }}</li>
                                                                <li><strong>Kode Shift:</strong>
                                                                    {{ $partnerScheduleData->{'Kode Shift'} ?? '-' }}</li>
                                                                <li><strong>Jam Shift:</strong>
                                                                    {{ $partnerScheduleData->{'Jam In'} ?? '-' }} -
                                                                    {{ $partnerScheduleData->{'Jam Out'} ?? '-' }}</li>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                @else
                                                    <tr class="table-warning">
                                                        <td width="35%">
                                                            <strong><i
                                                                    class="mdi mdi-account-alert me-2 text-warning"></i>Jadwal
                                                                Pengganti Tidak Ditemukan</strong>
                                                        </td>
                                                        <td>
                                                            <span class="text-danger">
                                                                <i class="mdi mdi-alert-circle"></i>
                                                                Tidak ada jadwal untuk teman yang diajak tukar shift
                                                                pada tanggal
                                                                <strong>{{ \Carbon\Carbon::parse($request->request_data['date'])->format('d/m/Y') }}</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        {{-- Informasi Shift akan ditukar untuk Holiday Scenario --}}
                        @if ($request->request_type === 'shift_change'
                            && isset($request->request_data['scenario_type'])
                            && $request->request_data['scenario_type'] === 'holiday')
                            <div class="alert alert-warning mt-3">
                                <h6 class="alert-heading">
                                    <i class="mdi mdi-information me-2"></i>Informasi Perubahan Jadwal
                                </h6>
                                <p class="mb-2">
                                    <strong>Setelah disetujui:</strong> Jadwal karyawan akan ditukar menjadi shift
                                    <span class="badge bg-danger text-white">OFF</span> pada tanggal kerja hari merah.
                                </p>
                                <ul class="mb-0">
                                    <li><strong>Tanggal Kerja:</strong> {{ \Carbon\Carbon::parse($request->request_data['holiday_work_date'] ?? $request->request_data['date'] ?? '-')->format('d/m/Y') }}</li>
                                    <li><strong>Jam Kerja:</strong> {{ $request->request_data['applicant_start_time'] ?? '-' }} - {{ $request->request_data['applicant_end_time'] ?? '-' }}</li>
                                    <li><strong>Shift Baru:</strong> <span class="badge bg-danger text-white">OFF</span> (Kode: O)</li>
                                </ul>
                            </div>
                        @endif

                        @if ($request->notes)
                            <div class="alert alert-info mt-3">
                                <h6 class="alert-heading">
                                    <i class="mdi mdi-note-text me-2"></i>Catatan Tambahan
                                </h6>
                                <p class="mb-0">{{ $request->notes }}</p>
                            </div>
                        @endif

                        @if ($request->attachment_path)
                            <div class="alert alert-secondary mt-3">
                                <h6 class="alert-heading">
                                    <i class="mdi mdi-paperclip me-2"></i>Lampiran
                                </h6>
                                <a href="{{ asset('storage/' . $request->attachment_path) }}" target="_blank"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="mdi mdi-download me-2"></i>Download Lampiran
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- Approval Form -->
                {{-- $canApprove sudah dihitung di controller menggunakan canApproveRequest() yang sudah mempertimbangkan ApprovalSetting dan DivisiApprovalSetting (untuk ABSENCE) --}}


                @if ($canApprove)
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Form Approval</h4>
                                </div>
                                <div class="card-body">
                                    <form id="approvalForm" method="POST"
                                        action="{{ route('hr.approval.process', $request->id) }}">
                                        @csrf
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <label class="form-label">Tindakan <span
                                                        class="text-danger">*</span></label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="action"
                                                        id="approve" value="approve" required>
                                                    <label class="form-check-label text-success" for="approve">
                                                        <i class="mdi mdi-check me-2"></i>Setujui Pengajuan
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="action"
                                                        id="reject" value="reject" required>
                                                    <label class="form-check-label text-danger" for="reject">
                                                        <i class="mdi mdi-close me-2"></i>Tolak Pengajuan
                                                    </label>
                                                </div>
                                            </div>
                                        </div>



                                        {{-- Form Pelaksana Tugas (hanya untuk approval pertama HEAD DIVISI pada request absence) --}}
                                        @if (isset($isFirstApproval) && $isFirstApproval && $request->request_type === 'absence')
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <h5 class="text-primary mb-3">
                                                        <i class="mdi mdi-account-switch me-2"></i>Informasi Pelaksana
                                                        Tugas
                                                    </h5>
                                                    <div class="alert alert-info">
                                                        <i class="mdi mdi-information me-2"></i>
                                                        Silakan tentukan pengganti yang akan menangani tugas selama karyawan
                                                        cuti.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Pilih dari Karyawan <span
                                                            class="text-danger">*</span></label>
                                                    <select name="replacement_person_id" id="replacement_person_id"
                                                        class="form-control">
                                                        <option value="">-- Pilih Karyawan --</option>
                                                        @foreach ($employees as $emp)
                                                            <option value="{{ $emp->id }}"
                                                                data-name="{{ $emp->name }}" data-nip=""
                                                                {{ old('replacement_person_id') == $emp->id ? 'selected' : '' }}>
                                                                {{ $emp->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <small class="text-muted">Atau isi manual di bawah jika karyawan tidak
                                                        ada di
                                                        list</small>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Atau Tulis Manual</label>
                                                    <input type="text" name="replacement_person_name"
                                                        id="replacement_person_name" class="form-control"
                                                        placeholder="Nama Pelaksana Tugas"
                                                        value="{{ old('replacement_person_name') }}">
                                                </div>
                                            </div>

                                            {{-- <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">NIP Pelaksana Tugas</label>
                                            <input type="text" name="replacement_person_nip" id="replacement_person_nip"
                                                   class="form-control"
                                                   placeholder="NIP (opsional)"
                                                   value="{{ old('replacement_person_nip') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Jabatan Pelaksana Tugas</label>
                                            <input type="text" name="replacement_person_position" id="replacement_person_position"
                                                   class="form-control"
                                                   placeholder="Jabatan (opsional)"
                                                   value="{{ old('replacement_person_position') }}">
                                        </div>
                                    </div> --}}
                                        @endif

                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <label class="form-label">Catatan</label>
                                                <textarea name="notes" id="notes" class="form-control" rows="4"
                                                    placeholder="Berikan catatan untuk pengajuan ini..."></textarea>
                                                <div class="form-text">
                                                    @if (auth()->user()->isHR())
                                                        Catatan ini akan dikirim ke pemohon dan atasan.
                                                    @else
                                                        Catatan ini akan dikirim ke pemohon dan HR.
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between">
                                                    {{-- Back route sudah ditentukan di controller --}}
                                                    <a href="{{ route($backRoute) }}" class="btn btn-secondary">
                                                        <i class="mdi mdi-arrow-left me-2"></i>Kembali
                                                    </a>
                                                    <button type="submit" id="submitApproval" class="btn btn-primary">
                                                        <i class="mdi mdi-content-save me-2"></i>Proses Approval
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="mdi mdi-history me-2"></i>Riwayat Approval
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="horizontal-timeline p-4" style="background: #f8f9fa; border-radius: 12px;">
                            {{-- Approval history sudah dibangun di controller untuk menghindari query di view --}}

                            {{-- Container horizontal dengan line connector --}}
                            <div class="d-flex justify-content-between align-items-start position-relative"
                                style="overflow-x: auto;">
                                {{-- Line connecting all steps --}}
                                <div
                                    style="position: absolute; top: 26px; left: 50px; right: 50px; height: 4px; background: #dee2e6; z-index: 0;">
                                </div>

                                @foreach ($approvalHistory as $index => $history)
                                    {{-- Calculate progress width --}}
                                    @php
                                        if ($index === 0) {
                                            $progressWidth = '0%';
                                        } elseif ($history['status'] === 'completed') {
                                            $progressWidth = ($index / (count($approvalHistory) - 1)) * 100;
                                        } else {
                                            $progressWidth = (($index - 1) / (count($approvalHistory) - 1)) * 100;
                                        }
                                    @endphp

                                    {{-- Progress line (only for completed) --}}
                                    @if ($history['status'] === 'completed')
                                        <div
                                            style="position: absolute; top: 26px; left: 50px; height: 4px; background: linear-gradient(to right, #28a745, #20c997); width: {{ $progressWidth }}%; z-index: 1;">
                                        </div>
                                    @endif

                                    {{-- Timeline Item --}}
                                    <div class="flex-shrink-0 text-center"
                                        style="flex: 1; min-width: 140px; max-width: 200px; z-index: 2;">
                                        {{-- Icon/Marker --}}
                                        <div class="mb-3">
                                            @if ($history['status'] === 'completed')
                                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mx-auto shadow-sm"
                                                    style="width: 56px; height: 56px; background: linear-gradient(135deg, #28a745, #20c997);">
                                                    <i class="mdi {{ $history['icon'] }} text-white"
                                                        style="font-size: 26px;"></i>
                                                </div>
                                            @elseif ($history['status'] === 'rejected')
                                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mx-auto shadow-sm"
                                                    style="width: 56px; height: 56px; background: linear-gradient(135deg, #dc3545, #c82333);">
                                                    <i class="mdi mdi-close text-white" style="font-size: 26px;"></i>
                                                </div>
                                            @else
                                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mx-auto bg-light border shadow-sm"
                                                    style="width: 56px; height: 56px; border-width: 3px !important;">
                                                    <i class="mdi mdi-clock-outline text-muted"
                                                        style="font-size: 26px;"></i>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Content Card --}}
                                        <div class="card shadow-sm border-0 mb-2">
                                            <div class="card-body p-2">
                                                @if ($history['status'] === 'completed')
                                                    <div class="mb-1">
                                                        <span class="badge bg-success"
                                                            style="font-size: 10px;">Selesai</span>
                                                    </div>
                                                    <h6 class="mb-1"
                                                        style="color: #28a745; font-size: 12px; line-height: 1.3;">
                                                        <i class="mdi mdi-check-circle me-1"></i>
                                                        {{ $history['title'] }}
                                                    </h6>
                                                @elseif ($history['status'] === 'rejected')
                                                    <div class="mb-1">
                                                        <span class="badge bg-danger"
                                                            style="font-size: 10px;">Ditolak</span>
                                                    </div>
                                                    <h6 class="mb-1"
                                                        style="color: #dc3545; font-size: 12px; line-height: 1.3;">
                                                        <i class="mdi mdi-close-circle me-1"></i>
                                                        {{ $history['title'] }}
                                                    </h6>
                                                @else
                                                    <div class="mb-1">
                                                        <span class="badge bg-warning text-dark"
                                                            style="font-size: 10px;">Pending</span>
                                                    </div>
                                                    <h6 class="mb-1 text-muted"
                                                        style="font-size: 12px; line-height: 1.3;">
                                                        <i class="mdi mdi-clock-outline me-1"></i>
                                                        {{ $history['title'] }}
                                                    </h6>
                                                @endif

                                                @if ($history['approver'])
                                                    <p class="mb-1" style="font-size: 11px;">
                                                        <strong>{{ $history['approver'] }}</strong>
                                                    </p>
                                                @endif

                                                @if ($history['timestamp'])
                                                    <p class="mb-0 text-muted" style="font-size: 10px;">
                                                        {{ $history['timestamp']->format('d/m/y H:i') }}
                                                    </p>
                                                @endif

                                                @if ($history['notes'])
                                                    <div class="alert alert-info mt-2 py-1 px-2" style="font-size: 10px;">
                                                        <small class="mb-0 d-block"
                                                            style="max-height: 40px; overflow: hidden; text-overflow: ellipsis;">
                                                            <strong>Catatan:</strong>
                                                            {{ \Illuminate\Support\Str::limit($history['notes'], 40) }}
                                                        </small>
                                                    </div>
                                                @endif

                                                {{-- Pelaksana Tugas (only for HEAD approval) --}}
                                                @if (
                                                    $history['title'] === 'Approval HEAD DIVISI' &&
                                                        $request->replacement_person_name &&
                                                        $history['status'] === 'completed')
                                                    <div class="alert alert-warning py-1 px-2 mt-2"
                                                        style="font-size: 10px;">
                                                        <strong><i class="mdi mdi-account-switch"></i></strong>
                                                        <span class="d-block mt-1"
                                                            style="max-height: 30px; overflow: hidden;">
                                                            {{ \Illuminate\Support\Str::limit($request->replacement_person_name, 25) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Legend --}}
                            <div class="d-flex justify-content-center gap-4 mt-4 pt-3 border-top">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle me-2"
                                        style="width: 14px; height: 14px; background: linear-gradient(135deg, #28a745, #20c997);">
                                    </div>
                                    <small class="text-muted">Selesai</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light border me-2"
                                        style="width: 14px; height: 14px; border-width: 2px !important;"></div>
                                    <small class="text-muted">Pending</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle me-2"
                                        style="width: 14px; height: 14px; background: linear-gradient(135deg, #dc3545, #c82333);">
                                    </div>
                                    <small class="text-muted">Ditolak</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




        <!-- Approval History -->

        </div>

    @section('styles')
        <style>
            .timeline {
                position: relative;
                padding-left: 30px;
            }

            .timeline::before {
                content: '';
                position: absolute;
                left: 9px;
                top: 0;
                bottom: 0;
                width: 2px;
                background: linear-gradient(to bottom, #007bff, #28a745, #17a2b8);
            }

            .timeline-item {
                position: relative;
                margin-bottom: 30px;
            }

            .timeline-marker {
                position: absolute;
                left: -35px;
                top: 5px;
                width: 20px;
                height: 20px;
                border-radius: 50%;
                border: 3px solid #fff;
                box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 1;
            }

            .timeline-content {
                background: #ffffff;
                padding: 20px;
                border-radius: 8px;
                border-left: 4px solid #007bff;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
            }

            .timeline-content:hover {
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                transform: translateY(-2px);
            }

            .timeline-title {
                margin-bottom: 10px;
                font-weight: 600;
                font-size: 16px;
            }

            .timeline-text {
                margin-bottom: 8px;
                color: #495057;
                font-size: 14px;
            }
        </style>
    @endsection

    @section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
        <script>
            $(document).ready(function() {
                // Handle form submission
                $('#approvalForm').on('submit', function(e) {
                    e.preventDefault();

                    const form = $(this);
                    const action = $('input[name="action"]:checked').val();
                    const notes = $('#notes').val();

                    // Validate action is selected
                    if (!action) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Silakan pilih tindakan (Setujui atau Tolak) terlebih dahulu!',
                            confirmButtonColor: '#4ecdc4'
                        });
                        return false;
                    }

                    // Validate notes if reject
                    if (action === 'reject' && !notes.trim()) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Catatan wajib diisi jika menolak pengajuan!',
                            confirmButtonColor: '#4ecdc4'
                        });
                        return false;
                    }

                    // Validate replacement person if approve and is first approval for absence
                    @if (isset($isFirstApproval) && $isFirstApproval && $request->request_type === 'absence')
                        if (action === 'approve') {
                            const replacementPersonId = $('#replacement_person_id').val();
                            const replacementPersonName = $('#replacement_person_name').val().trim();

                            if (!replacementPersonId && !replacementPersonName) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Pelaksana Tugas Belum Diisi',
                                    html: '<p>Silakan pilih karyawan dari dropdown atau tulis nama pelaksana tugas secara manual.</p>',
                                    confirmButtonColor: '#4ecdc4'
                                });
                                return false;
                            }
                        }
                    @endif

                    // Show confirmation dialog
                    const actionText = action === 'approve' ? 'menyetujui' : 'menolak';
                    const actionIcon = action === 'approve' ? 'question' : 'warning';
                    const confirmColor = action === 'approve' ? '#28a745' : '#dc3545';

                    Swal.fire({
                        icon: actionIcon,
                        title: 'Konfirmasi Approval',
                        html: `
                                <p>Apakah Anda yakin ingin <strong>${actionText}</strong> pengajuan ini?</p>
                                ${notes ? `<p><strong>Catatan:</strong> ${notes}</p>` : ''}
                            `,
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Proses',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: confirmColor,
                        cancelButtonColor: '#6c757d',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            Swal.fire({
                                title: 'Memproses...',
                                text: 'Mohon tunggu, sedang memproses approval.',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Submit form
                            form.off('submit').submit();
                        }
                    });

                    return false;
                });

                // Handle success/error messages from server
                @if (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: '{{ session('success') }}',
                        confirmButtonColor: '#4ecdc4',
                        timer: 3000,
                        timerProgressBar: true
                    });
                @endif

                @if (session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: '{{ session('error') }}',
                        confirmButtonColor: '#dc3545'
                    });
                @endif

                @if ($errors->any())
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        html: `
                                <ul style="text-align: left;">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            `,
                        confirmButtonColor: '#dc3545'
                    });
                @endif
            });

            {{-- JavaScript untuk auto-fill Pelaksana Tugas --}}
            @if (isset($isFirstApproval) && $isFirstApproval && $request->request_type === 'absence')
                // Auto-fill nama ketika memilih dari dropdown
                $('#replacement_person_id').on('change', function() {
                    const selectedOption = $(this).find('option:selected');
                    const name = selectedOption.data('name');

                    if ($(this).val()) {
                        // Jika memilih dari dropdown, auto-fill nama
                        if (name) {
                            $('#replacement_person_name').val(name);
                        }
                    } else {
                        // Jika tidak ada yang dipilih, kosongkan
                        $('#replacement_person_name').val('');
                    }
                });
            @endif
        </script>
    @endsection
@endsection
