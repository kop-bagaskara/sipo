@extends('main.layouts.main')
@section('title')
    Data First Plan
@endsection
@section('css')
    <link href="{{ asset('new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection
@section('page-title')
    Data First Plan
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Data First Plan</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Data</a></li>
                            <li class="breadcrumb-item active">First Plan</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="" style="font-size: 15px;">
                            <table id="datatable-data-accumulation" class="table table-hover table-responsive-md"
                                style="width: 100%; font-size:14px;margin-botton:15px;">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Kode Plan</th>
                                        <th>Proses</th>
                                        <th>Jml Mesin</th>
                                        <th>Dibuat Pada</th>
                                        <th>Dibuat Oleh</th>
                                        <th>Aksi</th>
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
    @endsection
    @section('scripts')
        <script src="{{ asset('new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/buttons.flash.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/buttons.print.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/dataTables.keyTable.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/dataTables.select.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/pdfmake.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/vfs_fonts.js') }}"></script>
        <script src="{{ asset('new/assets/pages/datatables-demo.js') }}"></script>

        <script>
            $(document).ready(function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var datatable = $('#datatable-data-accumulation').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('plan-firsts.data') }}",
                        type: 'POST',
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'code_plan',
                            name: 'code_plan'
                        },
                        {
                            data: 'process',
                            name: 'process'
                        },
                        {
                            data: 'count_machine',
                            name: 'count_machine'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'created_by',
                            name: 'created_by'
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


                $('body').on('click', '.edit-data', function() {
                    var id = $(this).attr('data-id');

                    var url = "/plan-mingguan?code-ac=" + id;

                    window.open(url, '_blank');
                });


                $('body').on('click', '.edit-data', function() {

                    $('#exampleModal').modal('show');
                    $('#select-machines').css('display', 'none');
                    $('#name-machines').css('display', 'block');
                    var id = $(this).data('id');

                    var url = "/get-detail-data-downtime/" + id + '/edit';

                    $.get(url, function(data) {

                        $('#id_downtime').val(data.data.id);
                        $('#code_downtime').val(data.data.code);
                        $('#downtime_reason').val(data.data.downtime_reason);

                    })
                });

            });
        </script>
    @endsection
