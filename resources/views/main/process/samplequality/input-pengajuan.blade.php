@extends('main.layouts.main')
@section('title')
    Input Pengajuan Trial
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
@endsection
@section('page-title')
    Input Pengajuan Trial
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Data Trial Bahan Baku</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Data Trial</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h3>PENGAJUAN TRIAL BAHAN BAKU</h3>
                        <form id="trialSampleForm" method="POST" action="{{ route('trial.samples.store') }}">
                            @csrf
                            <hr>
                            <div class="row">
                                <div class="col">
                                    <label for="tujuan_trial">Tujuan Trial</label>
                                    <select name="tujuan_trial" id="tujuan_trial" class="form-control" required>
                                        <option value="" disabled selected>-- Pilih Tujuan Trial --</option>
                                        <option value="trial_material">Trial Material</option>
                                        <option value="trial_produk">Trial Produk</option>
                                        <option value="trial_proses">Trial Proses</option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered mb-2">
                                        <tr>
                                            <td style="width:5%"><b>1</b></td>
                                            <td style="width:20%"><b>Material Bahan</b></td>
                                            <td>
                                                <input type="text" name="material_bahan" id="material_bahan" class="form-control" placeholder="Masukkan material bahan" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>2. </b></td>
                                            <td style="width:20%"><b>Kode Barang</b></td>
                                            <td>
                                                <input type="text" name="kode_barang" class="form-control mb-2" placeholder="Kode barang" required>
                                                <input type="text" name="nama_barang" class="form-control" placeholder="Nama barang" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>3. </b></td>
                                            <td style="width:20%"><b>Supplier</b></td>
                                            <td>
                                                <input type="text" name="kode_supplier" class="form-control mb-2" placeholder="Kode supplier" required>
                                                <input type="text" name="nama_supplier" class="form-control" placeholder="Nama supplier" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>4. </b></td>
                                            <td style="width:20%"><b>Jumlah Bahan</b></td>
                                            <td>
                                                <input type="number" name="jumlah_bahan" class="form-control" style="width:40%; margin-right:10px;" placeholder="Jumlah" step="0.01" min="0" required>
                                                <b>Satuan</b> 
                                                <input type="text" name="satuan" class="form-control" style="width:20%;margin-right:10px;margin-left:10px;" placeholder="kg/liter/pcs" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>5. </b></td>
                                            <td style="width:20%"><b>Tanggal Terima</b></td>
                                            <td>
                                                <input type="date" name="tanggal_terima" class="form-control" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>6. </b></td>
                                            <td style="width:20%"><b>Deskripsi</b></td>
                                            <td>
                                                <textarea name="deskripsi" id="deskripsi" cols="10" rows="5" class="form-control" placeholder="Jelaskan detail trial yang akan dilakukan..." required></textarea>
                                            </td>
                                        </tr>
                                    </table>
                                    <hr>
                                    <div class="row">
                                        <div class="col" style="text-align: center;">
                                            <span><b>Requested by</b></span>
                                            <br><br><br>
                                            <span>{{ auth()->user()->name }}</span>
                                            <br>
                                            <span>{{ date('Y-m-d') }}</span>
                                        </div>
                                        <div class="col" style="text-align: center;">
                                            <span><b>Status</b></span>
                                            <br><br><br>
                                            <span class="badge badge-warning">Draft</span>
                                            <br>
                                            <small>Menunggu submit ke purchasing</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-info w-100" id="submitButton">
                                        <i class="fas fa-save"></i> Simpan Draft
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-success w-100" id="submitToPurchasingBtn" style="display: none;">
                                        <i class="fas fa-paper-plane"></i> Submit ke Purchasing
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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

            $(document).ready(function() {
                let currentTrialSampleId = null;

                // Handle form submission untuk simpan draft
                $('#trialSampleForm').submit(function(e) {
                    e.preventDefault();
                    
                    var formData = $(this).serializeArray();
                    var submitUrl = $(this).attr('action');

                    $.ajax({
                        url: submitUrl,
                        data: formData,
                        type: "POST",
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                currentTrialSampleId = response.data.id;
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    showConfirmButton: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Show submit to purchasing button
                                        $('#submitToPurchasingBtn').show();
                                        $('#submitButton').text('Update Draft');
                                        
                                        // Update status display
                                        $('.badge-warning').removeClass('badge-warning').addClass('badge-info').text('Draft');
                                        $('small').text('Pengajuan tersimpan, siap untuk di-submit ke purchasing');
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message || 'Terjadi kesalahan',
                                    showConfirmButton: true
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'Terjadi kesalahan sistem';
                            
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                let errorText = '';
                                $.each(xhr.responseJSON.errors, function(key, value) {
                                    errorText += value[0] + '\n';
                                });
                                errorMessage = errorText;
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: errorMessage,
                                showConfirmButton: true
                            });
                        }
                    });
                });

                // Handle submit to purchasing
                $('#submitToPurchasingBtn').click(function() {
                    if (!currentTrialSampleId) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Harap simpan draft terlebih dahulu',
                            showConfirmButton: true
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Submit ke Purchasing?',
                        text: 'Pengajuan akan dikirim ke purchasing untuk review. Lanjutkan?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Submit!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/sipo/trial/samples/${currentTrialSampleId}/submit-purchasing`,
                                type: "POST",
                                dataType: 'json',
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: response.message,
                                            showConfirmButton: true
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = "{{ route('trial.samples.index') }}";
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text: response.message || 'Terjadi kesalahan',
                                            showConfirmButton: true
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    let errorMessage = 'Terjadi kesalahan sistem';
                                    
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                    
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: errorMessage,
                                        showConfirmButton: true
                                    });
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endsection
