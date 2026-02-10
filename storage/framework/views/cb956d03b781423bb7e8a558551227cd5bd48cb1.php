<?php $__env->startSection('title'); ?>
    Tambah Checklist Kendaraan
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css')); ?>" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css')); ?>" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css')); ?>" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css')); ?>" rel="stylesheet"
        type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

    <style>
        .cust-col {
            white-space: nowrap;
        }

        /* Foto preview styling */
        #previewArea {
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }

        .preview-card {
            transition: transform 0.2s;
        }

        .preview-card:hover {
            transform: scale(1.02);
        }

        .preview-card img {
            border-radius: 8px 8px 0 0;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .file-input-wrapper input[type=file] {
            position: absolute;
            left: -9999px;
        }

        .file-input-label {
            display: inline-block;
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .file-input-label:hover {
            background-color: #0056b3;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
    Tambah Checklist Kendaraan
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>

    <body data-sidebar="colored">
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('content'); ?>
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Tambah Checklist Kendaraan</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">Tambah Checklist Kendaraan</li>
                </ol>
            </div>
        </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Checklist Kendaraan Keluar Operasional </h4>
                    <p class="card-title-desc">Isi form berikut untuk mencatat kendaraan yang keluar</p>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('security.vehicle-checklist.store')); ?>" method="POST" enctype="multipart/form-data" id="checklistForm">
                        <?php echo csrf_field(); ?>

                        <!-- Header Information -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h5 class="alert-heading"><i class="bx bx-info-circle"></i> Informasi</h5>
                                    <p class="mb-0">No. Urut: <strong><?php echo e($noUrut); ?></strong> | Tanggal: <strong><?php echo e(date('d/m/Y')); ?></strong> | Petugas: <strong><?php echo e(Auth::user()->name); ?></strong></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Lokasi: <span class="text-danger">*</span></label>
                                <select class="form-select form-control" name="lokasi" required>
                                    <option value="">Pilih Lokasi</option>
                                    <option value="1">Lokasi 19 (KRISANTHIUM)</option>
                                    <option value="2">Lokasi 23 (KRISANTHIUM)</option>
                                    <option value="3">Lokasi 15 (BERBEK)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Checklist Masuk/Keluar: <span class="text-danger">*</span></label>
                                <select class="form-select form-control" name="checklist_pada" required>
                                    <option value="">Pilih Checklist Masuk/Keluar</option>
                                    <option value="1">MASUK</option>
                                    <option value="2">KELUAR</option>
                                </select>
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <!-- Data Dasar -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0"><i class="bx bx-calendar"></i> Data Dasar</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control <?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   name="tanggal" required>
                                            <?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        

                                        <div class="mb-3">
                                            <label class="form-label" id="jamLabel">Jam Keluar <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control <?php $__errorArgs = ['jam_out'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   name="jam_out" id="jamInput" required>
                                            <?php $__errorArgs = ['jam_out'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Kendaraan & Driver -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0"><i class="bx bx-car"></i> Data Kendaraan & Driver</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Driver <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['nama_driver'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   name="nama_driver" value="<?php echo e(old('nama_driver')); ?>"
                                                   placeholder="Masukkan nama driver" required>
                                            <?php $__errorArgs = ['nama_driver'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Model Kendaraan <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['model_kendaraan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   name="model_kendaraan" value="<?php echo e(old('model_kendaraan')); ?>"
                                                   placeholder="Contoh: Daihatsu Gran Max, Toyota Avanza" required>
                                            <?php $__errorArgs = ['model_kendaraan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">No. Polisi <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control <?php $__errorArgs = ['no_polisi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   name="no_polisi" value="<?php echo e(old('no_polisi')); ?>"
                                                   placeholder="Contoh: L 1234 AB">
                                            <?php $__errorArgs = ['no_polisi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Data Awal -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0"><i class="bx bx-tachometer"></i> Data Awal Kendaraan</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label" id="kmLabel">KM <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control <?php $__errorArgs = ['km_awal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   name="km_awal" id="kmInput" value="<?php echo e(old('km_awal')); ?>"
                                                   placeholder="Masukkan KM" min="0" required>
                                            <?php $__errorArgs = ['km_awal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" id="bbmLabel">BBM (Persen) <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" class="form-control <?php $__errorArgs = ['bbm_awal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   name="bbm_awal" id="bbmInput" value="<?php echo e(old('bbm_awal')); ?>"
                                                   placeholder="Masukkan BBM" min="0" required>
                                            <?php $__errorArgs = ['bbm_awal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tujuan & Keterangan -->
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0"><i class="bx bx-map"></i> Tujuan & Keterangan</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Tujuan <span class="text-danger">*</span></label>
                                            <textarea class="form-control <?php $__errorArgs = ['tujuan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                      name="tujuan" rows="3" placeholder="Masukkan tujuan perjalanan" required><?php echo e(old('tujuan')); ?></textarea>
                                            <?php $__errorArgs = ['tujuan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Keterangan</label>
                                            <textarea class="form-control <?php $__errorArgs = ['keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                      name="keterangan" rows="3" placeholder="Keterangan tambahan (opsional)"><?php echo e(old('keterangan')); ?></textarea>
                                            <?php $__errorArgs = ['keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Foto -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0"><i class="bx bx-camera"></i> Upload Foto Kendaraan</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label class="form-label">Foto Dashboard/SPIDO/BBM<span class="text-danger">*</span></label>
                                                    <input type="file" class="form-control <?php $__errorArgs = ['foto_dashboard'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                           name="foto_dashboard" accept="image/*" required>
                                                    <div class="form-text">Upload foto dashboard/SPIDO untuk melihat KM dan BBM (opsional)</div>
                                                    <?php $__errorArgs = ['foto_dashboard'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                </div>
                                            </div>
                                        </div>

                                        

                                        <!-- Preview Area -->
                                        <div class="row mt-3" id="previewArea" style="display: none;">
                                            <div class="col-12">
                                                <h6>Preview Foto:</h6>
                                                <div class="row" id="previewContainer">
                                                    <!-- Preview images will be inserted here -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <button type="submit" class="btn btn-success btn-lg me-2">
                                            <i class="bx bx-save"></i> Simpan Checklist
                                        </button>
                                        <a href="<?php echo e(route('security.vehicle-checklist.index')); ?>" class="btn btn-secondary btn-lg">
                                            <i class="bx bx-arrow-back"></i> Kembali
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// SweetAlert untuk error message
<?php if(session('error')): ?>
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '<?php echo e(session('error')); ?>',
        confirmButtonText: 'OK',
        confirmButtonColor: '#d33'
    });
<?php endif; ?>

// SweetAlert untuk validation errors
<?php if($errors->any()): ?>
    let errorMessages = '';
    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        errorMessages += '• <?php echo e($error); ?>\n';
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    Swal.fire({
        icon: 'error',
        title: 'Validasi Gagal',
        text: errorMessages,
        confirmButtonText: 'OK',
        confirmButtonColor: '#d33'
    });
<?php endif; ?>

console.log('Script loaded!');
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded!');

    // Set jam otomatis ke waktu sekarang - DISABLED
    const jamInput = document.getElementById('jamInput');
    // if (!jamInput.value) {
    //     const now = new Date();
    //     const hours = String(now.getHours()).padStart(2, '0');
    //     const minutes = String(now.getMinutes()).padStart(2, '0');
    //     jamInput.value = `${hours}:${minutes}`;
    // }

    // Handle perubahan "Checklist Pada"
    const checklistPadaSelect = document.querySelector('select[name="checklist_pada"]');
    const jamLabel = document.getElementById('jamLabel');
    const kmLabel = document.getElementById('kmLabel');
    const bbmLabel = document.getElementById('bbmLabel');

    function updateFormLabels() {
        const selectedValue = checklistPadaSelect.value;

        if (selectedValue === '1') { // AWAL MASUK
            jamLabel.innerHTML = 'Jam Masuk <span class="text-danger">*</span>';
            kmLabel.innerHTML = 'KM <span class="text-danger">*</span>';
            bbmLabel.innerHTML = 'BBM (Persen) <span class="text-danger">*</span>';

            // Update input names untuk AWAL MASUK
            jamInput.name = 'jam_in';
            document.getElementById('kmInput').name = 'km_akhir';
            document.getElementById('bbmInput').name = 'bbm_akhir';

        } else if (selectedValue === '2') { // AKHIR KELUAR
            jamLabel.innerHTML = 'Jam Keluar <span class="text-danger">*</span>';
            kmLabel.innerHTML = 'KM <span class="text-danger">*</span>';
            bbmLabel.innerHTML = 'BBM (Persen) <span class="text-danger">*</span>';

            // Update input names untuk AKHIR KELUAR
            jamInput.name = 'jam_out';
            document.getElementById('kmInput').name = 'km_awal';
            document.getElementById('bbmInput').name = 'bbm_awal';
        }
    }

    // Event listener untuk perubahan dropdown
    checklistPadaSelect.addEventListener('change', updateFormLabels);

    // Set default jika sudah ada value
    if (checklistPadaSelect.value) {
        updateFormLabels();
    }

    // Auto-set shift berdasarkan jam
    const shiftSelect = document.querySelector('select[name="shift"]');
    if (!shiftSelect.value) {
        const currentHour = new Date().getHours();
        if (currentHour >= 6 && currentHour < 14) {
            shiftSelect.value = 'pagi';
        } else if (currentHour >= 14 && currentHour < 22) {
            shiftSelect.value = 'siang';
        } else {
            shiftSelect.value = 'malam';
        }
    }

    // Form validation dengan AJAX
    const form = document.getElementById('checklistForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default submit

        const kmAwal = parseInt(document.querySelector('input[name="km_awal"]').value);
        const bbmAwal = parseFloat(document.querySelector('input[name="bbm_awal"]').value);

        // Validasi client-side
        if (kmAwal < 0) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'KM Awal tidak boleh negatif',
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33'
            });
            return false;
        }

        if (bbmAwal < 0) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'BBM Awal tidak boleh negatif',
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33'
            });
            return false;
        }

        // Validasi required fields
        const requiredFields = [
            { name: 'tanggal', label: 'Tanggal' },
            { name: 'nama_driver', label: 'Nama Driver' },
            { name: 'model_kendaraan', label: 'Model Kendaraan' },
            { name: 'no_polisi', label: 'No. Polisi' },
            { name: 'jam_out', label: 'Jam Keluar' },
            { name: 'km_awal', label: 'KM Awal' },
            { name: 'bbm_awal', label: 'BBM Awal' },
            { name: 'tujuan', label: 'Tujuan' }
        ];

        for (const field of requiredFields) {
            const input = document.querySelector(`input[name="${field.name}"], textarea[name="${field.name}"]`);
            if (!input || !input.value.trim()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: `${field.label} wajib diisi`,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                });
                input?.focus();
                return false;
            }
        }

        // Validasi foto wajib
        // const fotoKondisi = document.querySelector('input[name="foto_kondisi"]');
        // if (!fotoKondisi.files || fotoKondisi.files.length === 0) {
        //     Swal.fire({
        //         icon: 'error',
        //         title: 'Validasi Gagal',
        //         text: 'Foto kondisi kendaraan wajib diupload',
        //         confirmButtonText: 'OK',
        //         confirmButtonColor: '#d33'
        //     });
        //     return false;
        // }

        // Konfirmasi simpan
        Swal.fire({
            title: 'Yakin ingin menyimpan?',
            text: "Data checklist kendaraan akan disimpan!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Menyimpan Data...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // AJAX Submit
                const formData = new FormData(form);

                // Debug: Log form data
                console.log('Form action:', form.action);
                console.log('Form data entries:');
                for (let [key, value] of formData.entries()) {
                    console.log(key, value);
                }

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (response.ok) {
                        return response.text();
                    }
                    throw new Error('Network response was not ok');
                })
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data checklist kendaraan berhasil disimpan',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        window.location.href = '<?php echo e(route("security.vehicle-checklist.index")); ?>';
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan saat menyimpan data',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                });
            }
        });
    });

    // Auto uppercase untuk no polisi
    const nopolInput = document.querySelector('input[name="no_polisi"]');
    if (nopolInput) {
        nopolInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }

    // Auto capitalize untuk nama driver
    const namaDriverInput = document.querySelector('input[name="nama_driver"]');
    if (namaDriverInput) {
        namaDriverInput.addEventListener('input', function() {
            this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
        });
    }

    // Foto preview functionality
    const fileInputs = document.querySelectorAll('input[type="file"]');
    const previewArea = document.getElementById('previewArea');
    const previewContainer = document.getElementById('previewContainer');

    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validasi ukuran file (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: 'Ukuran file maksimal 5MB',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                    this.value = '';
                    return;
                }

                // Validasi tipe file
                if (!file.type.startsWith('image/')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format File Tidak Valid',
                        text: 'Hanya file gambar yang diperbolehkan',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                    this.value = '';
                    return;
                }

                // Tampilkan preview
                showImagePreview(file, this.name);
            }
        });
    });

    function showImagePreview(file, inputName) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Hapus preview lama untuk input yang sama
            const existingPreview = document.querySelector(`[data-input="${inputName}"]`);
            if (existingPreview) {
                existingPreview.remove();
            }

            // Buat preview baru
            const previewDiv = document.createElement('div');
            previewDiv.className = 'col-md-3 mb-3';
            previewDiv.setAttribute('data-input', inputName);

            const label = getInputLabel(inputName);
            previewDiv.innerHTML = `
                <div class="card preview-card">
                    <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="Preview">
                    <div class="card-body p-2">
                        <h6 class="card-title text-truncate" title="${label}">${label}</h6>
                        <small class="text-muted">${(file.size / 1024).toFixed(1)} KB</small>
                        <button type="button" class="btn btn-sm btn-danger mt-1" onclick="removePreview('${inputName}')">
                            <i class="bx bx-x"></i> Hapus
                        </button>
                    </div>
                </div>
            `;

            previewContainer.appendChild(previewDiv);
            previewArea.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

    function getInputLabel(inputName) {
        const labels = {
            'foto_kondisi': 'Foto Kondisi Kendaraan',
            'foto_dashboard': 'Foto Dashboard/SPIDO',
            'foto_driver': 'Foto Driver & Kendaraan',
            'foto_lainnya': 'Foto Lainnya'
        };
        return labels[inputName] || 'Preview';
    }

    // Global function untuk hapus preview
    window.removePreview = function(inputName) {
        const preview = document.querySelector(`[data-input="${inputName}"]`);
        if (preview) {
            preview.remove();
        }

        // Reset input file
        const input = document.querySelector(`input[name="${inputName}"]`);
        if (input) {
            input.value = '';
        }

        // Sembunyikan preview area jika tidak ada preview lagi
        const remainingPreviews = document.querySelectorAll('[data-input]');
        if (remainingPreviews.length === 0) {
            previewArea.style.display = 'none';
        }
    };
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/main/security/vehicle-checklist/create.blade.php ENDPATH**/ ?>