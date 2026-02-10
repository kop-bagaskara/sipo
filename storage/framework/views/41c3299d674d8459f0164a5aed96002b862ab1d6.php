<?php $__env->startSection('title'); ?>
    Tambah Keluar/Masuk Barang
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
    Tambah Keluar/Masuk Barang
<?php $__env->stopSection(); ?>

<?php $__env->startSection('body'); ?>

    <body data-sidebar="colored">
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('content'); ?>
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Tambah Keluar/Masuk Barang</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Security</a></li>
                    <li class="breadcrumb-item active">Tambah Keluar/Masuk Barang</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Form Keluar/Masuk Barang</h4>
                        <p class="card-title-desc">Isi form berikut untuk mencatat barang yang keluar atau masuk</p>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo e(route('security.goods-movement.store')); ?>" method="POST" id="movementForm">
                            <?php echo csrf_field(); ?>

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

                            <!-- Header Information -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <h5 class="alert-heading"><i class="mdi mdi-information-outline"></i> Informasi</h5>
                                        <p class="mb-0">No. Urut: <strong><?php echo e($noUrut); ?></strong> | Tanggal:
                                            <strong><?php echo e(date('d/m/Y')); ?></strong> | Petugas:
                                            <strong><?php echo e(Auth::user()->name); ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                                    <select class="form-select form-control" name="lokasi" required>
                                        <option value="">Pilih Lokasi</option>
                                        <option value="1">Lokasi 19 (KRISANTHIUM)</option>
                                        <option value="2">Lokasi 23 (KRISANTHIUM)</option>
                                        <option value="3">Lokasi 15 (BERBEK)</option>
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
                                                    name="tanggal" value="<?php echo e(old('tanggal', date('Y-m-d'))); ?>" required>
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
                                                    value="<?php echo e(old('jam_masuk', date('H:i'))); ?>" required>
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
                                                    name="jam_keluar" id="jam_keluar" value="<?php echo e(old('jam_keluar')); ?>">
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
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="auto_fill_jam_masuk">
                                                    <label class="form-check-label" for="auto_fill_jam_masuk">
                                                        <small class="text-muted">Centang jika hanya update jam keluar (jam
                                                            masuk akan sama dengan jam keluar)</small>
                                                    </label>
                                                </div>
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
                                                        <?php echo e(old('status_laporan') == 'IN' ? 'selected' : ''); ?>>IN (Baru
                                                        Masuk)</option>
                                                    <option value="OUT"
                                                        <?php echo e(old('status_laporan') == 'OUT' ? 'selected' : ''); ?>>OUT (Sudah
                                                        Keluar)</option>
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
                                                    class="form-control form-control <?php $__errorArgs = ['nama_pengunjung'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    name="nama_pengunjung" value="<?php echo e(old('nama_pengunjung')); ?>"
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
                                                <label class="form-label">Perusahaan/Instansi <span
                                                        class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control form-control <?php $__errorArgs = ['perusahaan_asal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    name="perusahaan_asal" value="<?php echo e(old('perusahaan_asal')); ?>"
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
                                                    name="no_telepon" value="<?php echo e(old('no_telepon')); ?>"
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
                                    <div class="card border optional-field">
                                        <div
                                            class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0"><i class="mdi mdi-package-variant"></i> Data
                                                Barang (Opsional)</h5>
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                onclick="addBarangItem()">
                                                <i class="mdi mdi-plus"></i> Tambah Barang
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div id="barang-items-container">
                                                <!-- Item pertama -->
                                                <div class="barang-item mb-3" data-index="0">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="mb-0">Barang #1</h6>
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="removeBarangItem(0)" style="display: none;">
                                                            <i class="mdi mdi-delete"></i> Hapus
                                                        </button>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">Jenis Barang</label>
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
                                                                    value="<?php echo e(old('barang.0.jenis_barang')); ?>"
                                                                    placeholder="Contoh: Kertas, Tinta, Mesin, dll">
                                                                <?php $__errorArgs = ['barang.0.jenis_barang'];
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
                                                        <div class="col-md-2">
                                                            <div class="mb-3">
                                                                <label class="form-label">Jumlah</label>
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
                                                                    value="<?php echo e(old('barang.0.jumlah')); ?>"
                                                                    placeholder="Jumlah" min="0">
                                                                <?php $__errorArgs = ['barang.0.jumlah'];
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
                                                        <div class="col-md-2">
                                                            <div class="mb-3">
                                                                <label class="form-label">Satuan</label>
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
                                                                    value="<?php echo e(old('barang.0.satuan')); ?>"
                                                                    placeholder="pcs, kg, box, pallet">
                                                                <?php $__errorArgs = ['barang.0.satuan'];
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
                                                                    value="<?php echo e(old('barang.0.berat')); ?>"
                                                                    placeholder="Berat" min="0">
                                                                <?php $__errorArgs = ['barang.0.berat'];
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
                                                        <div class="col-md-2">
                                                            <div class="mb-3">
                                                                <label class="form-label">Aksi</label>
                                                                <div class="d-flex gap-1">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-primary"
                                                                        onclick="duplicateBarangItem(0)" title="Duplikat">
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
                                                            name="barang[0][deskripsi_barang]" rows="2" placeholder="Deskripsi detail barang"><?php echo e(old('barang.0.deskripsi_barang')); ?></textarea>
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
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Barang Keluar - Optional -->
                            <div class="row" id="barangKeluarSection" style="display: none;">
                                <div class="col-12">
                                    <div class="card border optional-field">
                                        <div
                                            class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0"><i class="mdi mdi-package-variant-closed"></i>
                                                Data
                                                Barang yang Dibawa Keluar (Opsional)</h5>
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                onclick="addBarangKeluarItem()">
                                                <i class="mdi mdi-plus"></i> Tambah Barang Keluar
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div id="barang-keluar-items-container">
                                                <!-- Item pertama -->
                                                <div class="barang-item mb-3" data-index="0">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <h6 class="mb-0">Barang Keluar #1</h6>
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="removeBarangKeluarItem(0)" style="display: none;">
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
                                                                    name="barang_keluar[0][jumlah]" placeholder="Jumlah"
                                                                    min="0">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="mb-3">
                                                                <label class="form-label">Satuan</label>
                                                                <input type="text" class="form-control"
                                                                    name="barang_keluar[0][satuan]"
                                                                    placeholder="pcs, kg, box, pallet">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="mb-3">
                                                                <label class="form-label">Berat (kg)</label>
                                                                <input type="number" step="0.01" class="form-control"
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
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Waktu & Lokasi -->
                                
                                <!-- Data Kendaraan -->
                                <div class="col-md-6">
                                    <div class="card border optional-field">
                                        <div class="card-header bg-light">
                                            <h5 class="card-title mb-0"><i class="mdi mdi-truck"></i> Data Kendaraan</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label">Jenis Kendaraan <span
                                                        class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control <?php $__errorArgs = ['jenis_kendaraan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    name="jenis_kendaraan" value="<?php echo e(old('jenis_kendaraan')); ?>"
                                                    placeholder="Contoh: Truk, Motor, Mobil">
                                                <?php $__errorArgs = ['jenis_kendaraan'];
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
                                                <label class="form-label">No. Polisi <span
                                                        class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control <?php $__errorArgs = ['no_polisi'];
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

                                <!-- Dokumen & Keterangan -->
                                <div class="col-md-6">
                                    <div class="card border optional-field">
                                        <div class="card-header bg-light">
                                            <h5 class="card-title mb-0"><i class="mdi mdi-file-document"></i> Dokumen &
                                                Keterangan</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label">No. Surat Jalan <span
                                                        class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control <?php $__errorArgs = ['no_surat_jalan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    name="no_surat_jalan" value="<?php echo e(old('no_surat_jalan')); ?>"
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
                                                <label class="form-label">No. Invoice </label>
                                                <input type="text"
                                                    class="form-control <?php $__errorArgs = ['no_invoice'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                    name="no_invoice" value="<?php echo e(old('no_invoice')); ?>"
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
                                                <label class="form-label">Dokumen Pendukung </label>
                                                <textarea class="form-control <?php $__errorArgs = ['dokumen_pendukung'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="dokumen_pendukung"
                                                    rows="2" placeholder="Dokumen lain yang menyertai"><?php echo e(old('dokumen_pendukung')); ?></textarea>
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
                                                <label class="form-label">Keterangan </label>
                                                <textarea class="form-control <?php $__errorArgs = ['keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="keterangan" rows="3"
                                                    placeholder="Keterangan tambahan"><?php echo e(old('keterangan')); ?></textarea>
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


                            <!-- Action Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <button type="submit" class="btn btn-success btn-lg me-2">
                                                <i class="mdi mdi-content-save"></i> Simpan Data
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

    <?php $__env->startSection('scripts'); ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // // SweetAlert untuk error message
            // <?php if(session('error')): ?>
            //     Swal.fire({
            //         icon: 'error',
            //         title: 'Oops...',
            //         text: '<?php echo e(session('error')); ?>',
            //         confirmButtonText: 'OK',
            //         confirmButtonColor: '#d33'
            //     });
            // <?php endif; ?>

            // // SweetAlert untuk validation errors
            // <?php if($errors->any()): ?>
            //     let errorMessages = '';
            //     <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            //         errorMessages += '• <?php echo e($error); ?>\n';
            //     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            //     Swal.fire({
            //         icon: 'error',
            //         title: 'Validasi Gagal',
            //         text: errorMessages,
            //         confirmButtonText: 'OK',
            //         confirmButtonColor: '#d33'
            //     });
            // <?php endif; ?>

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

                    // Always show both asal and tujuan fields
                    asalField.style.display = 'block';
                    tujuanField.style.display = 'block';

                    // Toggle jam keluar and barang keluar based on status
                    if (statusLaporan === 'IN') {
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
                const autoFillCheckbox = document.getElementById('auto_fill_jam_masuk');
                const jamMasukField = document.getElementById('jam_masuk');
                const jamKeluarField = document.getElementById('jam_keluar');

                autoFillCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        // Disable jam masuk field dan set value sama dengan jam keluar
                        jamMasukField.disabled = true;
                        jamMasukField.style.backgroundColor = '#f8f9fa';

                        // Set jam masuk sama dengan jam keluar saat jam keluar berubah
                        jamKeluarField.addEventListener('input', function() {
                            jamMasukField.value = this.value;
                        });

                        // Set initial value jika jam keluar sudah ada
                        if (jamKeluarField.value) {
                            jamMasukField.value = jamKeluarField.value;
                        }
                    } else {
                        // Enable jam masuk field kembali
                        jamMasukField.disabled = false;
                        jamMasukField.style.backgroundColor = '';

                        // Remove event listener
                        jamKeluarField.removeEventListener('input', function() {
                            jamMasukField.value = this.value;
                        });
                    }
                });

                // // Auto-set shift berdasarkan jam
                // const shiftSelect = document.querySelector('select[name="shift"]');
                // if (!shiftSelect.value) {
                //     const currentHour = new Date().getHours();
                //     if (currentHour >= 6 && currentHour < 14) {
                //         shiftSelect.value = 'pagi';
                //     } else if (currentHour >= 14 && currentHour < 22) {
                //         shiftSelect.value = 'siang';
                //     } else {
                //         shiftSelect.value = 'malam';
                //     }
                // }

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
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // Prevent default submit

                    // Validasi client-side untuk multiple items
                    let hasError = false;
                    let errorMessage = '';

                    // Check required fields
                    const requiredFields = [
                        'tanggal', 'nama_pengunjung', 'jam_masuk', 'status_laporan',
                        'jenis_kendaraan', 'no_polisi', 'nama_driver', 'no_surat_jalan'
                    ];

                    requiredFields.forEach(fieldName => {
                        const field = document.querySelector(`[name="${fieldName}"]`);
                        if (!field || !field.value.trim()) {
                            hasError = true;
                            errorMessage += `• Field ${fieldName} harus diisi\n`;
                        }
                    });

                    // Check barang items (optional validation)
                    const barangItems = document.querySelectorAll('.barang-item');

                    // Validate each barang item only if they have some data
                    barangItems.forEach((item, index) => {
                        const jenisBarang = item.querySelector('input[name*="jenis_barang"]');
                        const jumlah = item.querySelector('input[name*="jumlah"]');
                        const satuan = item.querySelector('input[name*="satuan"]');
                        const berat = item.querySelector('input[name*="berat"]');

                        // Only validate if at least jenis_barang is filled
                        if (jenisBarang.value.trim()) {
                            if (!jumlah.value || parseInt(jumlah.value) <= 0) {
                                hasError = true;
                                errorMessage +=
                                    `• Barang #${index + 1}: Jumlah harus lebih dari 0 jika jenis barang diisi\n`;
                            }

                            if (!satuan.value.trim()) {
                                hasError = true;
                                errorMessage +=
                                    `• Barang #${index + 1}: Satuan harus diisi jika jenis barang diisi\n`;
                            }

                            if (berat.value && parseFloat(berat.value) < 0) {
                                hasError = true;
                                errorMessage += `• Barang #${index + 1}: Berat tidak boleh negatif\n`;
                            }
                        }
                    });

                    if (hasError) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: errorMessage,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        });
                        return false;
                    }

                    // Konfirmasi simpan
                    Swal.fire({
                        title: 'Yakin ingin menyimpan?',
                        text: "Data keluar/masuk barang akan disimpan!",
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
                                            'meta[name="csrf-token"]').getAttribute('content'),
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                .then(response => {
                                    console.log('Response status:', response.status);
                                    console.log('Response headers:', response.headers);

                                    if (response.redirected) {
                                        // Handle redirect response (success)
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: 'Data keluar/masuk barang berhasil disimpan',
                                            confirmButtonText: 'OK',
                                            confirmButtonColor: '#28a745'
                                        }).then(() => {
                                            window.location.href = response.url;
                                        });
                                        return;
                                    }

                                    if (response.ok) {
                                        return response.text();
                                    }

                                    // Handle error response
                                    return response.text().then(text => {
                                        throw new Error(
                                            `Server error: ${response.status} - ${text}`
                                            );
                                    });
                                })
                                .then(data => {
                                    // Handle successful response
                                    if (data) {
                                        console.log('Response data:', data);
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: 'Data keluar/masuk barang berhasil disimpan',
                                            confirmButtonText: 'OK',
                                            confirmButtonColor: '#28a745'
                                        }).then(() => {
                                            window.location.href =
                                                '<?php echo e(route('security.goods-movement.index')); ?>';
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Terjadi kesalahan saat menyimpan data: ' +
                                            error.message,
                                        confirmButtonText: 'OK',
                                        confirmButtonColor: '#d33'
                                    });
                                });
                        }
                    });
                });

            });

            // Global functions untuk multiple barang items
            let barangItemIndex = 0;

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
                    <label class="form-label">Jenis Barang</label>
                    <input type="text" class="form-control" name="barang[${barangItemIndex}][jenis_barang]"
                           placeholder="Contoh: Kertas, Tinta, Mesin, dll">
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label">Jumlah</label>
                    <input type="number" class="form-control" name="barang[${barangItemIndex}][jumlah]"
                           placeholder="Jumlah" min="0">
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label">Satuan</label>
                    <input type="text" class="form-control" name="barang[${barangItemIndex}][satuan]"
                           placeholder="pcs, kg, box">
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
                updateBarangKeluarDeleteButtons();
            });

            // Global functions untuk barang keluar items
            let barangKeluarItemIndex = 0;

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
                    <input type="text" class="form-control" name="barang_keluar[${barangKeluarItemIndex}][jenis_barang]"
                           placeholder="Contoh: Kertas, Tinta, Mesin, dll">
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label">Jumlah</label>
                    <input type="number" class="form-control" name="barang_keluar[${barangKeluarItemIndex}][jumlah]"
                           placeholder="Jumlah" min="0">
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label">Satuan</label>
                    <input type="text" class="form-control" name="barang_keluar[${barangKeluarItemIndex}][satuan]"
                           placeholder="pcs, kg, box">
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label">Berat (kg)</label>
                    <input type="number" step="0.01" class="form-control" name="barang_keluar[${barangKeluarItemIndex}][berat]"
                           placeholder="Berat" min="0">
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
            <textarea class="form-control" name="barang_keluar[${barangKeluarItemIndex}][deskripsi_barang]"
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
                updateBarangKeluarDeleteButtons();
            }

            // Function untuk menghapus item barang keluar
            function removeBarangKeluarItem(index) {
                const item = document.querySelector(`[data-index="${index}"]`);
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
        </script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/main/security/goods-movement/create.blade.php ENDPATH**/ ?>