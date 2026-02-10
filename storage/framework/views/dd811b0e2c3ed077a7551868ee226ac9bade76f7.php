<?php $__env->startSection('title'); ?>
    Detail Checklist Kendaraan
<?php $__env->stopSection(); ?>

<?php
    use Illuminate\Support\Facades\Storage;
?>

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

        /* Gradient backgrounds */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%);
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
        }

        .bg-gradient-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }

        .bg-gradient-dark {
            background: linear-gradient(135deg, #2d3436 0%, #636e72 100%);
        }

        /* Card hover effects */
        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }

        /* Avatar animations */
        .avatar-title {
            transition: all 0.3s ease;
        }

        .card:hover .avatar-title {
            transform: scale(1.1);
        }

        /* Badge animations */
        .badge {
            transition: all 0.3s ease;
        }

        .card:hover .badge {
            transform: scale(1.05);
        }

        /* Image hover effects */
        .card-img-top {
            transition: all 0.3s ease;
        }

        .card:hover .card-img-top {
            transform: scale(1.05);
        }

        /* Button hover effects */
        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-title'); ?>
    Detail Checklist Kendaraan
<?php $__env->stopSection(); ?>

<?php $__env->startSection('body'); ?>

    <body data-sidebar="colored">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Detail Checklist Kendaraan</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Security</a></li>
                <li class="breadcrumb-item active">Detail Checklist Kendaraan</li>
            </ol>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <div>
                    <a href="<?php echo e(route('security.vehicle-checklist.index')); ?>" class="btn btn-secondary">
                        <i class="bx bx-arrow-back"></i> Kembali
                    </a>
                    <a href="<?php echo e(route('security.vehicle-checklist.edit', $checklist->id)); ?>" class="btn btn-warning">
                        <i class="bx bx-edit"></i> Edit
                    </a>
                        <?php if($checklist->status == 'keluar'): ?>
                        <button type="button" class="btn btn-success" onclick="showReturnModal()">
                            <i class="bx bx-log-in"></i> Input Kembali
                        </button>
                    <?php endif; ?>
                </div>
                <div>
                    <button onclick="window.print()" class="btn btn-info">
                        <i class="bx bx-printer"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

        <!-- Header Information -->
        <div class="row">
        <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-info text-white border-0">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <div class="avatar-title bg-white bg-opacity-20 rounded-circle">
                                    <i class="bx bx-clipboard text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-0 text-white">Checklist Kendaraan #<?php echo e($checklist->no_urut); ?></h4>
                                <p class="text-white-50 mb-0">Detail informasi kendaraan dan perjalanan</p>
                            </div>
                        </div>
                </div>
                    <div class="card-body p-4">
                    <div class="row">
                            <!-- Driver Info -->
                            <div class="col-lg-4">
                                <div class="text-center p-4 bg-light rounded-3">
                                <div class="avatar-lg mx-auto mb-3">
                                        <div class="avatar-title bg-primary bg-opacity-10 text-primary rounded-circle">
                                            <i class="bx bx-user font-size-24"></i>
                                        </div>
                                    </div>
                                    <h5 class="mb-1 fw-bold">Nama Driver: <?php echo e($checklist->nama_driver); ?></h5>
                                    <p class="text-muted mb-2">Model Kendaraan: <?php echo e($checklist->model_kendaraan); ?></p>
                                    <?php if($checklist->no_polisi): ?>
                                        <span class="badge bg-info text-white fs-6 px-3 py-2">No. Polisi:
                                            <?php echo e($checklist->no_polisi); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Basic Info -->
                            <div class="col-lg-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                            <div class="avatar-sm me-3">
                                                <div class="avatar-title bg-info bg-opacity-10 text-info rounded">
                                                    <i class="bx bx-calendar"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="form-label text-muted small mb-0">Tanggal</label>
                                                <p class="mb-0 fw-bold">Tanggal: <?php echo e($checklist->tanggal); ?></p>
                            </div>
                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                            <div class="avatar-sm me-3">
                                                <div class="avatar-title bg-warning bg-opacity-10 text-warning rounded">
                                                    <i class="bx bx-time"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="form-label text-muted small mb-0">Shift</label>
                                        <p class="mb-0">
                                                    <span
                                                        class="badge bg-<?php echo e($checklist->shift == 'pagi' ? 'warning' : ($checklist->shift == 'siang' ? 'info' : 'dark')); ?> fs-6">
                                                <?php echo e(ucfirst($checklist->shift)); ?>

                                            </span>
                                        </p>
                                    </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                            <div class="avatar-sm me-3">
                                                <div class="avatar-title bg-success bg-opacity-10 text-success rounded">
                                                    <i class="bx bx-shield-check"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="form-label text-muted small mb-0">Petugas Security</label>
                                                <p class="mb-0 fw-bold"><?php echo e($checklist->petugas_security); ?></p>
                                            </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                            <div class="avatar-sm me-3">
                                                <div
                                                    class="avatar-title bg-<?php echo e($checklist->status == 'selesai' ? 'success' : 'warning'); ?> bg-opacity-10 text-<?php echo e($checklist->status == 'selesai' ? 'success' : 'warning'); ?> rounded">
                                                    <i
                                                        class="bx bx-<?php echo e($checklist->status == 'selesai' ? 'check-circle' : 'clock'); ?>"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="form-label text-muted small mb-0">Status</label>
                                        <p class="mb-0">
                                                    <span
                                                        class="badge bg-<?php echo e($checklist->status == 'selesai' ? 'success' : 'warning'); ?> fs-6">
                                                <?php echo e(ucfirst($checklist->status)); ?>

                                            </span>
                                        </p>
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

        <!-- Data Keluar Kendaraan -->
    <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-info text-white border-0">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <div class="avatar-title bg-white bg-opacity-20 rounded-circle">
                                    <i class="bx bx-car text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="card-title mb-0 text-white">Data Keluar Kendaraan</h5>
                                <p class="text-white-50 mb-0 small">Informasi awal saat kendaraan keluar</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="text-center p-4 bg-default bg-opacity-10 rounded-3">
                                    <h3 class="mb-1 fw-bold">
                                        <?php echo e($checklist->jam_out ? \Carbon\Carbon::parse($checklist->jam_out)->format('H:i') : '-'); ?>

                                    </h3>
                                    <p class="text-muted mb-0">Jam Keluar</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-4 bg-default bg-opacity-10 rounded-3">
                                    <h3 class="mb-1 fw-bold">
                                        <?php echo e($checklist->km_awal ? number_format($checklist->km_awal) : '-'); ?>

                                    </h3>
                                    <p class="text-muted mb-0">KM Awal</p>
                        </div>
                    </div>
                            <div class="col-md-4">
                                <div class="text-center p-4 bg-default bg-opacity-10 rounded-3">
                                    <h3 class="mb-1 fw-bold">
                                        <?php echo e($checklist->bbm_awal ? number_format($checklist->bbm_awal, 2) : '-'); ?>

                                    </h3>
                                    <p class="text-muted mb-0">BBM Awal (L)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tujuan & Keterangan -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-gradient-success text-white border-0">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <div class="avatar-title bg-white bg-opacity-20 rounded-circle">
                                    <i class="bx bx-map text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="card-title mb-0 text-white">Tujuan Perjalanan</h5>
                                <p class="text-white-50 mb-0 small">Lokasi tujuan kendaraan</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start">
                            <div class="avatar-sm me-3">
                                <div class="avatar-title bg-success bg-opacity-10 text-success rounded">
                                    <i class="bx bx-map-pin"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 fw-medium"><?php echo e($checklist->tujuan ?: 'Tidak ada tujuan yang dicatat'); ?></p>
                                <small class="text-muted">Lokasi:
                                    <?php switch($checklist->lokasi):
                                        case ('1'): ?> Lokasi 19 (KRISANTHIUM) <?php break; ?>
                                        <?php case ('2'): ?> Lokasi 23 (KRISANTHIUM) <?php break; ?>
                                        <?php case ('3'): ?> Lokasi 15 (BERBEK) <?php break; ?>
                                        <?php default: ?> -
                                    <?php endswitch; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-gradient-success text-white border-0">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm me-3">
                                <div class="avatar-title bg-white bg-opacity-20 rounded-circle">
                                    <i class="bx bx-note text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="card-title mb-0 text-white">Keterangan</h5>
                                <p class="text-white-50 mb-0 small">Catatan tambahan</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start">
                            <div class="avatar-sm me-3">
                                <div class="avatar-title bg-secondary bg-opacity-10 text-secondary rounded">
                                    <i class="bx bx-file-text"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 fw-medium"><?php echo e($checklist->keterangan ?: 'Tidak ada keterangan tambahan'); ?>

                                </p>
                            </div>
                        </div>
                    </div>
                            </div>
                        </div>
                    </div>

        <!-- Foto Dokumentasi -->
        <?php if($checklist->foto_kondisi || $checklist->foto_dashboard || $checklist->foto_driver || $checklist->foto_lainnya): ?>
                        <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-gradient-success text-white border-0">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-3">
                                    <div class="avatar-title bg-white bg-opacity-20 rounded-circle">
                                        <i class="bx bx-camera text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0 text-white">Foto Dokumentasi</h5>
                                    <p class="text-white-50 mb-0 small">Dokumentasi visual kendaraan dan perjalanan</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <?php if($checklist->foto_kondisi): ?>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="position-relative">
                                                <img src="<?php echo e(url('sipo_krisan/public/storage')); ?>/<?php echo e($checklist->foto_kondisi); ?>"
                                                    class="card-img-top" style="height: 200px; object-fit: cover;"
                                                    alt="Foto Kondisi Kendaraan">

                                            </div>
                                            <div class="card-body p-3">
                                                <h6 class="card-title mb-2">Foto Kondisi Kendaraan</h6>
                                                <button class="btn btn-info btn-sm w-100"
                                                    onclick="viewImage('<?php echo e(url('sipo_krisan/public/storage')); ?>/<?php echo e($checklist->foto_kondisi); ?>', 'Foto Kondisi Kendaraan')">
                                                    <i class="bx bx-zoom-in me-1"></i> Lihat Detail
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if($checklist->foto_dashboard): ?>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="position-relative">
                                                <img src="<?php echo e(url('sipo_krisan/public/storage')); ?>/<?php echo e($checklist->foto_dashboard); ?>"
                                                    class="card-img-top" style="height: 200px; object-fit: cover;"
                                                    alt="Foto Dashboard">

                                            </div>
                                            <div class="card-body p-3">
                                                <h6 class="card-title mb-2">Foto Dashboard/SPIDO</h6>
                                                <button class="btn btn-info btn-sm w-100"
                                                    onclick="viewImage('<?php echo e(url('sipo_krisan/public/storage')); ?>/<?php echo e($checklist->foto_dashboard); ?>', 'Foto Dashboard/SPIDO')">
                                                    <i class="bx bx-zoom-in me-1"></i> Lihat Detail
                                                </button>
                                            </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                                <?php if($checklist->foto_driver): ?>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="position-relative">
                                                <img src="<?php echo e(url('sipo_krisan/public/storage')); ?>/<?php echo e($checklist->foto_driver); ?>"
                                                    class="card-img-top" style="height: 200px; object-fit: cover;"
                                                    alt="Foto Driver">

                                            </div>
                                            <div class="card-body p-3">
                                                <h6 class="card-title mb-2">Foto Driver & Kendaraan</h6>
                                                <button class="btn btn-info btn-sm w-100"
                                                    onclick="viewImage('<?php echo e(url('sipo_krisan/public/storage')); ?>/<?php echo e($checklist->foto_driver); ?>', 'Foto Driver & Kendaraan')">
                                                    <i class="bx bx-zoom-in me-1"></i> Lihat Detail
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if($checklist->foto_lainnya): ?>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="position-relative">
                                                <img src="<?php echo e(url('sipo_krisan/public/storage')); ?>/<?php echo e($checklist->foto_lainnya); ?>"
                                                    class="card-img-top" style="height: 200px; object-fit: cover;"
                                                    alt="Foto Lainnya">

                                            </div>
                                            <div class="card-body p-3">
                                                <h6 class="card-title mb-2">Foto Lainnya</h6>
                                                <button class="btn btn-info btn-sm w-100"
                                                    onclick="viewImage('<?php echo e(url('sipo_krisan/public/storage')); ?>/<?php echo e($checklist->foto_lainnya); ?>', 'Foto Lainnya')">
                                                    <i class="bx bx-zoom-in me-1"></i> Lihat Detail
                                                </button>
                                    </div>
                                </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
                </div>
            </div>
        <?php endif; ?>


<!-- Modal Input Kembali -->
        <?php if($checklist->status == 'keluar'): ?>
<div class="modal fade" id="returnModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo e(route('security.vehicle-checklist.return', $checklist->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Input Data Kembali</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Jam Masuk <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control" name="jam_in"
                                                value="<?php echo e(date('H:i')); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">KM Akhir <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="km_akhir"
                                                min="<?php echo e($checklist->km_awal); ?>"
                                                placeholder="Min: <?php echo e(number_format($checklist->km_awal)); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                            <label class="form-label">BBM Akhir (Liter) <span
                                                    class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="bbm_akhir"
                                       min="0" placeholder="BBM saat kembali" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Keterangan Tambahan</label>
                                <textarea class="form-control" name="keterangan_masuk" rows="3"
                                          placeholder="Keterangan saat kembali (opsional)"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
// Function untuk show return modal
function showReturnModal() {
    const modal = new bootstrap.Modal(document.getElementById('returnModal'));
    modal.show();
}

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

// Make functions globally available
window.viewImage = viewImage;
window.showReturnModal = showReturnModal;

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

                .btn,
                .breadcrumb,
                .page-title-box,
                .modal {
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

                .text-primary,
                .text-success,
                .text-info,
                .text-warning,
                .text-danger {
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

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/main/security/vehicle-checklist/show.blade.php ENDPATH**/ ?>