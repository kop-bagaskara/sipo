

<?php $__env->startSection('title', 'Edit Pengajuan HR'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Edit Pengajuan HR</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('hr.dashboard')); ?>">HR</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo e(route('hr.requests.index')); ?>">Daftar Pengajuan</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Type Selection -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Jenis Pengajuan</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="btn btn-outline-primary btn-lg w-100 <?php echo e($request->request_type == 'shift_change' ? 'active' : ''); ?>">
                                <i class="fas fa-exchange-alt me-2"></i>
                                Permohonan Tukar Shift
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="btn btn-outline-warning btn-lg w-100 <?php echo e($request->request_type == 'absence' ? 'active' : ''); ?>">
                                <i class="fas fa-calendar-times me-2"></i>
                                Permohonan Tidak Masuk Kerja
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="btn btn-outline-info btn-lg w-100 <?php echo e($request->request_type == 'overtime' ? 'active' : ''); ?>">
                                <i class="fas fa-clock me-2"></i>
                                Surat Perintah Lembur
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="btn btn-outline-success btn-lg w-100 <?php echo e($request->request_type == 'vehicle_asset' ? 'active' : ''); ?>">
                                <i class="fas fa-car me-2"></i>
                                Permintaan Kendaraan/Inventaris
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <?php if($request->request_type == 'shift_change'): ?>
                            Permohonan Tukar Shift
                        <?php elseif($request->request_type == 'absence'): ?>
                            Permohonan Tidak Masuk Kerja
                        <?php elseif($request->request_type == 'overtime'): ?>
                            Surat Perintah Lembur
                        <?php elseif($request->request_type == 'vehicle_asset'): ?>
                            Permintaan Membawa Kendaraan/Inventaris
                        <?php endif; ?>
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('hr.requests.update', $request->id)); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <input type="hidden" name="request_type" value="<?php echo e($request->request_type); ?>">

                        <?php if($request->request_type == 'shift_change'): ?>
                            <?php echo $__env->make('hr.requests.forms.shift-change', ['data' => $data ?? $request->request_data], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php elseif($request->request_type == 'absence'): ?>
                            <?php echo $__env->make('hr.requests.forms.absence', ['data' => $data ?? $request->request_data], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php elseif($request->request_type == 'overtime'): ?>
                            <?php echo $__env->make('hr.requests.forms.overtime', ['data' => $data ?? $request->request_data], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php elseif($request->request_type == 'vehicle_asset'): ?>
                            <?php echo $__env->make('hr.requests.forms.vehicle-asset', ['data' => $data ?? $request->request_data], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endif; ?>

                        <!-- Common Fields -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Catatan Tambahan</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Catatan tambahan (opsional)"><?php echo e(old('notes', $request->notes)); ?></textarea>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Lampiran</label>
                                <?php if($request->attachment_path): ?>
                                <div class="mb-2">
                                    <span class="text-muted">Lampiran saat ini: </span>
                                    <a href="<?php echo e(asset('storage/' . $request->attachment_path)); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download me-1"></i>Download
                                    </a>
                                </div>
                                <?php endif; ?>
                                <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                <div class="form-text">Format yang diperbolehkan: PDF, JPG, JPEG, PNG (Maksimal 2MB)</div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="<?php echo e(route('hr.requests.show', $request->id)); ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Detail
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.btn.active {
    background-color: var(--bs-primary);
    color: white;
    border-color: var(--bs-primary);
}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/hr/requests/edit.blade.php ENDPATH**/ ?>