@extends('main.layouts.main')

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

        .whitespace-nowrap {
            white-space: nowrap;
        }
    </style>

@endsection
@section('page-title')
    Master Hari Libur
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Master Hari Libur</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Master</a></li>
                    <li class="breadcrumb-item active">Hari Libur</li>
                </ol>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h4 class="card-title">Master Hari Libur</h4>
                            </div>
                            <div class="col">
                                <div class="header-right d-flex flex-wrap justify-content-end">
                                    <button type="button" class="btn btn-info" data-toggle="modal"
                                        data-target="#exampleModal"> Add Master Hari Libur </button>
                                </div>
                            </div>
                        </div>
                        <br>

                        <div class="table-responsive" style="font-size: 15px;">
                            <table id="datatable-holiday-day-data" class="table table-responsive-md"
                                style="width: 100%; font-size:15px;">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jenis Hari</th>
                                        <th>Jam Kerja</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                        <th>Action</th>
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
                <form class="submitMasterHolidayDays" id="submitMasterHolidayDays">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add Master Hari Libur</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                id="close_button_1">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="text" name="id_holiday_day" id="id_holiday_day" class="form-control" hidden>

                            <div class="form-group">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jenis Hari</label>
                                <input type="text" name="jenis_hari" id="jenis_hari" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jam Kerja</label>
                                <input type="text" name="jam_kerja" id="jam_kerja" class="form-control"
                                    placeholder="Jam Kerja" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" id="keterangan" class="form-control"
                                    placeholder="Keterangan" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Status Active</label>
                                <input type="text" name="status_active" id="status_active" class="form-control"
                                    placeholder="Status Active" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                id="close_button_1">Close</button>
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

                var datatable = $('#datatable-holiday-day-data').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('master.holiday-days-data') }}",
                        type: "POST",
                        data: function(d) {
                            d._token = $('meta[name="csrf-token"]').attr('content');
                        },
                    },
                    columns: [
                        {
                            data: 'date',
                            name: 'date',
                            className: 'whitespace-nowrap',
                            render: function(data, type, row) {
                                return moment(data).format('DD-MM-YYYY');
                            }
                        },
                        {
                            data: 'override_type',
                            name: 'override_type',
                            className: 'whitespace-nowrap'
                        },
                        {
                            data: 'working_hours',
                            name: 'working_hours',
                            className: 'whitespace-nowrap'
                        },
                        {
                            data: 'is_active',
                            name: 'is_active',
                            className: 'whitespace-nowrap'
                        },
                        {
                            data: 'description',
                            name: 'description',
                            className: 'whitespace-nowrap'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'whitespace-nowrap'
                        }
                    ],
                });

                var routeSubmitUser = "{{ route('master.holiday-days-submit') }}";

                $('#submitMasterHolidayDays').submit(function(e) {

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


                var routeEditUser = "{{ route('master.holiday-days-data-detail', ['id' => 'ID_PLACEHOLDER']) }}";

                $('body').on('click', '.edit-data', function() {

                    $('#exampleModal').modal('show');
                    var id = $(this).data('id');
                    var url = routeEditUser.replace('ID_PLACEHOLDER', id);

                    $.get(url, function(data) {
                        console.log(data);

                        $('#id_holiday_day').val(data.data.id);
                        $('#tanggal').val(data.data.date);
                        $('#jenis_hari').val(data.data.override_type);
                        $('#jam_kerja').val(data.data.working_hours);
                        $('#status_active').val(data.data.is_active);
                        $('#keterangan').val(data.data.description);

                        $('#exampleModal').modal('show');
                    })
                });

                $('#exampleModal').on('hidden.bs.modal', function() {
                    $('#submitMasterHolidayDays').trigger("reset");
                    $('.error-message').hide();
                });

                $('body').on('click', '.delete-data', function() {
                    var id = $(this).data('id');
                    var url = "{{ route('master.delete-holiday-days-data', ['id' => 'ID_PLACEHOLDER']) }}";
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
