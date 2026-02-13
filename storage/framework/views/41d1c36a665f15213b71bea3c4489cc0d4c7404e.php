<?php $__env->startSection('title'); ?>
    Label Management
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .customer-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .customer-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .customer-header {
            border-bottom: 2px solid #4472C4;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .customer-name {
            font-size: 20px;
            font-weight: bold;
            color: #4472C4;
            margin-bottom: 5px;
        }
        .customer-code {
            font-size: 12px;
            color: #999;
            font-family: monospace;
        }
        .customer-info {
            margin-bottom: 15px;
        }
        .customer-info-item {
            font-size: 13px;
            color: #666;
            margin-bottom: 5px;
        }
        .customer-info-item i {
            width: 20px;
            color: #4472C4;
        }
        .template-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 11px;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .template-badge-besar {
            background-color: #4472C4;
            color: white;
        }
        .template-badge-kecil {
            background-color: #70AD47;
            color: white;
        }
        .template-list {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .template-item {
            padding: 8px 12px;
            margin-bottom: 8px;
            background-color: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #4472C4;
        }
        .template-item-name {
            font-weight: 500;
            color: #333;
            margin-bottom: 3px;
        }
        .template-item-meta {
            font-size: 11px;
            color: #999;
        }
        .stat-card {
            border-left: 4px solid #4472C4;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #4472C4;
        }
        .stat-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
    Label Management
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>
    <body data-sidebar="colored">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Label Management - Dashboard</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Operasional</a></li>
                <li class="breadcrumb-item active">Label Management</li>
            </ol>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if(session('success')): ?>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-check-circle"></i> <?php echo e(session('success')); ?>

                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="mdi mdi-alert-circle"></i> <?php echo e(session('error')); ?>

                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number"><?php echo e($totalCustomers); ?></div>
                <div class="stat-label">Total Customer</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: #70AD47;">
                <div class="stat-number" style="color: #70AD47;"><?php echo e($totalTemplates); ?></div>
                <div class="stat-label">Total Template</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: #4472C4;">
                <div class="stat-number" style="color: #4472C4;"><?php echo e($countBesar); ?></div>
                <div class="stat-label">Template Label Besar</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: #FF6B6B;">
                <div class="stat-number" style="color: #FF6B6B;"><?php echo e($countKecil); ?></div>
                <div class="stat-label">Template Label Kecil</div>
            </div>
        </div>
    </div>

    <!-- Master Cards Section -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="card" style="border-left: 4px solid #FF9800;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">
                                <i class="mdi mdi-package-variant" style="color: #FF9800;"></i> Master Item Unilever
                            </h5>
                            <p class="text-muted mb-0">Kelola master data item untuk Unilever</p>
                        </div>
                        <div>
                            <a href="<?php echo e(route('label-management.master-item-unilever.index')); ?>" class="btn btn-warning btn-sm">
                                <i class="mdi mdi-package-variant"></i> Kelola
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card" style="border-left: 4px solid #2196F3;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">
                                <i class="mdi mdi-account-key" style="color: #2196F3;"></i> Master Code Operator
                            </h5>
                            <p class="text-muted mb-0">Kelola master data code operator</p>
                        </div>
                        <div>
                            <a href="<?php echo e(route('label-management.master-code-operator.index')); ?>" class="btn btn-info btn-sm">
                                <i class="mdi mdi-account-key"></i> Kelola
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="card-title">Master Label Customer</h4>
                            <p class="text-muted">Daftar customer dan template label yang tersedia</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="<?php echo e(route('label-management.customer.create')); ?>" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Tambah Customer
                            </a>
                        </div>
                    </div>

                    <form method="GET" action="<?php echo e(route('label-management.index')); ?>" id="searchForm">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="search">Cari Customer</label>
                                    <input type="text" class="form-control" id="search" name="search"
                                           value="<?php echo e($search); ?>" placeholder="Cari berdasarkan nama customer, brand, atau kode...">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="mdi mdi-magnify"></i> Cari
                                        </button>
                                        <a href="<?php echo e(route('label-management.index')); ?>" class="btn btn-secondary">
                                            <i class="mdi mdi-refresh"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Cards -->
    <div class="row">
        <div class="col-12">
            <?php if($customers->count() > 0): ?>
                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="customer-card">
                        <div class="customer-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="customer-name"><?php echo e($customer->customer_name); ?></div>
                                    <?php if($customer->customer_code): ?>
                                        <div class="customer-code">Code: <?php echo e($customer->customer_code); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <a href="<?php echo e(route('label-management.customer.show', $customer->id)); ?>" class="btn btn-sm btn-primary" title="View">
                                        <i class="mdi mdi-eye"></i> View
                                    </a>
                                    <a href="<?php echo e(route('label-management.customer.edit', $customer->id)); ?>" class="btn btn-sm btn-info" title="Edit">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteCustomer(<?php echo e($customer->id); ?>, '<?php echo e(addslashes($customer->customer_name)); ?>')" title="Hapus">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="customer-info">
                            <?php if($customer->brand_name): ?>
                                <div class="customer-info-item">
                                    <i class="mdi mdi-tag"></i> Brand: <strong><?php echo e($customer->brand_name); ?></strong>
                                </div>
                            <?php endif; ?>
                            <?php if($customer->contact_person): ?>
                                <div class="customer-info-item">
                                    <i class="mdi mdi-account"></i> Contact: <?php echo e($customer->contact_person); ?>

                                </div>
                            <?php endif; ?>
                            <?php if($customer->email): ?>
                                <div class="customer-info-item">
                                    <i class="mdi mdi-email"></i> <?php echo e($customer->email); ?>

                                </div>
                            <?php endif; ?>
                            <?php if($customer->phone): ?>
                                <div class="customer-info-item">
                                    <i class="mdi mdi-phone"></i> <?php echo e($customer->phone); ?>

                                </div>
                            <?php endif; ?>
                            <?php if($customer->description): ?>
                                <div class="customer-info-item">
                                    <i class="mdi mdi-information"></i> <?php echo e(mb_strlen($customer->description) > 150 ? mb_substr($customer->description, 0, 150) . '...' : $customer->description); ?>

                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="template-list">
                            <h6 style="margin-bottom: 10px;">
                                Template Label (<?php echo e($customer->templates->count()); ?>)
                            </h6>

                            <?php if($customer->templates->count() > 0): ?>
                                <?php $__currentLoopData = $customer->templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="template-item">
                                        <div class="template-item-name">
                                            <span class="template-badge template-badge-<?php echo e($template->template_type); ?>">
                                                <?php echo e(strtoupper($template->template_type)); ?>

                                            </span>
                                            <?php echo e($template->template_name); ?>

                                        </div>
                                        <div class="template-item-meta">
                                            <i class="mdi mdi-file-excel"></i> <?php echo e($template->file_name); ?>

                                            <?php if($template->product_name): ?>
                                                | <i class="mdi mdi-package-variant"></i> <?php echo e($template->product_name); ?>

                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <p class="text-muted" style="font-size: 13px; margin: 0;">
                                    <i class="mdi mdi-information-outline"></i> Belum ada template untuk customer ini
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <div class="empty-state">
                            <i class="mdi mdi-account-off-outline"></i>
                            <h5>Tidak ada customer ditemukan</h5>
                            <p>Silakan tambahkan customer baru atau ubah kata kunci pencarian.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script>
        function deleteCustomer(id, name) {
            Swal.fire({
                title: 'Yakin?',
                text: 'Apakah Anda yakin ingin menghapus customer "' + name + '"?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create form for DELETE request
                    const form = $('<form>', {
                        'method': 'POST',
                        'action': '<?php echo e(url("sipo/label-management/customer")); ?>/' + id
                    });

                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '_token',
                        'value': '<?php echo e(csrf_token()); ?>'
                    }));

                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '_method',
                        'value': 'DELETE'
                    }));

                    $('body').append(form);
                    form.submit();
                }
            });
        }

        // Show success message if exists
        <?php if(session('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?php echo e(session('success')); ?>',
                timer: 2000,
                showConfirmButton: false
            });
        <?php endif; ?>
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/main/label-management/index.blade.php ENDPATH**/ ?>