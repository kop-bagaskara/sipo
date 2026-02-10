<?php $__env->startSection('title'); ?>
    Detail Pengajuan <?php echo e($request->request_type === 'vehicle' ? 'Kendaraan' : 'Inventaris'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .preview-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
        }

        .preview-header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .preview-section {
            margin-bottom: 20px;
        }

        .preview-section-title {
            font-weight: bold;
            font-size: 14px;
            background: #f5f5f5;
            padding: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #007bff;
        }

        .preview-row {
            display: flex;
            margin-bottom: 8px;
        }

        .preview-label {
            flex: 0 0 150px;
            font-weight: 600;
            color: #555;
        }

        .preview-value {
            flex: 1;
        }

        .approval-preview {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .approval-step {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }

        .approval-step.approved {
            background-color: #d4edda;
            border-color: #28a745;
        }

        .approval-step.pending {
            background-color: #fff3cd;
            border-color: #ffc107;
        }

        .approval-step.rejected {
            background-color: #f8d7da;
            border-color: #dc3545;
        }

        @media  print {
            .no-print {
                display: none !important;
            }
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
    
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>

    <body data-sidebar="colored">
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('content'); ?>
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Detail Pengajuan
                    <?php echo e($request->request_type === 'vehicle' ? 'Kendaraan' : 'Inventaris'); ?></h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('hr.vehicle-asset.manager-pending')); ?>">Manager
                            Pending</a></li>
                    <li class="breadcrumb-item active">Detail Pengajuan</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-4">
                <div class="card">
                    <div class="card-header bg-info">
                        <h5 class="card-title mb-0  text-white">
                            <i class="mdi mdi-check-circle-outline me-2"></i>Status Approval
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php
                            // Helper function untuk mendapatkan status approval
                            $getApprovalStatus = function ($role, $request) {
                                $status = [
                                    'label' => 'Menunggu',
                                    'color' => 'secondary',
                                    'icon' => 'mdi-clock-outline',
                                    'completed' => false,
                                    'pending' => false,
                                    'rejected' => false,
                                    'approver' => null,
                                    'date' => null,
                                ];

                                if ($role === 'manager') {
                                    if ($request->manager_at) {
                                        if (
                                            $request->status ===
                                            \App\Models\VehicleAssetRequest::STATUS_MANAGER_REJECTED
                                        ) {
                                            $status = [
                                                'label' => 'Ditolak',
                                                'color' => 'danger',
                                                'icon' => 'mdi-close-circle',
                                                'completed' => false,
                                                'pending' => false,
                                                'rejected' => true,
                                                'approver' => \App\Models\User::find($request->manager_id)->name ?? '-',
                                                'date' => $request->manager_at,
                                            ];
                                        } else {
                                            $status = [
                                                'label' => 'Disetujui',
                                                'color' => 'success',
                                                'icon' => 'mdi-check-circle',
                                                'completed' => true,
                                                'pending' => false,
                                                'rejected' => false,
                                                'approver' => \App\Models\User::find($request->manager_id)->name ?? '-',
                                                'date' => $request->manager_at,
                                            ];
                                        }
                                    } elseif (
                                        $request->status === \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER
                                    ) {
                                        $status = [
                                            'label' => 'Menunggu Approval',
                                            'color' => 'warning',
                                            'icon' => 'mdi-clock-outline',
                                            'completed' => false,
                                            'pending' => true,
                                            'rejected' => false,
                                            'approver' => null,
                                            'date' => null,
                                        ];
                                    }
                                } elseif ($role === 'general_manager') {
                                    if ($request->general_approved_at || $request->general_rejected_at) {
                                        $isRejected = $request->general_rejected_at;
                                        $status = [
                                            'label' => $isRejected ? 'Ditolak' : 'Disetujui',
                                            'color' => $isRejected ? 'danger' : 'success',
                                            'icon' => $isRejected ? 'mdi-close-circle' : 'mdi-check-circle',
                                            'completed' => !$isRejected,
                                            'pending' => false,
                                            'rejected' => $isRejected,
                                            'approver' => \App\Models\User::find($request->general_id)->name ?? '-',
                                            'date' => $request->general_approved_at ?? $request->general_rejected_at,
                                        ];
                                    } elseif ($request->general_id) {
                                        $status = [
                                            'label' => 'Menunggu Approval',
                                            'color' => 'warning',
                                            'icon' => 'mdi-clock-outline',
                                            'completed' => false,
                                            'pending' => true,
                                            'rejected' => false,
                                            'approver' => null,
                                            'date' => null,
                                        ];
                                    }
                                } elseif ($role === 'hrga') {
                                    if ($request->hrga_at) {
                                        $isRejected =
                                            $request->status === \App\Models\VehicleAssetRequest::STATUS_HRGA_REJECTED;
                                        $status = [
                                            'label' => $isRejected ? 'Ditolak' : 'Disetujui',
                                            'color' => $isRejected ? 'danger' : 'success',
                                            'icon' => $isRejected ? 'mdi-close-circle' : 'mdi-check-circle',
                                            'completed' => !$isRejected,
                                            'pending' => false,
                                            'rejected' => $isRejected,
                                            'approver' => \App\Models\User::find($request->hrga_id)->name ?? '-',
                                            'date' => $request->hrga_at,
                                        ];
                                    } elseif (
                                        $request->status === \App\Models\VehicleAssetRequest::STATUS_MANAGER_APPROVED
                                    ) {
                                        $status = [
                                            'label' => 'Menunggu Approval',
                                            'color' => 'warning',
                                            'icon' => 'mdi-clock-outline',
                                            'completed' => false,
                                            'pending' => true,
                                            'rejected' => false,
                                            'approver' => null,
                                            'date' => null,
                                        ];
                                    }
                                }

                                return (object) $status;
                            };

                            // Tentukan alur approval
                            $employee = \App\Models\User::find($request->employee_id);
                            $isManagerRequest = $employee && (int) $employee->jabatan === 3;

                            $approvalSteps = [];
                            if ($isManagerRequest) {
                                $approvalSteps = [
                                    [
                                        'role' => 'general_manager',
                                        'title' => 'General Manager',
                                        'icon' => 'mdi-account-supervisor',
                                    ],
                                    ['role' => 'hrga', 'title' => 'HRGA', 'icon' => 'mdi-account-tie'],
                                ];
                            } else {
                                $approvalSteps = [
                                    ['role' => 'manager', 'title' => 'Manager', 'icon' => 'mdi-account-key'],
                                    ['role' => 'hrga', 'title' => 'HRGA', 'icon' => 'mdi-account-tie'],
                                ];
                            }
                        ?>

                        <div class="list-group list-group-flush">
                            <?php $__currentLoopData = $approvalSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $status = $getApprovalStatus($step['role'], $request);
                                    $isLast = $index === count($approvalSteps) - 1;
                                ?>

                                <div
                                    class="list-group-item border-<?php echo e($status->completed ? 'success' : ($status->rejected ? 'danger' : ($status->pending ? 'warning' : 'light'))); ?>">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center
                                                <?php echo e($status->completed ? 'bg-success' : ($status->rejected ? 'bg-danger' : ($status->pending ? 'bg-warning' : 'bg-light'))); ?>"
                                                style="width: 48px; height: 48px;">
                                                <i class="mdi <?php echo e($status->icon); ?> <?php echo e($status->completed || $status->rejected ? 'text-white' : ($status->pending ? 'text-dark' : 'text-muted')); ?>"
                                                    style="font-size: 24px;"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6
                                                        class="mb-1 <?php echo e($status->completed ? 'text-success' : ($status->rejected ? 'text-danger' : ($status->pending ? 'text-warning' : 'text-muted'))); ?>">
                                                        <?php echo e($step['title']); ?>

                                                    </h6>
                                                    <span class="badge bg-<?php echo e($status->color); ?>">
                                                        <?php echo e($status->label); ?>

                                                    </span>
                                                </div>
                                                <?php if(!$isLast): ?>
                                                    <i class="mdi mdi-chevron-down text-muted" style="font-size: 20px;"></i>
                                                <?php endif; ?>
                                            </div>

                                            <?php if($status->completed || $status->rejected): ?>
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <i class="mdi mdi-account me-1"></i><?php echo e($status->approver); ?>

                                                    </small>
                                                    <br>
                                                    <small class="text-muted">
                                                        <i
                                                            class="mdi mdi-calendar me-1"></i><?php echo e($status->date ? $status->date->format('d M Y, H:i') : '-'); ?>

                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-8">
                <!-- Status Badge Header -->
                <div
                    class="card mb-3 border-0
                    <?php echo e($request->status === \App\Models\VehicleAssetRequest::STATUS_HRGA_APPROVED
                        ? 'border-success border-3'
                        : ($request->status === \App\Models\VehicleAssetRequest::STATUS_MANAGER_REJECTED ||
                        $request->status === \App\Models\VehicleAssetRequest::STATUS_HRGA_REJECTED
                            ? 'border-danger border-3'
                            : ($request->status === \App\Models\VehicleAssetRequest::STATUS_MANAGER_APPROVED
                                ? 'border-info border-3'
                                : 'border-warning border-3'))); ?>">
                    <div
                        class="card-body
                        <?php echo e($request->status === \App\Models\VehicleAssetRequest::STATUS_HRGA_APPROVED
                            ? 'bg-success'
                            : ($request->status === \App\Models\VehicleAssetRequest::STATUS_MANAGER_REJECTED ||
                            $request->status === \App\Models\VehicleAssetRequest::STATUS_HRGA_REJECTED
                                ? 'bg-danger'
                                : ($request->status === \App\Models\VehicleAssetRequest::STATUS_MANAGER_APPROVED
                                    ? 'bg-info'
                                    : 'bg-warning'))); ?> bg-opacity-10">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h5 class="card-title mb-1">
                                    <i
                                        class="mdi mdi-<?php echo e($request->request_type === 'vehicle' ? 'car' : 'package-variant'); ?> me-2"></i>
                                    <?php echo e($request->request_type === 'vehicle' ? 'Pengajuan Kendaraan' : 'Pengajuan Inventaris'); ?>

                                </h5>
                                <?php
                                    // $requestNumber = ($request->request_type === 'vehicle' ? 'VH-' : 'AS-') . str_pad($request->id, 4, '0', STR_PAD_LEFT);
                                    $requestNumber = $request->request_number ?? 'N/A';
                                ?>
                                <p class="card-text mb-0">No. <?php echo e($requestNumber); ?></p>
                            </div>
                            <div class="text-end">
                                <?php if($request->status === \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER): ?>
                                    <span class="badge bg-warning fs-6">Menunggu Approval</span>
                                <?php elseif($request->status === \App\Models\VehicleAssetRequest::STATUS_MANAGER_APPROVED): ?>
                                    <span class="badge bg-info fs-6">Disetujui Manager</span>
                                <?php elseif($request->status === \App\Models\VehicleAssetRequest::STATUS_MANAGER_REJECTED): ?>
                                    <span class="badge bg-danger fs-6">Ditolak Manager</span>
                                <?php elseif($request->status === \App\Models\VehicleAssetRequest::STATUS_HRGA_APPROVED): ?>
                                    <span class="badge bg-success fs-6">Disetujui HRGA</span>
                                <?php elseif($request->status === \App\Models\VehicleAssetRequest::STATUS_HRGA_REJECTED): ?>
                                    <span class="badge bg-danger fs-6">Ditolak HRGA</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary fs-6"><?php echo e($request->status); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Pemohon -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="mdi mdi-account me-2"></i>Informasi Pemohon
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Nama Pemohon</small>
                                <div class="fw-bold"><?php echo e($request->employee_name); ?></div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Divisi</small>
                                <div class="fw-bold"><?php echo e($request->divisi_id ?? 'N/A'); ?></div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Tanggal Pengajuan</small>
                                <div class="fw-bold"><?php echo e($request->request_date->format('d M Y')); ?></div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Jenis Pengajuan</small>
                                <div>
                                    <span class="badge bg-<?php echo e($request->request_type === 'vehicle' ? 'success' : 'info'); ?>">
                                        <?php echo e($request->request_type === 'vehicle' ? 'Kendaraan' : 'Inventaris'); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Pengajuan -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="mdi mdi-information me-2"></i>Detail Pengajuan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <small class="text-muted">Tujuan</small>
                                <div class="fw-bold"><?php echo e($request->destination); ?></div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Jenis Keperluan</small>
                                <div class="fw-bold"><?php echo e($request->purpose_type); ?></div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Keperluan</small>
                                <div class="fw-bold"><?php echo e($request->purpose); ?></div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Tanggal Mulai</small>
                                <div class="fw-bold"><?php echo e($request->start_date->format('d M Y')); ?></div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted">Tanggal Selesai</small>
                                <div class="fw-bold"><?php echo e($request->end_date->format('d M Y')); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <?php if($request->request_type === 'vehicle' && $request->license_plate): ?>
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="mdi mdi-car me-2"></i>Detail Kendaraan
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Jenis Kendaraan</small>
                                    <div class="fw-bold"><?php echo e($request->vehicle_type); ?></div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted">Nomor Plat</small>
                                    <div class="fw-bold"><?php echo e($request->license_plate); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php elseif($request->request_type === 'asset'): ?>
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="mdi mdi-package-variant me-2"></i>Detail Inventaris
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-2">
                                    <small class="text-muted">Kategori Inventaris</small>
                                    <div class="fw-bold"><?php echo e($request->asset_category); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Catatan -->
                <?php if($request->notes): ?>
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="mdi mdi-note-text me-2"></i>Catatan
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0"><?php echo e($request->notes); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>


        <?php if($canEdit): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h4 class="card-title mb-0">
                                <i class="mdi mdi-pencil me-2"></i>Edit Pengajuan
                            </h4>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">
                                <i class="mdi mdi-information-outline me-1"></i>
                                Pengajuan ini masih dapat diedit karena belum ada approval dari siapapun.
                            </p>
                            <a href="<?php echo e(route('hr.vehicle-asset.edit', $request->id)); ?>" class="btn btn-warning">
                                <i class="mdi mdi-pencil me-1"></i> Edit Pengajuan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if($canApprove): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h4 class="card-title mb-0">
                                <i class="mdi mdi-check-circle me-2"></i>Form Approval
                            </h4>
                        </div>
                        <div class="card-body">
                            <form id="approvalForm" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label class="form-label">Tindakan <span class="text-danger">*</span></label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="action" id="approve"
                                                value="approve" checked>
                                            <label class="form-check-label text-success" for="approve">
                                                <i class="mdi mdi-check-circle me-1"></i> Setujui Pengajuan
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="action" id="reject"
                                                value="reject">
                                            <label class="form-check-label text-danger" for="reject">
                                                <i class="mdi mdi-close-circle me-1"></i> Tolak Pengajuan
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="manager_notes" class="form-label">Catatan</label>
                                    <textarea class="form-control" id="manager_notes" name="manager_notes" rows="3"
                                        placeholder="Berikan catatan untuk pengajuan ini..."></textarea>
                                    <small class="text-muted">Catatan ini akan dikirim ke pemohon dan HRGA.</small>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="<?php echo e(route('hr.requests.index')); ?>" class="btn btn-secondary">
                                            <i class="mdi mdi-arrow-left me-1"></i> Kembali
                                        </a>
                                        <button type="button" class="btn btn-info ms-2" data-bs-toggle="modal"
                                            data-bs-target="#previewModal">
                                            <i class="mdi mdi-eye me-1"></i> SHOW
                                        </button>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-check me-1"></i> Proses Approval
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <a href="<?php echo e(route('hr.requests.index')); ?>" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="button" class="btn btn-info ms-2" data-toggle="modal"
                                data-target="#previewModal">
                                <i class="mdi mdi-eye me-1"></i> SHOW
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>


        <!-- Modal Preview -->
        <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="previewModalLabel">
                            <i class="mdi mdi-eye me-2"></i>Preview Pengajuan
                        </h5>
                        <button type="button" class="btn-close" data-toggle="modal" data-dismiss="modal"
                            aria-label="Close"><i class="mdi mdi-close"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-end mb-3 no-print">
                            <button type="button" class="btn btn-danger me-2" id="btnCopyImage">
                                <i class="mdi mdi-content-copy me-1"></i> Copy Image
                            </button>
                            <button type="button" class="btn btn-success" id="btnExportPdf">
                                <i class="mdi mdi-file-pdf me-1"></i> Export PDF
                            </button>
                        </div>
                        <div id="previewContent" class="preview-content">
                            <?php
                                $requestNumber = $request->request_number ?? 'N/A';
                                $employee = \App\Models\User::find($request->employee_id);
                                $isManagerRequest = $employee && (int) $employee->jabatan === 3;
                            ?>

                            <div class="preview-header">
                                <h4 class="mb-2">FORM PENGAJUAN
                                    <?php echo e($request->request_type === 'vehicle' ? 'KENDARAAN' : 'INVENTARIS'); ?></h4>
                                <p class="mb-0">No. <?php echo e($requestNumber); ?></p>
                            </div>

                            <!-- Informasi Pemohon -->
                            <div class="preview-section">
                                <div class="preview-section-title">
                                    <i class="mdi mdi-account me-1"></i>INFORMASI PEMOHON
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">Nama Pemohon:</div>
                                    <div class="preview-value"><?php echo e($request->employee_name); ?></div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">Divisi:</div>
                                    <div class="preview-value"><?php echo e($request->divisi_id ?? 'N/A'); ?></div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">Tanggal Pengajuan:</div>
                                    <div class="preview-value"><?php echo e($request->request_date->format('d M Y')); ?></div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">Jenis Pengajuan:</div>
                                    <div class="preview-value">
                                        <?php echo e($request->request_type === 'vehicle' ? 'Kendaraan' : 'Inventaris'); ?></div>
                                </div>
                            </div>

                            <!-- Detail Pengajuan -->
                            <div class="preview-section">
                                <div class="preview-section-title">
                                    <i class="mdi mdi-information me-1"></i>DETAIL PENGAJUAN
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">Tujuan:</div>
                                    <div class="preview-value"><?php echo e($request->destination); ?></div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">Jenis Keperluan:</div>
                                    <div class="preview-value"><?php echo e($request->purpose_type); ?></div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">Keperluan:</div>
                                    <div class="preview-value"><?php echo e($request->purpose); ?></div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">Tanggal Mulai:</div>
                                    <div class="preview-value"><?php echo e($request->start_date->format('d M Y')); ?></div>
                                </div>
                                <div class="preview-row">
                                    <div class="preview-label">Tanggal Selesai:</div>
                                    <div class="preview-value"><?php echo e($request->end_date->format('d M Y')); ?></div>
                                </div>
                            </div>

                            <!-- Detail Kendaraan/Inventaris -->
                            <?php if($request->request_type === 'vehicle' && $request->license_plate): ?>
                                <div class="preview-section">
                                    <div class="preview-section-title">
                                        <i class="mdi mdi-car me-1"></i>DETAIL KENDARAAN
                                    </div>
                                    <div class="preview-row">
                                        <div class="preview-label">Jenis Kendaraan:</div>
                                        <div class="preview-value"><?php echo e($request->vehicle_type); ?></div>
                                    </div>
                                    <div class="preview-row">
                                        <div class="preview-label">Nomor Plat:</div>
                                        <div class="preview-value"><?php echo e($request->license_plate); ?></div>
                                    </div>
                                </div>
                            <?php elseif($request->request_type === 'asset'): ?>
                                <div class="preview-section">
                                    <div class="preview-section-title">
                                        <i class="mdi mdi-package-variant me-1"></i>DETAIL INVENTARIS
                                    </div>
                                    <div class="preview-row">
                                        <div class="preview-label">Kategori Inventaris:</div>
                                        <div class="preview-value"><?php echo e($request->asset_category); ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Catatan -->
                            <?php if($request->notes): ?>
                                <div class="preview-section">
                                    <div class="preview-section-title">
                                        <i class="mdi mdi-note-text me-1"></i>CATATAN
                                    </div>
                                    <div class="preview-row">
                                        <div class="preview-value"><?php echo e($request->notes); ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Status Approval -->
                            <div class="preview-section">
                                <div class="preview-section-title">
                                    <i class="mdi mdi-check-circle me-1"></i>STATUS APPROVAL
                                </div>
                                <?php
                                    $getApprovalStatus = function ($role, $request) {
                                        $status = [
                                            'label' => 'Menunggu',
                                            'completed' => false,
                                            'pending' => false,
                                            'rejected' => false,
                                            'approver' => null,
                                            'date' => null,
                                        ];

                                        if ($role === 'manager') {
                                            if ($request->manager_at) {
                                                if (
                                                    $request->status ===
                                                    \App\Models\VehicleAssetRequest::STATUS_MANAGER_REJECTED
                                                ) {
                                                    $status = [
                                                        'label' => 'Ditolak',
                                                        'completed' => false,
                                                        'pending' => false,
                                                        'rejected' => true,
                                                        'approver' =>
                                                            \App\Models\User::find($request->manager_id)->name ?? '-',
                                                        'date' => $request->manager_at,
                                                    ];
                                                } else {
                                                    $status = [
                                                        'label' => 'Disetujui',
                                                        'completed' => true,
                                                        'pending' => false,
                                                        'rejected' => false,
                                                        'approver' =>
                                                            \App\Models\User::find($request->manager_id)->name ?? '-',
                                                        'date' => $request->manager_at,
                                                    ];
                                                }
                                            } elseif (
                                                $request->status ===
                                                \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER
                                            ) {
                                                $status = [
                                                    'label' => 'Menunggu Approval',
                                                    'completed' => false,
                                                    'pending' => true,
                                                    'rejected' => false,
                                                    'approver' => null,
                                                    'date' => null,
                                                ];
                                            }
                                        } elseif ($role === 'general_manager') {
                                            if ($request->general_approved_at || $request->general_rejected_at) {
                                                $isRejected = $request->general_rejected_at;
                                                $status = [
                                                    'label' => $isRejected ? 'Ditolak' : 'Disetujui',
                                                    'completed' => !$isRejected,
                                                    'pending' => false,
                                                    'rejected' => $isRejected,
                                                    'approver' =>
                                                        \App\Models\User::find($request->general_id)->name ?? '-',
                                                    'date' =>
                                                        $request->general_approved_at ?? $request->general_rejected_at,
                                                ];
                                            } elseif ($request->general_id) {
                                                $status = [
                                                    'label' => 'Menunggu Approval',
                                                    'completed' => false,
                                                    'pending' => true,
                                                    'rejected' => false,
                                                    'approver' => null,
                                                    'date' => null,
                                                ];
                                            }
                                        } elseif ($role === 'hrga') {
                                            if ($request->hrga_at) {
                                                $isRejected =
                                                    $request->status ===
                                                    \App\Models\VehicleAssetRequest::STATUS_HRGA_REJECTED;
                                                $status = [
                                                    'label' => $isRejected ? 'Ditolak' : 'Disetujui',
                                                    'completed' => !$isRejected,
                                                    'pending' => false,
                                                    'rejected' => $isRejected,
                                                    'approver' =>
                                                        \App\Models\User::find($request->hrga_id)->name ?? '-',
                                                    'date' => $request->hrga_at,
                                                ];
                                            } elseif (
                                                $request->status ===
                                                \App\Models\VehicleAssetRequest::STATUS_MANAGER_APPROVED
                                            ) {
                                                $status = [
                                                    'label' => 'Menunggu Approval',
                                                    'completed' => false,
                                                    'pending' => true,
                                                    'rejected' => false,
                                                    'approver' => null,
                                                    'date' => null,
                                                ];
                                            }
                                        }

                                        return (object) $status;
                                    };

                                    $approvalSteps = [];
                                    if ($isManagerRequest) {
                                        $approvalSteps = [
                                            ['role' => 'general_manager', 'title' => 'General Manager'],
                                            ['role' => 'hrga', 'title' => 'HRGA'],
                                        ];
                                    } else {
                                        $approvalSteps = [
                                            ['role' => 'manager', 'title' => 'Manager'],
                                            ['role' => 'hrga', 'title' => 'HRGA'],
                                        ];
                                    }
                                ?>

                                <div class="approval-preview">
                                    <?php $__currentLoopData = $approvalSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $status = $getApprovalStatus($step['role'], $request);
                                        ?>
                                        <div
                                            class="approval-step <?php echo e($status->completed ? 'approved' : ($status->rejected ? 'rejected' : ($status->pending ? 'pending' : ''))); ?>">
                                            <div><strong><?php echo e($step['title']); ?></strong></div>
                                            <div class="small"><?php echo e($status->label); ?></div>
                                            <?php if($status->approver): ?>
                                                <div class="small mt-1">
                                                    <i class="mdi mdi-account"></i> <?php echo e($status->approver); ?>

                                                </div>
                                            <?php endif; ?>
                                            <?php if($status->date): ?>
                                                <div class="small text-muted">
                                                    <i class="mdi mdi-calendar"></i>
                                                    <?php echo e($status->date->format('d M Y, H:i')); ?>

                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php $__env->stopSection(); ?>




    <?php $__env->startSection('scripts'); ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#approvalForm').on('submit', function(e) {
                    e.preventDefault();

                    const action = $('input[name="action"]:checked').val();
                    const notes = $('#manager_notes').val();
                    const actionText = action === 'approve' ? 'menyetujui' : 'menolak';

                    Swal.fire({
                        title: 'Konfirmasi',
                        text: `Apakah Anda yakin ingin ${actionText} pengajuan ini?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: action === 'approve' ? '#28a745' : '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, ' + (action === 'approve' ? 'Setujui' : 'Tolak'),
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const formData = {
                                _token: '<?php echo e(csrf_token()); ?>',
                                manager_notes: notes
                            };

                            const url = action === 'approve' ?
                                '<?php echo e(route('hr.vehicle-asset.manager-approve', $request->id)); ?>' :
                                '<?php echo e(route('hr.vehicle-asset.manager-reject', $request->id)); ?>';

                            $.ajax({
                                url: url,
                                method: 'POST',
                                data: formData,
                                success: function(response) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: response.message ||
                                            `Pengajuan berhasil di${actionText}.`,
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location.href =
                                            '<?php echo e(route('hr.vehicle-asset.manager-pending')); ?>';
                                    });
                                },
                                error: function(xhr) {
                                    let errorMessage =
                                        'Terjadi kesalahan saat memproses approval.';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    Swal.fire({
                                        title: 'Error!',
                                        text: errorMessage,
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            });
                        }
                    });
                });

                // Handle tombol batalkan approval
                $('.disapprove-btn').on('click', function() {
                    const requestId = $(this).data('request-id');

                    Swal.fire({
                        title: 'Konfirmasi Batalkan Approval',
                        text: 'Apakah Anda yakin ingin membatalkan approval ini?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Batalkan',
                        cancelButtonText: 'Tidak'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            Swal.fire({
                                title: 'Memproses...',
                                text: 'Sedang membatalkan approval',
                                icon: 'info',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            $.ajax({
                                url: '<?php echo e(route('hr.approval.disapprove', ':id')); ?>'.replace(
                                    ':id', requestId),
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                data: {
                                    _token: '<?php echo e(csrf_token()); ?>',
                                    request_type: 'vehicle_asset'
                                },
                                success: function(response) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: response.message ||
                                            'Approval berhasil dibatalkan.',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location.href =
                                            '<?php echo e(route('hr.requests.index')); ?>';
                                    });
                                },
                                error: function(xhr) {
                                    let errorMessage =
                                        'Terjadi kesalahan saat membatalkan approval.';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                                        errorMessage = xhr.responseJSON.error;
                                    } else if (xhr.responseText) {
                                        // Try to extract error message from HTML response
                                        const parser = new DOMParser();
                                        const doc = parser.parseFromString(xhr.responseText,
                                            'text/html');
                                        const errorElement = doc.querySelector(
                                            '.error, .alert-danger, [class*="error"]');
                                        if (errorElement) {
                                            errorMessage = errorElement.textContent.trim();
                                        }
                                    }
                                    Swal.fire({
                                        title: 'Error!',
                                        text: errorMessage,
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            });
                        }
                    });
                });

                // Export PDF
                $('#btnExportPdf').on('click', function() {
                    const element = document.getElementById('previewContent');
                    const requestType =
                        '<?php echo e($request->request_type === 'vehicle' ? 'Kendaraan' : 'Inventaris'); ?>';
                    const requestNumber = '<?php echo e($request->request_number ?? 'N/A'); ?>';

                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang membuat PDF',
                        icon: 'info',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    html2canvas(element, {
                        scale: 2,
                        useCORS: true,
                        logging: false
                    }).then(canvas => {
                        const imgData = canvas.toDataURL('image/png');
                        const {
                            jsPDF
                        } = window.jspdf;
                        const pdf = new jsPDF('p', 'mm', 'a4');

                        const imgWidth = 210; // A4 width in mm
                        const pageHeight = 297; // A4 height in mm
                        const imgHeight = canvas.height * imgWidth / canvas.width;
                        let heightLeft = imgHeight;
                        let position = 0;

                        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                        heightLeft -= pageHeight;

                        while (heightLeft >= 0) {
                            position = heightLeft - imgHeight;
                            pdf.addPage();
                            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                            heightLeft -= pageHeight;
                        }

                        pdf.save(`Pengajuan_${requestType}_${requestNumber}.pdf`);

                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'PDF berhasil diunduh',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    }).catch(error => {
                        console.error('Error generating PDF:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Gagal membuat PDF',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                });

                // Copy Image
                $('#btnCopyImage').on('click', function() {
                    const element = document.getElementById('previewContent');

                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang membuat gambar',
                        icon: 'info',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    html2canvas(element, {
                        scale: 2,
                        useCORS: true,
                        logging: false,
                        backgroundColor: '#ffffff'
                    }).then(canvas => {
                        canvas.toBlob(function(blob) {
                            try {
                                const item = new ClipboardItem({
                                    'image/png': blob
                                });
                                navigator.clipboard.write([item]).then(function() {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Gambar berhasil disalin ke clipboard',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    });
                                }).catch(function(err) {
                                    console.error('Clipboard error:', err);
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: 'Gagal menyalin ke clipboard. Silakan coba download gambar.',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Download',
                                        cancelButtonText: 'Tutup'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // Download alternative
                                            const link = document.createElement(
                                                'a');
                                            link.download = 'Pengajuan.png';
                                            link.href = canvas.toDataURL();
                                            link.click();
                                        }
                                    });
                                });
                            } catch (err) {
                                console.error('ClipboardItem error:', err);
                                // Fallback: download image
                                const link = document.createElement('a');
                                const requestType =
                                    '<?php echo e($request->request_type === 'vehicle' ? 'Kendaraan' : 'Inventaris'); ?>';
                                const requestNumber =
                                    '<?php echo e($request->request_number ?? 'N/A'); ?>';
                                link.download = `Pengajuan_${requestType}_${requestNumber}.png`;
                                link.href = canvas.toDataURL();
                                link.click();

                                Swal.fire({
                                    title: 'Info',
                                    text: 'Gambar didownload (clipboard tidak didukung di browser ini)',
                                    icon: 'info',
                                    confirmButtonText: 'OK'
                                });
                            }
                        }, 'image/png');
                    }).catch(error => {
                        console.error('Error generating image:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Gagal membuat gambar',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                });
            });
        </script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/hr/vehicle-asset/show.blade.php ENDPATH**/ ?>