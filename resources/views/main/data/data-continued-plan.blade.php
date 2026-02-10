@extends('main.layouts.main')
@section('title')
    Data Plan Harian
@endsection
@section('css')
    <link href="{{ asset('new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .bg-primary {
            background-color: #007bff !important;
            color: white;
        }

        .card-header {
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .card-header:hover {
            background-color: #f8f9fa !important;
        }

        .machine-details-content {
            background-color: #f8f9fa;
            border-radius: 0.25rem;
        }

        .card {
            border: 1px solid rgba(0, 0, 0, .125);
            box-shadow: 0 1px 3px rgba(0, 0, 0, .1);
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, .125);
        }

        .badge-primary {
            background-color: #007bff;
            color: #fff;
        }

        .btn-link {
            text-decoration: none;
            width: 100%;
            text-align: left;
        }

        .btn-link:hover {
            text-decoration: none;
        }

        .btn-link:focus {
            text-decoration: none;
            box-shadow: none;
        }

        .card-body {
            padding: 1rem;
        }

        .mdi {
            font-size: 1.1rem;
        }

        .nested-accordion .card-header {
            padding: 0.5rem 1rem;
        }

        .nested-accordion .card-body {
            padding: 0.75rem;
        }

        .hot-container {
            height: 400px;
            overflow: hidden;
            margin-top: 20px;
        }

        .plan-preview {
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
        }

        .plan-preview h5 {
            margin-bottom: 15px;
            color: #333;
        }
    </style>
@endsection
@section('page-title')
    Data Plan Harian
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Data Plan Harian</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Data</a></li>
                            <li class="breadcrumb-item active">Plan Harian</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="accordion" id="mainAccordion">
                            <!-- Data will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Change Date -->
        <div class="modal fade" id="changeDateModal" tabindex="-1" role="dialog" aria-labelledby="changeDateModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="changeDateModalLabel">Ubah Plan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="changeDateForm">
                            <input type="hidden" id="itemId" name="item_id">
                            <input type="hidden" id="currentDate" name="current_date">
                            <input type="hidden" id="currentMachine" name="current_machine">

                            {{-- <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Item</label>
                                        <input type="text" class="form-control" id="itemName" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mesin Saat Ini</label>
                                        <input type="text" class="form-control" id="currentMachineName" readonly>
                                    </div>
                                </div>
                            </div> --}}

                            {{-- <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="transferMachine" name="transfer_machine">
                                    <label class="custom-control-label" for="transferMachine">Pindah Mesin</label>
                                </div>
                            </div> --}}

                            {{-- <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal Baru</label>
                                        <input type="date" class="form-control" id="newDate" name="new_date" required>
                                    </div>
                                </div>
                                <div class="col-md-6 machine-transfer-group" style="display: none;">
                                    <div class="form-group">
                                        <label>Mesin Tujuan</label>
                                        <select class="form-control" id="newMachine" name="new_machine">
                                            <option value="">Pilih Mesin</option>
                                        </select>
                                    </div>
                                </div>
                            </div> --}}

                            {{-- <div class="form-group">
                                <label>Alasan Perubahan</label>
                                <select class="form-control" id="changeReason" name="change_reason" required>
                                    <option value="">Pilih Alasan</option>
                                    <option value="urgent">Plan Urgent</option>
                                    <option value="material">Keterlambatan Material</option>
                                    <option value="machine">Maintenance Mesin</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div> --}}
{{--
                            <div class="form-group">
                                <label>Keterangan Tambahan</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                            </div> --}}

                            <div class="plan-preview">
                                <h5>Detail Plan Terkait</h5>
                                <div id="planPreview" class="hot-container"></div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button> --}}
                        {{-- <button type="button" class="btn btn-primary" id="saveDateChange">Simpan</button> --}}
                    </div>
                </div>
            </div>
        </div>

            <!-- Modal Change Date -->
        <div class="modal fade" id="changeDateModals" tabindex="-1" role="dialog" aria-labelledby="changeDateModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeDateModalLabel">Ubah Tanggal Plan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="changeDateForm">
                        <input type="hidden" id="itemId" name="item_id">
                        <input type="hidden" id="planId" name="plan_id">
                        <input type="hidden" id="currentDate" name="current_date">
                        <input type="hidden" id="machine" name="machine">

                        <div class="form-group">
                            <label>Item</label>
                            <input type="text" class="form-control" id="itemName" readonly>
                        </div>

                        <div class="form-group">
                            <label>Mesin Saat Ini</label>
                            <input type="text" class="form-control" id="currentMachineName" readonly>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="transferMachine"
                                    name="transfer_machine">
                                <label class="custom-control-label" for="transferMachine">Pindah Mesin</label>
                            </div>
                        </div>
                        <div class="form-group machine-transfer-group" style="display: none;">
                            <label>Mesin Tujuan</label>
                            <select class="form-control" id="newMachine" name="new_machine">
                                <option value="">Pilih Mesin</option>
                            </select>
                            <div id="machinePlanPreview" class="mt-3" style="display:none;">
                                <label>Plan di Mesin Tujuan pada Tanggal Tersebut:</label>
                                <div id="hotMachinePlan" style="height:250px;"></div>
                                <button type="button" class="btn btn-info mt-2" id="previewMovePlan">Preview Pindah
                                    Plan</button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Baru</label>
                            <input type="date" class="form-control" id="newDate" name="new_date" required>
                        </div>

                        <div class="form-group">
                            <label>Alasan Perubahan</label>
                            <select class="form-control" id="changeReason" name="change_reason" required>
                                <option value="">Pilih Alasan</option>
                                <option value="urgent">Plan Urgent</option>
                                <option value="material">Keterlambatan Material</option>
                                <option value="machine">Maintenance Mesin</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Keterangan Tambahan</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveDateChange">Simpan</button>
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
        <script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js"></script>

        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                function loadData() {
                    $.ajax({
                        url: "{{ route('plan-harians.data') }}",
                        type: 'POST',
                        success: function(response) {
                            var html = '';
                            response.data.forEach(function(item, index) {
                                html += `
                                    <div class="card mb-2">
                                        <div class="card-header" id="heading-${index}">
                                            <h5 class="m-0 font-size-15">
                                                <a class="d-block pt-2 pb-2 text-dark collapsed" type="button" data-toggle="collapse"
                                                        data-target="#collapse-${index}" aria-expanded="false"
                                                        aria-controls="collapse-${index}">
                                                    <strong>Tanggal: ${item.date} </strong>
                                                    <button class="btn btn-sm btn-primary ml-2">${item.machine_count} Mesin</button>
                                                    <button class="btn btn-sm btn-info ml-2">${item.total_items} Item</button>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapse-${index}" class="collapse" aria-labelledby="heading-${index}" data-parent="#mainAccordion">
                                            <div class="card-body">
                                                ${item.machines_html}
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                            $('#mainAccordion').html(html);
                        }
                    });
                }

                loadData();

                let planPreviewTable;

                function initPlanPreview() {
                    const container = document.getElementById('planPreview');
                    planPreviewTable = new Handsontable(container, {
                        data: [],
                        columns: [
                            { data: 'date', title: 'Tanggal', readOnly: true },
                            { data: 'machine', title: 'Mesin', readOnly: true },
                            { data: 'time', title: 'Waktu', readOnly: true },
                            { data: 'item', title: 'Item', readOnly: true },
                            { data: 'qty', title: 'Qty', readOnly: true },
                            { data: 'status', title: 'Status', readOnly: true }
                        ],
                        colWidths: [100, 100, 100, 200, 80, 100],
                        height: '100%',
                        licenseKey: 'non-commercial-and-evaluation'
                    });
                }

                initPlanPreview();

                $('#transferMachine').change(function() {
                    if($(this).is(':checked')) {
                        $('.machine-transfer-group').show();
                        $('#newMachine').prop('required', true);
                    } else {
                        $('.machine-transfer-group').hide();
                        $('#newMachine').prop('required', false);
                    }
                });



                // Handle Change Date Modal
                // $(document).on('click', '.change-date', function() {
                //     var itemId = $(this).data('item-id');
                //     var currentDate = $(this).data('current-date');
                //     var itemName = $(this).data('item-name');
                //     var planId = $(this).data('plan-id');
                //     var machine = $(this).data('machine');

                //     $('#itemId').val(itemId);
                //     $('#currentDate').val(currentDate);
                //     $('#currentMachine').val(machine);
                //     $('#itemName').val(itemName);
                //     $('#planId').val(planId);
                //     $('#currentMachineName').val(machine);
                //     $('#newDate').val(currentDate);

                //     // Reset transfer checkbox
                //     $('#transferMachine').prop('checked', false);
                //     $('.machine-transfer-group').hide();
                //     $('#newMachine').prop('required', false);

                //     // Load machines
                //     loadMachines();

                //     // Load related plans
                //     loadRelatedPlans(itemId);
                // });

                $(document).on('click', '.change-dates', function() {
                    var machine = $(this).data('machine');
                    var planId = $(this).data('id-plan');
                    $('#currentMachineName').val(machine);
                    $('#planId').val(planId);

                    $('#transferMachine').prop('checked', false);
                    $('.machine-transfer-group').hide();
                    $('#newMachine').prop('required', false);
                    $('#machinePlanPreview').hide();

                    if (window.hotMachinePlan) window.hotMachinePlan.loadData([]);

                    var itemId = $(this).data('item-id');
                    var currentDate = $(this).data('current-date');
                    var itemName = $(this).data('item-name');
                    var planId = $(this).data('plan-id');
                    var machine = $(this).data('machine');

                    $('#itemId').val(itemId);
                    $('#currentDate').val(currentDate);
                    $('#currentMachine').val(machine);
                    $('#itemName').val(itemName);
                    $('#planId').val(planId);
                    $('#currentMachineName').val(machine);
                    $('#newDate').val(currentDate);

                    loadMachines();
                    loadRelatedPlans(itemId);

                    // Ambil data item yang akan dipindah dari baris tabel
                    var $row = $(this).closest('tr');

                    itemToMove = {
                        item: $('#itemName').val(),
                        qty: $row.find('td').eq(2).text(),
                        capacity: $row.find('td').eq(3).text(),
                        start: $row.find('td').eq(4).text(),
                        setup: $row.find('td').eq(5).text(),
                        istirahat: $row.find('td').eq(6).text(),
                        end: $row.find('td').eq(7).text(),
                        wo: $row.find('td').eq(8).text(),
                        so: $row.find('td').eq(9).text()
                    };
                });


                $('#send-machine-jo').off('click').on('click', function() {
                    alert('1');
                    var machine = $(this).data('machine');
                    var idPlan = $(this).data('id-plan');
                    var currentDate = $(this).data('current-date');
                    var itemName = $(this).data('item-name');

                    $.ajax({
                        url: "{{ route('send-machine-joborder.data') }}",
                        method: 'POST',
                        data: {
                            machine: machine,
                            id_plan: idPlan,
                            current_date: currentDate,
                            item_name: itemName
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Job Order berhasil dikirim'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat mengirim Job Order'
                            });
                        }
                    });
                });


                let isProcessing = false;

                function pad(n) {
                    return String(n).padStart(2, '0');
                }

                function formatDateTime(date) {
                    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`;
                }

                function parseDateTime(str) {
                    if (!str) return null;
                    const [date, time] = str.split(' ');
                    if (!date || !time) return null;
                    const [year, month, day] = date.split('-').map(Number);
                    const [hour, minute, second] = time.split(':').map(Number);
                    return new Date(year, month - 1, day, hour, minute, second);
                }

                function updateEndAndNextRows(rowIdx) {
                    const data = hotMachinePlan.getSourceData();
                    for (let i = rowIdx; i < data.length; i++) {
                        let row = data[i];
                        let startStr = row.start;
                        if (!startStr) break;

                        let qty = parseFloat(row.qty || 0);
                        let capacity = parseFloat(row.capacity || 1);
                        let setup = parseFloat(row.setup || 0) * 60; // JAM ke menit
                        let istirahat = parseFloat(row.istirahat || 0) * 60; // JAM ke menit

                        // Hitung est_hours
                        let est_hours = capacity > 0 ? qty / capacity : 0;
                        let est_minutes = est_hours * 60;

                        let startDate = parseDateTime(startStr);
                        if (!startDate) break;
                        let endDate = new Date(startDate.getTime());
                        endDate.setMinutes(endDate.getMinutes() + est_minutes + setup + istirahat);

                        row.end = formatDateTime(endDate);
                        hotMachinePlan.setDataAtCell(i, 6, row.end); // kolom end (index ke-6)

                        // Set start baris berikutnya
                        if (i + 1 < data.length) {
                            data[i + 1].start = row.end;
                            hotMachinePlan.setDataAtCell(i + 1, 3, row.end); // kolom start (index ke-3)
                        }
                    }
                }

                // Inisialisasi Handsontable untuk preview plan mesin tujuan
                let hotMachinePlan;

                function initHotMachinePlan() {
                    const container = document.getElementById('hotMachinePlan');
                    if (!hotMachinePlan) {
                        hotMachinePlan = new Handsontable(container, {
                            data: [],
                            columns: [{
                                    data: 'item',
                                    title: 'Item',
                                    readOnly: true
                                },
                                {
                                    data: 'qty',
                                    title: 'Qty',
                                    readOnly: true
                                },
                                {
                                    data: 'capacity',
                                    title: 'Capacity',
                                    readOnly: true
                                },
                                {
                                    data: 'start',
                                    title: 'Start',
                                    type: 'text'
                                },
                                {
                                    data: 'setup',
                                    title: 'Setup',
                                    type: 'numeric'
                                },
                                {
                                    data: 'istirahat',
                                    title: 'Istirahat',
                                    type: 'numeric'
                                },
                                {
                                    data: 'end',
                                    title: 'End',
                                    type: 'text',
                                    readOnly: true
                                },
                                {
                                    data: 'wo',
                                    title: 'WO',
                                    readOnly: true
                                },
                                {
                                    data: 'so',
                                    title: 'SO',
                                    readOnly: true
                                }
                            ],
                            colWidths: [200, 80, 80, 160, 80, 100, 160, 120, 120],
                            height: 200,
                            licenseKey: 'non-commercial-and-evaluation',
                            manualRowMove: true,
                            rowHeaders: true,
                            afterChange: function(changes, source) {
                                if (isProcessing) return;
                                isProcessing = true;
                                if (source === 'edit') {
                                    changes.forEach(function([row, prop, oldVal, newVal]) {
                                        if (['start', 'qty', 'capacity', 'setup', 'istirahat']
                                            .includes(prop) && oldVal !== newVal) {
                                            updateEndAndNextRows(row);
                                        }
                                    });
                                }
                                isProcessing = false;
                            }
                        });
                        window.hotMachinePlan = hotMachinePlan;
                    }
                }
                initHotMachinePlan();

                function updateDateIfNeeded(rowIdx) {
                    const data = hotMachinePlan.getSourceData();
                    const row = data[rowIdx];
                    if (row.start && row.end) {
                        const startParts = row.start.split(':');
                        const endParts = row.end.split(':');
                        if (startParts.length === 2 && endParts.length === 2) {
                            const startMinutes = parseInt(startParts[0], 10) * 60 + parseInt(startParts[1], 10);
                            const endMinutes = parseInt(endParts[0], 10) * 60 + parseInt(endParts[1], 10);
                            if (endMinutes < startMinutes) {
                                alert(
                                    'End time lebih kecil dari start time, tanggal plan seharusnya bertambah satu hari.'
                                    );
                            }
                        }
                    }
                }

                // Load machines
                function loadMachines() {
                    $.ajax({
                        url: "{{ route('master.machine-data') }}",
                        type: 'POST',
                        success: function(response) {
                            let options = '<option value="">Pilih Mesin</option>';
                            response.data.forEach(function(machine) {
                                options += `<option value="${machine.Code}">${machine.Code}</option>`;
                            });
                            $('#newMachine').html(options);
                        }
                    });
                }

                // Load related plans
                function loadRelatedPlans(codePlan) {
                    $.ajax({
                        url: "{{ route('plan-harians.data') }}",
                        type: 'POST',
                        success: function(response) {
                            let planData = [];
                            response.data.forEach(function(dateGroup) {
                                dateGroup.machines.forEach(function(machineGroup) {
                                    machineGroup.items.forEach(function(item) {
                                        if(item.code_plan === codePlan) {
                                            planData.push({
                                                date: dateGroup.date,
                                                machine: machineGroup.machine,
                                                time: item.start_time + ' - ' + item.end_time,
                                                item: item.material_name,
                                                qty: item.qty_plan,
                                                status: item.is_urgent ? 'Urgent' : 'Normal'
                                            });
                                        }
                                    });
                                });
                            });
                            planPreviewTable.loadData(planData);
                        }
                    });
                }

                // Handle machine change
                $('#newMachine').change(function() {
                    const newDate = $('#newDate').val();
                    const newMachine = $(this).val();
                    if(newDate && newMachine && $('#transferMachine').is(':checked')) {
                        loadMachinePlan(newDate, newMachine);
                    }
                });

                // Handle date change
                $('#newDate').change(function() {
                    const newDate = $(this).val();
                    const newMachine = $('#newMachine').val();
                    if(newDate && newMachine && $('#transferMachine').is(':checked')) {
                        loadMachinePlan(newDate, newMachine);
                    }
                });

                // Load machine plan
                function loadMachinePlan(date, machine) {
                    $.ajax({
                        url: "{{ route('plan-harians.data') }}",
                        type: 'POST',
                        data: { date: date },
                        success: function(response) {
                            let planData = [];
                            response.data.forEach(function(dateGroup) {
                                if(dateGroup.date === date) {
                                    dateGroup.machines.forEach(function(machineGroup) {
                                        if(machineGroup.machine === machine) {
                                            machineGroup.items.forEach(function(item) {
                                                planData.push({
                                                    date: dateGroup.date,
                                                    machine: machineGroup.machine,
                                                    time: item.start_time + ' - ' + item.end_time,
                                                    item: item.material_name,
                                                    qty: item.qty_plan,
                                                    status: item.is_urgent ? 'Urgent' : 'Normal'
                                                });
                                            });
                                        }
                                    });
                                }
                            });
                            planPreviewTable.loadData(planData);
                        }
                    });
                }


                $('#saveDateChange').off('click').on('click', function() {

                    const itemName = $('#itemName').val();
                    const tableData = hotMachinePlan.getSourceData();
                    const itemRow = tableData.find(row => row.item === itemName);


                    let latestStart = null;
                    let latestEnd = null;

                    console.log(itemRow);
                    if (itemRow) {
                        latestStart = itemRow.start;
                        latestEnd = itemRow.end;
                        quantity = itemRow.qty;
                        capacity = itemRow.capacity;
                        setup = itemRow.setup;
                        istirahat = itemRow.istirahat;
                    }

                    var formData = {
                        item_id: $('#itemId').val(),
                        plan_id: $('#planId').val(),
                        item_name: itemName,
                        current_date: $('#currentDate').val(),
                        current_machine: $('#currentMachine').val(),
                        new_date: $('#newDate').val(),
                        transfer_machine: $('#transferMachine').is(':checked'),
                        new_machine: $('#newMachine').val(),
                        change_reason: $('#changeReason').val(),
                        notes: $('#notes').val(),
                        start_time: latestStart,
                        end_time: latestEnd,
                        qty: quantity,
                        capacity: capacity,
                        setup: setup,
                        istirahat: istirahat,
                    };

                    // Log the data being sent for debugging
                    console.log('Submitting form data:', formData);
                    console.log('Found item in table:', itemRow);

                    let shiftedPlans = [];

                    $.ajax({
                        url: "{{ route('plan-harians.change-date') }}",
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            console.log('Response from server:', response);
                            let shiftedPlans = [];
                            if (response.shifted_plans) {
                                shiftedPlans = response.shifted_plans;
                            }
                            if (response.success) {
                                $('#changeDateModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Plan berhasil diubah'
                                });

                                window.location.reload();


                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    html: response.message || 'Terjadi kesalahan saat mengubah plan',
                                    showCancelButton: true,
                                    confirmButtonText: 'Geser Plan',
                                    cancelButtonText: 'Batal',
                                    customClass: {
                                        popup: 'swal2-bigger-popup',
                                        title: 'swal2-bigger-title',
                                        htmlContainer: 'swal2-bigger-html',
                                        confirmButton: 'swal2-bigger-btn',
                                        cancelButton: 'swal2-bigger-btn'
                                    },
                                    width: '50em'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        let dataToSend = {
                                            ...formData,
                                            shifted_plans: shiftedPlans
                                        };
                                        console.log('dataToSend', dataToSend);
                                        $.ajax({
                                            url: "{{ route('plan-harians.geser-plan') }}",
                                            type: 'POST',
                                            data: dataToSend,
                                            headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                            },
                                            success: function(response) {
                                                if (response.success) {
                                                    Swal.fire('Berhasil',
                                                        'Plan berhasil digeser!',
                                                        'success');
                                                    // reload data table atau lakukan aksi lain jika perlu
                                                } else {
                                                    Swal.fire('Gagal', response
                                                        .message ||
                                                        'Gagal menggeser plan',
                                                        'error');
                                                }
                                            },
                                            error: function() {
                                                Swal.fire('Gagal',
                                                    'Terjadi kesalahan saat menggeser plan',
                                                    'error');
                                            }
                                        });
                                    }
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat mengubah plan'
                            });
                        }
                    });
                });

                // Handle Mark as Urgent
                $(document).on('click', '.mark-urgent', function() {
                    var itemId = $(this).data('item-id');
                    var isUrgent = $(this).data('is-urgent');
                    var newStatus = isUrgent === '1' ? '0' : '1';
                    var statusText = newStatus === '1' ? 'urgent' : 'normal';

                    Swal.fire({
                        title: 'Konfirmasi',
                        text: `Apakah Anda yakin ingin mengubah status item menjadi ${statusText}?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Tidak'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ route('plan-harians.mark-urgent') }}",
                                type: 'POST',
                                data: {
                                    item_id: itemId,
                                    is_urgent: newStatus
                                },
                                success: function(response) {
                                    if(response.success) {
                                        loadData(); // Reload data
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: `Status item berhasil diubah menjadi ${statusText}`
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text: response.message || 'Terjadi kesalahan saat mengubah status'
                                        });
                                    }
                                },
                                error: function() {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: 'Terjadi kesalahan saat mengubah status'
                                    });
                                }
                            });
                        }
                    });
                });

                $('#previewMovePlan').click(function() {
                    if (!itemToMove) return;
                    let data = window.hotMachinePlan.getSourceData();
                    console.log('data', data);
                    // Cek apakah item sudah ada di preview, jika belum baru tambahkan
                    let alreadyExists = data.some(row => row.wo === itemToMove.wo && row.so === itemToMove.so);
                    console.log('alreadyExists', alreadyExists);
                    if (!alreadyExists) {
                        data.push(itemToMove);
                        window.hotMachinePlan.loadData(data);
                    }
                });



            });
        </script>
    @endsection
