<!-- Form Permintaan Membawa Kendaraan/Inventaris -->
<!-- Enhanced Information & Guide Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="accordion" id="vehicleReminderAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="vehicleReminderHeading">
                    <button class="btn btn-success accordion-button collapsed text-white" style="width: 100%;" type="button"
                        data-toggle="collapse" data-target="#vehicleReminderCollapse" aria-expanded="false"
                        aria-controls="vehicleReminderCollapse">
                        <i class="mdi mdi-car-info me-2"></i>
                        <strong>Panduan & Informasi Peminjaman Kendaraan/Inventaris</strong>
                    </button>
                </h2>
                <div id="vehicleReminderCollapse" class="accordion-collapse collapse" aria-labelledby="vehicleReminderHeading"
                    data-parent="#vehicleReminderAccordion">
                    <div class="accordion-body">
                        <!-- Quick Stats Cards -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="card border-info shadow-sm h-100">
                                    <div class="card-body text-center">
                                        <i class="mdi mdi-clock-alert-outline text-info fs-1 mb-2"></i>
                                        <h6 class="fw-bold text-info mb-1">H-1 Pengajuan</h6>
                                        <p class="small text-muted mb-0">Ajukan 1 hari sebelumnya</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-warning shadow-sm h-100">
                                    <div class="card-body text-center">
                                        <i class="mdi mdi-file-document-check text-warning fs-1 mb-2"></i>
                                        <h6 class="fw-bold text-warning mb-1">Wajib Approval</h6>
                                        <p class="small text-muted mb-0">Persetujuan atasan diperlukan</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-primary shadow-sm h-100">
                                    <div class="card-body text-center">
                                        <i class="mdi mdi-laptop text-primary fs-1 mb-2"></i>
                                        <h6 class="fw-bold text-primary mb-1">Laptop 1 Bulan</h6>
                                        <p class="small text-muted mb-0">Masa pinjam maksimal</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-success shadow-sm h-100">
                                    <div class="card-body text-center">
                                        <i class="mdi mdi-account-check text-success fs-1 mb-2"></i>
                                        <h6 class="fw-bold text-success mb-1">Wajib Kembalikan</h6>
                                        <p class="small text-muted mb-0">Dalam kondisi baik</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Important Reminders -->
                        <div class="alert alert-warning border-0 mb-4">
                            <div class="d-flex align-items-start">
                                <i class="mdi mdi-alert-circle text-warning fs-4 me-3 flex-shrink-0"></i>
                                <div>
                                    <h6 class="fw-bold text-warning-emphasis mb-2">Ketentuan Umum Peminjaman</h6>
                                    <ul class="small mb-0 text-muted">
                                        <li>Pengajuan harus dilakukan minimal H-1 sebelum tanggal penggunaan</li>
                                        <li>Wajib mendapat persetujuan atasan/department head</li>
                                        <li>Kendaraan/inventaris harus dikembalikan dalam kondisi baik</li>
                                        <li>Peminjaman laptop maksimal 1 bulan dan dapat diperpanjang</li>
                                        <li>Harap menjaga dengan baik dan bertanggung jawab atas barang yang dipinjam</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Policy Cards -->
                        <h6 class="fw-bold mb-3 text-secondary">
                            <i class="mdi mdi-information me-2"></i>Kebijakan Peminjaman
                        </h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="card border-info shadow-sm h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="rounded-circle bg-info bg-opacity-10 p-2 me-2">
                                                <i class="mdi mdi-car text-info fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-0">Kendaraan</h6>
                                                <span class="badge bg-info bg-opacity-25 text-info mt-1">Mobil/Motor</span>
                                            </div>
                                        </div>
                                        <div class="small text-muted">
                                            <div><i class="mdi mdi-clock-outline me-1"></i><strong>Pengajuan:</strong> Minimal H-1</div>
                                            <div><i class="mdi mdi-file-document-outline me-1"></i><strong>Wajib:</strong> No. polisi terdaftar</div>
                                            <div><i class="mdi mdi-account-star me-1"></i><strong>Approval:</strong> Atasan & GA</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-success shadow-sm h-100">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="rounded-circle bg-success bg-opacity-10 p-2 me-2">
                                                <i class="mdi mdi-package-variant text-success fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-0">Inventaris</h6>
                                                <span class="badge bg-success bg-opacity-25 text-success-emphasis mt-1">Peralatan/Barang</span>
                                            </div>
                                        </div>
                                        <div class="small text-muted">
                                            <div><i class="mdi mdi-clock-outline me-1"></i><strong>Pengajuan:</strong> Minimal H-1</div>
                                            <div><i class="mdi mdi-barcode me-1"></i><strong>Wajib:</strong> Kode inventaris</div>
                                            <div><i class="mdi mdi-account-star me-1"></i><strong>Approval:</strong> Atasan</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Laptop Policy Card -->
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <div class="card border-primary shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-2">
                                                <i class="mdi mdi-laptop text-primary fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-0">Laptop Company</h6>
                                                <span class="badge bg-primary bg-opacity-25 text-primary mt-1">Peminjaman Khusus</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="small text-muted">
                                                    <div><i class="mdi mdi-clock-outline me-1"></i><strong>Masa Pinjam:</strong> Maksimal 1 bulan</div>
                                                    <div><i class="mdi mdi-calendar-check me-1"></i><strong>Perpanjangan:</strong> Bisa diajukan</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="small text-muted">
                                                    <div><i class="mdi mdi-file-document-outline me-1"></i><strong>Wajib:</strong> Surat tugas/keperluan</div>
                                                    <div><i class="mdi mdi-account-star me-1"></i><strong>Approval:</strong> IT & Atasan</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="small text-muted">
                                                    <div><i class="mdi mdi-check-circle-outline me-1"></i><strong>Kondisi:</strong> Harus baik saat kembali</div>
                                                    <div><i class="mdi mdi-refresh me-1"></i><strong>Maintenance:</strong> Wajib update OS</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Section -->
                        <div class="p-3 border rounded bg-light bg-opacity-50 mb-3">
                            <h6 class="fw-bold mb-3 text-secondary">
                                <i class="mdi mdi-frequently-asked-questions me-2"></i>Pertanyaan yang Sering Diajukan (FAQ)
                            </h6>
                            <div class="accordion" id="vehicleFaqAccordion">
                                <div class="accordion-item border-0 mb-2">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-semibold py-2 px-3 bg-white shadow-sm" type="button"
                                            data-toggle="collapse" data-target="#vfaq1">
                                            <i class="mdi mdi-help-circle-outline me-2 text-warning"></i>
                                            Berapa lama masa peminjaman maksimal?
                                        </button>
                                    </h2>
                                    <div id="vfaq1" class="accordion-collapse collapse" data-parent="#vehicleFaqAccordion">
                                        <div class="accordion-body px-3 py-2 bg-white">
                                            Untuk kendaraan dan inventaris, sesuai keperluan (harian/mingguan). Untuk laptop, maksimal 1 bulan dan dapat diperpanjang dengan pengajuan ulang.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item border-0 mb-2">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-semibold py-2 px-3 bg-white shadow-sm" type="button"
                                            data-toggle="collapse" data-target="#vfaq2">
                                            <i class="mdi mdi-help-circle-outline me-2 text-warning"></i>
                                            Apakah bisa memperpanjang masa peminjaman?
                                        </button>
                                    </h2>
                                    <div id="vfaq2" class="accordion-collapse collapse" data-parent="#vehicleFaqAccordion">
                                        <div class="accordion-body px-3 py-2 bg-white">
                                            Ya, Anda dapat mengajukan perpanjangan dengan membuat form pengajuan baru sebelum masa peminjaman berakhir. Approval akan diberikan berdasarkan ketersediaan dan alasan perpanjangan.
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item border-0">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed fw-semibold py-2 px-3 bg-white shadow-sm" type="button"
                                            data-toggle="collapse" data-target="#vfaq3">
                                            <i class="mdi mdi-help-circle-outline me-2 text-warning"></i>
                                            Apa yang harus dilakukan jika terjadi kerusakan?
                                        </button>
                                    </h2>
                                    <div id="vfaq3" class="accordion-collapse collapse" data-parent="#vehicleFaqAccordion">
                                        <div class="accordion-body px-3 py-2 bg-white">
                                            Segera laporkan ke GA (untuk kendaraan) atau IT Department (untuk laptop/inventaris). Jangan mencoba memperbaiki sendiri. Dokumentasikan kejadian untuk proses klaim asuransi (jika ada).
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="alert alert-success border-0 shadow-sm mb-0">
                            <div class="d-flex align-items-start">
                                <i class="mdi mdi-headset text-success fs-3 me-3"></i>
                                <div>
                                    <h6 class="fw-bold alert-heading mb-2">Butuh Bantuan?</h6>
                                    <p class="small mb-2">Jika ada pertanyaan atau kendala terkait peminjaman, silakan hubungi:</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="small mb-2">
                                                <div><i class="mdi mdi-phone me-2"></i><strong>GA (Kendaraan):</strong> ext. 102</div>
                                                <div><i class="mdi mdi-phone me-2"></i><strong>IT (Laptop):</strong> ext. 103</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="small">
                                                <div><i class="mdi mdi-email me-2"></i>Email: <strong>ga@krisan.co.id</strong></div>
                                                <div><i class="mdi mdi-clock me-2"></i>Jam: <strong>08:00 - 17:00</strong></div>
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

<!-- Redirect to new vehicle-asset module -->
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-info border-0 shadow-sm">
            <div class="d-flex align-items-start">
                <i class="mdi mdi-information-outline fs-4 me-3 flex-shrink-0"></i>
                <div class="flex-grow-1">
                    <h6 class="alert-heading fw-bold mb-2">Informasi Penting</h6>
                    <p class="mb-3">Form permintaan kendaraan/inventaris telah dipindahkan ke modul terpisah untuk alur approval yang lebih baik dan terstruktur.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('hr.vehicle-asset.create', ['type' => 'vehicle']) }}" class="btn btn-primary">
                            <i class="mdi mdi-car me-2"></i>Permintaan Kendaraan
                        </a>
                        <a href="{{ route('hr.vehicle-asset.create', ['type' => 'asset']) }}" class="btn btn-success">
                            <i class="mdi mdi-package-variant me-2"></i>Permintaan Inventaris
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .vehicle-info-card {
        transition: all 0.3s ease;
    }
    .vehicle-info-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.1) !important;
    }
</style>

<!-- Data Pemohon Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="mdi mdi-account-circle me-2"></i>Data Pemohon
                    </h5>
                    <span class="badge bg-light text-secondary border">
                        <i class="mdi mdi-information-outline me-1"></i>Lihat panduan di atas
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="mdi mdi-account me-1 text-muted"></i>Nama
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $data['name'] ?? auth()->user()->name) }}" readonly>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="mdi mdi-office-building me-1 text-muted"></i>Bagian
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="department" class="form-control @error('department') is-invalid @enderror"
                               value="{{ old('department', $data['department'] ?? auth()->user()->divisiUser->divisi ?? '') }}" readonly>
                        @error('department')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Kendaraan/Inventaris Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="mdi mdi-car me-2"></i>Data Kendaraan/Inventaris
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="mdi mdi-tag me-1 text-muted"></i>Jenis Kendaraan/Barang
                            <span class="text-danger">*</span>
                            <i class="mdi mdi-help-circle text-info ms-1" data-toggle="tooltip"
                                data-placement="right" title="Pilih kendaraan, inventaris, atau laptop yang akan dipinjam"></i>
                        </label>
                        <select name="vehicle_item_type" class="form-select form-control @error('vehicle_item_type') is-invalid @enderror" required onchange="toggleAssetFields()">
                            <option value="">Pilih Jenis</option>
                            <optgroup label="🚗 Kendaraan">
                                @if(isset($data['vehicles']))
                                    @foreach($data['vehicles'] as $vehicle)
                                        <option value="Kendaraan - {{ $vehicle->vehicle_type }} - {{ $vehicle->brand }} {{ $vehicle->model }}"
                                                data-type="vehicle" data-license="{{ $vehicle->license_plate }}"
                                                {{ old('vehicle_item_type', $data['vehicle_item_type'] ?? '') == "Kendaraan - {$vehicle->vehicle_type} - {$vehicle->brand} {$vehicle->model}" ? 'selected' : '' }}>
                                            {{ $vehicle->vehicle_type }} - {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->license_plate }})
                                        </option>
                                    @endforeach
                                @endif
                            </optgroup>
                            <optgroup label="📦 Inventaris">
                                @if(isset($data['assets']))
                                    @foreach($data['assets'] as $asset)
                                        <option value="Inventaris - {{ $asset->asset_type }} - {{ $asset->asset_name }}"
                                                data-type="inventory" data-license=""
                                                {{ old('vehicle_item_type', $data['vehicle_item_type'] ?? '') == "Inventaris - {$asset->asset_type} - {$asset->asset_name}" ? 'selected' : '' }}>
                                            {{ $asset->asset_type }} - {{ $asset->asset_name }} ({{ $asset->asset_code }})
                                        </option>
                                    @endforeach
                                @endif
                            </optgroup>
                            <optgroup label="💻 Laptop">
                                <option value="Laptop"
                                        data-type="laptop" data-license=""
                                        {{ old('vehicle_item_type', $data['vehicle_item_type'] ?? '') == 'Laptop' ? 'selected' : '' }}>
                                    Laptop Company
                                </option>
                            </optgroup>
                        </select>
                        @error('vehicle_item_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted">
                            <i class="mdi mdi-lightbulb-on-outline me-1"></i>
                            Pilih jenis barang yang akan dipinjam
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="mdi mdi-card-text me-1 text-muted"></i>No. Polisi
                            <i class="mdi mdi-help-circle text-info ms-1" data-toggle="tooltip"
                                data-placement="right" title="Nomor polisi kendaraan (otomatis terisi)"></i>
                        </label>
                        <input type="text" name="license_plate" id="license_plate" class="form-control @error('license_plate') is-invalid @enderror"
                               value="{{ old('license_plate', $data['license_plate'] ?? '') }}" readonly placeholder="Otomatis terisi">
                        @error('license_plate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted">
                            <i class="mdi mdi-information-outline me-1"></i>
                            Terisi otomatis berdasarkan kendaraan yang dipilih
                        </div>
                    </div>
                </div>

                <!-- Type-specific info alerts -->
                <div id="vehicle-info" class="alert alert-info border-0 mb-3" style="display: none;">
                    <div class="d-flex align-items-center">
                        <i class="mdi mdi-car fs-4 me-3"></i>
                        <div>
                            <strong>Info Kendaraan:</strong> Pastikan untuk mengisi tanggal penggunaan dan tujuan perjalanan dengan lengkap.
                        </div>
                    </div>
                </div>

                <div id="inventory-info" class="alert alert-success border-0 mb-3" style="display: none;">
                    <div class="d-flex align-items-center">
                        <i class="mdi mdi-package-variant fs-4 me-3"></i>
                        <div>
                            <strong>Info Inventaris:</strong> Pastikan untuk mengembalikan inventaris dalam kondisi baik setelah digunakan.
                        </div>
                    </div>
                </div>

                <div id="laptop-info" class="alert alert-primary border-0 mb-3" style="display: none;">
                    <div class="d-flex align-items-center">
                        <i class="mdi mdi-laptop fs-4 me-3"></i>
                        <div>
                            <strong>Info Laptop:</strong> Masa pinjam maksimal 1 bulan. Tanggal mulai dan selesai akan dihitung otomatis.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Peminjaman Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="mdi mdi-calendar-range me-2"></i>Detail Peminjaman
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="mdi mdi-help-circle me-1 text-muted"></i>Keperluan
                            <span class="text-danger">*</span>
                            <i class="mdi mdi-help-circle text-info ms-1" data-toggle="tooltip"
                                data-placement="right" title="Pilih tujuan penggunaan kendaraan/inventaris"></i>
                        </label>
                        <select name="purpose_type" class="form-select form-control @error('purpose_type') is-invalid @enderror" required>
                            <option value="">Pilih Keperluan</option>
                            <option value="Dinas" {{ old('purpose_type', $data['purpose_type'] ?? '') == 'Dinas' ? 'selected' : '' }}>🏢 Dinas</option>
                            <option value="Pengiriman" {{ old('purpose_type', $data['purpose_type'] ?? '') == 'Pengiriman' ? 'selected' : '' }}>📦 Pengiriman</option>
                            <option value="Pribadi" {{ old('purpose_type', $data['purpose_type'] ?? '') == 'Pribadi' ? 'selected' : '' }}>👤 Pribadi</option>
                        </select>
                        @error('purpose_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Single date field for vehicle & inventory -->
                    <div class="col-md-6" id="single_date_field">
                        <label class="form-label fw-semibold">
                            <i class="mdi mdi-calendar-today me-1 text-muted"></i>Tanggal
                            <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror"
                               value="{{ old('date', $data['date'] ?? '') }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted">
                            <i class="mdi mdi-information-outline me-1"></i>
                            Tanggal penggunaan kendaraan/inventaris
                        </div>
                    </div>
                </div>

                <!-- Date range fields for laptop -->
                <div class="row g-3 mb-3" id="laptop_date_fields" style="display: none;">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="mdi mdi-calendar-check me-1 text-muted"></i>Tanggal Mulai Pinjam
                            <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date', $data['start_date'] ?? '') }}">
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted">
                            <i class="mdi mdi-lightbulb-on-outline me-1"></i>
                            Masa pinjam maksimal 1 bulan
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="mdi mdi-calendar-clock me-1 text-muted"></i>Tanggal Selesai Pinjam
                            <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror"
                               value="{{ old('end_date', $data['end_date'] ?? '') }}" readonly>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted">
                            <i class="mdi mdi-auto-fix me-1"></i>
                            Otomatis di-set 1 bulan setelah tanggal mulai
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Uraian & Tujuan Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 text-primary">
                    <i class="mdi mdi-text-box me-2"></i>Uraian & Tujuan
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            <i class="mdi mdi-text-box-outline me-1 text-muted"></i>Uraian Kegiatan
                            <span class="text-danger">*</span>
                            <i class="mdi mdi-help-circle text-info ms-1" data-toggle="tooltip"
                                data-placement="right" title="Jelaskan detail keperluan penggunaan kendaraan/inventaris"></i>
                        </label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="Contoh: Menghadiri meeting dengan client PT. XYZ di Jakarta untuk presentasi produk baru" required>{{ old('description', $data['description'] ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted">
                            <i class="mdi mdi-lightbulb-on-outline me-1"></i>
                            Jelaskan secara detail keperluan penggunaan barang yang dipinjam
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            <i class="mdi mdi-map-marker me-1 text-muted"></i>Tujuan
                            <span class="text-danger">*</span>
                            <i class="mdi mdi-help-circle text-info ms-1" data-toggle="tooltip"
                                data-placement="right" title="Lokasi tujuan penggunaan kendaraan/inventaris"></i>
                        </label>
                        <input type="text" name="destination" class="form-control @error('destination') is-invalid @enderror"
                               value="{{ old('destination', $data['destination'] ?? '') }}" placeholder="Contoh: Jakarta - Gedung Menara Sudirman Lt. 15" required>
                        @error('destination')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-muted">
                            <i class="mdi mdi-information-outline me-1"></i>
                            Sertakan lokasi yang lengkap dan jelas
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});

function toggleAssetFields() {
    const select = document.querySelector('select[name="vehicle_item_type"]');
    const licensePlateField = document.getElementById('license_plate');
    const selectedOption = select.options[select.selectedIndex];
    const laptopDateFields = document.getElementById('laptop_date_fields');
    const singleDateField = document.getElementById('single_date_field');
    const dateField = document.getElementById('date');
    const startDateField = document.getElementById('start_date');
    const endDateField = document.getElementById('end_date');

    // Hide all info alerts first
    document.getElementById('vehicle-info').style.display = 'none';
    document.getElementById('inventory-info').style.display = 'none';
    document.getElementById('laptop-info').style.display = 'none';

    if (selectedOption.dataset.type === 'vehicle') {
        licensePlateField.value = selectedOption.dataset.license;
        licensePlateField.readOnly = true;
        laptopDateFields.style.display = 'none';
        singleDateField.style.display = 'block';
        document.getElementById('vehicle-info').style.display = 'block';
        dateField.required = true;
        startDateField.required = false;
        endDateField.required = false;
    } else if (selectedOption.dataset.type === 'laptop') {
        // Tampilkan field tanggal khusus laptop
        licensePlateField.value = '';
        licensePlateField.readOnly = true;
        laptopDateFields.style.display = 'block';
        singleDateField.style.display = 'none';
        document.getElementById('laptop-info').style.display = 'block';
        dateField.required = false;
        startDateField.required = true;
        endDateField.required = true;

        // Set min date untuk start_date (hari ini)
        const today = new Date().toISOString().split('T')[0];
        startDateField.setAttribute('min', today);
        startDateField.removeAttribute('readonly');
    } else if (selectedOption.dataset.type === 'inventory') {
        licensePlateField.value = '';
        licensePlateField.readOnly = true;
        laptopDateFields.style.display = 'none';
        singleDateField.style.display = 'block';
        document.getElementById('inventory-info').style.display = 'block';
        dateField.required = true;
        startDateField.required = false;
        endDateField.required = false;
    } else {
        licensePlateField.value = '';
        licensePlateField.readOnly = true;
        laptopDateFields.style.display = 'none';
        singleDateField.style.display = 'block';
        dateField.required = true;
        startDateField.required = false;
        endDateField.required = false;
    }
}

// Auto-set end_date 1 bulan setelah start_date untuk laptop
function setEndDateFromStartDate() {
    const startDateField = document.getElementById('start_date');
    const endDateField = document.getElementById('end_date');

    if (startDateField.value) {
        const startDate = new Date(startDateField.value);
        const endDate = new Date(startDate);
        endDate.setMonth(endDate.getMonth() + 1); // Tambah 1 bulan

        endDateField.value = endDate.toISOString().split('T')[0];

        // Show duration info
        const durationInfo = document.createElement('small');
        durationInfo.className = 'text-success fw-bold';
        durationInfo.innerHTML = '<i class="mdi mdi-check-circle"></i> Masa pinjam: 1 bulan';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleAssetFields();

    const startDateField = document.getElementById('start_date');
    if (startDateField) {
        startDateField.addEventListener('change', function() {
            setEndDateFromStartDate();
        });
    }

    // Calculate initial end date if start date is already set
    if (startDateField && startDateField.value) {
        setEndDateFromStartDate();
    }
});
</script>
@endpush
