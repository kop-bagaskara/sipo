@extends('main.layouts.main')
@section('title')
    Master Machine
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
@endsection
@section('page-title')
    Master Machine
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Master Machine</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Master</a></li>
                    <li class="breadcrumb-item active">Machine</li>
                </ol>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title mb-0">Data Machine</h4>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addMachineModal">
                                <i class="mdi mdi-plus"></i> Add Machine
                            </button>
                        </div>
                        <div class="" style="font-size: 15px;">
                            <table id="datatable-machine" class="table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Unit</th>
                                        <th>Capacity</th>
                                        <th>Dept</th>
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

        <!-- Add Machine Modal -->
        <div class="modal fade" id="addMachineModal" tabindex="-1" role="dialog" aria-labelledby="addMachineModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="addMachineModalLabel">
                            <i class="mdi mdi-plus"></i> Add New Machine
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="addMachineForm">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="machine_code">Machine Code <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="machine_code" name="machine_code" required>
                                        <small class="form-text text-muted">Kode unik untuk mesin</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="machine_name">Machine Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="machine_name" name="machine_name" required>
                                        <small class="form-text text-muted">Nama lengkap mesin</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="machine_unit">Unit <span class="text-danger">*</span></label>
                                        <select class="form-control" id="machine_unit" name="machine_unit" required>
                                            <option value="">-- Pilih Unit --</option>
                                            <option value="PCS">PCS</option>
                                            <option value="LBR">LEMBAR</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="machine_capacity">Capacity Per Hour <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="machine_capacity" name="machine_capacity" min="0" step="0.01" required>
                                        <small class="form-text text-muted">Kapasitas per jam dalam unit yang dipilih</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="machine_department">Department <span class="text-danger">*</span></label>
                                        <select class="form-control" id="machine_department" name="machine_department" required>
                                            <option value="">-- Pilih Department --</option>
                                            <option value="CTK">CETAK</option>
                                            <option value="PLG">PLONG</option>
                                            <option value="FNS">FINISHING</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="machine_description">Description</label>
                                        <textarea class="form-control" id="machine_description" name="machine_description" rows="3" placeholder="Deskripsi tambahan mesin (opsional)"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="mdi mdi-close"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> Save Machine
                            </button>
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
                        url: "{{ route('master.machine-data') }}",
                        type: "POST",
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'Code',
                            name: 'Code'
                        },
                        {
                            data: 'Description',
                            name: 'Description'
                        },
                        {
                            data: 'Unit',
                            name: 'Unit'
                        },
                        {
                            data: 'CapacityPerHour',
                            name: 'CapacityPerHour'
                        },
                        {
                            data: 'Department',
                            name: 'Department'
                        },
                    ]
                });

                // Handle Add Machine Form Submission
                $('#addMachineForm').on('submit', function(e) {
                    e.preventDefault();
                    
                    // Get form data
                    var formData = {
                        machine_code: $('#machine_code').val(),
                        machine_name: $('#machine_name').val(),
                        machine_unit: $('#machine_unit').val(),
                        machine_capacity: $('#machine_capacity').val(),
                        machine_department: $('#machine_department').val(),
                        machine_description: $('#machine_description').val()
                    };

                    // Show loading state
                    var submitBtn = $(this).find('button[type="submit"]');
                    var originalText = submitBtn.html();
                    submitBtn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Saving...');

                    // Send AJAX request
                    $.ajax({
                        url: "{{ route('master.machine-store') }}",
                        type: "POST",
                        data: formData,
                        success: function(response) {
                            // Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Machine berhasil ditambahkan',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Close modal
                            $('#addMachineModal').modal('hide');
                            
                            // Reset form
                            $('#addMachineForm')[0].reset();
                            
                            // Reload DataTable
                            datatable.ajax.reload();
                        },
                        error: function(xhr) {
                            var errorMessage = 'Terjadi kesalahan saat menyimpan data';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMessage
                            });
                        },
                        complete: function() {
                            // Reset button state
                            submitBtn.prop('disabled', false).html(originalText);
                        }
                    });
                });

                // Reset form when modal is closed
                $('#addMachineModal').on('hidden.bs.modal', function() {
                    $('#addMachineForm')[0].reset();
                });

            });
        </script>
    @endsection
