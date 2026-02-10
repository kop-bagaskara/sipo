<?php
    // Determine routes and URLs based on level
    if ($level === 'staff') {
        $indexUrl = url('sipo/hr/applicants');
        $createUrl = url('sipo/hr/applicants/create');
    } else {
        $indexUrl = url('sipo/hr/staff-applicants');
        $createUrl = url('sipo/hr/staff-applicants/create');
    }

    // Get statuses and positions from parent scope if not passed
    $statuses = $statuses ?? ['pending', 'test', 'interview', 'accepted', 'rejected'];
    $positions = $positions ?? \App\Models\Applicant::distinct()->pluck('posisi_dilamar')->filter();
?>

<!-- Filter Section -->
<div class="row mb-3">
    <div class="col-md-12">
        <form method="GET" action="<?php echo e($indexUrl); ?>" class="form-inline">
            <div class="form-group mr-3">
                <label class="mr-2">Cari:</label>
                <input type="text" name="search" class="form-control" value="<?php echo e(request('search')); ?>" placeholder="Nama, Email, atau Posisi">
            </div>
            <div class="form-group mr-3">
                <label class="mr-2">Status:</label>
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($status); ?>" <?php echo e(request('status') == $status ? 'selected' : ''); ?>>
                            <?php echo e(ucfirst($status)); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="form-group mr-3">
                <label class="mr-2">Posisi:</label>
                <select name="posisi" class="form-control">
                    <option value="">Semua Posisi</option>
                    <?php $__currentLoopData = $positions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $position): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($position); ?>" <?php echo e(request('posisi') == $position ? 'selected' : ''); ?>>
                            <?php echo e($position); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <button type="submit" class="btn btn-info">
                <i class="mdi mdi-magnify"></i> Filter
            </button>
            <a href="<?php echo e($indexUrl); ?>" class="btn btn-secondary ml-2">
                <i class="mdi mdi-refresh"></i> Reset
            </a>
            
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>Posisi Dilamar</th>
                <th>Status</th>
                <th>Progress Test</th>
                <th>Tanggal Melamar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $applicants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $applicant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($applicants->firstItem() + $index); ?></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div>
                                <strong><?php echo e($applicant->nama_lengkap); ?></strong>
                                <br>
                                <small class="text-muted"><?php echo e($applicant->no_telepon); ?></small>
                            </div>
                        </div>
                    </td>
                    <td><?php echo e($applicant->email); ?></td>
                    <td><?php echo e($applicant->posisi_dilamar); ?></td>
                    <td>
                        <?php
                            $statusColors = [
                                'pending' => 'secondary',
                                'test' => 'warning',
                                'interview' => 'info',
                                'accepted' => 'success',
                                'rejected' => 'danger'
                            ];
                        ?>
                        <span class="badge badge-<?php echo e($statusColors[$applicant->status] ?? 'secondary'); ?>">
                            <?php echo e($applicant->status_formatted); ?>

                        </span>
                    </td>
                    <td>
                        <?php
                            $completedTests = $applicant->testResults->count();
                            $percentage = ($completedTests / $totalTests) * 100;
                        ?>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo e($percentage); ?>%">
                                <?php echo e($completedTests); ?>/<?php echo e($totalTests); ?>

                            </div>
                        </div>
                    </td>
                    <td><?php echo e($applicant->tanggal_melamar->format('d/m/Y')); ?></td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="<?php echo e($level === 'staff' ? url('sipo/hr/applicants/' . $applicant->id) : url('sipo/hr/staff-applicants/' . $applicant->id)); ?>"
                               class="btn btn-info btn-sm" title="Lihat Detail">
                                <i class="mdi mdi-eye"></i>
                            </a>
                            <a href="<?php echo e($level === 'staff' ? url('sipo/hr/applicants/' . $applicant->id . '/edit') : url('sipo/hr/staff-applicants/' . $applicant->id . '/edit')); ?>"
                               class="btn btn-warning btn-sm" title="Edit">
                                <i class="mdi mdi-pencil"></i>
                            </a>
                            <?php if($completedTests < $totalTests): ?>
                                <a href="<?php echo e($level === 'staff' ? url('sipo/hr/' . $applicant->id . '/tests') : url('sipo/hr/staff-applicants/' . $applicant->id . '/tests')); ?>"
                                   class="btn btn-success btn-sm" title="Mulai Test">
                                    <i class="mdi mdi-play"></i> Test
                                </a>
                            <?php endif; ?>
                            <button type="button" class="btn btn-danger btn-sm"
                                    onclick="confirmDelete(<?php echo e($applicant->id); ?>, '<?php echo e($level); ?>')" title="Hapus">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="text-center">
                        <div class="alert alert-info">
                            <i class="mdi mdi-information"></i>
                            Tidak ada data pelamar
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        <p class="text-muted mb-0">
            Menampilkan <?php echo e($applicants->firstItem() ?? 0); ?> sampai <?php echo e($applicants->lastItem() ?? 0); ?>

            dari <?php echo e($applicants->total()); ?> data
        </p>
    </div>
    <div>
        <?php if($applicants->hasPages()): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0">
                    
                    <?php if($applicants->onFirstPage()): ?>
                        <li class="page-item disabled">
                            <span class="page-link" aria-hidden="true">&laquo;</span>
                        </li>
                    <?php else: ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo e($applicants->appends(request()->query())->previousPageUrl()); ?>" rel="prev">&laquo;</a>
                        </li>
                    <?php endif; ?>

                    
                    <?php
                        $currentPage = $applicants->currentPage();
                        $lastPage = $applicants->lastPage();
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($lastPage, $currentPage + 2);
                    ?>

                    <?php if($startPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo e($applicants->appends(request()->query())->url(1)); ?>">1</a>
                        </li>
                        <?php if($startPage > 2): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for($page = $startPage; $page <= $endPage; $page++): ?>
                        <?php if($page == $currentPage): ?>
                            <li class="page-item active">
                                <span class="page-link"><?php echo e($page); ?></span>
                            </li>
                        <?php else: ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo e($applicants->appends(request()->query())->url($page)); ?>"><?php echo e($page); ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if($endPage < $lastPage): ?>
                        <?php if($endPage < $lastPage - 1): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo e($applicants->appends(request()->query())->url($lastPage)); ?>"><?php echo e($lastPage); ?></a>
                        </li>
                    <?php endif; ?>

                    
                    <?php if($applicants->hasMorePages()): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo e($applicants->appends(request()->query())->nextPageUrl()); ?>" rel="next">&raquo;</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link" aria-hidden="true">&raquo;</span>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/hr/applicants/partials/applicant-table.blade.php ENDPATH**/ ?>