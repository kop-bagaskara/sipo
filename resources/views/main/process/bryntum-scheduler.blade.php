@extends('main.layouts.main')
@section('title')
    Bryntum Scheduler Demo
@endsection
@section('css')
    <!-- Bryntum Scheduler CDN (trial) -->
    <link rel="stylesheet" href="https://cdn.bryntum.com/scheduler/5.5.1/scheduler.stockholm.css">
    <style>
        #bryntum-container {
            height: 600px;
            width: 100%;
        }
    </style>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">Bryntum Scheduler Demo</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div id="bryntum-container"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script src="https://cdn.bryntum.com/scheduler/5.5.1/scheduler.umd.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dummy data mesin
            const resources = [
                { id: 1, name: 'CD4 UNIT 19' },
                { id: 2, name: 'CD6-3 UNIT 19' },
                { id: 3, name: 'CX-104 UNIT 19' }
            ];
            // Dummy data plan
            const events = [
                { id: 1, resourceId: 1, name: 'SO-001 | ITEM-A | 1000', startDate: '2025-06-23T08:00:00', endDate: '2025-06-23T10:00:00' },
                { id: 2, resourceId: 1, name: 'SO-002 | ITEM-B | 2000', startDate: '2025-06-23T10:00:00', endDate: '2025-06-23T13:00:00' },
                { id: 3, resourceId: 2, name: 'SO-003 | ITEM-C | 1500', startDate: '2025-06-23T08:00:00', endDate: '2025-06-23T11:00:00' }
            ];
            // Inisialisasi Bryntum Scheduler
            const scheduler = new bryntum.scheduler.Scheduler({
                appendTo: 'bryntum-container',
                height: 600,
                startDate: '2025-06-23T06:00:00',
                endDate: '2025-06-23T20:00:00',
                viewPreset: 'hourAndDay',
                resources,
                events,
                columns: [
                    { text: 'Machines', field: 'name', width: 200 }
                ],
                eventDragCreate: false,
                eventEditFeature: false,
                listeners: {
                    eventDrop({ eventRecords, context }) {
                        const moved = eventRecords.map(ev => `${ev.name} ke mesin ${ev.resource.name}`).join(', ');
                        bryntum.scheduler.Toast.show(`Plan dipindah: ${moved}`);
                    }
                }
            });
        });
    </script>
@endsection
