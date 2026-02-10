<?php $__env->startSection('title'); ?>
    General Manager Pending Approval
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .cust-col { white-space: nowrap; }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
    General Manager Pending Approval
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>
    <body data-sidebar="colored">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">General Manager Pending Approval</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item active">General Manager Pending Approval</li>
            </ol>
        </div>
    </div>

    <?php
        $totalPending = $formRequests->count() + $vehicleRequests->count() + $assetRequests->count();
    ?>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card"><div class="card-body text-center"><h3 class="text-primary"><?php echo e($totalPending); ?></h3><p class="text-muted mb-0">Total Menunggu</p></div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body text-center"><h3 class="text-info"><?php echo e($formRequests->count()); ?></h3><p class="text-muted mb-0">Form Karyawan</p></div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body text-center"><h3 class="text-success"><?php echo e($vehicleRequests->count()); ?></h3><p class="text-muted mb-0">Kendaraan</p></div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body text-center"><h3 class="text-warning"><?php echo e($assetRequests->count()); ?></h3><p class="text-muted mb-0">Inventaris</p></div></div>
        </div>
    </div>

    <div class="row"><div class="col-12"><div class="card"><div class="card-header"><h4 class="card-title">Daftar Pengajuan Menunggu Approval General Manager</h4></div><div class="card-body">
        <ul class="nav nav-tabs mb-4" id="requestTabs" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" id="forms-tab" data-toggle="tab" data-target="#forms" type="button" role="tab"><i class="mdi mdi-file-document me-1"></i> Form Karyawan <span class="badge badge-info"><?php echo e($formRequests->count()); ?></span></button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="vehicle-tab" data-toggle="tab" data-target="#vehicle" type="button" role="tab"><i class="mdi mdi-car me-1"></i> Kendaraan <span class="badge badge-success"><?php echo e($vehicleRequests->count()); ?></span></button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="asset-tab" data-toggle="tab" data-target="#asset" type="button" role="tab"><i class="mdi mdi-package-variant me-1"></i> Inventaris <span class="badge badge-warning"><?php echo e($assetRequests->count()); ?></span></button></li>
        </ul>

        <div class="tab-content" id="requestTabsContent">
            <div class="tab-pane fade show active" id="forms" role="tabpanel">
                <?php if($formRequests->count() > 0): ?>
                    <div class="table-responsive"><table class="table table-hover"><thead><tr><th>No. Pengajuan</th><th>Jenis</th><th>Pemohon</th><th>Divisi</th><th>Tanggal Dibuat</th><th>Aksi</th></tr></thead><tbody>
                    <?php $__currentLoopData = $formRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><span class="fw-bold"><?php echo e($request->request_number); ?></span></td>
                            <td><span class="badge bg-info text-white"><?php echo e($request->request_type_label); ?></span></td>
                            <td><?php echo e($request->employee->name ?? 'N/A'); ?></td>
                            <td><?php echo e($request->employee->divisiUser->divisi ?? $request->employee->divisiUser->nama_divisi ?? 'Divisi ' . $request->employee->divisi); ?></td>
                            <td><?php echo e($request->created_at->format('d/m/Y H:i')); ?></td>
                            <td><a href="<?php echo e(route('hr.approval.show', $request->id)); ?>" class="btn btn-sm btn-outline-primary"><i class="mdi mdi-eye"></i> Lihat</a></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody></table></div>
                <?php else: ?>
                    <div class="text-center py-5"><i class="mdi mdi-check-circle text-success" style="font-size: 4rem;"></i><h5 class="text-muted mt-3">Tidak ada form pengajuan yang menunggu approval</h5></div>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="vehicle" role="tabpanel">
                <?php if($vehicleRequests->count() > 0): ?>
                    <div class="table-responsive"><table class="table table-hover"><thead><tr><th>Tanggal</th><th>Nama</th><th>Jenis</th><th>Tujuan</th><th>Aksi</th></tr></thead><tbody>
                    <?php $__currentLoopData = $vehicleRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($request->request_date->format('d/m/Y')); ?></td>
                            <td><?php echo e($request->employee_name); ?></td>
                            <td><?php echo e($request->vehicle_type); ?></td>
                            <td><?php echo e(\Illuminate\Support\Str::limit($request->destination, 30)); ?></td>
                            <td><a href="<?php echo e(route('hr.vehicle-asset.index', ['type' => 'vehicle'])); ?>" class="btn btn-sm btn-outline-success"><i class="mdi mdi-eye"></i> Lihat</a></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody></table></div>
                <?php else: ?>
                    <div class="text-center py-5"><i class="mdi mdi-check-circle text-success" style="font-size: 4rem;"></i><h5 class="text-muted mt-3">Tidak ada permintaan kendaraan yang menunggu approval</h5></div>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="asset" role="tabpanel">
                <?php if($assetRequests->count() > 0): ?>
                    <div class="table-responsive"><table class="table table-hover"><thead><tr><th>Tanggal</th><th>Nama</th><th>Kategori</th><th>Tujuan</th><th>Aksi</th></tr></thead><tbody>
                    <?php $__currentLoopData = $assetRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($request->request_date->format('d/m/Y')); ?></td>
                            <td><?php echo e($request->employee_name); ?></td>
                            <td><?php echo e($request->asset_category); ?></td>
                            <td><?php echo e(\Illuminate\Support\Str::limit($request->destination, 30)); ?></td>
                            <td><a href="<?php echo e(route('hr.vehicle-asset.index', ['type' => 'asset'])); ?>" class="btn btn-sm btn-outline-info"><i class="mdi mdi-eye"></i> Lihat</a></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody></table></div>
                <?php else: ?>
                    <div class="text-center py-5"><i class="mdi mdi-check-circle text-success" style="font-size: 4rem;"></i><h5 class="text-muted mt-3">Tidak ada permintaan inventaris yang menunggu approval</h5></div>
                <?php endif; ?>
            </div>
        </div>
    </div></div></div></div></div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js')); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/hr/approval/general-manager-pending.blade.php ENDPATH**/ ?>