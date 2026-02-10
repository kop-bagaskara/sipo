<?php $__env->startSection('title'); ?>
    Permohonan Data Karyawan
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
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
    Permohonan Data Karyawan
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>

    <body data-sidebar="colored">
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('content'); ?>
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Permohonan Data Karyawan</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">Permohonan Data Karyawan</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Data <?php echo e($type === 'vehicle' ? 'Kendaraan' : 'Inventaris'); ?></h4>
                        <p class="card-subtitle">Rekap data <?php echo e($type === 'vehicle' ? 'kendaraan' : 'inventaris'); ?> divisi
                            <?php echo e(Auth::user()->divisi); ?></p>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="btn-group" role="group">
                                    <a href="<?php echo e(route('hr.vehicle-asset.index', ['type' => 'vehicle'])); ?>"
                                        class="btn <?php echo e($type === 'vehicle' ? 'btn-info' : 'btn-outline-info'); ?>">
                                        Kendaraan
                                    </a>
                                    <a href="<?php echo e(route('hr.vehicle-asset.index', ['type' => 'asset'])); ?>"
                                        class="btn <?php echo e($type === 'asset' ? 'btn-info' : 'btn-outline-info'); ?>">
                                        Inventaris
                                    </a>
                                </div>
                            </div>
                            
                        </div>

                        <!-- Manager Pending Card -->
                        <?php if(Auth::user()->canApprove()): ?>
                            <?php
                                // PENTING: General Manager (divisi 13) melihat request yang dibuat oleh Manager (jabatan 3)
                                // Filter berdasarkan general_id, bukan divisi_id
                                if ((int) Auth::user()->divisi === 13) {
                                    // General Manager melihat request yang memiliki general_id = user.id dan belum di-approve/reject
                                    $pendingManager = \App\Models\VehicleAssetRequest::where(function ($q) {
                                        $q->where('general_id', Auth::user()->id)->orWhere(
                                            'manager_id',
                                            Auth::user()->id,
                                        ); // Backward compatibility
                                    })
                                        ->whereNull('general_approved_at')
                                        ->whereNull('general_rejected_at')
                                        ->where('status', \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER)
                                        ->where('request_type', $type)
                                        ->limit(5)
                                        ->get();
                                } else {
                                    // Manager/Head/SPV melihat request dari divisi mereka
                                    $pendingManager = \App\Models\VehicleAssetRequest::forDivisi(Auth::user()->divisi)
                                        ->where('request_type', $type)
                                        ->pendingManager()
                                        ->limit(5)
                                        ->get();
                                }
                            ?>
                            <?php if($pendingManager->count() > 0): ?>
                                <div class="card mb-4">
                                    <div class="card-header bg-info">
                                        <h5 class="card-title mb-0 text-white">
                                            <i class="mdi mdi-clock-outline"></i> Menunggu Persetujuan
                                            <?php echo e((int) Auth::user()->divisi === 13 ? 'General Manager' : 'Manager'); ?>

                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Tanggal</th>
                                                        <th>Nama</th>
                                                        <th><?php echo e($type === 'vehicle' ? 'Jenis Kendaraan' : 'Kategori'); ?></th>
                                                        <th>Tujuan</th>
                                                        <th>Periode</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $pendingManager; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td><?php echo e($request->request_date->format('d/m/Y')); ?></td>
                                                            <td><?php echo e($request->employee_name); ?></td>
                                                            <td><?php echo e($type === 'vehicle' ? $request->vehicle_type : $request->asset_category); ?>

                                                            </td>
                                                            <td><?php echo e(\Illuminate\Support\Str::limit($request->purpose, 30)); ?>

                                                            </td>
                                                            <td><?php echo e($request->start_date->format('d/m/Y')); ?> -
                                                                <?php echo e($request->end_date->format('d/m/Y')); ?></td>
                                                            <td>
                                                                <?php if(Auth::user()->jabatan == 4 && Auth::user()->divisi == 4): ?>
                                                                    <form method="POST"
                                                                        action="<?php echo e(route('hr.vehicle-asset.manager-approve', $request->id)); ?>"
                                                                        class="d-inline">
                                                                        <?php echo csrf_field(); ?>
                                                                        <button type="submit"
                                                                            class="btn btn-success btn-sm" disabled>
                                                                            <i class="mdi mdi-check"></i> Sedang Diajukan ke
                                                                            General Manager
                                                                        </button>
                                                                    </form>
                                                                <?php else: ?>
                                                                    

                                                                    <?php if(Auth::user()->jabatan == 3): ?>
                                                                        <?php if($request->employee_id == Auth::user()->id): ?>
                                                                            <button type="submit"
                                                                                class="btn btn-success btn-sm" disabled>
                                                                                <i class="mdi mdi-check"></i> Sedang
                                                                                Diajukan ke General
                                                                                Manager
                                                                            </button>
                                                                        <?php else: ?>
                                                                            <form method="POST"
                                                                                action="<?php echo e(route('hr.vehicle-asset.manager-approve', $request->id)); ?>"
                                                                                class="d-inline">
                                                                                <?php echo csrf_field(); ?>
                                                                                <button type="submit"
                                                                                    class="btn btn-success btn-sm">
                                                                                    <i class="mdi mdi-check"></i> Setujui
                                                                                </button>
                                                                            </form>
                                                                            <button type="button"
                                                                                class="btn btn-danger btn-sm"
                                                                                onclick="rejectRequest(<?php echo e($request->id); ?>)">
                                                                                <i class="mdi mdi-close"></i> Tolak
                                                                            </button>
                                                                        <?php endif; ?>
                                                                    <?php else: ?>
                                                                        <button type="submit"
                                                                            class="btn btn-success btn-sm" disabled>
                                                                            <i class="mdi mdi-check"></i> Sedang Diajukan ke
                                                                            Manager
                                                                        </button>
                                                                    <?php endif; ?>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-center mt-2">
                                            <a href="<?php echo e(route('hr.approval.manager-pending')); ?>"
                                                class="btn btn-info btn-sm">
                                                Lihat Semua (<?php echo e($pendingManager->count()); ?>)
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- HRGA Pending Card -->
                        <?php if(Auth::user()->isHR()): ?>
                            <?php
                                $pendingHrga = \App\Models\VehicleAssetRequest::where('request_type', $type)
                                    ->pendingHrga()
                                    ->limit(5)
                                    ->get();
                            ?>
                            <?php if($pendingHrga->count() > 0): ?>
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="card-title mb-0">
                                            <i class="mdi mdi-clock-outline"></i> Menunggu Persetujuan HRGA
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Tanggal</th>
                                                        <th>Nama</th>
                                                        <th><?php echo e($type === 'vehicle' ? 'Jenis Kendaraan' : 'Kategori'); ?></th>
                                                        <th>Tujuan</th>
                                                        <th>Periode</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $__currentLoopData = $pendingHrga; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td><?php echo e($request->request_date->format('d/m/Y')); ?></td>
                                                            <td><?php echo e($request->employee_name); ?></td>
                                                            <td><?php echo e($type === 'vehicle' ? $request->vehicle_type : $request->asset_category); ?>

                                                            </td>
                                                            <td><?php echo e(\Illuminate\Support\Str::limit($request->purpose, 30)); ?>

                                                            </td>
                                                            <td><?php echo e($request->start_date->format('d/m/Y')); ?> -
                                                                <?php echo e($request->end_date->format('d/m/Y')); ?></td>
                                                            <td>
                                                                <form method="POST"
                                                                    action="<?php echo e(route('hr.vehicle-asset.hrga-approve', $request->id)); ?>"
                                                                    class="d-inline">
                                                                    <?php echo csrf_field(); ?>
                                                                    <button type="submit" class="btn btn-success btn-sm">
                                                                        <i class="mdi mdi-check"></i> Setujui
                                                                    </button>
                                                                </form>
                                                                <button type="button" class="btn btn-danger btn-sm"
                                                                    onclick="rejectHrgaRequest(<?php echo e($request->id); ?>)">
                                                                    <i class="mdi mdi-close"></i> Tolak
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-center mt-2">
                                            <a href="<?php echo e(route('hr.approval.hrga-pending')); ?>"
                                                class="btn btn-primary btn-sm">
                                                Lihat Semua (<?php echo e($pendingHrga->count()); ?>)
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th><?php echo e($type === 'vehicle' ? 'Jenis Kendaraan' : 'Kategori'); ?></th>
                                        <th>Nama Karyawan</th>
                                        <th>Bagian</th>
                                        <th>Keperluan</th>
                                        <th>Tujuan</th>
                                        <th>Periode</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><?php echo e($loop->iteration); ?></td>
                                            <td><?php echo e($request->request_date->format('d/m/Y')); ?></td>
                                            <td><?php echo e($type === 'vehicle' ? $request->vehicle_type : $request->asset_category); ?>

                                            </td>
                                            <td><?php echo e($request->employee_name); ?></td>
                                            <td><?php echo e($request->department); ?></td>
                                            <td><?php echo e($request->purpose_type); ?></td>
                                            <td><?php echo e(\Illuminate\Support\Str::limit($request->destination, 30)); ?></td>
                                            <td><?php echo e($request->start_date->format('d/m/Y')); ?> -
                                                <?php echo e($request->end_date->format('d/m/Y')); ?></td>
                                            <td>
                                                <?php
                                                    $statusMap = [
                                                        'pending_manager' => [
                                                            'class' => 'warning',
                                                            'text' => 'Pending Manager',
                                                        ],
                                                        'manager_approved' => [
                                                            'class' => 'info',
                                                            'text' => 'Disetujui Manager',
                                                        ],
                                                        'manager_rejected' => [
                                                            'class' => 'danger',
                                                            'text' => 'Ditolak Manager',
                                                        ],
                                                        'hrga_approved' => [
                                                            'class' => 'success',
                                                            'text' => 'Disetujui HRGA',
                                                        ],
                                                        'hrga_rejected' => [
                                                            'class' => 'danger',
                                                            'text' => 'Ditolak HRGA',
                                                        ],
                                                    ];
                                                    $status = $statusMap[$request->status] ?? [
                                                        'class' => 'secondary',
                                                        'text' => $request->status,
                                                    ];
                                                ?>
                                                <span
                                                    class="badge badge-<?php echo e($status['class']); ?>"><?php echo e($status['text']); ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                    $currentUser = Auth::user();
                                                    $canEdit = false;
                                                    $canDelete = false;

                                                    // Cek apakah bisa edit/hapus (hanya oleh pembuat request dan belum ada approval sama sekali)
                                                    if ($request->employee_id == $currentUser->id) {
                                                        $canEdit =
                                                            is_null($request->manager_at) &&
                                                            is_null($request->general_approved_at) &&
                                                            is_null($request->general_rejected_at) &&
                                                            is_null($request->hrga_at) &&
                                                            $request->status ===
                                                                \App\Models\VehicleAssetRequest::STATUS_PENDING_MANAGER;
                                                        $canDelete = $canEdit; // Bisa hapus jika bisa edit
                                                    }
                                                ?>

                                                <div class="btn-group" role="group">
                                                    <a href="<?php echo e(route('hr.vehicle-asset.show', $request->id)); ?>"
                                                        class="btn btn-sm btn-outline-info" title="Detail">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>

                                                    <?php if($canEdit): ?>
                                                        <a href="<?php echo e(route('hr.vehicle-asset.edit', $request->id)); ?>"
                                                            class="btn btn-sm btn-outline-warning" title="Edit">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if($canDelete): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="confirmDelete(<?php echo e($request->id); ?>)" title="Hapus">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                    <?php if($request->status === 'manager_approved' && $currentUser->canApprove()): ?>
                                                        <form method="POST"
                                                            action="<?php echo e(route('hr.vehicle-asset.hrga-approve', $request->id)); ?>"
                                                            class="d-inline">
                                                            <?php echo csrf_field(); ?>
                                                            <button type="submit" class="btn btn-sm btn-primary">
                                                                <i class="mdi mdi-check"></i> Approve HRGA
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="10" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="mdi mdi-information-outline fs-1"></i>
                                                    <p class="mt-2">Belum ada data
                                                        <?php echo e($type === 'vehicle' ? 'kendaraan' : 'inventaris'); ?></p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if($requests->hasPages()): ?>
                            <div class="d-flex justify-content-center mt-3">
                                <?php echo e($requests->links()); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('scripts'); ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
        <script>
            function rejectRequest(id) {
                Swal.fire({
                    title: 'Tolak Request',
                    input: 'textarea',
                    inputLabel: 'Alasan Penolakan',
                    inputPlaceholder: 'Masukkan alasan penolakan...',
                    inputAttributes: {
                        'aria-label': 'Masukkan alasan penolakan'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Tolak',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/sipo/hr/vehicle-asset/${id}/manager-reject`;

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '<?php echo e(csrf_token()); ?>';
                        form.appendChild(csrfToken);

                        const notesInput = document.createElement('input');
                        notesInput.type = 'hidden';
                        notesInput.name = 'manager_notes';
                        notesInput.value = result.value;
                        form.appendChild(notesInput);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }

            function rejectHrgaRequest(id) {
                Swal.fire({
                    title: 'Tolak Request',
                    input: 'textarea',
                    inputLabel: 'Alasan Penolakan',
                    inputPlaceholder: 'Masukkan alasan penolakan...',
                    inputAttributes: {
                        'aria-label': 'Masukkan alasan penolakan'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Tolak',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/sipo/hr/vehicle-asset/${id}/hrga-reject`;

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '<?php echo e(csrf_token()); ?>';
                        form.appendChild(csrfToken);

                        const notesInput = document.createElement('input');
                        notesInput.type = 'hidden';
                        notesInput.name = 'hrga_notes';
                        notesInput.value = result.value;
                        form.appendChild(notesInput);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }

            function confirmDelete(id) {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Apakah Anda yakin ingin menghapus pengajuan ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `<?php echo e(url('hr/vehicle-asset')); ?>/${id}`;

                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '<?php echo e(csrf_token()); ?>';
                        form.appendChild(csrfToken);

                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        form.appendChild(methodInput);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        </script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/hr/vehicle-asset/index.blade.php ENDPATH**/ ?>