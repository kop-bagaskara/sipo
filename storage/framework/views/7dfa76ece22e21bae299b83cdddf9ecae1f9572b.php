<?php $__env->startSection('title'); ?>
    Tambah Laporan Aktivitas Harian
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .activity-row {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
            background-color: #f8f9fa;
        }

        .required-field {
            border-left: 4px solid #dc3545;
        }

        .optional-field {
            border-left: 4px solid #28a745;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-title'); ?>
    Tambah Laporan Aktivitas Harian
<?php $__env->stopSection(); ?>

<?php $__env->startSection('body'); ?>

    <body data-sidebar="colored">
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('content'); ?>
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Tambah Laporan Aktivitas Harian</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Security</a></li>
                    <li class="breadcrumb-item active">Tambah Laporan Aktivitas Harian</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Form Laporan Aktivitas Harian</h4>
                        <p class="card-title-desc">Isi data laporan aktivitas harian security</p>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo e(route('security.daily-activity.store')); ?>" method="POST" id="activityForm">
                            <?php echo csrf_field(); ?>

                            <!-- Header Information -->
                            <div class="row mb-4">

                                <?php if($errors->any()): ?>
                                    <div class="alert alert-danger">
                                        <ul>
                                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li><?php echo e($error); ?></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                                <div class="col-md-12">
                                    <div class="card border required-field">
                                        <div class="card-header bg-light">
                                            <h5 class="card-title mb-0"><i class="mdi mdi-calendar"></i> Informasi Dasar
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">Tanggal <span
                                                                class="text-danger">*</span></label>
                                                        <input type="date"
                                                            class="form-control <?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                            name="tanggal" value="<?php echo e(old('tanggal', date('Y-m-d'))); ?>"
                                                            required>
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
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">Shift <span
                                                                class="text-danger">*</span></label>
                                                        <select
                                                            class="form-select form-control <?php $__errorArgs = ['shift'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                            name="shift" required>
                                                            <option value="">Pilih Shift</option>
                                                            <option value="I"
                                                                <?php echo e(old('shift') == 'I' ? 'selected' : ''); ?>>I (Pagi)
                                                            </option>
                                                            <option value="II"
                                                                <?php echo e(old('shift') == 'II' ? 'selected' : ''); ?>>II (Sore)
                                                            </option>
                                                            <option value="III"
                                                                <?php echo e(old('shift') == 'III' ? 'selected' : ''); ?>>III (Malam)
                                                            </option>
                                                        </select>
                                                        <?php $__errorArgs = ['shift'];
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
                                                <div class="col-md-6">
                                                    <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                                                    <select class="form-select form-control" name="lokasi" required>
                                                        <option value="">Pilih Lokasi</option>
                                                        <option value="19">19 - Krisanthium</option>
                                                        <option value="23">23 - Krisanthium</option>
                                                        <option value="15">15 - Berbek</option>
                                                    </select>
                                                    
                                                </div>
                                                
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Personil Jaga <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text"
                                                            class="form-control <?php $__errorArgs = ['personil_jaga'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                            name="personil_jaga" value="<?php echo e(old('personil_jaga')); ?>"
                                                            placeholder="Contoh: Masrur + Lidya" required>
                                                        <?php $__errorArgs = ['personil_jaga'];
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
                                            <div class="row">
                                                

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Activity Entries -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card border required-field">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0"><i class="mdi mdi-clipboard-list"></i> Daftar
                                                Aktivitas</h5>
                                            <button type="button" class="btn btn-success btn-sm" id="addActivityBtn">
                                                <i class="mdi mdi-plus"></i> Tambah Aktivitas
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div id="activityContainer">
                                                <!-- Activity rows will be added here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label">Tanda Tangan</label>
                                        <div class="row">
                                            <div class="col-4">
                                                <input type="text" class="form-control" name="menyerahkan_by"
                                                    placeholder="Menyerahkan" value="<?php echo e(old('menyerahkan_by')); ?>">
                                            </div>
                                            <div class="col-4">
                                                <input type="text" class="form-control" name="diterima_by"
                                                    placeholder="Diterima" value="<?php echo e(old('diterima_by')); ?>">
                                            </div>
                                            <div class="col-4">
                                                <input type="text" class="form-control" name="diketahui_by"
                                                    placeholder="Diketahui" value="<?php echo e(old('diketahui_by')); ?>">
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
                                            <button type="submit" class="btn btn-primary btn-lg me-2">
                                                <i class="mdi mdi-content-save"></i> Simpan Laporan
                                            </button>
                                            <a href="<?php echo e(route('security.daily-activity.index')); ?>"
                                                class="btn btn-secondary btn-lg">
                                                <i class="mdi mdi-arrow-left"></i> Kembali
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

            // Global variables and functions
            let activityCounter = 0;

            // Make functions global
            window.addActivityRow = function() {
                activityCounter++;
                const container = document.getElementById('activityContainer');

                if (!container) {
                    console.error('Container not found!');
                    return;
                }

                const row = document.createElement('div');
                row.className = 'activity-row';
                row.id = 'activity-row-' + activityCounter;

                row.innerHTML = `
                <div class="row">
                    <div class="col-md-1">
                        <label class="form-label">No</label>
                        <input type="text" class="form-control" value="${activityCounter}" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Time IN</label>
                        <input type="time" class="form-control" name="activities[${activityCounter}][time_in]">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Time OUT</label>
                        <input type="time" class="form-control" name="activities[${activityCounter}][time_out]">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="activities[${activityCounter}][keterangan]" rows="2"
                                  placeholder="Deskripsi aktivitas" required></textarea>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm d-block"
                                onclick="removeActivityRow(${activityCounter})">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </div>
                </div>
            `;

                container.appendChild(row);
                console.log('Activity row added:', activityCounter);
            };

            window.removeActivityRow = function(id) {
                const row = document.getElementById('activity-row-' + id);
                if (row) {
                    row.remove();
                }
            };

            // Auto set jam berdasarkan shift
            // document.querySelector('select[name="shift"]').addEventListener('change', function() {
            //     const shift = this.value;
            //     const jamMulai = document.querySelector('input[name="jam_mulai"]');
            //     const jamSelesai = document.querySelector('input[name="jam_selesai"]');

            //     if (shift === 'I') {
            //         // jamMulai.value = '06:00';
            //         // jamSelesai.value = '14:00';
            //     } else if (shift === 'II') {
            //         // jamMulai.value = '14:00';
            //         // jamSelesai.value = '22:00';
            //     } else if (shift === 'III') {
            //         // jamMulai.value = '22:00';
            //         // jamSelesai.value = '06:00';
            //     }
            // });

            // Form validation dengan AJAX
            document.addEventListener('DOMContentLoaded', function() {
                console.log('DOM loaded, initializing...');

                // Add initial activity row
                window.addActivityRow();

                // Ensure button click handler is properly attached
                const addButton = document.getElementById('addActivityBtn');
                console.log('Add button found:', addButton);

                if (addButton) {
                    // Remove any existing event listeners first
                    addButton.replaceWith(addButton.cloneNode(true));
                    const newAddButton = document.getElementById('addActivityBtn');

                    newAddButton.addEventListener('click', function(e) {
                        e.preventDefault();
                        console.log('Add button clicked!');
                        window.addActivityRow();
                    });
                } else {
                    console.error('Add button not found!');
                }

                const form = document.getElementById('activityForm');
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Validasi minimal 1 aktivitas
                    const activityRows = document.querySelectorAll('.activity-row');
                    if (activityRows.length === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: 'Minimal harus ada 1 aktivitas',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        });
                        return false;
                    }

                    // Konfirmasi simpan
                    Swal.fire({
                        title: 'Yakin ingin menyimpan?',
                        text: "Laporan aktivitas harian akan disimpan!",
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

                            fetch(form.action, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute('content')
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
                                        text: 'Laporan aktivitas harian berhasil disimpan',
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#28a745'
                                    }).then(() => {
                                        window.location.href =
                                            '<?php echo e(route('security.daily-activity.index')); ?>';
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
            });
        </script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/main/security/daily-activity/create.blade.php ENDPATH**/ ?>