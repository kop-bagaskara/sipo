@extends('main.layouts.main')
@section('title')
    Monitoring SO
@endsection
@section('css')
    <style>
        .plan-items::-webkit-scrollbar {
            width: 6px;
        }

        .plan-items::-webkit-scrollbar-track {
            background: #f8f9fa;
            border-radius: 3px;
        }

        .plan-items::-webkit-scrollbar-thumb {
            background: #6c757d;
            border-radius: 3px;
        }

        .plan-items::-webkit-scrollbar-thumb:hover {
            background: #495057;
        }

        /* Chart Styling */
        .chart-container {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn-group .btn {
            border-radius: 0;
        }

        .btn-group .btn:first-child {
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }

        .btn-group .btn:last-child {
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        .card-title {
            font-weight: 600;
            color: #333;
        }

        .form-label {
            font-weight: 500;
            color: #555;
            font-size: 0.9rem;
        }

        .summary-card {
            transition: transform 0.2s ease-in-out;
        }

        .summary-card:hover {
            transform: translateY(-2px);
        }

        /* Loading Animation */
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .loading.show {
            display: block;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
    </style>
@endsection
@section('page-title')
    Monitoring SO
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection

    @section('content')

        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Monitoring SO</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Monitoring SO</li>
                </ol>
            </div>
        </div>

        <!-- Grafik Monitoring SO, WO, LPO -->
        <div class="row">
            <div class="col-12 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-chart-bar text-primary me-2"></i>
                                Monitoring SO, WO, LPO per Bulan
                            </h4>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary btn-sm" id="btnThisYear">
                                    Tahun Ini
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnLastYear">
                                    Tahun Lalu
                                </button>
                            </div>
                        </div>

                        <!-- Filter Controls -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="yearFilter" class="form-label">Tahun:</label>
                                <select class="form-control" id="yearFilter">
                                    <option value="2024">2024</option>
                                    <option value="2023">2023</option>
                                    <option value="2022">2022</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="monthFilter" class="form-label">Bulan:</label>
                                <select class="form-control" id="monthFilter">
                                    <option value="">Semua Bulan</option>
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" class="btn btn-info" id="btnRefresh">
                                    <i class="fas fa-sync-alt me-1"></i>
                                    Refresh Data
                                </button>
                            </div>
                        </div>

                        <!-- Loading Indicator -->
                        <div class="loading" id="chartLoading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Memuat data grafik...</p>
                        </div>

                        <!-- Chart Container -->
                        <div class="chart-container" style="position: relative; height:400px; width:100%">
                            <canvas id="monitoringChart"></canvas>
                        </div>

                        <!-- Summary Cards -->
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card bg-info text-white summary-card">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">
                                            <i class="fas fa-file-invoice me-2"></i>
                                            Total SO
                                        </h5>
                                        <h3 class="mb-0" id="totalSO">0</h3>
                                        <small>Sales Orders</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white summary-card">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">
                                            <i class="fas fa-tasks me-2"></i>
                                            Total WO
                                        </h5>
                                        <h3 class="mb-0" id="totalWO">0</h3>
                                        <small>Work Orders</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-warning text-white summary-card">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">
                                            <i class="fas fa-shopping-cart me-2"></i>
                                            Total LPO
                        </h5>
                                        <h3 class="mb-0" id="totalLPO">0</h3>
                                        <small>Laporan Penyelesaian Orders</small>
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
        <!-- FullCalendar -->
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
        
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <script>
            $(document).ready(function() {
                // Initialize Chart
                let monitoringChart;

                // Chart data will be loaded from API
                let chartData = {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [
                        {
                            label: 'Sales Order (SO)',
                            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                            backgroundColor: 'rgba(54, 162, 235, 0.8)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 2,
                            borderRadius: 4,
                            borderSkipped: false,
                        },
                        {
                            label: 'Work Order (WO)',
                            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                            backgroundColor: 'rgba(75, 192, 192, 0.8)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 2,
                            borderRadius: 4,
                            borderSkipped: false,
                        },
                        {
                            label: 'Local Purchase Order (LPO)',
                            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                            backgroundColor: 'rgba(255, 205, 86, 0.8)',
                            borderColor: 'rgba(255, 205, 86, 1)',
                            borderWidth: 2,
                            borderRadius: 4,
                            borderSkipped: false,
                        }
                    ]
                };

                // Chart configuration
                const chartConfig = {
                    type: 'bar',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: 'rgba(255, 255, 255, 0.1)',
                                borderWidth: 1,
                                cornerRadius: 6,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y + ' orders';
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                stacked: false,
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            y: {
                                stacked: false,
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)',
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    callback: function(value) {
                                        return value + ' orders';
                                    }
                                }
                            }
                        },
                        interaction: {
                            mode: 'nearest',
                            axis: 'x',
                            intersect: false
                        },
                        animation: {
                            duration: 1000,
                            easing: 'easeInOutQuart'
                        }
                    }
                };

                // Initialize chart
                function initChart() {
                    const ctx = document.getElementById('monitoringChart').getContext('2d');
                    monitoringChart = new Chart(ctx, chartConfig);
                }

                                // Load data from API
                function loadMonitoringData() {
                    const year = $('#yearFilter').val();
                    const month = $('#monthFilter').val();
                    const divisi = $('#divisiFilter').val();

                    showLoading();
                    $('#btnRefresh').html('<span class="spinner-border spinner-border-sm me-2"></span>Loading...');
                    $('#btnRefresh').prop('disabled', true);

                    $.ajax({
                        url: '{{ route("monitoring-so.data") }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            year: year,
                            month: month,
                            divisi: divisi
                        },
                        success: function(response) {
                            if (response.success) {
                                // Update chart data
                                chartData.labels = response.data.labels;
                                chartData.datasets = response.data.datasets;

                                // Update chart
                                monitoringChart.data = chartData;
                                monitoringChart.update();

                                // Update summary cards
                                $('#totalSO').text(response.summary.totalSO);
                                $('#totalWO').text(response.summary.totalWO);
                                $('#totalLPO').text(response.summary.totalLPO);
                            } else {
                                console.error('Error loading data:', response.message);
                                alert('Terjadi kesalahan saat memuat data: ' + response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', error);
                            alert('Terjadi kesalahan saat memuat data. Silakan coba lagi.');
                        },
                        complete: function() {
                            hideLoading();
                            $('#btnRefresh').html('<i class="fas fa-sync-alt me-1"></i>Refresh Data');
                            $('#btnRefresh').prop('disabled', false);
                        }
                    });
                }

                // Update summary cards
                function updateSummaryCards() {
                    const soData = chartData.datasets[0].data;
                    const woData = chartData.datasets[1].data;
                    const lpoData = chartData.datasets[2].data;

                    const totalSO = soData.reduce((a, b) => a + b, 0);
                    const totalWO = woData.reduce((a, b) => a + b, 0);
                    const totalLPO = lpoData.reduce((a, b) => a + b, 0);

                    $('#totalSO').text(totalSO);
                    $('#totalWO').text(totalWO);
                    $('#totalLPO').text(totalLPO);
                }

                // Filter data based on year
                function filterByYear(year) {
                    console.log('Filtering by year:', year);
                    loadMonitoringData();
                }

                // Filter data based on month
                function filterByMonth(month) {
                    console.log('Filtering by month:', month);
                    loadMonitoringData();
                }

                // Filter data based on division
                function filterByDivision(division) {
                    console.log('Filtering by division:', division);
                    loadMonitoringData();
                }

                // Show/hide loading indicator
                function showLoading() {
                    $('#chartLoading').addClass('show');
                    $('.chart-container').hide();
                }

                function hideLoading() {
                    $('#chartLoading').removeClass('show');
                    $('.chart-container').show();
                }

                // Update chart data
                function updateChartData() {
                    loadMonitoringData();
                }

                // Event listeners
                $('#btnThisYear').click(function() {
                    $('#yearFilter').val(new Date().getFullYear());
                    filterByYear(new Date().getFullYear());
                    $(this).removeClass('btn-outline-secondary').addClass('btn-outline-primary');
                    $('#btnLastYear').removeClass('btn-outline-primary').addClass('btn-outline-secondary');
                });

                $('#btnLastYear').click(function() {
                    $('#yearFilter').val(new Date().getFullYear() - 1);
                    filterByYear(new Date().getFullYear() - 1);
                    $(this).removeClass('btn-outline-secondary').addClass('btn-outline-primary');
                    $('#btnThisYear').removeClass('btn-outline-primary').addClass('btn-outline-secondary');
                });

                $('#yearFilter').change(function() {
                    filterByYear($(this).val());
                });

                $('#monthFilter').change(function() {
                    filterByMonth($(this).val());
                });

                $('#divisiFilter').change(function() {
                    filterByDivision($(this).val());
                });

                $('#btnRefresh').click(function() {
                    updateChartData();
                });

                                // Initialize
                initChart();

                // Set current year as default
                $('#yearFilter').val(new Date().getFullYear());
                $('#btnThisYear').removeClass('btn-outline-secondary').addClass('btn-outline-primary');

                // Load initial data
                loadMonitoringData();
            });
        </script>
    @endsection
