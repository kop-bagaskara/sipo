<?php $__env->startSection('title'); ?>
    Job Order Prepress
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(asset('public/new/plugins/datatables/dataTables.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('public/new/plugins/datatables/responsive.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('public/new/plugins/datatables/buttons.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('public/new/plugins/datatables/select.bootstrap4.css')); ?>" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

    <style>
        /* Attachment List Styling */
        .attachment-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .attachment-list .border {
            border: 1px solid #dee2e6 !important;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .attachment-list .border:hover {
            border-color: #4299e1 !important;
            box-shadow: 0 2px 8px rgba(66, 153, 225, 0.15);
            transform: translateY(-1px);
        }

        .attachment-list .badge {
            font-size: 11px;
            padding: 4px 8px;
        }

        .attachment-list .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        /* Scrollbar styling untuk attachment list */
        .attachment-list::-webkit-scrollbar {
            width: 6px;
        }

        .attachment-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .attachment-list::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .attachment-list::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Loading overlay to block UI while submitting */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.35);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }
        .loading-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            background: rgba(0,0,0,0.55);
            padding: 18px 22px;
            border-radius: 8px;
            color: #fff;
            min-width: 180px;
        }
        .loading-spinner {
            width: 46px;
            height: 46px;
            border: 4px solid #ffffff;
            border-top-color: #764ba2;
            border-radius: 50%;
            animation: spin 0.9s linear infinite;
        }
        @keyframes  spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Enhanced Error Alert Styling */
        .alert-custom {
            border: none;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .alert-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
        }

        .alert-custom.alert-danger {
            background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
            color: #c53030;
            border-left: 6px solid #e53e3e;
        }

        .alert-custom.alert-danger::before {
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        }

        .alert-custom.alert-warning {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            color: #d97706;
            border-left: 6px solid #f59e0b;
        }

        .alert-custom.alert-warning::before {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .alert-custom.alert-info {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            color: #2563eb;
            border-left: 6px solid #3b82f6;
        }

        .alert-custom.alert-info::before {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        /* SweetAlert Customization */
        .swal-wide {
            min-width: 500px !important;
            max-width: 600px !important;
        }

        .swal-title {
            font-size: 1.5rem !important;
            font-weight: 700 !important;
            color: #2d3748 !important;
        }

        .swal-content {
            font-size: 1rem !important;
            line-height: 1.6 !important;
            color: #4a5568 !important;
        }

        .swal2-popup {
            border-radius: 15px !important;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        }

        .swal2-title {
            margin-bottom: 1rem !important;
        }

        .swal2-html-container {
            margin: 1rem 0 !important;
        }

        .swal2-confirm {
            border-radius: 25px !important;
            padding: 12px 30px !important;
            font-weight: 600 !important;
            font-size: 1rem !important;
            transition: all 0.3s ease !important;
        }

        .swal2-confirm:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2) !important;
        }

        .swal2-close {
            color: #a0aec0 !important;
            transition: color 0.3s ease !important;
        }

        .swal2-close:hover {
            color: #4a5568 !important;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-title'); ?>
    Job Order Prepress
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>

    <body data-sidebar="colored">
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('content'); ?>
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Data Job Order Prepress</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Detail Job Order Prepress</li>
                </ol>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-10">
                                <h5>Job : <?php echo e($jobOrder->job_title); ?></h5>
                            </div>
                            <div class="col-md-2 d-flex align-items-center justify-content-end">
                                <button type="button" class="btn btn-primary" id="back-button">Back</button>
                            </div>
                        </div>
                        <form id="submitJobPrepress" method="POST">
                            
                            <input type="text" name="name_user" id="name_user" class="form-control"
                                value="<?php echo e(Auth::user()->name); ?>" hidden>
                            <input type="text" name="status_job_sekarang" id="status_job_sekarang" class="form-control"
                                value="<?php echo e($jobOrder->status_job); ?>" hidden>
                            <input type="text" name="pemilik_job" id="pemilik_job" class="form-control"
                                value="<?php echo e($jobOrder->created_by); ?>" hidden>
                            <input type="text" name="id_job" id="id_job" class="form-control"
                                value="<?php echo e($jobOrder->id); ?>" hidden>
                            <input type="text" name="status_job" id="status_job" class="form-control" hidden>
                            <?php echo csrf_field(); ?>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" name="tanggal" class="form-control" required
                                        value="<?php echo e($jobOrder->tanggal_job_order ? date('Y-m-d', strtotime($jobOrder->tanggal_job_order)) : ''); ?>">
                                    <div class="error-message"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="job_deadline">Job Deadline</label>
                                    <input type="date" name="job_deadline" class="form-control" required
                                        value="<?php echo e($jobOrder->tanggal_deadline ? date('Y-m-d', strtotime($jobOrder->tanggal_deadline)) : ''); ?>">
                                    <div class="error-message"></div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered mb-2">
                                        <tr>
                                            <td style="width:5%"><b>No. </b></td>
                                            <td style="width:20%"><b>Customer</b></td>
                                            <td><input type="text" name="customer" class="form-control" required
                                                    value="<?php echo e($jobOrder->customer); ?>">
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>1. </b></td>
                                            <td style="width:20%"><b>Product</b></td>
                                            <td><input type="text" name="product" class="form-control" required
                                                    value="<?php echo e($jobOrder->product); ?>">
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>2. </b></td>
                                            <td style="width:20%"><b>Kode Design</b></td>
                                            <td><input type="text" name="kode_design" class="form-control" required
                                                    value="<?php echo e($jobOrder->kode_design); ?>">
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>3. </b></td>
                                            <td style="width:20%"><b>Dimension</b></td>
                                            <td><input type="text" name="dimension" class="form-control" required
                                                    value="<?php echo e($jobOrder->dimension); ?>">
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>4. </b></td>
                                            <td style="width:20%"><b>Material</b></td>
                                            <td><input type="text" name="material" class="form-control" required
                                                    value="<?php echo e($jobOrder->material); ?>">
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>5. </b></td>
                                            <td style="width:20%"><b>Total Color</b></td>
                                            <td><input type="text" name="total_color" class="form-control" required
                                                    value="<?php echo e($jobOrder->total_color); ?>">
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td>
                                                <?php
                                                    $colorDetails = is_array($jobOrder->total_color_details)
                                                        ? $jobOrder->total_color_details
                                                        : json_decode($jobOrder->total_color_details, true);
                                                ?>

                                                <table class="table table-bordered mb-2" style="background:#fff;">
                                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                                        <tr>
                                                            <td style="width:10%"><?php echo e($i); ?>. </td>
                                                            <td style="width:40%">
                                                                <input type="text" name="color[<?php echo e($i); ?>]"
                                                                    id="color<?php echo e($i); ?>" class="form-control"
                                                                    value="<?php echo e(isset($colorDetails[$i - 1]) ? $colorDetails[$i - 1] : ''); ?>">
                                                            </td>
                                                            <td style="width:10%"><?php echo e($i + 5); ?>. </td>
                                                            <td style="width:40%">
                                                                <input type="text" name="color[<?php echo e($i + 5); ?>]"
                                                                    id="color<?php echo e($i + 5); ?>" class="form-control"
                                                                    value="<?php echo e(isset($colorDetails[$i + 4]) ? $colorDetails[$i + 4] : ''); ?>">
                                                            </td>
                                                        </tr>
                                                    <?php endfor; ?>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>6. </b></td>
                                            <td style="width:20%"><b>Qty Order Estimation</b></td>
                                            <td><input type="text" name="qty_order_estimation" class="form-control"
                                                    required value="<?php echo e($jobOrder->qty_order_estimation); ?>">
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>7. </b></td>
                                            <td style="width:20%"><b>Job Order</b></td>
                                            <td>
                                                <select name="job_order[]" id="job_order" class="form-control select2" multiple>
                                                    <?php $__currentLoopData = $jenisPekerjaan ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($item->nama_jenis); ?>"
                                                            <?php echo e(in_array($item->nama_jenis, is_array($jobOrder->job_order) ? $jobOrder->job_order : [$jobOrder->job_order]) ? 'selected' : ''); ?>>
                                                            <?php echo e($item->nama_jenis); ?>

                                                        </option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>8. </b></td>
                                            <td style="width:20%"><b>File atau Data <span class="text-danger">*</span></b></td>
                                            <td>
                                                <div class="alert alert-info alert-sm mb-2">
                                                    <small><i class="fa fa-info-circle"></i> Pilih minimal satu jenis file data yang akan disediakan</small>
                                                </div>
                                                <?php
                                                    // Pastikan $jobOrder->file_data berupa array
                                                    $fileDataList = is_array($jobOrder->file_data)
                                                        ? $jobOrder->file_data
                                                        : json_decode($jobOrder->file_data, true);
                                                ?>
                                                <table class="table table-bordered mb-2" style="background:#fff;">
                                                    <tr>
                                                        <td style="width:10%">
                                                            <input type="checkbox" name="file_data[]" id="file_data_contoh_cetak"
                                                                value="Contoh Cetak"
                                                                <?php echo e(in_array('Contoh Cetak', $fileDataList ?? []) ? 'checked' : ''); ?>>
                                                                <label for="file_data_contoh_cetak">&nbsp;</label>
                                                        </td>
                                                        <td style="width:40%">Contoh Cetak</td>
                                                        <td style="width:10%">
                                                            <input type="checkbox" name="file_data[]" id="file_data_contoh_produk"
                                                                value="Contoh Produk"
                                                                <?php echo e(in_array('Contoh Produk', $fileDataList ?? []) ? 'checked' : ''); ?>>
                                                                <label for="file_data_contoh_produk">&nbsp;</label>
                                                        </td>
                                                        <td style="width:30%">Contoh Produk</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:10%">
                                                            <input type="checkbox" name="file_data[]" id="file_data_softcopy"
                                                                value="File Softcopy"
                                                                <?php echo e(in_array('File Softcopy', $fileDataList ?? []) ? 'checked' : ''); ?>>
                                                                <label for="file_data_softcopy">&nbsp;</label>
                                                        </td>
                                                        <td style="width:40%">File Softcopy</td>
                                                    </tr>
                                                </table>
                                                <div class="error-message" id="file_data_error"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>9. </b></td>
                                            <td style="width:20%"><b>Prioritas</b></td>
                                            <td>
                                                <select name="prioritas_job" id="prioritas_job" class="form-control"
                                                    required>
                                                    <option value disabled selected>-- Pilih Prioritas --</option>
                                                    <option value="Urgent"
                                                        <?php echo e($jobOrder->prioritas_job == 'Urgent' ? 'selected' : ''); ?>>Urgent
                                                    </option>
                                                    <option value="Normal"
                                                        <?php echo e($jobOrder->prioritas_job == 'Normal' ? 'selected' : ''); ?>>Normal
                                                    </option>
                                                </select>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>10. </b></td>
                                            <td style="width:20%"><b>Catatan</b></td>
                                            <td>
                                                <textarea name="catatan" id="catatan" class="form-control"><?php echo e($jobOrder->catatan); ?></textarea>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>11. </b></td>
                                            <td style="width:20%"><b>Attachment</b></td>
                                            <td>
                                                <?php if($jobOrder->attachmentJobOrder && $jobOrder->attachmentJobOrder->count() > 0): ?>
                                                    <div class="attachment-list">
                                                        <?php $__currentLoopData = $jobOrder->attachmentJobOrder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div class="d-flex align-items-center justify-content-between mb-2 p-2 border rounded">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="mdi mdi-file-document-outline mr-2" style="font-size: 20px; color: #4299e1;"></i>
                                                                    <span class="font-weight-medium"><?php echo e($attachment->file_name); ?></span>
                                                                    <?php if($attachment->file_type): ?>
                                                                        <span class="badge badge-secondary ml-2"><?php echo e(strtoupper($attachment->file_type)); ?></span>
                                                                    <?php endif; ?>
                                                                    <?php
                                                                        // Cek ukuran file untuk menampilkan badge
                                                                        $filePath = public_path($attachment->file_path);
                                                                        if (file_exists($filePath)) {
                                                                            $fileSize = filesize($filePath);
                                                                            $fileSizeMB = round($fileSize / 1024 / 1024, 2);
                                                                            if ($fileSizeMB >= 100) {
                                                                                echo '<span class="badge badge-warning ml-2">Large File (' . $fileSizeMB . ' MB)</span>';
                                                                            }
                                                                        }
                                                                    ?>
                                                                </div>
                                                                <div class="d-flex">
                                                                    <a href="<?php echo e(asset('sipo_krisan/public/' . $attachment->file_path)); ?>"
                                                                        class="btn btn-primary btn-sm mr-1"
                                                                        title="View"
                                                                        target="_blank">
                                                                        <i class="mdi mdi-eye"></i> View
                                                                    </a>
                                                                    <a href="<?php echo e(asset('sipo_krisan/public/' . $attachment->file_path)); ?>"
                                                                        class="btn btn-info btn-sm mr-1"
                                                                        title="Download"
                                                                        download>
                                                                        <i class="mdi mdi-download"></i> Download
                                                                    </a>
                                                                    <?php if(Auth::user()->jabatan == '3' || Auth::user()->jabatan == '4' || $jobOrder->created_by == Auth::user()->name): ?>
                                                                        <form action="<?php echo e(route('prepress.job-order.delete-attachment', $attachment->id)); ?>" method="POST" class="d-inline">
                                                                            <?php echo csrf_field(); ?>
                                                                            <?php echo method_field('POST'); ?>
                                                                            <button type="submit"
                                                                                class="btn btn-danger btn-sm"
                                                                                title="Delete"
                                                                                onclick="return confirm('Apakah Anda yakin ingin menghapus file ini?')">
                                                                                <i class="mdi mdi-delete"></i> Delete
                                                                            </button>
                                                                        </form>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                <?php else: ?>
                                                    <p class="text-muted mb-0"><i>Tidak ada attachment</i></p>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                    <hr>
                                    <div class="row">
                                        <div class="col" style="text-align: center;">
                                            <span> <b>Issued by</b></span>
                                            <br>
                                            <br>
                                            <br>
                                            <span><?php echo e($jobOrder->created_by); ?></span>
                                            <br>
                                            <span><?php echo e($jobOrder->created_at ? date('Y-m-d', strtotime($jobOrder->created_at)) : date('Y-m-d')); ?></span>
                                        </div>
                                        <div class="col" style="text-align: center;">
                                            <span> <b>Received by</b></span>
                                            <br>
                                            <br>
                                            <br>
                                            <span><?php echo e($jobOrder->received_by ?? '-'); ?></span>
                                            <br>
                                            <span><?php echo e($jobOrder->received_at ? date('Y-m-d', strtotime($jobOrder->received_at)) : '-'); ?></span>
                                        </div>
                                    </div>
                                </div>

                            </div>


                            <button type="submit" id="submit-button" class="btn btn-warning my-3 w-100">Submit</button>

                            <button type="button" id="submit-button-delete"
                                class="btn btn-danger w-100">DELETE</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div id="loadingOverlay" class="loading-overlay">
            <div class="loading-box">
                <div class="loading-spinner"></div>
                <div>Sedang menyimpan...</div>
            </div>
        </div>
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('scripts'); ?>

        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {

                // Inisialisasi Select2 untuk multiple select
                $('#job_order').select2({
                    placeholder: "Pilih jenis pekerjaan...",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('body')
                });

                $('#back-button').click(function() {
                    window.location.href = "<?php echo e(route('prepress.job-order.data.index')); ?>";
                });

                var submitJobPrepress = "<?php echo e(route('prepress.job-order.submit')); ?>";

                $('#submitJobPrepress').submit(function(e) {

                    e.preventDefault();
                    var formData = $(this).serializeArray();
                    var csrfToken = $('#csrf_tokens').val();

                    // Validasi file_data - pastikan minimal ada satu yang dipilih
                    var fileDataCheckboxes = $('input[name="file_data[]"]:checked');
                    if (fileDataCheckboxes.length === 0) {
                        // Tampilkan error di bawah field file_data
                        $('#file_data_error').html('File data wajib dipilih minimal satu jenis').show();
                        // Scroll ke field file_data
                        $('html, body').animate({
                            scrollTop: $('#file_data_error').offset().top - 100
                        }, 500);
                        Swal.fire({
                            icon: 'error',
                            title: 'File Data Wajib Dipilih',
                            text: 'Mohon pilih minimal satu jenis file data yang akan disediakan.',
                            showConfirmButton: true
                        });
                        return;
                    } else {
                        // Hide error jika sudah valid
                        $('#file_data_error').hide();
                    }

                    $.ajax({
                        url: submitJobPrepress,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        data: formData,
                        type: "POST",
                        dataType: 'json',
                        success: function(response) {
                            console.log(response);
                            if (response.errors) {
                                $.each(response.errors, function(key, value) {
                                    $('#' + key).next('.error-message').text(value).show();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Job Order Prepress berhasil disubmit!',
                                    showConfirmButton: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href =
                                            "<?php echo e(route('prepress.job-order.data.index')); ?>";
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Server error atau response bukan JSON!',
                                showConfirmButton: true
                            });
                        },
                    });
                });

                var status_job_sekarang = $('#status_job_sekarang').val();
                var name_user = $('#name_user').val();
                var pemilik_job = $('#pemilik_job').val();

                // console.log(status_job_sekarang)

                if (status_job_sekarang == 'OPEN') {
                    if (name_user == pemilik_job) {
                        $('#submit-button').text('UPDATE');
                        $('#submit-button-delete').css('display', 'block');

                    } else {
                        $('#submit-button').attr('disabled', true);
                        $('#submit-button').css('display', 'none');
                        $('#submit-button-delete').css('display', 'none');
                    }


                    // $('#submit-button').text('UPDATE');
                } else {
                    $('#submit-button').attr('disabled', true);
                    $('#submit-button').css('display', 'none');
                    $('#submit-button-delete').css('display', 'none');
                }

                $('#submit-button-delete').click(function() {

                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'You want to delete this job order?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Delete',
                        cancelButtonText: 'Cancel',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "<?php echo e(route('prepress.job-order.delete-plan', $jobOrder->id)); ?>",
                                type: "POST",
                                data: {
                                    _token: "<?php echo e(csrf_token()); ?>"
                                },
                            });
                            window.location.href = "<?php echo e(route('prepress.job-order.data.index')); ?>";
                        }
                    });
                });

            });
        </script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/main/process/prepress/detail-job-order-prepress.blade.php ENDPATH**/ ?>