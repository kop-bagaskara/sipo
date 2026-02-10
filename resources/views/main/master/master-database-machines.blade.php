@extends('main.layouts.main')
@section('title')
    Master Database Machines
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection
@section('page-title')
    Master Database Machines
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Master Database Machines</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Master</a></li>
                    <li class="breadcrumb-item active">Database Machines</li>
                </ol>
            </div>
        </div>
        <div class="row">

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col">
                                <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal"
                                    data-target="#modal-series-material">
                                    <i class="mdi mdi-plus"></i> Tambah Data Database
                                </button>
                            </div>
                        </div>
                        <div class="">
                            <table id="datatable-machine"
                                class="w-100 table table-hover table-bordered table-responsive-md">
                                <thead>
                                    <tr>
                                        <th style="width:5%;">No.</th>
                                        <th>Mesin Name</th>
                                        <th>Mesin IP</th>
                                        <th>Mesin Port</th>
                                        <th>Mesin DB Name</th>
                                        <th>Mesin Column</th>
                                        <th style="width:15%;">Action</th>
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
        <!-- Modal Series Material -->
        <div class="modal fade" id="modal-series-material" tabindex="-1" role="dialog"
            aria-labelledby="modal-series-material-label" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-series-material-label">Tambah Database Machine</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"><i
                                class="mdi mdi-close"></i></button>
                    </div>
                    <form id="form-series-material">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Machine Name</label>
                                <select name="machine_name" id="machine_name" class="form-control">
                                    <option value="" disabled selected>Pilih Mesin</option>
                                    @foreach ($query as $item)
                                        <option value="{{ $item->Code }}">{{ $item->Code }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Machine IP</label>
                                <input type="text" name="machine_ip" id="machine_ip" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Machine Port</label>
                                <input type="text" name="machine_port" id="machine_port" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Machine DB Name</label>
                                <input type="text" name="machine_db_name" id="machine_db_name" class="form-control"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>Machine Column</label>
                                <input type="text" name="machine_column" id="machine_column" class="form-control"
                                    required>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="save-series-material">Simpan</button>
                        </div>
                    </form>
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

        <script>
            $(document).ready(function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });


                var datatable = $('#datatable-machine').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('master.database-machines-data') }}",
                        type: "POST",
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'machine_name',
                            name: 'machine_name'
                        },
                        {
                            data: 'machine_ip',
                            name: 'machine_ip'
                        },
                        {
                            data: 'machine_port',
                            name: 'machine_port'
                        },
                        {
                            data: 'machine_db_name',
                            name: 'machine_db_name'
                        },
                        {
                            data: 'machine_column_name',
                            name: 'machine_column_name'
                        },
                        {
                            data: 'action',
                            name: 'action'
                        },
                    ]
                });

                var routeSubmitMasterSeries = "{{ route('master.database-machines-data-submit') }}";

                $('#save-series-material').on('click', function(e) {

                    e.preventDefault();
                    var formData = $('#form-series-material').serializeArray();
                    var csrfToken = $('#csrf_tokens').val();

                    $.ajax({
                        url: routeSubmitMasterSeries,
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
                                $("#modal-series-material").removeClass("in");
                                $(".modal-backdrop").remove();
                                $("#modal-series-material").hide();

                                Swal.fire({
                                    icon: 'success',
                                    title: `${response.message}`,
                                    showConfirmButton: false,
                                    timer: 3000
                                }).then(function() {
                                    datatable.draw();
                                });

                                $('#form-series-material').trigger("reset");
                                $('#modal-series-material').modal('hide');
                                datatable.draw();

                            }
                        }
                    });

                });

                // whern modal closed form form-series-material reset
                $('#modal-series-material').on('hidden.bs.modal', function() {
                    $('#form-series-material').trigger("reset");
                });

                // edit data
                $(document).on('click', '.edit-data', function() {
                    // $('#form-series-material').trigger("reset");
                    var id = $(this).data('id');
                    var name = $(this).data('name');
                    var process = $(this).data('process');
                    var department = $(this).data('department');

                    $('#form-series-material').find('input[name="id"]').val(id);
                    $('#form-series-material').find('input[name="name"]').val(name);
                    $('#form-series-material').find('select[name="process"]').val(process);
                    $('#form-series-material').find('select[name="department"]').val(department);

                    $('#modal-series-material').modal('show');
                });

            });
        </script>
    @endsection
