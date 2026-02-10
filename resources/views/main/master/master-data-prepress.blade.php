@extends('main.layouts.main')
@section('title')
    Master Data Prepress
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

    <style>
        .cust-col {
            white-space: nowrap;
        }
    </style>
@endsection
@section('page-title')
    Master Data Prepress
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Master Data Prepress</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Master</a></li>
                    <li class="breadcrumb-item active">Data Prepress</li>
                </ol>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h4 class="card-title">Master Data Prepress</h4>
                            </div>
                            <div class="col">
                                <div class="header-right d-flex flex-wrap justify-content-end">
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#exampleModal"> Add Master Data </button>
                                </div>
                            </div>
                        </div>
                        <br>

                        <div class="" style="font-size: 15px;">
                            <table id="datatable-prepress-data" class="table table-responsive-md"
                                style="width: 100%; font-size:15px;">
                                <thead>
                                    <tr>
                                        <th style="width:5%;">No.</th>
                                        <th style="white-space: nowrap;">Kode</th>
                                        <th style="white-space: nowrap;">Keterangan</th>
                                        <th style="white-space: nowrap;">Job</th>
                                        <th style="white-space: nowrap;">Unit Job</th>
                                        <th style="white-space: nowrap;">Waktu Job</th>
                                        <th style="white-space: nowrap;">Job Rate</th>
                                        <th style="white-space: nowrap;">Point Job</th>
                                        <th style="white-space: nowrap;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form class="submitMasterDataPrepress" id="submitMasterDataPrepress">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add Master Data Prepress</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                id="close_button_1">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="text" name="id_data_prepress" id="id_data_prepress" class="form-control" hidden>

                            <div class="form-group">
                                <label class="form-label">Kode</label>
                                <input type="text" name="kode" id="kode" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" id="keterangan" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Waktu Job</label>
                                <input type="text" name="waktu_job" id="waktu_job" class="form-control"
                                    placeholder="Waktu Job Dalam Menit" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Job Rate</label>
                                <input type="text" name="job_rate" id="job_rate" class="form-control"
                                    placeholder="Job Rate (contoh: 1000, 2000)" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Point Job</label>
                                <input type="number" name="point_job" id="point_job" class="form-control"
                                    placeholder="Point Job (contoh: 10, 20)" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Job</label>
                                <input type="text" name="job" id="job" class="form-control"
                                    placeholder="Job" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Unit Job</label>
                                <input type="text" name="unit_job" id="unit_job" class="form-control"
                                    placeholder="Unit Job" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                id="close_button">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
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

        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

        <script>
            $(document).ready(function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var datatable = $('#datatable-prepress-data').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('master-data-prepress.data') }}",
                        type: "POST",
                        data: function(d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                        },
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'kode',
                            name: 'kode'
                        },
                        {
                            data: 'keterangan_job',
                            name: 'keterangan_job'
                        },
                        {
                            data: 'job',
                            name: 'job'
                        },
                        {
                            data: 'unit_job',
                            name: 'unit_job'
                        },
                        {
                            data: 'waktu_job',
                            name: 'waktu_job'
                        },
                        {
                            data: 'job_rate',
                            name: 'job_rate'
                        },
                        {
                            data: 'point_job',
                            name: 'point_job'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'cust-col'
                        },
                    ]
                });

                var routeSubmitUser = "{{ route('master-data-prepress.submit') }}";

                $('#submitMasterDataPrepress').submit(function(e) {

                    e.preventDefault();
                    var formData = $(this).serializeArray();
                    var csrfToken = $('#csrf_tokens').val();

                    $.ajax({
                        url: routeSubmitUser,
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
                                $('.alert-danger').hide();
                                $("#exampleModal").removeClass("in");
                                $(".modal-backdrop").remove();
                                $("#exampleModal").hide();
                                // $('#exampleModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: `${response.success}`,
                                    showConfirmButton: false,
                                    timer: 3000
                                }).then(function() {

                                    datatable.draw();
                                    window.location.reload();


                                });

                                $('#submitDisivi').trigger("reset");
                                $('#exampleModal').modal('hide');
                                $('#select-machines').show();
                                $('#name-machines').css('display', 'none');
                                datatable.draw();

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


                var routeEditUser = "{{ route('master-data-prepress.detail', ['id' => 'ID_PLACEHOLDER']) }}";

                $('body').on('click', '.edit-data', function() {

                    $('#exampleModal').modal('show');
                    var id = $(this).data('id');
                    var url = routeEditUser.replace('ID_PLACEHOLDER', id);

                    $.get(url, function(data) {

                        $('#id_data_prepress').val(data.data.id);
                        $('#kode').val(data.data.kode);
                        $('#waktu_job').val(data.data.waktu_job);
                        $('#job_rate').val(data.data.job_rate);
                        $('#point_job').val(data.data.point_job);
                        $('#job').val(data.data.job);
                        $('#unit_job').val(data.data.unit_job);

                        $('#keterangan').val(data.data.keterangan_job);

                    })
                });

                $('#exampleModal').on('hidden.bs.modal', function() {
                    $('#submitMasterDataPrepress').trigger("reset");
                    $('.error-message').hide();
                });

                $('body').on('click', '.delete-data', function() {
                    var id = $(this).data('id');
                    var url = "{{ route('master-data-prepress.delete', ['id' => 'ID_PLACEHOLDER']) }}";
                    url = url.replace('ID_PLACEHOLDER', id);

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: url,
                                type: 'POST',
                                dataType: 'json',
                                success: function(response) {
                                    Swal.fire(
                                        'Deleted!',
                                        response.message,
                                        'success'
                                    );
                                    datatable.draw();
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: 'Server error atau response bukan JSON!',
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
