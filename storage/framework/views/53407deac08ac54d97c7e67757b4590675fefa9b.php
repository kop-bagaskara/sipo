<?php $__env->startSection('title'); ?>
    Detail Surat Perintah Lembur (SPL)
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
    Detail Surat Perintah Lembur (SPL)
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>
    <body data-sidebar="colored">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Detail Surat Perintah Lembur (SPL)</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item"><a href="<?php echo e(route('hr.spl.index')); ?>">SPL</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Detail SPL: <?php echo e($splRequest->spl_number); ?></h4>
                    <div class="float-end">
                        <?php
                            $badgeClass = [
                                'draft' => 'secondary',
                                'submitted' => 'info',
                                'signed' => 'warning',
                                'approved_hrd' => 'success',
                                'rejected' => 'danger'
                            ][$splRequest->status] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?php echo e($badgeClass); ?> text-white"><?php echo e($splRequest->status_label); ?></span>
                    </div>
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

                    <!-- Informasi Umum -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary">Informasi Umum</h5>
                            <hr>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>No. SPL:</strong><br>
                            <?php echo e($splRequest->spl_number); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Tanggal:</strong><br>
                            <?php echo e($splRequest->request_date->format('d/m/Y')); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Shift:</strong><br>
                            <?php echo e($splRequest->shift); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Mesin:</strong><br>
                            <?php echo e($splRequest->mesin ?? '-'); ?>

                        </div>
                    </div>

                    <?php if($startTime || $endTime): ?>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <strong>Jam Mulai:</strong><br>
                            <?php echo e($startTime ?? '-'); ?>

                        </div>
                        <div class="col-md-3">
                            <strong>Jam Selesai:</strong><br>
                            <?php echo e($endTime ?? '-'); ?>

                        </div>
                        <?php if($startTime && $endTime): ?>
                        <div class="col-md-3">
                            <strong>Durasi:</strong><br>
                            <?php
                                $start = \Carbon\Carbon::createFromFormat('H:i', $startTime);
                                $end = \Carbon\Carbon::createFromFormat('H:i', $endTime);
                                // Handle jika end_time melewati tengah malam
                                if ($end->lt($start)) {
                                    $end->addDay();
                                }
                                $diff = $start->diff($end);
                                $hours = $diff->h;
                                $minutes = $diff->i;
                            ?>
                            <?php echo e($hours); ?> jam <?php echo e($minutes > 0 ? $minutes . ' menit' : ''); ?>

                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Keperluan:</strong><br>
                            <?php echo e($splRequest->keperluan); ?>

                        </div>
                    </div>

                    <!-- Daftar Karyawan -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary">Daftar Karyawan Lembur</h5>
                            <hr>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIP</th>
                                    <th>Nama Karyawan</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $splRequest->employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($index + 1); ?></td>
                                        <td><?php echo e($employee->nip ?? '-'); ?></td>
                                        <td><?php echo e($employee->employee_name); ?></td>
                                        <td><?php echo e($startTime ?? '-'); ?></td>
                                        <td><?php echo e($endTime ?? '-'); ?></td>
                                        
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Upload Signed Document -->
                    <?php if($splRequest->supervisor_id === auth()->id() && in_array($splRequest->status, ['submitted', 'signed'])): ?>
                        <div class="row mb-4" id="upload-section">
                            <div class="col-12">
                                <h5 class="text-primary">Upload Dokumen SPL yang Sudah Ditandatangani</h5>
                                <hr>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <?php if($splRequest->signed_document_path): ?>
                                    <div class="alert alert-info">
                                        <strong>Dokumen sudah diupload:</strong><br>
                                        <a href="<?php echo e(asset('storage/' . $splRequest->signed_document_path)); ?>" target="_blank" class="btn btn-sm btn-primary mt-2">
                                            <i class="fas fa-eye"></i> Lihat Dokumen
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <form action="<?php echo e(route('hr.spl.upload-signed-document', $splRequest->id)); ?>" method="POST" enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <div class="mb-3">
                                        <label class="form-label">Upload Foto SPL yang Sudah Ditandatangani <span class="text-danger">*</span></label>
                                        <input type="file" name="signed_document" class="form-control <?php $__errorArgs = ['signed_document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" accept="image/jpeg,image/jpg,image/png" required>
                                        <small class="text-muted">Format: JPG, PNG. Maksimal 5MB</small>
                                        <?php $__errorArgs = ['signed_document'];
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
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload"></i> Upload Dokumen
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Approval Form (jika user bisa approve) -->
                    <?php if($canApprove ?? false): ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-info">
                                        <h5 class="card-title mb-0 text-white">
                                            <i class="mdi mdi-check-circle me-2"></i>Form Approval
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="<?php echo e(route('hr.spl.process-approval', $splRequest->id)); ?>" method="POST" id="approvalForm">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="action" id="approvalAction" value="approve">

                                            <div class="mb-3">
                                                <label class="form-label">Catatan <span class="text-danger">*</span> (Wajib diisi untuk penolakan)</label>
                                                <textarea name="notes" class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3" id="approvalNotes" placeholder="Masukkan catatan approval..."></textarea>
                                                <?php $__errorArgs = ['notes'];
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

                                            <div class="d-flex gap-2 justify-content-end">
                                                <button type="button" class="btn btn-danger" onclick="submitApproval('reject')">
                                                    <i class="mdi mdi-close-circle me-2"></i>Tolak
                                                </button>
                                                <button type="button" class="btn btn-success" onclick="submitApproval('approve')">
                                                    <i class="mdi mdi-check-circle me-2"></i>Setujui
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    
                </div>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    function submitApproval(action) {
        const form = document.getElementById('approvalForm');
        const actionInput = document.getElementById('approvalAction');
        const notesInput = document.getElementById('approvalNotes');

        actionInput.value = action;

        // Validasi untuk reject
        if (action === 'reject' && !notesInput.value.trim()) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Catatan wajib diisi untuk penolakan!',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'OK'
            }).then(() => {
                notesInput.focus();
            });
            return;
        }

        // Konfirmasi dengan SweetAlert
        const actionText = action === 'approve' ? 'menyetujui' : 'menolak';
        const actionIcon = action === 'approve' ? 'question' : 'warning';
        const actionColor = action === 'approve' ? '#28a745' : '#dc3545';

        Swal.fire({
            icon: actionIcon,
            title: 'Konfirmasi',
            text: `Apakah Anda yakin ingin ${actionText} SPL ini?`,
            showCancelButton: true,
            confirmButtonText: 'Ya, ' + (action === 'approve' ? 'Setujui' : 'Tolak'),
            cancelButtonText: 'Batal',
            confirmButtonColor: actionColor,
            cancelButtonColor: '#6c757d',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                form.submit();
            }
        });
    }
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/hr/spl/show.blade.php ENDPATH**/ ?>