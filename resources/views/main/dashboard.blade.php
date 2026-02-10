@extends('main.layouts.main')
@section('title')
    Dashboard
@endsection
@section('css')
    <style>
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 20px;
        }

        .chart-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .chart-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            text-align: center;
        }

        .dashboard-link-card {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .dashboard-link-card:hover {
            transform: translateX(5px);
            border-left-color: #007bff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .dashboard-link-card.active {
            border-left-color: #007bff;
            background-color: #f8f9fa;
        }
    </style>
@endsection
@section('page-title')
    Dashboard
@endsection
@section('body')
    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Dashboard</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title" style="margin-bottom: 30px;">List Dashboard</h4>

                        <!-- Overview Data - Active -->
                        <a href="{{ route('dashboard') }}" class="btn btn-info w-100 text-left mb-2 dashboard-link-card active">
                            <i class="mdi mdi-view-dashboard mr-2"></i>
                            Overview Data
                        </a>

                        @if (auth()->user()->divisi != 11 && auth()->user()->divisi != 8)
                            <!-- PPIC Dashboard -->
                            <a href="{{ route('dashboard.ppic') }}" class="btn btn-info w-100 text-left mb-2 dashboard-link-card">
                                <i class="mdi mdi-view-dashboard mr-2"></i>
                                Dashboard PPIC
                            </a>

                            <!-- Prepress Dashboard -->
                            <a href="{{ route('dashboard.prepress') }}" class="btn btn-info w-100 text-left mb-2 dashboard-link-card">
                                <i class="mdi mdi-view-dashboard mr-2"></i>
                                Dashboard Prepress
                            </a>

                            <!-- Development Dashboard -->
                            {{-- <a href="{{ route('dashboard.development') }}" class="btn btn-info w-100 text-left mb-2 dashboard-link-card">
                                <i class="mdi mdi-flask mr-2"></i>
                                Development Item
                            </a> --}}
                        @endif

                        @if(auth()->user()->divisi == 8 || auth()->user()->divisi == 1)
                            <!-- Supplier Dashboard -->
                            {{-- <a href="{{ route('dashboard.supplier') }}" class="btn btn-info w-100 text-left mb-2 dashboard-link-card">
                                <i class="mdi mdi-truck mr-2"></i>
                                Dashboard Supplier
                            </a> --}}
                        @endif

                        @if(auth()->user()->divisi == 11 || auth()->user()->divisi == 1)
                            <!-- Security Dashboard -->
                            <a href="{{ route('dashboard.security') }}" class="btn btn-info w-100 text-left mb-2 dashboard-link-card">
                                <i class="mdi mdi-shield mr-2"></i>
                                Dashboard Security
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Column -->
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h4>Welcome to the Dashboard! {{ auth()->user()->name }}, {{ $user->divisi_name ?? '-' }}</h4>

                        <!-- Overview Data Content -->
                        <div class="row mb-4">
                            <!-- Mesin Paling Sibuk dari Plan yang Sudah Finish -->
                            <div class="col-lg-6">
                                <div class="chart-card">
                                    <div class="chart-title">
                                        <i class="mdi mdi-cog mr-2"></i>
                                        Busiest Machines (From Finished Plans)
                                    </div>
                                    <div class="chart-container">
                                        <canvas id="busiestMachineChart"></canvas>
                                    </div>

                                    <!-- Machine Details -->
                                    @if (isset($busiestMachines) && count($busiestMachines) > 0)
                                        <div class="mt-3 pt-3 border-top">
                                            <h6 class="text-muted mb-2">Detail Mesin:</h6>
                                            <div class="row">
                                                @foreach ($busiestMachines as $index => $machine)
                                                    <div class="col-12 mb-2">
                                                        <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                                            <div>
                                                                <span class="badge badge-primary mr-2">#{{ $index + 1 }}</span>
                                                                <strong class="text-primary">{{ $machine['name'] }}</strong>
                                                                <small class="text-muted d-block">{{ $machine['code'] }}</small>
                                                            </div>
                                                            <div class="text-right">
                                                                <span class="badge badge-{{ $machine['finished_count'] > 50 ? 'danger' : ($machine['finished_count'] > 30 ? 'warning' : 'success') }}">
                                                                    {{ $machine['finished_count'] }} Plan Selesai
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="mt-3 pt-3 border-top text-center">
                                            <small class="text-muted">
                                                <i class="mdi mdi-information-outline mr-1"></i>
                                                Belum ada data mesin dari plan yang sudah finish
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Order Terbanyak -->
                            <div class="col-lg-6">
                                <div class="chart-card">
                                    <div class="chart-title">
                                        <i class="mdi mdi-file-document-outline mr-2"></i>
                                        Top Orders
                                    </div>
                                    <div class="chart-container">
                                        <canvas id="topOrdersChart"></canvas>
                                    </div>

                                    <!-- Order Details -->
                                    @if (isset($topOrders) && count($topOrders) > 0)
                                        <div class="mt-3 pt-3 border-top">
                                            <h6 class="text-muted mb-2">Detail Order:</h6>
                                            <div class="row">
                                                @foreach ($topOrders as $index => $order)
                                                    <div class="col-12 mb-2">
                                                        <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                                            <div>
                                                                <span class="badge badge-info mr-2">#{{ $index + 1 }}</span>
                                                                <strong class="text-primary">{{ $order['jo'] }}</strong>
                                                                <small class="text-muted d-block">Total Produksi: {{ number_format($order['total_production'] ?? 0, 0) }}</small>
                                                            </div>
                                                            <div class="text-right">
                                                                <span class="badge badge-{{ $order['order_count'] > 20 ? 'danger' : ($order['order_count'] > 10 ? 'warning' : 'success') }}">
                                                                    {{ $order['order_count'] }} Order
                                                                </span>
                                                                <small class="text-muted d-block">{{ $order['source'] }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="mt-3 pt-3 border-top text-center">
                                            <small class="text-muted">
                                                <i class="mdi mdi-information-outline mr-1"></i>
                                                Belum ada data order
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            $(document).ready(function() {
                // Initialize Charts
                initializeCharts();
            });

            function initializeCharts() {
                // Chart 1: Mesin Paling Sibuk dari Plan yang Sudah Finish (Bar Chart)
                const busiestMachineCtx = document.getElementById('busiestMachineChart');
                if (busiestMachineCtx) {
                    @if (isset($busiestMachines) && count($busiestMachines) > 0)
                        const busiestMachineData = {
                            labels: {!! json_encode(collect($busiestMachines)->pluck('name')) !!},
                            data: {!! json_encode(collect($busiestMachines)->pluck('finished_count')) !!}
                        };
                        const busiestMachineDetails = {!! json_encode($busiestMachines) !!};
                    @else
                        const busiestMachineData = {
                            labels: ['Tidak Ada Data'],
                            data: [0]
                        };
                        const busiestMachineDetails = [];
                    @endif

                    const busiestMachineChart = new Chart(busiestMachineCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: busiestMachineData.labels,
                            datasets: [{
                                label: 'Jumlah Plan Finish',
                                data: busiestMachineData.data,
                                backgroundColor: [
                                    '#dc3545',
                                    '#fd7e14',
                                    '#ffc107',
                                    '#28a745',
                                    '#007bff'
                                ],
                                borderColor: [
                                    '#c82333',
                                    '#e55a00',
                                    '#e0a800',
                                    '#218838',
                                    '#0056b3'
                                ],
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        afterLabel: function(context) {
                                            if (busiestMachineDetails.length > 0) {
                                                const machineIndex = context.dataIndex;
                                                const machine = busiestMachineDetails[machineIndex];
                                                if (machine) {
                                                    return `Kode: ${machine.code}`;
                                                }
                                            }
                                            return '';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        callback: function(value) {
                                            return value + ' plan';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Chart 2: Order Terbanyak (Bar Chart)
                const topOrdersCtx = document.getElementById('topOrdersChart');
                if (topOrdersCtx) {
                    @if (isset($topOrders) && count($topOrders) > 0)
                        const topOrdersData = {
                            labels: {!! json_encode(collect($topOrders)->pluck('jo')) !!},
                            data: {!! json_encode(collect($topOrders)->pluck('order_count')) !!}
                        };
                        const topOrdersDetails = {!! json_encode($topOrders) !!};
                    @else
                        const topOrdersData = {
                            labels: ['No Data Available'],
                            data: [0]
                        };
                        const topOrdersDetails = [];
                    @endif

                    const topOrdersChart = new Chart(topOrdersCtx.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: topOrdersData.labels,
                            datasets: [{
                                label: 'Jumlah Order',
                                data: topOrdersData.data,
                                backgroundColor: [
                                    '#007bff',
                                    '#6f42c1',
                                    '#fd7e14',
                                    '#20c997',
                                    '#e83e8c'
                                ],
                                borderColor: [
                                    '#0056b3',
                                    '#5a2d91',
                                    '#e55a00',
                                    '#1a9f7a',
                                    '#c73e6f'
                                ],
                                borderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        afterLabel: function(context) {
                                            if (topOrdersDetails.length > 0) {
                                                const orderIndex = context.dataIndex;
                                                const order = topOrdersDetails[orderIndex];
                                                if (order) {
                                                    return `Total Produksi: ${parseInt(order.total_production || 0).toLocaleString()} | Sumber: ${order.source}`;
                                                }
                                            }
                                            return '';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        callback: function(value) {
                                            return value + ' order';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Store chart references for potential updates
                window.dashboardCharts = {
                    busiestMachine: typeof busiestMachineChart !== 'undefined' ? busiestMachineChart : null,
                    topOrders: typeof topOrdersChart !== 'undefined' ? topOrdersChart : null
                };
            }

            // Function to update charts with new data (can be called via AJAX)
            function updateCharts(newData) {
                if (window.dashboardCharts && newData) {
                    // Update busiest machine chart
                    if (newData.busiestMachine && window.dashboardCharts.busiestMachine) {
                        window.dashboardCharts.busiestMachine.data.datasets[0].data = newData.busiestMachine.data;
                        window.dashboardCharts.busiestMachine.data.labels = newData.busiestMachine.labels;
                        window.dashboardCharts.busiestMachine.update();
                    }

                    // Update top orders chart
                    if (newData.topOrders && window.dashboardCharts.topOrders) {
                        window.dashboardCharts.topOrders.data.datasets[0].data = newData.topOrders.data;
                        window.dashboardCharts.topOrders.data.labels = newData.topOrders.labels;
                        window.dashboardCharts.topOrders.update();
                    }
                }
            }
        </script>
    @endsection
