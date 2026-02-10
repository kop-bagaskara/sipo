@extends('main.layouts.main')
@section('title')
    Master Series Material
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection
@section('page-title')
    Master Series Material
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Master Series Material</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                            <li class="breadcrumb-item active">Master Series Material</li>
                        </ol>
                    </div>

                </div>
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
                                    <i class="mdi mdi-plus"></i> Tambah Series Material
                                </button>
                            </div>
                        </div>
                        <div class="">
                            <table id="datatable-machine" class="w-100 table table-hover table-bordered table-responsive-md">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Series Material</th>
                                        <th>Proses</th>
                                        <th>Department</th>
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
        <!-- Modal Series Material -->
        <div class="modal fade" id="modal-series-material" tabindex="-1" role="dialog"
            aria-labelledby="modal-series-material-label" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-series-material-label">Tambah Series Material</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="form-series-material">
                        <input type="text" name="id_series_material" id="id_series_material" hidden>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>

                            <div class="form-group">
                                <label>Proses</label>
                                <select name="process" id="process" class="form-control">
                                    <option value="" disabled selected>Pilih Proses</option>
                                    @foreach ($rencana as $item)
                                        <option value="{{ $item->r_plan }}">{{ $item->r_plan }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Department</label>
                                <select name="department" id="department" class="form-control">
                                    <option value="" disabled selected>Pilih Department</option>
                                    @foreach ($rencana as $item)
                                        <option value="{{ $item->r_dept }}">{{ $item->r_dept }}</option>
                                    @endforeach
                                </select>
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
                        url: "{{ route('master.series-material-data') }}",
                        type: "POST",
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'series_material',
                            name: 'series_material'
                        },
                        {
                            data: 'proses',
                            name: 'proses'
                        },
                        {
                            data: 'department',
                            name: 'department'
                        },
                        {
                            data: 'action',
                            name: 'action'
                        },
                    ]
                });

                var routeSubmitMasterSeries = "{{ route('master.series-material-data-submit') }}";

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
                                    title: `${response.success}`,
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

                    $('#form-series-material').find('input[name="id_series_material"]').val(id);
                    $('#form-series-material').find('input[name="name"]').val(name);
                    $('#form-series-material').find('select[name="process"]').val(process);
                    $('#form-series-material').find('select[name="department"]').val(department);

                    $('#modal-series-material').modal('show');
                });

            });
        </script>
    @endsection
