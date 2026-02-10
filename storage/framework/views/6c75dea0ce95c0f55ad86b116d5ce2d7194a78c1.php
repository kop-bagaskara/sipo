<?php $__env->startSection('title'); ?>
    <?php if(isset($mode) && $mode === 'update-jam-keluar'): ?>
        Update Jam Keluar
    <?php else: ?>
        Edit Keluar/Masuk Barang
    <?php endif; ?>
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

        .required-field {
            border-left: 4px solid #dc3545;
        }

        .optional-field {
            border-left: 4px solid #28a745;
        }

        /* Multiple barang items styling */
        .barang-item {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }

        .barang-item:hover {
            border-color: #007bff;
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.1);
        }

        .barang-item:first-child {
            background-color: #fff;
            border-color: #dee2e6;
        }

        .barang-item h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 15px;
        }

        #barang-items-container {
            max-height: 600px;
            overflow-y: auto;
        }

        /* Responsive layout for barang items */
        @media (max-width: 768px) {

            .barang-item .row .col-md-2,
            .barang-item .row .col-md-4 {
                margin-bottom: 10px;
            }
        }

        /* Gap between buttons */
        .d-flex.gap-1 {
            gap: 0.25rem;
        }

        /* Smooth scroll animation */
        .barang-item {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes  slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Delete button animation */
        .btn-danger {
            transition: all 0.2s ease;
        }

        .btn-danger:hover {
            transform: scale(1.05);
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-title'); ?>
    <?php if(isset($mode) && $mode === 'update-jam-keluar'): ?>
        Update Jam Keluar
    <?php else: ?>
        Edit Keluar/Masuk Barang
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('body'); ?>

    <body data-sidebar="colored">
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('content'); ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>
    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">
                    <?php if(isset($mode) && $mode === 'update-jam-keluar'): ?>
                        Update Jam Keluar
                    <?php else: ?>
                        Edit Keluar/Masuk Barang
                    <?php endif; ?>
                </h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Security</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('security.goods-movement.index')); ?>">Keluar/Masuk
                            Barang</a></li>
                    <li class="breadcrumb-item active">
                        <?php if(isset($mode) && $mode === 'update-jam-keluar'): ?>
                            Update Jam Keluar
                        <?php else: ?>
                            Edit
                        <?php endif; ?>
                    </li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <?php if(isset($mode) && $mode === 'update-jam-keluar'): ?>
                                Update Jam Keluar - No. Urut: <?php echo e($movement->no_urut); ?>

                            <?php else: ?>
                                Edit Keluar/Masuk Barang
                            <?php endif; ?>
                        </h4>
                        <p class="card-title-desc">
                            <?php if(isset($mode) && $mode === 'update-jam-keluar'): ?>
                                Update jam keluar dan barang yang dibawa keluar
                            <?php else: ?>
                                Edit data keluar/masuk barang
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo e(route('security.goods-movement.update', $movement->id)); ?>" method="POST"
                            id="movementForm">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>

                            <?php if(isset($mode) && $mode === 'update-jam-keluar'): ?>
                                <!-- Info Data Existing untuk Update Jam Keluar -->
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <h5 class="alert-heading"><i class="mdi mdi-information-outline"></i> Data
                                                Existing</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mb-1"><strong>Tanggal:</strong>
                                                        <?php echo e($movement->tanggal_formatted); ?></p>
                                                    <p class="mb-1"><strong>Nama:</strong>
                                                        <?php echo e($movement->nama_pengunjung); ?></p>
                                                    <p class="mb-1"><strong>Perusahaan:</strong>
                                                        <?php echo e($movement->perusahaan_asal); ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-1"><strong>Jam Masuk:</strong>
                                                        <?php echo e($movement->jam_masuk); ?></p>
                                                    <p class="mb-1"><strong>No. Polisi:</strong>
                                                        <?php echo e($movement->no_polisi); ?></p>
                                                    <p class="mb-1"><strong>Driver:</strong> <?php echo e($movement->nama_driver); ?>

                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Header Information -->
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <h5 class="alert-heading"><i class="mdi mdi-information-outline"></i> Informasi
                                            </h5>
                                            <p class="mb-0">
                                                No. Urut: <strong><?php echo e($movement->no_urut); ?></strong> |
                                                Petugas: <strong><?php echo e($movement->petugas_security); ?></strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                                    <select class="form-select form-control" name="lokasi" required>
                                        <option value="">Pilih Lokasi</option>
                                        <option value="1"
                                            <?php echo e(old('lokasi', $movement->lokasi) == '1' ? 'selected' : ''); ?>>Lokasi 19
                                            (KRISANTHIUM)</option>
                                        <option value="2"
                                            <?php echo e(old('lokasi', $movement->lokasi) == '2' ? 'selected' : ''); ?>>Lokasi 23
                                            (KRISANTHIUM)</option>
                                        <option value="3"
                                            <?php echo e(old('lokasi', $movement->lokasi) == '3' ? 'selected' : ''); ?>>Lokasi 15
                                            (BERBEK)</option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <!-- Data Dasar -->
                                <div class="col-md-6">
                                    <div class="card border required-field">
                                        <div class="card-header bg-light">
                                            <h5 class="card-title mb-0"><i class="mdi mdi-calendar"></i> Data Dasar</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                                <input type="date"
                                                    class="form-control <?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    name="tanggal"
                                                    value="<?php echo e(old('tanggal', $movement->tanggal->format('Y-m-d'))); ?>"
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

                                            <div class="mb-3">
                                                <label class="form-label">Jam Masuk <span
                                                        class="text-danger">*</span></label>
                                                <input type="time"
                                                    class="form-control <?php $__errorArgs = ['jam_masuk'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    name="jam_masuk" id="jam_masuk"
                                                    value="<?php echo e(old('jam_masuk', $movement->jam_masuk ? \Carbon\Carbon::parse($movement->jam_masuk)->format('H:i') : '')); ?>"
                                                    required>
                                                <?php $__errorArgs = ['jam_masuk'];
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
                                                <label class="form-label">Jam Keluar</label>
                                                <input type="time"
                                                    class="form-control <?php $__errorArgs = ['jam_keluar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    name="jam_keluar" id="jam_keluar"
                                                    value="<?php echo e(old('jam_keluar', $movement->jam_keluar ? \Carbon\Carbon::parse($movement->jam_keluar)->format('H:i') : '')); ?>">
                                                <?php $__errorArgs = ['jam_keluar'];
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
                                                <label class="form-label">Status Laporan <span
                                                        class="text-danger">*</span></label>
                                                <select
                                                    class="form-select form-control <?php $__errorArgs = ['status_laporan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    name="status_laporan" required>
                                                    <option value="">Pilih Status</option>
                                                    <option value="IN"
                                                        <?php echo e(old('status_laporan', $movement->status_laporan) == 'IN' ? 'selected' : ''); ?>>
                                                        IN (Baru Masuk)</option>
                                                    <option value="OUT"
                                                        <?php echo e(old('status_laporan', $movement->status_laporan) == 'OUT' ? 'selected' : ''); ?>>
                                                        OUT (Sudah Keluar)</option>
                                                </select>
                                                <?php $__errorArgs = ['status_laporan'];
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

                                <!-- Data Pengunjung -->
                                <div class="col-md-6">
                                    <div class="card border required-field">
                                        <div class="card-header bg-light">
                                            <h5 class="card-title mb-0"><i class="mdi mdi-account"></i> Data
                                                Pengunjung/Pengirim</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nama <span class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control <?php $__errorArgs = ['nama_pengunjung'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    name="nama_pengunjung"
                                                    value="<?php echo e(old('nama_pengunjung', $movement->nama_pengunjung)); ?>"
                                                    placeholder="Masukkan nama pengunjung/pengirim" required>
                                                <?php $__errorArgs = ['nama_pengunjung'];
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
                                                <label class="form-label">Perusahaan/Instansi</label>
                                                <input type="text"
                                                    class="form-control <?php $__errorArgs = ['perusahaan_asal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    name="perusahaan_asal"
                                                    value="<?php echo e(old('perusahaan_asal', $movement->perusahaan_asal)); ?>"
                                                    placeholder="Nama perusahaan atau instansi">
                                                <?php $__errorArgs = ['perusahaan_asal'];
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
                                                <label class="form-label">No. Telepon</label>
                                                <input type="text"
                                                    class="form-control <?php $__errorArgs = ['no_telepon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    name="no_telepon"
                                                    value="<?php echo e(old('no_telepon', $movement->no_telepon)); ?>"
                                                    placeholder="Nomor telepon">
                                                <?php $__errorArgs = ['no_telepon'];
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

                            <!-- Data Barang - Full Row -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card border required-field">
                                        <div
                                            class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0"><i class="mdi mdi-package-variant"></i> Data
                                                Barang</h5>
                                            <button type="button" class="btn btn-sm btn-success"
                                                onclick="addBarangItem()">
                                                <i class="mdi mdi-plus"></i> Tambah Barang
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div id="barang-items-container">
                                                <?php if($movement->barang_items && count($movement->barang_items) > 0): ?>
                                                    <!-- Display existing multiple items -->
                                                    <?php $__currentLoopData = $movement->barang_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="barang-item mb-3" data-index="<?php echo e($index); ?>">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center mb-2">
                                                                <h6 class="mb-0">Barang #<?php echo e($index + 1); ?></h6>
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                    onclick="removeBarangItem(<?php echo e($index); ?>)"
                                                                    style="<?php echo e(count($movement->barang_items) <= 1 ? 'display: none;' : ''); ?>">
                                                                    <i class="mdi mdi-delete"></i> Hapus
                                                                </button>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Jenis Barang <span
                                                                                class="text-danger">*</span></label>
                                                                        <input type="text"
                                                                            class="form-control <?php $__errorArgs = ['barang.' . $index . '.jenis_barang'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                                            name="barang[<?php echo e($index); ?>][jenis_barang]"
                                                                            value="<?php echo e(old('barang.' . $index . '.jenis_barang', $item['jenis_barang'] ?? '')); ?>"
                                                                            placeholder="Contoh: Kertas, Tinta, Mesin, dll"
                                                                            required>
                                                                        <?php $__errorArgs = ['barang.' . $index . '.jenis_barang'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                                            <div class="invalid-feedback"><?php echo e($message); ?>

                                                                            </div>
                                                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Jumlah <span
                                                                                class="text-danger">*</span></label>
                                                                        <input type="number"
                                                                            class="form-control <?php $__errorArgs = ['barang.' . $index . '.jumlah'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                                            name="barang[<?php echo e($index); ?>][jumlah]"
                                                                            value="<?php echo e(old('barang.' . $index . '.jumlah', $item['jumlah'] ?? '')); ?>"
                                                                            placeholder="Jumlah" min="0" required>
                                                                        <?php $__errorArgs = ['barang.' . $index . '.jumlah'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                                            <div class="invalid-feedback"><?php echo e($message); ?>

                                                                            </div>
                                                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Satuan <span
                                                                                class="text-danger">*</span></label>
                                                                        <input type="text"
                                                                            class="form-control <?php $__errorArgs = ['barang.' . $index . '.satuan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                                            name="barang[<?php echo e($index); ?>][satuan]"
                                                                            value="<?php echo e(old('barang.' . $index . '.satuan', $item['satuan'] ?? '')); ?>"
                                                                            placeholder="pcs, kg, box" required>
                                                                        <?php $__errorArgs = ['barang.' . $index . '.satuan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                                            <div class="invalid-feedback"><?php echo e($message); ?>

                                                                            </div>
                                                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Berat (kg)</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control <?php $__errorArgs = ['barang.' . $index . '.berat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                                            name="barang[<?php echo e($index); ?>][berat]"
                                                                            value="<?php echo e(old('barang.' . $index . '.berat', $item['berat'] ?? '')); ?>"
                                                                            placeholder="Berat" min="0">
                                                                        <?php $__errorArgs = ['barang.' . $index . '.berat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                                            <div class="invalid-feedback"><?php echo e($message); ?>

                                                                            </div>
                                                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Aksi</label>
                                                                        <div class="d-flex gap-1">
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-outline-primary"
                                                                                onclick="duplicateBarangItem(<?php echo e($index); ?>)"
                                                                                title="Duplikat">
                                                                                <i class="mdi mdi-content-duplicate"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Deskripsi Barang</label>
                                                                <textarea class="form-control <?php $__errorArgs = ['barang.' . $index . '.deskripsi_barang'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                                    name="barang[<?php echo e($index); ?>][deskripsi_barang]" rows="2" placeholder="Deskripsi detail barang"><?php echo e(old('barang.' . $index . '.deskripsi_barang', $item['deskripsi_barang'] ?? '')); ?></textarea>
                                                                <?php $__errorArgs = ['barang.' . $index . '.deskripsi_barang'];
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
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php else: ?>
                                                    <!-- Fallback untuk data lama (backward compatibility) -->
                                                    <div class="barang-item mb-3" data-index="0">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                            <h6 class="mb-0">Barang #1</h6>
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="removeBarangItem(0)" style="display: none;">
                                                                <i class="mdi mdi-delete"></i> Hapus
                                                            </button>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Jenis Barang <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text"
                                                                        class="form-control <?php $__errorArgs = ['barang.0.jenis_barang'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                                        name="barang[0][jenis_barang]"
                                                                        value="<?php echo e(old('barang.0.jenis_barang', $movement->jenis_barang)); ?>"
                                                                        placeholder="Contoh: Kertas, Tinta, Mesin, dll"
                                                                        required>
                                                                    <?php $__errorArgs = ['barang.0.jenis_barang'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                                        <div class="invalid-feedback"><?php echo e($message); ?>

                                                                        </div>
                                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Jumlah <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="number"
                                                                        class="form-control <?php $__errorArgs = ['barang.0.jumlah'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                                        name="barang[0][jumlah]"
                                                                        value="<?php echo e(old('barang.0.jumlah', $movement->jumlah)); ?>"
                                                                        placeholder="Jumlah" min="0" required>
                                                                    <?php $__errorArgs = ['barang.0.jumlah'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                                        <div class="invalid-feedback"><?php echo e($message); ?>

                                                                        </div>
                                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Satuan <span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text"
                                                                        class="form-control <?php $__errorArgs = ['barang.0.satuan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                                        name="barang[0][satuan]"
                                                                        value="<?php echo e(old('barang.0.satuan', $movement->satuan)); ?>"
                                                                        placeholder="pcs, kg, box" required>
                                                                    <?php $__errorArgs = ['barang.0.satuan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                                        <div class="invalid-feedback"><?php echo e($message); ?>

                                                                        </div>
                                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Berat (kg)</label>
                                                                    <input type="number" step="0.01"
                                                                        class="form-control <?php $__errorArgs = ['barang.0.berat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                                        name="barang[0][berat]"
                                                                        value="<?php echo e(old('barang.0.berat', $movement->berat)); ?>"
                                                                        placeholder="Berat" min="0">
                                                                    <?php $__errorArgs = ['barang.0.berat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                                        <div class="invalid-feedback"><?php echo e($message); ?>

                                                                        </div>
                                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Aksi</label>
                                                                    <div class="d-flex gap-1">
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-outline-primary"
                                                                            onclick="duplicateBarangItem(0)"
                                                                            title="Duplikat">
                                                                            <i class="mdi mdi-content-duplicate"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Deskripsi Barang</label>
                                                            <textarea class="form-control <?php $__errorArgs = ['barang.0.deskripsi_barang'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                                name="barang[0][deskripsi_barang]" rows="2" placeholder="Deskripsi detail barang"><?php echo e(old('barang.0.deskripsi_barang', $movement->deskripsi_barang)); ?></textarea>
                                                            <?php $__errorArgs = ['barang.0.deskripsi_barang'];
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
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Barang Keluar - Full Row -->
                            <div class="row" id="barangKeluarSection">
                                <div class="col-12">
                                    <div class="card border optional-field">
                                        <div
                                            class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0"><i class="mdi mdi-package-variant-closed"></i>
                                                Data Barang Keluar (Opsional)</h5>
                                            <button type="button" class="btn btn-sm btn-success"
                                                onclick="addBarangKeluarItem()">
                                                <i class="mdi mdi-plus"></i> Tambah Barang Keluar
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div id="barang-keluar-items-container">
                                                <?php if($movement->barang_keluar_items && count($movement->barang_keluar_items) > 0): ?>
                                                    <!-- Display existing barang keluar items -->
                                                    <?php $__currentLoopData = $movement->barang_keluar_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="barang-item mb-3" data-index="<?php echo e($index); ?>">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center mb-2">
                                                                <h6 class="mb-0">Barang Keluar #<?php echo e($index + 1); ?></h6>
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                    onclick="removeBarangKeluarItem(<?php echo e($index); ?>)"
                                                                    style="<?php echo e(count($movement->barang_keluar_items) <= 1 ? 'display: none;' : ''); ?>">
                                                                    <i class="mdi mdi-delete"></i> Hapus
                                                                </button>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Jenis Barang</label>
                                                                        <input type="text"
                                                                            class="form-control <?php $__errorArgs = ['barang_keluar.' . $index . '.jenis_barang'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                                            name="barang_keluar[<?php echo e($index); ?>][jenis_barang]"
                                                                            value="<?php echo e(old('barang_keluar.' . $index . '.jenis_barang', $item['jenis_barang'] ?? '')); ?>"
                                                                            placeholder="Contoh: Kertas, Tinta, Mesin, dll">
                                                                        <?php $__errorArgs = ['barang_keluar.' . $index . '.jenis_barang'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                                            <div class="invalid-feedback"><?php echo e($message); ?>

                                                                            </div>
                                                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Jumlah</label>
                                                                        <input type="number"
                                                                            class="form-control <?php $__errorArgs = ['barang_keluar.' . $index . '.jumlah'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                                            name="barang_keluar[<?php echo e($index); ?>][jumlah]"
                                                                            value="<?php echo e(old('barang_keluar.' . $index . '.jumlah', $item['jumlah'] ?? '')); ?>"
                                                                            placeholder="Jumlah" min="0">
                                                                        <?php $__errorArgs = ['barang_keluar.' . $index . '.jumlah'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                                            <div class="invalid-feedback"><?php echo e($message); ?>

                                                                            </div>
                                                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Satuan</label>
                                                                        <input type="text"
                                                                            class="form-control <?php $__errorArgs = ['barang_keluar.' . $index . '.satuan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                                            name="barang_keluar[<?php echo e($index); ?>][satuan]"
                                                                            value="<?php echo e(old('barang_keluar.' . $index . '.satuan', $item['satuan'] ?? '')); ?>"
                                                                            placeholder="pcs, kg, box">
                                                                        <?php $__errorArgs = ['barang_keluar.' . $index . '.satuan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                                            <div class="invalid-feedback"><?php echo e($message); ?>

                                                                            </div>
                                                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Berat (kg)</label>
                                                                        <input type="number" step="0.01"
                                                                            class="form-control <?php $__errorArgs = ['barang_keluar.' . $index . '.berat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                                            name="barang_keluar[<?php echo e($index); ?>][berat]"
                                                                            value="<?php echo e(old('barang_keluar.' . $index . '.berat', $item['berat'] ?? '')); ?>"
                                                                            placeholder="Berat" min="0">
                                                                        <?php $__errorArgs = ['barang_keluar.' . $index . '.berat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                                            <div class="invalid-feedback"><?php echo e($message); ?>

                                                                            </div>
                                                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Aksi</label>
                                                                        <div class="d-flex gap-1">
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-outline-primary"
                                                                                onclick="duplicateBarangKeluarItem(<?php echo e($index); ?>)"
                                                                                title="Duplikat">
                                                                                <i class="mdi mdi-content-duplicate"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Deskripsi Barang</label>
                                                                <textarea class="form-control <?php $__errorArgs = ['barang_keluar.' . $index . '.deskripsi_barang'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                                    name="barang_keluar[<?php echo e($index); ?>][deskripsi_barang]" rows="2"
                                                                    placeholder="Deskripsi detail barang"><?php echo e(old('barang_keluar.' . $index . '.deskripsi_barang', $item['deskripsi_barang'] ?? '')); ?></textarea>
                                                                <?php $__errorArgs = ['barang_keluar.' . $index . '.deskripsi_barang'];
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
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php else: ?>
                                                    <!-- Default empty item -->
                                                    <div class="barang-item mb-3" data-index="0">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                            <h6 class="mb-0">Barang Keluar #1</h6>
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="removeBarangKeluarItem(0)"
                                                                style="display: none;">
                                                                <i class="mdi mdi-delete"></i> Hapus
                                                            </button>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Jenis Barang</label>
                                                                    <input type="text" class="form-control"
                                                                        name="barang_keluar[0][jenis_barang]"
                                                                        placeholder="Contoh: Kertas, Tinta, Mesin, dll">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Jumlah</label>
                                                                    <input type="number" class="form-control"
                                                                        name="barang_keluar[0][jumlah]"
                                                                        placeholder="Jumlah" min="0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Satuan</label>
                                                                    <input type="text" class="form-control"
                                                                        name="barang_keluar[0][satuan]"
                                                                        placeholder="pcs, kg, box">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Berat (kg)</label>
                                                                    <input type="number" step="0.01"
                                                                        class="form-control"
                                                                        name="barang_keluar[0][berat]" placeholder="Berat"
                                                                        min="0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Aksi</label>
                                                                    <div class="d-flex gap-1">
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-outline-primary"
                                                                            onclick="duplicateBarangKeluarItem(0)"
                                                                            title="Duplikat">
                                                                            <i class="mdi mdi-content-duplicate"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Deskripsi Barang</label>
                                                            <textarea class="form-control" name="barang_keluar[0][deskripsi_barang]" rows="2"
                                                                placeholder="Deskripsi detail barang"></textarea>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                

                                <!-- Dokumen & Keterangan -->
                                <div class="col-md-4">
                                    <div class="card border optional-field">
                                        <div class="card-header bg-light">
                                            <h5 class="card-title mb-0"><i class="mdi mdi-file-document"></i> Dokumen &
                                                Keterangan</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label">No. Surat Jalan</label>
                                                <input type="text"
                                                    class="form-control <?php $__errorArgs = ['no_surat_jalan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    name="no_surat_jalan"
                                                    value="<?php echo e(old('no_surat_jalan', $movement->no_surat_jalan)); ?>"
                                                    placeholder="Nomor surat jalan">
                                                <?php $__errorArgs = ['no_surat_jalan'];
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
                                                <label class="form-label">No. Invoice</label>
                                                <input type="text"
                                                    class="form-control <?php $__errorArgs = ['no_invoice'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    name="no_invoice"
                                                    value="<?php echo e(old('no_invoice', $movement->no_invoice)); ?>"
                                                    placeholder="Nomor invoice">
                                                <?php $__errorArgs = ['no_invoice'];
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
                                                <label class="form-label">Dokumen Pendukung</label>
                                                <textarea class="form-control <?php $__errorArgs = ['dokumen_pendukung'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="dokumen_pendukung"
                                                    rows="2" placeholder="Dokumen lain yang menyertai"><?php echo e(old('dokumen_pendukung', $movement->dokumen_pendukung)); ?></textarea>
                                                <?php $__errorArgs = ['dokumen_pendukung'];
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
unset($__errorArgs, $__bag); ?>" name="keterangan" rows="2"
                                                    placeholder="Keterangan tambahan"><?php echo e(old('keterangan', $movement->keterangan)); ?></textarea>
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

                                            <div class="mb-3">
                                                <label class="form-label">Catatan Security</label>
                                                <textarea class="form-control <?php $__errorArgs = ['catatan_security'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="catatan_security"
                                                    rows="2" placeholder="Catatan dari security"><?php echo e(old('catatan_security', $movement->catatan_security)); ?></textarea>
                                                <?php $__errorArgs = ['catatan_security'];
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

                            <!-- Waktu & Lokasi -->

                    </div>

                    <div class="row">
                        <!-- Data Kendaraan -->

                    </div>

                    <!-- Action Buttons -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center">
                                    <button type="submit" class="btn btn-primary btn-lg me-2">
                                        <i class="mdi mdi-content-save"></i> Update Data
                                    </button>
                                    <a href="<?php echo e(route('security.goods-movement.index')); ?>"
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

    <?php $__env->startSection('script'); ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script type="text/javascript">
            console.log('Script loaded!');
            document.addEventListener('DOMContentLoaded', function() {
                console.log('DOM loaded!');
                const asalField = document.getElementById('asal_field');
                const tujuanField = document.getElementById('tujuan_field');
                const statusLaporanSelect = document.querySelector('select[name="status_laporan"]');
                const jamKeluarField = document.querySelector('input[name="jam_keluar"]');
                const barangKeluarSection = document.getElementById('barangKeluarSection');

                // Function to toggle fields based on status laporan
                function toggleFields() {
                    const statusLaporan = statusLaporanSelect.value;

                    // Check if we're in update-jam-keluar mode
                    const urlParams = new URLSearchParams(window.location.search);
                    const isUpdateJamKeluarMode = urlParams.get('mode') === 'update-jam-keluar';

                    // Always show both asal and tujuan fields
                    asalField.style.display = 'block';
                    tujuanField.style.display = 'block';

                    // Toggle jam keluar and barang keluar based on status or mode
                    if (isUpdateJamKeluarMode) {
                        // Untuk mode update-jam-keluar, selalu tampilkan jam keluar dan barang keluar
                        jamKeluarField.style.display = 'block';
                        if (barangKeluarSection) {
                            barangKeluarSection.style.display = 'block';
                        }
                    } else if (statusLaporan === 'IN') {
                        // Untuk status IN, sembunyikan jam keluar dan barang keluar
                        jamKeluarField.style.display = 'none';
                        if (barangKeluarSection) {
                            barangKeluarSection.style.display = 'none';
                        }
                    } else if (statusLaporan === 'OUT') {
                        // Untuk status OUT, tampilkan jam keluar dan barang keluar
                        jamKeluarField.style.display = 'block';
                        if (barangKeluarSection) {
                            barangKeluarSection.style.display = 'block';
                        }
                    } else {
                        // Default: sembunyikan jam keluar dan barang keluar
                        jamKeluarField.style.display = 'none';
                        if (barangKeluarSection) {
                            barangKeluarSection.style.display = 'none';
                        }
                    }
                }

                // Initial toggle
                toggleFields();

                // Toggle on change
                statusLaporanSelect.addEventListener('change', toggleFields);

                // Auto-fill jam masuk dengan jam keluar
                // const autoFillCheckbox = document.getElementById('auto_fill_jam_masuk');
                // const jamMasukField = document.getElementById('jam_masuk');
                // const jamKeluarFieldInput = document.getElementById('jam_keluar');

                // // Check if we're in update-jam-keluar mode and auto-check the checkbox
                // const urlParams = new URLSearchParams(window.location.search);
                // const isUpdateJamKeluarMode = urlParams.get('mode') === 'update-jam-keluar';

                // if (isUpdateJamKeluarMode) {
                //     // Auto-check the checkbox in update-jam-keluar mode
                //     autoFillCheckbox.checked = true;
                //     // Trigger the change event to apply the auto-fill logic
                //     autoFillCheckbox.dispatchEvent(new Event('change'));
                // }

                // autoFillCheckbox.addEventListener('change', function() {
                //     if (this.checked) {
                //         // Disable jam masuk field dan set value sama dengan jam keluar
                //         jamMasukField.disabled = true;
                //         jamMasukField.style.backgroundColor = '#f8f9fa';

                //         // Set jam masuk sama dengan jam keluar saat jam keluar berubah
                //         jamKeluarFieldInput.addEventListener('input', function() {
                //             jamMasukField.value = this.value;
                //         });

                //         // Set initial value jika jam keluar sudah ada
                //         if (jamKeluarFieldInput.value) {
                //             jamMasukField.value = jamKeluarFieldInput.value;
                //         }
                //     } else {
                //         // Enable jam masuk field kembali
                //         jamMasukField.disabled = false;
                //         jamMasukField.style.backgroundColor = '';

                //         // Remove event listener
                //         jamKeluarFieldInput.removeEventListener('input', function() {
                //             jamMasukField.value = this.value;
                //         });
                //     }
                // });

                // Auto uppercase untuk no polisi
                const nopolInput = document.querySelector('input[name="no_polisi"]');
                if (nopolInput) {
                    nopolInput.addEventListener('input', function() {
                        this.value = this.value.toUpperCase();
                    });
                }

                // Auto capitalize untuk nama
                const namaInputs = document.querySelectorAll(
                    'input[name="nama_pengunjung"], input[name="nama_driver"]');
                namaInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
                    });
                });

                // Form validation dengan AJAX
                const form = document.getElementById('movementForm');
                console.log('Form found:', form);
                form.addEventListener('submit', function(e) {
                    console.log('Form submitted!');
                    e.preventDefault(); // Prevent default submit

                    const jumlah = parseInt(document.querySelector('input[name="jumlah"]').value) || 0;
                    const berat = parseFloat(document.querySelector('input[name="berat"]').value) || 0;

                    // Validasi client-side
                    if (jumlah < 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: 'Jumlah tidak boleh negatif',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        });
                        return false;
                    }

                    if (berat < 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: 'Berat tidak boleh negatif',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        });
                        return false;
                    }

                    // Konfirmasi update
                    Swal.fire({
                        title: 'Yakin ingin mengubah?',
                        text: "Data keluar/masuk barang akan diupdate!",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
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
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute('content'),
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
                                        text: 'Data keluar/masuk barang berhasil diupdate',
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#28a745'
                                    }).then(() => {
                                        window.location.href =
                                            '<?php echo e(route('security.goods-movement.index')); ?>';
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
            });

            // Global functions untuk multiple barang items
            let barangItemIndex = <?php echo e($movement->barang_items ? count($movement->barang_items) - 1 : 0); ?>;

            // Function untuk menambah item barang baru
            function addBarangItem() {
                barangItemIndex++;
                const container = document.getElementById('barang-items-container');

                const newItem = document.createElement('div');
                newItem.className = 'barang-item mb-3';
                newItem.setAttribute('data-index', barangItemIndex);

                newItem.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Barang #${barangItemIndex + 1}</h6>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeBarangItem(${barangItemIndex})">
                            <i class="mdi mdi-delete"></i> Hapus
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Jenis Barang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="barang[${barangItemIndex}][jenis_barang]"
                                       placeholder="Contoh: Kertas, Tinta, Mesin, dll" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="barang[${barangItemIndex}][jumlah]"
                                       placeholder="Jumlah" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Satuan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="barang[${barangItemIndex}][satuan]"
                                       placeholder="pcs, kg, box" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Berat (kg)</label>
                                <input type="number" step="0.01" class="form-control" name="barang[${barangItemIndex}][berat]"
                                       placeholder="Berat" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Aksi</label>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="duplicateBarangItem(${barangItemIndex})" title="Duplikat">
                                        <i class="mdi mdi-content-duplicate"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi Barang</label>
                        <textarea class="form-control" name="barang[${barangItemIndex}][deskripsi_barang]"
                                  rows="2" placeholder="Deskripsi detail barang"></textarea>
                    </div>
                `;

                container.appendChild(newItem);

                // Scroll ke item baru
                newItem.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });

                // Update tombol hapus untuk item pertama jika ada lebih dari 1 item
                updateDeleteButtons();
            }

            // Function untuk menghapus item barang
            function removeBarangItem(index) {
                const item = document.querySelector(`[data-index="${index}"]`);
                if (item) {
                    item.remove();
                    updateDeleteButtons();
                    updateItemNumbers();
                }
            }

            // Function untuk update tombol hapus
            function updateDeleteButtons() {
                const items = document.querySelectorAll('.barang-item');
                const firstItem = items[0];

                if (firstItem) {
                    const deleteBtn = firstItem.querySelector('button[onclick*="removeBarangItem"]');
                    if (deleteBtn) {
                        deleteBtn.style.display = items.length > 1 ? 'inline-block' : 'none';
                    }
                }
            }

            // Function untuk update nomor item
            function updateItemNumbers() {
                const items = document.querySelectorAll('.barang-item');
                items.forEach((item, index) => {
                    const title = item.querySelector('h6');
                    if (title) {
                        title.textContent = `Barang #${index + 1}`;
                    }
                });
            }

            // Function untuk duplikat item barang
            function duplicateBarangItem(index) {
                const sourceItem = document.querySelector(`[data-index="${index}"]`);
                if (sourceItem) {
                    // Get values from source item
                    const jenisBarang = sourceItem.querySelector('input[name*="jenis_barang"]').value;
                    const jumlah = sourceItem.querySelector('input[name*="jumlah"]').value;
                    const satuan = sourceItem.querySelector('input[name*="satuan"]').value;
                    const berat = sourceItem.querySelector('input[name*="berat"]').value;
                    const deskripsi = sourceItem.querySelector('textarea[name*="deskripsi_barang"]').value;

                    // Add new item
                    addBarangItem();

                    // Set values in the new item
                    const newItem = document.querySelector(`[data-index="${barangItemIndex}"]`);
                    if (newItem) {
                        newItem.querySelector('input[name*="jenis_barang"]').value = jenisBarang;
                        newItem.querySelector('input[name*="jumlah"]').value = jumlah;
                        newItem.querySelector('input[name*="satuan"]').value = satuan;
                        newItem.querySelector('input[name*="berat"]').value = berat;
                        newItem.querySelector('textarea[name*="deskripsi_barang"]').value = deskripsi;
                    }
                }
            }

            // Initialize delete buttons on page load
            document.addEventListener('DOMContentLoaded', function() {
                updateDeleteButtons();
            });

            // Global functions untuk barang keluar items
            let barangKeluarItemIndex = <?php echo e($movement->barang_keluar_items ? count($movement->barang_keluar_items) - 1 : 0); ?>;

            // Function untuk menambah item barang keluar baru
            function addBarangKeluarItem() {
                barangKeluarItemIndex++;
                const container = document.getElementById('barang-keluar-items-container');

                const newItem = document.createElement('div');
                newItem.className = 'barang-item mb-3';
                newItem.setAttribute('data-index', barangKeluarItemIndex);

                newItem.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Barang Keluar #${barangKeluarItemIndex + 1}</h6>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeBarangKeluarItem(${barangKeluarItemIndex})">
                            <i class="mdi mdi-delete"></i> Hapus
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Jenis Barang</label>
                                <input type="text" class="form-control" name="barang_keluar[${barangKeluarItemIndex}][jenis_barang]" placeholder="Contoh: Kertas, Tinta, Mesin, dll">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Jumlah</label>
                                <input type="number" class="form-control" name="barang_keluar[${barangKeluarItemIndex}][jumlah]" placeholder="Jumlah" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Satuan</label>
                                <input type="text" class="form-control" name="barang_keluar[${barangKeluarItemIndex}][satuan]" placeholder="pcs, kg, box">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Berat (kg)</label>
                                <input type="number" step="0.01" class="form-control" name="barang_keluar[${barangKeluarItemIndex}][berat]" placeholder="Berat" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Aksi</label>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="duplicateBarangKeluarItem(${barangKeluarItemIndex})" title="Duplikat">
                                        <i class="mdi mdi-content-duplicate"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi Barang</label>
                        <textarea class="form-control" name="barang_keluar[${barangKeluarItemIndex}][deskripsi_barang]" rows="2" placeholder="Deskripsi detail barang"></textarea>
                    </div>
                `;

                container.appendChild(newItem);

                // Scroll ke item baru
                newItem.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });

                // Update tombol hapus untuk item pertama jika ada lebih dari 1 item
                updateBarangKeluarDeleteButtons();
            }

            // Function untuk menghapus item barang keluar
            function removeBarangKeluarItem(index) {
                const item = document.querySelector(`#barang-keluar-items-container [data-index="${index}"]`);
                if (item) {
                    item.remove();
                    updateBarangKeluarDeleteButtons();
                    updateBarangKeluarItemNumbers();
                }
            }

            // Function untuk update tombol hapus barang keluar
            function updateBarangKeluarDeleteButtons() {
                const items = document.querySelectorAll('#barang-keluar-items-container .barang-item');
                const firstItem = items[0];

                if (firstItem) {
                    const deleteBtn = firstItem.querySelector('button[onclick*="removeBarangKeluarItem"]');
                    if (deleteBtn) {
                        deleteBtn.style.display = items.length > 1 ? 'inline-block' : 'none';
                    }
                }
            }

            // Function untuk update nomor item barang keluar
            function updateBarangKeluarItemNumbers() {
                const items = document.querySelectorAll('#barang-keluar-items-container .barang-item');
                items.forEach((item, index) => {
                    const title = item.querySelector('h6');
                    if (title) {
                        title.textContent = `Barang Keluar #${index + 1}`;
                    }
                });
            }

            // Function untuk duplikat item barang keluar
            function duplicateBarangKeluarItem(index) {
                const sourceItem = document.querySelector(`#barang-keluar-items-container [data-index="${index}"]`);
                if (sourceItem) {
                    // Get values from source item
                    const jenisBarang = sourceItem.querySelector('input[name*="jenis_barang"]').value;
                    const jumlah = sourceItem.querySelector('input[name*="jumlah"]').value;
                    const satuan = sourceItem.querySelector('input[name*="satuan"]').value;
                    const berat = sourceItem.querySelector('input[name*="berat"]').value;
                    const deskripsi = sourceItem.querySelector('textarea[name*="deskripsi_barang"]').value;

                    // Add new item
                    addBarangKeluarItem();

                    // Set values in the new item
                    const newItem = document.querySelector(
                        `#barang-keluar-items-container [data-index="${barangKeluarItemIndex}"]`);
                    if (newItem) {
                        newItem.querySelector('input[name*="jenis_barang"]').value = jenisBarang;
                        newItem.querySelector('input[name*="jumlah"]').value = jumlah;
                        newItem.querySelector('input[name*="satuan"]').value = satuan;
                        newItem.querySelector('input[name*="berat"]').value = berat;
                        newItem.querySelector('textarea[name*="deskripsi_barang"]').value = deskripsi;
                    }
                }
            }

            // Initialize barang keluar delete buttons on page load
            document.addEventListener('DOMContentLoaded', function() {
                updateBarangKeluarDeleteButtons();
            });
        </script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/main/security/goods-movement/edit.blade.php ENDPATH**/ ?>