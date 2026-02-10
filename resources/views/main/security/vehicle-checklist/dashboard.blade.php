@extends('main.layouts.main')
@section('title')
    Dashboard Security
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

    <style>
        .cust-col {
            white-space: nowrap;
        }
    </style>
@endsection
@section('page-title')
    Dashboard Security
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Dashboard Security</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">Dashboard Security</li>
                </ol>
            </div>
        </div>

        <!-- Filters & Shortcuts -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between flex-wrap">
                        <h4 class="card-title mb-0"><i class="bx bx-filter-alt"></i> Filter Dashboard</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('hr.reports.security.vehicle-checklist') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bx bx-file"></i> HR Vehicle Report
                            </a>
                            <a href="{{ route('hr.reports.security.goods-movement') }}" class="btn btn-outline-success btn-sm">
                                <i class="bx bx-package"></i> HR Goods Report
                            </a>
                            <a href="{{ route('hr.reports.security.daily-activity') }}" class="btn btn-outline-warning btn-sm">
                                <i class="bx bx-clipboard"></i> HR Daily Report
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ url()->current() }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Dari</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date', now()->toDateString()) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Sampai</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date', now()->toDateString()) }}">
                                </div>
                                <div class="col-md-4 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-search"></i> Terapkan
                                    </button>
                                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                                        <i class="bx bx-reset"></i> Reset
                                    </a>
                                    <a href="{{ route('security.vehicle-checklist.export') }}?start_date={{ request('start_date', now()->toDateString()) }}&end_date={{ request('end_date', now()->toDateString()) }}" target="_blank" class="btn btn-success">
                                        <i class="bx bx-download"></i> Export Range
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-1 overflow-hidden">
                                <p class="text-truncate font-size-14 mb-2">Kendaraan Hari Ini</p>
                                <h4 class="mb-0">{{ $summary['total_hari_ini'] }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-primary">
                                        <i class="bx bx-car font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-1 overflow-hidden">
                                <p class="text-truncate font-size-14 mb-2">Keluar Hari Ini</p>
                                <h4 class="mb-0 text-warning">{{ $summary['keluar_hari_ini'] }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="avatar-sm rounded-circle bg-warning mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-warning">
                                        <i class="bx bx-log-out font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-1 overflow-hidden">
                                <p class="text-truncate font-size-14 mb-2">Sudah Kembali</p>
                                <h4 class="mb-0 text-success">{{ $summary['masuk_hari_ini'] }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="avatar-sm rounded-circle bg-success mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-success">
                                        <i class="bx bx-log-in font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-1 overflow-hidden">
                                <p class="text-truncate font-size-14 mb-2">Total Bulan Ini</p>
                                <h4 class="mb-0 text-info">{{ $summary['total_bulan_ini'] }}</h4>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <div class="avatar-sm rounded-circle bg-info mini-stat-icon">
                                    <span class="avatar-title rounded-circle bg-info">
                                        <i class="bx bx-calendar font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart & Quick Actions -->
        <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Aktivitas Kendaraan (7 Hari Terakhir)</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="vehicleChart" height="300"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Quick Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('security.vehicle-checklist.create') }}" class="btn btn-success btn-lg">
                                <i class="bx bx-plus"></i> Tambah Checklist Baru
                            </a>
                            <a href="{{ route('security.vehicle-checklist.index') }}" class="btn btn-primary btn-lg">
                                <i class="bx bx-list-ul"></i> Lihat Semua Checklist
                            </a>
                            <a href="{{ route('security.vehicle-checklist.export') }}?tanggal={{ date('Y-m-d') }}"
                                class="btn btn-info btn-lg" target="_blank">
                                <i class="bx bx-download"></i> Export Hari Ini
                            </a>
                        </div>

                        <hr>

                        <div class="text-center">
                            <h5 class="font-size-16">Status Hari Ini</h5>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mt-3">
                                        <p class="mb-1 text-truncate">
                                            <i class="bx bx-circle text-warning me-1"></i> Keluar
                                        </p>
                                        <h5 class="font-size-16 text-warning">{{ $summary['keluar_hari_ini'] }}</h5>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mt-3">
                                        <p class="mb-1 text-truncate">
                                            <i class="bx bx-circle text-success me-1"></i> Kembali
                                        </p>
                                        <h5 class="font-size-16 text-success">{{ $summary['masuk_hari_ini'] }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Informasi</h4>
                    </div>
                    <div class="card-body">
                        <div class="text-muted">
                            <p class="mb-2"><i class="bx bx-info-circle text-primary"></i> Dashboard ini menampilkan
                                ringkasan aktivitas kendaraan</p>
                            <p class="mb-2"><i class="bx bx-time text-warning"></i> Data diperbarui secara real-time</p>
                            <p class="mb-0"><i class="bx bx-shield text-success"></i> Sistem keamanan terintegrasi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Aktivitas Terbaru Hari Ini</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="recentActivitiesTable" class="table table-centered table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Driver</th>
                                        <th>Kendaraan</th>
                                        <th>Shift</th>
                                        <th>Status</th>
                                        <th>Tujuan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $start = request('start_date', now()->toDateString());
                                        $end = request('end_date', now()->toDateString());
                                        $recentActivities = \App\Models\SecurityVehicleChecklist::whereBetween('tanggal', [$start, $end])
                                            ->orderBy('created_at', 'desc')
                                            ->limit(50)
                                            ->get();
                                    @endphp

                                    @forelse($recentActivities as $activity)
                                        <tr>
                                            <td>
                                                <span class="text-muted">{{ $activity->created_at->format('H:i') }}</span>
                                            </td>
                                            <td>{{ $activity->nama_driver }}</td>
                                            <td>
                                                <span class="fw-bold">{{ $activity->model_kendaraan }}</span>
                                                @if ($activity->no_polisi)
                                                    <br><small class="text-muted">{{ $activity->no_polisi }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $activity->shift === 'pagi' ? 'success' : ($activity->shift === 'siang' ? 'warning' : 'info') }}">
                                                    {{ $activity->shift_formatted ?? ucfirst($activity->shift) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $activity->status == 'selesai' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($activity->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-truncate" style="max-width: 260px; display: inline-block;" title="{{ $activity->tujuan }}">
                                                    {{ strlen($activity->tujuan) > 60 ? substr($activity->tujuan, 0, 60) . '...' : $activity->tujuan }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('security.vehicle-checklist.show', $activity->id) }}"
                                                        class="btn btn-outline-secondary btn-sm" title="Detail">
                                                        <i class="bx bx-show"></i>
                                                    </a>
                                                    @if ($activity->status == 'keluar')
                                                        <a href="{{ route('security.vehicle-checklist.edit', $activity->id) }}"
                                                            class="btn btn-outline-success btn-sm" title="Input Kembali">
                                                            <i class="bx bx-log-in"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bx bx-info-circle fs-1"></i>
                                                    <p class="mt-2">Belum ada aktivitas hari ini</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('script')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Chart Data
                const chartData = @json($chartData);

                // Vehicle Activity Chart
                const ctx = document.getElementById('vehicleChart').getContext('2d');
                const vehicleChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.map(item => item.label),
                        datasets: [{
                            label: 'Jumlah Kendaraan',
                            data: chartData.map(item => item.total),
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            tension: 0.1,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Aktivitas Kendaraan Harian'
                            },
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });

                // Auto refresh every 5 minutes
                setInterval(function() {
                    location.reload();
                }, 300000);

                // Recent activities table
                if (window.jQuery) {
                    $('#recentActivitiesTable').DataTable({
                        pageLength: 10,
                        ordering: true,
                        order: [[0, 'desc']],
                        responsive: true,
                        language: {
                            search: 'Cari:',
                            lengthMenu: 'Tampil _MENU_',
                            zeroRecords: 'Tidak ada data',
                            info: 'Menampilkan _START_ - _END_ dari _TOTAL_',
                            infoEmpty: 'Tidak ada data',
                            infoFiltered: '(disaring dari _MAX_ total)'
                        }
                    });
                }
            });
        </script>
    @endsection
