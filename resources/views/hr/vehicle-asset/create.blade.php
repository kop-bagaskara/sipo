@extends('main.layouts.main')
@section('title')
    @if($type === 'vehicle')
        Form Permintaan Kendaraan
    @else
        Form Permintaan Inventaris
    @endif
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
        :root {
            --primary-vehicle: #0d6efd;
            --primary-asset: #198754;
            --radius-sm: 8px;
            --radius-md: 12px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
            --shadow-hover: 0 8px 24px rgba(0,0,0,0.12);
        }

        .cust-col {
            white-space: nowrap;
        }

        /* Progress Steps */
        .form-progress {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }
        .form-progress::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e9ecef;
            z-index: 0;
        }
        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
            flex: 1;
        }
        .progress-step-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: white;
            border: 2px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #6c757d;
            transition: all 0.3s ease;
            margin-bottom: 0.5rem;
        }
        .progress-step.active .progress-step-icon {
            border-color: var(--primary-vehicle);
            background: var(--primary-vehicle);
            color: white;
        }
        .progress-step.completed .progress-step-icon {
            border-color: #198754;
            background: #198754;
            color: white;
        }
        .progress-step-label {
            font-size: 0.75rem;
            color: #6c757d;
            font-weight: 500;
        }
        .progress-step.active .progress-step-label {
            color: var(--primary-vehicle);
        }

        /* Info Cards */
        .info-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid;
            border-radius: var(--radius-sm);
        }
        .info-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-hover);
        }
        .info-icon {
            font-size: 2rem;
            opacity: 0.8;
        }

        /* Stat Cards */
        .stat-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: var(--radius-md);
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        /* Form Styling */
        .form-section {
            background: #f8f9fa;
            border-radius: var(--radius-md);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
        }
        .form-section-title {
            color: #495057;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .form-section-title i {
            color: var(--primary-vehicle);
        }

        /* Input Styling */
        .form-control, .form-select {
            border-radius: var(--radius-sm);
            border: 1.5px solid #dee2e6;
            transition: all 0.2s ease;
            padding: 0.625rem 0.75rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-vehicle);
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        }
        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.375rem;
            font-size: 0.875rem;
        }

        /* Live Summary Card */
        .live-summary {
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.05) 0%, rgba(13, 110, 253, 0.02) 100%);
            border: 1px solid rgba(13, 110, 253, 0.2);
            border-radius: var(--radius-md);
            padding: 1.25rem;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px dashed #dee2e6;
        }
        .summary-item:last-child {
            border-bottom: none;
        }
        .summary-label {
            color: #6c757d;
            font-size: 0.875rem;
        }
        .summary-value {
            font-weight: 500;
            color: #212529;
            font-size: 0.875rem;
        }
        .summary-value.empty {
            color: #adb5bd;
            font-style: italic;
        }

        /* FAQ Items */
        .faq-item {
            cursor: pointer;
            transition: all 0.2s ease;
            border-radius: var(--radius-sm);
        }
        .faq-item:hover {
            background-color: #f8f9fa;
        }
        .policy-card {
            transition: all 0.3s ease;
            border-radius: var(--radius-sm);
        }
        .policy-card:hover {
            box-shadow: var(--shadow-md);
        }

        /* Button Styling */
        .btn {
            border-radius: var(--radius-sm);
            padding: 0.625rem 1.25rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .btn:hover {
            transform: translateY(-1px);
        }

        /* Alert Styling */
        .alert {
            border-radius: var(--radius-sm);
            border: none;
        }
        .alert-warning {
            background: linear-gradient(90deg, #fff3cd 0%, #ffeeba 100%);
        }

        /* Card Styling */
        .card {
            border-radius: var(--radius-md);
            border: 1px solid #e9ecef;
        }
        .card-header {
            border-radius: var(--radius-md) var(--radius-md) 0 0 !important;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        /* Accordion styling */
        .accordion-item {
            border-radius: var(--radius-sm) !important;
            border: 1px solid #e9ecef;
            margin-bottom: 0.5rem;
            overflow: hidden;
        }
        .accordion-button {
            font-weight: 500;
        }
        .accordion-button:not(.collapsed) {
            background-color: rgba(13, 110, 253, 0.05);
        }
    </style>
@endsection
@section('page-title')
    @if($type === 'vehicle')
        Form Permintaan Kendaraan
    @else
        Form Permintaan Inventaris
    @endif
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">
                @if($type === 'vehicle')
                    Form Permintaan Kendaraan
                @else
                    Form Permintaan Inventaris
                @endif
            </h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('hr.dashboard') }}">HR</a></li>
                <li class="breadcrumb-item">
                    <a href="{{ route('hr.vehicle-asset.index', ['type' => $type]) }}">
                        @if($type === 'vehicle')
                            Kendaraan
                        @else
                            Inventaris
                        @endif
                    </a>
                </li>
                <li class="breadcrumb-item active">Buat Permintaan Baru</li>
            </ol>
        </div>
    </div>

    <!-- Quick Info Pills -->
    {{-- <div class="row g-2 mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2">
                <div class="alert alert-info mb-0 py-2 px-3 d-inline-flex align-items-center">
                    <i class="mdi mdi-clock-outline me-2"></i>
                    <small><strong>H-1</strong> untuk pengajuan normal</small>
                </div>
                <div class="alert alert-warning mb-0 py-2 px-3 d-inline-flex align-items-center">
                    <i class="mdi mdi-account-check me-2"></i>
                    <small>Approval atasan <strong>wajib</strong></small>
                </div>
                <div class="alert alert-primary mb-0 py-2 px-3 d-inline-flex align-items-center">
                    <i class="mdi mdi-laptop me-2"></i>
                    <small>Laptop max <strong>1 bulan</strong></small>
                </div>
                <div class="alert alert-success mb-0 py-2 px-3 d-inline-flex align-items-center">
                    <i class="mdi mdi-handshake me-2"></i>
                    <small>Kembalikan dalam kondisi <strong>baik</strong></small>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Compact Info Bar -->
    <div class="card mb-4 border-0 bg-light">
        <div class="card-body py-2">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center text-muted small">
                    <i class="mdi mdi-information-outline me-1"></i>
                    <span>Pengajuan minimal H-1 • Butuh approval atasan • Kembalikan dalam kondisi baik</span>
                </div>
            </div>
        </div>
    </div>



    <div class="row">
        <!-- Form Column -->
        <div class="col ">
            <div class="card shadow-sm">
                <div class="card-header text-white @if($type === 'vehicle') bg-info @else bg-success @endif text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0 text-white"><i class="mdi @if($type === 'vehicle') mdi-car @else mdi-package-variant @endif me-2"></i>Form Permintaan {{ $type === 'vehicle' ? 'Kendaraan' : 'Inventaris' }}</h4>
                            <p class="card-subtitle mb-0 text-white-50 small mt-1">Isi data lengkap untuk pengajuan {{ $type === 'vehicle' ? 'kendaraan' : 'inventaris' }}</p>
                        </div>
                        <div class="text-end">
                            <small class="text-white-50">Langkah <span id="currentStep">1</span> dari 2</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('hr.vehicle-asset.store') }}" id="vehicleAssetForm">
                        @csrf
                        <input type="hidden" name="request_type" value="{{ $type }}">

                        <!-- Progress Steps -->
                        {{-- <div class="form-progress">
                            <div class="progress-step active" data-step="1">
                                <div class="progress-step-icon">
                                    <i class="mdi mdi-account"></i>
                                </div>
                                <span class="progress-step-label">Data Pemohon</span>
                            </div>
                            <div class="progress-step" data-step="2">
                                <div class="progress-step-icon">
                                    <i class="mdi @if($type === 'vehicle') mdi-car @else mdi-package-variant @endif"></i>
                                </div>
                                <span class="progress-step-label">Detail {{ $type === 'vehicle' ? 'Kendaraan' : 'Inventaris' }}</span>
                            </div>
                        </div> --}}

                        <!-- Step 1: Data Pemohon -->
                        <div class="form-section" id="step1">
                            <h5 class="form-section-title">
                                <i class="mdi mdi-account-circle"></i>
                                Informasi Pemohon
                            </h5>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    <i class="mdi mdi-calendar me-1 text-muted"></i>Tanggal Pengajuan
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="request_date" class="form-control @error('request_date') is-invalid @enderror"
                                       value="{{ old('request_date', date('Y-m-d')) }}" required>
                                @error('request_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    <i class="mdi mdi-account me-1 text-muted"></i>Nama
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="employee_name" class="form-control @error('employee_name') is-invalid @enderror"
                                       value="{{ old('employee_name', Auth::user()->name) }}" readonly>
                                @error('employee_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    <i class="mdi mdi-office-building me-1 text-muted"></i>Bagian
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="department" class="form-control @error('department') is-invalid @enderror"
                                       value="{{ old('department', Auth::user()->divisiUser->divisi ?? 'N/A') }}" readonly>
                                @error('department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Step 2: Data Kendaraan/Inventaris -->
                        <div class="form-section" id="step2">
                            <h5 class="form-section-title">
                                <i class="@if($type === 'vehicle') mdi-car @else mdi-package-variant @endif"></i>
                                Detail {{ $type === 'vehicle' ? 'Kendaraan' : 'Inventaris' }}
                            </h5>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="mdi mdi-tag me-1 text-muted"></i>Jenis {{ $type === 'vehicle' ? 'Kendaraan' : 'Barang' }}
                                    <span class="text-danger">*</span>
                                    <i class="mdi mdi-help-circle text-info ms-1" data-toggle="tooltip"
                                        data-placement="right" title="Pilih jenis @if($type === 'vehicle') kendaraan @else inventaris/inventaris @endif yang akan dipinjam"></i>
                                </label>
                                <select name="{{ $type === 'vehicle' ? 'vehicle_type' : 'asset_category' }}"
                                        id="item_type_select"
                                        class="form-select form-control @error($type === 'vehicle' ? 'vehicle_type' : 'asset_category') is-invalid @enderror"
                                        required onchange="toggleFields()">
                                    <option value="">Pilih Jenis</option>
                                    @if($type === 'vehicle')
                                        <optgroup label="🚗 Kendaraan">
                                            <option value="Mobil" {{ old('vehicle_type') == 'Mobil' ? 'selected' : '' }}>Mobil</option>
                                            <option value="Motor" {{ old('vehicle_type') == 'Motor' ? 'selected' : '' }}>Motor</option>
                                        </optgroup>
                                    @else
                                        <optgroup label="💻 Inventaris">
                                            <option value="Laptop" {{ old('asset_category') == 'Laptop' ? 'selected' : '' }}>Laptop Company</option>
                                            <option value="Lain-Lain" {{ old('asset_category') == 'Lain-Lain' ? 'selected' : '' }}>Inventaris Lain-lain</option>
                                        </optgroup>
                                    @endif
                                </select>
                                @error($type === 'vehicle' ? 'vehicle_type' : 'asset_category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">
                                    <i class="mdi mdi-lightbulb-on-outline me-1"></i>
                                    @if($type === 'vehicle')
                                        Pilih jenis kendaraan yang akan digunakan
                                    @else
                                        Untuk laptop, masa pinjam maksimal 1 bulan
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="mdi mdi-help-circle me-1 text-muted"></i>Keperluan
                                    <span class="text-danger">*</span>
                                    <i class="mdi mdi-help-circle text-info ms-1" data-toggle="tooltip"
                                        data-placement="right" title="Pilih tujuan penggunaan @if($type === 'vehicle') kendaraan @else inventaris @endif"></i>
                                </label>
                                <select name="purpose_type" class="form-select form-control @error('purpose_type') is-invalid @enderror" required>
                                    <option value="">Pilih Keperluan</option>
                                    <option value="Meeting" {{ old('purpose_type') == 'Meeting' ? 'selected' : '' }}>📅 Meeting</option>
                                    <option value="Dinas Luar" {{ old('purpose_type') == 'Dinas Luar' ? 'selected' : '' }}>🏢 Dinas Luar</option>
                                    <option value="Training" {{ old('purpose_type') == 'Training' ? 'selected' : '' }}>📚 Training</option>
                                    <option value="Presentasi" {{ old('purpose_type') == 'Presentasi' ? 'selected' : '' }}>📊 Presentasi</option>
                                    <option value="Lainnya" {{ old('purpose_type') == 'Lainnya' ? 'selected' : '' }}>📋 Lainnya</option>
                                </select>
                                @error('purpose_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="mdi mdi-text-box-outline me-1 text-muted"></i>Uraian Kegiatan
                                    <span class="text-danger">*</span>
                                    <i class="mdi mdi-help-circle text-info ms-1" data-toggle="tooltip"
                                        data-placement="right" title="Jelaskan detail keperluan penggunaan @if($type === 'vehicle') kendaraan @else inventaris @endif"></i>
                                </label>
                                <textarea name="purpose" class="form-control @error('purpose') is-invalid @enderror" rows="3"
                                          placeholder="Contoh: @if($type === 'vehicle') Menghadiri meeting dengan client PT. XYZ di Jakarta untuk presentasi produk. @else Mengikuti training di luar kota dan membutuh laptop untuk presentasi. @endif" required>{{ old('purpose') }}</textarea>
                                @error('purpose')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">
                                    <i class="mdi mdi-lightbulb-on-outline me-1"></i>
                                    Jelaskan secara detail agar approval lebih cepat
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="mdi mdi-map-marker me-1 text-muted"></i>Tujuan
                                    <span class="text-danger">*</span>
                                    <i class="mdi mdi-help-circle text-info ms-1" data-toggle="tooltip"
                                        data-placement="right" title="Lokasi tujuan penggunaan @if($type === 'vehicle') kendaraan @else inventaris @endif"></i>
                                </label>
                                <input type="text" class="form-control @error('destination') is-invalid @enderror" name="destination"
                                       placeholder="@if($type === 'vehicle')Contoh: Jakarta - Gedung Menara Sudirman Lt. 15 @else Contoh: Hotel XYZ - Ballroom A @endif"
                                       value="{{ old('destination') }}" required>
                                @error('destination')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">
                                    <i class="mdi mdi-information-outline me-1"></i>
                                    Sertakan lokasi yang lengkap dan jelas
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="mdi @if($type === 'vehicle') mdi-card-text @else mdi-barcode @endif me-1 text-muted"></i>@if($type === 'vehicle') No. Pol @else Kode @endif
                                    <i class="mdi mdi-help-circle text-info ms-1" data-toggle="tooltip"
                                        data-placement="right" title="@if($type === 'vehicle') Nomor polisi kendaraan (opsional) @else Kode inventaris (opsional) @endif"></i>
                                </label>
                                <input type="text" class="form-control @error('license_plate') is-invalid @enderror" name="license_plate"
                                       placeholder="@if($type === 'vehicle') Contoh: B 1234 XYZ @else Contoh: INV-001 @endif"
                                       value="{{ old('license_plate') }}">
                                @error('license_plate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($type === 'vehicle')
                                    <div class="form-text text-muted">
                                        <i class="mdi mdi-information-outline me-1"></i>
                                        Opsional, isi jika kendaraan memiliki plat nomor
                                    </div>
                                @else
                                    <div class="form-text text-muted">
                                        <i class="mdi mdi-information-outline me-1"></i>
                                        Opsional, isi jika inventaris memiliki kode
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="mdi mdi-calendar-check me-1 text-muted"></i>Tanggal Mulai
                                    <span class="text-danger">*</span>
                                    <i class="mdi mdi-help-circle text-info ms-1" data-toggle="tooltip"
                                        data-placement="right" title="Tanggal mulai penggunaan @if($type === 'vehicle') kendaraan @else inventaris @endif"></i>
                                </label>
                                <input type="date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" name="start_date"
                                       value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">
                                    <i class="mdi mdi-lightbulb-on-outline me-1"></i>
                                    @if($type === 'asset')
                                        Untuk laptop, tanggal selesai otomatis 1 bulan
                                    @else
                                        Minimal H-1 pengajuan
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="mdi mdi-calendar-clock me-1 text-muted"></i>Tanggal Selesai
                                    <span class="text-danger">*</span>
                                    <i class="mdi mdi-help-circle text-info ms-1" data-toggle="tooltip"
                                        data-placement="right" title="Tanggal selesai penggunaan @if($type === 'vehicle') kendaraan @else inventaris @endif"></i>
                                </label>
                                <input type="date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" name="end_date"
                                       value="{{ old('end_date') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" id="end_date_info"></small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="mdi mdi-note-text me-1 text-muted"></i>Catatan Tambahan
                                    <i class="mdi mdi-help-circle text-info ms-1" data-toggle="tooltip"
                                        data-placement="right" title="Catatan tambahan terkait peminjaman (opsional)"></i>
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" rows="2"
                                          placeholder="@if($type === 'vehicle') Contoh: Harap bensin terisi sebelum digunakan. @else Contoh: Sudah terinstall software yang diperlukan. @endif">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror>
                                <div class="form-text text-muted">
                                    <i class="mdi mdi-information-outline me-1"></i>
                                    Opsional, berikan informasi tambahan yang relevan
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between gap-2 pt-3">
                            <a href="{{ route('hr.vehicle-asset.index', ['type' => $type]) }}" class="btn btn-outline-secondary">
                                <i class="mdi mdi-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-info">
                                <i class="mdi mdi-check me-1"></i> Ajukan Permintaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script>
        // Define helper functions outside DOMContentLoaded
        function toggleFields() {
            const requestType = '{{ $type }}';
            const licensePlateField = document.querySelector('input[name="license_plate"]');
            const itemTypeSelect = document.getElementById('item_type_select');
            const startDateField = document.getElementById('start_date');
            const endDateField = document.getElementById('end_date');
            const endDateInfo = document.getElementById('end_date_info');

            if (!licensePlateField) return;

            const licensePlateLabel = licensePlateField.previousElementSibling;
            const selectedItemType = itemTypeSelect ? itemTypeSelect.value : '';

            if (requestType === 'vehicle') {
                licensePlateField.required = false;
                licensePlateField.disabled = false;
                if (licensePlateLabel) {
                    licensePlateLabel.innerHTML = 'No. Pol <i class="mdi mdi-help-circle text-info ms-1" data-toggle="tooltip" data-placement="right" title="Nomor polisi kendaraan (opsional)"></i>';
                }
                if (endDateField) {
                    endDateField.readOnly = false;
                    if (endDateInfo) {
                        endDateInfo.textContent = '';
                        endDateInfo.className = 'text-muted';
                    }
                }
                if (licensePlateLabel) {
                    var tooltipTrigger = licensePlateLabel.querySelector('[data-toggle="tooltip"]');
                    if (tooltipTrigger) {
                        new bootstrap.Tooltip(tooltipTrigger);
                    }
                }
            } else {
                licensePlateField.required = false;
                licensePlateField.disabled = true;
                if (licensePlateLabel) {
                    licensePlateLabel.innerHTML = 'Kode Inventaris <i class="mdi mdi-help-circle text-info ms-1" data-toggle="tooltip" data-placement="right" title="Kode inventaris (opsional)"></i>';
                }

                if (selectedItemType === 'Laptop' && endDateField) {
                    endDateField.readOnly = true;
                    if (endDateInfo) {
                        endDateInfo.textContent = 'Otomatis di-set 1 bulan setelah tanggal mulai';
                        endDateInfo.className = 'text-success fw-bold';
                    }
                } else if (endDateField) {
                    endDateField.readOnly = false;
                    if (endDateInfo) {
                        endDateInfo.textContent = '';
                        endDateInfo.className = 'text-muted';
                    }
                }
                if (licensePlateLabel) {
                    var tooltipTrigger = licensePlateLabel.querySelector('[data-toggle="tooltip"]');
                    if (tooltipTrigger) {
                        new bootstrap.Tooltip(tooltipTrigger);
                    }
                }
            }

            showInfoAlert(requestType, selectedItemType);
        }

        function showInfoAlert(requestType, itemType) {
            const existingAlert = document.getElementById('type-info-alert');
            if (existingAlert) {
                existingAlert.remove();
            }

            const form = document.querySelector('form');
            const formBody = form.querySelector('.card-body');

            if (requestType === 'asset' && itemType === 'Laptop') {
                const alertDiv = document.createElement('div');
                alertDiv.id = 'type-info-alert';
                alertDiv.className = 'alert alert-primary border-0 mb-3';
                alertDiv.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="mdi mdi-laptop fs-4 me-3"></i>
                        <div>
                            <strong>Info Laptop:</strong> Masa pinjam maksimal 1 bulan. Tanggal selesai akan otomatis dihitung dari tanggal mulai.
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                `;
                formBody.insertBefore(alertDiv, formBody.firstChild);
            }
        }

        function setEndDateForLaptop() {
            const itemTypeSelect = document.getElementById('item_type_select');
            const startDateField = document.getElementById('start_date');
            const endDateField = document.getElementById('end_date');
            const endDateInfo = document.getElementById('end_date_info');

            if (!itemTypeSelect || !startDateField || !endDateField) return;

            const selectedItemType = itemTypeSelect.value;
            const requestType = '{{ $type }}';

            if (requestType === 'asset' && selectedItemType === 'Laptop' && startDateField.value) {
                const startDate = new Date(startDateField.value);
                const endDate = new Date(startDate);
                endDate.setMonth(endDate.getMonth() + 1);

                endDateField.value = endDate.toISOString().split('T')[0];

                if (endDateInfo) {
                    const diffTime = endDate - startDate;
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    endDateInfo.textContent = `Otomatis di-set 1 bulan setelah tanggal mulai (${diffDays} hari)`;
                    endDateInfo.className = 'text-success fw-bold';
                }
            }
        }

        function updateLiveSummary() {
            const itemType = document.getElementById('item_type_select')?.value;
            const summaryType = document.getElementById('summaryType');
            if (summaryType) {
                summaryType.textContent = itemType || '-';
                summaryType.classList.toggle('empty', !itemType);
            }

            const purposeType = document.querySelector('[name="purpose_type"]')?.value;
            const summaryPurpose = document.getElementById('summaryPurpose');
            if (summaryPurpose) {
                summaryPurpose.textContent = purposeType || '-';
                summaryPurpose.classList.toggle('empty', !purposeType);
            }

            const startDate = document.getElementById('start_date')?.value;
            const endDate = document.getElementById('end_date')?.value;
            const summaryDates = document.getElementById('summaryDates');
            if (summaryDates) {
                if (startDate && endDate) {
                    const start = new Date(startDate).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                    const end = new Date(endDate).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                    summaryDates.textContent = `${start} - ${end}`;
                    summaryDates.classList.remove('empty');
                } else if (startDate) {
                    const start = new Date(startDate).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                    summaryDates.textContent = `${start} - ...`;
                    summaryDates.classList.remove('empty');
                } else {
                    summaryDates.textContent = '-';
                    summaryDates.classList.add('empty');
                }
            }

            const destination = document.querySelector('[name="destination"]')?.value;
            const summaryDestination = document.getElementById('summaryDestination');
            if (summaryDestination) {
                summaryDestination.textContent = destination || '-';
                summaryDestination.classList.toggle('empty', !destination);
            }

            updateProgressSteps();
        }

        function updateProgressSteps() {
            const steps = document.querySelectorAll('.progress-step');
            const hasItemType = document.getElementById('item_type_select')?.value;
            const hasPurpose = document.querySelector('[name="purpose_type"]')?.value;
            const hasDates = document.getElementById('start_date')?.value;

            steps[0]?.classList.add('completed');

            if (hasItemType || hasPurpose || hasDates) {
                steps[1]?.classList.add('active');
                const currentStep = document.getElementById('currentStep');
                if (currentStep) currentStep.textContent = '2';
            } else {
                steps[1]?.classList.remove('active');
                const currentStep = document.getElementById('currentStep');
                if (currentStep) currentStep.textContent = '1';
            }
        }

        // Main initialization
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            // Initialize live summary
            updateLiveSummary();
            const form = document.getElementById('vehicleAssetForm');
            const inputs = form?.querySelectorAll('input, select, textarea');

            if (inputs) {
                inputs.forEach(input => {
                    input.addEventListener('change', updateLiveSummary);
                    input.addEventListener('input', updateLiveSummary);
                });
            }

            // Initialize toggleFields
            toggleFields();

            // Add event listener to select
            const itemTypeSelect = document.getElementById('item_type_select');
            if (itemTypeSelect) {
                itemTypeSelect.addEventListener('change', function() {
                    toggleFields();
                    if (document.getElementById('start_date').value) {
                        setEndDateForLaptop();
                    }
                });
            }

            // Set minimum date for start_date
            const startDateField = document.getElementById('start_date');
            const endDateField = document.getElementById('end_date');

            if (startDateField && endDateField) {
                const today = new Date().toISOString().split('T')[0];
                startDateField.setAttribute('min', today);

                startDateField.addEventListener('change', function() {
                    const startDate = new Date(this.value);
                    endDateField.min = this.value;

                    setEndDateForLaptop();

                    const itemTypeSelect = document.getElementById('item_type_select');
                    const selectedItemType = itemTypeSelect ? itemTypeSelect.value : '';
                    const requestType = '{{ $type }}';

                    if (!(requestType === 'asset' && selectedItemType === 'Laptop')) {
                        if (endDateField.value && new Date(endDateField.value) < startDate) {
                            endDateField.value = '';
                        }
                    }
                });
            }

            // Form submission with SweetAlert
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const assetType = '{{ $type === 'vehicle' ? 'kendaraan' : 'inventaris' }}';
                    const confirmColor = '{{ $type === 'vehicle' ? '#17a2b8' : '#198754' }}';

                    Swal.fire({
                        icon: 'question',
                        title: 'Konfirmasi',
                        html: '<div class="text-left">' +
                              '<p>Apakah Anda yakin ingin mengajukan permintaan ' + assetType + ' ini?</p>' +
                              '<div class="alert alert-info mb-0 mt-3">' +
                              '<strong>Rincian:</strong><br>' +
                              'Jenis: ' + (document.getElementById('item_type_select').value || '-') + '<br>' +
                              'Keperluan: ' + (document.querySelector('[name="purpose_type"]').value || '-') + '<br>' +
                              'Tanggal: ' + (startDateField?.value || '-') + ' s/d ' + (endDateField?.value || '-') +
                              '</div></div>',
                        showCancelButton: true,
                        confirmButtonText: '<i class="mdi mdi-check"></i> Ya, Ajukan',
                        cancelButtonText: '<i class="mdi mdi-close"></i> Batal',
                        confirmButtonColor: confirmColor,
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            }
        });
    </script>
@endsection
