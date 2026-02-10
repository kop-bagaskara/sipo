@extends('main.layouts.main')
@section('title')
    Dashboard Training
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
        .cust-col { white-space: nowrap; }
        .stat-card {
            border: 0;
            border-radius: .85rem;
            color: #fff;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,.08);
            transition: transform .2s ease, box-shadow .2s ease;
            background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
            position: relative;
        }
        .stat-card .stat-body { padding: 1.25rem 1.25rem 1rem 1.25rem; }
        .stat-card .stat-icon {
            position: absolute; right: 12px; bottom: 8px; font-size: 2.5rem; opacity: .2;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 14px 26px rgba(0,0,0,.12); }
        .bg-grad-primary { background: linear-gradient(135deg, #0061f2 0%, #60a5fa 100%); }
        .bg-grad-success { background: linear-gradient(135deg, #16a34a 0%, #34d399 100%); }
        .bg-grad-warning { background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%); }
        .bg-grad-danger  { background: linear-gradient(135deg, #dc2626 0%, #f87171 100%); }
        .stat-link { display:flex; align-items:center; justify-content:space-between; padding:.6rem 1.25rem; background:rgba(255,255,255,.08); color:#fff; text-decoration:none; }
        .stat-link:hover { background:rgba(255,255,255,.14); color:#fff; text-decoration:none; }
        .stat-value { font-size:2.1rem; font-weight:700; margin:0; }
        .stat-label { margin:0; opacity:.9; letter-spacing:.3px; }
    </style>
@endsection
@section('page-title')
    Dashboard Training
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Dashboard Training</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">Dashboard Training</li>
                </ol>
            </div>
        </div>

        <!-- Statistics Cards (Modern) -->
        <div class="row">
            <div class="col-lg-4 col-sm-6 mb-3">
                <div class="stat-card bg-grad-primary">
                    <div class="stat-body">
                        <p class="stat-label">Total Training</p>
                        <p class="stat-value">{{ $stats['total_trainings'] }}</p>
                        <i class="mdi mdi-book"></i>
                    </div>
                    <a href="{{ route('hr.training.index') }}" class="stat-link">Lihat Detail <i class="mdi mdi-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6 mb-3">
                <div class="stat-card bg-grad-success">
                    <div class="stat-body">
                        <p class="stat-label">Training Aktif</p>
                        <p class="stat-value">{{ $stats['active_trainings'] }}</p>
                        <i class="mdi mdi-check"></i>
                    </div>
                    {{-- to schedule --}}
                    <a href="{{ route('hr.training.schedule.index') }}" class="stat-link">Lihat Detail <i class="mdi mdi-arrow-right"></i></a>
                    {{-- <a href="{{ route('hr.training.index', ['status' => 'active']) }}" class="stat-link">Lihat Detail <i class="mdi mdi-arrow-right"></i></a> --}}
                </div>
            </div>

            <div class="col-lg-4 col-sm-6 mb-3">
                <div class="stat-card bg-grad-danger">
                    <div class="stat-body">
                        <p class="stat-label">Training Selesai</p>
                        <p class="stat-value">{{ $stats['completed_trainings'] }}</p>
                        <i class="mdi mdi-trophy"></i>
                    </div>
                    <a href="{{ route('hr.training.registration.history', ['status' => 'completed']) }}" class="stat-link">Lihat Detail <i class="mdi mdi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Training by Type Chart -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="mdi mdi-chart-pie mr-2"></i>
                            Training by Type
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="trainingTypeChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Training by Status Chart -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="mdi mdi-chart-bar mr-2"></i>
                            Training by Status
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="trainingStatusChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Registration Trends -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="mdi mdi-chart-line mr-2"></i>
                            Tren Pendaftaran (30 Hari Terakhir)
                        </h3>
                    </div>
                    <div class="card-body">
                        <canvas id="registrationTrendsChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>

            <!-- Department Participation -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="mdi mdi-clock mr-2"></i>
                            Training Terbaru
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Training</th>
                                        <th>Tipe</th>
                                        <th>Status</th>
                                        <th>Peserta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentTrainings as $training)
                                        <tr>
                                            <td>
                                                <strong>{{ $training->training_name }}</strong>
                                                <br><small class="text-muted">{{ $training->training_code }}</small>
                                            </td>
                                            <td>
                                                @switch($training->training_type)
                                                    @case('mandatory')
                                                        <span class="badge badge-danger">Mandatory</span>
                                                    @break

                                                    @case('optional')
                                                        <span class="badge badge-success">Optional</span>
                                                    @break

                                                    @case('certification')
                                                        <span class="badge badge-warning">Certification</span>
                                                    @break

                                                    @case('skill_development')
                                                        <span class="badge badge-primary">Skill Development</span>
                                                    @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @switch($training->status)
                                                    @case('draft')
                                                        <span class="badge badge-secondary">Draft</span>
                                                    @break

                                                    @case('published')
                                                        <span class="badge badge-success">Published</span>
                                                    @break

                                                    @case('ongoing')
                                                        <span class="badge badge-warning">Ongoing</span>
                                                    @break

                                                    @case('completed')
                                                        <span class="badge badge-info">Completed</span>
                                                    @break

                                                    @case('cancelled')
                                                        <span class="badge badge-danger">Cancelled</span>
                                                    @break
                                                @endswitch
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-primary">{{ $training->participants_count }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Employee Training History by Department -->
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="mdi mdi-account-group mr-2"></i>
                            Riwayat Training Karyawan per Departemen
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="departmentSelect" class="form-label">Pilih Departemen:</label>
                            <select class="form-control" id="departmentSelect" onchange="loadEmployeeTrainingHistory()">
                                <option value="">Pilih Departemen</option>
                                <option value="all">All Karyawan</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->divisi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="employeeTrainingHistory">
                            <div class="text-center text-muted">
                                <i class="mdi mdi-information-outline mr-2"></i>
                                Pilih departemen untuk melihat riwayat training karyawan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @section('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            $(document).ready(function() {
                // Training by Type Chart
                const trainingTypeData = @json($trainingByType);
                const trainingTypeLabels = Object.keys(trainingTypeData);
                const trainingTypeValues = Object.values(trainingTypeData);

                new Chart(document.getElementById('trainingTypeChart'), {
                    type: 'doughnut',
                    data: {
                        labels: trainingTypeLabels,
                        datasets: [{
                            data: trainingTypeValues,
                            backgroundColor: [
                                '#dc3545',
                                '#28a745',
                                '#ffc107',
                                '#007bff'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });

                // Training by Status Chart
                const trainingStatusData = @json($trainingByStatus);
                const trainingStatusLabels = Object.keys(trainingStatusData);
                const trainingStatusValues = Object.values(trainingStatusData);

                new Chart(document.getElementById('trainingStatusChart'), {
                    type: 'bar',
                    data: {
                        labels: trainingStatusLabels,
                        datasets: [{
                            label: 'Jumlah Training',
                            data: trainingStatusValues,
                            backgroundColor: '#007bff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Registration Trends Chart
                const registrationTrends = @json($registrationTrends);
                const registrationLabels = registrationTrends.map(item => item.date);
                const registrationValues = registrationTrends.map(item => item.count);

                new Chart(document.getElementById('registrationTrendsChart'), {
                    type: 'line',
                    data: {
                        labels: registrationLabels,
                        datasets: [{
                            label: 'Pendaftaran',
                            data: registrationValues,
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Department Participation Chart
                const departmentData = @json($departmentParticipation);
                const departmentLabels = departmentData.map(item => item.divisi);
                const departmentValues = departmentData.map(item => item.count);

                new Chart(document.getElementById('departmentParticipationChart'), {
                    type: 'horizontalBar',
                    data: {
                        labels: departmentLabels,
                        datasets: [{
                            label: 'Partisipasi',
                            data: departmentValues,
                            backgroundColor: '#17a2b8'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });

            // Function to load employee training history (moved outside document.ready)
            window.loadEmployeeTrainingHistory = function() {
                const departmentId = document.getElementById('departmentSelect').value;
                const container = document.getElementById('employeeTrainingHistory');

                console.log('Selected department ID:', departmentId);

                if (!departmentId) {
                    container.innerHTML = `
                        <div class="text-center text-muted">
                            <i class="mdi mdi-information-outline mr-2"></i>
                            Pilih departemen untuk melihat riwayat training karyawan
                        </div>
                    `;
                    return;
                }

                // Show loading
                container.innerHTML = `
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data...</p>
                    </div>
                `;

                // AJAX call to get employee training history
                $.ajax({
                    url: '{{ route("hr.training.api.employee-history") }}',
                    method: 'GET',
                    data: { department_id: departmentId },
                    success: function(response) {
                        if (response.success) {
                            let html = '<div class="table-responsive"><table class="table table-sm table-hover">';
                            html += '<thead><tr><th>Nama Karyawan</th><th>NIP</th><th>Training yang Pernah Diikuti</th><th>Status</th></tr></thead><tbody>';

                            if (response.data.length === 0) {
                                const departmentId = document.getElementById('departmentSelect').value;
                                const message = departmentId === 'all'
                                    ? 'Tidak ada data karyawan'
                                    : 'Tidak ada data karyawan di departemen ini';
                                html += '<tr><td colspan="4" class="text-center text-muted">' + message + '</td></tr>';
                            } else {
                                response.data.forEach(function(employee) {
                                    console.log(employee);
                                    html += '<tr>';
                                    html += '<td><strong>' + employee.name + '</strong><br><small class="text-muted">' + employee.position + '</small></td>';
                                    html += '<td><code>' + employee.nip + '</code></td>';

                                    if (employee.trainings.length === 0) {
                                        html += '<td><span class="badge badge-warning">Belum pernah mengikuti training</span></td>';
                                        html += '<td><span class="badge badge-secondary">-</span></td>';
                                    } else {
                                        let trainingList = '';
                                        let statusList = '';
                                        employee.trainings.forEach(function(training) {
                                            trainingList += '<span class="badge badge-info mr-1 mb-1">' + training.training_name + '</span><br>';

                                            // Status badge based on registration_status
                                            let statusClass = 'badge-secondary';
                                            let statusText = training.registration_status;

                                            switch(training.registration_status) {
                                                case 'approved':
                                                    statusClass = 'badge-success';
                                                    statusText = 'Disetujui';
                                                    break;
                                                case 'completed':
                                                    statusClass = 'badge-primary';
                                                    statusText = 'Selesai';
                                                    break;
                                                case 'attended':
                                                    statusClass = 'badge-info';
                                                    statusText = 'Hadir';
                                                    break;
                                                case 'registered':
                                                    statusClass = 'badge-warning';
                                                    statusText = 'Terdaftar';
                                                    break;
                                            }

                                            statusList += '<span class="badge ' + statusClass + ' mr-1 mb-1">' + statusText + '</span><br>';
                                        });
                                        html += '<td>' + trainingList + '</td>';
                                        html += '<td>' + statusList + '</td>';
                                    }

                                    html += '</tr>';
                                });
                            }

                            html += '</tbody></table></div>';
                            container.innerHTML = html;
                        } else {
                            container.innerHTML = '<div class="alert alert-danger">Error: ' + response.message + '</div>';
                        }
                    },
                    error: function(xhr) {
                        container.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan saat memuat data</div>';
                        console.error('Error:', xhr);
                    }
                });
            };
        </script>
    @endsection
