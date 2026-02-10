<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>DHTMLX Scheduler Timeline - Process Management</title>

    <!-- DHTMLX Scheduler CSS -->
    <link rel="stylesheet" href="https://cdn.dhtmlx.com/scheduler/edge/dhtmlxscheduler.css" type="text/css" charset="utf-8">

    <!-- DHTMLX Scheduler Timeline CSS -->
    <link rel="stylesheet" href="https://cdn.dhtmlx.com/scheduler/edge/ext/dhtmlxscheduler_timeline.css" type="text/css" charset="utf-8">

    <!-- DHTMLX Scheduler Units CSS -->
    <link rel="stylesheet" href="https://cdn.dhtmlx.com/scheduler/edge/ext/dhtmlxscheduler_units.css" type="text/css" charset="utf-8">

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
            position: relative;
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

        .scheduler-container {
            padding: 20px;
            min-height: 600px;
        }

        #scheduler_here {
            width: 100%;
            height: 600px;
            background: white;
        }

        /* Custom DHTMLX Scheduler Styling */
        .dhx_cal_event div {
            border-radius: 4px !important;
            font-weight: 500 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
        }

        .dhx_cal_event_line {
            border-radius: 4px !important;
        }

        .dhx_cal_event_line div {
            border-radius: 4px !important;
        }

        /* Timeline specific styling */
        .dhx_timeline_scale_header {
            background-color: #f8f9fa !important;
            border-bottom: 2px solid #dee2e6 !important;
        }

        .dhx_timeline_scale_header .dhx_scale_hour {
            background-color: #e9ecef !important;
            border-right: 1px solid #dee2e6 !important;
            font-weight: 600 !important;
        }

        .dhx_timeline_scale_header .dhx_scale_day {
            background-color: #667eea !important;
            color: white !important;
            font-weight: 600 !important;
        }

        /* Resource grouping styling */
        .dhx_cal_resource_row {
            background-color: #f8f9fa !important;
            font-weight: 600 !important;
            color: #495057 !important;
        }

        .dhx_cal_resource_cell {
            background-color: #e9ecef !important;
            border-bottom: 1px solid #dee2e6 !important;
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

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>DHTMLX Scheduler Timeline</h1>
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

            <button class="refresh-btn" onclick="refreshScheduler()">
                <i class="mdi mdi-refresh"></i> Refresh
            </button>
        </div>

        <div class="scheduler-container">
            <div id="scheduler_here"></div>
        </div>
    </div>

    <!-- DHTMLX Scheduler JavaScript -->
    <script src="https://cdn.dhtmlx.com/scheduler/edge/dhtmlxscheduler.js" type="text/javascript" charset="utf-8"></script>

    <!-- DHTMLX Scheduler Timeline Extension -->
    <script src="https://cdn.dhtmlx.com/scheduler/edge/ext/dhtmlxscheduler_timeline.js" type="text/javascript" charset="utf-8"></script>

    <!-- DHTMLX Scheduler Units Extension -->
    <script src="https://cdn.dhtmlx.com/scheduler/edge/ext/dhtmlxscheduler_units.js" type="text/javascript" charset="utf-8"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing DHTMLX Scheduler Timeline...');

            // Check if DHTMLX Scheduler is loaded
            if (typeof scheduler === 'undefined') {
                document.getElementById('scheduler_here').innerHTML = '<div class="alert alert-danger">DHTMLX Scheduler tidak dapat dimuat!</div>';
                return;
            }

            console.log('DHTMLX Scheduler loaded:', scheduler);

            try {
                // Configure DHTMLX Scheduler untuk Timeline View
                scheduler.config.xml_date = "%Y-%m-%d %H:%i";
                scheduler.config.scroll_hour = 8;
                scheduler.config.preserve_scroll = true;
                scheduler.config.drag_resize = true;
                scheduler.config.drag_move = true;
                scheduler.config.drag_create = true;
                scheduler.config.drag_lightbox = true;
                scheduler.config.auto_scheduling = true;
                scheduler.config.auto_scheduling_strict = false;
                scheduler.config.work_week = true;
                scheduler.config.work_week_days = [1, 2, 3, 4, 5, 6, 0]; // Include Sunday

                // Units View Configuration (gratis)
                scheduler.config.view_name = "units";
                scheduler.config.scroll_hour = 8;
                scheduler.config.first_hour = 6;
                scheduler.config.last_hour = 22;

                scheduler.plugins({
                    timeline: true,
                    treetimeline: true,
                    daytimeline: true
                });

                // Gunakan Week View yang tersedia di versi gratis
                scheduler.config.view_name = "week";
                scheduler.config.scroll_hour = 8;
                scheduler.config.first_hour = 6;
                scheduler.config.last_hour = 22;

                // Week view tidak memerlukan resources
                // Events akan ditampilkan berdasarkan tanggal dan waktu

                // Sample events untuk week view (tanpa resource grouping)
                var events = [
                    {
                        id: 1,
                        text: "Task 1 - Cutting (Person 1)",
                        start_date: "2025-08-26 09:00",
                        end_date: "2025-08-26 10:00",
                        color: "#4CAF50"
                    },
                    {
                        id: 2,
                        text: "Task 2 - Printing (Person 2)",
                        start_date: "2025-08-26 10:00",
                        end_date: "2025-08-26 11:00",
                        color: "#2196F3"
                    },
                    {
                        id: 3,
                        text: "Task 3 - Finishing (Tool 1)",
                        start_date: "2025-08-26 11:00",
                        end_date: "2025-08-26 12:00",
                        color: "#FF9800"
                    },
                    {
                        id: 4,
                        text: "Task 4 - Packaging (Person 3)",
                        start_date: "2025-08-26 14:00",
                        end_date: "2025-08-26 16:00",
                        color: "#9C27B0"
                    },
                    {
                        id: 5,
                        text: "Task 5 - Quality Check (Tool 2)",
                        start_date: "2025-08-26 15:00",
                        end_date: "2025-08-26 17:00",
                        color: "#F44336"
                    },
                    {
                        id: 6,
                        text: "Task 6 - Shipping (Person 1)",
                        start_date: "2025-08-27 08:00",
                        end_date: "2025-08-27 10:00",
                        color: "#00BCD4"
                    }
                ];

                                // Initialize scheduler dengan week view
                scheduler.init('scheduler_here', new Date('2025-08-26'), 'week');

                // Load data (week view tidak support resource grouping)
                scheduler.parse(events);

                console.log('DHTMLX Scheduler Timeline initialized successfully!');

                // Event handlers
                scheduler.attachEvent("onEventClick", function(id, e, node) {
                    var event = scheduler.getEvent(id);
                    console.log('Event clicked:', event.text);
                    alert('Event: ' + event.text + '\nStart: ' + event.start_date + '\nEnd: ' + event.end_date);
                    return true;
                });

                scheduler.attachEvent("onEventChanged", function(id, event, is_new) {
                    console.log('Event changed:', event.text);
                    // Here you would typically update the database
                });

                scheduler.attachEvent("onEventCreated", function(id, event) {
                    console.log('Event created:', event.text);
                    // Here you would typically save to database
                });

                scheduler.attachEvent("onEventDeleted", function(id, event) {
                    console.log('Event deleted:', event.text);
                    // Here you would typically delete from database
                });

                // Add zoom functionality dengan mouse wheel
                var zoomLevels = [
                    { x_step: 1, x_unit: "hour", x_size: 24, label: "1 Hour" },
                    { x_step: 2, x_unit: "hour", x_size: 12, label: "2 Hours" },
                    { x_step: 4, x_unit: "hour", x_size: 6, label: "4 Hours" },
                    { x_step: 6, x_unit: "hour", x_size: 4, label: "6 Hours" },
                    { x_step: 12, x_unit: "hour", x_size: 2, label: "12 Hours" }
                ];
                var currentZoomIndex = 0;

                document.getElementById('scheduler_here').addEventListener('wheel', function(e) {
                    if (e.ctrlKey || e.metaKey) {
                        e.preventDefault();

                        if (e.deltaY > 0 && currentZoomIndex < zoomLevels.length - 1) {
                            // Zoom in
                            currentZoomIndex++;
                            applyZoom();
                        } else if (e.deltaY < 0 && currentZoomIndex > 0) {
                            // Zoom out
                            currentZoomIndex--;
                            applyZoom();
                        }
                    }
                });

                function applyZoom() {
                    var zoom = zoomLevels[currentZoomIndex];
                    // Week view tidak support zoom yang kompleks
                    // Gunakan scroll_hour untuk navigasi
                    scheduler.config.scroll_hour = 6 + (currentZoomIndex * 2);
                    scheduler.setCurrentView();
                    console.log('Zoom applied:', zoom.label);
                }

            } catch (error) {
                console.error('Error initializing DHTMLX Scheduler Timeline:', error);
                document.getElementById('scheduler_here').innerHTML = '<div class="alert alert-danger">Error: ' + error.message + '</div>';
            }
        });

        function refreshScheduler() {
            console.log('Refreshing scheduler...');
            location.reload();
        }
    </script>
</body>
</html>
