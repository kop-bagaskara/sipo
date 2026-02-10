@extends('main.layouts.main')
@section('title')
    Form Plan Mingguan
@endsection
@section('css')
    <link href="{{ asset('new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tabulator-tables/dist/css/tabulator.min.css">


    <style type="text/css">
        #handsontable-container {
            width: 100%;
            height: 400px;
            overflow: hidden;
        }

        #handsontable-container-pelumasan {
            width: 100%;
            height: 100px;
            overflow: hidden;
        }

        .moved-row {
            background-color: red !important;
            color: white;
        }

        .swal2-icon {
            padding: 20px;

        }
    </style>
@endsection
@section('page-title')
    Form Plan Mingguan
@endsection
@section('body')
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                    <p>Kode Plan</p>
                    <p>Mesin Tujuan</p>
                </div>
                <div class="col">
                    <p>: <span id="h_kodeplan"></span></p>
                    <p>: <span id="h_mesinplan"></span></p>
                </div>
                <div class="col-md-3 d-flex justify-content-end">
                    <button class="btn btn-primary" id="saveAllData">Save All Data</button>
                </div>
            </div>
            <hr>
            <a class="btn btn-warning btn-sm mb-4" data-toggle="collapse" href="#collapseExample" aria-expanded="true"
                aria-controls="collapseExample">
                Jadwal Pelumasan Maintenance
            </a>
            <div class="collapse" id="collapseExample">
                <div id="handsontable-container-pelumasan"></div>
            </div>
            <div id="handsontable-container" style="margin-bottom: 20px;margin-top:20px;"></div>
            <div id="handsontable-container-machine" style="margin-bottom: 20px;"></div>
            <div id="example-table"></div>

        </div>
    </div>

    {{-- Modal Pelumasan --}}

    <div class="modal fade" id="modalPelumasan" tabindex="-1" role="dialog" aria-labelledby="modalPelumasanLabel"
        aria-modal="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pelumasan Maintenance</h5>
                    <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="datatable-data-pelumasan" class="table table-hover table-responsive-md"
                        style="width: 100%; font-size:14px;margin-botton:15px;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kode Prob</th>
                                <th>Date Prob</th>
                                <th>Mesin</th>
                                <th>Est Day</th>
                                <th>Est Hour</th>
                                <th>Est Min</th>
                                <th>Shift</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect waves-light"
                        data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Structure -->
    <div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemModalLabel">Tambah Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="row">
                    <div class="col d-flex justify-content-end" style="margin-right: 20px;">
                        <button class="btn btn-sm btn-primary" id="tambah-downtime">Tambah Downtime</button> &nbsp;
                        <button class="btn btn-sm btn-warning" id="tambah-pelumasan">Tambah Pelumasan MTC</button>
                    </div>
                </div>
                <div class="modal-body">
                    <table class="table table-responsive table-hover" style="font-size: 13px;">
                        <thead>
                            <tr>
                                <th>WO Docno</th>
                                <th>SO Docno</th>
                                <th>Name</th>
                                <th>Material Code</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Up</th>
                                <th>Delivery Date</th>
                                {{-- <th>Status</th> --}}
                            </tr>
                        </thead>
                        <tbody id="modal-body">
                            <!-- Dynamically filled rows will go here -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="downtimeModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form class="materialAddDowntime" id="materialAddDowntime">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Downtime For Planned</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning" role="alert">
                            Untuk Reason <b>Tanggal Merah</b> yang berurutan, inputkan satu form saja.
                        </div>
                        <br>
                        <div id="downtime_to_plan" data-count="1">
                            <div class="row downtime-entry mb-4">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="form-label">Downtime Reason</label>
                                        <select name="downtime_reason_1" class="form-select">
                                            <option value disabled selected>-- Select Downtime --</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="form-label">Start Downtime</label>
                                    <div class="form-group">
                                        <input type="datetime-local" class="form-control" name="newStartTime_1"
                                            id="newStartTime_1">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="form-label">End Downtime</label>
                                    <div class="form-group">
                                        <input type="datetime-local" class="form-control" name="newEndTime_1"
                                            id="newEndTime_1">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <label for="form-label">&nbsp;</label>
                                    <div class="form-group justify-content-center d-flex">
                                        <button type="button" class="btn btn-warning add-list" id="addListDiv"> Add
                                        </button>
                                    </div>
                                </div>
                                <br>
                                <div class="mt-4">
                                    <p>Tambahkan detail alasan pemberhentian waktu mesin</p>
                                    <input type="text" class="form-control" name="reason_detail_downtime"
                                        id="reason_detail_downtime" placeholder="Input Detail Reason">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="submit-data-plan-tambahan" onclick="addedDowntime()"
                            class="btn btn-primary">Save changes</button>
                        {{--
                        <button type="button" id="submit-data-plan-tambahan" onclick="addedDowntime()"
                            class="btn btn-primary">Save changes</button> --}}
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pelumasanModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form class="materialAddPelumasan" id="materialAddPelumasan">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Jadwal Pelumasan MTC</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Spinner Animasi -->
                        <div id="loading-spinner" class="spinner-border text-primary" role="status"
                            style="display: none;">
                            <span class="visually-hidden">Loading...</span>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <input type="date" name="date_pelumasan" id="date_pelumasan" class="form-control">
                            </div>
                        </div>
                        <br>
                        <table class="table table-responsive table-hover" style="font-size: 13px;">
                            <thead>
                                <tr>
                                    <th>Kode Pelumasan</th>
                                    <th>Mesin</th>
                                    <th>Tanggal</th>
                                    <th>Shift</th>
                                    <th>Keterangan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="modal-body-pelumasan">
                                <!-- Dynamically filled rows will go here -->
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="submit-data-plan-tambahan" onclick="addedPelumasan()"
                            class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tabulator-tables/dist/js/tabulator.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hyperformula/dist/hyperformula.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap 5 (if needed for specific features) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

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

            var urlParams = new URLSearchParams(window.location.search);
            var codePlan = urlParams.get("code-ac");

            $('#h_kodeplan').text(codePlan);

            $('#tambah-downtime').on('click', function() {
                $('#itemModal').modal('hide');
                $('#downtimeModal').modal('show');
            });

            $('#tambah-pelumasan').on('click', function() {
                $('#itemModal').modal('hide');
                $('#pelumasanModal').modal('show');
            });

            $('#date_pelumasan').on('change', function() {
                var date = $('#date_pelumasan').val();
                loadModalDataPelumasan(date);
            });

            // Add event listener for Save All Data button


            var urlPlan = "{{ route('data-plans-first.data', ['code' => 'ID_PLACEHOLDER']) }}";
            var url = urlPlan.replace('ID_PLACEHOLDER', codePlan);

            $.ajax({
                url: url,
                method: 'GET',
                async: false,
                success: function(response) {
                    var data = response.data;
                    var shift = response.shift;

                    $('#h_mesinplan').text(data[0].code_machine);

                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });

        });

        // Function to save all table data
    </script>


    <script>
        var urlParams = new URLSearchParams(window.location.search);
        var codePlan = urlParams.get("code-ac");

        var urlPlan = "{{ route('data-plans-first.data', ['code' => 'ID_PLACEHOLDER']) }}";
        var url = urlPlan.replace('ID_PLACEHOLDER', codePlan);

        $.ajax({
            url: url,
            method: 'GET',
            async: false,
            success: function(response) {
                var data = response.data;
                var shift = response.shift;

                function loadData(codeAc) {
                    const urlPlan = "{{ route('data-plan-first.data', ['code' => 'ID_PLACEHOLDER']) }}";
                    const url = urlPlan.replace('ID_PLACEHOLDER', codeAc);

                    $.ajax({
                        url: url,
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        success: function(data) {
                            const rows = data.data.map(item => [
                                item.code_item,
                                item.material_name,
                                item.quantity,
                                item.capacity,
                                item.quantity / item.capacity,
                                (item.quantity / item.capacity) / 24,
                                item.start_jam,
                                '',
                                '',
                                item.end_jam,
                                item.delivery_date,
                                item.up_cetak,
                                item.wo_docno,
                                item.so_docno,
                                item.quantity,
                                item.id,
                            ]);

                            hot.loadData(rows);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('AJAX Error:', textStatus, errorThrown);
                            alert(
                                'An error occurred while fetching data. Check console for details.'
                            );
                        }
                    });


                    $.ajax({
                        url: "{{ route('data.pelumasan-maintenance-tn') }}",
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        success: function(data) {
                            const rows = data.data.map(item => {
                                // Declare and calculate values outside of the array
                                const est_day = parseFloat(item.est_day) || 0;
                                const est_hour = parseFloat(item.est_hour) || 0;
                                const est_min = parseFloat(item.est_min) || 0;

                                // Total hours and days calculation
                                const totalHours = (est_day * 24) + est_hour + (est_min /
                                    60);
                                const totalDay = totalHours / 24;

                                let startTime = "";
                                let endTime = "";

                                // Shift start times based on shift_prob
                                if (item.shift_prob == 1) {
                                    const dateProb = new Date(item.date_prob);
                                    dateProb.setHours(8, 0, 0, 0); // 8 AM
                                    startTime = formatDateToString(dateProb);
                                    endTime = calculateEndTime(dateProb,
                                        totalHours); // Calculate end time
                                } else if (item.shift_prob == 2) {
                                    const dateProb = new Date(item.date_prob);
                                    dateProb.setHours(16, 0, 0, 0); // 4 PM
                                    startTime = formatDateToString(dateProb);
                                    endTime = calculateEndTime(dateProb,
                                        totalHours); // Calculate end time
                                } else if (item.shift_prob == 3) {
                                    const dateProb = new Date(item.date_prob);
                                    dateProb.setHours(0, 0, 0, 0); // Midnight (12 AM)
                                    startTime = formatDateToString(dateProb);
                                    endTime = calculateEndTime(dateProb,
                                        totalHours); // Calculate end time
                                }


                                // Return the row data as an array
                                return [
                                    item.kode_prob,
                                    'Pelumasan pada mesin ' + item.mesin,
                                    '',
                                    '',
                                    totalHours,
                                    totalDay,
                                    startTime,
                                    '',
                                    '',
                                    endTime,
                                    '',
                                    '',
                                    '',
                                    '',
                                    ''
                                ];
                            });

                            hotPlm.loadData(rows);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('AJAX Error:', textStatus, errorThrown);
                            alert(
                                'An error occurred while fetching data. Check console for details.'
                            );
                        }
                    });


                }



                function formatDateToString(date) {
                    const year = date.getFullYear();
                    const month = (date.getMonth() + 1).toString().padStart(2, '0');
                    const day = date.getDate().toString().padStart(2, '0');
                    const hours = date.getHours().toString().padStart(2, '0');
                    const minutes = date.getMinutes().toString().padStart(2, '0');
                    const seconds = date.getSeconds().toString().padStart(2, '0');

                    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                }

                function calculateEndTime(startDate, totalHours) {
                    const endDate = new Date(startDate);
                    endDate.setHours(endDate.getHours() + totalHours); // Add total hours to start date

                    return formatDateToString(endDate); // Format end time as 'YYYY-MM-DD HH:mm:ss'
                }


                var machines = data[0].code_machine.split(',');

                $('#handsontable-container-machine').empty();

                var hotInstances = {};

                let isProcessing = false;

                machines.forEach(function(machine, index) {
                    var handsontableDiv = $('<div>', {
                        id: 'handsontable-' + machine,
                        class: 'handsontable-container'
                    });


                    var label = $(`
                        <div class="row mt-4">
                            <div class="col">
                                <p>Plan untuk Mesin ${machine}</p>
                                <input type="text" id="shift_msn" name="shift_msn" style="display: none;">
                            </div>
                            <div class="col d-flex justify-content-end mb-4">
                                <button class="btn btn-primary btn-sm" id="setShiftTwoMachine_${machine}">Set Shift 2 Mesin ${machine}</button>
                                <button class="btn btn-success btn-sm ml-4" id="setShiftThreeMachine_${machine}" disabled>Set Shift 3 Mesin ${machine}</button>
                            </div>
                        </div>
                    `);


                    $('#handsontable-container-machine').append(label);
                    $('#handsontable-container-machine').append(handsontableDiv);

                    const container = document.getElementById(`handsontable-${machine}`);
                    const machineList = data[0].code_machine.split(',').map(machine => machine.trim());

                    var hotTable = new Handsontable(container, {
                        data: [],
                        colHeaders: [
                            'Code Item', 'Material Name', 'Quantity',
                            'Capacity', 'Est. Hours', 'Est. Days', 'Start Time',
                            'Setup', 'Istirahat', 'End Time', 'Delivery Date',
                            'Up Cetak', 'Work Order DocNo', 'Sales Order DocNo',
                            'Quantity Awal', 'ID'
                        ],
                        columns: [{
                                data: 0
                            }, {
                                data: 1
                            }, {
                                data: 2
                            },
                            {
                                data: 3
                            }, {
                                data: 4,
                                type: 'numeric',
                                numericFormat: {
                                    pattern: '0.000'
                                }
                            },
                            {
                                data: 5,
                                type: 'numeric',
                                numericFormat: {
                                    pattern: '0.000'
                                }
                            },
                            {
                                data: 6,
                                type: 'time',
                                timeFormat: 'HH:MM'
                            },
                            {
                                data: 7,
                                type: 'numeric'
                            }, {
                                data: 8,
                                type: 'numeric'
                            },
                            {
                                data: 9,
                                type: 'time',
                                timeFormat: 'HH:MM'
                            },
                            {
                                data: 10
                            }, {
                                data: 11
                            }, {
                                data: 12
                            }, {
                                data: 13
                            }, {
                                data: 14
                            }, {
                                data: 15
                            }
                        ],
                        contextMenu: [
                            'row_above', 'row_below', 'remove_row', 'undo',
                            'redo',
                            {
                                name: 'Salin Baris',
                                callback: function() {
                                    duplicateRowTable();
                                },
                            },
                            {
                                name: 'Set Selesai Shift 2',
                                callback: function() {
                                    setSelesaiShiftDua();
                                },
                            },
                            // ...machineList.map((machine, index) => ({
                            //     key: `move_to_machine_${index}`,
                            //     name: `Pindahkan ke ${machine}`,
                            //     callback: function() {
                            //         moveRowToAnotherMachineTwo(machine);
                            //     },
                            // }))


                        ],
                        rowHeaders: true,
                        width: '100%',
                        height: 200,
                        manualRowMove: true,
                        manualColumnMove: true,
                        allowInsertRow: true,
                        allowRemoveRow: true,
                        multiSelect: true,
                        licenseKey: 'non-commercial-and-evaluation',
                        afterChange: function(changes, source) {
                            if (isProcessing) return;

                            isProcessing = true;
                            if (source === 'edit') {
                                changes.forEach(([row, prop, oldValue, newValue]) => {

                                    if (prop === 2) {
                                        const originalQuantity = oldValue || 0;
                                        const newQuantity = newValue || 0;

                                        const isNewRow = hotTable.getDataAtCell(row,
                                                2) === null || hotTable
                                            .getDataAtCell(row, 2) === "";
                                        const quantityAwal = isNewRow ? (hotTable
                                            .getDataAtCell(row, 2) || 1) : (
                                            hotTable.getDataAtCell(row, 14) || 1
                                            );
                                        const id = hotTable.getDataAtCell(row, 15);

                                        console.log('Quantity Awal:', quantityAwal);
                                        console.log('New Quantity:', newQuantity);

                                        if (parseFloat(newQuantity) > parseFloat(
                                                quantityAwal)) {
                                            Swal.fire({
                                                title: 'Quantity Terlalu Banyak!',
                                                text: 'Quantity yang dirubah, melebihi Quantity Awal.',
                                                confirmButtonText: 'OK'
                                            });

                                            hotTable.setDataAtCell(row, 2,
                                                newQuantity);
                                            isProcessing = false;
                                            return;
                                        }

                                        let totalQuantity = 0;
                                        const data = hotTable.getData();

                                        data.forEach((rowData) => {
                                            const rowId = rowData[15];
                                            if (id == rowId) {
                                                totalQuantity += parseFloat(
                                                    rowData[2]) || 0;
                                            }
                                        });

                                        console.log('Total Quantity untuk ID ' +
                                            id + ':', totalQuantity);
                                        console.log('Quantity Awal:', quantityAwal);

                                        if (totalQuantity > parseFloat(
                                            quantityAwal)) {
                                            Swal.fire({
                                                text: 'Jumlah total quantity dengan Data yang sama sudah melebihi Quantity Awal.',
                                                confirmButtonText: 'OK'
                                            });

                                            hotTable.setDataAtCell(row, 2,
                                                originalQuantity);
                                            isProcessing = false;
                                            return;
                                        }

                                        const capacity = hotTable.getDataAtCell(row,
                                            3) || 1;
                                        const estimationHours = (newQuantity /
                                            capacity);
                                        const estimationDays = estimationHours / 24;

                                        hotTable.setDataAtCell(row, 4,
                                            estimationHours);
                                        hotTable.setDataAtCell(row, 5,
                                            estimationDays);

                                        const remainingQuantity = originalQuantity -
                                            newQuantity;
                                        if (remainingQuantity > 0) {
                                            const newRow = [
                                                hotTable.getDataAtCell(row, 0),
                                                hotTable.getDataAtCell(row, 1),
                                                remainingQuantity, // Remaining Quantity
                                                capacity, // Capacity
                                                (remainingQuantity / capacity),
                                                (remainingQuantity / capacity) /
                                                24, // Est. Days
                                                hotTable.getDataAtCell(row,
                                                    6), // Start Time
                                                '',
                                                '',
                                                '', // Setup, Istirahat, End Time
                                                hotTable.getDataAtCell(row,
                                                    10), // Delivery Date
                                                hotTable.getDataAtCell(row,
                                                    11), // Up Cetak
                                                hotTable.getDataAtCell(row,
                                                    12), // Work Order DocNo
                                                hotTable.getDataAtCell(row,
                                                    13), // Sales Order DocNo
                                            ];

                                            const currentData = hotTable.getData();
                                            const rowExists = currentData.some(
                                                existingRow => existingRow[
                                                    0] === newRow[0] &&
                                                existingRow[
                                                    1] === newRow[1]
                                            );

                                            if (!rowExists) {
                                                currentData.splice(row + 1, 0,
                                                    newRow
                                                ); // Menambah baris baru setelah baris yang diubah
                                                hotTable.loadData(
                                                    currentData); // Muat ulang data
                                                hotTable
                                                    .render(); // Render ulang tabel
                                            }
                                        }
                                    }

                                    if (prop === 7 || prop === 8 || prop === 6 ||
                                        prop === 4) {
                                        const setup = hotTable.getDataAtCell(row,
                                            7) || 0;
                                        const additionalTime = hotTable
                                            .getDataAtCell(row, 8) || 0;
                                        const startTime = hotTable.getDataAtCell(
                                            row, 6);
                                        const estimationHours = hotTable
                                            .getDataAtCell(row, 4) || 0;

                                        let [startDate, startTimeStr] = startTime
                                            .split(' ');
                                        let [startHour, startMinute] = startTimeStr
                                            .split(':').map(Number);

                                        const totalHoursFromEstimation = Math.floor(
                                            estimationHours);
                                        const totalMinutesFromEstimation = Math
                                            .floor((estimationHours -
                                                totalHoursFromEstimation) * 60);
                                        const totalSecondsFromEstimation = Math
                                            .floor(((estimationHours -
                                                        totalHoursFromEstimation) *
                                                    60 - totalMinutesFromEstimation
                                                    ) * 60);

                                        let totalHour = startHour +
                                            totalHoursFromEstimation + setup +
                                            additionalTime;
                                        let totalMinute = startMinute +
                                            totalMinutesFromEstimation;
                                        let totalSecond =
                                        totalSecondsFromEstimation;

                                        if (totalMinute >= 60) {
                                            totalHour += Math.floor(totalMinute /
                                                60);
                                            totalMinute = totalMinute % 60;
                                        }

                                        if (totalSecond >= 60) {
                                            totalMinute += Math.floor(totalSecond /
                                                60);
                                            totalSecond = totalSecond % 60;
                                        }

                                        while (totalHour >= 24) {
                                            totalHour -= 24;
                                            let date = new Date(startDate);
                                            date.setDate(date.getDate() + 1);
                                            startDate = date.toISOString().split(
                                                'T')[0];
                                        }

                                        const updatedEndTime =
                                            `${startDate} ${String(totalHour).padStart(2, '0')}:${String(totalMinute).padStart(2, '0')}:${String(totalSecond).padStart(2, '0')}`;

                                        hotTable.setDataAtCell(row, 9,
                                            updatedEndTime);

                                        if (row + 1 < hotTable.countRows()) {
                                            const nextRowStartTime = updatedEndTime;
                                            hotTable.setDataAtCell(row + 1, 6,
                                                nextRowStartTime);
                                            let [nextRowDate, nextRowStartTimeStr] =
                                            nextRowStartTime.split(' ');
                                            let [nextRowStartHour,
                                                nextRowStartMinute
                                            ] = nextRowStartTimeStr.split(':').map(
                                                Number);
                                            let nextRowEndTime = new Date(
                                                nextRowDate);
                                            nextRowEndTime.setHours(
                                                nextRowStartHour +
                                                totalHoursFromEstimation);
                                            hotTable.setDataAtCell(row + 1, 9,
                                                nextRowEndTime
                                                .toISOString().split('T')[
                                                    0] + ' ' + String(
                                                    nextRowEndTime
                                                    .getHours()).padStart(2,
                                                    '0') +
                                                ':' + String(nextRowEndTime
                                                    .getMinutes())
                                                .padStart(2, '0') +
                                                ':00');
                                        }
                                    }


                                });
                            }
                            isProcessing = false;
                        }
                    });

                    function formatDateTime(date) {
                        const year = date.getFullYear();
                        const month = (date.getMonth() + 1).toString().padStart(2, '0');
                        const day = date.getDate().toString().padStart(2, '0');
                        const hours = date.getHours().toString().padStart(2, '0');
                        const minutes = date.getMinutes().toString().padStart(2, '0');
                        const seconds = date.getSeconds().toString().padStart(2, '0');

                        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                    };

                    function setSelesaiShiftDua() {
                        // Dapatkan baris yang dipilih
                        const selected = hotTable.getSelected();

                        if (!selected || selected.length === 0) {
                            Swal.fire({
                                title: 'Peringatan!',
                                text: 'Silakan pilih baris terlebih dahulu.',
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }

                        // Ambil indeks baris yang dipilih
                        const selectedRow = selected[0][0]; // [startRow, startCol, endRow, endCol]

                        // Dapatkan data baris yang dipilih
                        const rowData = hotTable.getDataAtRow(selectedRow);

                        // Cek apakah End Time melebihi jam 23:00
                        const endTimeStr = rowData[9]; // End Time ada di kolom 9
                        const startTimeStr = rowData[6]; // Start Time ada di kolom 6

                        // Parse tanggal dan waktu
                        const [endDate, endTime] = endTimeStr.split(' ');
                        const [startDate, startTime] = startTimeStr.split(' ');

                        // Buat objek Date untuk perbandingan waktu yang lebih akurat
                        const endDateTime = new Date(`${endDate}T${endTime}`);
                        const startDateTime = new Date(`${startDate}T${startTime}`);

                        // Tetapkan cutoff jam 23:00 di tanggal Start Time
                        const cutoffTime = new Date(startDateTime);
                        cutoffTime.setHours(23, 0, 0, 0);

                        // Jika End Time <= jam 23:00, tidak perlu potong
                        if (endDateTime <= cutoffTime) {
                            Swal.fire({
                                title: 'Informasi',
                                text: 'Baris ini tidak perlu dipotong karena End Time tidak melebihi jam 23:00',
                                icon: 'info',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }

                        // Tampilkan pesan untuk konfirmasi
                        Swal.fire({
                            title: 'Potong Baris ' + (selectedRow + 1),
                            html: 'End Time saat ini: <b>' + endTimeStr +
                                '</b><br>Baris ini akan dipotong di jam 23:00',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Potong Baris',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {

                                const setup = parseFloat(rowData[7]) || 0;
                                const additionalTime = parseFloat(rowData[8]) || 0;
                                const timeDiffInHours = (cutoffTime - startDateTime) / (1000 *
                                    60 * 60);
                                const hoursUntilCutoff = timeDiffInHours - setup -
                                    additionalTime;

                                const capacity = parseFloat(rowData[3]);
                                const totalQty = parseFloat(rowData[2]);
                                const qtyUntilCutoff = Math.floor(hoursUntilCutoff * capacity);
                                const remainingQty = totalQty - qtyUntilCutoff;

                                hotTable.setDataAtCell(selectedRow, 2, qtyUntilCutoff);
                                hotTable.setDataAtCell(selectedRow, 4, hoursUntilCutoff);
                                hotTable.setDataAtCell(selectedRow, 5, hoursUntilCutoff / 24);
                                hotTable.setDataAtCell(selectedRow, 9, `${startDate} 23:00:00`);


                                // LANGKAH 5: Tangani sisa quantity
                                const nextRow = selectedRow + 1;
                                const totalRows = hotTable.countRows();

                                function addTimeToDate(baseDate, hoursToAdd) {
                                    const hours = Math.floor(hoursToAdd);
                                    const minutes = Math.round((hoursToAdd - hours) * 60);

                                    const result = new Date(baseDate);
                                    let totalMinutes = result.getMinutes() + minutes;
                                    const additionalHours = Math.floor(totalMinutes / 60);
                                    totalMinutes = totalMinutes % 60;

                                    const totalHours = result.getHours() + hours +
                                        additionalHours;
                                    result.setHours(totalHours, totalMinutes, 0);
                                    return result;
                                }

                                // Jika masih ada baris berikutnya
                                if (nextRow < totalRows) {
                                    const nextRowData = hotTable.getDataAtRow(nextRow);

                                    // LANGKAH 6: Cek apakah item pada baris berikutnya sama (berdasarkan ID di kolom 15)
                                    const currentItemId = rowData[15];
                                    const nextItemId = nextRowData[15];

                                    // Jika item sama, tambahkan sisa quantity ke baris berikutnya
                                    if (currentItemId === nextItemId) {
                                        // Baris berikutnya mulai jam 08:00 hari berikutnya
                                        const nextDay = new Date(cutoffTime);
                                        nextDay.setDate(nextDay.getDate() + 1);
                                        nextDay.setHours(8, 0, 0, 0);

                                        // Update quantity dan jam estimasi baris berikutnya
                                        const nextQty = parseFloat(nextRowData[2]) +
                                            remainingQty;
                                        const nextEstHours = nextQty / capacity;

                                        // Update baris berikutnya
                                        hotTable.setDataAtCell(nextRow, 2, nextQty); // Quantity
                                        hotTable.setDataAtCell(nextRow, 4,
                                        nextEstHours); // Est. Hours
                                        hotTable.setDataAtCell(nextRow, 5, nextEstHours /
                                        24); // Est. Days
                                        hotTable.setDataAtCell(nextRow, 6,
                                            `${nextDay.toISOString().split('T')[0]} 08:00:00`
                                            ); // Start Time

                                        // Hitung End Time baru
                                        const nextEndTime = addTimeToDate(nextDay,
                                        nextEstHours);
                                        hotTable.setDataAtCell(nextRow, 9,
                                            `${nextEndTime.toISOString().split('T')[0]} ${String(nextEndTime.getHours()).padStart(2, '0')}:${String(nextEndTime.getMinutes()).padStart(2, '0')}:00`
                                        );

                                        Swal.fire({
                                            title: 'Berhasil!',
                                            text: 'Baris dipotong di jam 23:00 dan sisa quantity ditambahkan ke baris berikutnya.',
                                            icon: 'success',
                                            confirmButtonText: 'OK'
                                        });
                                    } else {
                                        // Item berbeda, buat baris baru
                                        createNewRow(remainingQty, capacity, rowData,
                                            cutoffTime, nextRow, selectedRow);
                                    }
                                } else {
                                    // Tidak ada baris berikutnya, buat baris baru
                                    createNewRow(remainingQty, capacity, rowData, cutoffTime,
                                        nextRow, selectedRow);
                                }

                                // Fungsi untuk membuat baris baru
                                function createNewRow(remainingQty, capacity, rowData,
                                    cutoffTime, nextRow, selectedRow) {
                                    // Baris baru mulai jam 08:00 hari berikutnya
                                    const nextDay = new Date(cutoffTime);
                                    nextDay.setDate(nextDay.getDate() + 1);
                                    nextDay.setHours(8, 0, 0, 0);

                                    // Hitung jam estimasi untuk sisa quantity
                                    const newRowEstHours = remainingQty / capacity;

                                    // Hitung End Time untuk baris baru
                                    const newRowEndTime = addTimeToDate(nextDay,
                                    newRowEstHours);

                                    // Data untuk baris baru (salin dari baris saat ini)
                                    const newRowData = [...rowData];
                                    newRowData[2] = remainingQty; // Quantity
                                    newRowData[4] = newRowEstHours; // Est. Hours
                                    newRowData[5] = newRowEstHours / 24; // Est. Days
                                    newRowData[6] =
                                        `${nextDay.toISOString().split('T')[0]} 08:00:00`; // Start Time
                                    newRowData[9] =
                                        `${newRowEndTime.toISOString().split('T')[0]} ${String(newRowEndTime.getHours()).padStart(2, '0')}:${String(newRowEndTime.getMinutes()).padStart(2, '0')}:00`; // End Time

                                    // Sisipkan baris baru
                                    if (nextRow < hotTable.countRows()) {
                                        // Sisipkan di antara baris saat ini dan baris berikutnya
                                        const allData = hotTable.getData();
                                        allData.splice(nextRow, 0, newRowData);
                                        hotTable.loadData(allData);
                                    } else {
                                        // Tambahkan di akhir tabel
                                        hotTable.alter('insert_row', nextRow);

                                        // Update semua sel di baris baru
                                        for (let col = 0; col < newRowData.length; col++) {
                                            hotTable.setDataAtCell(nextRow, col, newRowData[
                                                col]);
                                        }
                                    }

                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: 'Baris dipotong di jam 23:00 dan sisa quantity dipindahkan ke baris baru.',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            }
                        });
                    }

                    // SET untuk shift 2 mesin tertentu
                    $(`#setShiftTwoMachine_${machine}`).on('click', function() {
                        $('#shift_msn').val(2);

                        function addTimeToDate(baseDate, hoursToAdd) {
                            let hours = Math.floor(hoursToAdd);
                            let minutes = Math.round((hoursToAdd - hours) * 60);

                            let result = new Date(baseDate);
                            let totalMinutes = result.getMinutes() + minutes;
                            let additionalHours = Math.floor(totalMinutes / 60);
                            minutes = totalMinutes % 60;

                            let totalHours = result.getHours() + hours + additionalHours;
                            result.setHours(totalHours, minutes, 0);
                            return result;
                        }

                        // Fungsi untuk normalisasi data - selalu mulai dari jam 08:00
                        function normalizeStartTimes(inputData) {
                            let outputData = [];
                            let lastEndTime = null;
                            let nextRow = null;

                            for (let i = 0; i < inputData.length; i++) {
                                let row = [...inputData[i]];

                                // Jika ini baris pertama atau tidak ada end time sebelumnya
                                if (lastEndTime === null) {
                                    // Pastikan start time pertama selalu jam 08:00
                                    let startDate = new Date(row[6]);
                                    startDate.setHours(8, 0, 0, 0);
                                    row[6] = startDate.toISOString().split('T')[0] +
                                    ' 08:00:00';
                                } else {
                                    // Gunakan end time dari baris sebelumnya sebagai start time
                                    // Tapi pastikan selalu jam 08:00 jika hari baru
                                    let prevEndDate = new Date(lastEndTime);
                                    let startDate = new Date(row[6]);

                                    // Jika tanggal berbeda, selalu mulai jam 08:00
                                    if (prevEndDate.getDate() !== startDate.getDate() ||
                                        prevEndDate.getMonth() !== startDate.getMonth() ||
                                        prevEndDate.getFullYear() !== startDate.getFullYear()) {
                                        startDate = new Date(prevEndDate);
                                        startDate.setDate(startDate.getDate() + 1);
                                        startDate.setHours(8, 0, 0, 0);
                                    } else {
                                        // Tanggal sama, gunakan jam end time sebelumnya
                                        startDate = prevEndDate;
                                    }

                                    row[6] = startDate.toISOString().split('T')[0] + ' ' +
                                        String(startDate.getHours()).padStart(2, '0') + ':' +
                                        String(startDate.getMinutes()).padStart(2, '0') + ':00';
                                }

                                // Hitung end time baru berdasarkan start time yang telah dinormalisasi
                                let startDate = new Date(row[6]);
                                let capacity = parseFloat(row[3]);
                                let quantity = parseFloat(row[2]);
                                let estHours = quantity / capacity;
                                let realEndDate = addTimeToDate(startDate, estHours);

                                row[9] = realEndDate.toISOString().split('T')[0] + ' ' +
                                    String(realEndDate.getHours()).padStart(2, '0') + ':' +
                                    String(realEndDate.getMinutes()).padStart(2, '0') + ':00';

                                outputData.push(row);
                                lastEndTime = row[9];
                            }

                            return outputData;
                        }

                        // Fungsi untuk memproses data dan memotong baris yang melebihi jam 23:00
                        function processAndSplitData(inputData) {
                            var outputData = [];
                            var lastEndTime = null;

                            for (let i = 0; i < inputData.length; i++) {
                                let row = inputData[i];
                                let startTime = row[6];

                                if (lastEndTime) {
                                    startTime = lastEndTime;

                                    // Pastikan start time selalu 08:00 jika hari baru
                                    let lastEndDate = new Date(lastEndTime);
                                    let startDate = new Date(startTime);
                                    if (startDate.getDate() > lastEndDate.getDate() ||
                                        startDate.getMonth() > lastEndDate.getMonth() ||
                                        startDate.getFullYear() > lastEndDate.getFullYear()) {
                                        startDate.setHours(8, 0, 0, 0);
                                        startTime = startDate.toISOString().split('T')[0] +
                                            ' 08:00:00';
                                    }
                                }

                                // Pastikan jam start time selalu valid (antara 08:00 - 23:00)
                                let startDate = new Date(startTime);
                                if (startDate.getHours() < 8) {
                                    startDate.setHours(8, 0, 0, 0);
                                    startTime = startDate.toISOString().split('T')[0] +
                                        ' 08:00:00';
                                }

                                // Hitung end time
                                let capacity = parseFloat(row[3]);
                                let quantity = parseFloat(row[2]);
                                let estHours = quantity / capacity;
                                let realEndDate = addTimeToDate(startDate, estHours);

                                // Buat objek jam 23:00 pada tanggal yang sama dengan start time
                                let cutoffTime = new Date(startDate);
                                cutoffTime.setHours(23, 0, 0, 0);

                                // Jika end time > 23:00, potong baris
                                if (realEndDate > cutoffTime) {
                                    // Hitung jam hingga jam 23:00
                                    let hoursUntilCutoff = (cutoffTime - startDate) / (1000 *
                                        60 * 60);

                                    if (hoursUntilCutoff <= 0) {
                                        // Jika start time sudah terlalu dekat dengan/setelah cutoff, langsung mulai di hari berikutnya
                                        let nextDay = new Date(startDate);
                                        nextDay.setDate(nextDay.getDate() + 1);
                                        nextDay.setHours(8, 0, 0, 0);

                                        let currentRow = [...row];
                                        currentRow[6] = nextDay.toISOString().split('T')[0] +
                                            ' 08:00:00';
                                        let newEndDate = addTimeToDate(nextDay, estHours);
                                        currentRow[9] = newEndDate.toISOString().split('T')[0] +
                                            ' ' +
                                            String(newEndDate.getHours()).padStart(2, '0') +
                                            ':' +
                                            String(newEndDate.getMinutes()).padStart(2, '0') +
                                            ':00';

                                        outputData.push(currentRow);
                                        lastEndTime = currentRow[9];
                                    } else {
                                        // Hitung quantity yang bisa diproduksi sampai jam 23:00
                                        let qtyUntilCutoff = Math.floor(hoursUntilCutoff *
                                            capacity);
                                        if (qtyUntilCutoff <= 0) qtyUntilCutoff =
                                        1; // Minimal 1

                                        // Hitung quantity yang tersisa
                                        let remainingQty = quantity - qtyUntilCutoff;

                                        // Baris pertama (sampai jam 23:00)
                                        let firstRow = [...row];
                                        firstRow[2] = qtyUntilCutoff;
                                        firstRow[4] = hoursUntilCutoff;
                                        firstRow[5] = hoursUntilCutoff / 24;
                                        firstRow[6] = startTime;
                                        firstRow[9] = cutoffTime.toISOString().split('T')[0] +
                                            ' 23:00:00';

                                        outputData.push(firstRow);
                                        lastEndTime = firstRow[9];

                                        // Jika ada quantity yang tersisa, buat baris kedua
                                        if (remainingQty > 0) {
                                            // Mulai di jam 08:00 hari berikutnya
                                            let nextDay = new Date(cutoffTime);
                                            nextDay.setDate(nextDay.getDate() + 1);
                                            nextDay.setHours(8, 0, 0, 0);

                                            let secondRow = [...row];
                                            secondRow[2] = remainingQty;
                                            secondRow[4] = remainingQty / capacity;
                                            secondRow[5] = (remainingQty / capacity) / 24;
                                            secondRow[6] = nextDay.toISOString().split('T')[0] +
                                                ' 08:00:00';

                                            let endTimeForSecond = addTimeToDate(nextDay,
                                                remainingQty / capacity);
                                            secondRow[9] = endTimeForSecond.toISOString().split(
                                                    'T')[0] + ' ' +
                                                String(endTimeForSecond.getHours()).padStart(2,
                                                    '0') + ':' +
                                                String(endTimeForSecond.getMinutes()).padStart(
                                                    2, '0') + ':00';

                                            outputData.push(secondRow);
                                            lastEndTime = secondRow[9];
                                        }
                                    }
                                } else {
                                    // End time tidak melebihi jam 23:00, jadi tidak perlu dipotong
                                    let currentRow = [...row];
                                    currentRow[6] = startTime;
                                    currentRow[9] = realEndDate.toISOString().split('T')[0] +
                                        ' ' +
                                        String(realEndDate.getHours()).padStart(2, '0') + ':' +
                                        String(realEndDate.getMinutes()).padStart(2, '0') +
                                        ':00';

                                    outputData.push(currentRow);
                                    lastEndTime = currentRow[9];
                                }
                            }

                            return outputData;
                        }

                        // Ambil data dari tabel
                        var originalData = hotTable.getData();

                        // Langkah 1: Normalisasi start time (pastikan selalu 08:00)
                        var normalizedData = normalizeStartTimes(originalData);

                        // Langkah 2: Proses dan potong baris yang melebihi jam 23:00
                        var firstPassData = processAndSplitData(normalizedData);

                        // Langkah 3: Normalisasi lagi untuk memastikan semua start time benar
                        var normAgainData = normalizeStartTimes(firstPassData);

                        // Langkah 4: Proses sekali lagi untuk memastikan tidak ada baris yang terlewat
                        var finalData = processAndSplitData(normAgainData);

                        // Perbarui tabel dengan data yang sudah diproses
                        hotTable.loadData(finalData);
                        hotTable.render();
                    });

                    $(`#setShiftThreeMachine_${machine}`).on('click', function() {

                        $(`#setShiftTwoMachine_${machine}`).removeAttr('disabled');
                        $(`#setShiftThreeMachine_${machine}`).attr('disabled', 'true');
                        // var data = hotTable.getData();

                        // data.forEach(function (row) {
                        //     row[6] = '08:00';
                        //     row[9] = '12:00';
                        // });

                        // hotTable.loadData(data);
                    });

                    window.hotTable = window.hotTable || {};
                    window.hotTable[machine] = hotTable;

                    function duplicateRowTable() {
                        const selected = hotTable.getSelected();
                        if (!selected || selected.length === 0) {
                            alert("Please select a row first.");
                            return;
                        }
                        const selectedRow = selected[0][0];
                        const rowData = [...hotTable.getDataAtRow(
                        selectedRow)]; // Create a copy of the row data

                        // Set quantity to zero/empty in the duplicated row
                        rowData[2] = 0; // Set quantity (column 2) to zero

                        // Recalculate Est. Hours and Est. Days based on zero quantity
                        rowData[4] = 0; // Est. Hours
                        rowData[5] = 0; // Est. Days

                        // Clear the ID (column 15) since this is a new row
                        // rowData[15] = null;

                        const currentData = hotTable.getData();
                        currentData.splice(selectedRow + 1, 0, rowData);
                        hotTable.loadData(currentData);
                        hotTable.render();
                    }

                    function moveRowToAnotherMachineTwo(machine) {
                        const selected = hotTable.getSelected();


                        if (!selected || selected.length === 0) {
                            alert("Please select a row to move.");
                            return;
                        }

                        const selectedRange = selected[0];
                        const selectedRow = selectedRange ? selectedRange[0] : null;

                        if (selectedRow === null) {
                            alert("Please select a valid row.");
                            return;
                        }

                        const rowData = hotTable.getDataAtRow(selectedRow);
                        console.log("Row data selected:", rowData);

                        if (!rowData) {
                            alert("Error retrieving the row data.");
                            return;
                        }

                        const targetTable = window.hotTable[machine];

                        if (targetTable) {
                            const targetData = targetTable.getData();
                            const isRowAlreadyPresent = targetData.some(row => {
                                return row.every((cell, index) => {
                                    return cell === rowData[index];
                                });
                            });

                            if (isRowAlreadyPresent) {
                                alert("This row already exists in the target table.");
                                return;
                            }

                            const targetDataBefore = targetTable.getData();
                            const rowId = selectedRow;

                            for (let colIndex = 0; colIndex < hotTable.countCols(); colIndex++) {
                                const existingClass = hotTable.getCellMeta(selectedRow, colIndex)
                                    .className || '';
                                const newClass = `${existingClass} moved-row rows-${rowId}`;

                                hotTable.setCellMeta(selectedRow, colIndex, 'className', newClass);

                                const cell = hotTable.getCell(selectedRow, colIndex);
                                if (cell) {
                                    cell.classList.add('moved-row', `rows-${rowId}`);
                                }
                            }

                            hotTable.render();

                            for (let colIndex = 0; colIndex < hotTable.countCols(); colIndex++) {
                                const newClass = `moved-row rows-${rowId}`;

                                targetTable.setCellMeta(targetDataBefore.length, colIndex, 'className',
                                    newClass);

                                const targetCell = targetTable.getCell(targetDataBefore.length,
                                    colIndex);
                                if (targetCell) {
                                    targetCell.classList.add('moved-row', `rows-${rowId}`);
                                }
                            }

                            // Add the data to the target table
                            targetDataBefore.push(rowData);
                            targetTable.loadData(targetDataBefore);
                            targetTable.render();

                            hotTable.alter('remove_row', selectedRow);

                            console.log("Data after update:", targetTable.getData());
                        } else {
                            alert("Target machine table not found.");
                        }
                    }


                    function parseTimeToDate(time) {
                        let [hours, minutes] = time.split(':');
                        let date = new Date();
                        date.setHours(hours, minutes, 0, 0);
                        return date;
                    }

                    // function calculateEndTimeForSecondSegment(startTime, estHours) {
                    //     let startDate = parseTimeToDate(startTime);
                    //     let endDate = new Date(startDate);
                    //     endDate.setHours(endDate.getHours() + estHours);
                    //     return `${endDate.getHours()}:${String(endDate.getMinutes()).padStart(2, '0')}`;
                    // }

                });





                var urlParams = new URLSearchParams(window.location.search);
                var codePlan = urlParams.get("code-ac");

                loadData(codePlan);

                // const hotInstances = [];


                const machineList = data[0].code_machine.split(',').map(machine => machine.trim());
                const hotContainer = document.getElementById('handsontable-container');
                const hot = new Handsontable(hotContainer, {
                    data: [],
                    // colWidths: [100],
                    colHeaders: [
                        'Code Item', 'Material Name', 'Quantity', 'Capacity',
                        'Est. Hours', 'Est. Days', 'Start Time',
                        'Setup', 'Istirahat', 'End Time', 'Delivery Date', 'Up Cetak',
                        'Work Order DocNo', 'Sales Order DocNo', 'Quantity Awal', 'ID'
                    ],
                    rowHeaders: true,
                    multiSelect: true,
                    columns: [{
                            data: 0,
                            readOnly: true,
                            width: 120
                        }, {
                            data: 1,
                            readOnly: true,
                        }, {
                            data: 2,
                            readOnly: true,
                        },
                        {
                            data: 3,
                            readOnly: true,
                        },
                        {
                            data: 4,
                            readOnly: true,
                            type: 'numeric',
                            numericFormat: {
                                pattern: '0.000'
                            }
                        },
                        {
                            data: 5,
                            readOnly: true,
                            type: 'numeric',
                            numericFormat: {
                                pattern: '0.000'
                            }
                        },
                        {
                            data: 6,
                            // readOnly: true,
                            type: 'time',
                            timeFormat: 'HH:MM'
                        },
                        {
                            data: 7,
                            type: 'numeric'
                        },
                        {
                            data: 8,
                            type: 'numeric'
                        },
                        {
                            data: 9,
                            type: 'time',
                            // readOnly: true,
                            timeFormat: 'HH:MM'
                        },
                        {
                            data: 10,
                            readOnly: true,
                        },
                        {
                            data: 11,
                            readOnly: true,
                        },
                        {
                            data: 12,
                            readOnly: true,
                        },
                        {
                            data: 13,
                            readOnly: true,
                        },
                        {
                            data: 14,
                            readOnly: true,
                        },
                        {
                            data: 15,
                            readOnly: true,
                        },
                    ],
                    contextMenu: [
                        'undo', 'redo',
                        ...machineList.map((machine, index) => ({
                            key: `move_to_machine_${index}`,
                            name: `Pindahkan ke ${machine}`,
                            callback: function() {
                                moveRowToAnotherMachine(machine);
                            },
                        }))








                    ],
                    licenseKey: 'non-commercial-and-evaluation',
                    afterChange: function(changes, source) {
                        if (source === 'edit') {
                            changes.forEach(([row, prop, oldValue, newValue]) => {
                                if (prop === 2) {
                                    const originalQuantity = oldValue || 0;
                                    const newQuantity = newValue || 0;
                                    const isNewRow = hot.getDataAtCell(row, 2) === null ||
                                        hot.getDataAtCell(
                                            row, 2) === "";
                                    const quantityAwal = isNewRow ? hot.getDataAtCell(row,
                                            2) || 1 : hot
                                        .getDataAtCell(row, 14) || 1;

                                    if (newQuantity > quantityAwal) {
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Quantity tidak sinkron!',
                                            text: 'Sesuaikan dengan quantitiy yang benar.',
                                            confirmButtonText: 'OK'
                                        });

                                        hot.setDataAtCell(row, 2, originalQuantity);

                                        return;
                                    }

                                    const capacity = hot.getDataAtCell(row, 3) || 1;
                                    const estimationHours = (newQuantity / capacity);
                                    const estimationDays = estimationHours / 24;

                                    hot.setDataAtCell(row, 4, estimationHours);
                                    hot.setDataAtCell(row, 5, estimationDays);

                                    const remainingQuantity = originalQuantity -
                                        newQuantity;

                                    if (remainingQuantity > 0) {
                                        const newRow = [
                                            hot.getDataAtCell(row, 0),
                                            hot.getDataAtCell(row, 1),
                                            remainingQuantity,
                                            capacity,
                                            (remainingQuantity / capacity),
                                            (remainingQuantity / capacity) / 24,
                                            hot.getDataAtCell(row, 6),
                                            '',
                                            '',
                                            '',
                                            hot.getDataAtCell(row, 10),
                                            hot.getDataAtCell(row, 11),
                                            hot.getDataAtCell(row, 12),
                                            hot.getDataAtCell(row, 13),
                                        ];

                                        const currentData = hot.getData();

                                        const rowExists = currentData.some(existingRow =>
                                            existingRow[0] ===
                                            newRow[0] && existingRow[1] === newRow[1]);

                                        if (!rowExists) {
                                            currentData.splice(row + 1, 0, newRow);
                                            hot.loadData(currentData);
                                            hot.render();
                                        }
                                    }

                                }

                                if (prop === 7 || prop === 8 || prop === 6 || prop === 4) {
                                    const setup = hot.getDataAtCell(row, 7) || 0;
                                    const additionalTime = hot.getDataAtCell(row, 8) || 0;
                                    const startTime = hot.getDataAtCell(row, 6);
                                    const estimationHours = hot.getDataAtCell(row, 4) || 0;

                                    let [startDate, startTimeStr] = startTime.split(' ');
                                    let [startHour, startMinute] = startTimeStr.split(':')
                                        .map(Number);

                                    const totalHoursFromEstimation = Math.floor(
                                        estimationHours);
                                    const totalMinutesFromEstimation = Math.floor((
                                        estimationHours -
                                        totalHoursFromEstimation) * 60);
                                    const totalSecondsFromEstimation = Math.floor(((
                                                estimationHours -
                                                totalHoursFromEstimation) * 60 -
                                            totalMinutesFromEstimation) *
                                        60);

                                    let totalHour = startHour + totalHoursFromEstimation +
                                        setup +
                                        additionalTime;
                                    let totalMinute = startMinute +
                                        totalMinutesFromEstimation;
                                    let totalSecond = totalSecondsFromEstimation;

                                    if (totalMinute >= 60) {
                                        totalHour += Math.floor(totalMinute / 60);
                                        totalMinute = totalMinute % 60;
                                    }

                                    if (totalSecond >= 60) {
                                        totalMinute += Math.floor(totalSecond / 60);
                                        totalSecond = totalSecond % 60;
                                    }

                                    while (totalHour >= 24) {
                                        totalHour -= 24;
                                        let date = new Date(startDate);
                                        date.setDate(date.getDate() + 1);
                                        startDate = date.toISOString().split('T')[0];
                                    }

                                    const updatedEndTime =
                                        `${startDate} ${String(totalHour).padStart(2, '0')}:${String(totalMinute).padStart(2, '0')}:${String(totalSecond).padStart(2, '0')}`;

                                    hot.setDataAtCell(row, 9, updatedEndTime);

                                    if (row + 1 < hot.countRows()) {
                                        const nextRowStartTime = updatedEndTime;
                                        hot.setDataAtCell(row + 1, 6, nextRowStartTime);
                                        let [nextRowDate, nextRowStartTimeStr] =
                                        nextRowStartTime.split(' ');
                                        let [nextRowStartHour, nextRowStartMinute] =
                                        nextRowStartTimeStr.split(
                                            ':').map(Number);
                                        let nextRowEndTime = new Date(nextRowDate);
                                        nextRowEndTime.setHours(nextRowStartHour +
                                            totalHoursFromEstimation);
                                        hot.setDataAtCell(row + 1, 9, nextRowEndTime
                                            .toISOString().split('T')[
                                                0] + ' ' + String(nextRowEndTime
                                                .getHours()).padStart(2,
                                                '0') +
                                            ':' + String(nextRowEndTime.getMinutes())
                                            .padStart(2, '0') +
                                            ':00');
                                    }
                                }

                            });
                        }
                    }

                });


                const machineListPlm = data[0].code_machine.split(',').map(machine => machine.trim());
                const hotContainerPlm = document.getElementById('handsontable-container-pelumasan');
                const hotPlm = new Handsontable(hotContainerPlm, {
                    data: [],
                    colHeaders: [
                        'Code Item', 'Material Name', 'Quantity', 'Capacity',
                        'Est. Hours', 'Est. Days', 'Start Time',
                        'Setup', 'Istirahat', 'End Time', 'Delivery Date', 'Up Cetak',
                        'Work Order DocNo', 'Sales Order DocNo', 'Quantity Awal', 'ID'
                    ],
                    rowHeaders: true,
                    multiSelect: true,
                    columns: [{
                            data: 0
                        }, {
                            data: 1
                        }, {
                            data: 2
                        },
                        {
                            data: 3
                        },
                        {
                            data: 4,
                            readOnly: true,
                            type: 'numeric',
                            numericFormat: {
                                pattern: '0.000'
                            }
                        },
                        {
                            data: 5,
                            readOnly: true,
                            type: 'numeric',
                            numericFormat: {
                                pattern: '0.000'
                            }
                        },
                        {
                            data: 6,
                            type: 'time',
                            timeFormat: 'HH:MM'
                        },
                        {
                            data: 7,
                            type: 'numeric'
                        },
                        {
                            data: 8,
                            type: 'numeric'
                        },
                        {
                            data: 9,
                            type: 'time',
                            timeFormat: 'HH:MM'
                        },
                        {
                            data: 10
                        },
                        {
                            data: 11
                        },
                        {
                            data: 12
                        },
                        {
                            data: 13
                        },
                        {
                            data: 14
                        },
                        {
                            data: 15,
                            readOnly: true,
                        },
                    ],
                    contextMenu: [

                        ...machineListPlm.map((machine, index) => ({
                            key: `move_to_machine_${index}`,
                            name: `Pindahkan ke ${machine}`,
                            callback: function() {
                                moveRowToAnotherMachinePlm(machine);
                            },
                        }))

                    ],
                    manualRowMove: true,
                    manualColumnMove: true,
                    allowInsertRow: true,
                    allowRemoveRow: true,
                    licenseKey: 'non-commercial-and-evaluation',
                });

                function moveRowToAnotherMachine(machine) {
                    const selected = hot.getSelected();

                    if (!selected || selected.length === 0) {
                        alert("Please select a row to move.");
                        return;
                    }

                    const selectedRange = selected[0];
                    const selectedRow = selectedRange ? selectedRange[0] : null;

                    if (selectedRow === null) {
                        alert("Please select a valid row.");
                        return;
                    }

                    const rowData = hot.getDataAtRow(selectedRow);
                    console.log("Row data selected:", rowData);

                    if (!rowData) {
                        alert("Error retrieving the row data.");
                        return;
                    }

                    const targetTable = window.hotTable[machine];

                    if (targetTable) {
                        // Step 1: Check if the row already exists in the target table
                        const targetData = targetTable.getData();
                        const isRowAlreadyPresent = targetData.some(row => {
                            return row.every((cell, index) => {
                                // Compare each cell in the row with the corresponding column
                                return cell === rowData[index];
                            });
                        });

                        // Step 2: If the row is already present, alert and exit
                        if (isRowAlreadyPresent) {
                            alert("This row already exists in the target table.");
                            return;
                        }

                        // Step 3: If the row doesn't exist, proceed with moving it
                        const targetDataBefore = targetTable.getData();
                        const rowId = selectedRow;

                        // Set 'moved-row' and 'rows-{rowId}' classes in the source table
                        for (let colIndex = 0; colIndex < hot.countCols(); colIndex++) {
                            const existingClass = hot.getCellMeta(selectedRow, colIndex).className || '';
                            const newClass = `${existingClass} moved-row rows-${rowId}`;

                            hot.setCellMeta(selectedRow, colIndex, 'className', newClass);

                            const cell = hot.getCell(selectedRow, colIndex);
                            if (cell) {
                                cell.classList.add('moved-row', `rows-${rowId}`);
                            }
                        }

                        hot.render();

                        // Step 4: Add the row to the target table
                        for (let colIndex = 0; colIndex < hot.countCols(); colIndex++) {
                            const newClass = `moved-row rows-${rowId}`;

                            targetTable.setCellMeta(targetDataBefore.length, colIndex, 'className', newClass);

                            const targetCell = targetTable.getCell(targetDataBefore.length, colIndex);
                            if (targetCell) {
                                targetCell.classList.add('moved-row', `rows-${rowId}`);
                            }
                        }

                        // Add the data to the target table
                        targetDataBefore.push(rowData);
                        targetTable.loadData(targetDataBefore);
                        targetTable.render();

                        console.log("Data after update:", targetTable.getData());
                    } else {
                        alert("Target machine table not found.");
                    }
                }

                function moveRowToAnotherMachinePlm(machine) {
                    const selected = hotPlm.getSelected();

                    if (!selected || selected.length === 0) {
                        alert("Please select a row to move.");
                        return;
                    }

                    const selectedRange = selected[0];
                    const selectedRow = selectedRange ? selectedRange[0] : null;

                    if (selectedRow === null) {
                        alert("Please select a valid row.");
                        return;
                    }

                    const rowData = hotPlm.getDataAtRow(selectedRow);
                    console.log("Row data selected:", rowData);

                    if (!rowData) {
                        alert("Error retrieving the row data.");
                        return;
                    }

                    const targetTable = window.hotTable[machine];

                    if (targetTable) {
                        // Step 1: Check if the row already exists in the target table
                        const targetData = targetTable.getData();
                        const isRowAlreadyPresent = targetData.some(row => {
                            return row.every((cell, index) => {
                                // Compare each cell in the row with the corresponding column
                                return cell === rowData[index];
                            });
                        });

                        // Step 2: If the row is already present, alert and exit
                        if (isRowAlreadyPresent) {
                            alert("This row already exists in the target table.");
                            return;
                        }

                        // Step 3: If the row doesn't exist, proceed with moving it
                        const targetDataBefore = targetTable.getData();
                        const rowId = selectedRow;

                        // Set 'moved-row' and 'rows-{rowId}' classes in the source table
                        for (let colIndex = 0; colIndex < hotPlm.countCols(); colIndex++) {
                            const existingClass = hotPlm.getCellMeta(selectedRow, colIndex).className || '';
                            const newClass = `${existingClass} moved-row rows-${rowId}`;

                            hotPlm.setCellMeta(selectedRow, colIndex, 'className', newClass);

                            const cell = hotPlm.getCell(selectedRow, colIndex);
                            if (cell) {
                                cell.classList.add('moved-row', `rows-${rowId}`);
                            }
                        }

                        hotPlm.render();

                        // Step 4: Add the row to the target table
                        for (let colIndex = 0; colIndex < hotPlm.countCols(); colIndex++) {
                            const newClass = `moved-row rows-${rowId}`;

                            targetTable.setCellMeta(targetDataBefore.length, colIndex, 'className', newClass);

                            const targetCell = targetTable.getCell(targetDataBefore.length, colIndex);
                            if (targetCell) {
                                targetCell.classList.add('moved-row', `rows-${rowId}`);
                            }
                        }

                        // Add the data to the target table
                        targetDataBefore.push(rowData);
                        targetTable.loadData(targetDataBefore);
                        targetTable.render();

                        console.log("Data after update:", targetTable.getData());
                    } else {
                        alert("Target machine table not found.");
                    }
                }

                $('#saveAllData').on('click', function() {
                    saveAllTableData();
                });

                function saveAllTableData() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const codePlan = urlParams.get("code-ac");

                    // Show loading indicator
                    Swal.fire({
                        title: 'Saving data...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Collect data from main table
                    const mainTableData = hot.getData();

                    // Collect data from pelumasan table
                    const pelumasanTableData = hotPlm.getData();

                    // Collect data from machine tables
                    const machineTablesData = {};
                    const machines = $('#h_mesinplan').text().split(',');

                    machines.forEach(function(machine) {
                        const machineTable = window.hotTable[machine.trim()];
                        if (machineTable) {
                            machineTablesData[machine.trim()] = machineTable.getData();
                        }
                    });

                    // Prepare data for submission
                    const allData = {
                        code_plan: codePlan,
                        main_table: mainTableData,
                        pelumasan_table: pelumasanTableData,
                        machine_tables: machineTablesData
                    };

                    // Send data to server
                    $.ajax({
                        url: "{{ route('save-plan-mingguan.data') }}",
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            data: JSON.stringify(allData)
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'All data has been saved successfully.'
                            }).then(function() {
                                window.location.href = "{{ route('plan-first.data') }}";
                            });

                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to save data. Please try again.'
                            });
                        }
                    });
                }

            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });

        function shiftTimeByTwoHours() {
            const selectedRow = hot.getSelected();
            if (selectedRow) {
                const rows = selectedRow[0];
                const row = rows[0];

                const startTime = hot.getDataAtCell(row, 6); // Get the start time
                const endJamTime = hot.getDataAtCell(row, 9); // Get the end time

                if (endJamTime) {
                    let [endDate, endTimeStr] = endJamTime.split(' ');
                    let [endHour, endMinute, endSecond] = endTimeStr.split(':').map(Number);

                    // Check if the time is close to midnight and reset it to 00:00:00
                    if (endHour === 0 && endMinute >= 0 && endSecond > 0) {
                        // Set time to 00:00:00 (midnight)
                        endHour = 0;
                        endMinute = 0;
                        endSecond = 0;
                    }

                    // If the time is already at 00:59:11 or close, reset it to 00:00:00
                    if (endHour === 0 && endMinute === 59 && endSecond >= 0) {
                        // Set time to 00:00:00 (midnight)
                        endHour = 0;
                        endMinute = 0;
                        endSecond = 0;
                    }

                    // Create the updated end time string
                    const updatedEndTime =
                        `${endDate} 00:00:00`; // Ensure it is exactly midnight (00:00:00) of the given date

                    // Now, proceed with your remaining logic
                    let [startDate, startTimeStr] = startTime.split(' ');
                    let [startHour, startMinute] = startTimeStr.split(':').map(Number);

                    // Create a Date object for the start time
                    const startDateTime = new Date(`${startDate}T${startTimeStr}`);

                    // Create the endDateTime object with the new calculated time
                    const endDateTime = new Date(
                        `${endDate}T${String(endHour).padStart(2, '0')}:${String(endMinute).padStart(2, '0')}:${String(endSecond).padStart(2, '0')}`
                    );

                    // Calculate the time difference in milliseconds
                    const timeDifferenceInMs = endDateTime - startDateTime;
                    console.log('Time difference (ms):', timeDifferenceInMs);

                    // Step 2: Convert the difference from milliseconds to minutes
                    const timeDifferenceInMinutes = timeDifferenceInMs / (1000 * 60);
                    console.log('Time difference in minutes:', timeDifferenceInMinutes);

                    // Step 3: Convert minutes to hours for estimation
                    const estimationHours = timeDifferenceInMinutes / 60;
                    console.log('Estimation in hours:', estimationHours);

                    // Step 4: Estimate the quantity for the time difference
                    const capacity = hot.getDataAtCell(row, 3) || 1; // Get the capacity
                    const estimatedQuantity = (estimationHours.toFixed(1)) *
                        capacity; // Quantity estimation based on time difference and capacity

                    alert('Estimated Quantity: ' + estimatedQuantity);

                    // Step 5: Calculate the remaining quantity
                    const originalQuantity = hot.getDataAtCell(row, 2) || 0; // Get the original quantity
                    const remainingQuantity = originalQuantity - estimatedQuantity;
                    console.log('Remaining Quantity:', remainingQuantity);

                    // Step 6: Set the end time exactly at 00:00 (midnight)
                    hot.setDataAtCell(row, 9, `${endDate} 00:00:00`);

                    // Update the table with the new values after the calculation
                    hot.setDataAtCell(row, 2, Math.floor(estimatedQuantity)); // Update the quantity in the table

                    // Step 7: Handle the remaining quantity in the next row (if any)
                    if (remainingQuantity > 0) {
                        // Function to format Date object into 'YYYY-MM-DD HH:mm:ss'
                        function formatDate(date) {
                            const year = date.getFullYear();
                            const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed
                            const day = String(date.getDate()).padStart(2, '0');
                            const hours = String(date.getHours()).padStart(2, '0');
                            const minutes = String(date.getMinutes()).padStart(2, '0');
                            const seconds = String(date.getSeconds()).padStart(2, '0');
                            return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
                        }

                        const updatedEndTime = `${endDate} 08:00:00`; // Keep the new start time for the next row
                        const estimatedMinutes = Math.floor(remainingQuantity / capacity) * 60;
                        const newEndTime = new Date(updatedEndTime);

                        newEndTime.setMinutes(newEndTime.getMinutes() + estimatedMinutes);

                        const formattedEndTime = formatDate(newEndTime);

                        const newRow = [
                            hot.getDataAtCell(row, 0), // Copy other data from the current row
                            hot.getDataAtCell(row, 1),
                            (remainingQuantity.toFixed(2)), // Set remaining quantity
                            capacity,
                            (remainingQuantity / capacity), // Estimate hours for remaining quantity
                            ((remainingQuantity / capacity) / 24), // Estimate days for remaining quantity
                            updatedEndTime, // Set the new end time after adding the estimated hours
                            '', // Setup time
                            '', // Rest time
                            formattedEndTime, // New end time for the next row
                            hot.getDataAtCell(row, 10), // Delivery date
                            hot.getDataAtCell(row, 11), // Up Cetak
                            hot.getDataAtCell(row, 12), // Work Order DocNo
                            hot.getDataAtCell(row, 13), // Sales Order DocNo
                            hot.getDataAtCell(row, 14), // Quantity Awal
                        ];

                        const currentData = hot.getData();
                        currentData.splice(row + 1, 0, newRow); // Insert the new row after the selected row
                        hot.loadData(currentData); // Update the table with the new row
                        hot.render(); // Re-render the table
                    }
                }
            }
        }

        function mergeSelectedRows() {
            const selectedRanges = hot.getSelectedRange(); // Mendapatkan rentang yang dipilih
            if (selectedRanges && selectedRanges.length > 0) {
                let rows = [];
                selectedRanges.forEach(range => {
                    for (let i = range.from.row; i <= range.to.row; i++) {
                        rows.push(i); // Menambahkan nomor baris yang dipilih
                    }
                });

                console.log("Baris yang dipilih:", rows);

                // Memastikan ada setidaknya dua baris yang dipilih untuk digabung
                if (rows.length < 2) {
                    alert('Silakan pilih minimal dua baris untuk digabung.');
                    return;
                }

                // Variabel untuk menyimpan hasil yang digabungkan
                let totalQty = 0;
                let totalEstHours = 0;
                let totalEstDays = 0;
                let startTime = hot.getDataAtCell(rows[0], 6); // Start Time dari baris pertama yang dipilih
                let endTime = hot.getDataAtCell(rows[rows.length - 1], 9); // End Time dari baris terakhir yang dipilih

                // Menjumlahkan Quantity, Est. Hours, dan Est. Days
                rows.forEach(rowIdx => {
                    const qty = hot.getDataAtCell(rowIdx, 2) || 0; // Mengambil Quantity
                    const estHours = hot.getDataAtCell(rowIdx, 4) || 0; // Mengambil Est. Hours
                    const estDays = hot.getDataAtCell(rowIdx, 5) || 0; // Mengambil Est. Days

                    totalQty += qty; // Menjumlahkan Quantity
                    totalEstHours += estHours; // Menjumlahkan Est. Hours
                    totalEstDays += estDays; // Menjumlahkan Est. Days
                });

                // Menetapkan nilai yang digabungkan ke baris pertama yang dipilih
                hot.setDataAtCell(rows[0], 2, totalQty); // Update Quantity di baris pertama
                hot.setDataAtCell(rows[0], 4, totalEstHours); // Update Est. Hours di baris pertama
                hot.setDataAtCell(rows[0], 5, totalEstDays); // Update Est. Days di baris pertama
                hot.setDataAtCell(rows[0], 6, startTime); // Set Start Time ke baris pertama
                hot.setDataAtCell(rows[0], 9, endTime); // Set End Time ke baris pertama

                // Menghapus baris setelah yang pertama
                for (let i = rows.length - 1; i > 0; i--) {
                    hot.alter('remove_row', rows[i]);
                }

                console.log(`Baris digabung: ${rows.length} baris digabung ke baris pertama.`);
            } else {
                alert('Silakan pilih minimal dua baris untuk digabung.');
            }
        }

        // Fungsi untuk memotong baris jika End Time melebihi jam 23:00:00
        function splitRowIfNeeded(row) {
            let maxEndHour = 23;
            let capacity = hotTable.getDataAtCell(row, 3);
            let startTime = hotTable.getDataAtCell(row, 6);
            let qty = hotTable.getDataAtCell(row, 2);
            let estimationHours = qty / capacity;
            let [startDate, startTimeStr] = startTime.split(' ');
            let [startHour, startMinute] = startTimeStr.split(':').map(Number);
            let startDateTime = new Date(`${startDate}T${startTimeStr}`);
            let endDateTime = new Date(startDateTime);
            endDateTime.setHours(startHour + estimationHours);

            while (true) {
                let midnight = new Date(startDateTime);
                midnight.setHours(maxEndHour, 0, 0, 0);
                if (endDateTime <= midnight) break;
                // Hitung jam kerja sampai jam 23:00
                let hoursUntilMidnight = (midnight - startDateTime) / (1000 * 60 * 60);
                let qtyUntilMidnight = Math.floor(hoursUntilMidnight * capacity);
                // Set End Time baris ini ke jam 23:00
                hotTable.setDataAtCell(row, 9, midnight.toISOString().split('T')[0] + ' 23:00:00');
                hotTable.setDataAtCell(row, 2, qtyUntilMidnight);
                hotTable.setDataAtCell(row, 4, hoursUntilMidnight);
                hotTable.setDataAtCell(row, 5, hoursUntilMidnight / 24);
                // Sisa pekerjaan ke baris baru
                let remainingQty = qty - qtyUntilMidnight;
                let remainingHours = remainingQty / capacity;
                let nextDay = new Date(midnight);
                nextDay.setDate(nextDay.getDate() + 1);
                let startNextDay = nextDay.toISOString().split('T')[0] + ' 08:00:00';
                let newStartDate = new Date(startNextDay);
                newStartDate.setHours(newStartDate.getHours() + remainingHours);
                let newEndTime = newStartDate.toISOString().split('T')[0] + ' ' + String(newStartDate.getHours()).padStart(
                    2, '0') + ':' + String(newStartDate.getMinutes()).padStart(2, '0') + ':00';
                let newRow = [
                    hotTable.getDataAtCell(row, 0),
                    hotTable.getDataAtCell(row, 1),
                    remainingQty,
                    capacity,
                    remainingHours,
                    remainingHours / 24,
                    startNextDay,
                    '',
                    '',
                    newEndTime,
                    hotTable.getDataAtCell(row, 10),
                    hotTable.getDataAtCell(row, 11),
                    hotTable.getDataAtCell(row, 12),
                    hotTable.getDataAtCell(row, 13),
                    hotTable.getDataAtCell(row, 14),
                    hotTable.getDataAtCell(row, 15),
                ];
                let currentData = hotTable.getData();
                currentData.splice(row + 1, 0, newRow);
                hotTable.loadData(currentData);
                hotTable.render();
                // Update variabel untuk loop berikutnya
                row = row + 1;
                qty = remainingQty;
                startTime = startNextDay;
                estimationHours = qty / capacity;
                [startDate, startTimeStr] = startTime.split(' ');
                [startHour, startMinute] = startTimeStr.split(':').map(Number);
                startDateTime = new Date(`${startDate}T${startTimeStr}`);
                endDateTime = new Date(startDateTime);
                endDateTime.setHours(startHour + estimationHours);
            }
        }
    </script>
@endsection
