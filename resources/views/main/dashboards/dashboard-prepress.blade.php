@extends('main.layouts.main')
@section('title')
    Dashboard
@endsection
@section('css')
    <style>
        /* Fix for tab switching - ensure only one tab is visible at a time */
        .tab-pane {
            display: none !important;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .tab-pane.active {
            display: block !important;
        }

        .tab-pane.show {
            display: block !important;
            opacity: 1;
        }

        .tab-pane.fade {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .tab-pane.fade.show {
            opacity: 1;
        }

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

        .dashboard-column {
            min-height: 600px;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }

        .job-item {
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }

        .job-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .job-item.completed {
            border-left-color: #28a745;
        }

        .job-item.in-progress {
            border-left-color: #ffc107;
        }

        /* Refresh Animation */
        .fa-spin {
            animation: fa-spin 2s infinite linear;
        }

        @keyframes fa-spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        /* Loading indicator */
        #prepress-loading {
            width: 1rem;
            height: 1rem;
        }

        /* Smooth transition for updated values */
        .stat-value {
            transition: all 0.3s ease;
        }

        /* Prepress Stats Card Styling */
        .prepress-stat-card {
            background: #ffffff;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }

        .prepress-stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #4e73df, #224abe);
        }

        .prepress-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.35rem 1.75rem 0 rgba(58, 59, 69, 0.2);
            border-color: #d1d3e2;
        }

        .prepress-stat-card .card-body {
            padding: 1rem 1.25rem;
        }

        .prepress-stat-card h3 {
            color: #212529;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            line-height: 1.2;
        }

        .prepress-stat-card small {
            color: #858796;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Supplier Timeline Styles */
        .supplier-timeline {
            position: relative;
            padding: 20px 0;
        }

        .supplier-timeline::before {
            display: none;
        }

        .timeline-item-supplier {
            position: relative;
            margin-bottom: 30px;
            padding-left: 0;
        }

        .timeline-item-supplier::before {
            display: none;
        }

        .timeline-item-supplier.completed::before {
            display: none;
        }

        .timeline-item-supplier .truck-icon {
            display: none;
        }

        .timeline-content-supplier {
            background: #fff;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .timeline-content-supplier:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }

        .timeline-time-supplier {
            font-size: 12px;
            color: #6c757d;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .timeline-date-supplier {
            font-size: 11px;
            color: #858796;
            font-style: italic;
        }

        .timeline-title-supplier {
            font-size: 14px;
            font-weight: 600;
            color: #5a5c69;
            margin-bottom: 8px;
        }

        .timeline-supplier-name {
            font-size: 13px;
            color: #4e73df;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .timeline-po-number {
            font-size: 12px;
            color: #6c757d;
            font-family: monospace;
            background: #f8f9fc;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
        }

        .timeline-status-badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            border-radius: 12px;
            margin-top: 8px;
        }

        .timeline-status-completed {
            background: #d4edda;
            color: #155724;
        }

        /* New Timeline Styles - Horizontal Layout */
        .timeline-date-header {
            position: relative;
            margin: 20px 0;
            text-align: center;
        }

        .timeline-date-line {
            display: none;
        }

        .timeline-date-label {
            position: relative;
            z-index: 2;
            background: white;
            padding: 8px 16px;
            border-radius: 20px;
            border: 2px solid #4e73df;
            display: inline-block;
            font-weight: 600;
            color: #4e73df;
            box-shadow: 0 2px 8px rgba(78, 115, 223, 0.2);
            font-size: 14px;
        }

        .timeline-trucks-container {
            display: flex;
            flex-direction: row;
            gap: 15px;
            margin: 15px 0;
            padding: 0 20px;
            overflow-x: auto;
            scrollbar-width: thin;
        }

        .timeline-trucks-container::-webkit-scrollbar {
            height: 6px;
        }

        .timeline-trucks-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .timeline-trucks-container::-webkit-scrollbar-thumb {
            background: #4e73df;
            border-radius: 3px;
        }

        .timeline-truck-item {
            background: white;
            border: 2px solid #e3e6f0;
            border-radius: 12px;
            padding: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 180px;
            max-width: 220px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .timeline-truck-item:hover {
            border-color: #4e73df;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.2);
        }

        .truck-icon-container {
            position: relative;
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .truck-icon-small {
            color: white;
            font-size: 18px;
        }

        .truck-number {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ffc107;
            color: #333;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            font-weight: bold;
        }

        .truck-info {
            width: 100%;
        }

        .truck-supplier {
            font-weight: 600;
            color: #5a5c69;
            margin-bottom: 4px;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .truck-time {
            font-size: 11px;
            color: #6c757d;
            margin-bottom: 4px;
        }

        .truck-po {
            font-size: 10px;
            color: #858796;
            font-family: monospace;
            background: #f8f9fc;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 4px;
        }

        .truck-status {
            margin-top: 4px;
        }

        .truck-status i {
            font-size: 16px;
        }

        /* Horizontal Timeline Styles */
        .horizontal-timeline-container {
            display: flex;
            flex-direction: row;
            gap: 20px;
            padding: 20px;
            overflow-x: auto;
            scrollbar-width: thin;
        }

        .horizontal-timeline-container::-webkit-scrollbar {
            height: 6px;
        }

        .horizontal-timeline-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .horizontal-timeline-container::-webkit-scrollbar-thumb {
            background: #4e73df;
            border-radius: 3px;
        }

        .timeline-date-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 200px;
            flex-shrink: 0;
        }

        .timeline-date-header-horizontal {
            margin-bottom: 15px;
        }

        .timeline-date-label-horizontal {
            background: white;
            padding: 8px 12px;
            border-radius: 20px;
            border: 2px solid #4e73df;
            display: inline-block;
            font-weight: 600;
            color: #4e73df;
            box-shadow: 0 2px 8px rgba(78, 115, 223, 0.2);
            font-size: 14px;
            text-align: center;
        }

        .timeline-trucks-container-horizontal {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
        }

        .timeline-truck-item-horizontal {
            background: white;
            border: 2px solid #e3e6f0;
            border-radius: 10px;
            padding: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 6px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            min-height: 120px;
        }

        .timeline-truck-item-horizontal:hover {
            border-color: #4e73df;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.2);
        }

        .truck-icon-container-horizontal {
            position: relative;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .truck-info-horizontal {
            width: 100%;
        }

        .truck-info-horizontal .truck-supplier {
            font-weight: 600;
            color: #5a5c69;
            margin-bottom: 3px;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .truck-info-horizontal .truck-time {
            font-size: 11px;
            color: #6c757d;
            margin-bottom: 3px;
        }

        .truck-info-horizontal .truck-po {
            font-size: 10px;
            color: #858796;
            font-family: monospace;
            background: #f8f9fc;
            padding: 2px 4px;
            border-radius: 3px;
            display: inline-block;
        }

        /* Delivery Detail Modal Styles */
        .delivery-detail-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
        }

        .delivery-header {
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            color: white;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .delivery-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .delivery-info h5 {
            margin: 0;
            font-weight: 600;
            font-size: 18px;
        }

        .delivery-info p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .delivery-content {
            padding: 20px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #f8f9fc;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-item label {
            font-weight: 600;
            color: #5a5c69;
            min-width: 120px;
            margin: 0;
            font-size: 13px;
        }

        .detail-item span {
            color: #858796;
            font-size: 14px;
        }

        .po-number {
            font-family: monospace;
            background: #f8f9fc;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
        }

        .delivery-id {
            font-family: monospace;
            color: #4e73df;
            font-weight: 600;
        }

        .delivery-actions .alert {
            margin: 0;
            border-radius: 8px;
        }

        .job-item.pending {
            border-left-color: #6c757d;
        }

        .job-item.urgent {
            border-left-color: #dc3545;
        }

        .nav-tabs .nav-link {
            color: #6c757d;
            border: none;
            border-bottom: 2px solid transparent;
            border-radius: 0;
            padding: 12px 20px;
            font-weight: 500;
        }

        .nav-tabs .nav-link:hover {
            color: #495057;
            border-bottom-color: #dee2e6;
        }

        .nav-tabs .nav-link.active {
            color: #007bff;
            border-bottom-color: #007bff;
            background: transparent;
        }

        .tab-content {
            padding-top: 20px;
        }

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

        /* Table styling untuk dashboard */
        .table-responsive {
            border-radius: 0.375rem;
        }

        .table thead th {
            border-top: none;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }

        .badge-light {
            background-color: #f8f9fa;
            color: #495057;
        }

        /* Card header styling */
        .card-header {
            border-bottom: none;
            padding: 1rem 1.25rem;
        }

        .card-header h5 {
            margin-bottom: 0;
            font-weight: 600;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }

            .badge {
                font-size: 0.7rem;
                padding: 0.25rem 0.5rem;
            }
        }

        /* Development Dashboard Styling */
        .info-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .info-card h4 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .horizontal-timeline {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
            margin: 20px 0;
            padding: 0 30px;
            min-height: 100px;
        }

        .timeline-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
            flex: 1;
            min-width: 0;
        }

        .timeline-dot {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            margin-bottom: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            position: relative;
            z-index: 3;
        }

        .timeline-dot.bg-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
        }

        .timeline-dot.bg-info {
            background: linear-gradient(135deg, #17a2b8, #138496);
        }

        .timeline-dot.bg-warning {
            background: linear-gradient(135deg, #ffc107, #e0a800);
        }

        .timeline-dot.bg-success {
            background: linear-gradient(135deg, #28a745, #1e7e34);
        }

        .timeline-dot.bg-secondary {
            background: linear-gradient(135deg, #6c757d, #545b62);
        }

        .timeline-item.completed .timeline-dot {
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .timeline-label {
            font-size: 12px;
            font-weight: 600;
            color: #495057;
            text-align: center;
            max-width: 70px;
            line-height: 1.2;
            margin-top: 8px;
            word-wrap: break-word;
            white-space: normal;
        }

        .timeline-item.completed .timeline-label {
            color: #28a745;
            font-weight: 700;
        }

        .progress-summary {
            margin-top: 20px;
        }

        .progress {
            height: 20px;
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(135deg, #007bff, #0056b3);
            transition: width 0.6s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 12px;
        }

        /* Job Attention Items Styling */
        .job-attention-item {
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }

        .job-attention-item:hover {
            background-color: #f8f9fa;
            border-left-color: #007bff;
            transform: translateX(2px);
        }

        .job-attention-item.urgent {
            border-left-color: #dc3545;
            background-color: #fff5f5;
        }

        .job-attention-item.urgent:hover {
            background-color: #ffe6e6;
        }

        .job-attention-item.high-priority {
            border-left-color: #ffc107;
            background-color: #fffbf0;
        }

        .job-attention-item.high-priority:hover {
            background-color: #fff8e1;
        }

        /* Supplier Dashboard Styling */
        .supplier-ticket-item {
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }

        .supplier-ticket-item:hover {
            background-color: #f8f9fa;
            border-left-color: #28a745;
            transform: translateX(2px);
        }

        .supplier-ticket-item.urgent {
            border-left-color: #dc3545;
            background-color: #fff5f5;
        }

        .supplier-ticket-item.urgent:hover {
            background-color: #ffe6e6;
        }

        .supplier-ticket-item.high-priority {
            border-left-color: #ffc107;
            background-color: #fffbf0;
        }

        .supplier-ticket-item.high-priority:hover {
            background-color: #fff8e1;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: -22px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #dee2e6;
        }

        .timeline-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-left: 10px;
        }

        .timeline-title {
            font-weight: 600;
            margin-bottom: 5px;
            color: #495057;
        }

        .timeline-text {
            margin-bottom: 0;
            color: #6c757d;
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
                    <div class="card-body" id="dashboardTabs">
                        <h4 class="card-title" style="margin-bottom: 30px;">List Dashboard</h4>

                        @if (auth()->user()->divisi == 11)

                        <a href="{{ route('dashboard.security') }}" class="nav-link active btn btn-info mb-2 w-100 text-left" id="security-tab" data-bs-toggle="tab"
                            data-bs-target="#security" type="button" role="tab" aria-controls="security"
                            aria-selected="false">
                            <i class="mdi mdi-shield-account mr-2"></i>
                            Dashboard Security
                        </a>
                        @elseif (auth()->user()->divisi != 8)
                        <a href="{{ route('dashboard') }}" class="nav-link btn btn-info mb-2 w-100 text-left" id="overview-tab" data-bs-toggle="tab"
                            data-bs-target="#overview" type="button" role="tab" aria-controls="overview"
                            aria-selected="true">
                            <i class="mdi mdi-view-dashboard mr-2"></i>
                            Overview Data
                        </a>

                        <a href="{{ route('dashboard.ppic') }}" class="nav-link btn btn-info mb-2 w-100 text-left" id="ppic-tab" data-bs-toggle="tab"
                            data-bs-target="#ppic" type="button" role="tab" aria-controls="ppic" aria-selected="false">
                            <i class="mdi mdi-view-dashboard mr-2"></i>
                            Dashboard PPIC
                        </a>

                        <a href="{{ route('dashboard.prepress') }}" class="nav-link active btn btn-info mb-2 w-100 text-left" id="prepress-tab" data-bs-toggle="tab"
                            data-bs-target="#prepress" type="button" role="tab" aria-controls="prepress"
                            aria-selected="true">
                            <i class="mdi mdi-view-dashboard mr-2"></i>
                            Dashboard Prepress
                        </a>

                        {{-- <a href="{{ route('dashboard.development') }}" class="nav-link btn btn-info mb-2 w-100 text-left" id="development-tab" data-bs-toggle="tab"
                            data-bs-target="#development" type="button" role="tab" aria-controls="development"
                            aria-selected="false">
                            <i class="mdi mdi-flask mr-2"></i>
                            Development Item
                        </a> --}}
                        @endif

                        @if(auth()->user()->divisi == 8 || auth()->user()->divisi == 1)
                        {{-- <a href="{{ route('dashboard.supplier') }}" class="nav-link btn btn-info mb-2 w-100 text-left" id="supplier-tab" data-bs-toggle="tab"
                            data-bs-target="#supplier" type="button" role="tab" aria-controls="supplier"
                            aria-selected="false">
                            <i class="mdi mdi-truck mr-2"></i>
                            Dashboard Supplier
                        </a> --}}
                        @endif


                        @if(auth()->user()->divisi == 1)
                        <a href="{{ route('dashboard.security') }}" class="nav-link btn btn-info mb-2 w-100 text-left" id="security-tab" data-bs-toggle="tab"
                            data-bs-target="#security" type="button" role="tab" aria-controls="security"
                            aria-selected="true">
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
                        <h4>Welcome to the Dashboard! {{ auth()->user()->name }},
                            {{ $user->divisi_name ?? '-' }}</h4>

                        <div class="tab-content" id="dashboardTabContent">

                            <!-- Prepress Full Dashboard Tab -->
                            <div class="tab-pane show active fade" id="prepress" role="tabpanel" aria-labelledby="prepress-tab">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card border-left-success">
                                            <div class="card-header bg-info text-white">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <h4 class="card-title mb-0 text-white">
                                                        <i class="mdi mdi-palette mr-2"></i>
                                                        Dashboard Prepress
                                                    </h4>
                                                    <div class="d-flex align-items-center">
                                                        {{-- Search Job Order --}}
                                                        <div class="input-group input-group-sm mr-2" style="width: 250px;">
                                                            <input type="text" class="form-control" id="search-job-order" placeholder="Cari nomor job order..." autocomplete="off">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-light" type="button" id="btn-search-job-order" title="Cari Job Order">
                                                                    <i class="mdi mdi-magnify"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-light mr-2" id="btn-refresh-prepress" title="Refresh Data">
                                                            <i class="mdi mdi-refresh" id="refresh-icon"></i>
                                                            <span id="refresh-text">Refresh</span>
                                                        </button>
                                                        <div class="spinner-border spinner-border-sm text-light d-none" id="prepress-loading" role="status" style="width: 1rem; height: 1rem;">
                                                            <span class="sr-only">Loading...</span>
                                                        </div>
                                                        <small class="ml-2 text-white-50" id="last-update-time">
                                                            <i class="mdi mdi-clock-outline"></i>
                                                            <span id="update-time-text">Memuat...</span>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <!-- Prepress Statistics Row -->
                                                <div class="row" id="prepress-stats-row">
                                                    <div class="col-md-3">
                                                        <div class="card prepress-stat-card">
                                                            <div class="card-body text-center">
                                                                <h3 class="mb-0 font-weight-bold" id="stat-finish">
                                                                    {{ $prepressData['stats']['finish'] ?? 0 }}</h3>
                                                                <small>Finish</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="card prepress-stat-card">
                                                            <div class="card-body text-center">
                                                                <h3 class="mb-0 font-weight-bold" id="stat-in-progress">
                                                                    {{ $prepressData['stats']['in_progress'] ?? 0 }}</h3>
                                                                <small>In Progress</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="card prepress-stat-card">
                                                            <div class="card-body text-center">
                                                                <h3 class="mb-0 font-weight-bold" id="stat-assigned">
                                                                    {{ $prepressData['stats']['assigned'] ?? 0 }}</h3>
                                                                <small>Assigned</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="card prepress-stat-card">
                                                            <div class="card-body text-center">
                                                                <h3 class="mb-0 font-weight-bold" id="stat-approved">
                                                                    {{ $prepressData['stats']['approved'] ?? 0 }}</h3>
                                                                <small>Approved</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- <a href="{{ route('prepress.job-order.index') }}" class="btn btn-info w-100 mb-3">
                                                    <i class="mdi mdi-plus mr-1" ></i>
                                                    Input Job Order
                                                </a> --}}

                                                <!-- Department Statistics Row -->
                                                <div class="row mb-4" id="department-stats-row">
                                                    <div class="col-md-6">
                                                        <div class="card border-left-primary">
                                                            <div class="card-header bg-info text-white">
                                                                <h5 class="card-title mb-0 text-white">
                                                                    <i class="mdi mdi-account-group mr-2"></i>
                                                                    Marketing Department
                                                                    <span
                                                                        class="badge badge-light ml-2" id="marketing-total">{{ $prepressData['departments']['marketing']['stats']['total'] ?? 0 }}</span>
                                                                </h5>
                                                            </div>
                                                            <div class="card-body p-0">
                                                                <div class="table-responsive">
                                                                    <table class="table table-hover mb-0">
                                                                        <thead class="thead-light">
                                                                            <tr>
                                                                                <th>Status</th>
                                                                                <th>Count</th>
                                                                                <th>Percentage</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="marketing-stats-body">
                                                                            <tr>
                                                                                <td>
                                                                                    <span
                                                                                        class="badge badge-success">Finish</span>
                                                                                </td>
                                                                                <td id="marketing-finish">{{ $prepressData['departments']['marketing']['stats']['finish'] ?? 0 }}
                                                                                </td>
                                                                                <td id="marketing-finish-pct">
                                                                                    @if (($prepressData['departments']['marketing']['stats']['total'] ?? 0) > 0)
                                                                                        {{ round((($prepressData['departments']['marketing']['stats']['finish'] ?? 0) / ($prepressData['departments']['marketing']['stats']['total'] ?? 1)) * 100, 1) }}%
                                                                                    @else
                                                                                        0%
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <span class="badge badge-warning">In
                                                                                        Progress</span>
                                                                                </td>
                                                                                <td id="marketing-in-progress">{{ $prepressData['departments']['marketing']['stats']['in_progress'] ?? 0 }}
                                                                                </td>
                                                                                <td id="marketing-in-progress-pct">
                                                                                    @if (($prepressData['departments']['marketing']['stats']['total'] ?? 0) > 0)
                                                                                        {{ round((($prepressData['departments']['marketing']['stats']['in_progress'] ?? 0) / ($prepressData['departments']['marketing']['stats']['total'] ?? 1)) * 100, 1) }}%
                                                                                    @else
                                                                                        0%
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <span
                                                                                        class="badge badge-secondary">Assigned</span>
                                                                                </td>
                                                                                <td id="marketing-assigned">{{ $prepressData['departments']['marketing']['stats']['assigned'] ?? 0 }}
                                                                                </td>
                                                                                <td id="marketing-assigned-pct">
                                                                                    @if (($prepressData['departments']['marketing']['stats']['total'] ?? 0) > 0)
                                                                                        {{ round((($prepressData['departments']['marketing']['stats']['assigned'] ?? 0) / ($prepressData['departments']['marketing']['stats']['total'] ?? 1)) * 100, 1) }}%
                                                                                    @else
                                                                                        0%
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <span
                                                                                        class="badge badge-danger">Approved</span>
                                                                                </td>
                                                                                <td id="marketing-approved">{{ $prepressData['departments']['marketing']['stats']['approved'] ?? 0 }}
                                                                                </td>
                                                                                <td id="marketing-approved-pct">
                                                                                    @if (($prepressData['departments']['marketing']['stats']['total'] ?? 0) > 0)
                                                                                        {{ round((($prepressData['departments']['marketing']['stats']['approved'] ?? 0) / ($prepressData['departments']['marketing']['stats']['total'] ?? 1)) * 100, 1) }}%
                                                                                    @else
                                                                                        0%
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card border-left-info">
                                                            <div class="card-header bg-info text-white">
                                                                <h5 class="card-title mb-0 text-white">
                                                                    <i class="mdi mdi-account-multiple mr-2"></i>
                                                                    Other Departments
                                                                    <span
                                                                        class="badge badge-light ml-2" id="others-total">{{ $prepressData['departments']['others']['stats']['total'] ?? 0 }}</span>
                                                                </h5>
                                                            </div>
                                                            <div class="card-body p-0">
                                                                <div class="table-responsive">
                                                                    <table class="table table-hover mb-0">
                                                                        <thead class="thead-light">
                                                                            <tr>
                                                                                <th>Status</th>
                                                                                <th>Count</th>
                                                                                <th>Percentage</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="others-stats-body">
                                                                            <tr>
                                                                                <td>
                                                                                    <span
                                                                                        class="badge badge-success">Finish</span>
                                                                                </td>
                                                                                <td id="others-finish">{{ $prepressData['departments']['others']['stats']['finish'] ?? 0 }}
                                                                                </td>
                                                                                <td id="others-finish-pct">
                                                                                    @if (($prepressData['departments']['others']['stats']['total'] ?? 0) > 0)
                                                                                        {{ round((($prepressData['departments']['others']['stats']['finish'] ?? 0) / ($prepressData['departments']['others']['stats']['total'] ?? 1)) * 100, 1) }}%
                                                                                    @else
                                                                                        0%
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <span class="badge badge-warning">In
                                                                                        Progress</span>
                                                                                </td>
                                                                                <td id="others-in-progress">{{ $prepressData['departments']['others']['stats']['in_progress'] ?? 0 }}
                                                                                </td>
                                                                                <td id="others-in-progress-pct">
                                                                                    @if (($prepressData['departments']['others']['stats']['total'] ?? 0) > 0)
                                                                                        {{ round((($prepressData['departments']['others']['stats']['in_progress'] ?? 0) / ($prepressData['departments']['others']['stats']['total'] ?? 1)) * 100, 1) }}%
                                                                                    @else
                                                                                        0%
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <span
                                                                                        class="badge badge-secondary">Assigned</span>
                                                                                </td>
                                                                                <td id="others-assigned">{{ $prepressData['departments']['others']['stats']['assigned'] ?? 0 }}
                                                                                </td>
                                                                                <td id="others-assigned-pct">
                                                                                    @if (($prepressData['departments']['others']['stats']['total'] ?? 0) > 0)
                                                                                        {{ round((($prepressData['departments']['others']['stats']['assigned'] ?? 0) / ($prepressData['departments']['others']['stats']['total'] ?? 1)) * 100, 1) }}%
                                                                                    @else
                                                                                        0%
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <span
                                                                                        class="badge badge-danger">Approved</span>
                                                                                </td>
                                                                                <td id="others-approved">{{ $prepressData['departments']['others']['stats']['approved'] ?? 0 }}
                                                                                </td>
                                                                                <td id="others-approved-pct">
                                                                                    @if (($prepressData['departments']['others']['stats']['total'] ?? 0) > 0)
                                                                                        {{ round((($prepressData['departments']['others']['stats']['approved'] ?? 0) / ($prepressData['departments']['others']['stats']['total'] ?? 1)) * 100, 1) }}%
                                                                                    @else
                                                                                        0%
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Detailed Prepress Jobs by Department -->
                                                <div class="row">
                                                    <!-- Marketing Department Jobs -->
                                                    <div class="col-md-6">
                                                        <div class="card border-left-primary mb-3">
                                                            <div class="card-header bg-info text-white">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between">
                                                                    <h6 class="card-title mb-0 text-white">
                                                                        <i class="mdi mdi-account-group mr-2"></i>
                                                                        Marketing Department Jobs
                                                                    </h6>
                                                                </div>
                                                            </div>
                                                            <div class="card-body p-0">
                                                                <div class="plan-items"
                                                                    style="max-height: 400px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #6c757d #f8f9fa;">
                                                                    @if (isset($prepressData['departments']['marketing']['jobs']) &&
                                                                            count($prepressData['departments']['marketing']['jobs']) > 0)
                                                                        @foreach ($prepressData['departments']['marketing']['jobs'] as $job)
                                                                            @php
                                                                                $statusColor = 'bg-info';
                                                                                $statusText = 'Processing';
                                                                                $statusClass = 'in-progress';

                                                                                if ($job['status'] === 'completed') {
                                                                                    $statusColor = 'bg-success';
                                                                                    $statusText = 'Completed';
                                                                                    $statusClass = 'completed';
                                                                                } elseif ($job['status'] === 'in_progress') {
                                                                                    $statusColor = 'bg-warning';
                                                                                    $statusText = 'In Progress';
                                                                                    $statusClass = 'in-progress';
                                                                                } elseif ( $job['status'] === 'rejected') {
                                                                                    $statusColor = 'bg-danger';
                                                                                    $statusText = 'Rejected';
                                                                                    $statusClass = 'urgent';
                                                                                } elseif ($job['status'] === 'plan') {
                                                                                    $statusColor = 'bg-secondary';
                                                                                    $statusText = 'Plan';
                                                                                    $statusClass = 'plan';
                                                                                } elseif ($job['status'] === 'assigned') {
                                                                                    $statusColor = 'bg-secondary';
                                                                                    $statusText = 'Assign';
                                                                                    $statusClass = 'assign';
                                                                                } elseif ($job['status'] === 'open') {
                                                                                    $statusColor = 'bg-secondary';
                                                                                    $statusText = 'Open';
                                                                                    $statusClass = 'open';
                                                                                } elseif ($job['status'] === 'approved') {
                                                                                    $statusColor = 'bg-secondary';
                                                                                    $statusText = 'Approved';
                                                                                    $statusClass = 'approved';
                                                                                } elseif ($job['status'] === 'finish') {
                                                                                    $statusColor = 'bg-secondary';
                                                                                    $statusText = 'Finish';
                                                                                    $statusClass = 'finish';
                                                                                }

                                                                                // Priority indicator
                                                                                $priorityClass = 'text-secondary';
                                                                                $priorityIcon = '';
                                                                                if ($job['priority'] <= 3) {
                                                                                    $priorityClass = 'text-danger';
                                                                                    $priorityIcon = '🚨';
                                                                                } elseif ($job['priority'] <= 5) {
                                                                                    $priorityClass = 'text-warning';
                                                                                    $priorityIcon = '⚡';
                                                                                }
                                                                            @endphp

                                                                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-2 job-item {{ $statusClass }}"
                                                                                style="margin: 10px;">
                                                                                <div class="d-flex align-items-center">
                                                                                    <div class="{{ $statusColor }} rounded-circle mr-3"
                                                                                        style="width: 12px; height: 12px;">
                                                                                    </div>
                                                                                    <div style="min-width: 0; flex: 1;">
                                                                                        <h6
                                                                                            class="mb-0 font-size-14 text-truncate">
                                                                                            {{ $job['nomor_job_order'] }}
                                                                                        </h6>
                                                                                        <small
                                                                                            class="text-muted d-block">{{ $job['product'] }}</small>
                                                                                        <small
                                                                                            class="text-success d-block">Customer:
                                                                                            {{ $job['customer'] }}</small>
                                                                                        @if ($job['job'])
                                                                                            <small
                                                                                                class="text-info d-block">Job:
                                                                                                {{ $job['job'] }}</small>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                                <div class="text-right"
                                                                                    style="min-width: 0; flex-shrink: 0;">
                                                                                    <span
                                                                                        class="status-badge badge badge-{{ $statusColor === 'bg-success' ? 'success' : ($statusColor === 'bg-warning' ? 'warning' : ($statusColor === 'bg-danger' ? 'danger' : 'secondary')) }}">
                                                                                        {{ $statusText }}
                                                                                    </span>
                                                                                    <div class="mt-1">
                                                                                        @if ($job['assignee'])
                                                                                            <small
                                                                                                class="text-primary d-block">{{ $job['assignee'] }}</small>
                                                                                        @endif
                                                                                        @if ($job['est_waktu_job'])
                                                                                            <small
                                                                                                class="text-muted d-block">{{ $job['est_waktu_job'] }}
                                                                                                min</small>
                                                                                        @endif
                                                                                    </div>
                                                                                    <div class="mt-1">
                                                                                        <span
                                                                                            class="{{ $priorityClass }}">
                                                                                            {{ $priorityIcon }}
                                                                                            {{ $job['priority'] == 'Urgent' ? 'Urgent' : 'Normal' }}
                                                                                        </span>
                                                                                    </div>
                                                                                    @if ($job['tanggal_deadline'])
                                                                                        <small
                                                                                            class="text-warning d-block text-truncate"
                                                                                            style="max-width: 150px;">
                                                                                            Deadline:
                                                                                            {{ \Carbon\Carbon::parse($job['tanggal_deadline'])->format('d/m/Y H:i') }}
                                                                                        </small>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    @else
                                                                        <div class="text-center p-3">
                                                                            <small class="text-muted">
                                                                                <i class="mdi mdi-calendar-blank mr-1"></i>
                                                                                Tidak ada job IN PROGRESS dari Marketing
                                                                            </small>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Other Departments Jobs -->
                                                    <div class="col-md-6">
                                                        <div class="card border-left-info mb-3">
                                                            <div class="card-header bg-info text-white">
                                                                <div
                                                                    class="d-flex align-items-center justify-content-between">
                                                                    <h6 class="card-title mb-0 text-white">
                                                                        <i class="mdi mdi-account-multiple mr-2"></i>
                                                                        Other Departments Jobs
                                                                    </h6>
                                                                </div>
                                                            </div>
                                                            <div class="card-body p-0">
                                                                <div class="plan-items"
                                                                    style="max-height: 400px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #6c757d #f8f9fa;"
                                                                    id="others-jobs-list">
                                                                    @php
                                                                        $othersJobsInProgress = isset($prepressData['departments']['others']['jobs'])
                                                                            ? array_filter($prepressData['departments']['others']['jobs'], function($job) {
                                                                                return isset($job['status']) && $job['status'] === 'in_progress';
                                                                            })
                                                                            : [];
                                                                    @endphp
                                                                    @if (count($othersJobsInProgress) > 0)
                                                                        @foreach ($othersJobsInProgress as $job)
                                                                            @php
                                                                                $statusColor = 'bg-info';
                                                                                $statusText = 'Processing';
                                                                                $statusClass = 'in-progress';

                                                                                if ($job['status'] === 'completed') {
                                                                                    $statusColor = 'bg-success';
                                                                                    $statusText = 'Completed';
                                                                                    $statusClass = 'completed';
                                                                                } elseif (
                                                                                    $job['status'] === 'in_progress'
                                                                                ) {
                                                                                    $statusColor = 'bg-warning';
                                                                                    $statusText = 'In Progress';
                                                                                    $statusClass = 'in-progress';
                                                                                } elseif (
                                                                                    $job['status'] === 'rejected'
                                                                                ) {
                                                                                    $statusColor = 'bg-danger';
                                                                                    $statusText = 'Rejected';
                                                                                    $statusClass = 'urgent';
                                                                                } elseif ($job['status'] === 'plan') {
                                                                                    $statusColor = 'bg-secondary';
                                                                                    $statusText = 'Plan';
                                                                                    $statusClass = 'plan';
                                                                                } elseif ($job['status'] === 'assigned') {
                                                                                    $statusColor = 'bg-secondary';
                                                                                    $statusText = 'Assign';
                                                                                    $statusClass = 'assign';
                                                                                } elseif ($job['status'] === 'open') {
                                                                                    $statusColor = 'bg-secondary';
                                                                                    $statusText = 'Open';
                                                                                    $statusClass = 'open';
                                                                                }

                                                                                // Priority indicator
                                                                                $priorityClass = 'text-secondary';
                                                                                $priorityIcon = '';
                                                                                if ($job['priority'] <= 3) {
                                                                                    $priorityClass = 'text-danger';
                                                                                    $priorityIcon = '🚨';
                                                                                } elseif ($job['priority'] <= 5) {
                                                                                    $priorityClass = 'text-warning';
                                                                                    $priorityIcon = '⚡';
                                                                                }
                                                                            @endphp

                                                                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-2 job-item {{ $statusClass }}"
                                                                                style="margin: 10px;">
                                                                                <div class="d-flex align-items-center">
                                                                                    <div class="{{ $statusColor }} rounded-circle mr-3"
                                                                                        style="width: 12px; height: 12px;">
                                                                                    </div>
                                                                                    <div style="min-width: 0; flex: 1;">
                                                                                        <h6
                                                                                            class="mb-0 font-size-14 text-truncate">
                                                                                            {{ $job['nomor_job_order'] }}
                                                                                        </h6>
                                                                                        <small
                                                                                            class="text-muted d-block">{{ $job['product'] }}</small>
                                                                                        <small
                                                                                            class="text-success d-block">Customer:
                                                                                            {{ $job['customer'] }}</small>
                                                                                        @if ($job['job'])
                                                                                            <small
                                                                                                class="text-info d-block">Job:
                                                                                                {{ $job['job'] }}</small>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                                <div class="text-right"
                                                                                    style="min-width: 0; flex-shrink: 0;">
                                                                                    <span
                                                                                        class="status-badge badge badge-{{ $statusColor === 'bg-success' ? 'success' : ($statusColor === 'bg-warning' ? 'warning' : ($statusColor === 'bg-danger' ? 'danger' : 'secondary')) }}">
                                                                                        {{ $statusText }}
                                                                                    </span>
                                                                                    <div class="mt-1">
                                                                                        @if ($job['assignee'])
                                                                                            <small
                                                                                                class="text-primary d-block">{{ $job['assignee'] }}</small>
                                                                                        @endif
                                                                                        @if ($job['est_waktu_job'])
                                                                                            <small
                                                                                                class="text-muted d-block">{{ $job['est_waktu_job'] }}
                                                                                                min</small>
                                                                                        @endif
                                                                                    </div>
                                                                                    <div class="mt-1">
                                                                                        <span
                                                                                            class="{{ $priorityClass }}">
                                                                                            {{ $priorityIcon }}
                                                                                            {{ $job['priority'] }}
                                                                                        </span>
                                                                                    </div>
                                                                                    @if ($job['tanggal_deadline'])
                                                                                        <small
                                                                                            class="text-warning d-block text-truncate"
                                                                                            style="max-width: 150px;">
                                                                                            Deadline:
                                                                                            {{ \Carbon\Carbon::parse($job['tanggal_deadline'])->format('d/m/Y H:i') }}
                                                                                        </small>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    @else
                                                                        <div class="text-center p-3">
                                                                            <small class="text-muted">
                                                                                <i class="mdi mdi-calendar-blank mr-1"></i>
                                                                                Tidak ada job dari departemen lain
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
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Search Job Order -->
                            <div class="modal fade" id="modal-search-job-order" tabindex="-1" role="dialog" aria-labelledby="modalSearchJobOrderLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title" id="modalSearchJobOrderLabel" style="color: white;">
                                                <i class="mdi mdi-magnify mr-2"></i>
                                                Hasil Pencarian Job Order
                                            </h5>
                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="search-job-order-results">
                                                <div class="text-center p-4">
                                                    <i class="mdi mdi-magnify mdi-48px text-muted"></i>
                                                    <p class="text-muted mt-2">Masukkan nomor job order untuk mencari</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                        </div>
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
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- FullCalendar -->
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
        {{-- moment.js --}}
        <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>

        <script>
            $(document).ready(function() {
                // Add hover effects for job items
                $('.job-item').hover(
                    function() {
                        $(this).addClass('shadow-sm');
                    },
                    function() {
                        $(this).removeClass('shadow-sm');
                    }
                );

                // Auto-refresh status indicators (optional)
                setInterval(function() {
                    // You can add AJAX calls here to refresh job statuses
                    console.log('Status refresh check...');
                }, 30000); // Check every 30 seconds

                // Tab switching - Bootstrap native event handler
                // Listen to 'shown.bs.tab' event for lazy loading (after tab is shown)
                $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                    // Get the target tab ID from the activated tab (e.target)
                    const targetId = $(e.target).attr('data-bs-target');

                    // Track active tab for conditional polling
                    window.__dashboardActiveTab = targetId;
                    window.__devTabActive = (targetId === '#development');

                    // Trigger data loading for specific tabs
                    if (targetId === '#prepress') {
                        // Load prepress data if needed
                        if (typeof loadPrepressData === 'function' && !prepressDataCache) {
                            loadPrepressData();
                        }
                    }

                    if (targetId === '#development') {
                        if (typeof initializeDevelopmentDashboard === 'function') {
                            initializeDevelopmentDashboard();
                        }
                    }
                });

                // Initialize Charts
                initializeCharts();

                // Development dashboard is initialized lazily on tab open (#development)

                // Initialize Security Dashboard if user is security (divisi 11)
                @if(auth()->user()->divisi == 11)
                initializeSecurityDashboard();
                @endif

                // Initialize Supplier Dashboard if user is supplier
                @if(auth()->user()->divisi == 8)
                initializeSupplierDashboard();
                @endif
            });

            function initializeCharts() {


                // Chart 3: Mesin Paling Sibuk dari Plan yang Sudah Finish (Bar Chart)
                const busiestMachineCtx = document.getElementById('busiestMachineChart');
                if (busiestMachineCtx) {
                    // Real machine data from controller with fallback
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
                                        },
                                        format: {
                                            locale: 'id'
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Chart 4: Order Terbanyak (Bar Chart)
                const topOrdersCtx = document.getElementById('topOrdersChart');
                if (topOrdersCtx) {
                    // Real order data from controller with fallback
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
                                            return value + ' pesanan';
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

            // Development Dashboard Functions
            function initializeDevelopmentDashboard() {
                // Make init idempotent (avoid duplicate intervals if called multiple times)
                if (window.__devDashboardInitialized) {
                    return;
                }
                window.__devDashboardInitialized = true;

                loadDevelopmentData();
                loadRecentActivity();
                initializeDevTrendChart();

                // Auto-refresh every 30 seconds
                window.__devRefreshInterval = setInterval(function() {
                    if (!window.__devTabActive) return;
                    loadDevelopmentData();
                    loadRecentActivity();
                }, 30000);
            }

            function loadDevelopmentData() {
                // Skip if the tab isn't active (avoid background polling)
                if (window.__devTabActive === false) {
                    return;
                }

                // Abort previous request if still running to prevent overlap
                if (window.__devDataXhr && window.__devDataXhr.readyState !== 4) {
                    window.__devDataXhr.abort();
                }

                window.__devDataXhr = $.ajax({
                    url: '{{ route("development.rnd-workspace.data") }}',
                    type: 'GET',
                    data: {
                        dashboard_only: true
                    },
                    success: function(response) {
                        if (response.data) {
                            updateDevelopmentStats(response.data);
                            updateDevelopmentTable(response.jobs || []);
                        }
                    },
                    error: function(xhr) {
                        // Ignore abort errors (happens when switching tabs quickly)
                        if (xhr && xhr.statusText === 'abort') {
                            return;
                        }
                        console.error('Error loading development data:', xhr);
                        // Fallback dengan data sample untuk testing
                        const sampleData = {
                            total: 15,
                            draft: 3,
                            in_progress: 5,
                            completed: 7,
                            urgent: 2,
                            overdue: 1,
                            today: 4,
                            avg_lead_time: 3,
                            attention_jobs: [
                                {
                                    id: 1,
                                    job_code: 'DEV-001',
                                    customer: 'PT ABC',
                                    product: 'Produk A',
                                    status: 'MEETING_OPP',
                                    priority: 'Urgent',
                                    department: 'Marketing',
                                    action_required: 'Jadwalkan Meeting OPP',
                                    updated_at: '2024-01-15 10:30:00'
                                },
                                {
                                    id: 2,
                                    job_code: 'DEV-002',
                                    customer: 'PT XYZ',
                                    product: 'Produk B',
                                    status: 'READY_FOR_CUSTOMER',
                                    priority: 'High',
                                    department: 'PPIC',
                                    action_required: 'Scheduling Production',
                                    updated_at: '2024-01-15 09:15:00'
                                },
                                {
                                    id: 3,
                                    job_code: 'DEV-003',
                                    customer: 'PT DEF',
                                    product: 'Produk C',
                                    status: 'IN_PROGRESS_PREPRESS',
                                    priority: 'Normal',
                                    department: 'Prepress',
                                    action_required: 'Selesaikan Prepress',
                                    updated_at: '2024-01-15 08:45:00'
                                }
                            ]
                        };
                        updateDevelopmentStats(sampleData);
                    }
                });
            }

            function updateDevelopmentStats(data) {
                // Update main statistics
                $('#devTotalJobs').text(data.total || 0);
                $('#devDraftJobs').text(data.draft || 0);
                $('#devInProgressJobs').text(data.in_progress || 0);
                $('#devCompletedJobs').text(data.completed || 0);

                // Update performance metrics
                $('#completionRate').text(calculateCompletionRate(data) + '%');
                $('#avgLeadTime').text(calculateAvgLeadTime(data) + ' hari');
                $('#overdueJobs').text(data.overdue || 0);
                $('#todayJobs').text(data.today || 0);

                // Show urgent alert if needed
                if (data.urgent > 0) {
                    $('#urgentCount').text(data.urgent);
                    $('#urgentAlert').show();
                } else {
                    $('#urgentAlert').hide();
                }

                // Update attention jobs list
                updateAttentionJobsList(data.attention_jobs || []);
            }

            function calculateCompletionRate(data) {
                const total = data.total || 0;
                const completed = data.completed || 0;
                return total > 0 ? Math.round((completed / total) * 100) : 0;
            }

            function calculateAvgLeadTime(data) {
                return data.avg_lead_time || 0;
            }

            function updateAttentionJobsList(attentionJobs) {
                const container = $('#attentionJobsList');
                container.empty();

                if (attentionJobs.length === 0) {
                    container.html(`
                        <div class="text-center p-4">
                            <i class="mdi mdi-check-circle text-success" style="font-size: 3rem;"></i>
                            <h5 class="mt-2 text-muted">Tidak ada job yang memerlukan perhatian</h5>
                            <p class="text-muted">Semua job berjalan dengan normal</p>
                        </div>
                    `);
                    return;
                }

                attentionJobs.forEach(function(job) {
                    const priorityClass = getPriorityClass(job.priority);
                    const statusClass = getStatusClass(job.status);
                    const departmentClass = getDepartmentClass(job.department);
                    const timeAgo = moment(job.updated_at).fromNow();
                    const itemClass = job.priority === 'Urgent' ? 'urgent' : (job.priority === 'High' ? 'high-priority' : '');

                    container.append(`
                        <div class="d-flex align-items-center p-3 border-bottom job-attention-item ${itemClass}" style="cursor: pointer;" onclick="viewJobDetail(${job.id})">
                            <div class="mr-3">
                                <div class="rounded-circle ${priorityClass}" style="width: 12px; height: 12px;"></div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">
                                            <strong>${job.job_code}</strong>
                                            <span class="badge ${statusClass} ml-2">${job.status}</span>
                                        </h6>
                                        <p class="mb-1 text-muted">${job.customer} - ${job.product}</p>
                                        <p class="mb-0 small">
                                            <i class="mdi mdi-account mr-1"></i>
                                            <span class="badge ${departmentClass}">${job.department}</span>
                                            <span class="text-muted ml-2">harus segera diproses</span>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <small class="text-muted">${timeAgo}</small>
                                        <div class="mt-1">
                                            <span class="badge badge-warning">${job.action_required}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-3">
                                <i class="mdi mdi-chevron-right text-muted"></i>
                            </div>
                        </div>
                    `);
                });
            }

            function getPriorityClass(priority) {
                if (priority === 'Urgent') return 'bg-danger';
                if (priority === 'High') return 'bg-warning';
                return 'bg-info';
            }

            function getStatusClass(status) {
                const statusMap = {
                    'DRAFT': 'badge-warning',
                    'OPEN': 'badge-primary',
                    'IN_PROGRESS_PREPRESS': 'badge-info',
                    'FINISH_PREPRESS': 'badge-info',
                    'MEETING_OPP': 'badge-warning',
                    'READY_FOR_CUSTOMER': 'badge-info',
                    'SCHEDULED_FOR_PRODUCTION': 'badge-warning',
                    'PRODUCTION_COMPLETED': 'badge-success',
                    'PRODUCTION_APPROVED_BY_RND': 'badge-success',
                    'WAITING_MPP': 'badge-secondary',
                    'MPP_APPROVED': 'badge-success',
                    'SALES_ORDER_CREATED': 'badge-success',
                    'COMPLETED': 'badge-success'
                };
                return statusMap[status] || 'badge-secondary';
            }

            function getDepartmentClass(department) {
                const deptMap = {
                    'Marketing': 'badge-primary',
                    'RnD': 'badge-info',
                    'PPIC': 'badge-warning',
                    'Production': 'badge-success',
                    'Prepress': 'badge-secondary',
                    'Customer': 'badge-dark'
                };
                return deptMap[department] || 'badge-secondary';
            }

            function viewJobDetail(jobId) {
                window.location.href = '/sipo/development/rnd-workspace/' + jobId + '/view';
            }

            function updateDevelopmentTable(jobs) {
                const tbody = $('#devJobsTableBody');
                tbody.empty();

                if (jobs.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                <i class="mdi mdi-information-outline mr-1"></i>
                                Tidak ada data job development
                            </td>
                        </tr>
                    `);
                    return;
                }

                jobs.slice(0, 10).forEach(function(job) {
                    const statusBadge = getStatusBadge(job.status_job);
                    const priorityBadge = getPriorityBadge(job.prioritas_job);
                    const typeBadge = getTypeBadge(job.job_type);
                    const createdDate = moment(job.tanggal).format('DD/MM/YYYY');
                    const deadlineDate = job.job_deadline ? moment(job.job_deadline).format('DD/MM/YYYY') : '-';

                    tbody.append(`
                        <tr>
                            <td><strong>${job.job_code}</strong></td>
                            <td>${job.customer}</td>
                            <td>${job.product}</td>
                            <td>${typeBadge}</td>
                            <td>${priorityBadge}</td>
                            <td>${statusBadge}</td>
                            <td>${createdDate}</td>
                            <td>${deadlineDate}</td>
                            <td>
                                <a href="/sipo/development/rnd-workspace/${job.id}/view" class="btn btn-sm btn-info" title="View Detail">
                                    <i class="mdi mdi-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-warning" onclick="showDevProgressTimeline(${job.id})" title="Progress Timeline">
                                    <i class="mdi mdi-timeline"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });
            }

            function getStatusBadge(status) {
                const statusMap = {
                    'DRAFT': 'badge-warning',
                    'OPEN': 'badge-primary',
                    'IN_PROGRESS_PREPRESS': 'badge-info',
                    'FINISH_PREPRESS': 'badge-info',
                    'MEETING_OPP': 'badge-warning',
                    'READY_FOR_CUSTOMER': 'badge-info',
                    'SCHEDULED_FOR_PRODUCTION': 'badge-warning',
                    'PRODUCTION_COMPLETED': 'badge-success',
                    'PRODUCTION_APPROVED_BY_RND': 'badge-success',
                    'WAITING_MPP': 'badge-secondary',
                    'MPP_APPROVED': 'badge-success',
                    'SALES_ORDER_CREATED': 'badge-success',
                    'COMPLETED': 'badge-success'
                };

                const badgeClass = statusMap[status] || 'badge-secondary';
                return `<span class="badge ${badgeClass}">${status}</span>`;
            }

            function getPriorityBadge(priority) {
                if (priority === 'Urgent') {
                    return '<span class="badge badge-danger">Urgent</span>';
                }
                return '<span class="badge badge-info">Normal</span>';
            }

            function getTypeBadge(type) {
                if (type === 'new') {
                    return '<span class="badge badge-primary">Produk Baru</span>';
                } else if (type === 'repeat') {
                    return '<span class="badge badge-info">Produk Repeat</span>';
                }
                return '<span class="badge badge-secondary">' + type + '</span>';
            }

            function refreshDevData() {
                loadDevelopmentData();
                // Show success message
                if (typeof toastr !== 'undefined') {
                    toastr.success('Data development berhasil di-refresh', 'Refresh Berhasil');
                }
            }

            function exportDevData() {
                // Simple CSV export
                const table = document.getElementById('devJobsTable');
                const rows = Array.from(table.querySelectorAll('tr'));
                let csv = 'Job Code,Customer,Product,Type,Priority,Status,Created,Deadline\n';

                rows.slice(1).forEach(row => {
                    const cells = Array.from(row.querySelectorAll('td'));
                    if (cells.length > 1) {
                        const rowData = cells.slice(0, 8).map(cell => {
                            return '"' + cell.textContent.replace(/"/g, '""') + '"';
                        });
                        csv += rowData.join(',') + '\n';
                    }
                });

                const blob = new Blob([csv], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'development-jobs-' + moment().format('YYYY-MM-DD') + '.csv';
                a.click();
                window.URL.revokeObjectURL(url);

                if (typeof toastr !== 'undefined') {
                    toastr.success('Data development berhasil diexport', 'Export Berhasil');
                }
            }

            function showDevProgressTimeline(jobId) {
                // Create modal for timeline
                const modalHtml = `
                    <div class="modal fade" id="devTimelineModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Progress Timeline</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div id="devTimelineContent">
                                        <div class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            <p class="mt-2">Memuat timeline...</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Remove existing modal if any
                $('#devTimelineModal').remove();

                // Add modal to body
                $('body').append(modalHtml);

                // Show modal
                $('#devTimelineModal').modal('show');

                // Load timeline data
                loadDevTimelineData(jobId);
            }

            function loadDevTimelineData(jobId) {
                // Get job data from table
                const table = document.getElementById('devJobsTable');
                const rows = Array.from(table.querySelectorAll('tr'));
                let jobData = null;

                rows.forEach(row => {
                    const cells = Array.from(row.querySelectorAll('td'));
                    if (cells.length > 0 && cells[0].textContent.includes(jobId.toString())) {
                        jobData = {
                            id: jobId,
                            job_code: cells[0].textContent.trim(),
                            customer: cells[1].textContent.trim(),
                            product: cells[2].textContent.trim(),
                            job_type: cells[3].textContent.includes('Baru') ? 'new' : 'repeat',
                            prioritas_job: cells[4].textContent.includes('Urgent') ? 'Urgent' : 'Normal',
                            status_job: cells[5].textContent.trim(),
                            tanggal: cells[6].textContent.trim(),
                            job_deadline: cells[7].textContent.trim() !== '-' ? cells[7].textContent.trim() : null
                        };
                    }
                });

                if (jobData) {
                    const timelineHtml = generateDevTimelineHtml(jobData);
                    $('#devTimelineContent').html(timelineHtml);
                } else {
                    $('#devTimelineContent').html(`
                        <div class="alert alert-warning text-center">
                            <i class="mdi mdi-alert-circle"></i>
                            <p class="mb-0">Data job tidak ditemukan.</p>
                        </div>
                    `);
                }
            }

            function generateDevTimelineHtml(jobData) {
                const status = jobData.status_job;
                const createdAt = moment(jobData.tanggal, 'DD/MM/YYYY').format('DD/MM/YYYY HH:mm');
                const deadline = jobData.job_deadline ? moment(jobData.job_deadline, 'DD/MM/YYYY').format('DD/MM/YYYY') : '-';

                // Calculate progress percentage
                let progressPercentage = 0;
                switch (status) {
                    case 'DRAFT': progressPercentage = 8; break;
                    case 'OPEN':
                    case 'IN_PROGRESS_PREPRESS': progressPercentage = 17; break;
                    case 'FINISH_PREPRESS': progressPercentage = 25; break;
                    case 'MEETING_OPP': progressPercentage = 33; break;
                    case 'READY_FOR_CUSTOMER': progressPercentage = 50; break;
                    case 'SCHEDULED_FOR_PRODUCTION': progressPercentage = 58; break;
                    case 'PRODUCTION_COMPLETED': progressPercentage = 67; break;
                    case 'PRODUCTION_APPROVED_BY_RND': progressPercentage = 75; break;
                    case 'WAITING_MPP': progressPercentage = 83; break;
                    case 'MPP_APPROVED': progressPercentage = 92; break;
                    case 'SALES_ORDER_CREATED': progressPercentage = 95; break;
                    case 'COMPLETED': progressPercentage = 100; break;
                    default: progressPercentage = 0;
                }

                return `
                    <div class="info-card">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6><i class="mdi mdi-file-document"></i> Job Code: ${jobData.job_code}</h6>
                                <h6><i class="mdi mdi-account"></i> Customer: ${jobData.customer}</h6>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="mdi mdi-calendar"></i> Created: ${createdAt}</h6>
                                <h6><i class="mdi mdi-clock"></i> Deadline: ${deadline}</h6>
                            </div>
                        </div>

                        <div class="horizontal-timeline">
                            <div class="timeline-item ${['DRAFT', 'OPEN', 'IN_PROGRESS_PREPRESS', 'FINISH_PREPRESS', 'MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                <div class="timeline-dot ${['DRAFT', 'OPEN', 'IN_PROGRESS_PREPRESS', 'FINISH_PREPRESS', 'MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-primary' : 'bg-secondary'}">
                                    <i class="mdi mdi-plus"></i>
                                </div>
                                <div class="timeline-label">Draft</div>
                            </div>
                            <div class="timeline-item ${['IN_PROGRESS_PREPRESS', 'FINISH_PREPRESS', 'MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                <div class="timeline-dot ${['IN_PROGRESS_PREPRESS', 'FINISH_PREPRESS', 'MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-info' : 'bg-secondary'}">
                                    <i class="mdi mdi-printer"></i>
                                </div>
                                <div class="timeline-label">Prepress</div>
                            </div>
                            <div class="timeline-item ${['FINISH_PREPRESS', 'MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                <div class="timeline-dot ${['FINISH_PREPRESS', 'MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-warning' : 'bg-secondary'}">
                                    <i class="mdi mdi-calendar-clock"></i>
                                </div>
                                <div class="timeline-label">Finish Prepress</div>
                            </div>
                            <div class="timeline-item ${['MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                <div class="timeline-dot ${['MEETING_OPP', 'READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-success' : 'bg-secondary'}">
                                    <i class="mdi mdi-file-document"></i>
                                </div>
                                <div class="timeline-label">Meeting OPP</div>
                            </div>
                            <div class="timeline-item ${['READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                <div class="timeline-dot ${['READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-primary' : 'bg-secondary'}">
                                    <i class="mdi mdi-check-circle"></i>
                                </div>
                                <div class="timeline-label">Meeting OK</div>
                            </div>
                            <div class="timeline-item ${['READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                <div class="timeline-dot ${['READY_FOR_CUSTOMER', 'SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-info' : 'bg-secondary'}">
                                    <i class="mdi mdi-calendar-plus"></i>
                                </div>
                                <div class="timeline-label">ACC Customer</div>
                            </div>
                            <div class="timeline-item ${['SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                <div class="timeline-dot ${['SCHEDULED_FOR_PRODUCTION', 'PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-warning' : 'bg-secondary'}">
                                    <i class="mdi mdi-calendar-plus"></i>
                                </div>
                                <div class="timeline-label">PPIC</div>
                            </div>
                            <div class="timeline-item ${['PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                <div class="timeline-dot ${['PRODUCTION_COMPLETED', 'PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-success' : 'bg-secondary'}">
                                    <i class="mdi mdi-factory"></i>
                                </div>
                                <div class="timeline-label">Produksi</div>
                            </div>
                            <div class="timeline-item ${['PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                <div class="timeline-dot ${['PRODUCTION_APPROVED_BY_RND', 'WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-primary' : 'bg-secondary'}">
                                    <i class="mdi mdi-file-document"></i>
                                </div>
                                <div class="timeline-label">Map Proof</div>
                            </div>
                            <div class="timeline-item ${['WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                <div class="timeline-dot ${['WAITING_MPP', 'MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-info' : 'bg-secondary'}">
                                    <i class="mdi mdi-clock"></i>
                                </div>
                                <div class="timeline-label">Waiting MPP</div>
                            </div>
                            <div class="timeline-item ${['MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'completed' : ''}">
                                <div class="timeline-dot ${['MPP_APPROVED', 'SALES_ORDER_CREATED', 'COMPLETED'].includes(status) ? 'bg-warning' : 'bg-secondary'}">
                                    <i class="mdi mdi-cart-plus"></i>
                                </div>
                                <div class="timeline-label">Sales Order</div>
                            </div>
                            <div class="timeline-item ${status === 'COMPLETED' ? 'completed' : ''}">
                                <div class="timeline-dot ${status === 'COMPLETED' ? 'bg-success' : 'bg-secondary'}">
                                    <i class="mdi mdi-flag-checkered"></i>
                                </div>
                                <div class="timeline-label">Completed</div>
                            </div>
                        </div>

                        <div class="progress-summary mt-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: ${progressPercentage}%">
                                            ${progressPercentage}%
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 text-right">
                                    <span class="badge badge-${status === 'COMPLETED' ? 'success' : status === 'DRAFT' ? 'warning' : 'info'} mr-2">
                                        ${status}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            // New Development Dashboard Functions
            function loadRecentActivity() {
                // Simulate recent activity data
                const activities = [
                    {
                        type: 'job_created',
                        description: 'Job baru dibuat: DEV-001',
                        time: '5 menit yang lalu',
                        icon: 'mdi-plus-circle',
                        color: 'success'
                    },
                    {
                        type: 'status_changed',
                        description: 'Status job DEV-002 berubah ke In Progress',
                        time: '10 menit yang lalu',
                        icon: 'mdi-sync',
                        color: 'info'
                    },
                    {
                        type: 'job_completed',
                        description: 'Job DEV-003 selesai',
                        time: '15 menit yang lalu',
                        icon: 'mdi-check-circle',
                        color: 'success'
                    },
                    {
                        type: 'deadline_approaching',
                        description: 'Job DEV-004 deadline dalam 2 jam',
                        time: '20 menit yang lalu',
                        icon: 'mdi-clock-alert',
                        color: 'warning'
                    }
                ];

                updateRecentActivity(activities);
            }

            function updateRecentActivity(activities) {
                const container = $('#recentActivity');
                container.empty();

                if (activities.length === 0) {
                    container.html('<p class="text-muted text-center">Tidak ada aktivitas terbaru</p>');
                    return;
                }

                activities.forEach(function(activity) {
                    container.append(`
                        <div class="d-flex align-items-center mb-2">
                            <div class="mr-3">
                                <i class="mdi ${activity.icon} text-${activity.color}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <small class="text-muted">${activity.time}</small>
                                <p class="mb-0 small">${activity.description}</p>
                            </div>
                        </div>
                    `);
                });
            }

            function initializeDevTrendChart() {
                const ctx = document.getElementById('devTrendChart').getContext('2d');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                        datasets: [{
                            label: 'Jobs Created',
                            data: [12, 19, 3, 5, 2, 3, 8],
                            borderColor: '#007bff',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            tension: 0.4,
                            fill: true
                        }, {
                            label: 'Jobs Completed',
                            data: [8, 15, 5, 7, 4, 2, 6],
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Filter functions
            function filterUrgentJobs() {
                $('#devJobsTable tbody tr').each(function() {
                    const priority = $(this).find('td:eq(4)').text();
                    if (priority.includes('Urgent')) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            function filterTodayJobs() {
                const today = moment().format('DD/MM/YYYY');
                $('#devJobsTable tbody tr').each(function() {
                    const created = $(this).find('td:eq(6)').text();
                    if (created === today) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            function filterByStatus(status) {
                $('#devJobsTable tbody tr').each(function() {
                    const jobStatus = $(this).find('td:eq(5)').text();
                    if (jobStatus.includes(status)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            function filterByDepartment(department) {
                // Redirect to full workspace with filter
                window.location.href = '/sipo/development/rnd-workspace?filter=' + department;
            }

            function showAllJobs() {
                $('#devJobsTable tbody tr').show();
            }

            function refreshDevData() {
                loadDevelopmentData();
                loadRecentActivity();
                if (typeof toastr !== 'undefined') {
                    toastr.success('Data development berhasil di-refresh', 'Refresh Berhasil');
                }
            }

            function exportDevData() {
                const table = document.getElementById('devJobsTable');
                const rows = Array.from(table.querySelectorAll('tr'));
                let csv = 'Job Code,Customer,Product,Type,Priority,Status,Created,Deadline\n';

                rows.slice(1).forEach(row => {
                    const cells = Array.from(row.querySelectorAll('td'));
                    if (cells.length > 1) {
                        const rowData = cells.slice(0, 8).map(cell => {
                            return '"' + cell.textContent.replace(/"/g, '""') + '"';
                        });
                        csv += rowData.join(',') + '\n';
                    }
                });

                const blob = new Blob([csv], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'development-jobs-' + moment().format('YYYY-MM-DD') + '.csv';
                a.click();
                window.URL.revokeObjectURL(url);

                if (typeof toastr !== 'undefined') {
                    toastr.success('Data development berhasil diexport', 'Export Berhasil');
                }
            }

            // Supplier Dashboard Functions
            function initializeSupplierDashboard() {
                // Auto-open supplier tab for supplier users
                $('.nav-link').removeClass('active');
                $('.tab-pane').removeClass('show active');

                $('#supplier-tab').addClass('active');
                $('#supplier').addClass('show active');

                loadSupplierData();
                loadSupplierTickets();
            }

            function loadSupplierData() {
                $.ajax({
                    url: '{{ route("supplier-tickets.dashboard-data") }}',
                    type: 'GET',
                    success: function(response) {
                        if (response.data) {
                            updateSupplierStats(response.data);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading supplier data:', xhr);
                        // Fallback dengan data sample untuk testing
                        const sampleData = {
                            total: 12,
                            pending: 3,
                            in_progress: 4,
                            completed: 5,
                            overdue: 1,
                            today: 2,
                            response_rate: 85,
                            avg_resolution: 2.5
                        };
                        updateSupplierStats(sampleData);
                    }
                });
            }

            function updateSupplierStats(data) {
                // Update main statistics
                $('#supplierTotalTickets').text(data.total || 0);
                $('#supplierPendingTickets').text(data.pending || 0);
                $('#supplierInProgressTickets').text(data.in_progress || 0);
                $('#supplierCompletedTickets').text(data.completed || 0);

                // Update performance metrics
                $('#responseRate').text((data.response_rate || 0) + '%');
                $('#avgResolution').text((data.avg_resolution || 0) + ' hari');
                $('#overdueTickets').text(data.overdue || 0);
                $('#todayTickets').text(data.today || 0);
            }

            function loadSupplierTickets() {
                $.ajax({
                    url: '{{ route("supplier-tickets.dashboard-tickets") }}',
                    type: 'GET',
                    success: function(response) {
                        if (response.tickets) {
                            updateSupplierTicketsTable(response.tickets);
                            updateSupplierTicketsList(response.tickets);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading supplier tickets:', xhr);
                        // Fallback dengan data sample untuk testing
                        const sampleTickets = [
                            {
                                id: 1,
                                ticket_number: 'TKT-001',
                                subject: 'Permintaan Quotation Produk A',
                                priority: 'High',
                                status: 'PENDING',
                                created_at: '2024-01-15 10:30:00',
                                updated_at: '2024-01-15 10:30:00'
                            },
                            {
                                id: 2,
                                ticket_number: 'TKT-002',
                                subject: 'Follow up Order #12345',
                                priority: 'Normal',
                                status: 'IN_PROGRESS',
                                created_at: '2024-01-15 09:15:00',
                                updated_at: '2024-01-15 11:45:00'
                            },
                            {
                                id: 3,
                                ticket_number: 'TKT-003',
                                subject: 'Delivery Schedule Update',
                                priority: 'Urgent',
                                status: 'COMPLETED',
                                created_at: '2024-01-14 14:20:00',
                                updated_at: '2024-01-15 16:30:00'
                            }
                        ];
                        updateSupplierTicketsTable(sampleTickets);
                        updateSupplierTicketsList(sampleTickets);
                    }
                });
            }

            function updateSupplierTicketsTable(tickets) {
                const tbody = $('#supplierTicketsTableBody');
                tbody.empty();

                if (tickets.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                <i class="mdi mdi-information-outline mr-1"></i>
                                Tidak ada data tickets
                            </td>
                        </tr>
                    `);
                    return;
                }

                tickets.slice(0, 10).forEach(function(ticket) {
                    const priorityBadge = getSupplierPriorityBadge(ticket.priority);
                    const statusBadge = getSupplierStatusBadge(ticket.status);
                    const createdDate = moment(ticket.created_at).format('DD/MM/YYYY HH:mm');
                    const updatedDate = moment(ticket.updated_at).format('DD/MM/YYYY HH:mm');

                    tbody.append(`
                        <tr>
                            <td><strong>${ticket.ticket_number}</strong></td>
                            <td>${ticket.subject}</td>
                            <td>${priorityBadge}</td>
                            <td>${statusBadge}</td>
                            <td>${createdDate}</td>
                            <td>${updatedDate}</td>
                            <td>
                                <a href="/sipo/supplier-tickets/${ticket.id}" class="btn btn-sm btn-info" title="View Detail">
                                    <i class="mdi mdi-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-warning" onclick="showTicketTimeline(${ticket.id})" title="Timeline">
                                    <i class="mdi mdi-timeline"></i>
                                </button>
                            </td>
                        </tr>
                    `);
                });
            }

            // Supplier Timeline Functions
            function loadSupplierTimeline() {
                $.ajax({
                    url: 'api/supplier-timeline',
                    method: 'GET',
                    success: function(response) {
                        updateSupplierTimeline(response.data);
                        updateSupplierStats(response.stats);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading supplier timeline:', error);
                        $('#supplierTimelineContainer').html(`
                            <div class="text-center p-4">
                                <i class="mdi mdi-alert-circle text-danger" style="font-size: 3rem;"></i>
                                <h5 class="mt-2 text-muted">Error Loading Timeline</h5>
                                <p class="text-muted">Gagal memuat data timeline</p>
                            </div>
                        `);
                    }
                });
            }

            function updateSupplierTimeline(deliveries) {
                const container = $('#supplierTimelineContainer');
                container.empty();

                if (deliveries.length === 0) {
                    container.html(`
                        <div class="text-center p-4">
                            <i class="mdi mdi-truck-delivery text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-2 text-muted">Belum Ada Kedatangan</h5>
                            <p class="text-muted">Belum ada delivery yang completed</p>
                        </div>
                    `);
                    return;
                }

                // Group deliveries by date
                const groupedDeliveries = {};
                deliveries.forEach(function(delivery) {
                    const dateKey = moment(delivery.delivery_date).format('YYYY-MM-DD');
                    if (!groupedDeliveries[dateKey]) {
                        groupedDeliveries[dateKey] = [];
                    }
                    groupedDeliveries[dateKey].push(delivery);
                });

                // Sort dates descending
                const sortedDates = Object.keys(groupedDeliveries).sort().reverse();

                // Create horizontal timeline container
                const horizontalTimeline = $('<div class="horizontal-timeline-container"></div>');

                sortedDates.forEach(function(dateKey) {
                    const dayDeliveries = groupedDeliveries[dateKey];
                    const displayDate = moment(dateKey).format('DD MMM YYYY');

                    // Date section with trucks
                    const dateSection = $(`
                        <div class="timeline-date-section">
                            <div class="timeline-date-header-horizontal">
                                <div class="timeline-date-label-horizontal">
                                    <i class="mdi mdi-calendar mr-1"></i>
                                    ${displayDate}
                                    <span class="badge badge-primary ml-1">${dayDeliveries.length}</span>
                                </div>
                            </div>
                            <div class="timeline-trucks-container-horizontal">
                            </div>
                        </div>
                    `);

                    // Add trucks to this date section
                    const trucksContainer = dateSection.find('.timeline-trucks-container-horizontal');

                    dayDeliveries.forEach(function(delivery, index) {
                        const deliveryTime = moment(delivery.delivery_date).format('HH:mm');
                        const timeAgo = moment(delivery.delivery_date).fromNow();

                        trucksContainer.append(`
                            <div class="timeline-truck-item-horizontal" onclick="showDeliveryDetail(${delivery.id})">
                                <div class="truck-icon-container-horizontal">
                                    <i class="mdi mdi-truck truck-icon-small"></i>
                                    <div class="truck-number">${index + 1}</div>
                                </div>
                                <div class="truck-info-horizontal">
                                    <div class="truck-supplier">${delivery.supplier_name}</div>
                                    <div class="truck-time">${deliveryTime}</div>
                                    <div class="truck-po">${delivery.po_number}</div>
                                </div>
                                <div class="truck-status">
                                    <i class="mdi mdi-check-circle text-success"></i>
                                </div>
                            </div>
                        `);
                    });

                    horizontalTimeline.append(dateSection);
                });

                container.append(horizontalTimeline);
            }

            function showDeliveryDetail(deliveryId) {
                // Show delivery detail modal
                $('#deliveryDetailModal').data('delivery-id', deliveryId);
                $('#deliveryDetailModal').modal('show');
                loadDeliveryDetail(deliveryId);
            }

            function loadDeliveryDetail(deliveryId) {
                // Load delivery details via AJAX
                $.ajax({
                    url: 'api/supplier-timeline',
                    method: 'GET',
                    success: function(response) {
                        // Find the specific delivery
                        const delivery = response.data.find(d => d.id == deliveryId);
                        if (delivery) {
                            const deliveryDate = moment(delivery.delivery_date).format('DD MMMM YYYY');
                            const deliveryTime = moment(delivery.delivery_date).format('HH:mm');
                            const timeAgo = moment(delivery.delivery_date).fromNow();

                            $('#deliveryDetailContent').html(`
                                <div class="delivery-detail-card">
                                    <div class="delivery-header">
                                        <div class="delivery-icon">
                                            <i class="mdi mdi-truck-delivery"></i>
                                        </div>
                                        <div class="delivery-info">
                                            <h5 class="delivery-title">Detail Delivery</h5>
                                            <p class="delivery-subtitle">Informasi lengkap kedatangan barang</p>
                                        </div>
                                    </div>

                                    <div class="delivery-content">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="detail-item">
                                                    <label><i class="mdi mdi-account mr-2"></i>Supplier:</label>
                                                    <span>${delivery.supplier_name}</span>
                                                </div>
                                                <div class="detail-item">
                                                    <label><i class="mdi mdi-calendar mr-2"></i>Tanggal:</label>
                                                    <span>${deliveryDate}</span>
                                                </div>
                                                <div class="detail-item">
                                                    <label><i class="mdi mdi-clock mr-2"></i>Waktu:</label>
                                                    <span>${deliveryTime} (${timeAgo})</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="detail-item">
                                                    <label><i class="mdi mdi-ticket-confirmation mr-2"></i>PO Number:</label>
                                                    <span class="po-number">${delivery.po_number}</span>
                                                </div>
                                                <div class="detail-item">
                                                    <label><i class="mdi mdi-check-circle mr-2"></i>Status:</label>
                                                    <span class="badge badge-success">Completed</span>
                                                </div>
                                                <div class="detail-item">
                                                    <label><i class="mdi mdi-truck mr-2"></i>Delivery ID:</label>
                                                    <span class="delivery-id">#${delivery.id}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="delivery-actions mt-3">
                                            <div class="alert alert-info">
                                                <i class="mdi mdi-information mr-2"></i>
                                                <strong>Info:</strong> Delivery ini telah selesai dan barang telah diterima dengan baik.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `);
                        } else {
                            $('#deliveryDetailContent').html(`
                                <div class="alert alert-warning">
                                    <i class="mdi mdi-alert-circle mr-2"></i>
                                    Detail delivery tidak ditemukan
                                </div>
                            `);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading delivery detail:', error);
                        $('#deliveryDetailContent').html(`
                            <div class="alert alert-danger">
                                <i class="mdi mdi-alert-circle mr-2"></i>
                                Gagal memuat detail delivery
                            </div>
                        `);
                    }
                });
            }

            function openFullDetail() {
                // Get the current delivery ID from the modal
                const deliveryId = $('#deliveryDetailModal').data('delivery-id');
                if (deliveryId) {
                    window.open('admin/supplier-tickets/' + deliveryId, '_blank');
                }
            }

            function updateSupplierStats(stats) {
                $('#totalCompletedDeliveries').text(stats.total_completed || 0);
                $('#todayDeliveries').text(stats.today_deliveries || 0);
                $('#thisWeekDeliveries').text(stats.this_week_deliveries || 0);
                $('#uniqueSuppliers').text(stats.unique_suppliers || 0);
            }

            function refreshSupplierTimeline() {
                $('#supplierTimelineContainer').html(`
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat timeline kedatangan...</p>
                    </div>
                `);
                loadSupplierTimeline();
            }

            function updateSupplierTicketsList(tickets) {
                const container = $('#supplierTicketsList');
                container.empty();

                if (tickets.length === 0) {
                    container.html(`
                        <div class="text-center p-4">
                            <i class="mdi mdi-check-circle text-success" style="font-size: 3rem;"></i>
                            <h5 class="mt-2 text-muted">Tidak ada tickets</h5>
                            <p class="text-muted">Semua tickets telah selesai</p>
                        </div>
                    `);
                    return;
                }

                tickets.slice(0, 5).forEach(function(ticket) {
                    const priorityClass = getSupplierPriorityClass(ticket.priority);
                    const statusClass = getSupplierStatusClass(ticket.status);
                    const timeAgo = moment(ticket.updated_at).fromNow();

                    container.append(`
                        <div class="d-flex align-items-center p-3 border-bottom" style="cursor: pointer;" onclick="viewTicketDetail(${ticket.id})">
                            <div class="mr-3">
                                <div class="rounded-circle ${priorityClass}" style="width: 12px; height: 12px;"></div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">
                                            <strong>${ticket.ticket_number}</strong>
                                            <span class="badge ${statusClass} ml-2">${ticket.status}</span>
                                        </h6>
                                        <p class="mb-0 text-muted">${ticket.subject}</p>
                                    </div>
                                    <div class="text-right">
                                        <small class="text-muted">${timeAgo}</small>
                                        <div class="mt-1">
                                            <span class="badge ${priorityClass}">${ticket.priority}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-3">
                                <i class="mdi mdi-chevron-right text-muted"></i>
                            </div>
                        </div>
                    `);
                });
            }

            function getSupplierPriorityBadge(priority) {
                if (priority === 'Urgent') {
                    return '<span class="badge badge-danger">Urgent</span>';
                } else if (priority === 'High') {
                    return '<span class="badge badge-warning">High</span>';
                }
                return '<span class="badge badge-info">Normal</span>';
            }

            function getSupplierStatusBadge(status) {
                const statusMap = {
                    'PENDING': 'badge-warning',
                    'IN_PROGRESS': 'badge-info',
                    'COMPLETED': 'badge-success',
                    'CANCELLED': 'badge-secondary'
                };
                const badgeClass = statusMap[status] || 'badge-secondary';
                return `<span class="badge ${badgeClass}">${status}</span>`;
            }

            function getSupplierPriorityClass(priority) {
                if (priority === 'Urgent') return 'bg-danger';
                if (priority === 'High') return 'bg-warning';
                return 'bg-info';
            }

            function getSupplierStatusClass(status) {
                const statusMap = {
                    'PENDING': 'badge-warning',
                    'IN_PROGRESS': 'badge-info',
                    'COMPLETED': 'badge-success',
                    'CANCELLED': 'badge-secondary'
                };
                return statusMap[status] || 'badge-secondary';
            }

            function viewTicketDetail(ticketId) {
                window.location.href = '/sipo/supplier-tickets/' + ticketId;
            }

            function showTicketTimeline(ticketId) {
                // Create modal for timeline
                const modalHtml = `
                    <div class="modal fade" id="ticketTimelineModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Ticket Timeline</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div id="ticketTimelineContent">
                                        <div class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                            <p class="mt-2">Memuat timeline...</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Remove existing modal if any
                $('#ticketTimelineModal').remove();

                // Add modal to body
                $('body').append(modalHtml);

                // Show modal
                $('#ticketTimelineModal').modal('show');

                // Load timeline data
                loadTicketTimelineData(ticketId);
            }

            function loadTicketTimelineData(ticketId) {
                // Simulate timeline data
                const timelineData = {
                    id: ticketId,
                    ticket_number: 'TKT-' + ticketId.toString().padStart(3, '0'),
                    subject: 'Sample Ticket Subject',
                    status: 'IN_PROGRESS',
                    created_at: '2024-01-15 10:30:00',
                    timeline: [
                        {
                            status: 'PENDING',
                            description: 'Ticket created',
                            timestamp: '2024-01-15 10:30:00',
                            user: 'System'
                        },
                        {
                            status: 'IN_PROGRESS',
                            description: 'Ticket assigned to supplier',
                            timestamp: '2024-01-15 11:00:00',
                            user: 'Admin'
                        }
                    ]
                };

                const timelineHtml = generateTicketTimelineHtml(timelineData);
                $('#ticketTimelineContent').html(timelineHtml);
            }

            function generateTicketTimelineHtml(data) {
                return `
                    <div class="info-card">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6><i class="mdi mdi-ticket-confirmation"></i> Ticket: ${data.ticket_number}</h6>
                                <h6><i class="mdi mdi-file-document"></i> Subject: ${data.subject}</h6>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="mdi mdi-calendar"></i> Created: ${moment(data.created_at).format('DD/MM/YYYY HH:mm')}</h6>
                                <h6><i class="mdi mdi-flag"></i> Status: <span class="badge badge-info">${data.status}</span></h6>
                            </div>
                        </div>

                        <div class="timeline">
                            ${data.timeline.map(item => `
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">${item.description}</h6>
                                        <p class="timeline-text">
                                            <small class="text-muted">
                                                <i class="mdi mdi-clock mr-1"></i>
                                                ${moment(item.timestamp).format('DD/MM/YYYY HH:mm')} - ${item.user}
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            // Filter functions for supplier
            function filterPendingTickets() {
                $('#supplierTicketsTable tbody tr').each(function() {
                    const status = $(this).find('td:eq(3)').text();
                    if (status.includes('PENDING')) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            function filterUrgentTickets() {
                $('#supplierTicketsTable tbody tr').each(function() {
                    const priority = $(this).find('td:eq(2)').text();
                    if (priority.includes('Urgent')) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            function filterByStatus(status) {
                $('#supplierTicketsTable tbody tr').each(function() {
                    const ticketStatus = $(this).find('td:eq(3)').text();
                    if (ticketStatus.includes(status)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }

            function showAllTickets() {
                $('#supplierTicketsTable tbody tr').show();
            }

            function refreshSupplierData() {
                loadSupplierData();
                loadSupplierTickets();
                if (typeof toastr !== 'undefined') {
                    toastr.success('Data supplier berhasil di-refresh', 'Refresh Berhasil');
                }
            }

            function exportSupplierData() {
                const table = document.getElementById('supplierTicketsTable');
                const rows = Array.from(table.querySelectorAll('tr'));
                let csv = 'Ticket ID,Subject,Priority,Status,Created,Updated\n';

                rows.slice(1).forEach(row => {
                    const cells = Array.from(row.querySelectorAll('td'));
                    if (cells.length > 1) {
                        const rowData = cells.slice(0, 6).map(cell => {
                            return '"' + cell.textContent.replace(/"/g, '""') + '"';
                        });
                        csv += rowData.join(',') + '\n';
                    }
                });

                const blob = new Blob([csv], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'supplier-tickets-' + moment().format('YYYY-MM-DD') + '.csv';
                a.click();
                window.URL.revokeObjectURL(url);

                if (typeof toastr !== 'undefined') {
                    toastr.success('Data supplier berhasil diexport', 'Export Berhasil');
                }
            }

            // Security Dashboard Functions
            function initializeSecurityDashboard() {
                // Auto-open security tab for security users
                $('.nav-link').removeClass('active');
                $('.tab-pane').removeClass('show active');

                $('#security-tab').addClass('active');
                $('#security').addClass('show active');

                loadSecurityData();
                loadSecurityVehicleChecklists();
                loadSecurityGoodsMovements();
            }

            function loadSecurityData() {
                $.ajax({
                    url: '{{ route("security.dashboard-data") }}',
                    type: 'GET',
                    success: function(response) {
                        if (response.data) {
                            updateSecurityStats(response.data);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading security data:', xhr);
                        // Fallback dengan data sample untuk testing
                        const sampleData = {
                            total_checklists: 25,
                            completed_checklists: 18,
                            pending_checklists: 7,
                            today_checklists: 5,
                            total_goods_movements: 12,
                            completed_goods_movements: 10,
                            pending_goods_movements: 2,
                            today_goods_movements: 3
                        };
                        updateSecurityStats(sampleData);
                    }
                });
            }

            function updateSecurityStats(data) {
                // Update main statistics
                $('#securityTotalChecklists').text(data.total_checklists || 0);
                $('#securityCompletedChecklists').text(data.completed_checklists || 0);
                $('#securityPendingChecklists').text(data.pending_checklists || 0);
                $('#securityTodayChecklists').text(data.today_checklists || 0);
            }

            function loadSecurityVehicleChecklists() {
                $.ajax({
                    url: '{{ route("security.vehicle-checklist.dashboard-data") }}',
                    type: 'GET',
                    success: function(response) {
                        if (response.checklists) {
                            updateSecurityVehicleTable(response.checklists);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading security vehicle checklists:', xhr);
                        // Fallback dengan data sample untuk testing
                        const sampleChecklists = [
                            {
                                id: 1,
                                no_urut: 'VC-001',
                                nama_driver: 'John Doe',
                                model_kendaraan: 'Toyota Avanza',
                                status: 'keluar',
                                tanggal: '2024-01-15',
                                created_at: '2024-01-15 10:30:00'
                            },
                            {
                                id: 2,
                                no_urut: 'VC-002',
                                nama_driver: 'Jane Smith',
                                model_kendaraan: 'Honda Jazz',
                                status: 'masuk',
                                tanggal: '2024-01-15',
                                created_at: '2024-01-15 14:20:00'
                            }
                        ];
                        updateSecurityVehicleTable(sampleChecklists);
                    }
                });
            }

            function loadSecurityGoodsMovements() {
                $.ajax({
                    url: '{{ route("security.goods-movement.dashboard-data") }}',
                    type: 'GET',
                    success: function(response) {
                        if (response.movements) {
                            updateSecurityGoodsTable(response.movements);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading security goods movements:', xhr);
                        // Fallback dengan data sample untuk testing
                        const sampleMovements = [
                            {
                                id: 1,
                                no_urut: 'GM-001',
                                jenis_movement: 'keluar',
                                nama_tamu: 'PT ABC',
                                status: 'completed',
                                tanggal: '2024-01-15',
                                created_at: '2024-01-15 09:15:00'
                            },
                            {
                                id: 2,
                                no_urut: 'GM-002',
                                jenis_movement: 'masuk',
                                nama_tamu: 'PT XYZ',
                                status: 'pending',
                                tanggal: '2024-01-15',
                                created_at: '2024-01-15 16:45:00'
                            }
                        ];
                        updateSecurityGoodsTable(sampleMovements);
                    }
                });
            }

            function updateSecurityVehicleTable(checklists) {
                const tbody = $('#securityVehicleTableBody');
                tbody.empty();

                if (checklists.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                <i class="mdi mdi-information-outline mr-1"></i>
                                Tidak ada data vehicle checklist
                            </td>
                        </tr>
                    `);
                    return;
                }

                checklists.slice(0, 5).forEach(function(checklist) {
                    const statusBadge = getSecurityStatusBadge(checklist.status);
                    const createdDate = moment(checklist.created_at).format('DD/MM/YYYY HH:mm');

                    tbody.append(`
                        <tr>
                            <td><strong>${checklist.no_urut}</strong></td>
                            <td>${checklist.nama_driver}</td>
                            <td>${checklist.model_kendaraan}</td>
                            <td>${statusBadge}</td>
                            <td>${createdDate}</td>
                            <td>
                                <a href="/security/vehicle-checklist/${checklist.id}" class="btn btn-sm btn-info" title="View Detail">
                                    <i class="mdi mdi-eye"></i>
                                </a>
                                <a href="/security/vehicle-checklist/${checklist.id}/edit" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    `);
                });
            }

            function updateSecurityGoodsTable(movements) {
                const tbody = $('#securityGoodsTableBody');
                tbody.empty();

                if (movements.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                <i class="mdi mdi-information-outline mr-1"></i>
                                Tidak ada data goods movement
                            </td>
                        </tr>
                    `);
                    return;
                }

                movements.slice(0, 5).forEach(function(movement) {
                    const statusBadge = getSecurityStatusBadge(movement.status);
                    const createdDate = moment(movement.created_at).format('DD/MM/YYYY HH:mm');

                    tbody.append(`
                        <tr>
                            <td><strong>${movement.no_urut}</strong></td>
                            <td>${movement.jenis_movement}</td>
                            <td>${movement.nama_tamu}</td>
                            <td>${statusBadge}</td>
                            <td>${createdDate}</td>
                            <td>
                                <a href="/security/goods-movement/${movement.id}" class="btn btn-sm btn-info" title="View Detail">
                                    <i class="mdi mdi-eye"></i>
                                </a>
                                <a href="/security/goods-movement/${movement.id}/edit" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="mdi mdi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    `);
                });
            }

            function getSecurityStatusBadge(status) {
                const statusMap = {
                    'keluar': 'badge-warning',
                    'masuk': 'badge-success',
                    'completed': 'badge-success',
                    'pending': 'badge-warning',
                    'cancelled': 'badge-danger'
                };
                const badgeClass = statusMap[status] || 'badge-secondary';
                return `<span class="badge ${badgeClass}">${status}</span>`;
            }

            function refreshSecurityData() {
                loadSecurityData();
                loadSecurityVehicleChecklists();
                loadSecurityGoodsMovements();
                if (typeof toastr !== 'undefined') {
                    toastr.success('Data security berhasil di-refresh', 'Refresh Berhasil');
                }
            }

            // Document Ready
            $(document).ready(function() {
                // Load supplier timeline when supplier tab is shown
                $('#supplier-tab').on('click', function() {
                    // Load supplier data first
                    loadSupplierData();
                    loadSupplierTickets();
                    // Then load timeline
                    setTimeout(function() {
                        loadSupplierTimeline();
                    }, 1000);
                });

                // Prepress Real-time Auto Refresh
                let prepressRefreshInterval = null;
                let isPrepressTabActive = false;

                // Check if prepress tab is active
                $('#prepress-tab').on('shown.bs.tab', function() {
                    isPrepressTabActive = true;
                    loadPrepressData();
                    // Auto refresh every 30 seconds
                    prepressRefreshInterval = setInterval(function() {
                        if (isPrepressTabActive) {
                            loadPrepressData(true); // silent refresh
                        }
                    }, 30000); // 30 seconds
                });

                // Stop auto refresh when tab is hidden
                $('#prepress-tab').on('hidden.bs.tab', function() {
                    isPrepressTabActive = false;
                    if (prepressRefreshInterval) {
                        clearInterval(prepressRefreshInterval);
                        prepressRefreshInterval = null;
                    }
                });

                // Manual refresh button
                $('#btn-refresh-prepress').on('click', function() {
                    loadPrepressData(false);
                });

                // Search Job Order functionality - Pastikan elemen sudah ada
                $(document).on('click', '#btn-search-job-order', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Search button clicked');
                    if (typeof searchJobOrder === 'function') {
                        searchJobOrder();
                    } else {
                        console.error('searchJobOrder function not defined');
                        alert('Fungsi search belum tersedia. Silakan refresh halaman.');
                    }
                });

                // Enter key pada input search
                $(document).on('keypress', '#search-job-order', function(e) {
                    if (e.which === 13 || e.keyCode === 13) { // Enter key
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Enter key pressed in search input');
                        if (typeof searchJobOrder === 'function') {
                            searchJobOrder();
                        } else {
                            console.error('searchJobOrder function not defined');
                            alert('Fungsi search belum tersedia. Silakan refresh halaman.');
                        }
                    }
                });

                // Load prepress data on page load if tab is active
                if ($('#prepress-tab').hasClass('active')) {
                    isPrepressTabActive = true;
                    loadPrepressData();
                    prepressRefreshInterval = setInterval(function() {
                        if (isPrepressTabActive) {
                            loadPrepressData(true);
                        }
                    }, 30000);
                }
            });

            // Function to load prepress data
            function loadPrepressData(silent = false) {
                const loadingEl = $('#prepress-loading');
                const refreshBtn = $('#btn-refresh-prepress');
                const refreshIcon = $('#refresh-icon');

                if (!silent) {
                    loadingEl.removeClass('d-none');
                    refreshBtn.prop('disabled', true);
                    refreshIcon.addClass('fa-spin');
                }

                $.ajax({
                    url: "{{ route('api.dashboard.prepress-data') }}",
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            updatePrepressDashboard(response.data);
                            updateLastRefreshTime(response.timestamp);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error loading prepress data:', xhr);
                        if (!silent && typeof toastr !== 'undefined') {
                            toastr.error('Gagal memuat data prepress', 'Error');
                        }
                    },
                    complete: function() {
                        if (!silent) {
                            loadingEl.addClass('d-none');
                            refreshBtn.prop('disabled', false);
                            refreshIcon.removeClass('fa-spin');
                        }
                    }
                });
            }

            // Cache untuk menyimpan data prepress
            let prepressDataCache = null;

            // Function to update prepress dashboard UI
            function updatePrepressDashboard(data) {
                // Update cache
                prepressDataCache = data;

                // Update statistics
                $('#stat-finish').text(data.stats?.finish || 0);
                $('#stat-in-progress').text(data.stats?.in_progress || 0);
                $('#stat-assigned').text(data.stats?.assigned || 0);
                $('#stat-approved').text(data.stats?.approved || 0);

                // Update Marketing Department Stats
                const marketingStats = data.departments?.marketing?.stats || {};
                $('#marketing-total').text(marketingStats.total || 0);
                $('#marketing-finish').text(marketingStats.finish || 0);
                $('#marketing-in-progress').text(marketingStats.in_progress || 0);
                $('#marketing-assigned').text(marketingStats.assigned || 0);
                $('#marketing-approved').text(marketingStats.approved || 0);

                // Update percentages
                const marketingTotal = marketingStats.total || 1;
                $('#marketing-finish-pct').text(calculatePercentage(marketingStats.finish || 0, marketingTotal));
                $('#marketing-in-progress-pct').text(calculatePercentage(marketingStats.in_progress || 0, marketingTotal));
                $('#marketing-assigned-pct').text(calculatePercentage(marketingStats.assigned || 0, marketingTotal));
                $('#marketing-approved-pct').text(calculatePercentage(marketingStats.approved || 0, marketingTotal));

                // Update Other Departments Stats
                const othersStats = data.departments?.others?.stats || {};
                $('#others-total').text(othersStats.total || 0);
                $('#others-finish').text(othersStats.finish || 0);
                $('#others-in-progress').text(othersStats.in_progress || 0);
                $('#others-assigned').text(othersStats.assigned || 0);
                $('#others-approved').text(othersStats.approved || 0);

                // Update percentages
                const othersTotal = othersStats.total || 1;
                $('#others-finish-pct').text(calculatePercentage(othersStats.finish || 0, othersTotal));
                $('#others-in-progress-pct').text(calculatePercentage(othersStats.in_progress || 0, othersTotal));
                $('#others-assigned-pct').text(calculatePercentage(othersStats.assigned || 0, othersTotal));
                $('#others-approved-pct').text(calculatePercentage(othersStats.approved || 0, othersTotal));

                // Update Jobs Lists - Filter hanya yang statusnya 'in_progress'
                const marketingJobsInProgress = (data.departments?.marketing?.jobs || []).filter(job => job.status === 'in_progress');
                const othersJobsInProgress = (data.departments?.others?.jobs || []).filter(job => job.status === 'in_progress');

                updateJobsList('marketing', marketingJobsInProgress);
                updateJobsList('others', othersJobsInProgress);
            }

            // Function to calculate percentage
            function calculatePercentage(value, total) {
                if (total === 0) return '0%';
                return round((value / total) * 100, 1) + '%';
            }

            // Function to round number
            function round(value, decimals) {
                return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
            }

            // Function to update jobs list
            function updateJobsList(type, jobs) {
                const containerId = type === 'marketing' ? '#marketing-jobs-list' : '#others-jobs-list';
                const container = $(containerId);

                if (jobs.length === 0) {
                    container.html(`
                        <div class="text-center p-3">
                            <small class="text-muted">
                                <i class="mdi mdi-calendar-blank mr-1"></i>
                                ${type === 'marketing' ? 'Tidak ada job IN PROGRESS dari Marketing' : 'Tidak ada job IN PROGRESS dari departemen lain'}
                            </small>
                        </div>
                    `);
                    return;
                }

                let html = '';
                jobs.forEach(function(job) {
                    const statusInfo = getStatusInfo(job.status);
                    const priorityInfo = getPriorityInfo(job.priority);
                    let deadline = '';
                    if (job.tanggal_deadline) {
                        if (typeof moment !== 'undefined') {
                            deadline = moment(job.tanggal_deadline).format('DD/MM/YYYY HH:mm');
                        } else {
                            // Fallback if moment.js is not available
                            const date = new Date(job.tanggal_deadline);
                            const day = String(date.getDate()).padStart(2, '0');
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const year = date.getFullYear();
                            const hours = String(date.getHours()).padStart(2, '0');
                            const minutes = String(date.getMinutes()).padStart(2, '0');
                            deadline = day + '/' + month + '/' + year + ' ' + hours + ':' + minutes;
                        }
                    }

                    html += `
                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded mb-2 job-item ${statusInfo.class}" style="margin: 10px;">
                            <div class="d-flex align-items-center">
                                <div class="${statusInfo.color} rounded-circle mr-3" style="width: 12px; height: 12px;"></div>
                                <div style="min-width: 0; flex: 1;">
                                    <h6 class="mb-0 font-size-14 text-truncate">${job.nomor_job_order || ''}</h6>
                                    <small class="text-muted d-block">${job.product || ''}</small>
                                    <small class="text-success d-block">Customer: ${job.customer || ''}</small>
                                    ${job.job ? `<small class="text-info d-block">Job: ${job.job}</small>` : ''}
                                </div>
                            </div>
                            <div class="text-right" style="min-width: 0; flex-shrink: 0;">
                                <span class="status-badge badge badge-${statusInfo.badgeClass}">${statusInfo.text}</span>
                                <div class="mt-1">
                                    ${job.assignee ? `<small class="text-primary d-block">${job.assignee}</small>` : ''}
                                    ${job.est_waktu_job ? `<small class="text-muted d-block">${job.est_waktu_job} min</small>` : ''}
                                </div>
                                <div class="mt-1">
                                    <span class="${priorityInfo.class}">
                                        ${priorityInfo.icon} ${job.priority == 'Urgent' ? 'Urgent' : 'Normal'}
                                    </span>
                                </div>
                                ${deadline ? `<small class="text-warning d-block text-truncate" style="max-width: 150px;">Deadline: ${deadline}</small>` : ''}
                            </div>
                        </div>
                    `;
                });

                container.html(html);
            }

            // Function to get status info
            function getStatusInfo(status) {
                const statusMap = {
                    'completed': { color: 'bg-success', text: 'Completed', badgeClass: 'success', class: 'completed' },
                    'in_progress': { color: 'bg-warning', text: 'In Progress', badgeClass: 'warning', class: 'in-progress' },
                    'rejected': { color: 'bg-danger', text: 'Rejected', badgeClass: 'danger', class: 'urgent' },
                    'plan': { color: 'bg-secondary', text: 'Plan', badgeClass: 'secondary', class: 'plan' },
                    'assigned': { color: 'bg-secondary', text: 'Assign', badgeClass: 'secondary', class: 'assign' },
                    'open': { color: 'bg-secondary', text: 'Open', badgeClass: 'secondary', class: 'open' },
                    'approved': { color: 'bg-secondary', text: 'Approved', badgeClass: 'secondary', class: 'approved' },
                    'finish': { color: 'bg-secondary', text: 'Finish', badgeClass: 'secondary', class: 'finish' }
                };
                return statusMap[status] || { color: 'bg-info', text: 'Processing', badgeClass: 'info', class: 'in-progress' };
            }

            // Function to search job order
            function searchJobOrder() {
                console.log('=== searchJobOrder function called ===');
                const searchInput = $('#search-job-order');
                console.log('Search input element:', searchInput.length > 0 ? 'Found' : 'Not found');

                if (searchInput.length === 0) {
                    console.error('Search input element not found');
                    alert('Input search tidak ditemukan. Silakan refresh halaman.');
                    return;
                }

                const searchTerm = searchInput.val().trim().toUpperCase();
                console.log('Search term:', searchTerm);

                if (!searchTerm) {
                    console.warn('Search term is empty');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Masukkan nomor job order untuk mencari',
                            confirmButtonColor: '#3085d6'
                        });
                    } else {
                        alert('Masukkan nomor job order untuk mencari');
                    }
                    return;
                }

                console.log('Proceeding with search for:', searchTerm);

                // Cek apakah data sudah di-load
                if (!prepressDataCache) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Memuat Data',
                            text: 'Sedang memuat data prepress...',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    }

                    // Load data dulu
                    console.log('Loading prepress data from API...');
                    $.ajax({
                        url: '{{ route("api.dashboard.prepress-data") }}',
                        method: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            console.log('API response received:', response);
                            // Handle response format: {success: true, data: {...}} or direct data
                            if (response.success && response.data) {
                                prepressDataCache = response.data;
                            } else if (response.departments) {
                                // Direct data format
                                prepressDataCache = response;
                            } else {
                                prepressDataCache = response;
                            }
                            console.log('Data cached, performing search...');
                            performSearch(searchTerm);
                            if (typeof Swal !== 'undefined') {
                                Swal.close();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading data:', xhr, status, error);
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Gagal memuat data prepress: ' + error,
                                    confirmButtonColor: '#3085d6'
                                });
                            } else {
                                alert('Gagal memuat data prepress: ' + error);
                            }
                        }
                    });
                } else {
                    performSearch(searchTerm);
                }
            }

            // Function to perform the actual search
            function performSearch(searchTerm) {
                console.log('=== performSearch called ===');
                console.log('Search term:', searchTerm);
                console.log('Cached data:', prepressDataCache);

                if (!prepressDataCache) {
                    console.error('No cached data available');
                    alert('Data belum dimuat. Silakan tunggu sebentar dan coba lagi.');
                    return;
                }

                const results = [];

                // Search di semua jobs (marketing + others) - gunakan all_jobs untuk pencarian lengkap
                const marketingJobs = prepressDataCache.departments?.marketing?.all_jobs || prepressDataCache.departments?.marketing?.jobs || [];
                const othersJobs = prepressDataCache.departments?.others?.all_jobs || prepressDataCache.departments?.others?.jobs || [];
                const allJobs = [...marketingJobs, ...othersJobs];

                console.log('Marketing jobs:', marketingJobs.length);
                console.log('Others jobs:', othersJobs.length);
                console.log('Total jobs to search:', allJobs.length);

                // Cari job yang nomor job order-nya mengandung search term
                // Field yang digunakan: nomor_job_order (bukan job_order)
                allJobs.forEach(function(job) {
                    const jobOrderNumber = job.nomor_job_order || job.job_order || '';
                    const jobOrderUpper = jobOrderNumber.toUpperCase();
                    if (jobOrderUpper.includes(searchTerm)) {
                        console.log('Match found:', jobOrderNumber);
                        results.push(job);
                    }
                });

                console.log('Search results:', results.length);
                // Tampilkan hasil
                displaySearchResults(results, searchTerm);
            }

            // Function to display search results
            function displaySearchResults(results, searchTerm) {
                console.log('=== displaySearchResults called ===');
                console.log('Results count:', results.length);
                console.log('Search term:', searchTerm);

                const resultsContainer = $('#search-job-order-results');
                console.log('Results container found:', resultsContainer.length > 0);

                if (resultsContainer.length === 0) {
                    console.error('Results container not found');
                    alert('Container hasil pencarian tidak ditemukan.');
                    return;
                }

                if (results.length === 0) {
                    resultsContainer.html(`
                        <div class="text-center p-4">
                            <i class="mdi mdi-alert-circle mdi-48px text-warning"></i>
                            <h5 class="mt-3">Job Order Tidak Ditemukan</h5>
                            <p class="text-muted">Tidak ada job dengan nomor order: <strong>${searchTerm}</strong></p>
                        </div>
                    `);
                } else {
                    let html = `
                        <div class="mb-3">
                            <h6><i class="mdi mdi-check-circle text-success mr-2"></i>Ditemukan ${results.length} job dengan nomor: <strong>${searchTerm}</strong></h6>
                        </div>
                        <div class="list-group">
                    `;

                    results.forEach(function(job) {
                        const statusInfo = getStatusInfo(job.status);
                        const priorityInfo = getPriorityInfo(job.priority);
                        let deadline = '';

                        if (job.tanggal_deadline) {
                            if (typeof moment !== 'undefined') {
                                deadline = moment(job.tanggal_deadline).format('DD/MM/YYYY HH:mm');
                            } else {
                                const date = new Date(job.tanggal_deadline);
                                const day = String(date.getDate()).padStart(2, '0');
                                const month = String(date.getMonth() + 1).padStart(2, '0');
                                const year = date.getFullYear();
                                const hours = String(date.getHours()).padStart(2, '0');
                                const minutes = String(date.getMinutes()).padStart(2, '0');
                                deadline = `${day}/${month}/${year} ${hours}:${minutes}`;
                            }
                        }

                        // Format tanggal job order
                        let jobOrderDate = '';
                        if (job.tanggal_job_order) {
                            if (typeof moment !== 'undefined') {
                                jobOrderDate = moment(job.tanggal_job_order).format('DD/MM/YYYY');
                            } else {
                                const date = new Date(job.tanggal_job_order);
                                const day = String(date.getDate()).padStart(2, '0');
                                const month = String(date.getMonth() + 1).padStart(2, '0');
                                const year = date.getFullYear();
                                jobOrderDate = `${day}/${month}/${year}`;
                            }
                        }

                        // Status handling (status asli dari database)
                        const statusHandling = job.status_handling || job.status || '-';

                        html += `
                            <div class="list-group-item mb-2 border-left-${statusInfo.badgeClass}" style="border-left-width: 4px;">
                                <div class="d-flex align-items-start">
                                    <div class="${statusInfo.color} rounded-circle mr-3 mt-1" style="width: 12px; height: 12px; flex-shrink: 0;"></div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <span class="badge badge-${statusInfo.badgeClass} mr-2">${statusInfo.text}</span>
                                                    <strong>${job.nomor_job_order || job.job_order || '-'}</strong>
                                                </h6>
                                                <p class="mb-1 text-dark font-weight-bold">${job.product || '-'}</p>
                                                <small class="text-muted d-block">
                                                    <i class="mdi mdi-account-circle mr-1"></i>Customer: <strong>${job.customer || '-'}</strong>
                                                </small>
                                                ${job.job ? `<small class="text-info d-block"><i class="mdi mdi-briefcase mr-1"></i>Job Type: ${job.job}</small>` : ''}
                                                ${job.department ? `<small class="text-primary d-block"><i class="mdi mdi-office-building mr-1"></i>Department: ${job.department}</small>` : ''}
                                            </div>
                                            <div class="text-right ml-3">
                                                ${priorityInfo.icon ? `<span class="badge ${priorityInfo.class} mb-1 d-block">${priorityInfo.icon}</span>` : ''}
                                            </div>
                                        </div>

                                        <hr class="my-2">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <small class="text-muted d-block mb-1">
                                                    <i class="mdi mdi-account-check mr-1"></i>
                                                    <strong>PIC:</strong> ${job.assignee || '<span class="text-danger">Belum di-assign</span>'}
                                                </small>
                                                ${job.est_waktu_job ? `
                                                    <small class="text-muted d-block mb-1">
                                                        <i class="mdi mdi-clock mr-1"></i>
                                                        <strong>Estimated Time:</strong> ${job.est_waktu_job} min
                                                    </small>
                                                ` : ''}
                                                ${jobOrderDate ? `
                                                    <small class="text-muted d-block mb-1">
                                                        <i class="mdi mdi-calendar mr-1"></i>
                                                        <strong>Job Order Date:</strong> ${jobOrderDate}
                                                    </small>
                                                ` : ''}
                                            </div>
                                            <div class="col-md-6">
                                                ${deadline ? `
                                                    <small class="text-warning d-block mb-1">
                                                        <i class="mdi mdi-calendar-clock mr-1"></i>
                                                        <strong>Deadline:</strong> ${deadline}
                                                    </small>
                                                ` : ''}
                                                <small class="text-secondary d-block mb-1">
                                                    <i class="mdi mdi-information-outline mr-1"></i>
                                                    <strong>Status Handling:</strong> ${statusHandling}
                                                </small>
                                                ${job.created_by ? `
                                                    <small class="text-muted d-block mb-1">
                                                        <i class="mdi mdi-account-plus mr-1"></i>
                                                        <strong>Created By:</strong> ${job.created_by}
                                                    </small>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    html += `</div>`;
                    resultsContainer.html(html);
                }

                // Tampilkan modal
                console.log('Attempting to show modal');
                const modal = $('#modal-search-job-order');
                console.log('Modal element:', modal.length > 0 ? 'Found' : 'Not found');

                if (modal.length > 0) {
                    try {
                        // Coba Bootstrap 4/5 modal
                        if (typeof $.fn.modal !== 'undefined') {
                            modal.modal('show');
                            console.log('Modal shown using Bootstrap modal()');
                        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            // Bootstrap 5
                            const bsModal = new bootstrap.Modal(modal[0]);
                            bsModal.show();
                            console.log('Modal shown using Bootstrap 5');
                        } else {
                            // Fallback: show dengan CSS
                            modal.css('display', 'block');
                            modal.addClass('show');
                            $('body').addClass('modal-open');
                            modal.append('<div class="modal-backdrop fade show"></div>');
                            console.log('Modal shown using CSS fallback');
                        }
                    } catch (error) {
                        console.error('Error showing modal:', error);
                        alert('Error menampilkan modal: ' + error.message);
                    }
                } else {
                    console.error('Modal element not found in DOM');
                    alert('Modal tidak ditemukan di halaman. Pastikan modal sudah didefinisikan di HTML.');
                }
            }

            // Function to get priority info
            function getPriorityInfo(priority) {
                if (!priority || isNaN(priority)) {
                    return { icon: '', class: '' };
                }

                const priorityNum = parseInt(priority);
                if (priorityNum <= 3) {
                    return { icon: '🚨', class: 'badge-danger' };
                } else if (priorityNum <= 5) {
                    return { icon: '⚡', class: 'badge-warning' };
                }
                return { icon: '', class: '' };
            }

            // Function to update last refresh time
            function updateLastRefreshTime(timestamp) {
                if (timestamp) {
                    let time;
                    if (typeof moment !== 'undefined') {
                        time = moment(timestamp).format('HH:mm:ss');
                    } else {
                        // Fallback if moment.js is not available
                        const date = new Date(timestamp);
                        const hours = String(date.getHours()).padStart(2, '0');
                        const minutes = String(date.getMinutes()).padStart(2, '0');
                        const seconds = String(date.getSeconds()).padStart(2, '0');
                        time = hours + ':' + minutes + ':' + seconds;
                    }
                    $('#update-time-text').text('Update: ' + time);
                }
            }


        </script>
    @endsection
