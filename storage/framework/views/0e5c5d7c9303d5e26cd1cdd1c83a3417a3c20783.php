<?php $__env->startSection('title'); ?>
    Detail Laporan Aktivitas Harian
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .activity-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .log-header {
            /* background: linear-gradient(135deg, #007bff 0%, #007bff 100%); */
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .signature-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-title'); ?>
    Detail Laporan Aktivitas Harian
<?php $__env->stopSection(); ?>

<?php $__env->startSection('body'); ?>
    <body data-sidebar="colored">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Detail Laporan Aktivitas Harian</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Security</a></li>
                <li class="breadcrumb-item"><a href="<?php echo e(route('security.daily-activity.index')); ?>">Laporan Aktivitas Harian</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Header Information -->
            <div class="log-header bg-info" >
                <div class="row" style="color: white;">
                    <div class="col-md-8">
                        <h2 class="mb-2" style="color: white;">PT. KRISANTHIUM O.P.</h2>
                        <p class="mb-1" style="color: white;">Jl. Rungkut Industri III / No. 19</p>
                        <h4 class="mb-0" style="color: white;">LAPORAN AKTIVITAS HARIAN SECURITY</h4>
                    </div>
                    <div class="col-md-4 text-right" style="color: white;">
                        <h5 class="mb-1" style="color: white;">Hari / Tanggal : <?php echo e($log->hari_formatted); ?>, <?php echo e($log->tanggal->format('d F Y')); ?></h5>
                        <h5 class="mb-1" style="color: white;">Shift / Jam : <?php echo e($log->shift_formatted); ?> / <?php echo e($log->jam_mulai_formatted); ?> - <?php echo e($log->jam_selesai_formatted); ?></h5>
                        <h5 class="mb-0" style="color: white;">Personil Jaga : <?php echo e($log->personil_jaga); ?></h5>
                    </div>
                </div>
            </div>

            <!-- Activity Log Table -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Aktivitas</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered activity-table">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">IN</th>
                                    <th width="15%">OUT</th>
                                    <th width="65%">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $log->activityEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="text-center"><?php echo e($entry->urutan); ?></td>
                                        <td class="text-center">
                                            <?php if($entry->time_in_formatted): ?>
                                                <span class="badge badge-info"><?php echo e($entry->time_in_formatted); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($entry->time_out_formatted): ?>
                                                <span class="badge badge-success"><?php echo e($entry->time_out_formatted); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($entry->keterangan); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            <i class="mdi mdi-information"></i> Tidak ada data aktivitas
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Summary Information -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Kondisi Awal</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0"><?php echo e($log->kondisi_awal ?: 'Tidak ada catatan kondisi awal'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Kondisi Akhir</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0"><?php echo e($log->kondisi_akhir ?: 'Tidak ada catatan kondisi akhir'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signature Section -->
            <div class="signature-section">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <h6 class="mb-3">Menyerahkan</h6>
                        <div style="height: 60px; border-bottom: 1px solid #333; margin-bottom: 10px;"></div>
                        <p class="mb-0"><strong><?php echo e($log->menyerahkan_by ?: '________________'); ?></strong></p>
                    </div>
                    <div class="col-md-4 text-center">
                        <h6 class="mb-3">Diterima</h6>
                        <div style="height: 60px; border-bottom: 1px solid #333; margin-bottom: 10px;"></div>
                        <p class="mb-0"><strong><?php echo e($log->diterima_by ?: '________________'); ?></strong></p>
                    </div>
                    <div class="col-md-4 text-center">
                        <h6 class="mb-3">Diketahui</h6>
                        <div style="height: 60px; border-bottom: 1px solid #333; margin-bottom: 10px;"></div>
                        <p class="mb-0"><strong><?php echo e($log->diketahui_by ?: '________________'); ?></strong></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12 text-right">
                        <p class="mb-0">Surabaya, <?php echo e($log->tanggal->format('d F Y')); ?></p>
                        <p class="mb-0"><strong>PAPERLINE</strong></p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <a href="<?php echo e(route('security.daily-activity.edit', $log->id)); ?>" class="btn btn-warning btn-lg me-2">
                                <i class="mdi mdi-pencil"></i> Edit Laporan
                            </a>
                            <a href="<?php echo e(route('security.daily-activity.index')); ?>" class="btn btn-secondary btn-lg me-2">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                            <a href="<?php echo e(route('security.daily-activity.export.single', $log->id)); ?>" class="btn btn-success btn-lg me-2" target="_blank">
                                <i class="mdi mdi-file-pdf"></i> Export PDF
                            </a>
                            <button type="button" class="btn btn-info btn-lg" onclick="window.print()">
                                <i class="mdi mdi-printer"></i> Cetak
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
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
        function printPage() {
            window.print();
        }
    </script>

    <style media="print">
        .btn, .breadcrumb, .page-titles {
            display: none !important;
        }
        .log-header {
            background: #f8f9fa !important;
            color: #000 !important;
            border: 2px solid #000;
        }
        .card {
            border: 1px solid #000 !important;
        }
        .activity-table th {
            background-color: #e9ecef !important;
        }
        .signature-section {
            background-color: #f8f9fa !important;
            border: 1px solid #000 !important;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/main/security/daily-activity/show.blade.php ENDPATH**/ ?>