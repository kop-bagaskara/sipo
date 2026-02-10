@if (isset($machines) && count($machines) > 0)
    <style>
        .no-job-order {
            background-color: #ffebee !important;
            /* Light pink background */
        }
    </style>
    <div class="accordion" id="accordion-{{ $date ?? 'default' }}">
        @foreach ($machines as $index => $machine)
            <div class="card mb-2">
                <div class="card-header bg-light" id="heading-{{ $date }}-{{ $index }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <a class="btn btn-link text-dark" data-toggle="collapse"
                                data-target="#collapse-{{ $date }}-{{ $index }}" aria-expanded="false"
                                aria-controls="collapse-{{ $date }}-{{ $index }}">
                                <strong>{{ $machine['machine'] }}</strong>
                                <button class="btn btn-sm btn-primary ml-2">{{ $machine['item_count'] }} Item</button>
                            </a>
                        </h5>
                    </div>
                </div>
                <div id="collapse-{{ $date }}-{{ $index }}" class="collapse"
                    aria-labelledby="heading-{{ $date }}-{{ $index }}"
                    data-parent="#accordion-{{ $date }}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-2">
                                    <div class="card-header bg-white">
                                        <h6 class="mb-0">
                                            <button class="btn btn-link text-dark" type="button" data-toggle="collapse"
                                                data-target="#process-{{ $date }}-{{ $index }}"
                                                aria-expanded="false">
                                                <i class="mdi mdi-cog mr-2"></i>Proses
                                            </button>
                                        </h6>
                                    </div>

                                    <div id="process-{{ $date }}-{{ $index }}" class="collapse show">
                                        <div class="card-body">
                                            {{ $machine['process'] }}
                                            <hr>
                                            <button type="button" class="btn btn-primary w-100 send-machine-jo" id="send-machine-jo"
                                                data-machine="{{ $machine['machine'] }}"
                                                data-current-date="{{ $date }}">Kirim Job Order ke Mesin
                                                {{ $machine['machine'] }}</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-2">
                                    <div class="card-header bg-white">
                                        <h6 class="mb-0">
                                            <button class="btn btn-link text-dark" type="button" data-toggle="collapse"
                                                data-target="#info-{{ $date }}-{{ $index }}"
                                                aria-expanded="false">
                                                <i class="mdi mdi-information mr-2"></i>Informasi
                                            </button>
                                        </h6>
                                    </div>
                                    <div id="info-{{ $date }}-{{ $index }}" class="collapse show">
                                        <div class="card-body">
                                            <p class="mb-2"><strong>Dibuat Oleh:</strong>
                                                {{ $machine['created_by'] }}</p>
                                            <p class="mb-0"><strong>Terakhir Diperbarui:</strong>
                                                {{ $machine['updated_at'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Item Details -->
                        <div class="card mt-3">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">
                                    <button class="btn btn-link text-dark" type="button" data-toggle="collapse"
                                        data-target="#items-{{ $date }}-{{ $index }}"
                                        aria-expanded="false">
                                        <i class="mdi mdi-format-list-bulleted mr-2"></i>Daftar Item
                                    </button>
                                </h6>
                            </div>
                            <div id="items-{{ $date }}-{{ $index }}" class="collapse">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Code Item</th>
                                                    <th style="width: 200px;">Name Item</th>
                                                    <th>Qty</th>
                                                    <th>Capacity</th>
                                                    <th>Start Time</th>
                                                    <th>Setup</th>
                                                    <th>Istirahat</th>
                                                    <th>End Time</th>
                                                    <th>WO</th>
                                                    <th>SO</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($machine['items'] as $item)
                                                    <tr
                                                        class="{{ empty($item['joborder_docno']) ? 'no-job-order' : '' }}">
                                                        <td>{{ $item['code_item'] }}</td>
                                                        <td>{{ $item['material_name'] }}</td>
                                                        <td>{{ $item['qty_plan'] }}</td>
                                                        <td>{{ $item['capacity'] }}</td>
                                                        <td>{{ $item['start_time'] }}</td>
                                                        <td>{{ $item['setup_time'] }}</td>
                                                        <td>{{ $item['istirahat_time'] }}</td>
                                                        <td>{{ $item['end_time'] }}</td>
                                                        <td>{{ $item['wo_docno'] }}</td>
                                                        <td>{{ $item['so_docno'] }}</td>
                                                        <td>{{ $item['joborder_docno'] }}</td>
                                                        <td>
                                                            <button type="button"
                                                                class="btn btn-sm btn-warning change-dates"
                                                                data-toggle="modal" data-target="#changeDateModals"
                                                                data-item-id="{{ $item['code_plan'] }}"
                                                                data-id-plan="{{ $item['id_plan_harian'] }}"
                                                                data-current-date="{{ $date }}"
                                                                data-item-name="{{ $item['material_name'] }}"
                                                                data-machine="{{ $machine['machine'] }}">
                                                                <i class="mdi mdi-calendar-edit"></i>
                                                            </button>
                                                            {{-- <button type="button"
                                                                class="btn btn-sm btn-danger mark-urgent"
                                                                data-item-id="{{ $item['code_plan'] }}"
                                                                data-is-urgent="{{ isset($item['is_urgent']) && $item['is_urgent'] ? '1' : '0' }}">
                                                                <i class="mdi mdi-alert"></i>
                                                            </button> --}}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>



    <script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js"></script>
    <script>
        $(document).ready(function() {
            // Show/hide mesin tujuan
            $('#transferMachine').change(function() {
                if ($(this).is(':checked')) {
                    $('.machine-transfer-group').show();
                    $('#newMachine').prop('required', true);
                } else {
                    $('.machine-transfer-group').hide();
                    $('#newMachine').prop('required', false);
                    $('#machinePlanPreview').hide();
                    if (window.hotMachinePlan) window.hotMachinePlan.loadData([]);
                }
            });

            let itemToMove = null;

            // Simpan data item yang akan dipindah saat modal dibuka
            $(document).on('click', '.change-dates', function() {
                var machine = $(this).data('machine');
                var planId = $(this).data('id-plan');
                $('#currentMachineName').val(machine);
                $('#planId').val(planId);
                // Reset pindah mesin
                $('#transferMachine').prop('checked', false);
                $('.machine-transfer-group').hide();
                $('#newMachine').prop('required', false);
                $('#machinePlanPreview').hide();
                if (window.hotMachinePlan) window.hotMachinePlan.loadData([]);

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

            // Tampilkan plan di mesin tujuan saat mesin/tanggal dipilih
            $('#newMachine, #newDate').on('change', function() {
                const newMachine = $('#newMachine').val();
                const newDate = $('#newDate').val();

                if (newMachine && newDate) {

                    $.ajax({
                        url: "{{ route('plan-harians.data') }}",
                        type: 'POST',
                        data: {
                            date: newDate
                        },
                        success: function(response) {
                            let planData = [];
                            response.data.forEach(function(dateGroup) {
                                if (dateGroup.date === newDate) {
                                    dateGroup.machines.forEach(function(machineGroup) {
                                        if (machineGroup.machine ===
                                            newMachine) {
                                            machineGroup.items.forEach(function(
                                                item) {
                                                console.log('item',
                                                    item);
                                                planData.push({
                                                    item: item
                                                        .material_name,
                                                    qty: item
                                                        .qty_plan,
                                                    capacity: item
                                                        .capacity,
                                                    start: item
                                                        .start_time,
                                                    setup: item
                                                        .setup_time,
                                                    istirahat: item
                                                        .istirahat_time,
                                                    end: item
                                                        .end_time,
                                                    wo: item
                                                        .wo_docno,
                                                    so: item
                                                        .so_docno
                                                });
                                            });
                                        }
                                    });
                                }
                            });
                            $('#machinePlanPreview').show();
                            hotMachinePlan.loadData(planData);
                        }
                    });
                } else {
                    $('#machinePlanPreview').hide();
                    hotMachinePlan.loadData([]);
                }
            });

            // Saat tombol preview pindah plan diklik, tambahkan item ke baris terakhir Handsontable
            $('#previewMovePlan').click(function() {
                if (!itemToMove) return;
                let data = window.hotMachinePlan.getSourceData();
                console.log('data', data);
                // Cek apakah item sudah ada di preview, jika belum baru tambahkan
                let alreadyExists = data.some(row => row.wo === itemToMove.wo && row.so === itemToMove.so);
                if (!alreadyExists) {
                    data.push(itemToMove);
                    window.hotMachinePlan.loadData(data);
                }
            });

            // Pastikan event handler hanya didaftarkan sekali
            $('#saveDateChange').off('click').on('click', function() {
                // Get the current item name
                const itemName = $('#itemName').val();

                // Find the row in Handsontable that matches this item
                const tableData = hotMachinePlan.getSourceData();
                const itemRow = tableData.find(row => row.item === itemName);

                // Get the latest start and end times if the item exists in the table
                let latestStart = null;
                let latestEnd = null;

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
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                html: response.message ||
                                    'Terjadi kesalahan saat mengubah plan',
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
                                            'X-CSRF-TOKEN': $(
                                                    'meta[name="csrf-token"]')
                                                .attr('content')
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

            $('.send-machine-jo').off('click').on('click', function() {
                // alert('1');
                var machine = $(this).data('machine');
                // var idPlan = $(this).data('id-plan');
                var currentDate = $(this).data('current-date');
                // var itemName = $(this).data('item-name');

                $.ajax({
                    url: "{{ route('send-machine-joborder.data') }}",
                    method: 'POST',
                    data: {
                        machine: machine,
                        current_date: currentDate
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



        });
    </script>
@else
    <div class="alert alert-info">
        Tidak ada data mesin untuk tanggal ini.
    </div>
@endif
