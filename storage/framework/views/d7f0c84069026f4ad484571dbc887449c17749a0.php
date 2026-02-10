<?php $__env->startSection('title'); ?>
    Detail Permohonan
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <style>
        .cust-col {
            white-space: nowrap;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
    Detail Permohonan
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>

    <body data-sidebar="colored">
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('content'); ?>
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Detail Permohonan</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">Detail Permohonan</li>
                </ol>
            </div>
        </div>

        <!-- Request Info -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Informasi Pengajuan</h4>
                        <div>
                            <span class="badge <?php echo e($request->status_badge_class); ?> fs-6"><?php echo e($request->status_label); ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%"><strong>No. Pengajuan:</strong></td>
                                        <td><?php echo e($request->request_number); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Jenis Pengajuan:</strong></td>
                                        <td><?php echo e($request->request_type_label); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Pemohon:</strong></td>
                                        <td><?php echo e($request->employee->name ?? 'N/A'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Bagian:</strong></td>
                                        <?php
                                            $divisi = $request->employee->divisi;
                                            $dataDivisi = \App\Models\Divisi::find($divisi);
                                        ?>
                                        <td><?php echo e($dataDivisi->divisi ?? 'N/A'); ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="30%"><strong>Tanggal Dibuat:</strong></td>
                                        <td><?php echo e($request->created_at->format('d/m/Y H:i')); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Lama Pengajuan:</strong></td>
                                        <td><?php echo e($request->days_since_created); ?> hari</td>
                                    </tr>
                                    <?php if($request->supervisor): ?>
                                        <tr>
                                            <td><strong>Atasan:</strong></td>
                                            <td><?php echo e($request->supervisor->name); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if($request->hr): ?>
                                        <tr>
                                            <td><strong>HR:</strong></td>
                                            <td><?php echo e($request->hr->name); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Details -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Detail Pengajuan</h4>
                    </div>
                    <div class="card-body">
                        <?php if($request->request_type == 'overtime' && $request->overtimeEmployees->count() > 0): ?>
                            <!-- Overtime Employees Table -->
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Karyawan</th>
                                            <th>Bagian</th>
                                            <th>Jam Kerja</th>
                                            <th>Keterangan Pekerjaan</th>
                                            <th>Tanda Tangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $request->overtimeEmployees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($index + 1); ?></td>
                                                <td><?php echo e($employee->employee_name); ?></td>
                                                <td><?php echo e($employee->department); ?></td>
                                                <td><?php echo e($employee->time_range); ?></td>
                                                <td><?php echo e($employee->job_description); ?></td>
                                                <td>
                                                    <?php if($employee->is_signed): ?>
                                                        <span class="badge bg-success">Sudah Ditandatangani</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">Belum Ditandatangani</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <!-- Regular Request Data -->
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tbody>
                                        <?php $__currentLoopData = $request->formatted_request_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td width="30%"><strong><?php echo e($key); ?>:</strong></td>
                                                <td><?php echo e($value); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <?php if($request->notes): ?>
                            <div class="mt-3">
                                <h6><strong>Catatan Tambahan:</strong></h6>
                                <p class="text-muted"><?php echo e($request->notes); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if($request->attachment_path): ?>
                            <div class="mt-3">
                                <h6><strong>Lampiran:</strong></h6>
                                <a href="<?php echo e(asset('storage/' . $request->attachment_path)); ?>" target="_blank"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-download me-2"></i>Download Lampiran
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval History -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="mdi mdi-history me-2"></i>Riwayat Approval
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($approvalHistory) && count($approvalHistory) > 0): ?>
                            <div class="horizontal-timeline p-4" style="background: #f8f9fa; border-radius: 12px;">
                                
                                <div class="d-flex justify-content-between align-items-start position-relative"
                                    style="overflow-x: auto;">
                                    
                                    <div
                                        style="position: absolute; top: 26px; left: 50px; right: 50px; height: 4px; background: #dee2e6; z-index: 0;">
                                    </div>

                                    <?php $__currentLoopData = $approvalHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        
                                        <?php
                                            if ($index === 0) {
                                                $progressWidth = '0%';
                                            } elseif ($history['status'] === 'completed') {
                                                $progressWidth = ($index / (count($approvalHistory) - 1)) * 100;
                                            } else {
                                                $progressWidth = (($index - 1) / (count($approvalHistory) - 1)) * 100;
                                            }
                                        ?>

                                        
                                        <?php if($history['status'] === 'completed'): ?>
                                            <div
                                                style="position: absolute; top: 26px; left: 50px; height: 4px; background: linear-gradient(to right, #28a745, #20c997); width: <?php echo e($progressWidth); ?>%; z-index: 1;">
                                            </div>
                                        <?php endif; ?>

                                        
                                        <div class="flex-shrink-0 text-center"
                                            style="flex: 1; min-width: 140px; max-width: 200px; z-index: 2;">
                                            
                                            <div class="mb-3">
                                                <?php if($history['status'] === 'completed'): ?>
                                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mx-auto shadow-sm"
                                                        style="width: 56px; height: 56px; background: linear-gradient(135deg, #28a745, #20c997);">
                                                        <i class="mdi <?php echo e($history['icon']); ?> text-white"
                                                            style="font-size: 26px;"></i>
                                                    </div>
                                                <?php elseif($history['status'] === 'rejected'): ?>
                                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mx-auto shadow-sm"
                                                        style="width: 56px; height: 56px; background: linear-gradient(135deg, #dc3545, #c82333);">
                                                        <i class="mdi mdi-close text-white" style="font-size: 26px;"></i>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mx-auto bg-light border shadow-sm"
                                                        style="width: 56px; height: 56px; border-width: 3px !important;">
                                                        <i class="mdi mdi-clock-outline text-muted"
                                                            style="font-size: 26px;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            
                                            <div class="card shadow-sm border-0 mb-2">
                                                <div class="card-body p-2">
                                                    <?php if($history['status'] === 'completed'): ?>
                                                        <div class="mb-1">
                                                            <span class="badge bg-success"
                                                                style="font-size: 10px;">Selesai</span>
                                                        </div>
                                                        <h6 class="mb-1"
                                                            style="color: #28a745; font-size: 12px; line-height: 1.3;">
                                                            <i class="mdi mdi-check-circle me-1"></i>
                                                            <?php echo e($history['title']); ?>

                                                        </h6>
                                                    <?php elseif($history['status'] === 'rejected'): ?>
                                                        <div class="mb-1">
                                                            <span class="badge bg-danger"
                                                                style="font-size: 10px;">Ditolak</span>
                                                        </div>
                                                        <h6 class="mb-1"
                                                            style="color: #dc3545; font-size: 12px; line-height: 1.3;">
                                                            <i class="mdi mdi-close-circle me-1"></i>
                                                            <?php echo e($history['title']); ?>

                                                        </h6>
                                                    <?php else: ?>
                                                        <div class="mb-1">
                                                            <span class="badge bg-warning text-dark"
                                                                style="font-size: 10px;">Pending</span>
                                                        </div>
                                                        <h6 class="mb-1 text-muted"
                                                            style="font-size: 12px; line-height: 1.3;">
                                                            <i class="mdi mdi-clock-outline me-1"></i>
                                                            <?php echo e($history['title']); ?>

                                                        </h6>
                                                    <?php endif; ?>

                                                    <?php if($history['approver']): ?>
                                                        <p class="mb-1" style="font-size: 11px;">
                                                            <strong><?php echo e($history['approver']); ?></strong>
                                                        </p>
                                                    <?php endif; ?>

                                                    <?php if($history['timestamp']): ?>
                                                        <p class="mb-0 text-muted" style="font-size: 10px;">
                                                            <?php echo e($history['timestamp']->format('d/m/y H:i')); ?>

                                                        </p>
                                                    <?php endif; ?>

                                                    <?php if($history['notes']): ?>
                                                        <div class="alert alert-info mt-2 py-1 px-2"
                                                            style="font-size: 10px;">
                                                            <small class="mb-0 d-block"
                                                                style="max-height: 40px; overflow: hidden; text-overflow: ellipsis;">
                                                                <strong>Catatan:</strong>
                                                                <?php echo e(\Illuminate\Support\Str::limit($history['notes'], 40)); ?>

                                                            </small>
                                                        </div>
                                                    <?php endif; ?>

                                                    
                                                    <?php if(
                                                        $history['title'] === 'Approval HEAD DIVISI' &&
                                                            $request->replacement_person_name &&
                                                            $history['status'] === 'completed'): ?>
                                                        <div class="alert alert-warning py-1 px-2 mt-2"
                                                            style="font-size: 10px;">
                                                            <strong><i class="mdi mdi-account-switch"></i></strong>
                                                            <span class="d-block mt-1"
                                                                style="max-height: 30px; overflow: hidden;">
                                                                <?php echo e(\Illuminate\Support\Str::limit($request->replacement_person_name, 25)); ?>

                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>

                                
                                <div class="d-flex justify-content-center gap-4 mt-4 pt-3 border-top">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle me-2"
                                            style="width: 14px; height: 14px; background: linear-gradient(135deg, #28a745, #20c997);">
                                        </div>
                                        <small class="text-muted">Selesai</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light border me-2"
                                            style="width: 14px; height: 14px; border-width: 2px !important;"></div>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle me-2"
                                            style="width: 14px; height: 14px; background: linear-gradient(135deg, #dc3545, #c82333);">
                                        </div>
                                        <small class="text-muted">Ditolak</small>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="mdi mdi-information me-2"></i>
                                Tidak ada riwayat approval yang ditemukan.
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
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo e(route('hr.requests.index')); ?>" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left me-2"></i>Kembali
                            </a>

                            <div>
                                <?php if($request->employee_id == auth()->id() && $request->status == 'pending'): ?>
                                    <a href="<?php echo e(route('hr.requests.edit', $request->id)); ?>" class="btn btn-warning me-2">
                                        <i class="mdi mdi-pencil me-2"></i>Edit
                                    </a>
                                    <form method="POST" action="<?php echo e(route('hr.requests.cancel', $request->id)); ?>"
                                        class="d-inline"
                                        onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan ini?')">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-danger">
                                            <i class="mdi mdi-delete me-2"></i>Batalkan
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if(auth()->user()->is_hr && $request->canBeApprovedByHR()): ?>
                                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal"
                                        data-bs-target="#approveModal">
                                        <i class="mdi mdi-check me-2"></i>Setujui
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#rejectModal">
                                        <i class="mdi mdi-delete me-2"></i>Tolak
                                    </button>
                                <?php elseif((int) auth()->user()->jabatan === 5 && isset($canApprove) && $canApprove): ?>
                                    
                                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal"
                                        data-bs-target="#approveModal">
                                        <i class="mdi mdi-check me-2"></i>Setujui
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#rejectModal">
                                        <i class="mdi mdi-delete me-2"></i>Tolak
                                    </button>
                                <?php elseif((int) auth()->user()->jabatan === 4 && isset($canApprove) && $canApprove): ?>
                                    
                                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal"
                                        data-bs-target="#approveModal">
                                        <i class="mdi mdi-check me-2"></i>Setujui
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#rejectModal">
                                        <i class="mdi mdi-delete me-2"></i>Tolak
                                    </button>
                                <?php elseif((int) auth()->user()->jabatan === 3 && isset($canApprove) && $canApprove): ?>
                                    
                                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal"
                                        data-bs-target="#approveModal">
                                        <i class="mdi mdi-check me-2"></i>Setujui
                                    </button>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#rejectModal">
                                        <i class="mdi mdi-delete me-2"></i>Tolak
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approve Modal -->
        <div class="modal fade" id="approveModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="<?php echo e(route('hr.requests.approve', $request->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="modal-header">
                            <h5 class="modal-title">Setujui Pengajuan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Catatan (Opsional)</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Catatan approval"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Setujui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div class="modal fade" id="rejectModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="<?php echo e(route('hr.requests.reject', $request->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="modal-header">
                            <h5 class="modal-title">Tolak Pengajuan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Alasan penolakan" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Tolak</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/hr/requests/show.blade.php ENDPATH**/ ?>