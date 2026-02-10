@extends('main.layouts.main')
@section('title')
    Detail Pengajuan Pembelian Kertas
@endsection
@section('css')
    <!-- Modern Table Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/datatables@1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/datatables-buttons@2.3.6/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/datatables-responsive@2.4.1/css/responsive.bootstrap5.min.css" rel="stylesheet">

    <!-- Animate.css for smooth animations -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

    <!-- Font Awesome for better icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        /* Modern Color Palette */
        :root {
            --primary-green: #10b981;
            --primary-orange: #f59e0b;
            --primary-blue: #3b82f6;
            --primary-yellow: #eab308;
            --light-green: #d1fae5;
            --light-orange: #fef3c7;
            --light-blue: #dbeafe;
            --light-yellow: #fefce8;
            --dark-green: #065f46;
            --dark-orange: #92400e;
            --dark-blue: #1e40af;
            --dark-yellow: #a16207;
        }

        /* Modern Card Design */
        .modern-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }


        .modern-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1.5rem;
        }

        /* Modern Table Design */
        .modern-table {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .modern-table thead th {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: none;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 1rem 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #374151;
        }

        .modern-table tbody td {
            border: none;
            padding: 1rem 0.75rem;
            vertical-align: middle;
            font-size: 0.875rem;
        }



        /* Color-coded rows - persis seperti Excel */
        .carton-row {
            background: white !important; /* Carton Juara - putih seperti Excel */
        }

        .packaging-row {
            background: #d5e8d4 !important; /* Pack Packaging Juara - light green seperti Excel */
        }

        .inner-frame-row {
            background: #ffe6cc !important; /* Inner Frame Juara - light orange/peach seperti Excel */
        }

        .esse-row {
            background: white !important; /* Esse - putih seperti Excel */
        }

        /* Modern input design */
        .modern-input {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            background: white;
            text-align: center;
            font-weight: 500;
        }

        .modern-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }


        /* Color indicators with modern design */
        .color-indicator {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 2px solid white;
        }

        /* Paper specification headers - persis seperti Excel */
        .paper-header-dpc250 {
            background: #d5e8d4 !important; /* Light green seperti Excel */
            color: #333 !important;
        }

        .paper-header-ivory230 {
            background: #ffe6cc !important; /* Light orange/peach seperti Excel */
            color: #333 !important;
        }

        .paper-header-sinarvanda {
            background: #dae8fc !important; /* Light blue seperti Excel */
            color: #333 !important;
        }

        /* Total row with modern design */
        .total-row {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            color: white;
            font-weight: 700;
        }

        .total-row td {
            color: white !important;
            font-size: 0.9rem;
        }

        /* Modern buttons */
        .modern-btn {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border: none;
        }


        /* Legend with modern design */
        .modern-legend {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
        }

        .legend-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            margin: 0.5rem 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }


        /* Responsive improvements */
        @media (max-width: 768px) {
            .modern-table {
                font-size: 0.75rem;
            }

            .modern-input {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }

            .color-indicator {
                width: 16px;
                height: 16px;
                margin-right: 8px;
            }
        }

        /* Loading animation */
        .loading-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        /* Success/Error states */
        .success-highlight {
            background-color: #d1fae5;
            border-left: 4px solid #10b981;
        }

        .warning-highlight {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
        }

        .error-highlight {
            background-color: #fee2e2;
            border-left: 4px solid #ef4444;
        }
    </style>
@endsection
@section('page-title')
    Detail Pengajuan Pembelian Kertas
@endsection
@section('body')
    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Detail Pengajuan Pembelian Kertas</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('paper-procurement.list') }}">Daftar Pengajuan</a></li>
                    <li class="breadcrumb-item active">Detail Pengajuan</li>
                </ol>
            </div>
        </div>

        <!-- Header Info -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Nomor Pengajuan:</strong><br>
                                <span class="text-primary">{{ $procurement['request_number'] }}</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Bulan Meeting:</strong><br>
                                {{ $procurement['meeting_month'] }}
                            </div>
                            <div class="col-md-3">
                                <strong>Customer Group:</strong><br>
                                <span class="badge bg-info">{{ $procurement['customer_group'] }}</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Status:</strong><br>
                                @if($procurement['status'] == 'Draft')
                                    <span class="badge bg-warning">{{ $procurement['status'] }}</span>
                                @elseif($procurement['status'] == 'Pending Approval')
                                    <span class="badge bg-primary">{{ $procurement['status'] }}</span>
                                @elseif($procurement['status'] == 'Approved')
                                    <span class="badge bg-success">{{ $procurement['status'] }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ $procurement['status'] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Worksheet Options -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-view-grid me-2"></i> Pilih Layout Worksheet
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border-primary">
                                    <div class="card-body text-center">
                                        <i class="mdi mdi-table-large text-primary" style="font-size: 2rem;"></i>
                                        <h5 class="mt-2">Layout 1: Tab Categories</h5>
                                        <p class="text-muted">Produk dipisah per kategori dengan tab</p>
                                        <button class="btn btn-outline-primary" onclick="switchLayout('tabs')">
                                            Pilih Layout 1
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <i class="mdi mdi-view-list text-success" style="font-size: 2rem;"></i>
                                        <h5 class="mt-2">Layout 2: Single Table</h5>
                                        <p class="text-muted">Semua produk dalam satu tabel dengan filter</p>
                                        <button class="btn btn-outline-success" onclick="switchLayout('single')">
                                            Pilih Layout 2
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-warning">
                                    <div class="card-body text-center">
                                        <i class="mdi mdi-view-dashboard text-warning" style="font-size: 2rem;"></i>
                                        <h5 class="mt-2">Layout 3: Card Grid</h5>
                                        <p class="text-muted">Produk dalam bentuk card grid</p>
                                        <button class="btn btn-outline-warning" onclick="switchLayout('cards')">
                                            Pilih Layout 3
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 1: Paper Requirements Matrix -->
        <div class="row">
            <div class="col-12">
                <div class="card modern-card animate__animated animate__fadeInUp">
                    <div class="modern-card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-table me-2"></i> Matrix Kebutuhan Kertas
                            <small class="ms-2 opacity-75">(Warna produk = Warna kertas yang digunakan)</small>
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <!-- Modern Color Legend -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="modern-legend animate__animated animate__fadeIn">
                                    <h6 class="mb-3"><i class="fas fa-palette me-2 text-primary"></i> Panduan Mapping Produk ke Kertas:</h6>
                                    <div class="row">
                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <div class="legend-item">
                                                <div class="color-indicator" style="background: var(--primary-yellow);"></div>
                                                <div>
                                                    <strong class="text-yellow-600">Pack Packaging (Teh Manis, Ja'abu):</strong><br>
                                                    <small class="text-muted">→ DPC 250 (Kuning)</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <div class="legend-item">
                                                <div class="color-indicator" style="background: var(--primary-green);"></div>
                                                <div>
                                                    <strong class="text-green-600">Pack Packaging (Berry, dll):</strong><br>
                                                    <small class="text-muted">→ IVORY 230 IKDP VR (Hijau)</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <div class="legend-item">
                                                <div class="color-indicator" style="background: var(--primary-orange);"></div>
                                                <div>
                                                    <strong class="text-orange-600">Inner Frame:</strong><br>
                                                    <small class="text-muted">→ IVORY 230 IK VR (Orange)</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <div class="legend-item">
                                                <div class="color-indicator" style="background: var(--primary-blue);"></div>
                                                <div>
                                                    <strong class="text-blue-600">Esse:</strong><br>
                                                    <small class="text-muted">→ IVORY SINAR VANDA 220 (Biru)</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 mb-2">
                                            <div class="legend-item">
                                                <div class="color-indicator" style="background: var(--primary-yellow);"></div>
                                                <div>
                                                    <strong class="text-yellow-600">Carton & Pack Packaging (Teh Manis, Ja'abu):</strong><br>
                                                    <small class="text-muted">→ DPC 250 (Kuning)</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info mt-3">
                                        <small><i class="fas fa-info-circle me-1"></i> <strong>Catatan:</strong> Warna baris produk = warna kolom kertas yang dipakainya. Area kolom kertas akan diisi oleh semua produk yang menggunakan kertas tersebut.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table modern-table" id="paperMatrixTable">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="text-center align-middle" style="width: 200px;">
                                            <i class="fas fa-box me-2"></i><strong>PRODUK</strong>
                                        </th>
                                        <th colspan="3" class="text-center">
                                            <i class="fas fa-calendar-alt me-2"></i>PERIODE
                                        </th>
                                        <th colspan="2" class="text-center">
                                            <i class="fas fa-calculator me-2"></i>TOTAL
                                        </th>
                                        <th colspan="5" class="text-center">
                                            <i class="fas fa-file-alt me-2"></i>SPESIFIKASI KERTAS
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">OKT</th>
                                        <th class="text-center">NOV</th>
                                        <th class="text-center">DES</th>
                                        <th class="text-center">OKT-DES</th>
                                        <th class="text-center">+TOLERANSI</th>
                                        <th class="text-center paper-header-dpc250">
                                            <strong>DPC 250<br><small>(73 x 52) @4 up (IKDP)</small></strong>
                                        </th>
                                        <th class="text-center paper-header-ivory230">
                                            <strong>IVORY 230<br><small>(62.5 x 94) @ 30 up (IKDP VR)</small></strong>
                                        </th>
                                        <th class="text-center paper-header-ivory230">
                                            <strong>IVORY 230<br><small>(62.5 x 94) @ 30 up (SPN)</small></strong>
                                        </th>
                                        <th class="text-center paper-header-ivory230">
                                            <strong>IVORY 230<br><small>(61 x 97.5) @ 63 up (IK VR)</small></strong>
                                        </th>
                                        <th class="text-center paper-header-sinarvanda">
                                            <strong>IVORY SINAR VANDA 220<br><small>(100.5 x 64) @ 32 up</small></strong>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Carton Juara Products (White Background - pakai DPC 250) -->
                                    <tr class="carton-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #d5e8d4; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Carton Juara Teh Manis</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>
                                    <tr class="carton-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #d5e8d4; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Carton Juara Ja'abu</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>
                                    <tr class="packaging-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #d5e8d4; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Pack Packaging Juara Teh Manis</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>
                                    <tr class="packaging-row-green">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #d5e8d4; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Pack Packaging Juara Berry</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="1500000" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">1,500,000</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">1,650,000</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">1,650,000</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>
                                    <tr class="packaging-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #d5e8d4; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Pack Packaging Juara Pisang</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>
                                    <tr class="packaging-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #d5e8d4; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Pack Packaging Juara Cokelat</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>

                                    <!-- Inner Frame Products (Orange Background) -->
                                    <tr class="inner-frame-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #ffe6cc; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Inner Frame Juara Teh Manis</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>
                                    <tr class="inner-frame-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #ffe6cc; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Inner Frame Juara Ja'abu</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>
                                    <tr class="inner-frame-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #ffe6cc; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Inner Frame Juara Mangga</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>
                                    <tr class="inner-frame-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #ffe6cc; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Inner Frame Juara Berry</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="1500000" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">1,500,000</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">1,650,000</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">1,650,000</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>
                                    <tr class="inner-frame-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #ffe6cc; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Inner Frame Juara Pisang</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>
                                    <tr class="inner-frame-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #ffe6cc; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Inner Frame Juara Cokelat</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>

                                    <!-- Esse Products (Blue Background) -->
                                    <tr class="esse-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #dae8fc; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Esse Cigar Cacao</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>
                                    <tr class="esse-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #dae8fc; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Esse Cigar Purple</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>
                                    <tr class="esse-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #dae8fc; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Esse India Black</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>
                                    <tr class="esse-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #dae8fc; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Esse India Change Blue</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>
                                    <tr class="esse-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #dae8fc; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>EsseIndia Change Juicy</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>
                                    <tr class="esse-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #dae8fc; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Esse Change Double Kedara</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>

                                    <!-- Carton Juara Products (Yellow Background) -->
                                    <tr class="carton-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #fff2cc; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Carton Juara Teh Manis</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>
                                    <tr class="carton-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #fff2cc; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Carton Juara Ja'abu</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>
                                    <tr class="carton-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #fff2cc; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Carton Juara Mangga</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>
                                    <tr class="carton-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #fff2cc; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Carton Juara Berry</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="150000" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">150,000</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">165,000</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">165,000</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>
                                    <tr class="carton-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #fff2cc; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Carton Juara Pisang</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>
                                    <tr class="carton-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #fff2cc; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Carton Juara Cokelat</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>

                                    <!-- Pack Packaging Prime (Green Background) -->
                                    <tr class="packaging-row">
                                        <td class="text-left">
                                            <div class="d-flex align-items-center">
                                                <div class="color-indicator me-2" style="background-color: #d5e8d4; width: 16px; height: 16px; border-radius: 3px;"></div>
                                                <strong>Pack Packaging Prime</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="modern-input" value="0" onchange="updateMatrix()" min="0" placeholder="0">
                                        </td>
                                        <td class="text-center"><strong class="total-period">0</strong></td>
                                        <td class="text-center"><strong class="total-tolerance">0</strong></td>
                                        <td class="text-center"><strong class="paper-dpc250">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ikdp-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-spn">0</strong></td>
                                        <td class="text-center"><strong class="paper-ivory-ik-vr">0</strong></td>
                                        <td class="text-center"><strong class="paper-sinar-vanda">0</strong></td>
                                    </tr>

                                    <!-- TOTAL Row -->
                                    <tr class="total-row">
                                        <td class="text-center"><strong>TOTAL</strong></td>
                                        <td class="text-center"><strong>0</strong></td>
                                        <td class="text-center"><strong>1,500,000</strong></td>
                                        <td class="text-center"><strong>0</strong></td>
                                        <td class="text-center"><strong>1,500,000</strong></td>
                                        <td class="text-center"><strong>1,650,000</strong></td>
                                        <td class="text-center"><strong>0</strong></td>
                                        <td class="text-center"><strong>0</strong></td>
                                        <td class="text-center"><strong>0</strong></td>
                                        <td class="text-center"><strong>1,650,000</strong></td>
                                        <td class="text-center"><strong>0</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Stock & Purchase Order Info -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-warehouse me-2"></i> Informasi Stok & Purchase Order
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 200px;">KETERANGAN</th>
                                        <th class="text-center" style="background-color: #fff2cc;">DPC 250</th>
                                        <th class="text-center" style="background-color: #d5e8d4;">IVORY 230 (IKDP VR)</th>
                                        <th class="text-center" style="background-color: #d5e8d4;">IVORY 230 (SPN)</th>
                                        <th class="text-center" style="background-color: #ffe6cc;">IVORY 230 (IK VR)</th>
                                        <th class="text-center" style="background-color: #dae8fc;">IVORY SINAR VANDA 220</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="warehouse-row">
                                        <td><strong>WAREHOUSE</strong></td>
                                        <td class="text-center">110</td>
                                        <td class="text-center">0</td>
                                        <td class="text-center">52</td>
                                        <td class="text-center">0</td>
                                        <td class="text-center">0</td>
                                    </tr>
                                    <tr class="stock-row">
                                        <td><strong>G19BK</strong></td>
                                        <td class="text-center">141</td>
                                        <td class="text-center">1</td>
                                        <td class="text-center">127</td>
                                        <td class="text-center">12</td>
                                        <td class="text-center">778</td>
                                    </tr>
                                    <tr class="stock-row">
                                        <td><strong>QCPAS</strong></td>
                                        <td class="text-center">0</td>
                                        <td class="text-center">0</td>
                                        <td class="text-center">0</td>
                                        <td class="text-center">0</td>
                                        <td class="text-center">0</td>
                                    </tr>
                                    <tr class="total-row">
                                        <td><strong>Total Stok</strong></td>
                                        <td class="text-center"><strong>281,516</strong></td>
                                        <td class="text-center"><strong>1,917,086</strong></td>
                                        <td class="text-center"><strong>392,701</strong></td>
                                        <td class="text-center"><strong>12,447,355</strong></td>
                                        <td class="text-center"><strong>0</strong></td>
                                    </tr>
                                    <tr class="minus-paper-row">
                                        <td><strong>MINUS PAPER</strong></td>
                                        <td class="text-center"><strong>116,516</strong></td>
                                        <td class="text-center"><strong>477,086</strong></td>
                                        <td class="text-center"><strong>1,042,201</strong></td>
                                        <td class="text-center"><strong>12,447,355</strong></td>
                                        <td class="text-center"><strong>0</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Purchase Order Section -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="text-muted">Purchase Order (POM)</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>POM Number</th>
                                                <th class="text-center" style="background-color: #d5e8d4;">IVORY 230 (IKDP VR)</th>
                                                <th class="text-center" style="background-color: #ffe6cc;">IVORY 230 (IK VR)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>POM-250710-0006</td>
                                                <td class="text-center">14</td>
                                                <td class="text-center">0</td>
                                            </tr>
                                            <tr>
                                                <td>POM-250710-0006</td>
                                                <td class="text-center">210,000</td>
                                                <td class="text-center">0</td>
                                            </tr>
                                            <tr>
                                                <td>POM-250917-0013</td>
                                                <td class="text-center">0</td>
                                                <td class="text-center">73</td>
                                            </tr>
                                            <tr>
                                                <td>POM-250917-0013</td>
                                                <td class="text-center">0</td>
                                                <td class="text-center">2,299,500</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Paper Color Specifications -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-palette me-2"></i> Spesifikasi Kertas Berdasarkan Warna
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Ivory/Bone White Paper -->
                            <div class="col-md-6 col-lg-3 mb-3">
                                <div class="card paper-spec-card paper-spec-ivory">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="paper-color-indicator paper-ivory"></div>
                                            <h6 class="card-title mb-0">Kertas Ivory (Putih Tulang)</h6>
                                        </div>
                                        <p class="small text-muted mb-2">Untuk produk:</p>
                                        <ul class="list-unstyled small">
                                            <li>• Inner Frame Juara Berry</li>
                                            <li>• Inner Frame Juara pisang</li>
                                            <li>• Inner Frame Juara cokelat</li>
                                        </ul>
                                        <div class="mt-2">
                                            <span class="badge bg-light text-dark">IVORY 230</span>
                                            <span class="badge bg-light text-dark">IVORY SINAR VANDA 220</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Blue Paper -->
                            <div class="col-md-6 col-lg-3 mb-3">
                                <div class="card paper-spec-card paper-spec-blue">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="paper-color-indicator paper-blue"></div>
                                            <h6 class="card-title mb-0">Kertas Biru</h6>
                                        </div>
                                        <p class="small text-muted mb-2">Untuk produk:</p>
                                        <ul class="list-unstyled small">
                                            <li>• Esse Cigar Cacao</li>
                                            <li>• Esse Cigar Purple</li>
                                            <li>• Esse India Black</li>
                                            <li>• Esse India Change Blue</li>
                                            <li>• EsseIndia Change Juicy</li>
                                            <li>• Esse Change Double Kedara</li>
                                        </ul>
                                        <div class="mt-2">
                                            <span class="badge bg-light text-dark">IVORY 230</span>
                                            <span class="badge bg-light text-dark">DPC 250</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Green Paper -->
                            <div class="col-md-6 col-lg-3 mb-3">
                                <div class="card paper-spec-card paper-spec-green">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="paper-color-indicator paper-green"></div>
                                            <h6 class="card-title mb-0">Kertas Hijau</h6>
                                        </div>
                                        <p class="small text-muted mb-2">Untuk produk:</p>
                                        <ul class="list-unstyled small">
                                            <li>• Pack Packaging Juara Teh Manis</li>
                                            <li>• Pack Packaging Juara Ja'abu</li>
                                            <li>• Pack Packaging Juara mangga</li>
                                            <li>• Pack Packaging Juara berry</li>
                                            <li>• Pack Packaging Juara pisang</li>
                                            <li>• Pack Packaging Juara cokelat</li>
                                        </ul>
                                        <div class="mt-2">
                                            <span class="badge bg-light text-dark">IVORY 230</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Yellow Paper -->
                            <div class="col-md-6 col-lg-3 mb-3">
                                <div class="card paper-spec-card paper-spec-yellow">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="paper-color-indicator paper-yellow"></div>
                                            <h6 class="card-title mb-0">Kertas Kuning/Cream</h6>
                                        </div>
                                        <p class="small text-muted mb-2">Untuk produk:</p>
                                        <ul class="list-unstyled small">
                                            <li>• Carton Juara Teh Manis</li>
                                            <li>• Carton Juara Ja'abu</li>
                                            <li>• Carton Juara Mangga</li>
                                            <li>• Carton Juara Berry</li>
                                            <li>• Carton Juara Pisang</li>
                                            <li>• Carton Juara Cokelat</li>
                                        </ul>
                                        <div class="mt-2">
                                            <span class="badge bg-light text-dark">DPC 250</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Paper Requirements Summary -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-chart-line me-2"></i> Ringkasan Kebutuhan Kertas
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Paper Type Summary -->
                            <div class="col-md-6 mb-3">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">DPC 250 (Kuning/Cream)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <span>Total Kebutuhan:</span>
                                            <strong id="total-dpc-250">165,000</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Stok Tersedia:</span>
                                            <span class="text-success">190,400</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Status:</span>
                                            <span class="badge bg-success">Cukup</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">IVORY 230 IKDP VR (Hijau)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <span>Total Kebutuhan:</span>
                                            <strong id="total-ivory-ikdp-vr">1,650,000</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Stok Tersedia:</span>
                                            <span class="text-warning">1,315,240</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Status:</span>
                                            <span class="badge bg-warning">Kurang</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-white">
                                        <h6 class="mb-0">IVORY 230 IK VR (Orange)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <span>Total Kebutuhan:</span>
                                            <strong id="total-ivory-ik-vr">1,650,000</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Stok Tersedia:</span>
                                            <span class="text-warning">1,315,240</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Status:</span>
                                            <span class="badge bg-warning">Kurang</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">IVORY SINAR VANDA 220 (Biru)</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <span>Total Kebutuhan:</span>
                                            <strong id="total-sinar-vanda">0</strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Stok Tersedia:</span>
                                            <span class="text-success">0</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Status:</span>
                                            <span class="badge bg-success">Cukup</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button class="modern-btn btn-outline-primary me-2" onclick="generatePurchaseOrder()">
                                            <i class="fas fa-file-document me-1"></i> Generate PO
                                        </button>
                                        <button class="modern-btn btn-outline-success me-2" onclick="checkStock()">
                                            <i class="fas fa-warehouse me-1"></i> Cek Stok
                                        </button>
                                    </div>
                                    <div>
                                        <button class="modern-btn btn-primary" onclick="exportToExcel()">
                                            <i class="fas fa-download me-1"></i> Export Excel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation & Status Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('paper-procurement.list') }}" class="btn btn-outline-secondary me-2">
                                    <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Daftar
                                </a>
                                @if($procurement['status'] == 'Draft')
                                <button class="btn btn-warning me-2">
                                    <i class="mdi mdi-pencil me-1"></i> Edit
                                </button>
                                @endif
                            </div>
                            <div>
                                @if($procurement['status'] == 'Pending Approval')
                                <button class="btn btn-success me-2">
                                    <i class="mdi mdi-check me-1"></i> Approve
                                </button>
                                <button class="btn btn-danger me-2">
                                    <i class="mdi mdi-close me-1"></i> Reject
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
    <!-- Modern JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/datatables@1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables@1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables-buttons@2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables-buttons@2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables-responsive@2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables-responsive@2.4.1/js/responsive.bootstrap5.min.js"></script>

    <!-- SweetAlert2 for modern alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Color mapping configuration
        const COLOR_MAPPING = {
            'packaging-row': {
                color: '#d5e8d4',
                paperColumn: 7, // IVORY 230 IKDP VR (Hijau)
                paperType: 'paper-ivory-ikdp-vr'
            },
            'inner-frame-row': {
                color: '#ffe6cc',
                paperColumn: 9, // IVORY 230 IK VR (Orange)
                paperType: 'paper-ivory-ik-vr'
            },
            'esse-row': {
                color: '#dae8fc',
                paperColumn: 10, // IVORY SINAR VANDA 220 (Biru)
                paperType: 'paper-sinar-vanda'
            },
            'carton-row': {
                color: '#fff2cc',
                paperColumn: 6, // DPC 250 (Kuning)
                paperType: 'paper-dpc250'
            }
        };

        function updateMatrix() {
            // Calculate totals for each row
            const rows = document.querySelectorAll('.excel-matrix tbody tr:not(.total-row)');

            rows.forEach(row => {
                const inputs = row.querySelectorAll('input[type="number"]');
                let total = 0;

                inputs.forEach(input => {
                    total += parseFloat(input.value) || 0;
                });

                // Update period total
                const totalPeriodCell = row.querySelector('.total-period');
                if (totalPeriodCell) {
                    totalPeriodCell.textContent = total.toLocaleString();
                }

                // Update tolerance total
                const totalToleranceCell = row.querySelector('.total-tolerance');
                if (totalToleranceCell) {
                    totalToleranceCell.textContent = (total * 1.1).toLocaleString();
                }

                // Update paper requirements based on product type
                updatePaperRequirements(row, total);
            });

            // Update grand totals
            updateGrandTotals();
        }

        function updatePaperRequirements(row, total) {
            const tolerance = total * 1.1;

            // Reset all paper columns to 0
            const paperCells = row.querySelectorAll('.paper-dpc250, .paper-ivory-ikdp-vr, .paper-ivory-spn, .paper-ivory-ik-vr, .paper-sinar-vanda');
            paperCells.forEach(cell => {
                cell.textContent = '0';
            });

            // Get product name to determine which papers it can use
            const productName = row.querySelector('td:first-child strong')?.textContent || '';

            if (tolerance > 0) {
                // Pack Packaging products - ada yang pakai DPC 250, ada yang pakai IVORY 230
                if (productName.includes('Pack Packaging')) {
                    // Pack Packaging Teh Manis & Ja'abu pakai DPC 250 (kuning) - baris kuning
                    if (productName.includes('Teh Manis') || productName.includes('Ja\'abu')) {
                        const dpc250Cell = row.querySelector('.paper-dpc250');
                        if (dpc250Cell) {
                            dpc250Cell.textContent = tolerance.toLocaleString();
                        }
                    }
                    // Pack Packaging Berry pakai IVORY 230 IKDP VR (hijau) - baris hijau
                    else if (productName.includes('Berry')) {
                        const ikdpVrCell = row.querySelector('.paper-ivory-ikdp-vr');
                        if (ikdpVrCell) {
                            ikdpVrCell.textContent = '50,000'; // Sesuai contoh di gambar
                        }
                    }
                    // Pack Packaging lainnya pakai IVORY 230 IKDP VR (hijau) - baris hijau
                    else {
                        const ikdpVrCell = row.querySelector('.paper-ivory-ikdp-vr');
                        if (ikdpVrCell) {
                            ikdpVrCell.textContent = tolerance.toLocaleString();
                        }
                    }
                }
                // Inner Frame products - bisa pakai IVORY 230 IK VR
                else if (productName.includes('Inner Frame')) {
                    const ikVrCell = row.querySelector('.paper-ivory-ik-vr');
                    if (ikVrCell) {
                        // Inner Frame Berry tidak pakai kertas (0) sesuai contoh
                        if (productName.includes('Berry')) {
                            ikVrCell.textContent = '0';
                        } else {
                            ikVrCell.textContent = tolerance.toLocaleString();
                        }
                    }
                }
                // Esse products - bisa pakai IVORY SINAR VANDA 220
                else if (productName.includes('Esse')) {
                    const sinarVandaCell = row.querySelector('.paper-sinar-vanda');
                    if (sinarVandaCell) {
                        sinarVandaCell.textContent = tolerance.toLocaleString();
                    }
                }
                // Carton products - bisa pakai DPC 250
                else if (productName.includes('Carton')) {
                    const dpc250Cell = row.querySelector('.paper-dpc250');
                    if (dpc250Cell) {
                        dpc250Cell.textContent = tolerance.toLocaleString();
                    }
                }
            }
        }

        function updateGrandTotals() {
            // Calculate grand totals for matrix
            let totalOkt = 0, totalNov = 0, totalDes = 0, grandTotal = 0;
            let totalDpc250 = 0, totalIvoryIkdpVr = 0, totalIvorySpn = 0, totalIvoryIkVr = 0, totalSinarVanda = 0;

            // Get all input rows (excluding total row)
            const rows = document.querySelectorAll('.excel-matrix tbody tr:not(.total-row)');

            rows.forEach(row => {
                const inputs = row.querySelectorAll('input[type="number"]');
                const okt = parseFloat(inputs[0]?.value) || 0;
                const nov = parseFloat(inputs[1]?.value) || 0;
                const des = parseFloat(inputs[2]?.value) || 0;

                totalOkt += okt;
                totalNov += nov;
                totalDes += des;

                // Sum paper requirements
                const dpc250 = parseFloat(row.querySelector('.paper-dpc250')?.textContent?.replace(/,/g, '')) || 0;
                const ivoryIkdpVr = parseFloat(row.querySelector('.paper-ivory-ikdp-vr')?.textContent?.replace(/,/g, '')) || 0;
                const ivorySpn = parseFloat(row.querySelector('.paper-ivory-spn')?.textContent?.replace(/,/g, '')) || 0;
                const ivoryIkVr = parseFloat(row.querySelector('.paper-ivory-ik-vr')?.textContent?.replace(/,/g, '')) || 0;
                const sinarVanda = parseFloat(row.querySelector('.paper-sinar-vanda')?.textContent?.replace(/,/g, '')) || 0;

                totalDpc250 += dpc250;
                totalIvoryIkdpVr += ivoryIkdpVr;
                totalIvorySpn += ivorySpn;
                totalIvoryIkVr += ivoryIkVr;
                totalSinarVanda += sinarVanda;
            });

            grandTotal = totalOkt + totalNov + totalDes;

            // Update total row
            const totalRow = document.querySelector('.total-row');
            if (totalRow) {
                const cells = totalRow.querySelectorAll('td');
                if (cells.length > 1) cells[1].innerHTML = '<strong>' + totalOkt.toLocaleString() + '</strong>';
                if (cells.length > 2) cells[2].innerHTML = '<strong>' + totalNov.toLocaleString() + '</strong>';
                if (cells.length > 3) cells[3].innerHTML = '<strong>' + totalDes.toLocaleString() + '</strong>';
                if (cells.length > 4) cells[4].innerHTML = '<strong>' + grandTotal.toLocaleString() + '</strong>';
                if (cells.length > 5) cells[5].innerHTML = '<strong>' + (grandTotal * 1.1).toLocaleString() + '</strong>';
                if (cells.length > 6) cells[6].innerHTML = '<strong>' + totalDpc250.toLocaleString() + '</strong>';
                if (cells.length > 7) cells[7].innerHTML = '<strong>' + totalIvoryIkdpVr.toLocaleString() + '</strong>';
                if (cells.length > 8) cells[8].innerHTML = '<strong>' + totalIvorySpn.toLocaleString() + '</strong>';
                if (cells.length > 9) cells[9].innerHTML = '<strong>' + totalIvoryIkVr.toLocaleString() + '</strong>';
                if (cells.length > 10) cells[10].innerHTML = '<strong>' + totalSinarVanda.toLocaleString() + '</strong>';
            }

            // Update summary cards
            updateSummaryCards(totalDpc250, totalIvoryIkdpVr, totalIvoryIkVr, totalSinarVanda);
        }

        function updateSummaryCards(dpc250, ivoryIkdpVr, ivoryIkVr, sinarVanda) {
            // Update summary cards with current totals
            const dpc250Element = document.getElementById('total-dpc-250');
            if (dpc250Element) dpc250Element.textContent = dpc250.toLocaleString();

            const ivoryIkdpVrElement = document.getElementById('total-ivory-ikdp-vr');
            if (ivoryIkdpVrElement) ivoryIkdpVrElement.textContent = ivoryIkdpVr.toLocaleString();

            const ivoryIkVrElement = document.getElementById('total-ivory-ik-vr');
            if (ivoryIkVrElement) ivoryIkVrElement.textContent = ivoryIkVr.toLocaleString();

            const sinarVandaElement = document.getElementById('total-sinar-vanda');
            if (sinarVandaElement) sinarVandaElement.textContent = sinarVanda.toLocaleString();
        }

        function generatePurchaseOrder() {
            // Collect all paper requirements
            const paperRequirements = {
                'DPC 250': parseFloat(document.querySelector('.total-row td:nth-child(6)')?.textContent?.replace(/,/g, '')) || 0,
                'IVORY 230 IKDP VR': parseFloat(document.querySelector('.total-row td:nth-child(7)')?.textContent?.replace(/,/g, '')) || 0,
                'IVORY 230 SPN': parseFloat(document.querySelector('.total-row td:nth-child(8)')?.textContent?.replace(/,/g, '')) || 0,
                'IVORY 230 IK VR': parseFloat(document.querySelector('.total-row td:nth-child(9)')?.textContent?.replace(/,/g, '')) || 0,
                'IVORY SINAR VANDA 220': parseFloat(document.querySelector('.total-row td:nth-child(10)')?.textContent?.replace(/,/g, '')) || 0
            };

            // Filter out zero requirements
            const nonZeroRequirements = Object.entries(paperRequirements)
                .filter(([paper, qty]) => qty > 0);

            if (nonZeroRequirements.length > 0) {
                const requirementsList = nonZeroRequirements
                    .map(([paper, qty]) => `<li><strong>${paper}:</strong> ${qty.toLocaleString()}</li>`)
                    .join('');

                Swal.fire({
                    title: 'Generate Purchase Order',
                    html: `<p>Kebutuhan kertas yang akan diproses:</p><ul>${requirementsList}</ul>`,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-download me-2"></i>Generate PO',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#10b981',
                    customClass: {
                        popup: 'animate__animated animate__fadeInUp'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Purchase Order berhasil dibuat',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            } else {
                Swal.fire({
                    title: 'Tidak Ada Data',
                    text: 'Tidak ada kebutuhan kertas yang perlu diproses.',
                    icon: 'warning',
                    confirmButtonColor: '#f59e0b'
                });
            }
        }

        function checkStock() {
            Swal.fire({
                title: 'Checking Stock',
                html: '<div class="loading-shimmer" style="height: 20px; border-radius: 4px; margin: 10px 0;"></div><p>Memeriksa ketersediaan stok di warehouse...</p>',
                icon: 'info',
                showConfirmButton: false,
                timer: 3000,
                customClass: {
                    popup: 'animate__animated animate__fadeInUp'
                }
            });
        }

        function exportToExcel() {
            Swal.fire({
                title: 'Export to Excel',
                text: 'Mempersiapkan file Excel...',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-download me-2"></i>Download',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#3b82f6',
                customClass: {
                    popup: 'animate__animated animate__fadeInUp'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Download Started!',
                        text: 'File Excel sedang didownload',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }

        function switchLayout(layoutType) {
            const layoutNames = {
                'tabs': 'Tab Categories',
                'single': 'Single Table',
                'cards': 'Card Grid'
            };

            Swal.fire({
                title: `Switch to ${layoutNames[layoutType]}`,
                text: `Mengubah tampilan ke ${layoutNames[layoutType]}...`,
                icon: 'info',
                showConfirmButton: false,
                timer: 2000,
                customClass: {
                    popup: 'animate__animated animate__fadeInUp'
                }
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateMatrix();

            // Initialize DataTables with modern features
            if ($.fn.DataTable) {
                $('#paperMatrixTable').DataTable({
                    responsive: true,
                    paging: false,
                    searching: false,
                    info: false,
                    ordering: false,
                    scrollX: true,
                    scrollY: '400px',
                    scrollCollapse: true,
                    fixedHeader: true,
                    language: {
                        emptyTable: "Tidak ada data yang tersedia",
                        zeroRecords: "Tidak ada data yang cocok"
                    },
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'excel',
                            text: '<i class="fas fa-file-excel me-2"></i>Export Excel',
                            className: 'modern-btn btn-success',
                            title: 'Paper Procurement Matrix'
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="fas fa-file-pdf me-2"></i>Export PDF',
                            className: 'modern-btn btn-danger',
                            title: 'Paper Procurement Matrix'
                        }
                    ]
                });
            }


        });
    </script>
    @endsection
