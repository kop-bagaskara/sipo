@extends('main.layouts.main')
@section('title')
    Job Order Prepress
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

    <style>
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
        @keyframes spin {
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

        .alert-custom .alert-icon {
            font-size: 24px;
            margin-right: 12px;
            float: left;
        }

        .alert-custom .alert-content {
            overflow: hidden;
        }

        .alert-custom .alert-title {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .alert-custom .alert-message {
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 0;
        }

        .alert-custom .alert-details {
            background: rgba(255,255,255,0.7);
            border-radius: 8px;
            padding: 12px;
            margin-top: 12px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #4a5568;
        }

        .alert-custom .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 20px;
            color: inherit;
            opacity: 0.7;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .alert-custom .close-btn:hover {
            opacity: 1;
        }

        /* Field Error Styling */
        .form-control.is-invalid {
            border-color: #e53e3e;
            box-shadow: 0 0 0 0.2rem rgba(229, 62, 62, 0.25);
        }

        .error-message {
            color: #e53e3e;
            font-size: 12px;
            margin-top: 4px;
            display: none;
            background: #fff5f5;
            padding: 6px 10px;
            border-radius: 6px;
            border-left: 3px solid #e53e3e;
        }

        /* Success Alert Styling */
        .alert-custom.alert-success {
            background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
            color: #22543d;
            border-left: 6px solid #38a169;
        }

        .alert-custom.alert-success::before {
            background: linear-gradient(135deg, #38a169 0%, #22543d 100%);
        }

        /* Small Info Alert Styling */
        .alert-info.alert-sm {
            padding: 8px 12px;
            font-size: 12px;
            border-radius: 6px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-left: 3px solid #3b82f6;
            color: #1e40af;
        }

        .alert-info.alert-sm i {
            margin-right: 6px;
        }

        /* SweetAlert Custom Styling */
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
@endsection
@section('page-title')
    Job Order Prepress
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Data Job Order Prepress</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Data Job Order Prepress</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h3>JOB ORDER TO PREPRESS</h3>
                        <form id="submitJobPrepress" method="POST">
                            @csrf
                            <hr>
                            <input type="text" name="status_job" id="status_job" class="form-control" value="OPEN"
                                hidden>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" name="tanggal" class="form-control" required>
                                    <div class="error-message"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="job_deadline">Job Deadline</label>
                                    <input type="date" name="job_deadline" class="form-control">
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
                                            <td>
                                                <input type="text" name="customer" class="form-control" required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>1. </b></td>
                                            <td style="width:20%"><b>Product</b></td>
                                            <td>
                                                <input type="text" name="product" class="form-control" required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>2. </b></td>
                                            <td style="width:20%"><b>Kode Design</b></td>
                                            <td>
                                                <input type="text" name="kode_design" class="form-control" required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>3. </b></td>
                                            <td style="width:20%"><b>Dimension</b></td>
                                            <td>
                                                <input type="text" name="dimension" class="form-control" required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>4. </b></td>
                                            <td style="width:20%"><b>Material</b></td>
                                            <td>
                                                <input type="text" name="material" class="form-control" required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>5. </b></td>
                                            <td style="width:20%"><b>Total Color</b></td>
                                            <td>
                                                <input type="text" name="total_color" class="form-control" required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td>
                                                <table class="table table-bordered mb-2" style="background:#fff;">
                                                    <tr>
                                                        <td style="width:10%">1. </td>
                                                        <td style="width:40%"><input type="text" name="color[1]"
                                                                id="color" class="form-control"></td>
                                                        <td style="width:10%">6. </td>
                                                        <td style="width:40%"><input type="text" name="color[]"
                                                                id="color" class="form-control"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:10%">2. </td>
                                                        <td style="width:40%"><input type="text" name="color[2]"
                                                                id="color" class="form-control"></td>
                                                        <td style="width:10%">7. </td>
                                                        <td style="width:40%"><input type="text" name="color[7]"
                                                                id="color" class="form-control"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:10%">3. </td>
                                                        <td style="width:40%"><input type="text" name="color[3]"
                                                                id="color" class="form-control"></td>
                                                        <td style="width:10%">8. </td>
                                                        <td style="width:40%"><input type="text" name="color[8]"
                                                                id="color" class="form-control"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:10%">4. </td>
                                                        <td style="width:40%"><input type="text" name="color[4]"
                                                                id="color" class="form-control"></td>
                                                        <td style="width:10%">9. </td>
                                                        <td style="width:40%"><input type="text" name="color[9]"
                                                                id="color" class="form-control"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:10%">5. </td>
                                                        <td style="width:40%"><input type="text" name="color[5]"
                                                                id="color" class="form-control"></td>
                                                        <td style="width:10%">10. </td>
                                                        <td style="width:40%"><input type="text" name="color[10]"
                                                                id="color" class="form-control"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>6. </b></td>
                                            <td style="width:20%"><b>Qty Order Estimation</b></td>
                                            <td>
                                                <input type="text" name="qty_order_estimation" class="form-control"
                                                    required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>7. </b></td>
                                            <td style="width:20%"><b>Job Order</b></td>
                                            <td>
                                                <table class="table table-bordered mb-2" style="background:#fff;">
                                                    <tr>
                                                        <select name="job_order[]" id="job_order"
                                                            class="form-control select2" multiple>
                                                            @foreach ($jenisPekerjaan as $item)
                                                                <option value="{{ $item->nama_jenis }}">
                                                                    {{ $item->nama_jenis }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="error-message"></div>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>8. </b></td>
                                            <td style="width:20%"><b>File atau Data <span class="text-danger">*</span></b></td>
                                            <td>
                                                <div class="alert alert-info alert-sm mb-2">
                                                    <small><i class="fa fa-info-circle"></i> Pilih minimal satu jenis file data yang akan disediakan</small>
                                                </div>
                                                <table class="table table-bordered mb-2" style="background:#fff;">
                                                    <tr>
                                                        <td style="width:10%"><input type="checkbox" name="file_data[]"
                                                                id="file_data_contoh_cetak" value="Contoh Cetak">
                                                            <label for="file_data_contoh_cetak">&nbsp;</label>
                                                        </td>
                                                        <td style="width:40%"><label for="file_data_contoh_cetak">Contoh
                                                                Cetak</label></td>
                                                        <td style="width:10%"><input type="checkbox" name="file_data[]"
                                                                id="file_data_contoh_produk" value="Contoh Produk">
                                                            <label for="file_data_contoh_produk">&nbsp;</label>
                                                        </td>
                                                        <td style="width:30%"><label for="file_data_contoh_produk">Contoh
                                                                Produk</label></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:10%"><input type="checkbox" name="file_data[]"
                                                                id="file_data_softcopy" value="File Softcopy">
                                                            <label for="file_data_softcopy">&nbsp;</label>
                                                        </td>
                                                        <td style="width:40%"><label for="file_data_softcopy">File
                                                                Softcopy</label></td>
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
                                                    <option value="Urgent">Urgent</option>
                                                    <option value="Normal">Normal</option>
                                                </select>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>10. </b></td>
                                            <td style="width:20%"><b>Catatan</b></td>
                                            <td>
                                                <textarea name="catatan" id="catatan" class="form-control"></textarea>
                                                <div class="error-message"></div>
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
                                            <span>{{ auth()->user()->name }}</span>
                                            <br>
                                            <span>{{ date('Y-m-d') }}</span>
                                        </div>
                                        <div class="col" style="text-align: center;">
                                            <span> <b>Received by</b></span>
                                            <br>
                                            <br>
                                            <br>
                                            <span>-</span>
                                            <br>
                                            <span>-</span>
                                        </div>
                                    </div>
                                </div>

                            </div>


                            <button type="submit" class="btn btn-info my-4 w-100" id="submitButton">Submit</button>

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
    @endsection
    @section('scripts')
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.flash.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.print.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.keyTable.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.select.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/pdfmake.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/vfs_fonts.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/assets/pages/datatables-demo.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


        <script>
            // Setup CSRF token untuk semua AJAX request
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Helper function untuk menampilkan SweetAlert yang lebih user-friendly
            function showSweetAlert(type, title, message, details = null) {
                let icon = '';
                let confirmButtonColor = '';

                switch(type) {
                    case 'error':
                        icon = 'error';
                        confirmButtonColor = '#e53e3e';
                        break;
                    case 'warning':
                        icon = 'warning';
                        confirmButtonColor = '#f59e0b';
                        break;
                    case 'info':
                        icon = 'info';
                        confirmButtonColor = '#3b82f6';
                        break;
                    case 'success':
                        icon = 'success';
                        confirmButtonColor = '#38a169';
                        break;
                }

                let htmlContent = `<div class="text-left">${message}`;
                if (details) {
                    htmlContent += `<hr><div class="mt-3"><strong>Detail:</strong><br>${details}</div>`;
                }
                htmlContent += '</div>';

                Swal.fire({
                    icon: icon,
                    title: title,
                    html: htmlContent,
                    confirmButtonText: 'OK',
                    confirmButtonColor: confirmButtonColor,
                    showCloseButton: true,
                    customClass: {
                        popup: 'swal-wide',
                        title: 'swal-title',
                        content: 'swal-content'
                    }
                });
            }

            // Helper function untuk menampilkan field errors
            function showFieldErrors(errors) {
                // Reset semua field error
                $('.form-control').removeClass('is-invalid');
                $('.error-message').hide();

                // Tampilkan error untuk setiap field
                $.each(errors, function(field, messages) {
                    let fieldElement = $(`[name="${field}"]`);
                    if (fieldElement.length) {
                        fieldElement.addClass('is-invalid');

                        // Buat atau update error message
                        let errorDiv = fieldElement.next('.error-message');
                        if (errorDiv.length === 0) {
                            errorDiv = $('<div class="error-message"></div>');
                            fieldElement.after(errorDiv);
                        }

                        errorDiv.html(Array.isArray(messages) ? messages.join('<br>') : messages).show();
                    }
                });
            }

            $(document).ready(function() {
                var isSubmittingJob = false;

                // Inisialisasi Select2 untuk multiple select
                $('#job_order').select2({
                    placeholder: "Pilih jenis pekerjaan...",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('body')
                });

                // Cek limit job order saat tanggal deadline berubah
                $('input[name="job_deadline"]').on('change', function() {
                    checkJobOrderLimit($(this).val());
                });

                var submitJobPrepress = "{{ route('prepress.job-order.submit') }}";

                $('#submitJobPrepress').submit(function(e) {
                    e.preventDefault();
                    if (isSubmittingJob) {
                        return; // prevent double submit
                    }

                    // Reset field errors
                    $('.form-control').removeClass('is-invalid');
                    $('.error-message').hide();

                    // Validasi file_data - pastikan minimal ada satu yang dipilih
                    var fileDataCheckboxes = $('input[name="file_data[]"]:checked');
                    if (fileDataCheckboxes.length === 0) {
                        // Tampilkan error di bawah field file_data
                        $('#file_data_error').html('File data wajib dipilih minimal satu jenis').show();
                        // Scroll ke field file_data
                        $('html, body').animate({
                            scrollTop: $('#file_data_error').offset().top - 100
                        }, 500);
                        showSweetAlert('error', 'File Data Wajib Dipilih', 'Mohon pilih minimal satu jenis file data yang akan disediakan.', 'Pilih salah satu atau lebih dari: Contoh Cetak, Contoh Produk, atau File Softcopy.');
                        return;
                    } else {
                        // Hide error jika sudah valid
                        $('#file_data_error').hide();
                    }

                    var formData = $(this).serializeArray();
                    // show loading & guard
                    isSubmittingJob = true;
                    $('#submitButton').prop('disabled', true).text('Menyimpan...');
                    $('#loadingOverlay').fadeIn(120);

                    $.ajax({
                        url: submitJobPrepress,
                        data: formData,
                        type: "POST",
                        dataType: 'json',
                        success: function(response) {
                            console.log(response);
                            if (response.errors) {
                                showFieldErrors(response.errors);
                                showSweetAlert('error', 'Validasi Error', 'Mohon periksa kembali data yang diinput. Beberapa field memiliki kesalahan validasi.', 'Silakan perbaiki field yang ditandai dengan border merah.');
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Job Order Prepress berhasil disubmit! Mohon ditunggu Job diproses oleh Team Prepress. Untuk monitoring, silahkan klik Dashboard Prepress!',
                                    showConfirmButton: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href =
                                            "{{ route('prepress.job-order.data.index') }}";
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'Terjadi kesalahan pada server. Silakan coba lagi nanti.';
                            let errorDetails = '';

                            if (xhr.responseJSON) {
                                // Handle validation errors
                                if (xhr.responseJSON.errors) {
                                    showFieldErrors(xhr.responseJSON.errors);
                                    showSweetAlert('error', 'Validasi Error', 'Mohon periksa kembali data yang diinput. Beberapa field memiliki kesalahan validasi.', 'Silakan perbaiki field yang ditandai dengan border merah.');
                                    return;
                                }

                                errorMessage = xhr.responseJSON.message || errorMessage;

                                // Jika limit reached, tampilkan alert khusus
                                if (xhr.responseJSON.limit_reached) {
                                    showSweetAlert('warning', 'Limit Job Order Tercapai',
                                        `Tanggal deadline ${xhr.responseJSON.deadline_date || 'yang dipilih'} sudah mencapai limit maksimal.`,
                                        `Limit: ${xhr.responseJSON.limit} job order<br>Current: ${xhr.responseJSON.current_count} job order<br><br><strong>Silakan koordinasi terlebih dahulu dengan Head/SPV Prepress!</strong>`);
                                    return;
                                }

                                // Handle specific error types
                                if (xhr.responseJSON.type === 'data_type_error') {
                                    errorMessage = 'Kesalahan tipe data pada input. Mohon periksa kembali.';
                                    errorDetails = xhr.responseJSON.details || 'Pastikan format input sesuai dengan yang diminta (angka untuk field numerik, tanggal untuk field tanggal, dll).';
                                } else if (xhr.responseJSON.type === 'database_error') {
                                    errorMessage = 'Kesalahan pada database. Data tidak dapat disimpan.';
                                    errorDetails = xhr.responseJSON.details || 'Silakan coba lagi atau hubungi administrator jika masalah berlanjut.';
                                } else if (xhr.responseJSON.type === 'validation_error') {
                                    errorMessage = 'Data tidak lengkap atau tidak valid.';
                                    errorDetails = xhr.responseJSON.details || 'Mohon periksa kembali semua field yang wajib diisi.';
                                }
                            }

                            // Jika CSRF token mismatch
                            if (xhr.status === 419) {
                                errorMessage = 'Session expired. Silakan refresh halaman dan coba lagi.';
                                errorDetails = 'Ini terjadi karena session login sudah berakhir. Silakan login ulang.';
                            }

                            // Jika server error
                            if (xhr.status >= 500) {
                                errorMessage = 'Terjadi kesalahan pada server. Silakan coba lagi nanti.';
                                errorDetails = `Error Code: ${xhr.status}<br>Silakan hubungi administrator jika masalah berlanjut.`;
                            }

                            // Jika bad request (400) - biasanya validation atau data error
                            if (xhr.status === 400) {
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                    if (xhr.responseJSON.details) {
                                        errorDetails = xhr.responseJSON.details;
                                    }
                                } else {
                                    errorMessage = 'Data yang dikirim tidak valid atau tidak lengkap.';
                                    errorDetails = 'Mohon periksa kembali semua field yang wajib diisi.';
                                }
                            }

                            showSweetAlert('error', 'Gagal Submit Job Order', errorMessage, errorDetails);
                        },
                        complete: function() {
                            // always hide overlay and re-enable submit (if not redirected yet)
                            $('#loadingOverlay').fadeOut(100);
                            $('#submitButton').prop('disabled', false).text('Submit');
                            isSubmittingJob = false;
                        }
                    });
                });

            });

            // Fungsi untuk cek limit job order
            function checkJobOrderLimit(deadlineDate) {
                if (!deadlineDate) {
                    // Jika tanggal kosong, hapus alert dan enable tombol
                    $('.alert-custom').remove();
                    $('#submitButton').prop('disabled', false);
                    return;
                }

                $.ajax({
                    url: "{{ route('prepress.job-order.check-limit') }}",
                    type: "POST",
                    data: {
                        deadline_date: deadlineDate
                    },
                    success: function(response) {
                        // Hapus alert sebelumnya jika ada
                        $('.alert-custom').remove();

                        // Enable tombol submit terlebih dahulu
                        $('#submitButton').prop('disabled', false);

                        if (response.current_count > 0) {
                            let remainingSlots = response.limit - response.current_count;

                            if (remainingSlots <= 0) {
                                showSweetAlert('warning', 'Limit Job Order Tercapai',
                                    `Tanggal deadline ${deadlineDate} sudah mencapai limit maksimal.`,
                                    `Limit: ${response.limit} job order<br>Current: ${response.current_count} job order<br><br><strong>Silakan koordinasi dengan Head/SPV Prepress!</strong>`);
                                $('#submitButton').prop('disabled', true);
                            } else {
                                showSweetAlert('info', 'Info Limit Job Order',
                                    `Tanggal deadline ${deadlineDate} sudah memiliki ${response.current_count} job order.`,
                                    `Sisa slot tersedia: <strong>${remainingSlots}</strong> job order.`);
                            }
                        }
                    },
                    error: function(xhr) {
                        console.log('Error checking limit:', xhr);
                        // Jika error, hapus alert dan enable tombol
                        $('.alert-custom').remove();
                        $('#submitButton').prop('disabled', false);

                        showSweetAlert('error', 'Error Cek Limit', 'Gagal memeriksa limit job order.', 'Silakan coba lagi atau hubungi administrator.');
                    }
                });
            }
        </script>
    @endsection
