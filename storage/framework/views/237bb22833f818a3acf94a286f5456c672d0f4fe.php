<?php $__env->startSection('title'); ?>
    Detail Keluar/Masuk Barang
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
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-title'); ?>
    Detail Keluar/Masuk Barang
<?php $__env->stopSection(); ?>

<?php $__env->startSection('body'); ?>
    <body data-sidebar="colored">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Detail Keluar/Masuk Barang</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Security</a></li>
                <li class="breadcrumb-item active">Detail Keluar/Masuk Barang</li>
            </ol>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <div>
                    <a href="<?php echo e(route('security.goods-movement.index')); ?>" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> Kembali
                    </a>
                    <a href="<?php echo e(route('security.goods-movement.edit', $movement->id)); ?>" class="btn btn-warning">
                        <i class="mdi mdi-pencil"></i> Edit
                    </a>
                </div>
                <div>
                    <button onclick="window.print()" class="btn btn-info">
                        <i class="mdi mdi-printer"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Header Information -->
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4 class="card-title mb-0 text-white">
                        <i class="mdi mdi-package-variant"></i> Keluar/Masuk Barang #<?php echo e($movement->no_urut); ?>

                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="avatar-lg mx-auto mb-3">
                                    <div class="avatar-title bg-soft-<?php echo e($movement->movement_color); ?> text-<?php echo e($movement->movement_color); ?> rounded-circle font-size-24">
                                        <i class="mdi mdi-<?php echo e($movement->jenis_movement == 'masuk' ? 'arrow-down-bold' : 'arrow-up-bold'); ?>"></i>
                                    </div>
                                </div>
                                <h5 class="font-size-16 mb-1"><?php echo e($movement->nama_pengunjung); ?></h5>
                                <?php if($movement->perusahaan_asal): ?>
                                    <p class="text-muted mb-2"><?php echo e($movement->perusahaan_asal); ?></p>
                                <?php endif; ?>
                                <span class="badge bg-<?php echo e($movement->movement_color); ?> font-size-12"><?php echo e(ucfirst($movement->jenis_movement)); ?></span>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Tanggal</label>
                                        <p class="mb-0 fw-bold"><?php echo e($movement->tanggal_formatted); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Shift</label>
                                        <p class="mb-0">
                                            <span class="badge bg-<?php echo e($movement->shift == 'pagi' ? 'warning' : ($movement->shift == 'siang' ? 'info' : 'dark')); ?>">
                                                <?php echo e(ucfirst($movement->shift)); ?>

                                            </span>
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Petugas Security</label>
                                        <p class="mb-0"><?php echo e($movement->petugas_security); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Dibuat</label>
                                        <p class="mb-0"><?php echo e($movement->created_at->format('d/m/Y H:i')); ?></p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Lokasi</label>
                                        <p class="mb-0">
                                            <?php switch($movement->lokasi):
                                                case ('1'): ?> Lokasi 19 (KRISANTHIUM) <?php break; ?>
                                                <?php case ('2'): ?> Lokasi 23 (KRISANTHIUM) <?php break; ?>
                                                <?php case ('3'): ?> Lokasi 15 (BERBEK) <?php break; ?>
                                                <?php default: ?> -
                                            <?php endswitch; ?>
                                        </p>
                                    </div>
                                    <?php if($movement->updated_at != $movement->created_at): ?>
                                        <div class="mb-3">
                                            <label class="form-label text-muted">Diupdate</label>
                                            <p class="mb-0"><?php echo e($movement->updated_at->format('d/m/Y H:i')); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Data Barang -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="mdi mdi-package-variant"></i> Data Barang</h5>
                </div>
                <div class="card-body">
                    <?php if($movement->barang_items && count($movement->barang_items) > 0): ?>
                        <!-- Multiple Items Display -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="25%">Jenis Barang</th>
                                        <th width="15%">Jumlah</th>
                                        <th width="10%">Satuan</th>
                                        <th width="10%">Berat (kg)</th>
                                        <th width="35%">Deskripsi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $movement->barang_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td class="text-center">
                                                <span class="badge bg-primary"><?php echo e($index + 1); ?></span>
                                            </td>
                                            <td>
                                                <strong><?php echo e($item['jenis_barang'] ?? '-'); ?></strong>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold"><?php echo e(number_format($item['jumlah'] ?? 0)); ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info"><?php echo e($item['satuan'] ?? '-'); ?></span>
                                            </td>
                                            <td class="text-end">
                                                <?php if(isset($item['berat']) && $item['berat']): ?>
                                                    <?php echo e(number_format($item['berat'], 2)); ?>

                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if(isset($item['deskripsi_barang']) && $item['deskripsi_barang']): ?>
                                                    <?php echo e($item['deskripsi_barang']); ?>

                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="2" class="text-end fw-bold">Total:</td>
                                        <td class="text-end fw-bold"><?php echo e(number_format($movement->total_jumlah_barang)); ?></td>
                                        <td class="text-center">-</td>
                                        <td class="text-end fw-bold">
                                            <?php if($movement->total_berat_barang > 0): ?>
                                                <?php echo e(number_format($movement->total_berat_barang, 2)); ?>

                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php else: ?>
                        <!-- Fallback untuk data lama (backward compatibility) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Jenis Barang</label>
                                    <h6 class="mb-0"><?php echo e($movement->jenis_barang ?? '-'); ?></h6>
                                </div>
                                <?php if($movement->deskripsi_barang): ?>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Deskripsi</label>
                                        <p class="mb-0"><?php echo e($movement->deskripsi_barang); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <?php if($movement->jumlah): ?>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Jumlah</label>
                                        <h6 class="mb-0"><?php echo e(number_format($movement->jumlah)); ?> <?php echo e($movement->satuan); ?></h6>
                                    </div>
                                <?php endif; ?>
                                <?php if($movement->berat): ?>
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Berat</label>
                                        <h6 class="mb-0"><?php echo e(number_format($movement->berat, 2)); ?> kg</h6>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Waktu & Lokasi -->

    </div>

    <div class="row">
        <!-- Data Pengunjung -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="mdi mdi-account"></i> Data Pengunjung</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Nama</label>
                        <h6 class="mb-0"><?php echo e($movement->nama_pengunjung); ?></h6>
                    </div>
                    <?php if($movement->perusahaan_asal): ?>
                        <div class="mb-3">
                            <label class="form-label text-muted">Perusahaan</label>
                            <p class="mb-0"><?php echo e($movement->perusahaan_asal); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if($movement->no_telepon): ?>
                        <div class="mb-3">
                            <label class="form-label text-muted">No. Telepon</label>
                            <p class="mb-0"><?php echo e($movement->no_telepon); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Data Kendaraan -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="mdi mdi-truck"></i> Data Kendaraan</h5>
                </div>
                <div class="card-body">
                    <?php if($movement->jenis_kendaraan || $movement->no_polisi || $movement->nama_driver): ?>
                        <?php if($movement->jenis_kendaraan): ?>
                            <div class="mb-3">
                                <label class="form-label text-muted">Jenis Kendaraan</label>
                                <p class="mb-0"><?php echo e($movement->jenis_kendaraan); ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if($movement->no_polisi): ?>
                            <div class="mb-3">
                                <label class="form-label text-muted">No. Polisi</label>
                                <p class="mb-0"><?php echo e($movement->no_polisi); ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if($movement->nama_driver): ?>
                            <div class="mb-3">
                                <label class="form-label text-muted">Nama Driver</label>
                                <p class="mb-0"><?php echo e($movement->nama_driver); ?></p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted">Tidak ada data kendaraan</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="mdi mdi-clock-outline"></i> Waktu & Lokasi</h5>
                </div>
                <div class="card-body">
                    <?php if($movement->jenis_movement == 'masuk'): ?>
                        <?php if($movement->jam_masuk): ?>
                            <div class="mb-3">
                                <label class="form-label text-muted">Jam Masuk</label>
                                <h6 class="mb-0 text-success"><?php echo e(\Carbon\Carbon::parse($movement->jam_masuk)->format('H:i')); ?></h6>
                            </div>
                        <?php endif; ?>
                        <?php if($movement->asal): ?>
                            <div class="mb-3">
                                <label class="form-label text-muted">Asal Barang</label>
                                <p class="mb-0"><?php echo e($movement->asal); ?></p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if($movement->jam_keluar): ?>
                            <div class="mb-3">
                                <label class="form-label text-muted">Jam Keluar</label>
                                <h6 class="mb-0 text-primary"><?php echo e(\Carbon\Carbon::parse($movement->jam_keluar)->format('H:i')); ?></h6>
                            </div>
                        <?php endif; ?>
                        <?php if($movement->tujuan): ?>
                            <div class="mb-3">
                                <label class="form-label text-muted">Tujuan Barang</label>
                                <p class="mb-0"><?php echo e($movement->tujuan); ?></p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if($movement->alamat): ?>
                        <div class="mb-3">
                            <label class="form-label text-muted">Alamat</label>
                            <p class="mb-0"><?php echo e($movement->alamat); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Dokumen -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="mdi mdi-file-document"></i> Dokumen</h5>
                </div>
                <div class="card-body">
                    <?php if($movement->no_surat_jalan || $movement->no_invoice || $movement->dokumen_pendukung): ?>
                        <?php if($movement->no_surat_jalan): ?>
                            <div class="mb-3">
                                <label class="form-label text-muted">No. Surat Jalan</label>
                                <p class="mb-0"><?php echo e($movement->no_surat_jalan); ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if($movement->no_invoice): ?>
                            <div class="mb-3">
                                <label class="form-label text-muted">No. Invoice</label>
                                <p class="mb-0"><?php echo e($movement->no_invoice); ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if($movement->dokumen_pendukung): ?>
                            <div class="mb-3">
                                <label class="form-label text-muted">Dokumen Pendukung</label>
                                <p class="mb-0"><?php echo e($movement->dokumen_pendukung); ?></p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted">Tidak ada dokumen pendukung</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Keterangan & Approval -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="mdi mdi-note-text"></i> Keterangan & Approval</h5>
                </div>
                <div class="card-body">
                    <?php if($movement->keterangan): ?>
                        <div class="mb-3">
                            <label class="form-label text-muted">Keterangan</label>
                            <p class="mb-0"><?php echo e($movement->keterangan); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if($movement->catatan_security): ?>
                        <div class="mb-3">
                            <label class="form-label text-muted">Catatan Security</label>
                            <p class="mb-0"><?php echo e($movement->catatan_security); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// SweetAlert untuk success message
<?php if(session('success')): ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?php echo e(session('success')); ?>',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        toast: true,
        position: 'top-end'
    });
<?php endif; ?>

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

// Print styles
window.addEventListener('beforeprint', function() {
    document.body.classList.add('print-mode');
});

window.addEventListener('afterprint', function() {
    document.body.classList.remove('print-mode');
});
</script>

<style>
@media  print {
    .btn, .breadcrumb, .page-title-box, .modal {
        display: none !important;
    }

    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
        margin-bottom: 20px !important;
    }

    .card-header {
        background-color: #f8f9fa !important;
        color: #000 !important;
        border-bottom: 1px solid #000 !important;
    }

    .text-primary, .text-success, .text-info, .text-warning, .text-danger {
        color: #000 !important;
    }

    .badge {
        border: 1px solid #000 !important;
        color: #000 !important;
        background-color: #fff !important;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/main/security/goods-movement/show.blade.php ENDPATH**/ ?>