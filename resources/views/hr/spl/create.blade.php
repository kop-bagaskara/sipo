@extends('main.layouts.main')
@section('title')
    Form Surat Perintah Lembur (SPL)
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .info-card {
            transition: all 0.3s ease;
            border-left: 4px solid;
        }
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .info-icon {
            font-size: 2rem;
            opacity: 0.8;
        }
        .employee-row {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            transition: all 0.3s ease;
        }
        .employee-row:hover {
            border-color: #4ecdc4;
            box-shadow: 0 4px 12px rgba(78, 205, 196, 0.15);
            transform: translateX(5px);
        }
        .employee-row:last-child {
            margin-bottom: 0;
        }
        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: scale(1.05);
        }
        .faq-item {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .faq-item:hover {
            background-color: #f8f9fa;
        }
        .policy-card {
            transition: all 0.3s ease;
        }
        .policy-card:hover {
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        .employee-counter {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
    </style>
@endsection
@section('page-title')
    Form Surat Perintah Lembur (SPL)
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Form Surat Perintah Lembur (SPL)</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.spl.index') }}">SPL</a></li>
                <li class="breadcrumb-item active">Buat SPL Baru</li>
            </ol>
        </div>
    </div>

    <!-- Quick Stats Section -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-warning shadow-sm stat-card h-100">
                <div class="card-body text-center">
                    <i class="mdi mdi-clock-alert text-warning fs-1 mb-2"></i>
                    <h6 class="fw-bold text-warning mb-1">H-1 Pengajuan</h6>
                    <p class="small text-muted mb-0">Ajukan minimal 1 hari sebelumnya</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info shadow-sm stat-card h-100">
                <div class="card-body text-center">
                    <i class="mdi mdi-account text-info fs-1 mb-2"></i>
                    <h6 class="fw-bold text-info mb-1">Multi Karyawan</h6>
                    <p class="small text-muted mb-0">Satu SPL untuk banyak karyawan</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success shadow-sm stat-card h-100">
                <div class="card-body text-center">
                    <i class="mdi mdi-file-check text-success fs-1 mb-2"></i>
                    <h6 class="fw-bold text-success mb-1">Approval Digital</h6>
                    <p class="small text-muted mb-0">Tidak perlu cetak & tanda tangan manual</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-primary shadow-sm stat-card h-100">
                <div class="card-body text-center">
                    <i class="mdi mdi-calendar-clock text-primary fs-1 mb-2"></i>
                    <h6 class="fw-bold text-primary mb-1">Per Shift</h6>
                    <p class="small text-muted mb-0">Satu SPL per shift kerja</p>
                </div>
            </div>
        </div>
    </div>


    <!-- Information & Policy Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info">
                    <h5 class="card-title mb-0 text-white"><i class="mdi mdi-book-open-variant me-2"></i>Informasi & Kebijakan SPL</h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="splInfoAccordion">
                        <!-- Kebijakan Umum -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="btn btn-info w-100" type="button" data-toggle="collapse" data-target="#policyCollapse">
                                    <i class="mdi mdi-file-document-multiple me-2 text-primary"></i>
                                    <strong>Kebijakan Umum SPL</strong>
                                </button>
                            </h2>
                            <div id="policyCollapse" class="accordion-collapse collapse show" data-parent="#splInfoAccordion">
                                <div class="accordion-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="card border-left border-4 border-info policy-card h-100">
                                                <div class="card-body">
                                                    <h6 class="card-title fw-bold text-info mb-3">
                                                        <i class="mdi mdi-clock-outline me-2"></i>Waktu Pengajuan
                                                    </h6>
                                                    <ul class="list-unstyled mb-0 small">
                                                        <li class="mb-2"><i class="mdi mdi-check-circle text-success me-2"></i>Minimal H-1 sebelum lembur</li>
                                                        <li class="mb-2"><i class="mdi mdi-check-circle text-success me-2"></i>Setelah submit, langsung masuk approval flow</li>
                                                        <li class="mb-2"><i class="mdi mdi-check-circle text-success me-2"></i>Approval dilakukan secara digital</li>
                                                        <li><i class="mdi mdi-check-circle text-success me-2"></i>SPL dibuat per tanggal dan shift</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-left border-4 border-warning policy-card h-100">
                                                <div class="card-body">
                                                    <h6 class="card-title fw-bold text-warning mb-3">
                                                        <i class="mdi mdi-account-group me-2"></i>Data Karyawan
                                                    </h6>
                                                    <ul class="list-unstyled mb-0 small">
                                                        <li class="mb-2"><i class="mdi mdi-check-circle text-success me-2"></i>Pilih dari Master Employee atau Users</li>
                                                        <li class="mb-2"><i class="mdi mdi-check-circle text-success me-2"></i>Bisa input manual jika tidak ada di daftar</li>
                                                        <li class="mb-2"><i class="mdi mdi-check-circle text-success me-2"></i>Tambah karyawan tanpa batas</li>
                                                        <li><i class="mdi mdi-check-circle text-success me-2"></i>Pastikan NIP terisi dengan benar</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-left border-4 border-success policy-card h-100">
                                                <div class="card-body">
                                                    <h6 class="card-title fw-bold text-success mb-3">
                                                        <i class="mdi mdi-information me-2"></i>Informasi Wajib
                                                    </h6>
                                                    <ul class="list-unstyled mb-0 small">
                                                        <li class="mb-2"><i class="mdi mdi-check-circle text-success me-2"></i>Tanggal pelaksanaan lembur</li>
                                                        <li class="mb-2"><i class="mdi mdi-check-circle text-success me-2"></i>Shift kerja (1, 2, atau 3)</li>
                                                        <li class="mb-2"><i class="mdi mdi-check-circle text-success me-2"></i>Jam mulai dan jam selesai lembur</li>
                                                        <li class="mb-2"><i class="mdi mdi-check-circle text-success me-2"></i>Nama mesin (opsional)</li>
                                                        <li><i class="mdi mdi-check-circle text-success me-2"></i>Keperluan lembur yang jelas</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-left border-4 border-danger policy-card h-100">
                                                <div class="card-body">
                                                    <h6 class="card-title fw-bold text-danger mb-3">
                                                        <i class="mdi mdi-alert-circle me-2"></i>Hal yang Perlu Diperhatikan
                                                    </h6>
                                                    <ul class="list-unstyled mb-0 small">
                                                        <li class="mb-2"><i class="mdi mdi-close-circle text-danger me-2"></i>Jangan ajukan SPL terlalu jauh hari</li>
                                                        <li class="mb-2"><i class="mdi mdi-close-circle text-danger me-2"></i>Hindari pengajuan tanpa keperluan jelas</li>
                                                        <li class="mb-2"><i class="mdi mdi-close-circle text-danger me-2"></i>Tunggu approval dari atasan sebelum lembur</li>
                                                        <li class="mb-2"><i class="mdi mdi-close-circle text-danger me-2"></i>Tidak perlu cetak manual, approval digital</li>
                                                        <li><i class="mdi mdi-close-circle text-danger me-2"></i>Cek kembali data sebelum submit</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary">
                    <h4 class="card-title mb-0"><i class="mdi mdi-file-plus me-2"></i>Form Surat Perintah Lembur</h4>
                    <p class="card-subtitle mb-0 text-white-50 small">Isi form berikut untuk membuat SPL baru</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('hr.spl.store') }}" method="POST" id="splForm">
                        @csrf

                        <!-- Informasi Umum -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary"><i class="mdi mdi-information-outline me-2"></i>Informasi Umum</h5>
                                <hr>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">
                                    Tanggal <span class="text-danger">*</span>
                                    <i class="mdi mdi-help-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Tanggal pelaksanaan lembur. Minimal H-1 pengajuan."></i>
                                </label>
                                <input type="date" name="request_date" class="form-control @error('request_date') is-invalid @enderror"
                                       value="{{ old('request_date', date('Y-m-d')) }}" required>
                                @error('request_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">
                                    Shift <span class="text-danger">*</span>
                                    <i class="mdi mdi-help-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Pilih shift kerja karyawan (Shift 1, 2, atau 3)"></i>
                                </label>
                                <select name="shift" class="form-select form-control @error('shift') is-invalid @enderror" required>
                                    <option value="">Pilih Shift</option>
                                    <option value="1" {{ old('shift') == '1' ? 'selected' : '' }}>1 (Pagi)</option>
                                    <option value="2" {{ old('shift') == '2' ? 'selected' : '' }}>2 (Siang)</option>
                                    <option value="3" {{ old('shift') == '3' ? 'selected' : '' }}>3 (Malam)</option>
                                </select>
                                @error('shift')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">
                                    Jam Mulai <span class="text-danger">*</span>
                                    <i class="mdi mdi-help-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Jam mulai lembur (Format: HH:MM, contoh: 18:00)"></i>
                                </label>
                                <input type="time" name="start_time" class="form-control @error('start_time') is-invalid @enderror"
                                       value="{{ old('start_time') }}" placeholder="Contoh: 18:00" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">
                                    Jam Selesai <span class="text-danger">*</span>
                                    <i class="mdi mdi-help-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Jam selesai lembur (Format: HH:MM, contoh: 22:00)"></i>
                                </label>
                                <input type="time" name="end_time" class="form-control @error('end_time') is-invalid @enderror"
                                       value="{{ old('end_time') }}" placeholder="Contoh: 22:00" required>
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">
                                    Mesin
                                    <i class="mdi mdi-help-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Nama mesin yang akan dioperasikan selama lembur (Opsional)"></i>
                                </label>
                                <input type="text" name="mesin" class="form-control @error('mesin') is-invalid @enderror"
                                       value="{{ old('mesin') }}" placeholder="Contoh: Mesin CNC-01">
                                @error('mesin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">
                                    Jumlah Karyawan
                                    <i class="mdi mdi-help-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Total karyawan yang akan lembur"></i>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-account-group text-primary"></i></span>
                                    <input type="text" class="form-control bg-light" id="employee-count" value="0" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">
                                    Keperluan <span class="text-danger">*</span>
                                    <i class="mdi mdi-help-circle text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="Jelaskan alasan lembur secara detail agar atasan dapat approve"></i>
                                </label>
                                <textarea name="keperluan" class="form-control @error('keperluan') is-invalid @enderror" rows="3"
                                          placeholder="Contoh: Selesaikan target produksi batch 123 yang harus dikirim besok..." required>{{ old('keperluan') }}</textarea>
                                @error('keperluan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted"><i class="mdi mdi-information-outline"></i> Semakin jelas keperluan lembur, semakin cepat proses approval</small>
                            </div>
                        </div>

                        <!-- Daftar Karyawan -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary"><i class="mdi mdi-account-group me-2"></i>Daftar Karyawan Lembur</h5>
                                <hr>
                            </div>
                        </div>

                        <!-- Alert Reminder -->
                        <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                            <div class="d-flex align-items-start">
                                <i class="mdi mdi-lightbulb-on-outline fs-4 me-3 mt-1"></i>
                                <div>
                                    <h6 class="alert-heading fw-bold mb-1">Tips Pengisian Data Karyawan</h6>
                                    <ul class="mb-0 small">
                                        <li>Pilih dari <strong>Master Employee</strong> untuk karyawan dengan NIP terdaftar</li>
                                        <li>Atau pilih dari <strong>Users</strong> untuk karyawan yang memiliki akun sistem</li>
                                        <li>Bisa juga <strong>input manual</strong> jika nama tidak ada di daftar</li>
                                    </ul>
                                </div>
                                <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"><i class="mdi mdi-close"></i></button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="employees-table">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;" class="text-center bg-light">No</th>
                                        <th style="width: 75%;" class="bg-light">
                                            Nama Karyawan <span class="text-danger">*</span>
                                            <small class="text-muted fw-normal d-block">(Pilih dari dropdown atau input manual)</small>
                                        </th>
                                        <th style="width: 20%;" class="text-center bg-light">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="employees-tbody">
                                    <!-- Default 2 rows -->
                                    @for($i = 0; $i < 2; $i++)
                                        <tr data-index="{{ $i }}" class="employee-row-{{ $i }}">
                                            <td class="text-center">{{ $i + 1 }}</td>
                                            <td>
                                                <div class="mb-2">
                                                    <label class="small text-muted mb-1">
                                                        <i class="mdi mdi-database-search"></i> Dari Master Employee
                                                    </label>
                                                    <select class="form-control form-select form-select-sm employee-select" data-index="{{ $i }}">
                                                        <option value="">-- Pilih dari Master Employee --</option>
                                                        @foreach($payrollEmployees as $emp)
                                                            <option value="{{ $emp->Nip }}"
                                                                    data-name="{{ $emp->Nama }}"
                                                                    data-nip="{{ $emp->Nip }}">
                                                                {{ $emp->Nama }} ({{ $emp->Nip }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="small text-muted mb-1">
                                                        <i class="mdi mdi-account-search"></i> Dari Users
                                                    </label>
                                                    <select class="form-control form-select form-select-sm user-select" data-index="{{ $i }}">
                                                        <option value="">-- Atau Pilih dari Users --</option>
                                                        @foreach($users as $user)
                                                            <option value="{{ $user->id }}"
                                                                    data-name="{{ $user->name }}">
                                                                {{ $user->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-2">
                                                    <label class="small text-muted mb-1">
                                                        <i class="mdi mdi-keyboard"></i> Input Manual
                                                    </label>
                                                    <input type="text" name="employees[{{ $i }}][employee_name]"
                                                           class="form-control form-control-sm employee-name @error('employees.'.$i.'.employee_name') is-invalid @enderror"
                                                           placeholder="Atau tulis nama manual" required>
                                                </div>
                                                <input type="hidden" name="employees[{{ $i }}][employee_id]" class="employee-id">
                                                <input type="hidden" name="employees[{{ $i }}][nip]" class="employee-nip">
                                                <input type="hidden" name="employees[{{ $i }}][is_manual]" class="is-manual" value="false">
                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                    <small class="text-muted">
                                                        <i class="mdi mdi-information-outline"></i>
                                                        Pilih dari dropdown atau tulis manual
                                                    </small>
                                                    <span class="badge bg-secondary employee-status-badge-{{ $i }} d-none">
                                                        <i class="mdi mdi-pencil"></i> Manual
                                                    </span>
                                                    <span class="badge bg-success employee-selected-badge-{{ $i }} d-none">
                                                        <i class="mdi mdi-check-circle"></i> Terpilih
                                                    </span>
                                                </div>
                                                @error('employees.'.$i.'.employee_name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td class="text-center">
                                                @if($i > 0)
                                                    <button type="button" class="btn btn-sm btn-danger remove-employee-row" data-index="{{ $i }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus baris ini">
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                @else
                                                    <span class="text-muted small">Min. 1</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <button type="button" class="btn btn-outline-primary" id="add-employee-row">
                                    <i class="mdi mdi-plus"></i> Tambah Baris Karyawan
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="row">
                            <div class="col-12 text-end">
                                <a href="{{ route('hr.spl.index') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-close"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-info">
                                    <i class="mdi mdi-content-save"></i> Simpan SPL
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script src="{{ asset('sipo_krisan/public/news/plugins/bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/news/plugins/bootstrap/js/bootstrap.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('sipo_krisan/public/news/js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/news/js/waves.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/news/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/news/plugins/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/news/js/custom.min.js') }}"></script>

    <!-- Charts and Plugins -->
    <script src="{{ asset('sipo_krisan/public/news/plugins/sparkline/jquery.sparkline.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/news/plugins/raphael/raphael-min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/news/plugins/morrisjs/morris.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/news/plugins/styleswitcher/jQuery.style.switcher.js') }}"></script>

    <script>
        let employeeIndex = {{ count(old('employees', [])) > 0 ? count(old('employees', [])) : 2 }};


        // Function to update row numbers
        function updateRowNumbers() {
            $('#employees-tbody tr').each(function(index) {
                $(this).find('td:first-child').text(index + 1);
            });
            updateEmployeeCounter();
        }

        // Function to update employee counter
        function updateEmployeeCounter() {
            let count = 0;
            $('.employee-name').each(function() {
                if ($(this).val().trim()) {
                    count++;
                }
            });
            $('#employee-count').val(count);
            $('#employee-counter-badge').text(count);
        }

        // Auto-fill employee data when selecting from masteremployee or users
        $(document).on('change', '.employee-select, .user-select', function() {
            const select = $(this);
            const index = select.data('index');
            const row = select.closest('tr');
            const nameInput = row.find('.employee-name');
            const employeeIdInput = row.find('.employee-id');
            const employeeNipInput = row.find('.employee-nip');
            const isManualInput = row.find('.is-manual');
            const statusBadge = row.find('.employee-status-badge-' + index);
            const selectedBadge = row.find('.employee-selected-badge-' + index);

            // Clear other select
            if (select.hasClass('employee-select')) {
                row.find('.user-select').val('');
            } else {
                row.find('.employee-select').val('');
            }

            if (select.val()) {
                const selectedOption = select.find('option:selected');
                const name = selectedOption.data('name');
                const nip = selectedOption.data('nip') || '';
                const userId = select.hasClass('user-select') ? select.val() : '';

                nameInput.val(name);
                employeeIdInput.val(userId);
                if (nip) {
                    employeeNipInput.val(nip);
                }
                isManualInput.val('false');

                // Show selected badge, hide manual badge
                selectedBadge.removeClass('d-none');
                statusBadge.addClass('d-none');
            } else {
                selectedBadge.addClass('d-none');
            }
            updateEmployeeCounter();
        });

        // Allow manual input
        $(document).on('input', '.employee-name', function() {
            const row = $(this).closest('tr');
            const index = row.closest('tr').data('index');
            const isManualInput = row.find('.is-manual');
            const employeeSelect = row.find('.employee-select');
            const userSelect = row.find('.user-select');
            const statusBadge = row.find('.employee-status-badge-' + index);
            const selectedBadge = row.find('.employee-selected-badge-' + index);

            // If user types manually and selects are empty, mark as manual
            if ($(this).val() && !employeeSelect.val() && !userSelect.val()) {
                isManualInput.val('true');
                statusBadge.removeClass('d-none');
                selectedBadge.addClass('d-none');
            } else if (!$(this).val()) {
                statusBadge.addClass('d-none');
                selectedBadge.addClass('d-none');
            }
            updateEmployeeCounter();
        });


        // Add employee row
        $('#add-employee-row').on('click', function() {
            const rowCount = $('#employees-tbody tr').length;
            const newRow = `
                <tr data-index="${employeeIndex}" class="employee-row-${employeeIndex}">
                    <td class="text-center">${rowCount + 1}</td>
                    <td>
                        <div class="mb-2">
                            <label class="small text-muted mb-1">
                                <i class="mdi mdi-database-search"></i> Dari Master Employee
                            </label>
                            <select class="form-control form-select form-select-sm employee-select" data-index="${employeeIndex}">
                                <option value="">-- Pilih dari Master Employee --</option>
                                @foreach($payrollEmployees as $emp)
                                    <option value="{{ $emp->Nip }}"
                                            data-name="{{ $emp->Nama }}"
                                            data-nip="{{ $emp->Nip }}">
                                        {{ $emp->Nama }} ({{ $emp->Nip }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="small text-muted mb-1">
                                <i class="mdi mdi-account-search"></i> Dari Users
                            </label>
                            <select class="form-control form-select form-select-sm user-select" data-index="${employeeIndex}">
                                <option value="">-- Atau Pilih dari Users --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}"
                                            data-name="{{ $user->name }}">
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="small text-muted mb-1">
                                <i class="mdi mdi-keyboard"></i> Input Manual
                            </label>
                            <input type="text" name="employees[${employeeIndex}][employee_name]"
                                   class="form-control form-control-sm employee-name"
                                   placeholder="Atau tulis nama manual" required>
                        </div>
                        <input type="hidden" name="employees[${employeeIndex}][employee_id]" class="employee-id">
                        <input type="hidden" name="employees[${employeeIndex}][nip]" class="employee-nip">
                        <input type="hidden" name="employees[${employeeIndex}][is_manual]" class="is-manual" value="false">
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <small class="text-muted">
                                <i class="mdi mdi-information-outline"></i>
                                Pilih dari dropdown atau tulis manual
                            </small>
                            <span class="badge bg-secondary employee-status-badge-${employeeIndex} d-none">
                                <i class="mdi mdi-pencil"></i> Manual
                            </span>
                            <span class="badge bg-success employee-selected-badge-${employeeIndex} d-none">
                                <i class="mdi mdi-check-circle"></i> Terpilih
                            </span>
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-employee-row" data-index="${employeeIndex}" data-bs-toggle="tooltip" title="Hapus baris ini">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#employees-tbody').append(newRow);

            // Initialize tooltips for new elements
            var newTooltipTriggerList = [].slice.call(document.querySelectorAll(`[data-index="${employeeIndex}"][data-bs-toggle="tooltip"]`))
            var newTooltipList = newTooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            employeeIndex++;
            updateRowNumbers();
        });

        // Remove employee row
        $(document).on('click', '.remove-employee-row', function() {
            $(this).closest('tr').remove();
            updateRowNumbers();
        });

        // Form submission
        $('#splForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);

            // Validate at least one employee name is filled
            let hasEmployee = false;
            $('.employee-name').each(function() {
                if ($(this).val().trim()) {
                    hasEmployee = true;
                    return false; // break loop
                }
            });

            if (!hasEmployee) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Minimal harus ada 1 karyawan yang diisi!',
                    confirmButtonColor: '#4ecdc4'
                });
                return false;
            }

            // Build detail HTML
            let detailHtml = '<div class="text-left">' +
                      '<p>Apakah Anda yakin ingin menyimpan SPL ini?</p>' +
                      '<div class="alert alert-info mb-0">' +
                      '<strong>Rincian:</strong><br>' +
                      'Jumlah Karyawan: ' + $('#employee-count').val() + '<br>' +
                      'Shift: ' + $('select[name="shift"] option:selected').text() + '<br>' +
                      'Tanggal: ' + $('input[name="request_date"]').val();

            // Add jam if filled
            const startTime = $('input[name="start_time"]').val();
            const endTime = $('input[name="end_time"]').val();
            if (startTime || endTime) {
                detailHtml += '<br>Jam: ';
                if (startTime && endTime) {
                    detailHtml += startTime + ' - ' + endTime;
                } else if (startTime) {
                    detailHtml += 'Mulai: ' + startTime;
                } else if (endTime) {
                    detailHtml += 'Selesai: ' + endTime;
                }
            }

            detailHtml += '</div></div>';

            Swal.fire({
                icon: 'question',
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menyimpan SPL ini?',
                html: detailHtml,
                showCancelButton: true,
                confirmButtonText: '<i class="mdi mdi-check"></i> Ya, Simpan',
                cancelButtonText: '<i class="mdi mdi-close"></i> Batal',
                confirmButtonColor: '#4ecdc4',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Mohon tunggu',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Remove event listener and submit
                    form.off('submit');
                    form[0].submit();
                }
            });
        });

        // Initial employee counter update
        updateEmployeeCounter();

        // Update counter on employee name change
        $(document).on('blur', '.employee-name', function() {
            updateEmployeeCounter();
        });
    </script>
@endsection
