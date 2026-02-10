@extends('main.layouts.main')
@section('title')
    Process
@endsection
@section('css')
    <link href="{{ asset('new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection
@section('page-title')
    Process
@endsection
@section('body')
    <style>
        .status-deleted {
            background-color: #f8d7da;
        }

        .status-in-progress {
            background-color: #d4edda;
        }

        .status-open {
            background-color: #d1ecf1;
        }
    </style>

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18" id="header_rencana_plan">Pilih Rencana Plan</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Process</a></li>
                            <li class="breadcrumb-item active">Select Machine</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            <div class="col">
                                <input type="text" name="selected_rencana_plan" id="selected_rencana_plan" hidden>
                                <h4 class="card-title" id="header_rencana_plan_dua">Pilih Rencana Plan</h4>
                            </div>
                            <div class="col">
                                <div class="header-right d-flex flex-wrap justify-content-end">
                                    <button type="button" class="btn btn-primary" id="select_rencana_plan"> Pilih Rencana Plan </button>
                                    <button type="button" class="btn btn-primary" id="select_machine"> Pilih Mesin </button>
                                    <button type="button" class="btn btn-primary" id="select_workorder" onclick="selectMaterialToAc()"> Pilih Item </button>
                                </div>
                            </div>
                        </div>
                        <br>

                        <div class="" id="table-rencana-plan" style="display: block;">
                            <table id="datatable-rencana-plan" class="table table-bordered table-responsive-md">
                                <thead>
                                    <tr>
                                        <th style="width:5%;">#</th>
                                        <th>Rencana Plan </th>
                                        <th>Department</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>


                        <div id="table-machine" style="display: none;">
                            <table id="datatable-machine" class="table table-hover table-responsive-md">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Machine</th>
                                        <th>Department</th>
                                        <th>Kapasitas</th>
                                        <th>Unit</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>


                        <div id="table-work-order" style="display: none;">
                            <div id="advanced_filter_wo">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p>SO Date - From</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p>SO Date - To</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="date" class="form-control" name="from_date_advanced_filter_wo" id="from_date_advanced_filter_wo">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="date" class="form-control" name="to_date_advanced_filter_wo" id="to_date_advanced_filter_wo">
                                    </div>
                                    <button class="btn btn-primary" type="button" id="filterButton">Filter</button>
                                </div>
                            </div>
                            <br>
                            <hr>

                            <table id="datatable-work-order" class="table table-hover nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>WO DocNo </th>
                                        <th>SO DocNo </th>
                                        <th>Name</th>
                                        <th>Material Code</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Up</th>
                                        <th>Delivery Date</th>
                                        <th>Status</th>
                                        <th>Detail</th>
                                        <th>Order Status</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>


                        <!-- Modal -->
                        <div class="modal fade" id="detailModal" tabindex="-1" role="dialog"
                            aria-labelledby="detailModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="detailModalLabel">Detail Information</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body" id="modal-body-detail-info-jo">
                                        <input type="text" name="docno_detail_info" id="docno_detail_info"
                                            class="form-control">
                                        <br>
                                        <div id="loading-indicator" style="display: none;">
                                            <p>Loading...</p> <!-- Atau bisa menggunakan animasi gif jika mau -->
                                        </div>
                                        <div id="tabel-info-jo">


                                        </div>
                                        <!-- Isi modal akan di-update di sini -->
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form class="submitAkumulasi" id="submitAkumulasi">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>


                                        <div class="row" style="margin: 10px;">
                                            <div class="col">
                                                <h6>Start Date</h6>
                                                <input type="date" name="start_date" id="start_date"
                                                    class="form-control" onchange="setEndDate()" required>
                                            </div>
                                            <div class="col">
                                                <h6>End Date</h6>
                                                <input type="date" name="end_date" id="end_date"
                                                    class="form-control">
                                            </div>
                                            <div class="col-md-3" style="display: none;">
                                                <h6>Shift Plan</h6>
                                                <input type="number" value="0" class="form-control"
                                                    id="shift_plan" name="shift_plan" required>
                                            </div>
                                        </div>
                                        <br>


                                        <div class="modal-body" id="modal-penjadwalan">
                                            ...
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                            <button type="button" id="submit-data-ac" class="btn btn-primary">Save
                                                changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
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
            function setEndDate() {
                var startDate = new Date(document.getElementById("start_date").value);
                var endDate = new Date(startDate.getTime() + 6 * 24 * 60 * 60 * 1000); // Add 7 days to start date
                document.getElementById("end_date").value = endDate.toISOString().split('T')[0];
            }

            function selectMachine() {

                var selectedMachine = [];
                var selectedCode = [];
                var selectedDept = [];

                $('.select-machine:checked').each(function() {
                    selectedMachine.push($(this).val());
                    var rowData = table.row($(this).closest('tr')).data();
                    alert(rowData.dept)
                    selectedCode.push(rowData.code);
                    selectedDept.push(rowData.dept); // Add dept value
                });

                $('#selected_machines').val(selectedCode.join(', '));

                var currentQueryString = window.location.search;
                var searchParams = new URLSearchParams(currentQueryString);
                searchParams.set('machine', selectedCode.join(', '));
                searchParams.set('dept', selectedDept.join(', ')); // Set the dept parameter
                var newQueryString = searchParams.toString();
                var newURL = window.location.pathname + '?' + newQueryString;
                newURL = newURL.replace('/select-machine', '');

                window.location.href = newURL;

            }

            const getSelectedRows = () => {
                const table = $('#datatable-work-order').DataTable();
                const checkedInputs = document.querySelectorAll('input.select-wodocno:checked');
                return Array.from(checkedInputs).map(input => table.row($(input).closest('tr')).data());
            };

            const createTable = (dataToSend) => {
                const table = document.createElement('table');
                table.classList.add('table', 'table-responsive');

                const theadContent = `
                    <tr>
                        <th>Prioritas</th>
                        <th>Data Code</th>
                        <th>Data Qty</th>
                        <th>Up</th>
                        <th>Delivery</th>
                        <th>WODocNo</th>
                        <th>JODocNo</th>
                        <th>Process</th>
                        <th>Department</th>
                    </tr>`;
                table.innerHTML = `<thead>${theadContent}</thead><tbody></tbody>`;

                const tbody = table.querySelector('tbody');

                dataToSend.forEach(data => {
                    const rowContent = `
                        <tr>
                            <td><input type="number" name="order" class="form-control form-control-sm" style="width: 80px;"></td>
                            <td>${data.dataCode}</td>
                            <td>${data.dataQty}</td>
                            <td>${data.dataUp}</td>
                            <td>${data.deliveryDate}</td>
                            <td>${data.woDocNo}</td>
                            <td>${data.joDocNo}</td>
                            <td>${data.process}</td>
                            <td>${data.department}</td>
                        </tr>`;
                    tbody.insertAdjacentHTML('beforeend', rowContent);
                });

                return table;
            };

            const isSequential = (values) => {
                const sorted = values.map(Number).sort((a, b) => a - b);
                return sorted.every((val, i) => i === 0 || val === sorted[i - 1] + 1);
            };

            const sendData = (payload, csrfToken) => {
                return fetch('acumulation-data', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(response => response.json())
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Terjadi kesalahan saat mengirim data!',
                        });
                    });
            };

            let dataToSend = []; // Simpan data untuk dikirimkan ke server

            function selectMaterialToAc() {
                var urlParams = new URLSearchParams(window.location.search);
                const table = $('#datatable-work-order').DataTable();
                const rows = table.rows().nodes();
                var machineSelected = urlParams.get("machine");
                var deptPlan = urlParams.get("dept");
                var rPlan = urlParams.get("rplan");
                const selectedData = [];

                $(rows).each(function() {
                    const checkbox = $(this).find('input.select-wodocno');
                    const counterOrder = $(this).find('.counter-order').val();

                    // Cek jika checkbox tercentang dan counterOrder ada
                    if (checkbox.is(':checked') && counterOrder) {
                        const rowData = table.row($(this)).data();
                        rowData.counterOrder = parseInt(counterOrder); // Simpan counter order

                        // Menambahkan process dan department dari URL params
                        rowData.process = rPlan;
                        rowData.department = deptPlan;

                        // Menambahkan rowData ke dalam selectedData
                        selectedData.push(rowData);
                    }
                });

                // Urutkan data berdasarkan counterOrder
                selectedData.sort((a, b) => a.counterOrder - b.counterOrder);

                // Simpan data untuk server
                dataToSend = selectedData;

                // Tampilkan data di modal
                const modalBody = document.getElementById('modal-penjadwalan');
                modalBody.innerHTML = '';

                const tableElement = document.createElement('table');
                tableElement.classList.add('table', 'table-striped');
                const thead = document.createElement('thead');
                const tbody = document.createElement('tbody');

                const headers = ['No.', 'Data Code', 'Data Qty', 'Up', 'Delivery Date', 'WODocNo', 'SODocNo', 'Process','Dept'];
                const headerRow = document.createElement('tr');
                headers.forEach(header => {
                    const th = document.createElement('th');
                    th.textContent = header;
                    headerRow.appendChild(th);
                });
                thead.appendChild(headerRow);
                tableElement.appendChild(thead);

                selectedData.forEach(data => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><input type="text" class="form-control" name="order" value="${data.counterOrder}" readonly></td>
                        <td>${data.MaterialCode}</td>
                        <td>${data.Quantity}</td>
                        <td>${data.Up}</td>
                        <td>${data.DeliveryDate}</td>
                        <td>${data.WODocNo}</td>
                        <td>${data.SODocNo}</td>
                        <td>${rPlan}</td>
                        <td>${deptPlan}</td>
                    `;
                    tbody.appendChild(row);
                });

                tableElement.appendChild(tbody);
                modalBody.appendChild(tableElement);

                $('#myModal').modal('show');
            }

            document.getElementById('submit-data-ac').addEventListener('click', function() {
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                // const shiftPlan = document.getElementById('shift_plan').value;

                if (!startDate.trim()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Tanggal mulai harus diisi!',
                    });
                    return;
                }

                const orderValues = [];
                const dropdowns = document.querySelectorAll('.form-control[name="order"]');
                dropdowns.forEach((dropdown) => {
                    orderValues.push(dropdown.value);
                });

                dataToSend.forEach((data, index) => {
                    data.order = orderValues[index];
                });

                function isSequential(arr) {
                    const intArr = arr.map(Number);
                    intArr.sort((a, b) => a - b);
                    for (let i = 0; i < intArr.length - 1; i++) {
                        if (intArr[i] + 1 !== intArr[i + 1]) {
                            return false;
                        }
                    }
                    return true;
                }

                if (!isSequential(orderValues)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Nilai input tidak berurutan!',
                    });
                    return;
                }

                var machineSelected = [];
                const urlParams = new URLSearchParams(window.location.search);

                urlParams.getAll("machine").forEach(function(machine) {
                    machineSelected.push(machine);
                });

                var deptPlan = urlParams.get("dept");


                console.log(machineSelected);

                // console.log(machineSelected);

                const payload = {
                    data: dataToSend,
                    start_date: startDate,
                    end_date: endDate,
                    department: deptPlan,
                    // shift: shiftPlan,
                    machine: machineSelected,
                };

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                // alert('1')
                fetch('submit-plan-first', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload)
                })
                .then(async response => {
                    let res;

                    try {
                        res = await response.json();
                    } catch (e) {
                        // Jika gagal parse JSON (misal karena dd()), tampilkan error
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Data error. Cek console/log atau coba hubungi Team IT.',
                            showConfirmButton: true
                        });
                        return;
                    }

                    if (res.errors) {
                        $.each(res.errors, function(key, value) {
                            $('#' + key).next('.error-message').text(value).show();
                        });
                    } else {
                        $('.alert-danger').hide();
                        $("#myModal").removeClass("in");
                        $(".modal-backdrop").remove();
                        $("#myModal").hide();

                        if (res.success === false) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Terjadi kesalahan saat menyimpan plan!',
                                showConfirmButton: true
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: res.message || 'Plan berhasil disimpan!',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(function() {
                                window.location.href = "{{ route('process.bryntum-scheduler') }}";
                            });

                            $('#submitAkumulasi').trigger("reset");
                            $('#myModal').modal('hide');
                        }
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Gagal',
                        text: error.message || 'Terjadi kesalahan saat submit data!',
                        icon: 'error'
                    });
                });
            });





            function selectMaterialToAcA() {
                var urlParams = new URLSearchParams(window.location.search);
                var machineSelected = urlParams.get("machine");
                var deptPlan = urlParams.get("dept");
                var rPlan = urlParams.get("rplan");

                // Get selected checkboxes
                const checkedInputs = document.querySelectorAll('input.select-wodocno:checked');
                const dataToSend = [];

                // console.log(checkedInputs);

                // Iterate through each selected checkbox
                checkedInputs.forEach(input => {
                    // Get the row data using DataTable's API
                    const table = $('#datatable-work-order').DataTable();
                    const row = table.row($(input).closest('tr')).data();

                    // Extract necessary values from the row
                    const dataCode = row.MaterialCode;
                    const dataQty = row.Quantity;
                    const dataUp = row.Up;
                    const deliveryDate = row.DeliveryDate;
                    const machineValue = machineSelected;
                    const woDocNo = row.WODocNo;
                    const joDocNo = row.SODocNo;
                    const process = rPlan;
                    const department = deptPlan;

                    // Push the extracted data into the dataToSend array
                    dataToSend.push({
                        dataCode: dataCode,
                        dataQty: dataQty,
                        dataUp: dataUp,
                        deliveryDate: deliveryDate,
                        machine: machineValue,
                        woDocNo: woDocNo,
                        joDocNo: joDocNo,
                        process: process,
                        department: department,
                    });
                });

                // Create and populate the table in the modal
                const table = document.createElement('table');
                table.classList.add('table', 'table-responsive');
                const thead = document.createElement('thead');
                const tbody = document.createElement('tbody');
                const tr = document.createElement('tr');
                const th1 = document.createElement('th');
                const th2 = document.createElement('th');
                const th3 = document.createElement('th');
                const th4 = document.createElement('th');
                const th5 = document.createElement('th');
                const th6 = document.createElement('th');
                const th7 = document.createElement('th');
                const th8 = document.createElement('th');
                const th9 = document.createElement('th');

                th1.textContent = 'Prioritas';
                th2.textContent = 'Data Code';
                th3.textContent = 'Data Qty';
                th4.textContent = 'Up';
                th5.textContent = 'Delivery';
                th6.textContent = 'WODocNo';
                th7.textContent = 'JODocNo';
                th8.textContent = 'Process';
                th9.textContent = 'Department';

                tr.appendChild(th1);
                tr.appendChild(th2);
                tr.appendChild(th3);
                tr.appendChild(th4);
                tr.appendChild(th5);
                tr.appendChild(th6);
                tr.appendChild(th7);
                tr.appendChild(th8);
                tr.appendChild(th9);
                thead.appendChild(tr);
                table.appendChild(thead);
                table.appendChild(tbody);

                dataToSend.forEach(data => {
                    const tr = document.createElement('tr');
                    const td1 = document.createElement('td');
                    const td2 = document.createElement('td');
                    const td3 = document.createElement('td');
                    const td4 = document.createElement('td');
                    const td5 = document.createElement('td');
                    const td6 = document.createElement('td');
                    const td7 = document.createElement('td');
                    const td8 = document.createElement('td');
                    const td9 = document.createElement('td');

                    var urlParams = new URLSearchParams(window.location.search);
                    var deptPlan = urlParams.get("dept");
                    var rPlan = urlParams.get("rplan");

                    td2.textContent = data.dataCode;
                    td3.textContent = data.dataQty;
                    td4.textContent = data.dataUp;
                    td5.textContent = data.deliveryDate;
                    td6.textContent = data.woDocNo;
                    td7.textContent = data.joDocNo;
                    td8.textContent = deptPlan;
                    td9.textContent = rPlan;

                    const select = document.createElement('input');
                    select.classList.add('form-control', 'form-control-sm');
                    select.style.width = '80px';
                    select.setAttribute('name', 'order');
                    select.setAttribute('id', 'order');
                    select.setAttribute('type', 'number');

                    td1.appendChild(select);
                    tr.appendChild(td1);
                    tr.appendChild(td2);
                    tr.appendChild(td3);
                    tr.appendChild(td4);
                    tr.appendChild(td5);
                    tr.appendChild(td6);
                    tr.appendChild(td7);
                    tr.appendChild(td8);
                    tr.appendChild(td9);
                    tbody.appendChild(tr);
                });

                const modalBody = document.getElementById('modal-penjadwalan');
                modalBody.innerHTML = '';
                modalBody.appendChild(table);


                $('#myModal').modal('show');


                // Add event listener for save button
                const saveChangesButton = document.getElementById('submit-data-ac');

                saveChangesButton.addEventListener('click', function() {
                    const startDate = document.getElementById('start_date').value;
                    const endDate = document.getElementById('end_date').value;
                    const shiftPlan = document.getElementById('shift_plan').value;

                    if (!startDate.trim()) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Tanggal mulai harus diisi!',
                        });
                        return;
                    }

                    const orderValues = [];
                    const dropdowns = document.querySelectorAll('.form-control[name="order"]');
                    dropdowns.forEach((dropdown) => {
                        orderValues.push(dropdown.value);
                    });

                    dataToSend.forEach((data, index) => {
                        data.order = orderValues[index];
                    });

                    function isSequential(arr) {
                        const intArr = arr.map(Number);
                        intArr.sort((a, b) => a - b);
                        for (let i = 0; i < intArr.length - 1; i++) {
                            if (intArr[i] + 1 !== intArr[i + 1]) {
                                return false;
                            }
                        }
                        return true;
                    }

                    if (!isSequential(orderValues)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Nilai input tidak berurutan!',
                        });
                        return;
                    }

                    const payload = {
                        data: dataToSend,
                        start_date: startDate,
                        end_date: endDate,
                        order: orderValues,
                        shift: shiftPlan,
                    };
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    alert('berhasil')
                    // fetch('acumulation-data', {
                    //         method: 'POST',
                    //         headers: {
                    //             'Content-Type': 'application/json',
                    //             'X-CSRF-TOKEN': csrfToken,
                    //         },
                    //         body: JSON.stringify(payload)
                    //     })
                    //     .then(response => response.json())
                    //     .then(data => {
                    //         Swal.fire({
                    //             title: 'Success',
                    //             text: data.message,
                    //             icon: 'success'
                    //         }).then((result) => {
                    //             if (result.isConfirmed) {
                    //                 window.location.href = '/ppicplan/planned-production-new?code-ac=' +
                    //                     data.code_ac;
                    //             }
                    //         });
                    //     })
                    //     .catch(error => {
                    //         console.error('Error:', error);
                    //     });
                });
            }


            $('#datatable-rencana-plan').on('click', '.select-rplan', function() {
                $('.select-rplan').not(this).prop('checked', false);
            });

            // Array untuk melacak urutan centang
            let selectedRowsOrder = [];

            document.addEventListener('change', function(event) {
                if (event.target.classList.contains('select-wodocno')) {
                    const table = $('#datatable-work-order').DataTable();
                    const rowElement = $(event.target).closest('tr');
                    const rowIndex = table.row(rowElement).index();

                    if (event.target.checked) {
                        // Tambahkan indeks baris ke daftar jika checkbox dicentang
                        if (!selectedRowsOrder.includes(rowIndex)) {
                            selectedRowsOrder.push(rowIndex);
                        }
                    } else {
                        // Hapus indeks dari daftar jika checkbox di-uncheck
                        selectedRowsOrder = selectedRowsOrder.filter(index => index !== rowIndex);
                    }

                    // Perbarui kolom `counter-order` sesuai urutan centang
                    selectedRowsOrder.forEach((index, order) => {
                        const row = $(table.row(index).node());
                        row.find('.counter-order').val(order + 1).prop('disabled', false);
                    });

                    // Kosongkan `counter-order` untuk checkbox yang tidak dicentang
                    table.rows().every(function(idx) {
                        if (!selectedRowsOrder.includes(idx)) {
                            const row = $(this.node());
                            row.find('.counter-order').val('').prop('disabled', true);
                        }
                    });
                }
            });


            $(document).ready(function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // AKSI PENGURUTAN DARI HASIL CENTANG AN
                let selectedRows = [];

                $(document).on('change', '.select-wodocno', function() {
                    const table = $('#datatable-work-order').DataTable();
                    const row = table.row($(this).closest('tr')).data();
                    const value = $(this).val();

                    if (this.checked) {
                        // Tambahkan data ke urutan
                        selectedRows.push(row);
                    } else {
                        // Hapus data dari urutan
                        selectedRows = selectedRows.filter(r => r.WODocNo !== value);
                    }
                });


                var table = $('#datatable-rencana-plan').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('rencana-plan.index') }}",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    },
                    paging: false,
                    searching: false,
                    select: {
                        style: 'single'
                    },
                    columns: [{
                            data: 'select',
                            name: 'select',
                            orderable: false,
                            searchable: false,
                            targets: 0,
                            defaultContent: '',
                            render: function(data, type, full, meta) {
                                return '<input type="checkbox" id="select-rplan" class="select-rplan" value="' +
                                    full.r_plan + '" data-dept="' + full.r_dept + '">';
                            }
                        },
                        {
                            data: 'r_plan',
                            name: 'r_plan'
                        },
                        {
                            data: 'r_dept',
                            name: 'r_dept'
                        },
                    ]
                });

                $('#select_rencana_plan').on('click', function() {

                    // if ($('input[id="select-rplan"]:checked').length === 0) {

                    //     Swal.fire({
                    //         title: '',
                    //         text: 'Anda belum memilih mesin. Silakan pilih mesin terlebih dahulu.',
                    //         confirmButtonText: 'OK'
                    //     });

                    // } else {

                    //     console.log('Mesin terpilih:', $('input[id="select-rplan"]:checked').map(function() {
                    //         return $(this).val();
                    //     }).get());


                        // alert('a')
                        var selectedMachine = [];
                        var selectedTops = [];
                        var selectedCode = [];
                        var selectedDept = [];

                        $('.select-rplan:checked').each(function() {
                            selectedMachine.push($(this).val());
                            var rowData = table.row($(this).closest('tr')).data();
                            // alert(rowData.dept)
                            selectedCode.push(rowData.r_plan);
                            selectedDept.push(rowData.r_dept); // Add dept value
                        });

                        $('#selected_rencana_plan').val(selectedCode.join(', '));

                        var currentQueryString = window.location.search;

                        var searchParams = new URLSearchParams(currentQueryString);
                        searchParams.set('rplan', selectedCode.join(', '));
                        searchParams.set('dept', selectedDept.join(', ')); // Set the dept parameter
                        var newQueryString = searchParams.toString();
                        var newURL = window.location.pathname + '?' + newQueryString;
                        // alert(newURL)
                        // newURLs = newURL.replace('/select-machine', '');

                        // alert(newURLs)

                        window.location.href = newURL;
                    // }

                });



                var currentQueryString = window.location.search;
                var parameter = new URLSearchParams(currentQueryString);
                var rplan = parameter.get("rplan");
                var rdept = parameter.get("dept");
                var rmachine = parameter.get("machine");
                var from_date = parameter.get("from_date");
                var to_date = parameter.get("to_date");

                if (from_date && to_date) {

                    $('#from_date_advanced_filter_wo').val(from_date);
                    $('#to_date_advanced_filter_wo').val(to_date);

                    $('#select_workorder').css('display', 'block');
                    $('#table-work-order').css('display', 'block');
                    $('#table-rencana-plan').css('display', 'none');

                    $('#select_rencana_plan').css('display', 'none');
                    $('#select_machine').css('display', 'none');
                    // $('#advanced_filter_wo').css('display', 'none');

                    var table = $('#datatable-work-order').DataTable({
                        processing: true,
                        serverSide: true,
                        scrollX: true,
                        ajax: {
                            url: "{{ route('wo-data.index') }}",
                            data: function(d) {
                                d.from = from_date;
                                d.to = to_date;
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                        },
                        lengthMenu: [25, 50, 100],
                        paginate: false,
                        searching: false,
                        select: {
                            style: 'single'
                        },
                        columns: [{
                                data: 'WODocNo',
                                name: 'select',
                                orderable: false,
                                searchable: false,
                                render: function(data, type, full, meta) {
                                    return '<input type="checkbox" id="select-wo" class="select-wodocno" value="' +
                                        data + '">';
                                }
                            },
                            {
                                data: 'WODocNo',
                                name: 'WODocNo'
                            },
                            {
                                data: 'SODocNo',
                                name: 'SODocNo'
                            },
                            {
                                data: 'MaterialName',
                                name: 'MaterialName'
                            },
                            {
                                data: 'MaterialCode',
                                name: 'MaterialCode'
                            },
                            {
                                data: 'Quantity',
                                name: 'Quantity'
                            },
                            {
                                data: 'Unit',
                                name: 'Unit'
                            },
                            {
                                data: 'Up',
                                name: 'Up'
                            },
                            {
                                data: 'DeliveryDate',
                                name: 'DeliveryDate'
                            },
                            {
                                data: 'Status',
                                name: 'Status'
                            },
                            {
                                data: 'Detail',
                                name: 'Detail'
                            },
                            {
                                data: null, // Kolom counter_order
                                render: function(data, type, row) {
                                    return `<input type="text" class="counter-order" disabled style="width: 50px; text-align: center;">`;
                                },
                            },
                        ]
                    });

                } else {

                    if (rdept && rplan) {
                        $('#table-rencana-plan').css('display', 'none');

                        $('#datatable-work-order').css('display', 'none');
                        $('#select_workorder').css('display', 'none');
                        $('#table-work-order').css('display', 'block');
                        $('#select_machine').css('display', 'none');
                    } else {
                        $('#select_rencana_plan').css('display', 'block');
                        $('#select_machine').css('display', 'none');
                        $('#select_workorder').css('display', 'none');

                        $('#table-machine').css('display', 'none');
                        $('#table-rencana-plan').css('display', 'block');
                    }
                }

                document.getElementById('filterButton').addEventListener('click', function() {
                    var fromDate = document.getElementById('from_date_advanced_filter_wo').value;
                    var toDate = document.getElementById('to_date_advanced_filter_wo').value;

                    var currentQueryString = window.location.search;
                    var searchParams = new URLSearchParams(currentQueryString);

                    if (fromDate) {
                        searchParams.set('from_date', fromDate);
                    }
                    if (toDate) {
                        searchParams.set('to_date', toDate);
                    }

                    var newQueryString = searchParams.toString();
                    var newURL = window.location.pathname + '?' + newQueryString;

                    window.location.href = newURL;
                });

                // if (rmachine && rdept && rplan) {

                //     $('#table-machine').css('display', 'none');
                //     $('#table-rencana-plan').css('display', 'none');
                //     $('#table-work-order').css('display', 'block');

                //     $('#header_rencana_plan').text('Pilih Item');
                //     $('#header_rencana_plan_dua').text('Filter Item Berdasarkan Date dari Sales Order');

                //     $('#select_rencana_plan').css('display', 'none');
                //     $('#select_machine').css('display', 'none');
                //     $('#select_workorder').css('display', 'block');








                //     $(document).on('click', '.btn-detail', function() {
                //         var sodocno = $(this).data('sodocno');
                //         $('#docno_detail_info').val(sodocno);


                //         $.ajax({
                //             url: 'get-information-job-order-or-process/' +
                //                 sodocno, // Append sodocno to the URL
                //             method: 'GET',
                //             beforeSend: function() {
                //                 // Tampilkan indikator loading sebelum AJAX request dijalankan
                //                 $('#loading-indicator').show();
                //                 $('#tabel-info-jo').html(
                //                     ''); // Kosongkan tabel sebelum data baru masuk
                //             },
                //             success: function(response) {

                //                 console.log(response);
                //                 // Mulai membuat struktur tabel
                //                 var table = `
                //                     <table class="table table-striped">
                //                         <thead>
                //                             <tr>
                //                                 <th>Job Order</th>
                //                                 <th>Material Code</th>
                //                                 <th>Quantity</th>
                //                                 <th>Status</th>
                //                             </tr>
                //                         </thead>
                //                         <tbody>
                //                 `;

                //                 // Loop over the response data
                //                 response.forEach(function(item) {
                //                     table += `
                //                         <tr>
                //                             <td>${item.JobOrderData.DocNo}</td>
                //                             <td>${item.JobOrderData.MaterialCode}</td>
                //                             <td>${item.JobOrderData.QtyTarget}</td>
                //                             <td>${item.JobOrderData.Status}</td>
                //                         </tr>
                //                     `;
                //                 });

                //                 // Tutup tabel
                //                 table += `
                //                         </tbody>
                //                     </table>
                //                 `;

                //                 // Masukkan tabel ke dalam div dengan id "tabel-info-jo"
                //                 $('#tabel-info-jo').html(table);
                //             },
                //             complete: function() {
                //                 // Sembunyikan indikator loading setelah request selesai
                //                 $('#loading-indicator').hide();
                //             },
                //             error: function(xhr, status, error) {
                //                 console.error("Error fetching data: ", error);
                //                 // Sembunyikan indikator loading jika terjadi error
                //                 $('#loading-indicator').hide();
                //             }
                //         });



                //         $('#detailModal').modal('show');

                //     });








                // } else if (rplan) {

                //     $('#table-machine').css('display', 'block');
                //     $('#table-rencana-plan').css('display', 'none');

                //     $('#header_rencana_plan').text('Pilih Rencana Mesin');
                //     $('#header_rencana_plan_dua').text('Pilih Rencana Mesin');

                //     $('#select_rencana_plan').css('display', 'none');
                //     $('#select_workorder').css('display', 'none');
                //     $('#select_machine').css('display', 'block');

                //     var table = $('#datatable-machine').DataTable({
                //         processing: true,
                //         serverSide: true,
                //         ajax: {
                //             url: "{{ route('master.machine-data') }}",
                //             data: function(d) {
                //                 d.rplan = rdept;
                //             },
                //             type: "POST",
                //             headers: {
                //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                //             },
                //         },
                //         lengthMenu: [25, 50, 100],
                //         select: {
                //             style: 'multiple'
                //         },
                //         columns: [{
                //                 data: 'select',
                //                 name: 'select',
                //                 orderable: false,
                //                 searchable: false,
                //                 render: function(data, type, full, meta) {
                //                     // return '<input type="checkbox" class="select-machine select-mcn" value="' + full.code + '" data-dept="'+full.dept+'">';

                //                     return '<input type="checkbox" id="select-mcn-' + full.Code +
                //                         '" class="select-machine-' + full.Code + '" value="' + full
                //                         .Code + '" data-dept="' + full.Department + '">';
                //                 }
                //             },
                //             {
                //                 data: 'Description',
                //                 name: 'Description'
                //             },
                //             {
                //                 data: 'Unit',
                //                 name: 'Unit'
                //             },
                //             {
                //                 data: 'CapacityPerHour',
                //                 name: 'CapacityPerHour'
                //             },
                //             {
                //                 data: 'Department',
                //                 name: 'Department'
                //             },
                //         ]
                //     });

                //     $('#datatable-machine').on('click', '.select-machine', function() {
                //         $('.select-machine').not(this).prop('checked', false);
                //     });


                //     $('#select_machine').on('click', function() {
                //         if ($('input[id^="select-mcn-"]:checked').length === 0) {
                //             // Menampilkan pesan jika tidak ada mesin yang dipilih
                //             Swal.fire({
                //                 title: '',
                //                 text: 'Anda belum memilih mesin. Silakan pilih mesin terlebih dahulu.',
                //                 confirmButtonText: 'OK'
                //             });
                //         } else {
                //             var selectedMachine = [];
                //             var selectedCode = [];
                //             var selectedDept = [];

                //             $('input[id^="select-mcn-"]:checked').each(function() {
                //                 selectedMachine.push($(this).val());
                //                 var rowData = table.row($(this).closest('tr')).data();
                //                 selectedCode.push(rowData.Code);
                //                 selectedDept.push(rowData.Department);
                //             });

                //             $('#selected_machines').val(selectedCode.join(', '));

                //             var currentQueryString = window.location.search;
                //             var searchParams = new URLSearchParams(currentQueryString);

                //             selectedCode.forEach(function(code) {
                //                 searchParams.append('machine', code);
                //             });

                //             var newQueryString = searchParams.toString();
                //             var newURL = window.location.pathname + '?' + newQueryString;

                //             newURL = newURL.replace('/select-machine', '');

                //             window.location.href = newURL;

                //         };
                //     });






                // } else {
                //     $('#select_rencana_plan').css('display', 'block');
                //     $('#select_machine').css('display', 'none');
                //     $('#select_workorder').css('display', 'none');

                //     $('#table-machine').css('display', 'none');
                //     $('#table-rencana-plan').css('display', 'block');
                // }

                console.log(rplan);




            });
        </script>
    @endsection
