<?php $__env->startSection('title'); ?>
    Edit Checklist Kendaraan
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
    Edit Checklist Kendaraan
<?php $__env->stopSection(); ?>

<?php $__env->startSection('body'); ?>

    <body data-sidebar="colored">
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('content'); ?>
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Edit Checklist Kendaraan</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">Edit Checklist Kendaraan</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Checklist Kendaraan Keluar Operasional</h4>
                    <p class="card-title-desc">Edit form checklist kendaraan yang keluar</p>
                </div>
                    <div class="card-body">
                        <form action="<?php echo e(route('security.vehicle-checklist.update', $checklist->id)); ?>" method="POST"
                            id="checklistForm" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>

                            <!-- Hidden inputs untuk foto yang akan dihapus -->
                            <input type="hidden" name="remove_foto_dashboard" id="removeFotoDashboard" value="0">

                            <!-- Header Information -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <h5 class="alert-heading"><i class="bx bx-info-circle"></i> Informasi</h5>
                                        <p class="mb-0">
                                            No. Urut: <strong><?php echo e($checklist->no_urut); ?></strong> |
                                            Status: <strong
                                                class="text-<?php echo e($checklist->status == 'selesai' ? 'success' : 'warning'); ?>"><?php echo e(ucfirst($checklist->status)); ?></strong>
                                            |
                                            Petugas: <strong><?php echo e($checklist->petugas_security); ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Lokasi: <span class="text-danger">*</span></label>
                                    <select class="form-select form-control" name="lokasi" required>
                                        <option value="">Pilih Lokasi</option>
                                        <option value="1" <?php echo e(old('lokasi', $checklist->lokasi) == '1' ? 'selected' : ''); ?>>Lokasi 19 (KRISANTHIUM)</option>
                                        <option value="2" <?php echo e(old('lokasi', $checklist->lokasi) == '2' ? 'selected' : ''); ?>>Lokasi 23 (KRISANTHIUM)</option>
                                        <option value="3" <?php echo e(old('lokasi', $checklist->lokasi) == '3' ? 'selected' : ''); ?>>Lokasi 15 (BERBEK)</option>
                                    </select>
                                    <?php $__errorArgs = ['lokasi'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Checklist Masuk/Keluar: <span class="text-danger">*</span></label>
                                    <select class="form-select form-control" name="checklist_pada" required>
                                        <option value="">Pilih Checklist Masuk/Keluar</option>
                                        <option value="1" <?php echo e(old('checklist_pada', $checklist->checklist_pada) == '1' ? 'selected' : ''); ?>>MASUK</option>
                                        <option value="2" <?php echo e(old('checklist_pada', $checklist->checklist_pada) == '2' ? 'selected' : ''); ?>>KELUAR</option>
                                    </select>
                                    <?php $__errorArgs = ['checklist_pada'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                                                       name="tanggal" value="<?php echo e(old('tanggal', $checklist->tanggal->format('Y-m-d'))); ?>" required>
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
                                                       name="jam_out" id="jamInput" value="<?php echo e(old('jam_out', $checklist->jam_out ? \Carbon\Carbon::parse($checklist->jam_out)->format('H:i') : '')); ?>" required>
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
                                                       name="nama_driver" value="<?php echo e(old('nama_driver', $checklist->nama_driver)); ?>"
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
                                                       name="model_kendaraan" value="<?php echo e(old('model_kendaraan', $checklist->model_kendaraan)); ?>"
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
                                                       name="no_polisi" value="<?php echo e(old('no_polisi', $checklist->no_polisi)); ?>"
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
                                            <label class="form-label" id="kmLabel">KM Awal <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control <?php $__errorArgs = ['km_awal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   name="km_awal" id="kmInput" value="<?php echo e(old('km_awal', $checklist->km_awal)); ?>"
                                                   placeholder="Masukkan KM awal" min="0" required>
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
                                            <label class="form-label" id="bbmLabel">BBM Awal (Persen) <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" class="form-control <?php $__errorArgs = ['bbm_awal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                   name="bbm_awal" id="bbmInput" value="<?php echo e(old('bbm_awal', $checklist->bbm_awal)); ?>"
                                                   placeholder="Masukkan BBM awal" min="0" required>
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
                                                      name="tujuan" rows="3" placeholder="Masukkan tujuan perjalanan" required><?php echo e(old('tujuan', $checklist->tujuan)); ?></textarea>
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
                                                      name="keterangan" rows="3" placeholder="Keterangan tambahan (opsional)"><?php echo e(old('keterangan', $checklist->keterangan)); ?></textarea>
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

                                        <!-- Current Photos -->
                                        <?php if($checklist->foto_dashboard): ?>
                                        <div class="mt-4">
                                            <h6 class="mb-3">Foto Saat Ini:</h6>
                                            <div class="row">
                                                <div class="col-md-3 mb-3" id="currentFotoDashboard">
                                                    <div class="card">
                                                        <img src="<?php echo e(url('sipo_krisan/public/storage')); ?>/<?php echo e($checklist->foto_dashboard); ?>"
                                                             class="card-img-top" style="height: 150px; object-fit: cover;" alt="Foto Dashboard">
                                                        <div class="card-body p-2">
                                                            <h6 class="card-title small">Foto Dashboard</h6>
                                                            <div class="btn-group w-100" role="group">
                                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                                        onclick="viewImage('<?php echo e(url('sipo_krisan/public/storage')); ?>/<?php echo e($checklist->foto_dashboard); ?>', 'Foto Dashboard/SPIDO')">
                                                                    <i class="bx bx-zoom-in"></i> Lihat
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                                        onclick="removeCurrentPhoto('foto_dashboard', 'currentFotoDashboard')">
                                                                    <i class="bx bx-trash"></i> Hapus
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
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
                                            <i class="bx bx-save"></i> Update Checklist
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

    // Set jam otomatis ke waktu sekarang
    const jamInput = document.getElementById('jamInput');
    if (!jamInput.value) {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        jamInput.value = `${hours}:${minutes}`;
    }

    // Handle perubahan "Checklist Pada"
    const checklistPadaSelect = document.querySelector('select[name="checklist_pada"]');
    const jamLabel = document.getElementById('jamLabel');
    const kmLabel = document.getElementById('kmLabel');
    const bbmLabel = document.getElementById('bbmLabel');

    console.log('Elements found:', {
        checklistPadaSelect: !!checklistPadaSelect,
        jamLabel: !!jamLabel,
        kmLabel: !!kmLabel,
        bbmLabel: !!bbmLabel,
        jamInput: !!jamInput
    });

    function updateFormLabels() {
        const selectedValue = checklistPadaSelect.value;
        console.log('Dropdown changed to:', selectedValue);

        if (selectedValue === '1') { // AWAL MASUK
            console.log('Setting to AWAL MASUK');
            jamLabel.innerHTML = 'Jam Masuk <span class="text-danger">*</span>';
            kmLabel.innerHTML = 'KM Akhir <span class="text-danger">*</span>';
            bbmLabel.innerHTML = 'BBM Akhir (Persen) <span class="text-danger">*</span>';

            // Update input names untuk AWAL MASUK
            jamInput.name = 'jam_in';
            document.getElementById('kmInput').name = 'km_akhir';
            document.getElementById('bbmInput').name = 'bbm_akhir';

            console.log('Updated names - jam:', jamInput.name, 'km:', document.getElementById('kmInput').name, 'bbm:', document.getElementById('bbmInput').name);

        } else if (selectedValue === '2') { // AKHIR KELUAR
            console.log('Setting to AKHIR KELUAR');
            jamLabel.innerHTML = 'Jam Keluar <span class="text-danger">*</span>';
            kmLabel.innerHTML = 'KM Awal <span class="text-danger">*</span>';
            bbmLabel.innerHTML = 'BBM Awal (Persen) <span class="text-danger">*</span>';

            // Update input names untuk AKHIR KELUAR
            jamInput.name = 'jam_out';
            document.getElementById('kmInput').name = 'km_awal';
            document.getElementById('bbmInput').name = 'bbm_awal';

            console.log('Updated names - jam:', jamInput.name, 'km:', document.getElementById('kmInput').name, 'bbm:', document.getElementById('bbmInput').name);
        }
    }

    // Event listener untuk perubahan dropdown
    if (checklistPadaSelect) {
        checklistPadaSelect.addEventListener('change', function() {
            console.log('Dropdown change event triggered');
            updateFormLabels();
        });

        checklistPadaSelect.addEventListener('input', function() {
            console.log('Dropdown input event triggered');
            updateFormLabels();
        });
    }

    // Set default jika sudah ada value
    if (checklistPadaSelect && checklistPadaSelect.value) {
        console.log('Initial value:', checklistPadaSelect.value);
        updateFormLabels();
    } else {
        console.log('No initial value, setting default to KELUAR (2)');
        // Set default ke KELUAR jika tidak ada nilai
        if (checklistPadaSelect) {
            checklistPadaSelect.value = '2';
            updateFormLabels();
        }
    }

    // Form validation dengan AJAX
    const form = document.getElementById('checklistForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default submit

        // Ambil nilai KM dan BBM berdasarkan field yang aktif
        const kmInput = document.getElementById('kmInput');
        const bbmInput = document.getElementById('bbmInput');
        const kmValue = parseInt(kmInput.value);
        const bbmValue = parseFloat(bbmInput.value);
        const kmFieldName = kmInput.name; // 'km_awal' atau 'km_akhir'
        const bbmFieldName = bbmInput.name; // 'bbm_awal' atau 'bbm_akhir'

        // Validasi client-side untuk KM
        if (kmValue < 0) {
            const kmLabel = kmFieldName === 'km_awal' ? 'KM Awal' : 'KM Akhir';
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: `${kmLabel} tidak boleh negatif`,
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33'
            });
            return false;
        }

        // Validasi client-side untuk BBM
        if (bbmValue < 0) {
            const bbmLabel = bbmFieldName === 'bbm_awal' ? 'BBM Awal' : 'BBM Akhir';
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: `${bbmLabel} tidak boleh negatif`,
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33'
            });
            return false;
        }

        // Validasi required fields - sesuaikan dengan field yang aktif
        const requiredFields = [
            { name: 'tanggal', label: 'Tanggal' },
            { name: 'nama_driver', label: 'Nama Driver' },
            { name: 'model_kendaraan', label: 'Model Kendaraan' },
            { name: 'no_polisi', label: 'No. Polisi' },
            { name: jamInput.name, label: jamInput.name === 'jam_in' ? 'Jam Masuk' : 'Jam Keluar' },
            { name: kmFieldName, label: kmFieldName === 'km_awal' ? 'KM Awal' : 'KM Akhir' },
            { name: bbmFieldName, label: bbmFieldName === 'bbm_awal' ? 'BBM Awal' : 'BBM Akhir' },
            { name: 'tujuan', label: 'Tujuan' }
        ];

        for (const field of requiredFields) {
            let input;
            // Untuk field jam, KM, dan BBM, gunakan ID karena nama field berubah
            if (field.name === jamInput.name) {
                input = jamInput;
            } else if (field.name === kmFieldName) {
                input = kmInput;
            } else if (field.name === bbmFieldName) {
                input = bbmInput;
            } else {
                input = document.querySelector(`input[name="${field.name}"], textarea[name="${field.name}"]`);
            }

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

        // Konfirmasi update
        Swal.fire({
            title: 'Yakin ingin mengubah?',
            text: "Data checklist kendaraan akan diupdate!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Update!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Mengupdate Data...',
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-HTTP-Method-Override': 'PUT'
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
                        text: 'Data checklist kendaraan berhasil diupdate',
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
                        text: 'Terjadi kesalahan saat mengupdate data',
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

    // Function untuk view image dalam modal
    function viewImage(imageUrl, title) {
        // Buat modal HTML jika belum ada
        let imageModal = document.getElementById('imageModal');
        if (!imageModal) {
            imageModal = document.createElement('div');
            imageModal.id = 'imageModal';
            imageModal.className = 'modal fade';
            imageModal.innerHTML = `
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="imageModalTitle"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center">
                            <img id="imageModalImg" src="" class="img-fluid" style="max-height: 70vh;">
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(imageModal);
        }

        // Set image dan title
        document.getElementById('imageModalTitle').textContent = title;
        document.getElementById('imageModalImg').src = imageUrl;

        // Show modal
        const modal = new bootstrap.Modal(imageModal);
        modal.show();
    }

    // Function untuk remove current photo
    function removeCurrentPhoto(photoType, elementId) {
        Swal.fire({
            title: 'Yakin ingin menghapus foto?',
            text: "Foto akan dihapus dari database saat form disubmit!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Hide the photo element
                const photoElement = document.getElementById(elementId);
                if (photoElement) {
                    photoElement.style.display = 'none';
                }

                // Set hidden input to mark for removal
                const removeInput = document.getElementById('remove' + photoType.charAt(0).toUpperCase() + photoType.slice(1));
                if (removeInput) {
                    removeInput.value = '1';
                }

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Foto Ditandai untuk Dihapus',
                    text: 'Foto akan dihapus saat form disubmit',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }

    // Make functions globally available
    window.viewImage = viewImage;
    window.removeCurrentPhoto = removeCurrentPhoto;
});
</script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/main/security/vehicle-checklist/edit.blade.php ENDPATH**/ ?>