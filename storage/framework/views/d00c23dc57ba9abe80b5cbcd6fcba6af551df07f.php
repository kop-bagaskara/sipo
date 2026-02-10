<?php $__env->startSection('title'); ?>
    Daftar Surat Perintah Lembur (SPL)
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
    Daftar Surat Perintah Lembur (SPL)
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>
    <body data-sidebar="colored">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Daftar Surat Perintah Lembur (SPL)</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item active">SPL</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar SPL</h4>
                    <?php if(auth()->user()->canApprove() || (int)(auth()->user()->jabatan ?? 0) === 5): ?>
                        <a href="<?php echo e(route('hr.spl.create')); ?>" class="btn btn-primary float-end">
                            <i class="mdi mdi-plus"></i> Buat SPL Baru
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(session('success')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo e(session('error')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No. SPL</th>
                                    <th>Tanggal</th>
                                    <th>Shift</th>
                                    <th>Mesin</th>
                                    <th>Jumlah Karyawan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $splRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($spl->spl_number); ?></td>
                                        <td><?php echo e($spl->request_date->format('d/m/Y')); ?></td>
                                        <td><?php echo e($spl->shift); ?></td>
                                        <td><?php echo e($spl->mesin ?? '-'); ?></td>
                                        <td><?php echo e($spl->employees->count()); ?></td>
                                        <td>
                                            <?php
                                                $badgeClass = [
                                                    'draft' => 'secondary',
                                                    'submitted' => 'info',
                                                    'signed' => 'warning',
                                                    'approved_hrd' => 'success',
                                                    'rejected' => 'danger'
                                                ][$spl->status] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?php echo e($badgeClass); ?>"><?php echo e($spl->status_label); ?></span>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('hr.spl.show', $spl->id)); ?>" class="btn btn-sm btn-info">
                                                <i class="mdi mdi-eye"></i> Detail
                                            </a>
                                            <?php if($spl->status !== 'rejected'): ?>
                                                <a href="<?php echo e(route('hr.spl.print', $spl->id)); ?>" class="btn btn-sm btn-primary" target="_blank">
                                                    <i class="mdi mdi-printer"></i> Cetak
                                                </a>
                                            <?php endif; ?>
                                            <?php if($spl->supervisor_id === auth()->id() && in_array($spl->status, ['submitted', 'signed']) && !$spl->signed_document_path): ?>
                                                <a href="<?php echo e(route('hr.spl.show', $spl->id)); ?>#upload-section" class="btn btn-sm btn-warning" title="Upload Dokumen yang Sudah Ditandatangani">
                                                    <i class="mdi mdi-upload"></i> Upload Hasil
                                                </a>
                                            <?php elseif($spl->signed_document_path): ?>
                                                <a href="<?php echo e(asset('storage/' . $spl->signed_document_path)); ?>" target="_blank" class="btn btn-sm btn-success" title="Lihat Dokumen yang Sudah Diupload">
                                                    <i class="mdi mdi-file-check"></i> Lihat Hasil
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data SPL</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <?php echo e($splRequests->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/hr/spl/index.blade.php ENDPATH**/ ?>