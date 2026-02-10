@extends('main.layouts.main')
@section('title')
    Master Level
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection
@section('page-title')
    Master Level
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Master Level</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Master</a></li>
                    <li class="breadcrumb-item active">Level</li>
                </ol>
            </div>
        </div>


        <div class="row">

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h4 class="card-title">Master Level</h4>
                            </div>
                            <div class="col">
                                <div class="header-right d-flex flex-wrap justify-content-end">
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                        data-target="#exampleModal"> Add Level </button>
                                </div>
                            </div>
                        </div>
                        <br>

                        <div class="" style="font-size: 15px;">
                            <table id="datatable-level" class="table table-responsive-md"
                                style="width: 100%; font-size:15px;">
                                <thead>
                                    <tr>
                                        <th style="width:5%;">No.</th>
                                        <th>Level</th>
                                        <th>Keterangan</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
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
                <form class="submitLevel" id="submitLevel">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add Level</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                id="close_button_1">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="text" name="id_level" id="id_level" class="form-control" hidden>

                            <div class="form-group">
                                <label class="form-label">Level</label>
                                <input type="text" name="level" id="level" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Keterangan</label>
                                <input type="text" name="keterangan" id="keterangan" class="form-control">
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

                var datatable = $('#datatable-level').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('master.level-data') }}",
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
                            data: 'level',
                            name: 'level'
                        },
                        {
                            data: 'keterangan',
                            name: 'keterangan'
                        },
                        {
                            data: 'created_by',
                            name: 'created_by'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            render: function(data, type, row) {
                                return moment(data).format('DD-MM-YYYY HH:mm:ss');
                            }
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        },
                    ]
                });

                var routeSubmitUser = "{{ route('master.level-data-submit') }}";

                $('#submitLevel').submit(function(e) {

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

                                });

                                $('#submitLevel').trigger("reset");
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


                var routeEditUser = "{{ route('master.level-data-detail', ['id' => 'ID_PLACEHOLDER']) }}";

                $('body').on('click', '.edit-data', function() {

                    $('#exampleModal').modal('show');
                    var id = $(this).data('id');
                    var url = routeEditUser.replace('ID_PLACEHOLDER', id);

                    $.get(url, function(data) {

                        $('#id_level').val(data.data.id);

                        $('#level').val(data.data.level);
                        $('#keterangan').val(data.data.keterangan);

                    })
                });

                $('#exampleModal').on('hidden.bs.modal', function() {
                    $('#submitLevel').trigger("reset");
                    $('.error-message').hide();
                });

                $('body').on('click', '.delete-data', function() {
                    var id = $(this).data('id');
                    var url = "{{ route('master.delete-master-level-data', ['id' => 'ID_PLACEHOLDER']) }}";
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
