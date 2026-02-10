@extends('main.layouts.main')
@section('title')
    Data Trial
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/news/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/news/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/news/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/news/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

    <style>
        .cust-col {
            max-width: 20%;
            /* sesuaikan lebar */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }
    </style>
@endsection
@section('page-title')
    Data Trial Material
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
                        {{-- <div class="row">
                            <div class="col">
                                <h4 class="card-title">Data Job Order Prepress</h4>
                            </div>
                        </div> --}}
                        <a class="btn btn-info" href="{{ route('input-pengajuan-trial') }}" style="width: 100%;">Input
                            Pengajuan Trial</a>
                        <br>
                        <br>

                        <div class="table-responsive">
                            {{-- button Laporan Pribadi --}}
                            {{-- <button class="btn btn-info" id="btn-laporan-pribadi">Laporan Pribadi</button> --}}
                            <table id="datatable-job-order-prepress" class="table table-responsive-md"
                                style="width: 100%; font-size:14px;">
                                <thead>
                                    <tr>
                                        <th style="width:5%;">No.</th>
                                        <th>Tanggal</th>
                                        <th>Deadline</th>
                                        <th>Customer</th>
                                        <th>Product</th>
                                        <th style="white-space: nowrap;">Qty Order</th>
                                        <th style="white-space: nowrap;">Job Order</th>
                                        <th>Data</th>
                                        <th>Prioritas</th>
                                        <th style="white-space: nowrap;">Status Job</th>
                                        <th style="white-space: nowrap;">Created By</th>
                                        <th style="white-space: nowrap;">Created At</th>
                                        <th style="width:10%;">Action</th>
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
                <form class="submitDivisi" id="submitDivisi">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add Divisi</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                                id="close_button_1">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="text" name="id_divisi" id="id_divisi" class="form-control" hidden>

                            <div class="form-group">
                                <label class="form-label">Divisi</label>
                                <input type="text" name="d_divisi" id="d_divisi" class="form-control" required>
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
        <script src="{{ asset('sipo_krisan/public/news/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <!-- start - This is for export functionality only -->
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
        <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
        <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
        {{-- jquery --}}

        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

        <script>
            $(document).ready(function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var datatable = $('#datatable-job-order-prepress').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('prepress.job-order.data') }}",
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
                            data: 'tanggal_job_order',
                            name: 'tanggal_job_order',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'tanggal_deadline',
                            name: 'tanggal_deadline',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'customer',
                            name: 'customer'
                        },
                        {
                            data: 'product',
                            name: 'product'
                        },
                        {
                            data: 'qty_order_estimation',
                            name: 'qty_order_estimation'
                        },
                        {
                            data: 'job_order',
                            name: 'job_order'
                        },
                        {
                            data: 'file_data',
                            name: 'file_data',
                            className: 'cust-col',
                            render: function(data, type, row) {
                                let files = [];
                                if (typeof data === 'string') {
                                    try {
                                        files = JSON.parse(data);
                                    } catch (e) {
                                        files = [];
                                    }
                                } else if (Array.isArray(data)) {
                                    files = data;
                                }
                                if (!files.length) return '-';
                                // Tampilkan sebagai list
                                return '<ul style="padding-left:18px; margin-bottom:0;">' + files.map(
                                    f => `<li>${f}</li>`).join('') + '</ul>';
                            },
                        },
                        {
                            data: 'prioritas_job',
                            name: 'prioritas_job',
                            className: 'cust-col',
                            render: function(data, type, row) {
                                console.log(data);
                                return data == 'Urgent' ?
                                    '<button type="button" class="btn btn-danger btn-sm" data-sodocno="${row.id}" data-status="Urgent">Urgent</button>' :
                                    '<button type="button" class="btn btn-success btn-sm w-100" data-sodocno="${row.id}" data-status="Normal">Normal</button>';
                            }
                        },
                        {
                            data: 'status_job',
                            name: 'status_job',
                        },
                        {
                            data: 'created_by',
                            name: 'created_by',
                            render: function(data, type, row) {
                                return `<span class="cust-col">${data}</span>`;
                            }
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            className: 'cust-col',
                            render: function(data, type, row) {
                                return moment(data).format('DD-MM-YYYY HH:mm:ss');
                            }
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'cust-col'
                        },
                    ],
                    order: [
                        [8, 'desc'],
                        [1, 'asc']
                    ]
                });


            });
        </script>
    @endsection
