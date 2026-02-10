@extends('main.layouts.main')
@section('title')
    Process
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

        /* Styling untuk checkbox */
        .select-wodocno {
            cursor: pointer;
            transform: scale(1.2);
        }

        #select-all-work-orders {
            cursor: pointer;
            transform: scale(1.2);
        }

        /* Highlight row ketika checkbox dipilih */
        .table tbody tr.selected {
            background-color: #e3f2fd !important;
        }
    </style>

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Pilih Rencana Plan</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Process</a></li>
                    <li class="breadcrumb-item active">Buat Plan</li>
                </ol>
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
                                    <button type="button" class="btn btn-info" id="select_rencana_plan"> Pilih Rencana
                                        Plan </button>
                                    <button type="button" class="btn btn-info" id="select_machine"> Pilih Mesin
                                    </button>
                                    <button type="button" class="btn btn-info" id="select_workorder"
                                        onclick="selectMaterialToAc()"> Pilih Item </button>
                                </div>
                            </div>
                        </div>
                        <br>

                        <div id="table-work-order">
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
                                        <input type="date" class="form-control" name="from_date_advanced_filter_wo"
                                            id="from_date_advanced_filter_wo">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="date" class="form-control" name="to_date_advanced_filter_wo"
                                            id="to_date_advanced_filter_wo">
                                    </div>
                                    <button class="btn btn-info" type="button" id="filterButton">Filter</button>
                                </div>
                            </div>
                            <br>
                            <hr>

                            <table id="datatable-work-order" class="table table-hover nowrap">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all-work-orders" title="Select All"></th>
                                        <th>WO DocNo </th>
                                        <th>SO DocNo </th>
                                        <th>Delivery Date</th>
                                        <th>Material Code</th>
                                        <th>Name</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Up</th>
                                        <th>Status</th>
                                        <th>Detail</th>
                                        <th>Order Status</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            <input type="hidden" id="selected-work-orders" name="selected_work_orders" value="">
                            <div class="mt-2">
                                <small class="text-muted">Selected: <span id="selected-count">0</span> items</small>
                            </div>
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
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <form class="submitAkumulasi" id="submitAkumulasi">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Tanggal Rencana Plan</h5>
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

                        <!-- Modal PTG/TUM Choice - Lebih Informatif -->
                        <div class="modal fade" id="ptgTumModal" tabindex="-1" aria-labelledby="ptgTumModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content shadow-lg">
                                    <div class="modal-header bg-gradient-primary text-white">
                                        <h5 class="modal-title" id="ptgTumModalLabel">
                                            <i class="fas fa-random"></i> Pilih Proses PTG/TUM
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal"
                                            aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Data Item -->
                                        <div class="mb-3">
                                            <h6 class="mb-1"><i class="fas fa-box"></i>
                                                <span id="ptgTumItemName"></span>
                                            </h6>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-4"><b>Qty:</b> <span id="ptgTumQty"></span></div>
                                                <div class="col-md-4"><b>WO:</b> <span id="ptgTumWODocNo"></span></div>
                                                <div class="col-md-4"><b>SO:</b> <span id="ptgTumSODocNo"></span></div>
                                            </div>
                                        </div>
                                        <hr>
                                        <!-- Penjelasan -->
                                        <div class="alert alert-info py-2 mb-3">
                                            <i class="fas fa-info-circle"></i>
                                            Silakan pilih proses yang akan digunakan untuk item ini. <br>
                                            <b>PTG</b> = Potong, <b>TUM</b> = Tumbling. Anda juga bisa memilih keduanya jika
                                            diperlukan.
                                        </div>
                                        <!-- Opsi Pilihan -->
                                        <div class="d-flex justify-content-around">
                                            <button type="button" class="btn btn-outline-primary btn-lg" id="choosePTG">
                                                <i class="fas fa-cut"></i> Pakai <b>PTG</b> saja
                                            </button>
                                            <button type="button" class="btn btn-outline-success btn-lg" id="chooseTUM">
                                                <i class="fas fa-recycle"></i> Pakai <b>TUM</b> saja
                                            </button>
                                            <button type="button" class="btn btn-outline-warning btn-lg"
                                                id="chooseBoth">
                                                <i class="fas fa-layer-group"></i> Pakai <b>PTG & TUM</b>
                                            </button>
                                        </div>
                                        <div class="mt-3" id="ptgTumMessage"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <style>
                            .bg-gradient-primary {
                                background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
                            }

                            .btn-lg {
                                min-width: 180px;
                            }
                        </style>

                        <script>
                            // Fungsi untuk mengisi data modal PTG/TUM
                            function showPtgTumModal(item) {
                                console.log('ini item', item);
                                $('#ptgTumItemName').text(item.MaterialCode + ' - ' + item.MaterialName || '-');
                                // $('#ptgTumItemCode').text(item.MaterialCode || '-');
                                $('#ptgTumWODocNo').text(item.WODocNo || '-');
                                $('#ptgTumSODocNo').text(item.SODocNo || '-');
                                $('#ptgTumQty').text(item.Quantity || '-');
                                $('#ptgTumEst').text(item.Estimation || '-');
                                $('#ptgTumMessage').text(''); // atau pesan warning jika ada
                                $('#ptgTumModal').modal('show');
                            }
                        </script>

                        <!-- PTG/TUM Choice Modal -->
                        <div class="modal fade" id="ptgTumChoiceModal" tabindex="-1" role="dialog"
                            aria-labelledby="ptgTumChoiceModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="ptgTumChoiceModalLabel">Pilih Proses PTG/TUM</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                    </div>
                                    <div class="modal-body">
                                        <div id="ptgTumChoiceItemInfo"></div>
                                        <div class="form-group mt-3">
                                            <label>Pilih Proses:</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="ptgTumChoice"
                                                    id="ptg_only" value="ptg_only">
                                                <label class="form-check-label" for="ptg_only">PTG Saja</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="ptgTumChoice"
                                                    id="tum_only" value="tum_only">
                                                <label class="form-check-label" for="tum_only">TUM Saja</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="ptgTumChoice"
                                                    id="both" value="both">
                                                <label class="form-check-label" for="both">PTG dan TUM</label>
                                            </div>
                                        </div>
                                        <div id="ptgTumProcessDetails" class="mt-2"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Batal</button>
                                        <button type="button" class="btn btn-primary"
                                            id="confirmPtgTumChoice">Konfirmasi
                                            Pilihan</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Plan Preview Modal -->
                        <div class="modal fade" id="planPreviewModal" tabindex="-1" role="dialog"
                            aria-labelledby="planPreviewModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="planPreviewModalLabel">Preview Timeline Rencana
                                            Produksi</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body" id="planPreviewModalBody">
                                        <!-- Preview content will be loaded here -->
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-success" id="savePlanFromPreview">Simpan ke
                                            Database</button>
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Kembali/Edit</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
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

                const headers = ['No.', 'Data Code', 'Data Qty', 'Up', 'Delivery Date', 'WODocNo', 'SODocNo'];
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
                        <td style='width: 80px;'><input type="text" class="form-control" name="order" value="${data.counterOrder}" readonly></td>
                        <td>${data.MaterialCode}</td>
                        <td>${data.Quantity}</td>
                        <td>${data.Up}</td>
                        <td>${data.DeliveryDate}</td>
                        <td>${data.WODocNo}</td>
                        <td>${data.SODocNo}</td>
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
                const payload = {
                    data: dataToSend,
                    start_date: startDate,
                    end_date: endDate,
                    department: deptPlan,
                    machine: machineSelected,
                };

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                window.lastRequestPayload = payload;

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
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Data error. Cek console/log atau coba hubungi Team IT.',
                                showConfirmButton: true
                            });
                            return;
                        }

                        console.log('response nya adalah ', res);

                        if (res.success === false && res.options) {
                            showPtgTumChoiceModal(res);
                            return;
                        }

                        // NEW: Jika ada preview data dengan tanda PTG/TUM completed, tampilkan modal preview
                        if (res.success === false && res.preview === true && res.ptg_tum_completed === true &&
                            res.data && res.data.planPerItem) {
                            console.log('Received preview response after PTG/TUM completion:', res);

                            // Tutup modal sebelumnya
                            $("#myModal").removeClass("in");
                            $(".modal-backdrop").remove();
                            $("#myModal").hide();

                            // Generate HTML preview dari data JSON
                            const previewHtml = generatePreviewHtml(res.data);
                            $('#planPreviewModalBody').html(previewHtml);

                            // Tampilkan modal preview
                            setTimeout(() => {
                                try {
                                    $('#planPreviewModal').modal('show');
                                    console.log(
                                        'Preview modal show triggered after PTG/TUM completion');
                                } catch (error) {
                                    console.error('Error showing preview modal:', error);
                                }
                            }, 100);

                            // Simpan data untuk save
                            window.previewData = res.data;
                            console.log('Preview data saved to window.previewData');
                            return;
                        }

                        // NEW: Jika ada preview data untuk item normal (tanpa PTG/TUM), tampilkan modal preview
                        if (res.success === false && res.preview === true && res.data && res.data.planPerItem) {
                            console.log('Received preview response for normal items (no PTG/TUM):', res);

                            // Tutup modal sebelumnya
                            $("#myModal").removeClass("in");
                            $(".modal-backdrop").remove();
                            $("#myModal").hide();

                            // Generate HTML preview dari data JSON
                            const previewHtml = generatePreviewHtml(res.data);
                            $('#planPreviewModalBody').html(previewHtml);

                            // Tampilkan modal preview
                            setTimeout(() => {
                                try {
                                    $('#planPreviewModal').modal('show');
                                    console.log(
                                        'Preview modal show triggered for normal items (no PTG/TUM)'
                                    );

                                    // Recalculate times untuk memastikan urutan yang benar
                                    setTimeout(() => {
                                        console.log(
                                            '=== MANUAL RECALCULATE AFTER MODAL SHOW ===');
                                        recalculateTimesSequential();
                                        console.log(
                                            'Times recalculated after modal show for normal items'
                                        );
                                    }, 500);
                                } catch (error) {
                                    console.error('Error showing preview modal:', error);
                                }
                            }, 100);

                            // Simpan data untuk save
                            window.previewData = res.data;
                            console.log('Preview data saved to window.previewData');
                            return;
                        }

                        if (res.errors) {
                            $.each(res.errors, function(key, value) {
                                $('#' + key).next('.error-message').text(value).show();
                            });
                        } else if (res.success === true) {
                            // Response sukses - bisa preview atau save
                            console.log('Received success response:', res);

                            if (res.preview === true && res.data && res.data.planPerItem) {
                                // Ini adalah preview untuk item normal (tanpa PTG/TUM)
                                console.log('Received preview response for normal items:', res);

                                // Tutup modal sebelumnya
                                $("#myModal").removeClass("in");
                                $(".modal-backdrop").remove();
                                $("#myModal").hide();

                                // Generate HTML preview dari data JSON
                                const previewHtml = generatePreviewHtml(res.data);
                                $('#planPreviewModalBody').html(previewHtml);

                                // Tampilkan modal preview
                                setTimeout(() => {
                                    try {
                                        $('#planPreviewModal').modal('show');
                                        console.log('Preview modal show triggered for normal items');

                                        // Recalculate times untuk memastikan urutan yang benar
                                        setTimeout(() => {
                                            recalculateTimesSequential();
                                            console.log(
                                                'Times recalculated after modal show for normal items'
                                            );
                                        }, 300);
                                    } catch (error) {
                                        console.error('Error showing preview modal:', error);
                                    }
                                }, 100);

                                // Simpan data untuk save
                                window.previewData = res.data;
                                console.log('Preview data saved to window.previewData');
                                return;
                            } else {
                                // Ini adalah response save berhasil
                                console.log('Received save success response:', res);

                                $('.alert-danger').hide();
                                $("#myModal").removeClass("in");
                                $(".modal-backdrop").remove();
                                $("#myModal").hide();

                                Swal.fire({
                                    icon: 'success',
                                    title: res.message || 'Plan berhasil disimpan!',
                                    showConfirmButton: false,
                                    timer: 2000
                                }).then(function() {
                                    // to route process.bryntum-scheduler
                                    window.location.reload();
                                });

                                $('#submitAkumulasi').trigger("reset");
                                $('#myModal').modal('hide');
                            }
                        } else if (res.success === false) {
                            // Error response yang bukan preview atau PTG/TUM
                            console.log('Received error response:', res);

                            $('.alert-danger').hide();
                            $("#myModal").removeClass("in");
                            $(".modal-backdrop").remove();
                            $("#myModal").hide();

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Terjadi kesalahan saat menyimpan plan!',
                                showConfirmButton: true
                            });
                        }
                        // Jika res.success === true && res.data, sudah ditangani di atas dengan return
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Gagal',
                            text: error.message || 'Terjadi kesalahan saat submit data!',
                            icon: 'error'
                        });
                    });
            });

            // Global tracking untuk item yang sudah dipilih
            window.processedItems = window.processedItems || [];
            window.currentConflictIndex = window.currentConflictIndex || 0;

            function showPtgTumChoiceModal(res) {

                console.log('ini res', res);
                // Update progress tracking
                if (res.current_item_index) {
                    window.currentConflictIndex = res.current_item_index;
                }

                // $('#ptgTumMessage').text(res.message);
                $('#ptgTumItemName').text(res.item.MaterialName || '-');
                $('#ptgTumItemCode').text(res.item.MaterialCode || '-');
                $('#ptgTumWODocNo').text(res.item.WODocNo || '-');
                $('#ptgTumSODocNo').text(res.item.SODocNo || '-');
                $('#ptgTumQty').text(res.item.Quantity || '-');
                $('#ptgTumEst').text(res.item.Estimation || '-');
                $('#ptgTumModal').modal('show');
                $('#myModal').modal('hide');

                // Simpan data untuk submit ulang
                window.lastTumPtgItem = res.item;
                window.lastTumPtgOptions = res.options;

                // Unbind dulu supaya tidak double event
                $('#choosePTG').off('click').on('click', function() {
                    handlePtgTumChoice('PTG');
                });
                $('#chooseTUM').off('click').on('click', function() {
                    handlePtgTumChoice('TUM');
                });
                $('#chooseBoth').off('click').on('click', function() {
                    handlePtgTumChoice('BOTH');
                });
            }

            window.userChoicesGlobal = window.userChoicesGlobal || [];

            function handlePtgTumChoice(choice) {
                // Map choice ke format yang diharapkan backend
                let choiceValue;
                if (choice === 'PTG') {
                    choiceValue = 'ptg_only';
                } else if (choice === 'TUM') {
                    choiceValue = 'tum_only';
                } else if (choice === 'BOTH') {
                    choiceValue = 'both';
                }

                // Ambil semua user_choices yang sudah ada (jika ada)
                let existingChoices = window.userChoicesGlobal;

                // Tambahkan pilihan baru
                let newChoice = {
                    material_code: window.lastTumPtgItem.MaterialCode,
                    choice: choiceValue
                };

                // Cek apakah sudah ada pilihan untuk item ini
                let found = false;
                for (let i = 0; i < existingChoices.length; i++) {
                    if (existingChoices[i].material_code === window.lastTumPtgItem.MaterialCode) {
                        console.log('Updating existing choice for:', window.lastTumPtgItem.MaterialCode);
                        existingChoices[i] = newChoice;
                        found = true;
                        break;
                    }
                }

                if (!found) {
                    console.log('Adding new choice for:', window.lastTumPtgItem.MaterialCode);
                    existingChoices.push(newChoice);
                }

                // Simpan ke global
                window.userChoicesGlobal = existingChoices;

                // Debug: Log data yang akan dikirim
                console.log('Sending payload with user_choices:', existingChoices);
                console.log('Total choices:', existingChoices.length);
                console.log('Current conflict index:', window.currentConflictIndex);

                // Track item yang sudah dipilih
                window.processedItems.push(window.lastTumPtgItem.MaterialCode);
                console.log('Processed items:', window.processedItems);

                // Kirim ulang ke backend dengan user_choices yang lengkap
                submitPlanFirstWithPayload({
                    ...window.lastRequestPayload,
                    user_choices: existingChoices
                });
                // Tutup modal
                $('#ptgTumModal').modal('hide');
            }

            function submitPlanFirstWithPayload(payload) {
                // Debug: Log payload yang dikirim
                console.log('Submitting payload:', payload);

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
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
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Data error. Cek console/log atau coba hubungi Team IT.',
                                showConfirmButton: true
                            });
                            return;
                        }

                        // Handle PTG/TUM conflict
                        if (res.success === false && res.options) {
                            showPtgTumChoiceModal(res);
                            return;
                        }

                        // Handle preview after PTG/TUM completion
                        if (res.success === false && res.preview === true && res.ptg_tum_completed === true && res
                            .data && res.data.planPerItem) {
                            console.log(
                                'Received preview response after PTG/TUM completion in submitPlanFirstWithPayload:',
                                res);

                            // Tutup modal PTG/TUM
                            $('#ptgTumModal').modal('hide');

                            // Generate HTML preview dari data JSON
                            const previewHtml = generatePreviewHtml(res.data);
                            $('#planPreviewModalBody').html(previewHtml);

                            // Tampilkan modal preview
                            setTimeout(() => {
                                try {
                                    $('#planPreviewModal').modal('show');
                                    console.log('Preview modal show triggered after PTG/TUM completion');

                                    // Recalculate times untuk memastikan urutan yang benar
                                    setTimeout(() => {
                                        console.log(
                                            '=== MANUAL RECALCULATE AFTER PTG/TUM COMPLETION ===');
                                        recalculateTimesSequential();
                                        console.log('Times recalculated after modal show');

                                        // Inisialisasi drag & drop setelah modal muncul
                                        initializeDragAndDrop();
                                        console.log('Drag & drop initialized');
                                    }, 500);
                                } catch (error) {
                                    console.error('Error showing preview modal:', error);
                                }
                            }, 100);

                            // Simpan data untuk save
                            window.previewData = res.data;
                            console.log('Preview data saved to window.previewData');
                            return;
                        }

                        // Handle success save
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: res.message || 'Plan berhasil disimpan!',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(function() {
                                window.location.href = '{{ route('process.plan-first-prd') }}';

                                // window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message || 'Terjadi kesalahan saat menyimpan plan!',
                                showConfirmButton: true
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Gagal',
                            text: error.message || 'Terjadi kesalahan saat submit data!',
                            icon: 'error'
                        });
                    });
            }

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


            // Fungsi untuk generate HTML preview dengan logika sequential yang benar
            function generatePreviewHtml(data) {
                console.log('generatePreviewHtml called with data:', data);

                const {
                    planPerItem,
                    groupedByMachine
                } = data;
                console.log('planPerItem:', planPerItem);
                console.log('groupedByMachine:', groupedByMachine);



                                let html = `
                        <div class="mb-2">
                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="showDeletedProcesses()" id="restoreButton" style="display: none;">
                                <i class="fas fa-trash-restore"></i> Pulihkan Proses Dihapus
                            </button>
                        </div>

                    <!-- View Container -->
                    <div id="viewContainer">
                `;

                // Urutkan item berdasarkan prioritas (bisa diubah nanti)
                const sortedItems = Object.entries(planPerItem).sort((a, b) => {
                    // Urutkan berdasarkan WODocNo atau prioritas yang diinginkan
                    return a[1][0]?.WODocNo?.localeCompare(b[1][0]?.WODocNo) || 0;
                });

                let currentGlobalTime = null;
                const startDateInput = document.getElementById('start_date');

                if (startDateInput && startDateInput.value) {
                    // Pastikan tanggal di-parse dengan benar dan set ke jam 8 pagi
                    const [year, month, day] = startDateInput.value.split('-').map(Number);
                    currentGlobalTime = new Date(year, month - 1, day, 8, 0, 0, 0);
                    console.log('=== INITIAL SETUP ===');
                    console.log('Parsed start date:', startDateInput.value);
                    console.log('Created date object:', currentGlobalTime.toISOString());
                    console.log('Local time:', currentGlobalTime.toLocaleString('id-ID'));
                    console.log('Hours:', currentGlobalTime.getHours(), 'Minutes:', currentGlobalTime.getMinutes());
                } else {
                    currentGlobalTime = new Date();
                    currentGlobalTime.setHours(8, 0, 0, 0);
                    console.log('=== INITIAL SETUP ===');
                    console.log('Using current date with 8 AM:', currentGlobalTime.toISOString());
                    console.log('Local time:', currentGlobalTime.toLocaleString('id-ID'));
                }

                // Update timeline overview
                const startDateDisplay = currentGlobalTime.toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                html = html.replace('<span id="startDate">-</span>', `<span id="startDate">${startDateDisplay}</span>`);

                // Generate View per Item (Original View)
                let itemViewHtml = generateItemView(sortedItems, currentGlobalTime);

                // Combine views
                html += `
                    <div id="itemView" class="view-content">
                        ${itemViewHtml}
                    </div>
                `;

                // Update timeline overview dengan end date yang benar
                // Hitung total durasi dari semua proses
                let totalProcessDuration = 0;
                sortedItems.forEach(([itemCode, plans]) => {
                    plans.forEach(plan => {
                        totalProcessDuration += parseFloat(plan.Estimation);
                    });
                });

                const finalEndTime = new Date(currentGlobalTime.getTime() + (totalProcessDuration * 60 * 60 * 1000));
                const finalEndDate = finalEndTime.toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                const totalDuration = (totalProcessDuration / 24).toFixed(1);

                html = html.replace('<span id="endDate">-</span>', `<span id="endDate">${finalEndDate}</span>`);
                html = html.replace('<span id="totalDuration">Menghitung...</span>',
                    `<span id="totalDuration">${totalDuration} hari</span>`);

                // Close view container
                html += `</div>`;

                html += `
                    <style>
                        .process-row {
                            background: #f8f9fa;
                            border: 1px solid #dee2e6;
                            border-radius: 4px;
                            margin-bottom: 4px;
                            transition: all 0.3s ease;
                        }
                        .process-row:hover {
                            background: #e9ecef;
                            border-color: #007bff;
                            transform: translateY(-1px);
                            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                        }
                        .process-row.moving {
                            background: #fff3cd;
                            border-color: #ffc107;
                            animation: pulse 0.5s ease-in-out;
                        }
                        .item-section.moving {
                            background: #fff3cd;
                            border-color: #ffc107;
                            animation: pulse 0.5s ease-in-out;
                        }
                        .timeline-overview .badge {
                            font-size: 0.8rem;
                            margin-left: 5px;
                        }
                        .item-section .card-header {
                            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
                        }

                        .btn-group-vertical .btn {
                            margin-bottom: 1px;
                            padding: 2px 6px;
                            font-size: 0.75rem;
                        }
                        .btn-group-vertical .btn:last-child {
                            margin-bottom: 0;
                        }
                        .process-row .btn-group-vertical {
                            display: flex;
                            flex-direction: column;
                            gap: 1px;
                        }
                        .start-time, .end-time {
                            font-size: 0.85rem;
                            font-weight: 500;
                            color: #495057;
                        }
                        .start-time {
                            color: #28a745;
                        }
                        .end-time {
                            color: #dc3545;
                        }
                        @keyframes pulse {
                            0% { transform: scale(1); }
                            50% { transform: scale(1.02); }
                            100% { transform: scale(1); }
                        }

                        /* Styling untuk item pertama */
                        .item-section[data-item-index="0"] .card-header {
                            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
                        }

                        .process-row.border-warning {
                            border: 2px solid #ffc107 !important;
                            background: #fff3cd !important;
                        }

                        .badge-warning {
                            background-color: #ffc107 !important;
                            color: #212529 !important;
                        }

                        .badge-danger {
                            background-color: #dc3545 !important;
                            color: white !important;
                        }
                    </style>
                `;

                return html;
            }

            // Fungsi untuk generate view per item dengan waktu sequential berdasarkan PROSES TYPE
            function generateItemView(sortedItems, currentGlobalTime) {
                let html = '';

                // Hitung waktu untuk setiap proses berdasarkan TIPE PROSES (bukan per item)
                let processTimings = {};
                let currentTimeForProcessTimings = new Date(currentGlobalTime);

                // Flatten semua proses dari semua item dan urutkan berdasarkan tipe proses
                let allProcesses = [];
                sortedItems.forEach(([itemCode, plans], itemIndex) => {
                    plans.forEach((plan, processIndex) => {
                        allProcesses.push({
                            itemCode: itemCode,
                            itemIndex: itemIndex,
                            plan: plan,
                            processIndex: processIndex,
                            uniqueItemId: `${itemCode}_${plans[0]?.WODocNo || 'unknown'}`
                        });
                    });
                });

                // Urutkan berdasarkan jenis proses (CTK harus pertama!) TAPI dengan prioritas urutan visual
                const processOrder = ['CTK', 'PTG', 'TUM', 'EMB', 'PLG', 'KPS', 'STR', 'LEM', 'EPL', 'Finishing', 'Packing'];

                // Cari proses yang berada di urutan teratas secara visual
                let topVisualProcess = null;
                let topVisualPosition = Infinity;

                allProcesses.forEach(process => {
                    const element = process.processRow;
                    if (element && element.length) {
                        const position = element.offset().top;
                        if (position < topVisualPosition) {
                            topVisualPosition = position;
                            topVisualProcess = process;
                        }
                    }
                });

                if (topVisualProcess) {
                    const topProcessType = topVisualProcess.plan.Proses;
                    const topProcessIndex = processOrder.indexOf(topProcessType);

                    // Urutkan berdasarkan tipe proses, tapi prioritaskan tipe yang berada di urutan teratas
                    allProcesses.sort((a, b) => {
                        const aProcessIndex = processOrder.indexOf(a.plan.Proses);
                        const bProcessIndex = processOrder.indexOf(b.plan.Proses);

                        // Jika salah satu adalah tipe yang berada di urutan teratas, prioritaskan
                        if (aProcessIndex === topProcessIndex && bProcessIndex !== topProcessIndex) {
                            return -1; // a lebih dulu
                        }
                        if (bProcessIndex === topProcessIndex && aProcessIndex !== topProcessIndex) {
                            return 1; // b lebih dulu
                        }

                        // Jika keduanya sama atau berbeda, urutkan normal
                        if (aProcessIndex !== bProcessIndex) {
                            return aProcessIndex - bProcessIndex;
                        }

                        // Jika proses sama, urutkan berdasarkan item (item pertama dulu)
                        return a.itemIndex - b.itemIndex;
                    });
                } else {
                    // Fallback ke urutan normal jika tidak bisa deteksi posisi visual
                    allProcesses.sort((a, b) => {
                        const aProcessIndex = processOrder.indexOf(a.plan.Proses);
                        const bProcessIndex = processOrder.indexOf(b.plan.Proses);

                        if (aProcessIndex !== bProcessIndex) {
                            return aProcessIndex - bProcessIndex;
                        }

                        return a.itemIndex - b.itemIndex;
                    });
                }

                // Hitung waktu untuk setiap proses secara berurutan berdasarkan tipe
                let currentTimeForProcessType = new Date(currentGlobalTime);

                // Reset ke jam 8 pagi untuk proses pertama
                const startDateInput = document.getElementById('start_date');
                if (startDateInput && startDateInput.value) {
                    const [year, month, day] = startDateInput.value.split('-').map(Number);
                    currentTimeForProcessType = new Date(year, month - 1, day, 8, 0, 0, 0);
                    console.log('Reset to 8 AM for first process:', currentTimeForProcessType.toISOString());
                }

                allProcesses.forEach((processData, index) => {
                    const {
                        itemCode,
                        itemIndex,
                        plan,
                        processIndex,
                        uniqueItemId
                    } = processData;

                    const processStartTime = new Date(currentTimeForProcessType);
                    const processEndTime = new Date(currentTimeForProcessType.getTime() + (parseFloat(plan.Estimation) *
                        60 * 60 * 1000));

                    console.log(`Process ${index + 1}: ${plan.Proses} (Item ${itemIndex + 1})`);
                    console.log(`  Start: ${processStartTime.toISOString()}`);
                    console.log(`  End: ${processEndTime.toISOString()}`);
                    console.log(`  Duration: ${plan.Estimation} hours`);

                    // Simpan timing untuk setiap item dan proses
                    if (!processTimings[uniqueItemId]) {
                        processTimings[uniqueItemId] = {};
                    }
                    processTimings[uniqueItemId][processIndex] = {
                        startTime: processStartTime,
                        endTime: processEndTime,
                        processType: plan.Proses
                    };

                    // Update waktu untuk proses berikutnya (berdasarkan tipe proses)
                    currentTimeForProcessType = new Date(processEndTime);
                });

                // Generate HTML untuk setiap item
                sortedItems.forEach(([itemCode, plans], itemIndex) => {
                    console.log('itemCode:', itemCode);
                    console.log('plans:', plans);

                    // Set flag untuk menandai ini adalah item pertama yang perlu di-recalculate
                    if (itemIndex === 0) {
                        window.needsRecalculation = true;
                    }

                    // Buat unique identifier yang menggabungkan MaterialCode dan WODocNo
                    const uniqueItemId = `${itemCode}_${plans[0]?.WODocNo || 'unknown'}`;

                    // Ekstrak MaterialCode bersih dari itemCode (hilangkan _WODocNo suffix)
                    let cleanMaterialCode = itemCode;
                    if (itemCode.includes('_')) {
                        cleanMaterialCode = itemCode.split('_')[0];
                    }

                    // Hitung durasi total untuk item ini berdasarkan timing yang sudah dihitung
                    const itemTimings = processTimings[uniqueItemId] || {};
                    let itemStartTime = null;
                    let itemEndTime = null;

                    Object.values(itemTimings).forEach(timing => {
                        if (!itemStartTime || timing.startTime < itemStartTime) {
                            itemStartTime = timing.startTime;
                        }
                        if (!itemEndTime || timing.endTime > itemEndTime) {
                            itemEndTime = timing.endTime;
                        }
                    });

                    const totalItemDuration1 = itemStartTime && itemEndTime ?
                        (itemEndTime.getTime() - itemStartTime.getTime()) / (1000 * 60 * 60) : 0;

                    // Hitung durasi total untuk item ini
                    const totalItemDuration = plans.reduce((total, plan) => total + parseFloat(plan.Estimation), 0);

                    // Tandai item pertama yang seharusnya mulai jam 8 pagi
                    const isFirstItem = itemIndex === 0;
                    const firstItemIndicator = isFirstItem ?
                        '<span class="badge badge-warning mr-2"><i class="fas fa-star"></i> ITEM PERTAMA (8 AM)</span>' :
                        '';

                    html += `
                            <div class="item-section mb-4" data-item-code="${uniqueItemId}" data-material-code="${itemCode}" data-wo-docno="${plans[0]?.WODocNo || ''}" data-code-item="${plans[0]?.CodeItem || ''}" data-item-index="${itemIndex}">
                                <div class="card">
                                <div class="card-header ${isFirstItem ? 'bg-warning' : 'bg-info'}">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                        <h6 class="mb-0 text-white">
                                            <i class="fas fa-box"></i> <strong>WO DocNo:</strong> ${plans[0]?.WODocNo || '-'} || <strong>Material Code:</strong> ${plans[0]?.CodeItem || '-'} || <strong>Material Name:</strong> ${plans[0]?.MaterialName || '-'}
                                        </h6>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <span class="badge badge-light">
                                                <i class="fas fa-clock"></i> ${itemStartTime ? itemStartTime.toLocaleDateString('id-ID') : '-'} - ${itemEndTime ? itemEndTime.toLocaleDateString('id-ID') : '-'}
                                            </span>
                                            <span class="badge badge-info">
                                                <i class="fas fa-hourglass-half"></i> ${totalItemDuration.toFixed(2)} jam
                                            </span>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="process-timeline" data-item-code="${itemCode}">
                        `;

                    // Header untuk proses item ini
                    html += `
                            <div class="process-header mb-2">
                                <div class="row">
                                    <div class="col-md-1"><strong>Proses</strong></div>
                                <div class="col-md-1"><strong>Mesin</strong></div>
                                    <div class="col-md-2"><strong>Waktu Mulai</strong></div>
                                    <div class="col-md-2"><strong>Waktu Selesai</strong></div>
                                    <div class="col-md-1"><strong>Durasi</strong></div>
                                    <div class="col-md-1"><strong>Qty</strong></div>
                                    <div class="col-md-1"><strong>UP</strong></div>
                                    <div class="col-md-2"><strong>BOM</strong></div>
                                    <div class="col-md-1"><strong>Aksi</strong></div>
                                </div>
                            </div>
                        `;

                    // Proses untuk item ini dengan timing yang berurutan
                    plans.forEach((plan, processIndex) => {
                        console.log('plan:', plan);

                        // Ambil timing yang sudah dihitung
                        const timing = processTimings[uniqueItemId]?.[processIndex];
                        let processStartTime, processEndTime;

                        if (timing) {
                            processStartTime = timing.startTime;
                            processEndTime = timing.endTime;
                        } else {
                            // Fallback jika timing tidak ditemukan
                            processStartTime = new Date(currentGlobalTime);
                            processEndTime = new Date(currentGlobalTime.getTime() + (parseFloat(plan
                                .Estimation) * 60 * 60 * 1000));
                        }

                        console.log(
                            `Item ${itemIndex + 1} - Process ${processIndex + 1} (${plan.Proses}): Start=${processStartTime.toISOString()}, End=${processEndTime.toISOString()}, Duration=${plan.Estimation}h`
                        );

                        const startTime = processStartTime.toLocaleString('id-ID', {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        });

                        const endTime = processEndTime.toLocaleString('id-ID', {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        });

                        const duration = parseFloat(plan.Estimation);

                        // Button UP/DOWN dan HAPUS untuk semua proses - gunakan uniqueItemId
                        const upButton = `<button type="button" class="btn btn-sm btn-outline-secondary" onclick="moveProcessUp('${uniqueItemId}', ${processIndex})" title="Pindah ke atas">
                            <i class="fas fa-chevron-up"></i>
                        </button>`;

                        const downButton = `<button type="button" class="btn btn-sm btn-outline-secondary" onclick="moveProcessDown('${uniqueItemId}', ${processIndex})" title="Pindah ke bawah">
                            <i class="fas fa-chevron-down"></i>
                        </button>`;

                        const deleteButton = `<button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteProcess('${uniqueItemId}', ${processIndex})" title="Hapus proses">
                            <i class="fas fa-trash"></i>
                        </button>`;


                        html += `
                            <div class="process-row" data-item-code="${uniqueItemId}" data-process-index="${processIndex}" data-process="${plan.Proses}" data-start-time="${processStartTime.toISOString()}" data-end-time="${processEndTime.toISOString()}" data-duration="${duration}">
                                    <div class="row align-items-center py-2 border-bottom">
                                        <div class="col-md-1">
                                            <div class="d-flex align-items-center">
                                                <button type="button" class="btn btn-sm btn-outline-primary mr-2">${plan.Proses}</button>
                                                <span class="text-muted process-number" data-original-index="${processIndex}">#${processIndex + 1}</span>
                                            </div>
                                        </div>
                                    <div class="col-md-1">${plan.Machine}</div>
                                    <div class="col-md-2 start-time" data-original="${processStartTime.toISOString()}">${startTime}</div>
                                    <div class="col-md-2 end-time" data-original="${processEndTime.toISOString()}">${endTime}</div>
                                    <div class="col-md-1">${duration.toFixed(2)} jam</div>
                                    <div class="col-md-1">${plan.Quantity}</div>
                                    <div class="col-md-1">${plan.Up}</div>
                                    <div class="col-md-2">${plan.Formula}</div>
                                    <div class="col-md-1">
                                        <div class="btn-group-vertical btn-group-sm">
                                            ${upButton}
                                            <button type="button" class="btn btn-sm btn-outline-info" onclick="showProcessDetails('${uniqueItemId}', ${processIndex})" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            ${downButton}
                                            ${deleteButton}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        // Tidak perlu update currentItemTime karena sudah menggunakan timing yang sudah dihitung
                    });

                    html += `
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                });

                return html;
            }





            // Fungsi untuk menampilkan detail proses
            function showProcessDetails(itemCode, processIndex) {
                const processRow = $(`.process-row[data-item-code="${itemCode}"][data-process-index="${processIndex}"]`);
                const process = processRow.data('process');
                const machine = processRow.find('.col-md-1').eq(1).text(); // Mesin ada di kolom kedua
                const startTime = processRow.find('.start-time').text();
                const endTime = processRow.find('.end-time').text();
                const duration = processRow.find('.col-md-1').eq(2).text(); // Durasi ada di kolom ketiga
                const quantity = processRow.find('.col-md-1').eq(3).text(); // Qty ada di kolom keempat
                const up = processRow.find('.col-md-1').eq(4).text(); // UP ada di kolom kelima

                // Ambil data original untuk format yang lebih detail
                const startTimeOriginal = processRow.find('.start-time').attr('data-original');
                const endTimeOriginal = processRow.find('.end-time').attr('data-original');

                let startTimeDetail = startTime;
                let endTimeDetail = endTime;

                if (startTimeOriginal) {
                    const startDate = new Date(startTimeOriginal);
                    startTimeDetail = startDate.toLocaleString('id-ID', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        weekday: 'long'
                    });
                }

                if (endTimeOriginal) {
                    const endDate = new Date(endTimeOriginal);
                    endTimeDetail = endDate.toLocaleString('id-ID', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        weekday: 'long'
                    });
                }

                // Ambil informasi item dari data attributes
                const itemSection = processRow.closest('.item-section');
                const materialCode = itemSection.data('material-code');
                const woDocNo = itemSection.data('wo-docno');

                Swal.fire({
                    title: `Detail Proses ${process}`,
                    html: `
                            <div class="text-left">
                                <p><strong>Material Code:</strong> ${materialCode}</p>
                                <p><strong>WO DocNo:</strong> ${woDocNo}</p>
                                <p><strong>Proses:</strong> ${process}</p>
                                <p><strong>Mesin:</strong> ${machine}</p>
                                <p><strong>Waktu Mulai:</strong> ${startTimeDetail}</p>
                                <p><strong>Waktu Selesai:</strong> ${endTimeDetail}</p>
                                <p><strong>Quantity:</strong> ${quantity}</p>
                                <p><strong>Durasi:</strong> ${duration}</p>
                                <p><strong>UP:</strong> ${up}</p>
                            </div>
                        `,
                    icon: 'info',
                    confirmButtonText: 'Tutup'
                });
            }

            // Fungsi untuk inisialisasi button UP/DOWN
            function initializeDragAndDrop() {
                // Tidak perlu event listeners untuk drag & drop
                console.log('Button UP/DOWN system initialized');

                // Pastikan waktu sudah dihitung dengan benar
                setTimeout(() => {
                    console.log('=== INITIALIZE DRAG DROP - CHECKING TIMES ===');
                    const firstItemStart = $('.item-section').first().find('.start-time').first().text();
                    const secondItemStart = $('.item-section').eq(1).find('.start-time').first().text();
                    console.log(`First item start: ${firstItemStart}`);
                    console.log(`Second item start: ${secondItemStart}`);
                }, 100);
            }

            // Fungsi untuk memindahkan proses ke atas
            function moveProcessUp(itemCode, processIndex) {
                const rows = $(`.process-row[data-item-code="${itemCode}"]`);

                if (processIndex > 0) {
                    const currentRow = rows.eq(processIndex);
                    const prevRow = rows.eq(processIndex - 1);

                    // Swap positions in DOM instead of swapping HTML content
                    currentRow.insertBefore(prevRow);

                    // Update data attributes
                    currentRow.data('process-index', processIndex - 1);
                    prevRow.data('process-index', processIndex);

                    // Update button onclick attributes
                    updateButtonOnClick(itemCode, processIndex - 1, 'moveProcessUp');
                    updateButtonOnClick(itemCode, processIndex, 'moveProcessDown');

                    // Update process numbers
                    updateProcessNumbers(itemCode);

                    // Recalculate times after swap dengan delay untuk memastikan DOM sudah terupdate
                    console.log('Calling recalculateTimesSequential for UP movement - Item:', itemCode);
                    setTimeout(() => {
                        recalculateTimesSequential();
                        console.log('Recalculated after moving process UP');
                    }, 100);

                    // Visual feedback
                    currentRow.addClass('moving');
                    prevRow.addClass('moving');
                    setTimeout(() => {
                        currentRow.removeClass('moving');
                        prevRow.removeClass('moving');
                    }, 500);
                } else {
                    // Proses sudah di posisi paling atas
                    const currentRow = rows.eq(processIndex);
                    currentRow.addClass('moving');
                    setTimeout(() => {
                        currentRow.removeClass('moving');
                    }, 300);

                    Swal.fire({
                        icon: 'info',
                        title: 'Sudah di Posisi Paling Atas',
                        text: 'Proses ini sudah berada di urutan pertama!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            }

            // Fungsi untuk memindahkan proses ke bawah
            function moveProcessDown(itemCode, processIndex) {
                const rows = $(`.process-row[data-item-code="${itemCode}"]`);

                if (processIndex < rows.length - 1) {
                    const currentRow = rows.eq(processIndex);
                    const nextRow = rows.eq(processIndex + 1);

                    // Swap positions in DOM instead of swapping HTML content
                    nextRow.insertBefore(currentRow);

                    // Update data attributes
                    currentRow.data('process-index', processIndex + 1);
                    nextRow.data('process-index', processIndex);

                    // Update button onclick attributes
                    updateButtonOnClick(itemCode, processIndex + 1, 'moveProcessDown');
                    updateButtonOnClick(itemCode, processIndex, 'moveProcessUp');

                    // Update process numbers
                    updateProcessNumbers(itemCode);

                    // Recalculate times after swap dengan delay untuk memastikan DOM sudah terupdate
                    console.log('Calling recalculateTimesSequential for DOWN movement - Item:', itemCode);
                    setTimeout(() => {
                        recalculateTimesSequential();
                        console.log('Recalculated after moving process DOWN');
                    }, 100);

                    // Visual feedback
                    currentRow.addClass('moving');
                    nextRow.addClass('moving');
                    setTimeout(() => {
                        currentRow.removeClass('moving');
                        nextRow.removeClass('moving');
                    }, 500);
                } else {
                    // Proses sudah di posisi paling bawah
                    const currentRow = rows.eq(processIndex);
                    currentRow.addClass('moving');
                    setTimeout(() => {
                        currentRow.removeClass('moving');
                    }, 300);

                    Swal.fire({
                        icon: 'info',
                        title: 'Sudah di Posisi Paling Bawah',
                        text: 'Proses ini sudah berada di urutan terakhir!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            }

            // Fungsi untuk update button onclick attributes dan nomor urutan
            function updateButtonOnClick(itemCode, processIndex, direction) {
                const row = $(`.process-row[data-item-code="${itemCode}"]`).eq(processIndex);
                const upButton = row.find('.btn-outline-secondary').first();
                const downButton = row.find('.btn-outline-secondary').last();

                // Update semua button onclick dengan index yang benar
                upButton.attr('onclick', `moveProcessUp('${itemCode}', ${processIndex})`);
                downButton.attr('onclick', `moveProcessDown('${itemCode}', ${processIndex})`);
            }

            // Fungsi untuk update nomor urutan
            function updateProcessNumbers(itemCode) {
                const rows = $(`.process-row[data-item-code="${itemCode}"]`);
                rows.each(function(index) {
                    const processNumber = $(this).find('.process-number');
                    if (processNumber.length > 0) {
                        processNumber.text(`#${index + 1}`);
                    }
                });
            }

            // Fungsi untuk menghapus proses
            function deleteProcess(itemCode, processIndex) {
                const rows = $(`.process-row[data-item-code="${itemCode}"]`);
                const processToDelete = rows.eq(processIndex);
                const processName = processToDelete.find('.btn-outline-primary').text();

                // Ambil informasi item untuk konfirmasi yang lebih jelas
                const itemSection = processToDelete.closest('.item-section');
                const materialCode = itemSection.data('material-code');
                const woDocNo = itemSection.data('wo-docno');

                // Konfirmasi penghapusan
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: `Apakah Anda yakin ingin menghapus proses "${processName}" dari item ${materialCode} (WO: ${woDocNo})?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Simpan data proses yang dihapus untuk recovery
                        const deletedProcess = {
                            itemCode: itemCode,
                            materialCode: materialCode,
                            woDocNo: woDocNo,
                            processIndex: processIndex,
                            processName: processName,
                            processData: processToDelete.data('process'),
                            timestamp: new Date().toISOString()
                        };

                        // Simpan ke array global untuk recovery
                        if (!window.deletedProcesses) {
                            window.deletedProcesses = [];
                        }
                        window.deletedProcesses.push(deletedProcess);

                        // Tampilkan button restore
                        $('#restoreButton').show();

                        // Hapus elemen dari DOM
                        processToDelete.remove();

                        // Update nomor urutan
                        updateProcessNumbers(itemCode);

                        // Recalculate times setelah penghapusan
                        console.log('Calling recalculateTimesSequential for DELETE - Item:', itemCode);
                        setTimeout(() => {
                            recalculateTimesSequential();
                            console.log('Recalculated after deleting process');
                        }, 100);

                        // Update button onclick untuk semua proses yang tersisa
                        updateAllButtonOnClick(itemCode);

                        // Visual feedback
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Dihapus!',
                            text: `Proses "${processName}" telah dihapus dari item ${materialCode}.`,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            }

            // Fungsi untuk update semua button onclick setelah penghapusan
            function updateAllButtonOnClick(itemCode) {
                const rows = $(`.process-row[data-item-code="${itemCode}"]`);
                rows.each(function(index) {
                    const upButton = $(this).find('.btn-outline-secondary').first();
                    const downButton = $(this).find('.btn-outline-secondary').last();
                    const deleteButton = $(this).find('.btn-outline-danger');

                    if (upButton.length > 0) {
                        upButton.attr('onclick', `moveProcessUp('${itemCode}', ${index})`);
                    }
                    if (downButton.length > 0) {
                        downButton.attr('onclick', `moveProcessDown('${itemCode}', ${index})`);
                    }
                    if (deleteButton.length > 0) {
                        deleteButton.attr('onclick', `deleteProcess('${itemCode}', ${index})`);
                    }
                });
            }

            // Fungsi untuk menampilkan proses yang dihapus
            function showDeletedProcesses() {
                if (!window.deletedProcesses || window.deletedProcesses.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Ada Proses Dihapus',
                        text: 'Belum ada proses yang dihapus.',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                let deletedList = '';
                window.deletedProcesses.forEach((process, index) => {
                    const deleteTime = new Date(process.timestamp).toLocaleString('id-ID');
                    deletedList += `
                            <div class="mb-2 p-2 border rounded">
                                <strong>${process.processName}</strong><br>
                                <small class="text-muted">
                                    Material: ${process.materialCode} | WO: ${process.woDocNo}<br>
                                    Dihapus: ${deleteTime}
                                </small>
                                <button type="button" class="btn btn-sm btn-success float-right" onclick="restoreProcess(${index})">
                                    <i class="fas fa-undo"></i> Pulihkan
                                </button>
                            </div>
                        `;
                });

                Swal.fire({
                    title: 'Proses yang Dihapus',
                    html: `
                            <div class="text-left">
                                <p>Berikut adalah daftar proses yang telah dihapus:</p>
                                ${deletedList}
                            </div>
                        `,
                    width: '600px',
                    showConfirmButton: true,
                    confirmButtonText: 'Tutup'
                });
            }

            // Fungsi untuk memulihkan proses yang dihapus
            function restoreProcess(deletedIndex) {
                const deletedProcess = window.deletedProcesses[deletedIndex];

                // Cari item section yang sesuai berdasarkan unique identifier
                const itemSection = $(`.item-section[data-item-code="${deletedProcess.itemCode}"]`);
                if (itemSection.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memulihkan',
                        text: `Item tidak ditemukan! (${deletedProcess.materialCode} - ${deletedProcess.woDocNo})`,
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Ambil data asli dari window.originalPlanData berdasarkan material code
                const originalData = window.originalPlanData.planPerItem[deletedProcess.materialCode];
                const originalProcess = originalData[deletedProcess.processIndex];

                if (!originalProcess) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memulihkan',
                        text: 'Data proses asli tidak ditemukan!',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Generate HTML untuk proses yang dipulihkan (waktu akan dihitung ulang oleh recalculateTimes)

                // Button untuk proses yang dipulihkan
                const upButton = `<button type="button" class="btn btn-sm btn-outline-secondary" onclick="moveProcessUp('${deletedProcess.itemCode}', 0)" title="Pindah ke atas">
                        <i class="fas fa-chevron-up"></i>
                    </button>`;
                const downButton = `<button type="button" class="btn btn-sm btn-outline-secondary" onclick="moveProcessDown('${deletedProcess.itemCode}', 0)" title="Pindah ke bawah">
                        <i class="fas fa-chevron-down"></i>
                    </button>`;
                const deleteButton = `<button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteProcess('${deletedProcess.itemCode}', 0)" title="Hapus proses">
                        <i class="fas fa-trash"></i>
                    </button>`;

                const restoredProcessHtml = `
                        <div class="process-row" data-item-code="${deletedProcess.itemCode}" data-process-index="0" data-process="${originalProcess.Proses}">
                            <div class="row align-items-center py-2 border-bottom">
                                <div class="col-md-2">
                                    <div class="d-flex align-items-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary mr-2">${originalProcess.Proses}</button>
                                        <span class="text-muted process-number" data-original-index="0">#1</span>
                                    </div>
                                </div>
                                <div class="col-md-2">${originalProcess.Machine}</div>
                                <div class="col-md-2 start-time" data-original="">Menunggu perhitungan...</div>
                                <div class="col-md-2 end-time" data-original="">Menunggu perhitungan...</div>
                                <div class="col-md-1">${parseFloat(originalProcess.Estimation).toFixed(2)} jam</div>
                                <div class="col-md-1">${originalProcess.Quantity}</div>
                                <div class="col-md-1">${parseFloat(originalProcess.Estimation).toFixed(2)} jam</div>
                                <div class="col-md-1">
                                    <div class="btn-group-vertical btn-group-sm">
                                        ${upButton}
                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="showProcessDetails('${deletedProcess.itemCode}', 0)" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        ${downButton}
                                        ${deleteButton}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                // Tambahkan ke timeline (setelah header, di posisi pertama)
                const timeline = itemSection.find('.process-timeline');
                const processHeader = timeline.find('.process-header');

                if (processHeader.length > 0) {
                    // Masukkan setelah header
                    processHeader.after(restoredProcessHtml);
                } else {
                    // Jika tidak ada header, masukkan di awal
                    timeline.prepend(restoredProcessHtml);
                }

                // Update nomor urutan
                updateProcessNumbers(deletedProcess.itemCode);

                // Recalculate times
                console.log('Calling recalculateTimesSequential for RESTORE - Item:', deletedProcess.itemCode);
                setTimeout(() => {
                    recalculateTimesSequential();
                    console.log('Recalculated after restoring process');
                }, 100);

                // Update semua button onclick
                updateAllButtonOnClick(deletedProcess.itemCode);

                // Hapus dari daftar deleted processes
                window.deletedProcesses.splice(deletedIndex, 1);

                // Sembunyikan button restore jika tidak ada lagi proses yang dihapus
                if (window.deletedProcesses.length === 0) {
                    $('#restoreButton').hide();
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Dipulihkan!',
                    text: `Proses "${deletedProcess.processName}" telah dipulihkan dari item ${deletedProcess.materialCode}.`,
                    timer: 1500,
                    showConfirmButton: false
                });
            }

            // Fungsi untuk memindahkan item ke atas
            function moveItemUp(itemCode) {
                const itemSection = $(`.item-section[data-item-code="${itemCode}"]`);
                const prevItemSection = itemSection.prev('.item-section');

                if (prevItemSection.length > 0) {
                    // Swap positions
                    itemSection.insertBefore(prevItemSection);

                    console.log('=== ITEM MOVED UP - RECALCULATING ===');

                    // Recalculate times untuk semua item dengan logika sequential
                    setTimeout(() => {
                        recalculateTimesSequential();
                    }, 100);

                    // Visual feedback
                    itemSection.addClass('moving');
                    prevItemSection.addClass('moving');
                    setTimeout(() => {
                        itemSection.removeClass('moving');
                        prevItemSection.removeClass('moving');
                    }, 500);
                } else {
                    // Item sudah di posisi paling atas
                    itemSection.addClass('moving');
                    setTimeout(() => {
                        itemSection.removeClass('moving');
                    }, 300);

                    Swal.fire({
                        icon: 'info',
                        title: 'Sudah di Posisi Paling Atas',
                        text: 'Item ini sudah berada di urutan pertama!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            }

            // Fungsi untuk memindahkan item ke bawah
            function moveItemDown(itemCode) {
                const itemSection = $(`.item-section[data-item-code="${itemCode}"]`);
                const nextItemSection = itemSection.next('.item-section');

                if (nextItemSection.length > 0) {
                    // Swap positions
                    nextItemSection.insertBefore(itemSection);

                    console.log('=== ITEM MOVED DOWN - RECALCULATING ===');

                    // Recalculate times untuk semua item dengan logika sequential
                    setTimeout(() => {
                        recalculateTimesSequential();
                    }, 100);

                    // Visual feedback
                    itemSection.addClass('moving');
                    nextItemSection.addClass('moving');
                    setTimeout(() => {
                        itemSection.removeClass('moving');
                        nextItemSection.removeClass('moving');
                    }, 500);
                } else {
                    // Item sudah di posisi paling bawah
                    itemSection.addClass('moving');
                    setTimeout(() => {
                        itemSection.removeClass('moving');
                    }, 300);

                    Swal.fire({
                        icon: 'info',
                        title: 'Sudah di Posisi Paling Bawah',
                        text: 'Item ini sudah berada di urutan terakhir!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            }

            function recalculateTimes() {
                // Recalculate times untuk semua item berdasarkan urutan baru
                console.log('=== RECALCULATE ALL TIMES BASED ON NEW ORDER (LEGACY) ===');
                console.log('WARNING: Using legacy recalculateTimes, should use recalculateTimesSequential instead');

                // Ambil semua item sections yang ada
                const allItemSections = $('.item-section');
                if (allItemSections.length === 0) return;

                // Ambil start date dari form
                let currentTimeForRecalculate;
                const startDateInput = document.getElementById('start_date');

                if (startDateInput && startDateInput.value) {
                    // Pastikan tanggal di-parse dengan benar dan set ke jam 8 pagi
                    const [year, month, day] = startDateInput.value.split('-').map(Number);
                    currentTimeForRecalculate = new Date(year, month - 1, day, 8, 0, 0, 0);
                    console.log('=== RECALCULATE INITIAL SETUP ===');
                    console.log('Recalculate - Parsed start date:', startDateInput.value);
                    console.log('Recalculate - Created date object:', currentTimeForRecalculate.toISOString());
                    console.log('Recalculate - Local time:', currentTimeForRecalculate.toLocaleString('id-ID'));
                    console.log('Recalculate - Hours:', currentTimeForRecalculate.getHours(), 'Minutes:',
                        currentTimeForRecalculate.getMinutes());
                } else {
                    currentTimeForRecalculate = new Date();
                    currentTimeForRecalculate.setHours(8, 0, 0, 0);
                    console.log('=== RECALCULATE INITIAL SETUP ===');
                    console.log('Recalculate - Using current date with 8 AM:', currentTimeForRecalculate.toISOString());
                    console.log('Recalculate - Local time:', currentTimeForRecalculate.toLocaleString('id-ID'));
                }

                console.log('=== RECALCULATE TIMES ===');
                console.log('Start time:', currentTimeForRecalculate.toISOString());
                console.log('Start time should be 8 AM:', currentTimeForRecalculate.getHours(), ':', currentTimeForRecalculate
                    .getMinutes());

                // Tidak perlu mengurutkan per proses lagi, langsung hitung per item berdasarkan posisi di DOM
                console.log('=== RECALCULATE LANGSUNG PER ITEM ===');

                // Hitung waktu untuk setiap item secara berurutan (bukan per proses)
                console.log('=== RECALCULATE WAKTU PER ITEM ===');

                // Gunakan allItemSections yang sudah dideklarasikan di atas

                allItemSections.each(function(itemIndex) {
                    const itemSection = $(this);
                    const uniqueItemId = itemSection.data('item-code');

                    // Item pertama (posisi teratas) harus mulai jam 8 pagi
                    if (itemIndex === 0) {
                        console.log('=== RECALCULATE ITEM PERTAMA (POSISI TERATAS) ===');
                        console.log('Current time before reset:', currentTimeForRecalculate.toISOString());
                        console.log('Should start at 8 AM on start date');

                        // Reset ke jam 8 pagi untuk item pertama
                        const startDateInput = document.getElementById('start_date');
                        if (startDateInput && startDateInput.value) {
                            const [year, month, day] = startDateInput.value.split('-').map(Number);
                            currentTimeForRecalculate = new Date(year, month - 1, day, 8, 0, 0, 0);
                            console.log('Recalculate - Reset to 8 AM for first item:', currentTimeForRecalculate
                                .toISOString());
                        }
                    }

                    // Hitung waktu untuk setiap proses dalam item ini
                    const processRows = itemSection.find('.process-row');
                    processRows.each(function(processIndex) {
                        const processRow = $(this);
                        const durationElement = processRow.find('.col-md-1').eq(2);
                        const duration = parseFloat(durationElement.text().replace(' jam', ''));

                        const startTime = new Date(currentTimeForRecalculate);
                        const endTime = new Date(currentTimeForRecalculate.getTime() + (duration * 60 * 60 *
                            1000));

                        console.log(`Recalculate Item ${itemIndex + 1} - Process ${processIndex + 1}`);
                        console.log(`  Start: ${startTime.toISOString()}`);
                        console.log(`  End: ${endTime.toISOString()}`);
                        console.log(`  Duration: ${duration} hours`);

                        // Update display
                        const startTimeElement = processRow.find('.start-time');
                        const endTimeElement = processRow.find('.end-time');

                        if (startTimeElement.length > 0) {
                            const formattedStartTime = startTime.toLocaleString('id-ID', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            });
                            startTimeElement.text(formattedStartTime);
                            startTimeElement.attr('data-original', startTime.toISOString());
                        }

                        if (endTimeElement.length > 0) {
                            const formattedEndTime = endTime.toLocaleString('id-ID', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            });
                            endTimeElement.text(formattedEndTime);
                            endTimeElement.attr('data-original', endTime.toISOString());
                        }

                        // Update waktu untuk proses berikutnya dalam item yang sama
                        currentTimeForRecalculate = new Date(endTime);
                    });

                    console.log(`Recalculate Item ${itemIndex + 1} selesai, lanjut ke item berikutnya`);

                    // Simpan waktu akhir item untuk digunakan item berikutnya
                    const lastProcessRow = itemSection.find('.process-row').last();
                    const lastEndTimeElement = lastProcessRow.find('.end-time');
                    if (lastEndTimeElement.length > 0) {
                        const lastEndTimeOriginal = lastEndTimeElement.attr('data-original');
                        if (lastEndTimeOriginal) {
                            currentTimeForRecalculate = new Date(lastEndTimeOriginal);
                            console.log(
                                `Item ${itemIndex + 1} selesai pada: ${currentTimeForRecalculate.toISOString()}`);
                            console.log(
                                `Item ${itemIndex + 2} akan mulai pada: ${currentTimeForRecalculate.toISOString()}`);
                        }
                    }
                });

                console.log('=== END RECALCULATE ALL TIMES ===');
            }

            // Fungsi baru untuk recalculate times dengan logika sequential berdasarkan PROSES TYPE
            function recalculateTimesSequential() {
                console.log('=== RECALCULATE TIMES SEQUENTIAL - PROSES TYPE ===');

                // Ambil semua item sections yang ada
                const allItemSections = $('.item-section');
                if (allItemSections.length === 0) return;

                // Ambil start date dari form
                let currentTimeForSequential;
                const startDateInput = document.getElementById('start_date');

                if (startDateInput && startDateInput.value) {
                    const [year, month, day] = startDateInput.value.split('-').map(Number);
                    currentTimeForSequential = new Date(year, month - 1, day, 8, 0, 0, 0);
                    console.log('Sequential - Start time set to 8 AM:', currentTimeForSequential.toISOString());
                } else {
                    currentTimeForSequential = new Date();
                    currentTimeForSequential.setHours(8, 0, 0, 0);
                    console.log('Sequential - Using current date with 8 AM:', currentTimeForSequential.toISOString());
                }

                // Flatten semua proses dari semua item dan urutkan berdasarkan tipe proses
                let allProcesses = [];
                allItemSections.each(function(itemIndex) {
                    const itemSection = $(this);
                    const processRows = itemSection.find('.process-row');

                    processRows.each(function(processIndex) {
                        const processRow = $(this);
                        const processType = processRow.data('process');
                        const durationElement = processRow.find('.col-md-1').eq(2);
                        const duration = parseFloat(durationElement.text().replace(' jam', ''));

                        allProcesses.push({
                            itemIndex: itemIndex,
                            processIndex: processIndex,
                            processType: processType,
                            duration: duration,
                            itemSection: itemSection,
                            processRow: processRow
                        });
                    });
                });

                // Urutkan berdasarkan posisi visual di DOM (yang sebenarnya dilihat user)
                allProcesses.sort((a, b) => {
                    const aElement = a.processRow;
                    const bElement = b.processRow;

                    if (aElement.length && bElement.length) {
                        const aPosition = aElement.offset().top;
                        const bPosition = bElement.offset().top;

                        // Jika posisi berbeda, urutkan berdasarkan posisi visual
                        if (aPosition !== bPosition) {
                            return aPosition - bPosition;
                        }

                        // Jika posisi sama, urutkan berdasarkan index dalam item yang sama
                        if (a.itemIndex === b.itemIndex) {
                            return a.processIndex - b.processIndex;
                        }

                        // Jika item berbeda, urutkan berdasarkan item index
                        return a.itemIndex - b.itemIndex;
                    }

                    // Fallback jika tidak bisa dapat posisi
                    if (a.itemIndex !== b.itemIndex) {
                        return a.itemIndex - b.itemIndex;
                    }
                    return a.processIndex - b.processIndex;
                });

                console.log('Urutan proses berdasarkan posisi visual:', allProcesses.map(p =>
                    `${p.processType} (Item ${p.itemIndex + 1}, Process ${p.processIndex + 1})`));

                console.log('=== HITUNG WAKTU BERDASARKAN URUTAN VISUAL ===');
                console.log('Proses pertama akan mulai jam 8 pagi:', allProcesses[0] ?
                    `${allProcesses[0].processType} (Item ${allProcesses[0].itemIndex + 1}, Process ${allProcesses[0].processIndex + 1})` : 'Tidak ada proses');

                // Hitung waktu untuk setiap proses secara berurutan berdasarkan urutan visual
                allProcesses.forEach((processData, index) => {
                    const {
                        itemIndex,
                        processIndex,
                        processType,
                        duration,
                        processRow
                    } = processData;

                    // Proses pertama (index 0) harus mulai jam 8 pagi
                    if (index === 0) {
                        console.log(`=== PROSES PERTAMA (URUTAN TERATAS) ===`);
                        console.log(
                            `Process ${index + 1}: ${processType} (Item ${itemIndex + 1}, Process ${processIndex + 1}) - HARUS MULAI JAM 8 PAGI`);

                        // Reset ke jam 8 pagi untuk proses pertama
                        const startDateInput = document.getElementById('start_date');
                        if (startDateInput && startDateInput.value) {
                            const [year, month, day] = startDateInput.value.split('-').map(Number);
                            currentTimeForSequential = new Date(year, month - 1, day, 8, 0, 0, 0);
                            console.log(`Reset to 8 AM for first process: ${currentTimeForSequential.toISOString()}`);
                        }
                    } else {
                        console.log(
                            `Process ${index + 1}: ${processType} (Item ${itemIndex + 1}, Process ${processIndex + 1}) - Lanjut dari proses sebelumnya`
                            );
                    }

                    const startTime = new Date(currentTimeForSequential);
                    const endTime = new Date(currentTimeForSequential.getTime() + (duration * 60 * 60 * 1000));

                    console.log(
                        `  ${processType} (Item ${itemIndex + 1}, Process ${processIndex + 1}): Start: ${startTime.toLocaleString('id-ID')} - End: ${endTime.toLocaleString('id-ID')} (${duration}h)`
                        );

                    // Update display
                    const startTimeElement = processRow.find('.start-time');
                    const endTimeElement = processRow.find('.end-time');

                    if (startTimeElement.length > 0) {
                        const formattedStartTime = startTime.toLocaleString('id-ID', {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        });
                        startTimeElement.text(formattedStartTime);
                        startTimeElement.attr('data-original', startTime.toISOString());
                        console.log(
                            `Updated start time for ${processType} (Item ${itemIndex + 1}, Process ${processIndex + 1}): ${formattedStartTime}`
                            );
                    }

                    if (endTimeElement.length > 0) {
                        const formattedEndTime = endTime.toLocaleString('id-ID', {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        });
                        endTimeElement.text(formattedEndTime);
                        endTimeElement.attr('data-original', endTime.toISOString());
                        console.log(
                            `Updated end time for ${processType} (Item ${itemIndex + 1}, Process ${processIndex + 1}): ${formattedEndTime}`
                            );
                    }

                    // Update waktu untuk proses berikutnya (berdasarkan urutan visual)
                    currentTimeForSequential = new Date(endTime);
                    console.log(`  Next process will start at: ${currentTimeForSequential.toLocaleString('id-ID')}`);
                });

                console.log('=== END RECALCULATE TIMES SEQUENTIAL ===');

                // Force refresh tampilan untuk memastikan perubahan terlihat
                setTimeout(() => {
                    console.log('=== FORCE REFRESH DISPLAY ===');
                    $('.start-time, .end-time').each(function() {
                        const element = $(this);
                        const original = element.attr('data-original');
                        if (original) {
                            const date = new Date(original);
                            const formatted = date.toLocaleString('id-ID', {
                                year: 'numeric',
                                month: '2-digit',
                                day: '2-digit',
                                hour: '2-digit',
                                minute: '2-digit',
                                second: '2-digit'
                            });
                            element.text(formatted);
                            console.log(`Force refresh: ${formatted}`);
                        }
                    });

                    // Update visual indicator untuk proses pertama
                    $('.process-row').removeClass('border-warning');
                    const firstProcessRow = $('.process-row').first();
                    firstProcessRow.addClass('border-warning');
                    console.log('Updated visual indicator for first process:', firstProcessRow.find(
                        '.btn-outline-primary').text().trim());


                }, 100);
            }

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
                        columns: [{
                                data: 'WODocNo',
                                name: 'select',
                                orderable: false,
                                searchable: false,
                                render: function(data, type, full, meta) {
                                    return '<input type="checkbox" id="select-wo-' + data +
                                        '" class="select-wodocno" value="' + data +
                                        '"> <label for="select-wo-' + data + '">&nbsp;</label>';
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
                                data: 'DeliveryDate',
                                name: 'DeliveryDate'
                            },
                            {
                                data: 'MaterialCode',
                                name: 'MaterialCode'
                            },
                            {
                                data: 'MaterialName',
                                name: 'MaterialName'
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
                                data: 'Status',
                                name: 'Status'
                            },
                            {
                                data: 'Detail',
                                name: 'Detail'
                            },
                            {
                                data: null,
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


                // Event handler untuk tombol save dari preview
                $('#savePlanFromPreview').on('click', function() {
                    if (!window.previewData) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Data preview tidak ditemukan!',
                            showConfirmButton: true
                        });
                        return;
                    }

                    // Kirim data ke endpoint save
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    fetch('save-plan-from-preview', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({
                                plan_data: window.previewData
                            })
                        })
                        .then(response => response.json())
                        .then(res => {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: res.message || 'Plan berhasil disimpan ke database!',
                                    showConfirmButton: false,
                                    timer: 2000
                                }).then(function() {
                                    $('#planPreviewModal').modal('hide');
                                    window.location.href =
                                        '{{ route('process.plan-first-prd') }}';

                                    //
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: res.message || 'Terjadi kesalahan saat menyimpan plan!',
                                    showConfirmButton: true
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Gagal',
                                text: error.message || 'Terjadi kesalahan saat menyimpan data!',
                                icon: 'error'
                            });
                        });
                });

                // Event handler untuk multiple selection checkbox
                $(document).on('change', '.select-wodocno', function() {
                    var selectedValues = [];
                    $('.select-wodocno:checked').each(function() {
                        selectedValues.push($(this).val());
                    });

                    // Simpan nilai yang dipilih ke variabel global atau hidden input
                    $('#selected-work-orders').val(selectedValues.join(','));

                    // Update counter
                    $('#selected-count').text(selectedValues.length);

                    // Update visual row yang dipilih
                    var row = $(this).closest('tr');
                    if ($(this).is(':checked')) {
                        row.addClass('selected');
                    } else {
                        row.removeClass('selected');
                    }

                    // Update counter jika ada
                    $('.counter-order').each(function() {
                        var row = $(this).closest('tr');
                        var checkbox = row.find('.select-wodocno');
                        if (checkbox.is(':checked')) {
                            $(this).prop('disabled', false);
                        } else {
                            $(this).prop('disabled', true);
                            $(this).val('');
                        }
                    });
                });

                // Select All checkbox functionality
                $(document).on('change', '#select-all-work-orders', function() {
                    var isChecked = $(this).is(':checked');
                    $('.select-wodocno').prop('checked', isChecked).trigger('change');

                    // Update counter
                    var totalRows = $('.select-wodocno').length;
                    $('#selected-count').text(isChecked ? totalRows : 0);

                    // Update visual untuk semua row
                    if (isChecked) {
                        $('.select-wodocno').closest('tr').addClass('selected');
                    } else {
                        $('.select-wodocno').closest('tr').removeClass('selected');
                    }
                });

                // Function untuk mendapatkan nilai yang dipilih
                function getSelectedWorkOrders() {
                    var selectedValues = [];
                    $('.select-wodocno:checked').each(function() {
                        selectedValues.push($(this).val());
                    });
                    return selectedValues;
                }

                // Function untuk mendapatkan nilai yang dipilih sebagai string
                function getSelectedWorkOrdersString() {
                    return getSelectedWorkOrders().join(',');
                }

            });
        </script>
    @endsection
