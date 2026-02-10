@extends('main.layouts.main')
@section('title')
    Jenis Pekerjaan Prepress
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
    Jenis Pekerjaan Prepress
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Jenis Pekerjaan Prepress</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Master</a></li>
                <li class="breadcrumb-item active">Jenis Pekerjaan Prepress</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">Jenis Pekerjaan Prepress</h4>
                        </div>
                        <div class="col">
                            <div class="header-right d-flex flex-wrap justify-content-end">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#jenisPekerjaanModal">
                                    Add Jenis Pekerjaan
                                </button>
                            </div>
                        </div>
                    </div>
                    <br>

                    <div class="" style="font-size: 15px;">
                        <table id="datatable-jenis-pekerjaan-prepress" class="table table-responsive-md" style="width: 100%; font-size:15px;">
                            <thead>
                                <tr>
                                    <th style="width:5%;">No.</th>
                                    <th>Kode</th>
                                    <th>Nama Jenis</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="jenisPekerjaanModal" tabindex="-1" role="dialog" aria-labelledby="jenisPekerjaanModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form class="submitJenisPekerjaanPrepress" id="submitJenisPekerjaanPrepress">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="jenisPekerjaanModalLabel">Add Jenis Pekerjaan Prepress</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="id" id="id_jenis_pekerjaan" class="form-control" hidden>

                        <div class="form-group">
                            <label class="form-label">Kode <span class="text-danger">*</span></label>
                            <input type="text" name="kode" id="kode" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nama Jenis <span class="text-danger">*</span></label>
                            <input type="text" name="nama_jenis" id="nama_jenis" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var datatable = $('#datatable-jenis-pekerjaan-prepress').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('jenis-pekerjaan-prepress.data') }}",
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
                        data: 'nama_jenis',
                        name: 'nama_jenis'
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
                        className: 'cust-col'
                    },
                ]
            });

            var routeSubmitJenisPekerjaan = "{{ route('jenis-pekerjaan-prepress.submit') }}";

            $('#submitJenisPekerjaanPrepress').submit(function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                formData.set('is_active', $('#is_active').is(':checked') ? 1 : 0);

                $.ajax({
                    url: routeSubmitJenisPekerjaan,
                    data: formData,
                    type: "POST",
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success === false) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message,
                                showConfirmButton: true
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.success,
                                showConfirmButton: false,
                                timer: 3000
                            }).then(function() {
                                datatable.draw();
                                $('#jenisPekerjaanModal').modal('hide');
                                $('#submitJenisPekerjaanPrepress').trigger("reset");
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

            var routeEditJenisPekerjaan = "{{ route('jenis-pekerjaan-prepress.detail', ['id' => 'ID_PLACEHOLDER']) }}";

            $('body').on('click', '.edit-data', function() {
                $('#jenisPekerjaanModal').modal('show');
                var id = $(this).data('id');
                var url = routeEditJenisPekerjaan.replace('ID_PLACEHOLDER', id);

                $.get(url, function(data) {
                    if (data.success) {
                        $('#id_jenis_pekerjaan').val(data.data.id);
                        $('#kode').val(data.data.kode);
                        $('#nama_jenis').val(data.data.nama_jenis);
                        $('#keterangan').val(data.data.keterangan);
                        $('#waktu_estimasi').val(data.data.waktu_estimasi);
                        $('#job_rate').val(data.data.job_rate);
                        $('#point_job').val(data.data.point_job);
                        $('#is_active').prop('checked', data.data.is_active);

                        $('#jenisPekerjaanModalLabel').text('Edit Jenis Pekerjaan Prepress');
                    }
                });
            });

            $('#jenisPekerjaanModal').on('hidden.bs.modal', function() {
                $('#submitJenisPekerjaanPrepress').trigger("reset");
                $('#id_jenis_pekerjaan').val('');
                $('#jenisPekerjaanModalLabel').text('Add Jenis Pekerjaan Prepress');
                $('.error-message').hide();
            });

            $('body').on('click', '.delete-data', function() {
                var id = $(this).data('id');
                var url = "{{ route('jenis-pekerjaan-prepress.delete', ['id' => 'ID_PLACEHOLDER']) }}";
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
                                if (response.success) {
                                    Swal.fire(
                                        'Deleted!',
                                        response.message,
                                        'success'
                                    );
                                    datatable.draw();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message,
                                        showConfirmButton: true
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
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
