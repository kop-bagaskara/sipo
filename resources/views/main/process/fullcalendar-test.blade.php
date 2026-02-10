@extends('main.layouts.main')
@section('title')
    FullCalendar Test
@endsection

@section('css')
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.css' rel='stylesheet' />
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.19/index.global.min.css' rel='stylesheet' />
    <style>
        #calendar {
            height: 600px;
            margin: 20px;
        }
    </style>
@endsection

@section('content')
<div class="container">
    <h1>FullCalendar Test</h1>
    <div id="calendar"></div>
</div>
@endsection

@section('js')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.19/index.global.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('FullCalendar version:', FullCalendar.version);
    console.log('FullCalendar object:', FullCalendar);

    const calendarEl = document.getElementById('calendar');

    if (typeof FullCalendar === 'undefined') {
        calendarEl.innerHTML = '<div class="alert alert-danger">FullCalendar tidak dimuat!</div>';
        return;
    }

    try {
        const calendar = new FullCalendar.Calendar(calendarEl, {
            plugins: [FullCalendar.resourceTimelinePlugin],
            initialView: 'resourceTimelineWeek',
            resources: [
                { id: 'a', title: 'Resource A' },
                { id: 'b', title: 'Resource B' }
            ],
            events: [
                { id: '1', title: 'Event 1', start: '2024-01-15', resourceId: 'a' },
                { id: '2', title: 'Event 2', start: '2024-01-16', resourceId: 'b' }
            ]
        });

        calendar.render();
        console.log('Calendar rendered successfully');
    } catch (error) {
        console.error('Error:', error);
        calendarEl.innerHTML = '<div class="alert alert-danger">Error: ' + error.message + '</div>';
    }
});
</script>
@endsection
