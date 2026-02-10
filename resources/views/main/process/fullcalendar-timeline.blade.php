<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>FullCalendar Timeline - Process Management</title>

    @section('css')
    <!-- FullCalendar Scheduler CSS Bundle -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@5.11.3/main.min.css' rel='stylesheet' />

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
        }

        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255,255,255,0.2);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background: rgba(255,255,255,0.3);
            color: white;
            text-decoration: none;
        }

        .controls {
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .control-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .control-group label {
            font-weight: 500;
            color: #495057;
            margin: 0;
        }

        .control-group select {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            background: white;
            font-size: 14px;
        }

        .refresh-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .refresh-btn:hover {
            background: #218838;
        }

        .calendar-container {
            padding: 20px;
            min-height: 600px;
        }

        #calendar {
            min-height: 600px;
            background: white;
        }

        /* FullCalendar Custom Styling */
        .fc-toolbar {
            margin-bottom: 20px !important;
        }

        .fc-toolbar-title {
            font-size: 24px !important;
            font-weight: 600 !important;
            color: #495057 !important;
        }

        .fc-button {
            background: #667eea !important;
            border-color: #667eea !important;
            font-weight: 500 !important;
        }

        .fc-button:hover {
            background: #5a6fd8 !important;
            border-color: #5a6fd8 !important;
        }

        .fc-button-active {
            background: #4c63d2 !important;
            border-color: #4c63d2 !important;
        }

        .fc-resource-timeline-divider {
            background: #e9ecef !important;
        }

        .fc-resource-area {
            background: #f8f9fa !important;
        }

        .fc-resource-group {
            background: #e9ecef !important;
            font-weight: 600 !important;
            color: #495057 !important;
        }

        .fc-resource {
            background: white !important;
            border-bottom: 1px solid #e9ecef !important;
        }

        .fc-timeline-slot {
            background: white !important;
        }

        .fc-timeline-slot-label {
            background: #f8f9fa !important;
            border-right: 1px solid #e9ecef !important;
        }

        .fc-event {
            border-radius: 4px !important;
            border: none !important;
            font-weight: 500 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
        }

        .fc-event-title {
            font-size: 12px !important;
        }

        /* Highlight hari Minggu dengan warna merah */
        .fc-day-sun .fc-timeline-slot-label,
        .fc-day-sun .fc-timeline-slot {
            background-color: #ffebee !important;
            color: #c62828 !important;
        }

        .fc-day-sun .fc-timeline-slot-label {
            font-weight: bold !important;
        }

        /* Styling untuk weekend secara umum */
        .fc-day-sat .fc-timeline-slot-label,
        .fc-day-sat .fc-timeline-slot {
            background-color: #fff3e0 !important;
            color: #ef6c00 !important;
        }

        /* Highlight hari Minggu dengan warna merah */
        .sunday-highlight .fc-timeline-slot-label,
        .sunday-highlight .fc-timeline-slot {
            background-color: #ffebee !important;
            color: #c62828 !important;
        }

        .sunday-highlight .fc-timeline-slot-label {
            font-weight: bold !important;
        }

        .alert {
            padding: 15px;
            margin: 20px 0;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .alert-warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeaa7;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
    </style>
    @show
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>FullCalendar Timeline</h1>
            <p>Timeline view dengan resource grouping untuk manajemen proses</p>
        </div>

        <div class="controls">
            <div class="control-group">
                <label for="deptFilter">Department:</label>
                <select id="deptFilter">
                    <option value="">Semua Department</option>
                    <option value="people">People</option>
                    <option value="tools">Tools</option>
                </select>
            </div>

            <div class="control-group">
                <label for="machineFilter">Machine:</label>
                <select id="machineFilter">
                    <option value="">Semua Machine</option>
                    <option value="person1">Person 1</option>
                    <option value="person2">Person 2</option>
                    <option value="person3">Person 3</option>
                    <option value="person4">Person 4</option>
                    <option value="tool1">Tool 1</option>
                    <option value="tool2">Tool 2</option>
                </select>
            </div>

            <div class="control-group">
                <label for="statusFilter">Status:</label>
                <select id="statusFilter">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="in-progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <button class="refresh-btn" onclick="refreshCalendar()">
                <i class="mdi mdi-refresh"></i> Refresh
            </button>
        </div>

        <div class="calendar-container">
            <div id="calendar"></div>
        </div>
    </div>

    @section('scripts')
    <!-- FullCalendar Core + Scheduler - Using stable versions -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@5.11.3/main.min.js'></script>

    <!-- Fallback CDNs -->
    <script>
        if (typeof FullCalendar === 'undefined') {
            document.write('<script src="https://unpkg.com/fullcalendar@5.11.3/main.min.js"><\/script>');
            document.write('<script src="https://unpkg.com/fullcalendar-scheduler@5.11.3/main.min.js"><\/script>');
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing FullCalendar...');

            var calendarEl = document.getElementById('calendar');

            if (typeof FullCalendar === 'undefined') {
                calendarEl.innerHTML = '<div class="alert alert-danger">FullCalendar tidak dapat dimuat!</div>';
                return;
            }

            console.log('FullCalendar loaded:', FullCalendar);
            console.log('FullCalendar version:', FullCalendar.version);
            console.log('Available plugins:', Object.keys(FullCalendar));

            try {
                // In global build v5, plugins are auto-registered; no need to pass `plugins`
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'resourceTimelineWeek',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'resourceTimelineDay,resourceTimelineWeek,resourceTimelineMonth'
                    },
                    resourceAreaWidth: '25%',
                    resourcesInitiallyExpanded: true,
                    height: 'auto',
                    expandRows: true,
                    nowIndicator: true,
                    scrollTime: '08:00:00',
                    slotMinTime: '00:00:00',
                    slotMaxTime: '24:00:00',
                    locale: 'id',
                    firstDay: 1,
                    slotDuration: '00:10:00',
                    // Tampilkan dua baris label: atas = tanggal, bawah = jam
                    slotLabelFormat: [
                        { weekday: 'short', day: '2-digit', month: 'short', omitCommas: true },
                        { hour: '2-digit', minute: '2-digit', hour12: false }
                    ],
                    // Khususkan format per view
                    views: {
                        resourceTimelineDay: {
                            slotLabelFormat: [
                                { weekday: 'long', day: '2-digit', month: 'short', omitCommas: true },
                                { hour: '2-digit', minute: '2-digit', hour12: false }
                            ]
                        },
                        resourceTimelineWeek: {
                            slotLabelFormat: [
                                { weekday: 'short', day: '2-digit', month: 'short', omitCommas: true },
                                { hour: '2-digit', minute: '2-digit', hour12: false }
                            ]
                        },
                        resourceTimelineMonth: {
                            slotLabelFormat: [
                                { year: 'numeric', month: 'long' },
                                { weekday: 'short', day: '2-digit' }
                            ],
                            slotDuration: { days: 1 }
                        }
                    },
                    businessHours: {
                        daysOfWeek: [1, 2, 3, 4, 5, 6],
                        startTime: '08:00',
                        endTime: '17:00',
                    },
                    editable: true,
                    selectable: true,
                    selectMirror: true,
                    dayMaxEvents: true,
                    weekends: true,
                    // Highlight hari Minggu dengan warna merah
                    dayCellClassNames: function(arg) {
                        if (arg.date.getDay() === 0) { // 0 = Sunday
                            return ['sunday-highlight'];
                        }
                        return [];
                    },
                    // Enable drag untuk timeline
                    dragScroll: true,
                    scrollTimeReset: false,

                    // Sample data untuk demonstration
                    resources: [
                        {
                            id: 'people',
                            title: 'People',
                            children: [
                                { id: 'person1', title: 'Person 1' },
                                { id: 'person2', title: 'Person 2' },
                                { id: 'person3', title: 'Person 3' },
                                { id: 'person4', title: 'Person 4' }
                            ]
                        },
                        {
                            id: 'tools',
                            title: 'Tools',
                            children: [
                                { id: 'tool1', title: 'Tool 1' },
                                { id: 'tool2', title: 'Tool 2' }
                            ]
                        }
                    ],

                    events: [
                        {
                            id: 'event1',
                            title: 'Task 1',
                            start: '2025-08-26T09:00:00',
                            end: '2025-08-26T10:00:00',
                            resourceId: 'person1',
                            backgroundColor: '#4CAF50',
                            borderColor: '#2E7D32'
                        },
                        {
                            id: 'event2',
                            title: 'Task 2',
                            start: '2025-08-26T10:00:00',
                            end: '2025-08-26T11:00:00',
                            resourceId: 'person2',
                            backgroundColor: '#2196F3',
                            borderColor: '#1976D2'
                        },
                        {
                            id: 'event3',
                            title: 'Task 3',
                            start: '2025-08-26T11:00:00',
                            end: '2025-08-26T12:00:00',
                            resourceId: 'tool1',
                            backgroundColor: '#FF9800',
                            borderColor: '#F57C00'
                        }
                    ],

                    // Event handlers untuk Scheduler UI
                    eventClick: function(info) {
                        console.log('Event clicked:', info.event.title);
                        alert('Event: ' + info.event.title + '\nStart: ' + info.event.start.toLocaleString() + '\nEnd: ' + info.event.end.toLocaleString());
                    },

                    eventDrop: function(info) {
                        console.log('Event dropped:', info.event.title, 'to', info.event.start.toLocaleString());
                        // Here you would typically update the database
                    },

                    eventResize: function(info) {
                        console.log('Event resized:', info.event.title, 'to', info.event.end.toLocaleString());
                        // Here you would typically update the database
                    },

                    select: function(info) {
                        console.log('Date selected:', info.startStr, 'to', info.endStr);
                        var title = prompt('Enter event title:');
                        if (title) {
                            calendar.addEvent({
                                title: title,
                                start: info.startStr,
                                end: info.endStr,
                                resourceId: info.resource ? info.resource.id : null,
                                backgroundColor: '#9C27B0',
                                borderColor: '#7B1FA2'
                            });
                        }
                        calendar.unselect();
                    }
                });

                calendar.render();
                console.log('Calendar rendered successfully!');

                // Simple zoom: scroll up/down untuk zoom slotDuration
                var zoomLevels = ['06:00:00','03:00:00','02:00:00','01:00:00','00:30:00','00:15:00'];
                var currentZoomIndex = 4; // Start from 00:30:00

                calendarEl.addEventListener('wheel', function(e) {
                    // Hanya zoom jika scroll vertical (up/down)
                    if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
                        e.preventDefault(); // Prevent page scroll

                        if (e.deltaY > 0 && currentZoomIndex > 0) {
                            // Scroll down = zoom out (slot makin besar)
                            currentZoomIndex--;
                            var newDuration = zoomLevels[currentZoomIndex];
                            calendar.setOption('slotDuration', newDuration);
                            console.log('Zoom out:', newDuration);
                        } else if (e.deltaY < 0 && currentZoomIndex < zoomLevels.length - 1) {
                            // Scroll up = zoom in (slot makin kecil)
                            currentZoomIndex++;
                            var newDuration = zoomLevels[currentZoomIndex];
                            calendar.setOption('slotDuration', newDuration);
                            console.log('Zoom in:', newDuration);
                        }
                    }
                    // Scroll horizontal tetap untuk geser timeline (tidak di-prevent)
                });

                // Tambahkan pan/drag untuk timeline
                var isDragging = false;
                var startX = 0;
                var startScrollLeft = 0;

                calendarEl.addEventListener('mousedown', function(e) {
                    // Hanya jika klik di area timeline (bukan di resources)
                    if (e.target.closest('.fc-timeline-body') || e.target.closest('.fc-timegrid-body')) {
                        isDragging = true;
                        startX = e.pageX;
                        startScrollLeft = calendarEl.scrollLeft;
                        calendarEl.style.cursor = 'grabbing';
                        e.preventDefault();
                    }
                });

                calendarEl.addEventListener('mousemove', function(e) {
                    if (isDragging) {
                        var deltaX = e.pageX - startX;
                        calendarEl.scrollLeft = startScrollLeft - deltaX;
                    }
                });

                calendarEl.addEventListener('mouseup', function() {
                    isDragging = false;
                    calendarEl.style.cursor = 'grab';
                });

                calendarEl.addEventListener('mouseleave', function() {
                    isDragging = false;
                    calendarEl.style.cursor = 'grab';
                });

                // Check if calendar actually appeared
                setTimeout(function() {
                    var calendarContent = calendarEl.innerHTML;
                    console.log('Calendar content after render:', calendarContent);

                    if (calendarContent.includes('fc-') || calendarContent.includes('fullcalendar')) {
                        console.log('✅ Calendar appears to be working');
                    } else {
                        console.log('❌ Calendar may not have rendered properly');
                        calendarEl.innerHTML = '<div class="alert alert-warning">Calendar may not have loaded properly. Check console for errors.</div>';
                    }
                }, 1000);

            } catch (error) {
                console.error('Error creating calendar:', error);
                calendarEl.innerHTML = '<div class="alert alert-danger">Error: ' + error.message + '</div>';
            }
        });

        function refreshCalendar() {
            console.log('Refreshing calendar...');
            location.reload();
        }
    </script>
    @show
</body>
</html>
