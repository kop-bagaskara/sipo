<?php $__env->startSection('title'); ?>
    HRD Pending Approval
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css')); ?>" rel="stylesheet"
        type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css')); ?>" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .cust-col {
            white-space: nowrap;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
    HRD Pending Approval
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>

    <body data-sidebar="colored">
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('content'); ?>
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">HRD Pending Approval</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">HRD Pending Approval</li>
                </ol>
            </div>
        </div>

        <!-- Info Card: Permohonan yang Bisa Di-Approve -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-header bg-info">
                        <h5 class="card-title mb-0 text-white">
                            <i class="mdi mdi-information-outline me-2"></i>
                            Informasi Permohonan yang Bisa Di-Approve
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="mdi mdi-account-tie me-2"></i>Sebagai HRD</h6>
                                <ul class="list-unstyled">
                                    <li>
                                        <i class="mdi <?php echo e($approvalInfo['can_approve_absence'] ? 'mdi-check-circle text-success' : 'mdi-close-circle text-danger'); ?> me-2"></i>
                                        <strong>Permohonan Tidak Masuk Kerja (Absence):</strong>
                                        <?php if($approvalInfo['can_approve_absence']): ?>
                                            <span class="badge badge-success">Bisa Approve</span>
                                            <?php if(!empty($approvalInfo['absence_approval_order'])): ?>
                                                <div class="mt-2">
                                                    <small class="text-muted d-block mb-1"><strong>Urutan Approval Global:</strong></small>
                                                    <ol class="mb-0" style="padding-left: 20px;">
                                                        <?php $__currentLoopData = $approvalInfo['absence_approval_order']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orderItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li class="mb-1">
                                                                <span class="badge badge-<?php echo e($orderItem['is_current_user'] ? 'success' : 'primary'); ?>">
                                                                    Urutan <?php echo e($orderItem['order']); ?>: <?php echo e($orderItem['role']); ?>

                                                                    <?php if($orderItem['is_current_user']): ?>
                                                                        <i class="mdi mdi-account-check text-warning" title="Anda"></i>
                                                                    <?php endif; ?>
                                                                </span>
                                                            </li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ol>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Tidak Bisa Approve</span>
                                            <small class="text-muted d-block">HR tidak ada di approval flow untuk absence</small>
                                        <?php endif; ?>
                                    </li>
                                    <li class="mt-2">
                                        <i class="mdi <?php echo e($approvalInfo['can_approve_shift_change'] ? 'mdi-check-circle text-success' : 'mdi-close-circle text-danger'); ?> me-2"></i>
                                        <strong>Permohonan Tukar Shift:</strong>
                                        <?php if($approvalInfo['can_approve_shift_change']): ?>
                                            <span class="badge badge-success">Bisa Approve</span>
                                            <?php if(!empty($approvalInfo['shift_change_approval_order'])): ?>
                                                <div class="mt-2">
                                                    <small class="text-muted d-block mb-1"><strong>Urutan Approval Global:</strong></small>
                                                    <ol class="mb-0" style="padding-left: 20px;">
                                                        <?php $__currentLoopData = $approvalInfo['shift_change_approval_order']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orderItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li class="mb-1">
                                                                <span class="badge badge-<?php echo e($orderItem['is_current_user'] ? 'success' : 'primary'); ?>">
                                                                    Urutan <?php echo e($orderItem['order']); ?>: <?php echo e($orderItem['role']); ?>

                                                                    <?php if($orderItem['is_current_user']): ?>
                                                                        <i class="mdi mdi-account-check text-warning" title="Anda"></i>
                                                                    <?php endif; ?>
                                                                </span>
                                                            </li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ol>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Tidak Bisa Approve</span>
                                            <small class="text-muted d-block">HR tidak ada di approval flow untuk shift change</small>
                                        <?php endif; ?>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="mdi mdi-information me-2"></i>Informasi</h6>
                                <p class="mb-2">Sebagai <strong>HRD</strong>, Anda dapat menyetujui permohonan dari semua divisi setelah semua level approval sebelumnya sudah disetujui.</p>
                                <p class="mb-0 text-muted"><small>HR adalah approver terakhir dalam alur approval dan memiliki akses ke semua permohonan yang sudah melewati level sebelumnya.</small></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <?php
            $dataLemburCount = $overtimeRequests->count() + ($splRequests ?? collect())->count();
            $totalPending =
                $formRequestsWithAccess->count() +
                $dataLemburCount +
                $vehicleRequests->count() +
                $assetRequests->count();
        ?>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-warning"><?php echo e($totalPending); ?></h3>
                        <p class="text-muted mb-0">Total Menunggu</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-info"><?php echo e($formRequestsWithAccess->count()); ?></h3>
                        <p class="text-muted mb-0">Form Karyawan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-warning"><?php echo e($dataLemburCount); ?></h3>
                        <p class="text-muted mb-0">Data Lembur</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-success"><?php echo e($vehicleRequests->count() + $assetRequests->count()); ?></h3>
                        <p class="text-muted mb-0">Kendaraan & Inventaris</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requests Table with Tabs -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Daftar Pengajuan Menunggu Approval HRD</h4>
                    </div>
                    <div class="card-body">
                        <!-- Tabs -->
                        <ul class="nav nav-tabs mb-4" id="requestTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="forms-tab" data-toggle="tab" data-target="#forms"
                                    type="button" role="tab">
                                    <i class="mdi mdi-file-document me-1"></i> Form Karyawan
                                    <span class="badge badge-info"><?php echo e($formRequestsWithAccess->count()); ?></span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="overtime-tab" data-toggle="tab" data-target="#overtime"
                                    type="button" role="tab">
                                    <i class="mdi mdi-clock me-1"></i> Data Lembur
                                    <span class="badge badge-warning"><?php echo e($dataLemburCount); ?></span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="vehicle-tab" data-toggle="tab" data-target="#vehicle"
                                    type="button" role="tab">
                                    <i class="mdi mdi-car me-1"></i> Kendaraan
                                    <span class="badge badge-success"><?php echo e($vehicleRequests->count()); ?></span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="asset-tab" data-toggle="tab" data-target="#asset"
                                    type="button" role="tab">
                                    <i class="mdi mdi-package-variant me-1"></i> Inventaris
                                    <span class="badge badge-info"><?php echo e($assetRequests->count()); ?></span>
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="requestTabsContent">
                            <!-- Form Karyawan Tab -->
                            <div class="tab-pane fade show active" id="forms" role="tabpanel">
                                <?php if($formRequestsWithAccess->count() > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No. Pengajuan</th>
                                                    <th>Jenis</th>
                                                    <th>Pemohon</th>
                                                    <th>Divisi</th>
                                                    <th>Status Approval</th>
                                                    <th>Tanggal Dibuat</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $formRequestsWithAccess; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><span class="fw-bold"><?php echo e($request->request_number); ?></span>
                                                        </td>
                                                        <td><span
                                                                class="badge bg-info text-white"><?php echo e($request->request_type_label); ?></span>
                                                        </td>
                                                        <td><?php echo e($request->employee->name ?? 'N/A'); ?></td>
                                                        <td>
                                                            <?php if($request->employee && $request->employee->divisiUser): ?>
                                                                <span class="badge badge-secondary">
                                                                    <?php echo e($request->employee->divisiUser->divisi ?? $request->employee->divisiUser->nama_divisi ?? 'Divisi ' . $request->employee->divisi); ?>

                                                                </span>
                                                            <?php elseif($request->employee): ?>
                                                                <span class="badge badge-secondary">
                                                                    Divisi <?php echo e($request->employee->divisi ?? 'N/A'); ?>

                                                                </span>
                                                            <?php else: ?>
                                                                <span class="badge badge-secondary">N/A</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $request->current_approval_status_text; ?>

                                                            <?php if(!($request->can_approve ?? false) && isset($request->cannot_approve_reason)): ?>
                                                                <br><small class="text-danger">
                                                                    <i class="mdi mdi-alert-circle"></i> <?php echo e($request->cannot_approve_reason); ?>

                                                                </small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo e($request->created_at->format('d/m/Y H:i')); ?></td>
                                                        <td>
                                                            <?php if($request->can_approve ?? false): ?>
                                                                <a href="<?php echo e(route('hr.approval.show', $request->id)); ?>"
                                                                    class="btn btn-sm btn-outline-primary" title="Bisa di-approve">
                                                                    <i class="mdi mdi-eye"></i> Lihat
                                                                </a>
                                                            <?php else: ?>
                                                                <button class="btn btn-sm btn-outline-secondary" disabled
                                                                    title="<?php echo e($request->cannot_approve_reason ?? 'Tidak bisa di-approve'); ?>">
                                                                    <i class="mdi mdi-eye-off"></i> Lihat
                                                                </button>
                                                                <?php if(isset($request->cannot_approve_reason)): ?>
                                                                    <small class="d-block text-danger mt-1" style="font-size: 0.75rem; max-width: 200px;">
                                                                        <i class="mdi mdi-alert-circle"></i> <?php echo e($request->cannot_approve_reason); ?>

                                                                    </small>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="mdi mdi-check-circle text-success" style="font-size: 4rem;"></i>
                                        <h5 class="text-muted mt-3">Tidak ada form pengajuan yang menunggu approval</h5>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Data Lembur Tab -->
                            <div class="tab-pane fade" id="overtime" role="tabpanel">
                                <?php if($dataLemburCount > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No. Dokumen</th>
                                                    <th>Tanggal</th>
                                                    <th>Shift</th>
                                                    <th>Divisi</th>
                                                    <th>Supervisor</th>
                                                    <th>Jml Karyawan</th>
                                                    <th>Keperluan</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $splRequests ?? collect(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><span class="fw-bold"><?php echo e($spl->spl_number); ?></span></td>
                                                        <td><?php echo e($spl->request_date->format('d/m/Y')); ?></td>
                                                        <td><span class="badge bg-info text-white"><?php echo e($spl->shift); ?></span></td>
                                                        <td><?php echo e($spl->divisi_name ?? 'N/A'); ?></td>
                                                        <td><?php echo e($spl->supervisor->name ?? 'N/A'); ?></td>
                                                        <td><span class="badge bg-info text-white"><?php echo e($spl->employees->count() ?? 0); ?> orang</span></td>
                                                        <td><small><?php echo e(\Illuminate\Support\Str::limit($spl->keperluan, 40)); ?></small></td>
                                                        <td><span class="badge bg-warning text-white"><?php echo e($spl->status_label); ?></span></td>
                                                        <td><a href="<?php echo e(route('hr.spl.show', $spl->id)); ?>" class="btn btn-sm btn-outline-warning"><i class="mdi mdi-eye"></i> Lihat</a></td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php $__currentLoopData = $overtimeRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><span class="fw-bold">-</span></td>
                                                        <td><?php echo e($entry->request_date->format('d/m/Y')); ?></td>
                                                        <td><span class="badge bg-secondary text-white">-</span></td>
                                                        <td><?php echo e($entry->employee_name); ?></td>
                                                        <td>-</td>
                                                        <td><span class="badge bg-info text-white">1 orang</span></td>
                                                        <td><small><?php echo e(\Illuminate\Support\Str::limit($entry->location ?? '-', 40)); ?></small></td>
                                                        <td><span class="badge bg-warning text-white">Pending</span></td>
                                                        <td><a href="<?php echo e(route('hr.overtime.index')); ?>" class="btn btn-sm btn-outline-warning"><i class="mdi mdi-eye"></i> Lihat</a></td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="mdi mdi-check-circle text-success" style="font-size: 4rem;"></i>
                                        <h5 class="text-muted mt-3">Tidak ada data lembur yang menunggu approval</h5>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Kendaraan Tab -->
                            <div class="tab-pane fade" id="vehicle" role="tabpanel">
                                <?php if($vehicleRequests->count() > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Nama</th>
                                                    <th>Jenis</th>
                                                    <th>Tujuan</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $vehicleRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e($request->request_date->format('d/m/Y')); ?></td>
                                                        <td><?php echo e($request->employee_name); ?></td>
                                                        <td><?php echo e($request->vehicle_type); ?></td>
                                                        <td><?php echo e(\Illuminate\Support\Str::limit($request->destination, 30)); ?>

                                                        </td>
                                                        <td>
                                                            <a href="<?php echo e(route('hr.vehicle-asset.index', ['type' => 'vehicle'])); ?>"
                                                                class="btn btn-sm btn-outline-success">
                                                                <i class="mdi mdi-eye"></i> Lihat
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="mdi mdi-check-circle text-success" style="font-size: 4rem;"></i>
                                        <h5 class="text-muted mt-3">Tidak ada permintaan kendaraan yang menunggu approval
                                        </h5>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Inventaris Tab -->
                            <div class="tab-pane fade" id="asset" role="tabpanel">
                                <?php if($assetRequests->count() > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Nama</th>
                                                    <th>Kategori</th>
                                                    <th>Tujuan</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $assetRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e($request->request_date->format('d/m/Y')); ?></td>
                                                        <td><?php echo e($request->employee_name); ?></td>
                                                        <td><?php echo e($request->asset_category); ?></td>
                                                        <td><?php echo e(\Illuminate\Support\Str::limit($request->destination, 30)); ?>

                                                        </td>
                                                        <td>
                                                            <a href="<?php echo e(route('hr.vehicle-asset.index', ['type' => 'asset'])); ?>"
                                                                class="btn btn-sm btn-outline-info">
                                                                <i class="mdi mdi-eye"></i> Lihat
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="mdi mdi-check-circle text-success" style="font-size: 4rem;"></i>
                                        <h5 class="text-muted mt-3">Tidak ada permintaan inventaris yang menunggu approval
                                        </h5>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('scripts'); ?>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js')); ?>"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/hr/approval/hr-pending.blade.php ENDPATH**/ ?>