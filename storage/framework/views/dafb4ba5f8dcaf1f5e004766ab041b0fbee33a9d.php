<?php $__env->startSection('title'); ?>
    Riwayat Training
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />
    <style>
        .status-passed {
            background-color: #d4edda;
            color: #155724;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        .score-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
        }
        .score-passed {
            background: #28a745;
            color: white;
        }
        .score-failed {
            background: #dc3545;
            color: white;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
    Riwayat Training
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>
    <body data-sidebar="colored">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Riwayat Training</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('hr.portal-training.index')); ?>">Portal Training</a></li>
                <li class="breadcrumb-item active">Riwayat</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Hasil Training Ujian</h4>
                </div>
                <div class="card-body">
                    <?php if($results->isEmpty()): ?>
                        <div class="alert alert-info">
                            <i class="mdi mdi-information mr-2"></i>
                            Belum ada riwayat ujian training.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Training</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Skor</th>
                                        <th>Status</th>
                                        <th>Sertifikat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($results->firstItem() + $index); ?></td>
                                            <td>
                                                <strong><?php echo e($result->assignment->training->training_name ?? '-'); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo e($result->assignment->materials->first()->title ?? '-'); ?></small>
                                            </td>
                                            <td><?php echo e($result->completed_date->format('d M Y H:i')); ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="score-circle <?php echo e($result->status == 'passed' ? 'score-passed' : 'score-failed'); ?> mr-2">
                                                        <?php echo e($result->total_score); ?>

                                                    </div>
                                                    <div>
                                                        <div>Max: <?php echo e($result->max_score); ?></div>
                                                        <div class="text-muted small">Passing: <?php echo e($result->passing_score); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if($result->status == 'passed'): ?>
                                                    <span class="status-passed">
                                                        <i class="mdi mdi-check-circle"></i> LULUS
                                                    </span>
                                                <?php else: ?>
                                                    <span class="status-failed">
                                                        <i class="mdi mdi-close-circle"></i> TIDAK LULUS
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($result->status == 'passed' && $result->certificate_path): ?>
                                                    <a href="<?php echo e(asset($result->certificate_path)); ?>"
                                                       target="_blank"
                                                       class="btn btn-sm btn-success">
                                                        <i class="mdi mdi-download"></i> Download
                                                    </a>
                                                <?php elseif($result->status == 'passed'): ?>
                                                    <span class="text-muted">
                                                        <i class="mdi mdi-clock-outline"></i> Sedang diproses
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if($results->hasPages()): ?>
                            <div class="d-flex justify-content-center mt-3">
                                <?php echo e($results->links()); ?>

                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/portal-training/history.blade.php ENDPATH**/ ?>