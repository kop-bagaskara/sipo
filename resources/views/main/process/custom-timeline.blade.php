<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Custom Timeline - Process Management</title>

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

        .timeline-container {
            padding: 20px;
            overflow-x: auto;
        }

        .timeline {
            min-width: 1200px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
        }

        .timeline-header {
            display: flex;
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .resource-header {
            width: 200px;
            min-width: 200px;
            padding: 15px;
            background: #e9ecef;
            border-right: 1px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }

        .time-header {
            display: flex;
            flex: 1;
        }

        .time-slot {
            flex: 1;
            min-width: 60px;
            padding: 10px 5px;
            text-align: center;
            background: #f8f9fa;
            border-right: 1px solid #dee2e6;
            font-size: 12px;
            font-weight: 500;
        }

        .timeline-body {
            display: flex;
        }

        .resource-column {
            width: 200px;
            min-width: 200px;
            border-right: 1px solid #dee2e6;
        }

        .resource-group {
            background: #f8f9fa;
            padding: 10px 15px;
            font-weight: 600;
            color: #495057;
            border-bottom: 1px solid #dee2e6;
        }

        .resource-item {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            background: white;
        }

        .timeline-grid {
            flex: 1;
            display: flex;
            position: relative;
        }

        .time-column {
            flex: 1;
            min-width: 60px;
            border-right: 1px solid #e9ecef;
            position: relative;
        }

        .time-slot-grid {
            height: 50px;
            border-bottom: 1px solid #f0f0f0;
            position: relative;
        }

        .event {
            position: absolute;
            background: #4CAF50;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 10;
            min-width: 80px;
            text-align: center;
        }

        .event:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .event.person1 { background: #4CAF50; }
        .event.person2 { background: #2196F3; }
        .event.person3 { background: #9C27B0; }
        .event.tool1 { background: #FF9800; }
        .event.tool2 { background: #F44336; }

        .current-time {
            position: absolute;
            left: 0;
            right: 0;
            height: 2px;
            background: #f44336;
            z-index: 20;
        }

        .current-time::before {
            content: '';
            position: absolute;
            left: -5px;
            top: -4px;
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 8px solid #f44336;
        }

        .zoom-controls {
            padding: 10px 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .zoom-btn {
            padding: 5px 10px;
            border: 1px solid #ced4da;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .zoom-btn:hover {
            background: #e9ecef;
        }

        .zoom-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Custom Timeline</h1>
            <p>Timeline view dengan resource grouping untuk manajemen proses (100% Gratis)</p>
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

            <button class="refresh-btn" onclick="refreshTimeline()">
                <i class="mdi mdi-refresh"></i> Refresh
            </button>
        </div>

        <div class="zoom-controls">
            <span>Zoom:</span>
            <button class="zoom-btn active" onclick="setZoom(1)">1 Hour</button>
            <button class="zoom-btn" onclick="setZoom(2)">2 Hours</button>
            <button class="zoom-btn" onclick="setZoom(4)">4 Hours</button>
            <button class="zoom-btn" onclick="setZoom(6)">6 Hours</button>
        </div>

        <div class="timeline-container">
            <div class="timeline" id="timeline">
                <!-- Timeline akan di-generate dengan JavaScript -->
            </div>
        </div>
    </div>

    <script>
        // Data untuk timeline
        const resources = [
            { id: 'people', name: 'People', type: 'group' },
            { id: 'person1', name: 'Person 1', parent: 'people', type: 'resource' },
            { id: 'person2', name: 'Person 2', parent: 'people', type: 'resource' },
            { id: 'person3', name: 'Person 3', parent: 'people', type: 'resource' },
            { id: 'person4', name: 'Person 4', parent: 'people', type: 'resource' },
            { id: 'tools', name: 'Tools', type: 'group' },
            { id: 'tool1', name: 'Tool 1', parent: 'tools', type: 'resource' },
            { id: 'tool2', name: 'Tool 2', parent: 'tools', type: 'resource' }
        ];

        const events = [
            {
                id: 1,
                text: 'Task 1 - Cutting',
                resource: 'person1',
                start: '2025-08-26T09:00:00',
                end: '2025-08-26T10:00:00',
                color: '#4CAF50'
            },
            {
                id: 2,
                text: 'Task 2 - Printing',
                resource: 'person2',
                start: '2025-08-26T10:00:00',
                end: '2025-08-26T11:00:00',
                color: '#2196F3'
            },
            {
                id: 3,
                text: 'Task 3 - Finishing',
                resource: 'tool1',
                start: '2025-08-26T11:00:00',
                end: '2025-08-26T12:00:00',
                color: '#FF9800'
            },
            {
                id: 4,
                text: 'Task 4 - Packaging',
                resource: 'person3',
                start: '2025-08-26T14:00:00',
                end: '2025-08-26T16:00:00',
                color: '#9C27B0'
            },
            {
                id: 5,
                text: 'Task 5 - Quality Check',
                resource: 'tool2',
                start: '2025-08-26T15:00:00',
                end: '2025-08-26T17:00:00',
                color: '#F44336'
            },
            {
                id: 6,
                text: 'Task 6 - Shipping',
                resource: 'person1',
                start: '2025-08-27T08:00:00',
                end: '2025-08-27T10:00:00',
                color: '#00BCD4'
            }
        ];

        let currentZoom = 1;
        let currentDate = new Date('2025-08-26');

        function generateTimeline() {
            const timeline = document.getElementById('timeline');
            timeline.innerHTML = '';

            // Generate header
            const header = document.createElement('div');
            header.className = 'timeline-header';

            // Resource header
            const resourceHeader = document.createElement('div');
            resourceHeader.className = 'resource-header';
            resourceHeader.textContent = 'Resources';
            header.appendChild(resourceHeader);

            // Time header
            const timeHeader = document.createElement('div');
            timeHeader.className = 'time-header';

            // Generate time slots
            for (let hour = 6; hour <= 22; hour += currentZoom) {
                const timeSlot = document.createElement('div');
                timeSlot.className = 'time-slot';
                timeSlot.textContent = `${hour.toString().padStart(2, '0')}:00`;
                timeHeader.appendChild(timeSlot);
            }

            header.appendChild(timeHeader);
            timeline.appendChild(header);

            // Generate body
            const body = document.createElement('div');
            body.className = 'timeline-body';

            // Resource column
            const resourceColumn = document.createElement('div');
            resourceColumn.className = 'resource-column';

            // Group resources
            const groups = {};
            resources.forEach(resource => {
                if (resource.type === 'group') {
                    groups[resource.id] = { ...resource, children: [] };
                } else if (resource.parent) {
                    if (groups[resource.parent]) {
                        groups[resource.parent].children.push(resource);
                    }
                }
            });

            // Generate resource rows
            Object.values(groups).forEach(group => {
                // Group header
                const groupHeader = document.createElement('div');
                groupHeader.className = 'resource-group';
                groupHeader.textContent = group.name;
                resourceColumn.appendChild(groupHeader);

                // Group children
                group.children.forEach(child => {
                    const resourceItem = document.createElement('div');
                    resourceItem.className = 'resource-item';
                    resourceItem.textContent = child.name;
                    resourceItem.style.height = '50px';
                    resourceColumn.appendChild(resourceItem);
                });
            });

            body.appendChild(resourceColumn);

            // Timeline grid
            const timelineGrid = document.createElement('div');
            timelineGrid.className = 'timeline-grid';

            // Generate time columns
            for (let hour = 6; hour <= 22; hour += currentZoom) {
                const timeColumn = document.createElement('div');
                timeColumn.className = 'time-column';

                // Generate time slots
                Object.values(groups).forEach(group => {
                    group.children.forEach(child => {
                        const timeSlot = document.createElement('div');
                        timeSlot.className = 'time-slot-grid';
                        timeColumn.appendChild(timeSlot);
                    });
                });

                timelineGrid.appendChild(timeColumn);
            }

            body.appendChild(timelineGrid);
            timeline.appendChild(body);

            // Add events
            addEvents();
        }

        function addEvents() {
            events.forEach(event => {
                const startTime = new Date(event.start);
                const endTime = new Date(event.end);
                const startHour = startTime.getHours();
                const endHour = endTime.getHours();
                const duration = endHour - startHour;

                // Find resource row
                const resourceRows = document.querySelectorAll('.resource-item');
                let resourceRow = null;
                resources.forEach((resource, index) => {
                    if (resource.id === event.resource) {
                        resourceRow = resourceRows[index];
                    }
                });

                if (resourceRow) {
                    const eventElement = document.createElement('div');
                    eventElement.className = `event ${event.resource}`;
                    eventElement.textContent = event.text;
                    eventElement.style.left = `${((startHour - 6) / currentZoom) * 60}px`;
                    eventElement.style.width = `${(duration / currentZoom) * 60 - 4}px`;
                    eventElement.style.top = `${resourceRow.offsetTop + 5}px`;
                    eventElement.style.height = '40px';
                    eventElement.style.lineHeight = '40px';

                    // Add click event
                    eventElement.onclick = () => {
                        alert(`Event: ${event.text}\nStart: ${startTime.toLocaleString()}\nEnd: ${endTime.toLocaleString()}`);
                    };

                    document.querySelector('.timeline-grid').appendChild(eventElement);
                }
            });
        }

        function setZoom(zoom) {
            currentZoom = zoom;

            // Update active button
            document.querySelectorAll('.zoom-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            generateTimeline();
        }

        function refreshTimeline() {
            console.log('Refreshing timeline...');
            generateTimeline();
        }

        // Initialize timeline
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing Custom Timeline...');
            generateTimeline();

            // Add current time indicator
            const now = new Date();
            const currentHour = now.getHours();
            if (currentHour >= 6 && currentHour <= 22) {
                const currentTime = document.createElement('div');
                currentTime.className = 'current-time';
                currentTime.style.top = `${((currentHour - 6) / currentZoom) * 50 + 50}px`;
                document.querySelector('.timeline-grid').appendChild(currentTime);
            }
        });
    </script>
</body>
</html>
