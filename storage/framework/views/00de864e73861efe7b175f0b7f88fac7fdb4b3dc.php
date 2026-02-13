<?php $__env->startSection('title'); ?>
    Portal Training Karyawan
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
        .assignment-card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 20px;
        }
        .assignment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .progress-bar-custom {
            height: 25px;
            border-radius: 15px;
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-assigned { background: #e3f2fd; color: #1976d2; }
        .status-in-progress { background: #fff3e0; color: #f57c00; }
        .status-completed { background: #e8f5e9; color: #388e3c; }
        .status-expired { background: #ffebee; color: #d32f2f; }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
    Portal Training Karyawan
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>
    <body data-sidebar="colored">
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('content'); ?>
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Portal Training Karyawan</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">Portal Training Karyawan</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="mdi mdi-school mr-2"></i>
                            Training Assignment Saya
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if($assignments->isEmpty()): ?>
                            <div class="alert alert-info">
                                <i class="mdi mdi-information mr-2"></i>
                                Belum ada training yang di-assign untuk Anda.
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-6">
                                        <div class="card assignment-card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <h5 class="card-title mb-0"><?php echo e($assignment->training ? $assignment->training->training_name : 'Training Assignment'); ?></h5>
                                                    <span class="status-badge status-<?php echo e($assignment->status); ?>">
                                                        <?php if($assignment->status == 'assigned'): ?>
                                                            Ditetapkan
                                                        <?php elseif($assignment->status == 'in_progress'): ?>
                                                            Sedang Dikerjakan
                                                        <?php elseif($assignment->status == 'completed'): ?>
                                                            Selesai
                                                        <?php else: ?>
                                                            Expired
                                                        <?php endif; ?>
                                                    </span>
                                                </div>

                                                <p class="text-muted mb-2">
                                                    <i class="mdi mdi-calendar mr-1"></i>
                                                    Ditetapkan: <?php echo e($assignment->assigned_date->format('d M Y')); ?>

                                                </p>

                                                <?php if($assignment->start_date): ?>
                                                    <p class="text-muted mb-2">
                                                        <i class="mdi mdi-calendar-clock mr-1"></i>
                                                        Mulai: <?php echo e($assignment->start_date->format('d M Y')); ?>

                                                    </p>
                                                <?php endif; ?>

                                                <?php if($assignment->deadline_date): ?>
                                                    <p class="text-muted mb-2">
                                                        <i class="mdi mdi-clock mr-1"></i>
                                                        Deadline: <?php echo e($assignment->deadline_date->format('d M Y')); ?>

                                                    </p>
                                                <?php endif; ?>

                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <small class="text-muted">Progress</small>
                                                        <small class="text-muted"><strong><?php echo e(number_format($assignment->progress_percentage, 1)); ?>%</strong></small>
                                                    </div>
                                                    <div class="progress progress-bar-custom">
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                             style="width: <?php echo e($assignment->progress_percentage); ?>%"
                                                             aria-valuenow="<?php echo e($assignment->progress_percentage); ?>"
                                                             aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-3">
                                                    <?php
                                                        $today = \Carbon\Carbon::now()->toDateString();
                                                        $startDate = $assignment->start_date ? $assignment->start_date->format('Y-m-d') : null;
                                                        $canStart = false;
                                                        $disabledReason = '';
                                                        
                                                        if ($assignment->status === 'completed') {
                                                            $disabledReason = 'Training sudah selesai';
                                                        } elseif ($assignment->status === 'expired') {
                                                            $disabledReason = 'Training sudah expired';
                                                        } elseif (!$assignment->is_opened) {
                                                            // Tombol hanya bisa ditekan ketika training sudah dibuka oleh penyelenggara (is_opened = true)
                                                            $disabledReason = 'Training belum dibuka oleh penyelenggara. Silakan tunggu penyelenggara membuka training.';
                                                        } elseif ($assignment->is_opened) {
                                                            // Training sudah dibuka oleh penyelenggara, user bisa mulai
                                                            $canStart = true;
                                                        }
                                                        
                                                        // Cek materials dari pivot table atau material_ids JSON
                                                        $hasMaterial = false;
                                                        $materials = $assignment->materials;
                                                        
                                                        // Fallback: jika materials kosong, cek material_ids JSON
                                                        if ($materials->isEmpty() && $assignment->material_ids) {
                                                            $materialIds = is_array($assignment->material_ids) 
                                                                ? $assignment->material_ids 
                                                                : json_decode($assignment->material_ids, true);
                                                            $hasMaterial = !empty($materialIds);
                                                        } else {
                                                            $hasMaterial = $materials->count() > 0;
                                                        }
                                                    ?>
                                                    
                                                    <?php if($hasMaterial): ?>
                                                        <?php
                                                            // Get first material ID
                                                            $firstMaterialId = null;
                                                            if ($materials->isNotEmpty()) {
                                                                $firstMaterialId = $materials->first()->id;
                                                            } elseif ($assignment->material_ids) {
                                                                $materialIds = is_array($assignment->material_ids) 
                                                                    ? $assignment->material_ids 
                                                                    : json_decode($assignment->material_ids, true);
                                                                $firstMaterialId = !empty($materialIds) ? $materialIds[0] : null;
                                                            }
                                                        ?>
                                                        
                                                        <?php if($assignment->is_opened && $canStart && $firstMaterialId): ?>
                                                            
                                                            <button type="button" class="btn btn-primary btn-sm btn-start-training" 
                                                                    data-assignment-id="<?php echo e($assignment->id); ?>"
                                                                    data-material-id="<?php echo e($firstMaterialId); ?>">
                                                                <i class="mdi mdi-play-circle mr-1"></i>
                                                                Mulai Training
                                                            </button>
                                                        <?php elseif($assignment->is_opened && $firstMaterialId): ?>
                                                            
                                                            <a href="<?php echo e(route('hr.portal-training.materials.show', $firstMaterialId)); ?>"
                                                               class="btn btn-primary btn-sm">
                                                                <i class="mdi mdi-play-circle mr-1"></i>
                                                                Lanjutkan Training
                                                            </a>
                                                        <?php else: ?>
                                                            
                                                            <button type="button" class="btn btn-secondary btn-sm" disabled title="<?php echo e($disabledReason ?: 'Tidak ada materi training'); ?>">
                                                                <i class="mdi mdi-clock-outline mr-1"></i>
                                                                Mulai Training
                                                            </button>
                                                            <?php if($disabledReason): ?>
                                                                <small class="d-block text-muted mt-1">
                                                                    <i class="mdi mdi-information-outline mr-1"></i>
                                                                    <?php echo e($disabledReason); ?>

                                                                </small>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-secondary btn-sm" disabled title="Tidak ada materi training">
                                                            <i class="mdi mdi-clock-outline mr-1"></i>
                                                            Mulai Training
                                                        </button>
                                                        <small class="d-block text-muted mt-1">
                                                            <i class="mdi mdi-information-outline mr-1"></i>
                                                            Tidak ada materi training
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('scripts'); ?>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js')); ?>"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
        <script>
            $(document).ready(function() {
                // Start training button
                $(document).on('click', '.btn-start-training', function() {
                    var assignmentId = $(this).data('assignment-id');
                    var materialId = $(this).data('material-id');
                    var $btn = $(this);
                    
                    // Disable button saat loading
                    $btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin mr-1"></i> Memulai...');
                    
                    $.ajax({
                        url: `<?php echo e(route('hr.portal-training.start', ':id')); ?>`.replace(':id', assignmentId),
                        type: 'POST',
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                // Redirect ke materials
                                window.location.href = response.redirect_url || `<?php echo e(route('hr.portal-training.materials.show', ':id')); ?>`.replace(':id', materialId);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message || 'Terjadi kesalahan saat memulai training.'
                                });
                                // Re-enable button
                                $btn.prop('disabled', false).html('<i class="mdi mdi-play-circle mr-1"></i> Mulai Training');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan saat memulai training.'
                            });
                            // Re-enable button
                            $btn.prop('disabled', false).html('<i class="mdi mdi-play-circle mr-1"></i> Mulai Training');
                        }
                    });
                });
            });
        </script>
    <?php $__env->stopSection(); ?>


<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/portal-training/index.blade.php ENDPATH**/ ?>