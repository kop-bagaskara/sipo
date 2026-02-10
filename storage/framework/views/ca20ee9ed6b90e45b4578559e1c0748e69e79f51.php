<?php $__env->startSection('content'); ?>
<div class="row page-titles">
    <div class="col-md-6 col-12">
        <h3 class="text-themecolor">Log Viewer PKB</h3>
        <p class="text-muted m-b-0">Pantau siapa yang membaca PKB dan durasinya</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">Aktivitas Terbaru</h4>
        <div class="table-responsive m-t-20">
            <table class="table table-bordered table-hover">
                <thead class="bg-light" >
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Divisi</th>
                        <th>Jabatan</th>
                        <th style="text-wrap: nowrap;">Hal. Awal</th>
                        <th style="text-wrap: nowrap;">Hal. Terakhir</th>
                        <th style="text-wrap: nowrap;">Total Hal. Dibaca</th>
                        <th style="text-wrap: nowrap;">Durasi (detik)</th>
                        <th style="text-wrap: nowrap;">Status</th>
                        <th style="text-wrap: nowrap;">Mulai</th>
                        <th style="text-wrap: nowrap;">Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($idx + 1); ?></td>
                            <td>
                                <div class="font-medium"><?php echo e($log->user->name ?? 'Unknown'); ?></div>
                                <?php
                                    $divisiName = $log->user->divisiUser->divisi ?? null;
                                    $jabatanName = $log->user->jabatanUser->jabatan ?? null;
                                ?>
                                
                                
                                <div class="text-muted small"><?php echo e($log->user->email ?? ''); ?></div>
                            </td>
                            <td><?php echo e($divisiName ?? '-'); ?></td>
                            <td><?php echo e($jabatanName ?? '-'); ?></td>
                            <td><?php echo e($log->start_page); ?></td>
                            <td><?php echo e($log->last_page_viewed); ?></td>
                            <td><?php echo e($log->total_pages_viewed); ?></td>
                            <td><?php echo e($log->time_spent_seconds ?? 0); ?></td>
                            <?php
                                $isComplete = $log->marked_as_complete || ($log->last_page_viewed >= 46 && $log->session_end_at);
                                $isActiveWindow = $log->session_end_at && $log->session_end_at >= now()->subHours(6);
                            ?>
                            <td>
                                <?php if($isComplete): ?>
                                    <span class="badge badge-success">Selesai</span>
                                <?php elseif($isActiveWindow): ?>
                                    <span class="badge badge-warning">Aktif / Belum selesai</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Idle >6 jam</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-wrap: nowrap;"><?php echo e(optional($log->session_start_at)->format('d M Y H:i:s')); ?></td>
                            <td style="text-wrap: nowrap;"><?php echo e(optional($log->session_end_at)->format('d M Y H:i:s')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">Belum ada data</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p class="text-muted small m-t-10">Menampilkan <?php echo e($logs->count()); ?> sesi terbaru (dibatasi 100).</p>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/main/ebook-pkb/logs.blade.php ENDPATH**/ ?>