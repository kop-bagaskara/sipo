<?php $__env->startSection('title'); ?>
    Test Google Drive API
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-title'); ?>
    Test Google Drive API
<?php $__env->stopSection(); ?>

<?php $__env->startSection('body'); ?>
    <body data-sidebar="colored">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Test Google Drive API</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo e(route('hr.portal-training.master.dashboard.index')); ?>">Portal Training Master</a></li>
                <li class="breadcrumb-item active">Test Google Drive API</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="mdi mdi-google-drive"></i> Test Koneksi Google Drive API
                    </h4>

                    <!-- Test Form -->
                    <form id="testForm" method="GET" action="<?php echo e(route('hr.portal-training.master.test-google-drive')); ?>">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Test dengan File ID (Opsional)</label>
                                    <input type="text"
                                        name="file_id"
                                        class="form-control"
                                        id="testFileId"
                                        value="<?php echo e($testFileId ?? ''); ?>"
                                        placeholder="Masukkan Google Drive File ID untuk test akses file spesifik">
                                    <small class="text-muted">
                                        Kosongkan jika hanya ingin test koneksi API.
                                        Masukkan File ID jika ingin test akses ke file tertentu.
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="mdi mdi-test-tube"></i> Test Koneksi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <!-- Test Results -->
                    <?php if(isset($result)): ?>
                        <div class="test-results">
                            <h5 class="mb-3">Hasil Test:</h5>

                    <!-- Overall Status -->
                    <?php if(isset($result['file_permission_error']) && $result['file_permission_error']): ?>
                        <div class="alert alert-warning mb-4">
                            <h5 class="mb-2">
                                <i class="mdi mdi-alert"></i>
                                API Berfungsi, Tapi File Belum Di-Share dengan Benar
                            </h5>
                            <p class="mb-2"><strong><?php echo e($result['message'] ?? 'Tidak ada pesan'); ?></strong></p>
                            <div class="mt-3">
                                <h6><strong>Cara Memperbaiki:</strong></h6>
                                <ol class="mb-0">
                                    <li>Buka file di Google Drive</li>
                                    <li>Klik kanan pada file > Pilih "Share" atau "Bagikan"</li>
                                    <li>Ubah permission dari "Restricted" menjadi <strong>"Anyone with the link"</strong></li>
                                    <li>Pastikan role adalah <strong>"Viewer"</strong> (bukan "Editor" atau "Commenter")</li>
                                    <li>Klik "Done" atau "Selesai"</li>
                                    <li>Test lagi dengan File ID yang sama</li>
                                </ol>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-<?php echo e($result['success'] ? 'success' : 'danger'); ?> mb-4">
                            <h5 class="mb-2">
                                <i class="mdi mdi-<?php echo e($result['success'] ? 'check-circle' : 'close-circle'); ?>"></i>
                                <?php echo e($result['success'] ? 'API Berfungsi dengan Baik!' : 'API Tidak Berfungsi'); ?>

                            </h5>
                            <p class="mb-0"><?php echo e($result['message'] ?? 'Tidak ada pesan'); ?></p>

                            <?php if(isset($result['diagnosis']) && !$result['success']): ?>
                                <div class="mt-3 pt-3 border-top">
                                    <h6><strong><i class="mdi mdi-information"></i> Diagnosa Masalah:</strong></h6>
                                    <p class="mb-2"><strong><?php echo e($result['diagnosis']['title']); ?></strong></p>
                                    <?php if(!empty($result['diagnosis']['solutions'])): ?>
                                        <h6 class="mt-3"><strong>Cara Memperbaiki:</strong></h6>
                                        <ol class="mb-0">
                                            <?php $__currentLoopData = $result['diagnosis']['solutions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $solution): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li><?php echo e($solution); ?></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ol>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                            <!-- Detailed Status -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="mdi mdi-<?php echo e($result['api_key_configured'] ? 'check' : 'close'); ?> text-<?php echo e($result['api_key_configured'] ? 'success' : 'danger'); ?>"></i>
                                                API Key Terkonfigurasi
                                            </h6>
                                            <p class="mb-0">
                                                <?php if($result['api_key_configured']): ?>
                                                    <span class="badge badge-success">Ya</span>
                                                    <small class="text-muted d-block mt-1">API Key ditemukan di .env</small>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Tidak</span>
                                                    <small class="text-muted d-block mt-1">API Key tidak ditemukan di .env</small>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="mdi mdi-<?php echo e($result['api_key_valid'] ? 'check' : 'close'); ?> text-<?php echo e($result['api_key_valid'] ? 'success' : 'danger'); ?>"></i>
                                                API Key Valid
                                            </h6>
                                            <p class="mb-0">
                                                <?php if($result['api_key_valid']): ?>
                                                    <span class="badge badge-success">Valid</span>
                                                    <small class="text-muted d-block mt-1">API Key dapat digunakan</small>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Tidak Valid</span>
                                                    <small class="text-muted d-block mt-1">API Key tidak valid atau tidak memiliki akses</small>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="mdi mdi-<?php echo e($result['drive_api_accessible'] ? 'check' : 'close'); ?> text-<?php echo e($result['drive_api_accessible'] ? 'success' : 'danger'); ?>"></i>
                                                Google Drive API Dapat Diakses
                                            </h6>
                                            <p class="mb-0">
                                                <?php if($result['drive_api_accessible']): ?>
                                                    <span class="badge badge-success">Dapat Diakses</span>
                                                    <small class="text-muted d-block mt-1">Koneksi ke Google Drive API berhasil</small>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Tidak Dapat Diakses</span>
                                                    <small class="text-muted d-block mt-1">Gagal mengakses Google Drive API</small>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <?php if(isset($testFileId) && $testFileId): ?>
                                    <div class="col-md-6">
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <h6 class="card-title">
                                                    <i class="mdi mdi-<?php echo e($result['test_file_accessible'] ? 'check' : 'close'); ?> text-<?php echo e($result['test_file_accessible'] ? 'success' : (isset($result['file_permission_error']) && $result['file_permission_error'] ? 'warning' : 'danger')); ?>"></i>
                                                    File Test Dapat Diakses
                                                </h6>
                                                <p class="mb-0">
                                                    <?php if($result['test_file_accessible']): ?>
                                                        <span class="badge badge-success">Dapat Diakses</span>
                                                        <?php if(isset($result['test_file_info'])): ?>
                                                            <small class="text-muted d-block mt-1">
                                                                <strong>File:</strong> <?php echo e($result['test_file_info']['name']); ?><br>
                                                                <strong>Type:</strong> <?php echo e($result['test_file_info']['mimeType']); ?>

                                                            </small>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="badge badge-<?php echo e(isset($result['file_permission_error']) && $result['file_permission_error'] ? 'warning' : 'danger'); ?>">Tidak Dapat Diakses</span>
                                                        <small class="text-muted d-block mt-1">
                                                            <?php if(isset($result['file_permission_error']) && $result['file_permission_error']): ?>
                                                                <strong>Masalah Permission:</strong> File belum di-share dengan benar.
                                                                Ubah permission ke "Anyone with the link" di Google Drive.
                                                            <?php else: ?>
                                                                Pastikan file sudah di-share dengan permission "Anyone with the link"
                                                            <?php endif; ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Details -->
                            <?php if(!empty($result['details'])): ?>
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Detail:</h6>
                                        <ul class="mb-0">
                                            <?php $__currentLoopData = $result['details']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li><?php echo e($detail); ?></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Test File Info -->
                            <?php if(isset($result['test_file_info']) && $result['test_file_accessible']): ?>
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Informasi File Test:</h6>
                                        <table class="table table-sm">
                                            <tr>
                                                <th width="150">File ID:</th>
                                                <td><code><?php echo e($result['test_file_info']['id']); ?></code></td>
                                            </tr>
                                            <tr>
                                                <th>Nama File:</th>
                                                <td><?php echo e($result['test_file_info']['name']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>MIME Type:</th>
                                                <td><?php echo e($result['test_file_info']['mimeType']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Ukuran:</th>
                                                <td><?php echo e($result['test_file_info']['size'] !== 'unknown' ? number_format($result['test_file_info']['size'] / 1024 / 1024, 2) . ' MB' : 'Unknown'); ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="mdi mdi-information"></i>
                            Klik tombol "Test Koneksi" untuk memulai test.
                        </div>
                    <?php endif; ?>

                    <!-- Instructions -->
                    <div class="card mt-4">
                        <div class="card-body">
                            <h6 class="card-title">Cara Menggunakan:</h6>
                            <ol>
                                <li>Pastikan <code>GOOGLE_DRIVE_API_KEY</code> sudah dikonfigurasi di file <code>.env</code></li>
                                <li><strong>PENTING:</strong> Pastikan Anda menggunakan <strong>API Key</strong> (bukan OAuth Client ID)</li>
                                <li>Klik tombol "Test Koneksi" untuk test koneksi dasar</li>
                                <li>Untuk test akses file spesifik, masukkan File ID dari Google Drive dan klik "Test Koneksi"</li>
                                <li>Pastikan file di Google Drive sudah di-share dengan permission "Anyone with the link"</li>
                            </ol>

                            <hr>

                            <h6 class="card-title mt-3">Cara Membuat API Key yang Benar:</h6>
                            <ol>
                                <li>Buka <a href="https://console.cloud.google.com/apis/credentials" target="_blank">Google Cloud Console > APIs & Services > Credentials</a></li>
                                <li>Klik <strong>"+ CREATE CREDENTIALS"</strong> > Pilih <strong>"API key"</strong></li>
                                <li>Copy API Key yang dibuat</li>
                                <li>Klik <strong>"Restrict key"</strong> untuk keamanan (opsional tapi disarankan)</li>
                                <li>Di bagian <strong>"API restrictions"</strong>:
                                    <ul>
                                        <li>Pilih <strong>"Restrict key"</strong></li>
                                        <li>Pilih <strong>"Google Drive API"</strong> dari daftar</li>
                                        <li>Klik <strong>"Save"</strong></li>
                                    </ul>
                                </li>
                                <li>Paste API Key ke file <code>.env</code> sebagai <code>GOOGLE_DRIVE_API_KEY</code></li>
                                <li>Test lagi di halaman ini</li>
                            </ol>

                            <div class="alert alert-info mt-3">
                                <strong><i class="mdi mdi-information"></i> Catatan:</strong>
                                <ul class="mb-0 mt-2">
                                    <li><strong>API Key</strong> berbeda dengan <strong>OAuth 2.0 Client ID</strong></li>
                                    <li>Untuk streaming video dari Google Drive, kita perlu <strong>API Key</strong>, bukan OAuth Client ID</li>
                                    <li>API Key tidak memerlukan autentikasi user, cocok untuk server-side streaming</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        $(document).ready(function() {
            // Auto-submit form on Enter key
            $('#testFileId').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    $('#testForm').submit();
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/portal-training/master/test-google-drive.blade.php ENDPATH**/ ?>