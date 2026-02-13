<?php $__env->startSection('title'); ?>
    Process
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
    Process
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>
    <style>
        .process-card {
            transition: all 0.3s ease;
            cursor: pointer;
            border: 3px solid #e9ecef;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            background: white;
            opacity: 0.8;
        }

        .process-card:not(.selected) {
            filter: grayscale(30%);
            opacity: 0.6;
            transform: scale(0.98);
        }

        .process-card:not(.selected):hover {
            filter: grayscale(10%);
            opacity: 0.8;
            transform: scale(1.02);
        }

        .process-card.selected {
            opacity: 1;
            filter: none;
        }

        .process-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border-color: #007bff;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        /* Pastikan efek terjaga saat card yang dipilih di-hover */
        .process-card.selected:hover {
            transform: translateY(-8px) scale(1.08);
            border-color: #28a745 !important;
            box-shadow: 0 25px 50px rgba(40, 167, 69, 0.5), 0 0 30px rgba(40, 167, 69, 0.3) !important;
        }

        .process-card.selected {
            border-color: #28a745;
            border-width: 4px;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            transform: scale(1.08);
            box-shadow: 0 25px 50px rgba(40, 167, 69, 0.4);
            position: relative;
            z-index: 10;
            outline: 3px solid rgba(40, 167, 69, 0.3);
            outline-offset: 5px;
        }

        /* (ring dihapus untuk menghindari konflik dengan overlay hover) */

        .process-card.selected::before {
            content: '✓';
            position: absolute;
            top: 15px;
            right: 15px;
            background: #28a745;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            animation: fadeInScale 0.3s ease;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.5);
        }

        .process-card.selected .card-body::before {
            content: 'DIPILIH';
            position: absolute;
            top: 15px;
            left: 15px;
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            animation: fadeInScale 0.3s ease;
            z-index: 5;
        }

        @keyframes  fadeInScale {
            0% {
                opacity: 0;
                transform: scale(0);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .process-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .process-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .process-description {
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.4;
            transition: all 0.3s ease;
        }

        .process-card:hover .process-icon {
            transform: scale(1.1);
            color: #007bff;
        }

        .process-card:hover .process-title {
            color: #007bff;
        }

        .process-card:hover .process-description {
            color: #495057;
        }

        .process-card.selected .process-icon {
            color: #28a745;
            transform: scale(1.2);
            text-shadow: 0 0 20px rgba(40, 167, 69, 0.5);
        }

        .process-card.selected .process-title {
            color: #155724;
            text-shadow: 0 2px 4px rgba(21, 87, 36, 0.2);
            font-size: 1.6rem;
        }

        .process-card.selected .process-description {
            color: #155724;
            font-weight: 500;
        }

        .process-card.selected .process-stats {
            background: rgba(40, 167, 69, 0.3);
            color: #155724;
            font-weight: bold;
            transform: scale(1.05);
        }

        .process-stats {
            background: rgba(0, 123, 255, 0.1);
            border-radius: 10px;
            padding: 0.5rem;
            margin-top: 1rem;
            font-size: 0.8rem;
            color: #007bff;
            transition: all 0.3s ease;
        }

        .process-card:hover .process-stats {
            background: rgba(0, 123, 255, 0.2);
            transform: translateY(-2px);
        }

        .process-card.selected .process-stats {
            background: rgba(40, 167, 69, 0.2);
            color: #155724;
        }

        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }

        .btn-continue {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-continue:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.5);
        }

        .btn-continue:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-continue:not(:disabled)::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-continue:not(:disabled):hover::before {
            left: 100%;
        }

        /* Enhanced button when enabled */
        .btn-continue:not(:disabled) {
            animation: pulseButton 2s infinite;
        }

        @keyframes  pulseButton {

            0%,
            100% {
                box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3),
                    0 0 20px rgba(40, 167, 69, 0.2);
            }

            50% {
                box-shadow: 0 4px 15px rgba(40, 167, 69, 0.5),
                    0 0 30px rgba(40, 167, 69, 0.4);
            }
        }

        .selected-count {
            background: #28a745;
            color: white;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-left: 10px;
            animation: bounceIn 0.6s ease;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        }

        @keyframes  bounceIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Enhanced summary styling */
        #selectedSummary .alert {
            border: 3px solid #28a745;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            animation: slideInUp 0.5s ease;
        }

        @keyframes  slideInUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Button control styling */
        .btn-outline-primary,
        .btn-outline-secondary {
            transition: all 0.3s ease;
            border-width: 2px;
        }

        .btn-outline-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }

        .btn-outline-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }

        /* Table selection styles */
        .process-table {
            width: 100%;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }

        .process-table thead th {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .process-table tbody tr:hover {
            background: #fdfdfd;
        }

        /* Accessible hidden text for labels */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 1px, 0);
            white-space: nowrap;
            border: 0;
        }

        /* Process card pulse animation when selected */
        .process-card.selected {
            animation: pulseGlow 2s infinite;
        }

        @keyframes  pulseGlow {

            0%,
            100% {
                box-shadow: 0 25px 50px rgba(40, 167, 69, 0.4),
                    0 0 30px rgba(40, 167, 69, 0.3),
                    inset 0 0 20px rgba(40, 167, 69, 0.1);
            }

            50% {
                box-shadow: 0 25px 50px rgba(40, 167, 69, 0.6),
                    0 0 40px rgba(40, 167, 69, 0.5),
                    inset 0 0 30px rgba(40, 167, 69, 0.2);
            }
        }

        /* Add inner glow effect for selected card */
        .process-card.selected .card-body {
            background: linear-gradient(135deg,
                    rgba(40, 167, 69, 0.08) 0%,
                    rgba(40, 167, 69, 0.15) 50%,
                    rgba(40, 167, 69, 0.08) 100%);
        }

        /* Click animation */
        .process-card.clicked {
            transform: scale(0.95);
            transition: transform 0.1s ease;
        }

        /* Hover effect for process cards */
        .process-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.1) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .process-card:hover::after {
            opacity: 1;
        }

        .process-card.selected::after {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.2) 0%, transparent 100%);
            opacity: 1;
        }
    </style>

    <body data-sidebar="colored">
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('content'); ?>
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Pilih Proses Rencana Plan</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Penjadwalan</a></li>
                    <li class="breadcrumb-item active">Pilih Proses Rencana Plan</li>
                </ol>
            </div>
        </div>


        <div class="card">
            <div class="card-body">
                <!-- Process Selection Table -->
                <div class="row">
                    <div class="col-12 mb-3 d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-list"></i> Daftar Proses</h4>
                    </div>
                    <div class="col-12">
                        <table class="table process-table">
                            <thead>
                                <tr>
                                    <th style="width: 60px;" class="text-center">Pilih</th>
                                    <th>Proses</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">
                                        <input type="radio" name="process_choice" class="check-process" id="proc_cetak"
                                            value="CETAK">
                                        <label for="proc_cetak" class="sr-only">Pilih proses CETAK</label>
                                    </td>
                                    <td><strong>CETAK</strong></td>
                                    <td>Proses pencetakan desain, logo, atau informasi pada material.</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <input type="radio" name="process_choice" class="check-process" id="proc_plong"
                                            value="PLONG">
                                        <label for="proc_plong" class="sr-only">Pilih proses PLONG</label>
                                    </td>
                                    <td><strong>PLONG</strong></td>
                                    <td>Plan mencakup proses Hotstamp, UV, EMBOS, dan Plong.
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td class="text-center">
                                        <input type="radio" name="process_choice" class="check-process" id="proc_sortir"
                                            value="SORTIR">
                                        <label for="proc_sortir" class="sr-only">Pilih proses SORTIR</label>
                                    </td>
                                    <td><strong>SORTIR</strong></td>
                                    <td>Pemisahan/pengelompokan material sesuai kriteria untuk memastikan kualitas.</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <input type="radio" name="process_choice" class="check-process" id="proc_glueing"
                                            value="GLUEING">
                                        <label for="proc_glueing" class="sr-only">Pilih proses GLUEING</label>
                                    </td>
                                    <td><strong>GLUEING</strong></td>
                                    <td>Proses perekatan menggunakan adhesive untuk assembly/finishing.</td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Continue Button -->
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <button type="button" class="btn btn-continue" id="btnContinue" disabled>
                            <i class="fas fa-arrow-right"></i> Lanjutkan ke Planning
                        </button>
                    </div>
                </div>

                <!-- Selected Processes Summary -->
                <div class="row mt-4" id="selectedSummary" style="display: none;">
                    <div class="col-12">
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle"></i> Proses yang Dipilih:</h6>
                            <div id="selectedProcessesList"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('scripts'); ?>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.buttons.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/buttons.html5.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/buttons.flash.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/buttons.print.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.keyTable.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/dataTables.select.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/pdfmake.min.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/plugins/datatables/vfs_fonts.js')); ?>"></script>
        <script src="<?php echo e(asset('sipo_krisan/public/new/assets/pages/datatables-demo.js')); ?>"></script>

        <script>
            $(document).ready(function() {
                let selectedProcesses = [];

                $(document).on('change', '.check-process', function() {
                    const chosen = $('.check-process:checked').val();
                    selectedProcesses = chosen ? [chosen] : [];
                    updateUI();
                });

                // Event handler untuk Continue Button
                $('#btnContinue').on('click', function() {
                    if (selectedProcesses.length > 0) {
                        const chosenProcess = selectedProcesses[0];

                        // Jika GLUEING, redirect ke route khusus GLUEING
                        if (chosenProcess === 'GLUEING') {
                            const planningUrl = "<?php echo e(route('planning.glueing')); ?>";
                            const target = `${planningUrl}?processes=${encodeURIComponent(chosenProcess)}`;
                            window.location.href = target;
                        } else {
                            // Untuk proses lain, gunakan route planning biasa
                            const processesParam = selectedProcesses.join(',');
                            const planningUrl = "<?php echo e(route('planning.blade')); ?>";
                            const target = `${planningUrl}?processes=${encodeURIComponent(processesParam)}`;
                            window.location.href = target;
                        }
                    }
                });

                // Fungsi untuk update UI
                function updateUI() {
                    const count = selectedProcesses.length;

                    $('#selectedProcessesCount').text(count);

                    if (count > 0) {
                        $('#btnContinue').prop('disabled', false);
                        $('#selectedSummary').show();

                        const processesList = selectedProcesses.map(process =>
                            `<span class="badge badge-primary mr-2 mb-1">${process}</span>`
                        ).join('');
                        $('#selectedProcessesList').html(processesList);
                    } else {
                        $('#btnContinue').prop('disabled', true);
                        $('#selectedSummary').hide();
                    }
                }

                updateUI();
            });
        </script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/main/process/pilih-rencana-plan.blade.php ENDPATH**/ ?>