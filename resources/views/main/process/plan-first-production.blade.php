@extends('main.layouts.main')
@section('title')
    Plan First Production
@endsection
@section('css')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@5.11.3/main.min.css' rel='stylesheet' />
    <style>
        .fc-timeline-slot-frame {
            height: 100%;
        }
        .fc-timeline-event {
            padding: 4px;
            font-size: 12px;
        }
        .fc-timeline-event .fc-event-main {
            padding: 2px 4px;
        }
        .fc-event-title {
            font-weight: bold;
        }
        .machine-resource {
            font-weight: bold;
            padding: 4px;
        }
        .status-pending {
            background-color: #ffc107;
            border-color: #ffc107;
        }
        .status-progress {
            background-color: #28a745;
            border-color: #28a745;
        }
        .status-completed {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }
        .fc .fc-timeline-header-row-chrono .fc-timeline-slot-frame {
            justify-content: flex-start;
        }
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">Plan First Production</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Process</a></li>
                    <li class="breadcrumb-item active">Plan First Production</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>Department</label>
                            <select class="form-control" id="department-filter">
                                <option value="">All Departments</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label>View Range</label>
                            <select class="form-control" id="view-range">
                                <option value="day" selected>Day</option>
                                <option value="week">Week</option>
                                <option value="month">Month</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4 text-right">
                        <button type="button" class="btn btn-success waves-effect waves-light mb-2" onclick="exportData()">
                            <i class="mdi mdi-file-excel"></i> Export Excel
                        </button>
                        <button type="button" class="btn btn-primary mb-2" id="save-move-btn" style="display:none;">Save Perubahan</button>
                    </div>
                </div>

                <div id="calendar"></div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Production Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tr>
                        <td>Code Plan</td>
                        <td id="modal-code-plan"></td>
                    </tr>
                    <tr>
                        <td>Material</td>
                        <td id="modal-material"></td>
                    </tr>
                    <tr>
                        <td>Quantity</td>
                        <td id="modal-quantity"></td>
                    </tr>
                    <tr>
                        <td>Start Time</td>
                        <td id="modal-start"></td>
                    </tr>
                    <tr>
                        <td>End Time</td>
                        <td id="modal-end"></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td id="modal-status"></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="editEvent()">Edit</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@5.11.3/main.min.js'></script>
    <script>
    let selectedDepartment = '';
    let movedEvents = [];

    function loadDepartments() {
        fetch("{{ route('departments.list') }}")
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById('department-filter');
                data.departments.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept;
                    option.textContent = dept;
                    select.appendChild(option);
                });
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadDepartments();
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
            initialView: 'resourceTimelineDay',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'resourceTimelineDay,resourceTimelineWeek,resourceTimelineMonth'
            },
            aspectRatio: 1.5,
            resourceAreaWidth: '15%',
            resourceAreaHeaderContent: 'Machines',
            editable: true,
            eventResourceEditable: true,
            resources: function(fetchInfo, successCallback, failureCallback) {
                fetch(`{{ route('master.machine.data') }}`)
                    .then(response => response.json())
                    .then(data => {
                        let resources = data.data.map(machine => ({
                            id: machine.Code,
                            title: machine.Description,
                            dept: machine.Department
                        }));
                        if (selectedDepartment) {
                            resources = resources.filter(r => r.dept === selectedDepartment);
                        }
                        successCallback(resources);
                    })
                    .catch(error => {
                        failureCallback(error);
                    });
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                fetch('{{ route("plan.first.data") }}')
                    .then(response => response.json())
                    .then(data => {
                        const events = data.data.map(plan => ({
                            id: plan.id,
                            resourceId: plan.code_machine,
                            title: `${plan.so_docno} | ${plan.code_item} | ${plan.quantity}`,
                            start: plan.start_jam,
                            end: plan.end_jam,
                            classNames: getStatusClass(plan.flag_status),
                            extendedProps: {
                                code_plan: plan.code_plan,
                                quantity: plan.quantity,
                                status: plan.flag_status
                            }
                        }));
                        successCallback(events);
                    })
                    .catch(error => {
                        failureCallback(error);
                    });
            },
            eventDrop: function(info) {
                const droppedEvent = info.event;
                const newResourceId = droppedEvent.getResources()[0].id;
                const allEvents = info.view.calendar.getEvents();
                const duration = info.oldEvent.end - info.oldEvent.start;

                // Find the latest ending event on the same machine (that is not the one being dropped)
                const precedingEvent = allEvents
                    .filter(e => e.getResources()[0]?.id === newResourceId && e.id !== droppedEvent.id && e.end <= droppedEvent.start)
                    .reduce((latest, current) => (!latest || current.end > latest.end) ? current : latest, null);

                let newStart = droppedEvent.start;
                // If there is a preceding event, snap to its end time
                if (precedingEvent) {
                    newStart = precedingEvent.end;
                }

                const newEnd = new Date(newStart.getTime() + duration);

                // Visually update the event's position to snap correctly
                droppedEvent.setStart(newStart);
                droppedEvent.setEnd(newEnd);

                const moved = {
                    id: droppedEvent.id,
                    new_machine: newResourceId,
                    new_start: newStart.toISOString(),
                    new_end: newEnd.toISOString()
                };

                const idx = movedEvents.findIndex(e => e.id == moved.id);
                if (idx >= 0) {
                    movedEvents[idx] = moved;
                } else {
                    movedEvents.push(moved);
                }
                document.getElementById('save-move-btn').style.display = 'inline-block';
            },
            eventClick: function(info) {
                showEventDetail(info.event);
            },
            slotMinTime: '00:00:00',
            slotMaxTime: '24:00:00',
            nowIndicator: true,
            slotDuration: '00:15:00',
            slotLabelInterval: '01:00:00'
        });

        calendar.render();

        // Handle view range changes
        document.getElementById('view-range').addEventListener('change', function(e) {
            const view = 'resourceTimeline' + e.target.value.charAt(0).toUpperCase() + e.target.value.slice(1);
            calendar.changeView(view);
        });

        document.getElementById('department-filter').addEventListener('change', function(e) {
            selectedDepartment = e.target.value;
            calendar.refetchResources();
        });

        document.getElementById('save-move-btn').addEventListener('click', function() {
            if (movedEvents.length === 0) return;
            Swal.fire({
                title: 'Menyimpan perubahan...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            fetch('/plan-first-production/move-item', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ moves: movedEvents })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Perubahan berhasil disimpan!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => { window.location.reload(); });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: data.message || 'Gagal menyimpan perubahan.'
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: err.message || 'Gagal menyimpan perubahan.'
                });
            });
        });
    });

    function getStatusClass(status) {
        switch(status) {
            case 0: return ['status-pending'];
            case 1: return ['status-progress'];
            case 2: return ['status-completed'];
            default: return [];
        }
    }

    function showEventDetail(event) {
        document.getElementById('modal-code-plan').textContent = event.extendedProps.code_plan;
        document.getElementById('modal-material').textContent = event.title;
        document.getElementById('modal-quantity').textContent = event.extendedProps.quantity;
        document.getElementById('modal-start').textContent = event.start ? event.start.toLocaleString() : '-';
        document.getElementById('modal-end').textContent = event.end ? event.end.toLocaleString() : '-';
        document.getElementById('modal-status').textContent = getStatusText(event.extendedProps.status);

        $('#eventModal').modal('show');
    }

    function getStatusText(status) {
        switch(status) {
            case 0: return 'Pending';
            case 1: return 'In Progress';
            case 2: return 'Completed';
            default: return 'Unknown';
        }
    }

    function editEvent() {
        // Implement edit functionality
        window.location.href = '/plan-first-production/edit/' + currentEventId;
    }

    function exportData() {
        // Implement export functionality
        window.location.href = '{{ route("plan.first.export") }}';
    }
    </script>
@endsection
