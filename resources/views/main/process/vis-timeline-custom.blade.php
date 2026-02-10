<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Vis Timeline Custom - Process Management</title>

    <!-- Vis.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis-timeline-graph2d.min.css">

    <!-- Font Awesome untuk icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            position: relative;
        }

        .header h1 {
            margin: 0;
            font-size: 36px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header p {
            margin: 15px 0 0 0;
            font-size: 18px;
            color: #666;
            font-weight: 400;
        }

        .back-button {
            position: absolute;
            top: 30px;
            left: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }

        .controls {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: center;
        }

        .control-group {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f8f9fa;
            padding: 15px 20px;
            border-radius: 15px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .control-group:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }

        .control-group label {
            font-weight: 600;
            color: #495057;
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .control-group select {
            padding: 10px 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            background: white;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            min-width: 150px;
        }

        .control-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .refresh-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .refresh-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(40, 167, 69, 0.4);
        }

        .timeline-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        #timeline {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        /* Custom Vis Timeline Styling */
        .vis-timeline {
            border: none !important;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        }

        .vis-panel.vis-center {
            background: white !important;
        }

        .vis-panel.vis-left {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
        }

        .vis-panel.vis-left .vis-content {
            background: transparent !important;
        }

        .vis-item {
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            padding: 8px 12px !important;
            transition: all 0.3s ease !important;
        }

        .vis-item:hover {
            transform: scale(1.05) !important;
            box-shadow: 0 12px 35px rgba(0,0,0,0.25) !important;
        }

        .vis-item.vis-selected {
            box-shadow: 0 0 0 3px #667eea, 0 8px 25px rgba(0,0,0,0.15) !important;
        }

        /* Custom colors untuk setiap resource */
        .vis-item.person1 {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%) !important;
            color: white !important;
        }

        .vis-item.person2 {
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%) !important;
            color: white !important;
        }

        .vis-item.person3 {
            background: linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%) !important;
            color: white !important;
        }

        .vis-item.person4 {
            background: linear-gradient(135deg, #00BCD4 0%, #0097A7 100%) !important;
            color: white !important;
        }

        .vis-item.tool1 {
            background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%) !important;
            color: white !important;
        }

        .vis-item.tool2 {
            background: linear-gradient(135deg, #F44336 0%, #D32F2F 100%) !important;
            color: white !important;
        }

        .vis-item.person5 {
            background: linear-gradient(135deg, #E91E63 0%, #C2185B 100%) !important;
            color: white !important;
        }

        .vis-item.tool3 {
            background: linear-gradient(135deg, #795548 0%, #5D4037 100%) !important;
            color: white !important;
        }

        /* Timeline header styling */
        .vis-time-axis {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
        }

        .vis-time-axis .vis-text {
            color: #495057 !important;
            font-weight: 600 !important;
            font-size: 12px !important;
        }

        .vis-time-axis .vis-minor {
            background: #f8f9fa !important;
        }

        .vis-time-axis .vis-minor.vis-saturday {
            background: #fff3e0 !important;
        }

        .vis-time-axis .vis-minor.vis-sunday {
            background: #ffebee !important;
        }

        /* Resource panel styling */
        .vis-panel.vis-left .vis-content .vis-item {
            background: transparent !important;
            color: white !important;
            font-weight: 600 !important;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3) !important;
        }

        .vis-panel.vis-left .vis-content .vis-item.vis-group {
            background: rgba(255,255,255,0.1) !important;
            border-radius: 8px !important;
            margin: 5px 0 !important;
            padding: 10px !important;
        }

        /* Current time indicator */
        .vis-current-time {
            background-color: #f44336 !important;
            width: 3px !important;
            z-index: 1000 !important;
        }

        .vis-current-time::before {
            content: '';
            position: absolute;
            left: -6px;
            top: -6px;
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 12px solid #f44336;
        }

        /* Zoom controls */
        .zoom-controls {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            display: flex;
            gap: 15px;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }

        .zoom-btn {
            padding: 10px 20px;
            border: 2px solid #e9ecef;
            background: white;
            border-radius: 25px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: #495057;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .zoom-btn:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
        }

        .zoom-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .controls {
                flex-direction: column;
                gap: 15px;
            }

            .control-group {
                width: 100%;
                justify-content: space-between;
            }

            .header h1 {
                font-size: 28px;
            }

            .container {
                padding: 15px;
            }
        }

        /* Loading animation */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
            color: #667eea;
            font-size: 18px;
            font-weight: 600;
        }

        .loading::after {
            content: '';
            width: 20px;
            height: 20px;
            border: 2px solid #667eea;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Vis Timeline Custom</h1>
            <p>Timeline view dengan resource grouping dan styling custom yang menarik</p>
        </div>

        <div class="controls">
            <div class="control-group">
                <label for="deptFilter">
                    <i class="fas fa-building"></i> Department:
                </label>
                <select id="deptFilter">
                    <option value="">Semua Department</option>
                    <option value="people">People</option>
                    <option value="tools">Tools</option>
                </select>
            </div>

            <div class="control-group">
                <label for="machineFilter">
                    <i class="fas fa-cogs"></i> Machine:
                </label>
                <select id="machineFilter">
                    <option value="">Semua Machine</option>
                    <option value="person1">Person 1</option>
                    <option value="person2">Person 2</option>
                    <option value="person3">Person 3</option>
                    <option value="person4">Person 4</option>
                    <option value="person5">Person 5</option>
                    <option value="tool1">Tool 1</option>
                    <option value="tool2">Tool 2</option>
                    <option value="tool3">Tool 3</option>
                </select>
            </div>

            <div class="control-group">
                <label for="statusFilter">
                    <i class="fas fa-info-circle"></i> Status:
                </label>
                <select id="statusFilter">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="in-progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <button class="refresh-btn" onclick="refreshTimeline()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>

        <div class="zoom-controls">
            <span style="font-weight: 600; color: #495057; margin-right: 10px;">
                <i class="fas fa-search-plus"></i> Zoom Level:
            </span>
            <button class="zoom-btn active" onclick="setZoom('hour')">Hour</button>
            <button class="zoom-btn" onclick="setZoom('day')">Day</button>
            <button class="zoom-btn" onclick="setZoom('week')">Week</button>
            <button class="zoom-btn" onclick="setZoom('month')">Month</button>
        </div>

        <div class="timeline-container">
            <div id="timeline" class="loading">Loading Timeline...</div>
        </div>
    </div>

    <!-- Vis.js JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis-timeline-graph2d.min.js"></script>

    <script>
        // Data untuk timeline
        const resources = [
            { id: 'people', content: '👥 People', className: 'vis-group' },
            { id: 'person1', content: '👨‍💼 Person 1', group: 'people', className: 'person1' },
            { id: 'person2', content: '👩‍💼 Person 2', group: 'people', className: 'person2' },
            { id: 'person3', content: '👨‍🔧 Person 3', group: 'people', className: 'person3' },
            { id: 'person4', content: '👩‍🔧 Person 4', group: 'people', className: 'person4' },
            { id: 'person5', content: '👨‍🏭 Person 5', group: 'people', className: 'person5' },
            { id: 'tools', content: '🛠️ Tools', className: 'vis-group' },
            { id: 'tool1', content: '⚙️ Tool 1', group: 'tools', className: 'tool1' },
            { id: 'tool2', content: '🔧 Tool 2', group: 'tools', className: 'tool2' },
            { id: 'tool3', content: '📐 Tool 3', group: 'tools', className: 'tool3' }
        ];

        const events = [
            {
                id: 1,
                content: '🪚 Task 1 - Cutting',
                start: '2025-08-26T09:00:00',
                end: '2025-08-26T10:00:00',
                group: 'person1',
                className: 'person1'
            },
            {
                id: 2,
                content: '🖨️ Task 2 - Printing',
                start: '2025-08-26T10:00:00',
                end: '2025-08-26T11:00:00',
                group: 'person2',
                className: 'person2'
            },
            {
                id: 3,
                content: '✨ Task 3 - Finishing',
                start: '2025-08-26T11:00:00',
                end: '2025-08-26T12:00:00',
                group: 'tool1',
                className: 'tool1'
            },
            {
                id: 4,
                content: '📦 Task 4 - Packaging',
                start: '2025-08-26T14:00:00',
                end: '2025-08-26T16:00:00',
                group: 'person3',
                className: 'person3'
            },
            {
                id: 5,
                content: '🔍 Task 5 - Quality Check',
                start: '2025-08-26T15:00:00',
                end: '2025-08-26T17:00:00',
                group: 'tool2',
                className: 'tool2'
            },
            {
                id: 6,
                content: '🚚 Task 6 - Shipping',
                start: '2025-08-27T08:00:00',
                end: '2025-08-27T10:00:00',
                group: 'person1',
                className: 'person1'
            },
            {
                id: 7,
                content: '📋 Task 7 - Planning',
                start: '2025-08-27T13:00:00',
                end: '2025-08-27T15:00:00',
                group: 'person4',
                className: 'person4'
            },
            {
                id: 8,
                content: '⚡ Task 8 - Testing',
                start: '2025-08-28T09:00:00',
                end: '2025-08-28T11:00:00',
                group: 'tool3',
                className: 'tool3'
            },
            {
                id: 9,
                content: '🎯 Task 9 - Review',
                start: '2025-08-28T14:00:00',
                end: '2025-08-28T16:00:00',
                group: 'person5',
                className: 'person5'
            }
        ];

        let timeline;
        let currentZoom = 'hour';

        function initializeTimeline() {
            console.log('Initializing Vis Timeline Custom...');

            // Create timeline container
            const container = document.getElementById('timeline');
            container.innerHTML = '';

            // Create timeline
            timeline = new vis.Timeline(container, events, resources, {
                // Basic configuration
                start: '2025-08-26T06:00:00',
                end: '2025-08-31T22:00:00',
                timeAxis: { scale: 'hour', step: 1 },
                orientation: 'top',
                height: '600px',

                // Resource grouping
                groupOrder: 'content',
                stack: false,

                // Styling
                showCurrentTime: true,
                showMajorLabels: true,
                showMinorLabels: true,

                // Interaction
                editable: {
                    add: true,
                    updateTime: true,
                    updateGroup: true,
                    remove: true
                },

                // Zoom and navigation
                zoomMin: 1000 * 60 * 60, // 1 hour
                zoomMax: 1000 * 60 * 60 * 24 * 31, // 31 days

                // Custom styling
                itemTemplate: function(item) {
                    return '<div class="vis-item-content">' + item.content + '</div>';
                },

                // Locale
                locale: 'id'
            });

            // Event handlers
            timeline.on('click', function(properties) {
                if (properties.item) {
                    const item = timeline.itemsData.get(properties.item);
                    alert(`Event: ${item.content}\nStart: ${new Date(item.start).toLocaleString()}\nEnd: ${new Date(item.end).toLocaleString()}`);
                }
            });

            timeline.on('doubleClick', function(properties) {
                if (properties.item) {
                    const item = timeline.itemsData.get(properties.item);
                    console.log('Double clicked item:', item);
                }
            });

            timeline.on('itemover', function(properties) {
                properties.item.style.cursor = 'pointer';
            });

            // Remove loading text
            container.classList.remove('loading');

            console.log('Vis Timeline Custom initialized successfully!');
        }

        function setZoom(zoom) {
            currentZoom = zoom;

            // Update active button
            document.querySelectorAll('.zoom-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            // Set timeline scale
            let scale, step;
            switch(zoom) {
                case 'hour':
                    scale = 'hour';
                    step = 1;
                    break;
                case 'day':
                    scale = 'day';
                    step = 1;
                    break;
                case 'week':
                    scale = 'week';
                    step = 1;
                    break;
                case 'month':
                    scale = 'month';
                    step = 1;
                    break;
                default:
                    scale = 'hour';
                    step = 1;
            }

            timeline.setOptions({
                timeAxis: { scale: scale, step: step }
            });

            console.log('Zoom changed to:', zoom);
        }

        function refreshTimeline() {
            console.log('Refreshing timeline...');
            if (timeline) {
                timeline.destroy();
            }
            initializeTimeline();
        }

        // Filter functions
        function filterByDepartment(dept) {
            if (!dept) {
                timeline.setItems(events);
                return;
            }

            const filtered = events.filter(event => {
                const resource = resources.find(r => r.id === event.group);
                return resource && resource.group === dept;
            });

            timeline.setItems(filtered);
        }

        function filterByMachine(machine) {
            if (!machine) {
                timeline.setItems(events);
                return;
            }

            const filtered = events.filter(event => event.group === machine);
            timeline.setItems(filtered);
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing Vis Timeline Custom...');

            // Add filter event listeners
            document.getElementById('deptFilter').addEventListener('change', function() {
                filterByDepartment(this.value);
            });

            document.getElementById('machineFilter').addEventListener('change', function() {
                filterByMachine(this.value);
            });

            // Initialize timeline
            initializeTimeline();
        });
    </script>
</body>
</html>
