@extends('main.layouts.main')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('title')
    Plan Production - Timeline (Table View)
@endsection
@section('css')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/colreorder/1.7.0/css/colReorder.bootstrap4.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #007bff;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-radius: 12px;
            --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }


        .view-toggle {
            margin-bottom: 25px;
            background: var(--light-color);
            padding: 15px;
            border-radius: var(--border-radius);
            border: 1px solid #e9ecef;
        }



        .filter-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 25px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .filter-section h6 {
            color: var(--dark-color);
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 12px 16px;
            font-size: 0.9rem;
            transition: var(--transition);
            background: white;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            transform: translateY(-1px);
        }

        .form-control-sm {
            padding: 10px 14px;
            font-size: 0.85rem;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn-filter {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
            transition: var(--transition);
            border: none;
        }

        .btn-filter:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Job Order Status Styling */
        .no-job-order {
            background-color: #ffebee !important;
            border-left: 4px solid #f44336 !important;
            position: relative;
        }

        .no-job-order:hover {
            background-color: #ffcdd2 !important;
        }

        .has-job-order {
            background-color: #e8f5e8 !important;
            border-left: 4px solid #4caf50 !important;
        }

        .has-job-order:hover {
            background-color: #c8e6c9 !important;
        }

        /* WO Column Styling */
        .wo-status-icon {
            font-size: 1.1rem;
            margin-right: 8px;
        }

        .wo-number {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .wo-cell-content {
            display: flex;
            align-items: center;
            min-height: 24px;
        }

        .job-order-warning {
            animation: pulse-warning 2s infinite;
        }

        @keyframes pulse-warning {
            0% {
                box-shadow: 0 0 0 0 rgba(244, 67, 54, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(244, 67, 54, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(244, 67, 54, 0);
            }
        }

        .job-order-success {
            animation: pulse-success 2s infinite;
        }

        @keyframes pulse-success {
            0% {
                box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(76, 175, 80, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(76, 175, 80, 0);
            }
        }

        .btn-apply {
            background: linear-gradient(135deg, var(--primary-color), #0056b3);
            color: white;
        }

        .btn-reset {
            background: linear-gradient(135deg, #6c757d, #545b62);
            color: white;
        }

        .export-section {
            background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
            padding: 20px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            border: 1px solid #c8e6c9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .export-section h6 {
            color: var(--success-color);
            margin: 0;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .export-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .export-btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
            transition: var(--transition);
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .export-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-excel {
            background: linear-gradient(135deg, #217346, #1e6b3d);
            color: white;
        }

        .btn-pdf {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }

        .btn-print {
            background: linear-gradient(135deg, #fd7e14, #e55a00);
            color: white;
        }

        .table-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            padding: 0;
            border: 1px solid #e9ecef;
        }

        .table-header {
            background: linear-gradient(135deg, var(--success-color), #1e7e34);
            color: white;
            padding: 20px;
            text-align: center;
        }

        .table-header h5 {
            margin: 0;
            font-weight: 700;
            font-size: 1.3rem;
        }

        .table-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        /* DataTables Custom Styling */
        .dataTables_wrapper {
            padding: 20px;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            margin: 15px 0;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 10px 16px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            margin-left: 10px;
            transition: var(--transition);
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .dataTables_wrapper .dataTables_length select {
            padding: 8px 12px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            margin: 0 10px;
            transition: var(--transition);
        }

        .dataTables_wrapper .dataTables_length select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .page-link {
            padding: 10px 16px;
            border-radius: 8px;
            margin: 0 3px;
            border: 1px solid #dee2e6;
            color: var(--primary-color);
            transition: var(--transition);
        }

        .page-link:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            transform: translateY(-1px);
        }

        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Table Styling */
        #timeline-table {
            font-size: 0.9rem;
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }

        #timeline-table th {
            padding: 16px 12px;
            font-weight: 700;
            font-size: 0.85rem;
            background: linear-gradient(135deg, var(--success-color), #1e7e34);
            color: white;
            border: none;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
        }

        #timeline-table th:first-child {
            border-top-left-radius: 8px;
        }

        #timeline-table th:last-child {
            border-top-right-radius: 8px;
        }

        #timeline-table th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: rgba(255, 255, 255, 0.3);
        }

        #timeline-table td {
            padding: 14px 12px;
            vertical-align: middle;
            border: 1px solid #e9ecef;
            line-height: 1.5;
            transition: var(--transition);
        }

        #timeline-table tbody tr {
            transition: var(--transition);
        }

        #timeline-table tbody tr:hover {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            transform: scale(1.01);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        #timeline-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        #timeline-table tbody tr:nth-child(even):hover {
            background: linear-gradient(135deg, #e9ecef, #dee2e6);
        }

        /* Column Specific Styling */
        .col-kode-item {
            min-width: 160px;
        }

        .col-nama-item {
            min-width: 300px;
        }
        .col-wo-docno-item {
            min-width: 180px;
        }
        .col-end-cetak-item {
            min-width: 150px;
        }

        .col-qty-order {
            min-width: 130px;
        }

        .col-delivery {
            min-width: 130px;
        }

        .col-mesin {
            min-width: 110px;
        }

        .col-kapasitas {
            min-width: 110px;
        }

        .col-lama-cetak-jam {
            min-width: 130px;
        }

        .col-lama-cetak-hari {
            min-width: 130px;
        }

        .col-mulai-cetak {
            min-width: 150px;
        }

        .col-setup {
            min-width: 110px;
        }

        .col-istirahat {
            min-width: 110px;
        }

        .col-akhir-cetak {
            min-width: 150px;
        }

        .col-process {
            min-width: 110px;
        }

        .col-actions {
            min-width: 100px;
        }

        /* Status and Badge Styling */
        .status-pending {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7) !important;
            border-left: 4px solid var(--warning-color);
        }

        .status-progress {
            background: linear-gradient(135deg, #d4edda, #c8e6c9) !important;
            border-left: 4px solid var(--success-color);
        }

        .status-completed {
            background: linear-gradient(135deg, #d1ecf1, #b8e6f1) !important;
            border-left: 4px solid var(--info-color);
        }

        .status-urgent {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb) !important;
            border-left: 4px solid var(--danger-color);
        }

        .process-badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            min-width: 60px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            transition: var(--transition);
            border: 2px solid transparent;
        }

        .process-badge:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .process-badge.ctk {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            color: #1565c0;
            border-color: #2196f3;
        }

        .process-badge.printing {
            background: linear-gradient(135deg, #fff3e0, #ffe0b2);
            color: #f57c00;
            border-color: #ff9800;
        }

        .dept-badge {
            padding: 6px 14px;
            border-radius: 18px;
            font-size: 0.75rem;
            font-weight: 700;
            min-width: 70px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: var(--transition);
        }

        .dept-badge:hover {
            transform: scale(1.05);
        }

        .dept-ctk {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            color: #1565c0;
        }

        .dept-prepress {
            background: linear-gradient(135deg, #f3e5f5, #e1bee7);
            color: #7b1fa2;
        }

        .dept-finishing {
            background: linear-gradient(135deg, #e8f5e8, #c8e6c9);
            color: #388e3c;
        }

        .dept-packing {
            background: linear-gradient(135deg, #fff3e0, #ffe0b2);
            color: #f57c00;
        }

        .dept-maintenance {
            background: linear-gradient(135deg, #fce4ec, #f8bbd9);
            color: #c2185b;
        }

        .dept-warehouse {
            background: linear-gradient(135deg, #e0f2f1, #b2dfdb);
            color: #00695c;
        }

        .dept-quality {
            background: linear-gradient(135deg, #f1f8e9, #dcedc8);
            color: #558b2f;
        }

        .dept-production {
            background: linear-gradient(135deg, #e8eaf6, #c5cae9);
            color: #3f51b5;
        }

        .dept-default {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            color: #495057;
        }

        /* Special Cell Styling */
        .machine-cell {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            font-weight: 700;
            border-left: 4px solid var(--primary-color);
            text-align: center;
            color: var(--primary-color);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .quantity-cell {
            text-align: right;
            font-weight: 600;
            font-family: 'Courier New', monospace;
            color: var(--dark-color);
        }

        .time-cell {
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            text-align: center;
            font-weight: 600;
            color: var(--dark-color);
        }

        /* Loading and Empty States */
        .loading-state,
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--dark-color);
        }

        .loading-state i,
        .empty-state i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 20px;
            opacity: 0.7;
        }

        .loading-state p,
        .empty-state p {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .loading-state small,
        .empty-state small {
            color: #6c757d;
            font-size: 0.9rem;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {


            .filter-section {
                padding: 20px;
            }

            .export-section {
                flex-direction: column;
                align-items: stretch;
            }
        }

        @media (max-width: 768px) {


            .filter-section {
                padding: 15px;
            }

            .filter-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .export-buttons {
                justify-content: center;
            }

            .table-container {
                overflow-x: auto;
            }

            .dataTables_wrapper {
                padding: 15px;
            }
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        .slide-up {
            animation: slideUp 0.4s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom Scrollbar */
        .table-container::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #0056b3;
        }

        /* Toast Notifications */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
            min-width: 250px;
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            animation: slideInRight 0.3s ease-out;
        }

        .toast.show {
            display: block;
        }

        .toast-body {
            padding: 12px 16px;
            font-weight: 500;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Enhanced Button States */
        .btn:active {
            transform: translateY(1px);
        }

        /* Sync Times Button */
        .sync-times-btn {
            transition: all 0.3s ease;
        }

        .sync-times-btn:hover {
            background-color: #17a2b8;
            color: white;
            transform: scale(1.05);
        }

        .sync-times-btn:active {
            transform: scale(0.95);
        }

        /* Save Plan Button */
        #save-plan-update {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        #save-plan-update:hover {
            background-color: #28a745;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }

        #save-plan-update:active {
            transform: translateY(0);
        }

        #save-plan-update.loading {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Enhanced Form Controls */
        .form-control:focus {
            transform: translateY(-1px);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Enhanced Table Interactions */
        #timeline-table tbody tr {
            position: relative;
            overflow: hidden;
        }

        #timeline-table tbody tr::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, transparent, rgba(0, 123, 255, 0.05));
            opacity: 0;
            transition: var(--transition);
        }

        #timeline-table tbody tr:hover::before {
            opacity: 1;
        }

        /* Enhanced Modal Styling */
        .modal-content {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
        }

        /* Enhanced Loading States */
        .loading-state,
        .empty-state {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin: 20px 0;
        }

        /* Enhanced Filter Section */
        .filter-section {
            position: relative;
            overflow: hidden;
        }

        .filter-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--success-color), var(--info-color));
            border-radius: 3px;
        }

        /* Enhanced Export Section */
        .export-section {
            position: relative;
            overflow: hidden;
        }

        .export-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--success-color), var(--primary-color), var(--warning-color));
            border-radius: 3px;
        }

        /* Enhanced Table Header */
        .table-header {
            position: relative;
            overflow: hidden;
        }

        .table-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--success-color), var(--info-color), var(--success-color));
            border-radius: 3px;
        }

        /* Enhanced Process Badges */
        .process-badge {
            position: relative;
            overflow: hidden;
        }

        .process-badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .process-badge:hover::before {
            left: 100%;
        }

        /* Enhanced Machine Cells */
        .machine-cell {
            position: relative;
            overflow: hidden;
        }

        .machine-cell::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--primary-color);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .machine-cell:hover::after {
            transform: scaleX(1);
        }

        /* Enhanced Quantity Cells */
        .quantity-cell {
            position: relative;
        }

        .quantity-cell::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 0;
            background: var(--success-color);
            transition: height 0.3s ease;
        }

        .quantity-cell:hover::before {
            height: 80%;
        }

        /* Enhanced Time Cells */
        .time-cell {
            position: relative;
        }

        .time-cell::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: var(--info-color);
            transition: width 0.3s ease;
        }

        .time-cell:hover::after {
            width: 80%;
        }

        /* Enhanced Status Rows */
        .status-pending,
        .status-progress,
        .status-completed,
        .status-urgent {
            position: relative;
        }

        .status-pending::before,
        .status-progress::before,
        .status-completed::before,
        .status-urgent::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: currentColor;
            opacity: 0.7;
        }

        /* Enhanced Responsive Design */
        @media (max-width: 576px) {

            .filter-section {
                padding: 15px;
            }

            .export-section {
                padding: 15px;
                flex-direction: column;
                gap: 10px;
            }

            .export-buttons {
                justify-content: center;
            }

            .table-container {
                overflow-x: auto;
            }

            .dataTables_wrapper {
                padding: 10px;
            }

            .btn-group .btn {
                padding: 8px 16px;
                font-size: 0.8rem;
            }

            .filter-actions {
                flex-direction: column;
                gap: 10px;
            }
        }

        /* Setup dan Break Time Input Styling */
        .setup-time-input,
        .break-time-input,
        .quantity-input,
        .date-input {
            border: 2px solid #e9ecef;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-weight: 600;
            color: #495057;
        }

        .setup-time-input:focus,
        .break-time-input:focus,
        .quantity-input:focus,
        .date-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            transform: translateY(-1px);
        }

        .setup-time-input:hover,
        .break-time-input:hover,
        .quantity-input:hover,
        .date-input:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }

        /* Date Input Styling */
        .date-input {
            font-size: 0.85rem;
            padding: 6px 10px;
            width: 100%;
            max-width: 180px;
        }

        /* Timeline calculation indicator */
        .timeline-calculating {
            opacity: 0.7;
            pointer-events: none;
        }

        .timeline-calculating::after {
            content: '⏳';
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: translateY(-50%) rotate(0deg); }
            to { transform: translateY(-50%) rotate(360deg); }
        }

        /* Print Styles */
        @media print {

            .page-titles,
            .view-toggle,
            .filter-section,
            .export-section,
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                display: none !important;
            }

            .table-container {
                box-shadow: none;
                border: 1px solid #000;
            }

            #timeline-table th,
            #timeline-table td {
                border: 1px solid #000 !important;
                background: white !important;
                color: black !important;
            }
        }

        /* High Contrast Mode Support */
        @media (prefers-contrast: high) {
            :root {
                --primary-color: #000080;
                --success-color: #008000;
                --warning-color: #808000;
                --danger-color: #800000;
                --info-color: #008080;
            }

            .btn {
                border: 2px solid currentColor;
            }

            .form-control {
                border: 2px solid currentColor;
            }
        }

        /* Reduced Motion Support */
        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Machine Tabs Styling */
        .machine-tabs {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .machine-tabs .nav-tabs {
            border-bottom: none;
        }

        .machine-tabs .nav-link {
            border: none;
            border-radius: 8px;
            margin-right: 5px;
            padding: 10px 20px;
            font-weight: 500;
            color: #6c757d;
            background: rgba(255, 255, 255, 0.7);
            transition: all 0.3s ease;
        }

        .machine-tabs .nav-link:hover {
            background: rgba(255, 255, 255, 0.9);
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .machine-tabs .nav-link.active {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        /* Machine Table Container */
        .machine-table-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }

        .machine-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary-color);
        }

        .machine-header h6 {
            margin: 0;
            color: #495057;
            font-weight: 600;
        }

        .machine-header .badge {
            background: var(--primary-color);
            color: white;
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .machine-header p {
            margin: 5px 0 0 0;
            font-size: 0.9rem;
        }

        .shift-select-btn {
            font-size: 0.75rem;
            padding: 4px 10px;
        }

        .shift-select-btn.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .shift-select-btn.active:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        /* Machine-specific table styling */
        .machine-table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 15px;
        }

        .machine-table thead th {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
            color: white;
            border: none;
            padding: 15px 10px;
            font-weight: 600;
            text-align: center;
            font-size: 0.85rem;
        }

        .machine-table tbody tr {
            transition: all 0.2s ease;
        }

        .machine-table tbody tr:hover {
            background: rgba(0, 123, 255, 0.05);
            transform: translateX(5px);
        }

        /* Tab Content Styling */
        .tab-content {
            margin-top: 20px;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
        }

        .tab-pane.show {
            display: block;
        }

        /* Hide machine-specific tables by default */
        .tab-pane:not(#all) .machine-table-container {
            display: none;
        }

        /* Show machine-specific tables when tab is active */
        .tab-pane.active .machine-table-container {
            display: block;
        }

        /* Hide "all" table when machine-specific tab is active */
        .tab-pane.active:not(#all)~#all .table-responsive {
            display: none;
        }

        /* Machine tab badges */
        .nav-link .badge {
            margin-left: 8px;
            font-size: 0.7rem;
            padding: 3px 6px;
        }

        /* Active tab styling */
        .nav-link.active {
            background: var(--primary-color) !important;
            color: white !important;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        /* Tab hover effects */
        .nav-link:hover:not(.active) {
            background: rgba(255, 255, 255, 0.9) !important;
            color: var(--primary-color) !important;
            transform: translateY(-2px);
        }

        /* Enhanced Table Styling */
        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table td {
            vertical-align: middle;
            border-color: #e9ecef;
            padding: 12px 10px;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: rgba(0, 123, 255, 0.05);
            transform: translateX(5px);
        }

        /* Process Badge Styling */
        .process-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .process-badge.ctk {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .process-badge.ct {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
        }

        /* Quantity Cell Styling */
        .quantity-cell {
            text-align: right;
            font-weight: 600;
            font-family: 'Courier New', monospace;
            color: #495057;
        }

        /* Time Cell Styling */
        .time-cell {
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
            color: #6c757d;
        }

        /* Machine Cell Styling */
        .machine-cell {
            font-weight: 600;
            color: var(--primary-color);
            text-align: center;
        }

        /* Fade In Animation */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Modal Styling */
        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            border-radius: 15px 15px 0 0;
            border-bottom: none;
        }

        .modal-footer {
            border-top: none;
            border-radius: 0 0 15px 15px;
        }

        .modal-body {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Action Buttons Styling */
        .btn-group .btn {
            border-radius: 6px;
            margin: 0 2px;
            transition: all 0.3s ease;
        }

        .btn-group .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .move-item-btn {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            border: none;
        }

        .split-item-btn {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            border: none;
        }

        /* Toast Notifications */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
            min-width: 300px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .toast-header {
            border-bottom: none;
            border-radius: 10px 10px 0 0;
        }

        .toast-body {
            padding: 15px 20px;
            font-weight: 500;
        }

        /* Loading Spinner */
        #loading-spinner {
            display: none;
            text-align: center;
            padding: 40px;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
            color: var(--primary-color);
        }

        /* Empty State */
        #empty-state {
            display: none;
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            margin: 20px 0;
        }

        #empty-state i {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 20px;
        }

        /* Responsive Improvements */
        @media (max-width: 768px) {
            .machine-table-container {
                padding: 15px;
                margin-bottom: 15px;
            }

            .machine-header {
                padding: 12px 15px;
                margin-bottom: 15px;
            }

            .table-responsive {
                font-size: 0.85rem;
            }

            .table th,
            .table td {
                padding: 8px 6px;
            }

            .btn-group .btn {
                padding: 4px 6px;
                font-size: 0.75rem;
            }

            .modal-dialog {
                margin: 10px;
            }
        }

        /* Radio button and label styling for merge modal */
        .cursor-pointer {
            cursor: pointer !important;
        }

        /* Make radio button more visible in merge table */
        input[type="radio"][name="merge-target"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        /* Style for merge table rows */
        #merge-target-list tr:hover {
            background-color: #f8f9fa;
        }

        /* Enhance clickable area */
        #merge-target-list label {
            display: block;
            padding: 8px 4px;
            margin: 0;
            cursor: pointer;
        }

        /* UP/DOWN Priority Styling */
        .priority-cell {
            position: relative;
            min-width: 80px;
        }

        .priority-number {
            font-weight: bold;
            color: var(--primary-color);
            font-size: 1.1rem;
        }

        /* Remove any ::before pseudo-elements for maintenance rows */
        .maintenance-row::before,
        .maintenance-row::after {
            content: none !important;
            display: none !important;
        }

        /* Remove ::before from all table rows in machine table */
        .machine-table tbody tr::before {
            content: none !important;
            display: none !important;
        }

        /* Ensure priority cell is visible for maintenance */
        .maintenance-row .priority-cell {
            position: relative !important;
            z-index: 10 !important;
            background-color: #fff3cd !important;
            border: 2px solid #ffc107 !important;
        }

        .maintenance-row .priority-number {
            position: relative !important;
            z-index: 11 !important;
            background-color: white !important;
            padding: 4px !important;
            border-radius: 4px !important;
            display: block !important;
            visibility: visible !important;
        }

        /* Priority controls container */
        .priority-controls {
            display: flex;
            gap: 2px;
            justify-content: center;
            margin-top: 4px;
        }

        /* Priority button styling */
        .priority-up-btn,
        .priority-down-btn {
            padding: 4px 6px;
            font-size: 0.75rem;
            min-width: 32px;
            transition: all 0.2s ease;
        }

        .priority-up-btn:disabled,
        .priority-down-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .priority-up-btn:not(:disabled):hover {
            background-color: #0056b3;
            border-color: #0056b3;
            color: white;
            transform: translateY(-1px);
        }

        .priority-down-btn:not(:disabled):hover {
            background-color: #545b62;
            border-color: #545b62;
            color: white;
            transform: translateY(-1px);
        }

        .priority-up-btn:not(:disabled):active,
        .priority-down-btn:not(:disabled):active {
            transform: translateY(0);
        }

        /* Priority input styling */
        .priority-input {
            border: 2px solid #007bff;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
            color: #007bff;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .priority-input:focus {
            border-color: #0056b3;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            background-color: white;
            outline: none;
        }

        .priority-input:hover {
            border-color: #0056b3;
            background-color: white;
        }

        .priority-input-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Row hover effect for better UX */
        .plan-row:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        /* Plan Preview Card Styling */
        .plan-preview-card {
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(10px);
            max-height: 0;
            overflow: hidden;
            margin-top: 15px;
        }

        .plan-preview-card.show {
            opacity: 1;
            transform: translateY(0);
            max-height: 600px;
        }

        .plan-preview-card .card {
            border: 2px solid #17a2b8;
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.2);
        }

        .plan-preview-card .card-header {
            background: linear-gradient(135deg, #17a2b8, #138496);
            border-bottom: none;
        }

        .plan-item {
            transition: all 0.2s ease;
            border: 1px solid #dee2e6;
            margin-bottom: 8px;
            border-radius: 6px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }

        .plan-item.bg-light {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef) !important;
        }

        .plan-item.bg-warning {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7) !important;
            border: 2px solid #ffc107;
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
            position: relative;
        }

        .plan-item:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-color: #17a2b8;
        }

        .plan-item.bg-warning {
            border: 2px solid #ffc107;
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
            position: relative;
            background: linear-gradient(135deg, #fff3cd, #ffeaa7) !important;
        }

        .plan-item.bg-warning::before {
            content: '✏️';
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ffc107;
            color: #000;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            z-index: 1;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .plan-list {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 10px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 10px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }

        .plan-list::-webkit-scrollbar {
            width: 6px;
        }

        .plan-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .plan-list::-webkit-scrollbar-thumb {
            background: #17a2b8;
            border-radius: 3px;
        }

        .plan-list::-webkit-scrollbar-thumb:hover {
            background: #138496;
        }

        /* Responsive adjustments for plan preview */
        @media (max-width: 768px) {
            .plan-preview-card .card-body {
                padding: 15px;
            }

            .plan-item .row {
                font-size: 0.85rem;
            }

            .plan-list {
                max-height: 250px;
            }
        }

        /* Unsaved Changes Indicator */
        #unsaved-changes-indicator {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* Notification Panel */
        .notification-panel {
            position: fixed;
            top: 80px;
            right: -350px;
            width: 350px;
            max-height: calc(100vh - 100px);
            background: white;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px 0 0 8px;
            z-index: 9999;
            transition: right 0.3s ease;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .notification-panel.show {
            right: 0;
        }

        .notification-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e9ecef;
        }

        .notification-header h6 {
            margin: 0;
            font-weight: 600;
        }

        .notification-body {
            overflow-y: auto;
            max-height: 550px;
            padding: 0;
        }

        .notification-item {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            transition: background 0.2s;
        }

        .notification-item:hover {
            background: #f8f9fa;
        }

        .notification-item.notification-error {
            border-left: 4px solid #dc3545;
        }

        .notification-item.notification-warning {
            border-left: 4px solid #ffc107;
        }

        .notification-item.notification-success {
            border-left: 4px solid #28a745;
        }

        .notification-item.notification-info {
            border-left: 4px solid #17a2b8;
        }

        .notification-item.notification-deadline {
            border-left: 4px solid #fd7e14;
            background: #fff3cd;
        }

        /* Plan Notification Badge - ensure it doesn't get cut off */
        .plan-notif-badge {
            position: absolute !important;
            top: 2px !important;
            right: 2px !important;
            z-index: 10000 !important;
            font-size: 10px !important;
            padding: 2px 5px !important;
            min-width: 18px !important;
            height: 18px !important;
            line-height: 14px !important;
            text-align: center !important;
            border-radius: 9px !important;
        }

        .notification-content {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .notification-content i {
            font-size: 18px;
        }

        .notification-content span {
            font-size: 14px;
        }

        /* Conflict Warning */
        .conflict-warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
        }

        .capacity-overload {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
        }
    </style>

<!-- SheetJS (XLSX) Library for Excel Export -->
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>

<!-- ExcelJS for advanced Excel styling -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>

@endsection
@section('page-title')
    Timeline Table
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Timeline Table</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Transaction</a></li>
                <li class="breadcrumb-item active">PPIC</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group mb-3" role="group" aria-label="View Toggle">
                        <a href="{{ route('process.plan-first-prd') }}" class="btn btn-outline-info">
                            <i class="mdi mdi-chart-gantt"></i> Timeline View
                        </button>
                        <a href="{{ route('process.plan-first-table') }}" class="btn btn-info">
                            <i class="mdi mdi-table"></i> Timeline Plan CETAK
                        </a>
                        <a href="{{ route('process.plan-first-table-plong') }}" class="btn btn-outline-info">
                            <i class="mdi mdi-table"></i> Timeline Plan PLONG
                        </a>
                    </div>
                    <br>

                    <div class="filter-section">
                        <h6><i class="mdi mdi-filter-variant"></i> Filter & Pencarian</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date-from" class="form-label">Dari Tanggal</label>
                                    <input type="date" class="form-control form-control-sm" id="date-from">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date-to" class="form-label">Sampai Tanggal</label>
                                    <input type="date" class="form-control form-control-sm" id="date-to">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="machine-filter" class="form-label">Mesin</label>
                                    <select class="form-control form-control-sm" id="machine-filter">
                                        <option value="">Semua Mesin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="department-filter" class="form-label">Departemen</label>
                                    <select class="form-control form-control-sm" id="department-filter">
                                        <option value="">Semua Departemen</option>
                                        <option value="Printing">Printing</option>
                                        <option value="Cetak">Cetak</option>
                                        <option value="CTK">CTK</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="filter-actions">
                            <button type="button" class="btn btn-filter btn-apply text-white" id="apply-filters">
                                <i class="mdi mdi-filter"></i> Terapkan Filter
                            </button>
                            <button type="button" class="btn btn-filter btn-reset text-white" id="reset-filters">
                                <i class="mdi mdi-refresh"></i> Reset Filter
                            </button>
                            <button type="button" class="btn btn-success ml-2" id="save-plan-update">
                                <i class="mdi mdi-content-save"></i> Save Plan Update
                            </button>
                            <button type="button" class="btn btn-excel ml-2 text-white" id="export-excel-direct">
                                <i class="mdi mdi-file-excel"></i> Export Excel
                            </button>
                            <span id="unsaved-changes-indicator" class="ml-2 badge badge-warning" style="display: none;">
                                <i class="mdi mdi-alert-circle"></i> Ada perubahan yang belum disimpan
                            </span>
                            <div class="btn-group ml-2" role="group">
                                <button type="button" class="btn btn-outline-secondary" id="view-history-btn" title="Lihat History Perubahan Plan" style="display: none;">
                                    <i class="mdi mdi-history"></i> View History
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="compare-versions-btn" title="Bandingkan Versi Plan" style="display: none;">
                                    <i class="mdi mdi-code-compare"></i> Compare Versions
                                </button>
                            </div>
                            {{-- <div class="ml-auto">
                                <span class="text-muted">
                                    <i class="mdi mdi-information-outline"></i>
                                    Filter akan otomatis diterapkan saat data berubah
                                </span>
                            </div> --}}
                        </div>
                    </div>

                    <div class="table-container">
                        <div id="loading-spinner">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Memuat data timeline produksi...</p>
                        </div>

                        <div id="empty-state">
                            <i class="mdi mdi-information-outline"></i>
                            <h5 class="text-muted">Tidak Ada Data</h5>
                            <p class="text-muted">Tidak ada data timeline produksi yang tersedia untuk filter yang dipilih.
                            </p>
                            <button class="btn btn-primary" onclick="DataManager.loadData()">
                                <i class="mdi mdi-refresh"></i> Muat Ulang Data
                            </button>
                        </div>

                        <div id="machine-tables-container">
                        </div>

                        <div class="remaining-items-section mt-4" id="remaining-items-section" style="display: none;">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">
                                        <i class="mdi mdi-clock-outline"></i> Item Sisa - Menunggu Produksi
                                        <span class="badge badge-dark ml-2" id="remaining-count">0</span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="remaining-items-list">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Plan Manual Modal -->
    <div class="modal fade" id="addPlanManualModal" tabindex="-1" role="dialog" aria-labelledby="addPlanManualModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white" id="addPlanManualModalLabel">
                        <i class="mdi mdi-plus-circle"></i> Tambah Plan Manual
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="mdi mdi-information-outline"></i>
                        <small>Fungsi ini digunakan untuk menambah plan manual (misalnya untuk ganti rusak, dll) ke mesin yang dipilih.</small>
                    </div>

                    <div class="form-group">
                        <label for="manual-machine-code" class="form-label">
                            <i class="mdi mdi-gauge"></i> Mesin <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="manual-machine-code" readonly>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="manual-code-item" class="form-label">
                                    <i class="mdi mdi-barcode"></i> Kode Item <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="manual-code-item" placeholder="Masukkan kode item" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="manual-material-name" class="form-label">
                                    <i class="mdi mdi-tag"></i> Nama Material <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="manual-material-name" placeholder="Akan terisi otomatis dari kode item" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="manual-wo-docno" class="form-label">
                                    <i class="mdi mdi-file-document"></i> WO DocNo
                                </label>
                                <input type="text" class="form-control" id="manual-wo-docno" placeholder="WOT-XXXXXX-XXXX">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="manual-quantity" class="form-label">
                                    <i class="mdi mdi-counter"></i> Quantity <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="manual-quantity" placeholder="0" min="1" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="manual-delivery-date" class="form-label">
                                    <i class="mdi mdi-calendar-check"></i> Tanggal Delivery
                                </label>
                                <input type="date" class="form-control" id="manual-delivery-date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="manual-process" class="form-label">
                                    <i class="mdi mdi-cogs"></i> Process
                                </label>
                                <select class="form-control" id="manual-process">
                                    <option value="CTK">CTK</option>
                                    <option value="Printing">Printing</option>
                                    <option value="Cetak">Cetak</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="mdi mdi-information-outline"></i>
                        <small><strong>Catatan:</strong> Mulai dan Akhir Cetak akan dihitung otomatis berdasarkan plan terakhir di mesin ini. Plan manual akan ditambahkan di urutan paling akhir (prioritas terakhir) dan bisa diubah prioritasnya nanti.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="manual-setup-time" class="form-label">
                                    <i class="mdi mdi-timer"></i> Setup Time (jam)
                                </label>
                                <input type="number" class="form-control" id="manual-setup-time" placeholder="0" min="0" step="0.1" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="manual-break-time" class="form-label">
                                    <i class="mdi mdi-coffee"></i> Istirahat (jam)
                                </label>
                                <input type="number" class="form-control" id="manual-break-time" placeholder="0" min="0" step="0.1" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="manual-up-cetak" class="form-label">
                                    <i class="mdi mdi-speedometer"></i> Up Cetak
                                </label>
                                <input type="number" class="form-control" id="manual-up-cetak" placeholder="1" min="1" value="1">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="mdi mdi-close"></i> Batal
                    </button>
                    <button type="button" class="btn btn-success" id="confirm-add-plan-manual">
                        <i class="mdi mdi-check"></i> Tambah Plan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Excel Modal -->
    <div class="modal fade" id="exportExcelModal" tabindex="-1" role="dialog" aria-labelledby="exportExcelModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white" id="exportExcelModalLabel">
                        <i class="mdi mdi-file-excel"></i> Export ke Excel
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="mdi mdi-information-outline"></i>
                        <p><strong>Export Excel</strong> akan mengekspor semua data yang sedang ditampilkan di tabel.</p>
                        <p class="mb-0"><small>Export akan memecah item yang melewati shift menjadi beberapa baris sesuai shift yang dilewati.</small></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="mdi mdi-close"></i> Batal
                    </button>
                    <button type="button" class="btn btn-success" id="confirm-export-excel">
                        <i class="mdi mdi-download"></i> Export Excel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View History Modal -->
    <div class="modal fade" id="viewHistoryModal" tabindex="-1" role="dialog" aria-labelledby="viewHistoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title text-white" id="viewHistoryModalLabel">
                        <i class="mdi mdi-history"></i> History Perubahan Plan
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="history-item-info" class="alert alert-info mb-3">
                        <!-- Item info will be populated here -->
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Waktu</th>
                                    <th>User</th>
                                    <th>Aksi</th>
                                    <th>Detail Perubahan</th>
                                </tr>
                            </thead>
                            <tbody id="history-table-body">
                                <!-- History entries will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="mdi mdi-close"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Compare Plan Versions Modal -->
    <div class="modal fade" id="compareVersionsModal" tabindex="-1" role="dialog" aria-labelledby="compareVersionsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="compareVersionsModalLabel">
                        <i class="mdi mdi-code-compare"></i> Bandingkan Versi Plan
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Versi Sebelumnya:</label>
                            <select class="form-control" id="compare-version-1">
                                <option value="">-- Pilih Versi --</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Versi Saat Ini / Versi Lain:</label>
                            <select class="form-control" id="compare-version-2">
                                <option value="current">Versi Saat Ini</option>
                            </select>
                        </div>
                    </div>
                    <div id="compare-result" class="mt-3">
                        <!-- Comparison result will be shown here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="mdi mdi-close"></i> Tutup
                    </button>
                    <button type="button" class="btn btn-primary" id="btn-compare-versions">
                        <i class="mdi mdi-code-compare"></i> Bandingkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="planDetailModal" tabindex="-1" role="dialog" aria-labelledby="planDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title text-white" id="planDetailModalLabel">
                        <i class="mdi mdi-information-outline"></i> Detail Plan Production
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="mdi mdi-file-document"></i> Informasi Dokumen
                            </h6>
                            <table class="table table-sm table-bordered">
                                <tr>
                                    <th>SO DocNo</th>
                                    <td id="modal-so"></td>
                                </tr>
                                <tr>
                                    <th>WO DocNo</th>
                                    <td id="modal-wo"></td>
                                </tr>
                                <tr>
                                    <th>Material Code</th>
                                    <td id="modal-item"></td>
                                </tr>
                                <tr>
                                    <th>Material Name</th>
                                    <td id="modal-material-name"></td>
                                </tr>
                                <tr>
                                    <th>Quantity</th>
                                    <td id="modal-qty"></td>
                                </tr>
                                <tr>
                                    <th>Machine</th>
                                    <td id="modal-machine"></td>
                                </tr>
                                <tr>
                                    <th>Capacity Per Hour</th>
                                    <td id="modal-capacity-per-hour"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="mdi mdi-cogs"></i> Informasi Proses
                            </h6>
                            <table class="table table-sm table-bordered">
                                <tr>
                                    <th>Process</th>
                                    <td id="modal-process"></td>
                                </tr>
                                <tr>
                                    <th>Department</th>
                                    <td id="modal-department"></td>
                                </tr>
                                <tr>
                                    <th>Start Time</th>
                                    <td id="modal-start"></td>
                                </tr>
                                <tr>
                                    <th>End Time</th>
                                    <td id="modal-end"></td>
                                </tr>
                                <tr>
                                    <th>Duration</th>
                                    <td id="modal-duration"></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td id="modal-status"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="plan-preview-card">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="mdi mdi-calendar-clock"></i> Preview Plan - Tanggal & Mesin Terpilih
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="plan-preview-content">
                                            <!-- Data plan akan ditampilkan di sini -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="mdi mdi-close"></i> Tutup
                    </button>
                    <button type="button" class="btn btn-primary" id="save-date-changes">
                        <i class="mdi mdi-content-save"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Move Item Modal -->
    <div class="modal fade" id="moveItemModal" tabindex="-1" role="dialog" aria-labelledby="moveItemModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="moveItemModalLabel">
                        <i class="mdi mdi-swap-horizontal"></i> Pindahkan Item ke Mesin Lain
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="move-item-info">Informasi Item:</label>
                        <div id="move-item-info" class="alert alert-info">
                            <!-- Item info will be populated here -->
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="target-machine-select">Pilih Mesin Tujuan:</label>
                        <select class="form-control" id="target-machine-select">
                            <option value="">-- Pilih Mesin --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="move-quantity">Quantity yang Dipindah:</label>
                        <input type="number" class="form-control" id="move-quantity" min="1" value="1">
                        <small class="form-text text-muted">
                            <strong>MOVE:</strong> Jika quantity = total, item akan pindah ke mesin baru<br>
                            <strong>SPLIT:</strong> Jika quantity < total, item akan dibagi antara mesin asal dan baru
                                </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary view-history-from-modal" data-item-id="" title="Lihat History Item Ini">
                        <i class="mdi mdi-history"></i> View History
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="mdi mdi-close"></i> Batal
                    </button>
                    <button type="button" class="btn btn-info" id="confirm-move-btn">
                        <i class="mdi mdi-check"></i> Pindahkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Split Item Modal -->
    <div class="modal fade" id="splitItemModal" tabindex="-1" role="dialog" aria-labelledby="splitItemModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="splitItemModalLabel">
                        <i class="mdi mdi-split-horizontal"></i> Bagi Quantity Item
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="split-item-info">Informasi Item:</label>
                        <div id="split-item-info" class="alert alert-warning">
                            <!-- Item info will be populated here -->
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="split-target-machine">Pilih Mesin Tujuan:</label>
                        <select class="form-control" id="split-target-machine">
                            <option value="">-- Pilih Mesin --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="split-quantity">Quantity yang Dibagi:</label>
                        <input type="number" class="form-control" id="split-quantity" min="1" value="1">
                        <small class="form-text text-muted">Quantity yang akan dipindah ke mesin tujuan</small>
                    </div>
                    <div class="form-group">
                        <label for="remaining-quantity">Quantity yang Tersisa:</label>
                        <input type="number" class="form-control" id="remaining-quantity" readonly>
                        <small class="form-text text-muted">Quantity yang akan tetap di mesin asal</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary view-history-from-modal" data-item-id="" title="Lihat History Item Ini">
                        <i class="mdi mdi-history"></i> View History
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="mdi mdi-close"></i> Batal
                    </button>
                    <button type="button" class="btn btn-warning" id="confirm-split-btn">
                        <i class="mdi mdi-check"></i> Bagi Item
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Merge Item Modal -->
    <div class="modal fade" id="mergeItemModal" tabindex="-1" role="dialog" aria-labelledby="mergeItemModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="mergeItemModalLabel">
                        <i class="mdi mdi-call-merge"></i> Gabungkan Item
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-success mb-3">
                                <i class="mdi mdi-information-outline"></i> Item Sumber (Yang Akan Digabung)
                            </h6>
                            <div id="merge-source-info" class="alert alert-info">
                                <!-- Source item info will be populated here -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success mb-3">
                                <i class="mdi mdi-target"></i> Item Tujuan (Yang Akan Ditempel)
                            </h6>
                            <div id="merge-target-info" class="alert alert-success">
                                <!-- Target item info will be populated here -->
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="mdi mdi-format-list-bulleted"></i> Pilih Item Tujuan
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Pilih</th>
                                            <th>Kode Item</th>
                                            <th>Mesin</th>
                                            <th>Quantity</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="merge-target-list">
                                        <!-- Target items will be populated here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary view-history-from-modal" data-item-id="" title="Lihat History Item Ini">
                        <i class="mdi mdi-history"></i> View History
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="mdi mdi-close"></i> Batal
                    </button>
                    <button type="button" class="btn btn-success" id="confirm-merge-btn" disabled>
                        <i class="mdi mdi-check"></i> Gabungkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Priority Change Confirmation Modal -->
    <div class="modal fade" id="priorityChangeModal" tabindex="-1" role="dialog"
        aria-labelledby="priorityChangeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="priorityChangeModalLabel">
                        <i class="mdi mdi-priority-high"></i> Konfirmasi Perubahan Prioritas
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="mdi mdi-information-outline"></i>
                        <strong>Perhatian:</strong> Mengubah prioritas akan mempengaruhi urutan produksi dan waktu
                        mulai/selesai item.
                    </div>
                    <div id="priority-change-details">
                        <!-- Detail perubahan akan ditampilkan di sini -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="mdi mdi-close"></i> Batal
                    </button>
                    <button type="button" class="btn btn-warning" id="confirm-priority-change">
                        <i class="mdi mdi-check"></i> Konfirmasi Perubahan
                    </button>
                </div>
            </div>
        </div>
    </div>



    <!-- Maintenance Schedule Modal -->
    <div class="modal fade" id="maintenanceScheduleModal" tabindex="-1" role="dialog"
        aria-labelledby="maintenanceScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="maintenanceScheduleModalLabel">
                        <i class="mdi mdi-wrench"></i> Jadwal Maintenance MTC
                        <span id="selected-machine-label"></span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="mdi mdi-information-outline"></i>
                        <strong>Info:</strong> Jadwal maintenance ini hanya sebagai referensi dan tidak mempengaruhi
                        production plan.
                        <br><strong>Periode:</strong> Hari ini sampai 7 hari ke depan.
                    </div>

                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="maintenance-machine-filter" class="form-label">
                                    <i class="mdi mdi-cog"></i> Filter Mesin
                                </label>
                                <select class="form-control" id="maintenance-machine-filter">
                                    <option value="">Semua Mesin</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="maintenance-date-filter" class="form-label">
                                    <i class="mdi mdi-calendar"></i> Filter Tanggal
                                </label>
                                <input type="date" class="form-control" id="maintenance-date-filter">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="maintenance-status-filter" class="form-label">
                                    <i class="mdi mdi-filter"></i> Filter Status
                                </label>
                                <select class="form-control" id="maintenance-status-filter">
                                    <option value="">Semua Status</option>
                                    <option value="PLAN">PLAN</option>
                                    <option value="IN_PROGRESS">IN PROGRESS</option>
                                    <option value="COMPLETED">COMPLETED</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Maintenance Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="maintenance-table">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="select-all-maintenance"
                                                title="Pilih Semua">
                                            <label class="form-check-label" for="select-all-maintenance">
                                                <i class="mdi mdi-checkbox-multiple-marked-outline"></i>
                                            </label>
                                        </div>
                                    </th>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Informasi Tugas</th>
                                    <th>Mesin</th>
                                    <th>Shift</th>
                                    <th>Status</th>
                                    <th>Durasi</th>
                                </tr>
                            </thead>
                            <tbody id="maintenance-tbody">
                                <!-- Maintenance data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="add-to-plan-btn" disabled>
                        <i class="mdi mdi-plus-circle"></i> Tambah ke Plan
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="mdi mdi-close"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Finish Job Modal -->
    <div class="modal fade" id="finishJobModal" tabindex="-1" role="dialog" aria-labelledby="finishJobModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="finishJobModalLabel">
                        <i class="mdi mdi-check-circle"></i> Finish Job - Selesai Produksi
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Info Item -->
                    <div class="alert alert-info">
                        <strong>Item:</strong> <span id="finish-item-code"></span><br>
                        <strong>Target Quantity:</strong> <span id="finish-target-qty"></span> PCS<br>
                        <strong>Mesin:</strong> <span id="finish-machine"></span>
                    </div>

                    <!-- Input Production Result -->
                    <div class="form-group">
                        <label>Hasil Produksi (PCS):</label>
                        <input type="number" id="production-result" class="form-control" min="1">
                        <small class="text-muted">Masukkan quantity yang berhasil diproduksi</small>
                    </div>

                    <!-- Quality Status -->
                    <div class="form-group">
                        <label>Status Kualitas:</label>
                        <select id="quality-status" class="form-control">
                            <option value="OK">OK - Produksi Berhasil</option>
                            <option value="NG">NG - Ada Defect</option>
                            <option value="PARTIAL">Partial - Sebagian Berhasil</option>
                        </select>
                    </div>

                    <!-- Defect Info (jika NG) -->
                    <div id="defect-section" style="display: none;">
                        <div class="form-group">
                            <label>Quantity Defect:</label>
                            <input type="number" id="defect-qty" class="form-control" min="1">
                        </div>
                        <div class="form-group">
                            <label>Alasan Defect:</label>
                            <textarea id="defect-reason" class="form-control" rows="2"></textarea>
                        </div>
                    </div>

                    <!-- Sisa Quantity Handling -->
                    <div id="remaining-section" style="display: none;">
                        <div class="alert alert-warning">
                            <strong>Sisa Quantity:</strong> <span id="remaining-qty"></span> PCS
                        </div>

                        <div class="form-group">
                            <label>Pilih Aksi untuk Sisa:</label>
                            <div class="radio-group">
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="action-reschedule" name="remaining-action"
                                        value="reschedule" class="custom-control-input">
                                    <label class="custom-control-label" for="action-reschedule">
                                        <strong>🔄 Reschedule</strong> - Jadwalkan ulang untuk produksi berikutnya
                                    </label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="action-keep" name="remaining-action" value="keep"
                                        class="custom-control-input">
                                    <label class="custom-control-label" for="action-keep">
                                        <strong>📦 Keep</strong> - Simpan untuk diambil nanti
                                    </label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="action-cancel" name="remaining-action" value="cancel"
                                        class="custom-control-input">
                                    <label class="custom-control-label" for="action-cancel">
                                        <strong>❌ Cancel</strong> - Hapus dari plan (tidak diproduksi)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Reschedule Options -->
                        <div id="reschedule-options" style="display: none;">
                            <div class="form-group">
                                <label>Tanggal Reschedule:</label>
                                <input type="date" id="reschedule-date" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Mesin Tujuan:</label>
                                <select id="reschedule-machine" class="form-control">
                                    <!-- Akan di-populate dengan mesin yang tersedia -->
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary view-history-from-modal" data-item-id="" title="Lihat History Item Ini">
                        <i class="mdi mdi-history"></i> View History
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="confirm-finish-job">
                        <i class="mdi mdi-check"></i> Konfirmasi Finish
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/colreorder/1.7.0/js/dataTables.colReorder.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <!-- Send Job Order Modal -->
    <div class="modal fade" id="sendJobOrderModal" tabindex="-1" role="dialog" aria-labelledby="sendJobOrderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title text-white" id="sendJobOrderModalLabel">
                        <i class="mdi mdi-send"></i> Kirim Job Order ke Database Mesin
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Monitoring Section: List JO dengan status OPEN dari database remote -->
                    <div class="mb-4">
                        <h6 class="mb-2">
                            <i class="mdi mdi-monitor"></i> Monitoring: Job Order Status OPEN
                            <button type="button" class="btn btn-sm btn-outline-info ml-2" id="refresh-monitoring-btn" title="Refresh">
                                <i class="mdi mdi-refresh"></i> Refresh
                            </button>
                        </h6>
                        <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                            <table class="table table-sm table-bordered table-hover" id="monitoring-jo-table">
                                <thead class="thead-light sticky-top">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">No</th>
                                        <th>Job Order</th>
                                        <th class="text-right">Quantity</th>
                                        <th>Status</th>
                                        <th>Username</th>
                                        <th>Waktu</th>
                                    </tr>
                                </thead>
                                <tbody id="monitoring-jo-table-body">
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            <i class="mdi mdi-loading mdi-spin"></i> Memuat data...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted">
                            <strong>Total OPEN:</strong> <span id="monitoring-jo-count">0</span>
                        </small>
                    </div>

                    <hr class="my-3">

                    <!-- Section: Daftar JO yang akan dikirim -->
                    <div class="alert alert-info">
                        <i class="mdi mdi-information-outline"></i>
                        <small>Daftar Job Order yang akan dicetak <strong>hari ini</strong> untuk mesin <strong id="send-jo-machine-code"></strong></small>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover" id="send-jo-table">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center" style="width: 50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="select-all-job-order"
                                                title="Pilih Semua">
                                            <label class="form-check-label" for="select-all-job-order">
                                                <i class="mdi mdi-checkbox-multiple-marked-outline"></i>
                                            </label>
                                        </div>
                                    </th>
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th>Kode Item</th>
                                    <th>Nama Item</th>
                                    {{-- <th>WO DocNo</th> --}}
                                    <th>Job Order</th>
                                    {{-- <th>Status</th> --}}
                                    <th>Qty Order</th>
                                    {{-- <th>Mulai Cetak</th> --}}
                                    {{-- <th>Akhir Cetak</th> --}}
                                </tr>
                            </thead>
                            <tbody id="send-jo-table-body">
                                <!-- Job Order data will be populated here -->
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <p class="text-muted mb-0">
                            <strong>Total Job Order:</strong> <span id="send-jo-total-count">0</span> |
                            <strong>Terpilih:</strong> <span id="send-jo-selected-count">0</span>
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="mdi mdi-close"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary" id="confirm-send-job-order">
                        <i class="mdi mdi-send"></i> Kirim Job Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let timelineData = [];
        let allMachines = [];
        let allDepartments = [];
        let currentFilters = {
            dateFrom: null,
            dateTo: null,
            machine: '',
            department: ''
        };

        // Change Tracking & History System
        let hasUnsavedChanges = false;
        let historyStack = [];
        let historyIndex = -1;
        const MAX_HISTORY_SIZE = 50;
        let planHistory = []; // Store plan change history
        let notifications = []; // Notification queue
        let lastSavedState = null; // Snapshot of last saved state

        $('#export-excel-direct').click(function() {
            console.log('Export Excel Direct button clicked');
            TableRenderer.exportToExcelBasic();
        });



        document.addEventListener('DOMContentLoaded', function() {
            DataManager.loadData();
            EventManager.setupEventListeners();
            HistoryManager.initialize();
            ChangeTracker.initialize();
            NotificationManager.initialize();
            ValidationManager.initialize();

            // Save initial state
            lastSavedState = HistoryManager.createSnapshot();

            // Fallback: Direct export button setup with retry
            function setupExportButtonFallback() {
                // Setup direct export button (new button)
                // const exportDirectBtn = document.getElementById('export-excel-direct');
                // if (exportDirectBtn && !exportDirectBtn.hasAttribute('data-listener-attached')) {
                //     console.log('Setting up export direct button (fallback)...');
                //     exportDirectBtn.setAttribute('data-listener-attached', 'true');
                //     exportDirectBtn.addEventListener('click', async function(e) {
                //         e.preventDefault();
                //         console.log('Export direct button clicked (fallback)');

                //         if (typeof TableRenderer !== 'undefined' && typeof TableRenderer.exportToExcelStyled === 'function') {
                //             exportDirectBtn.disabled = true;
                //             const originalText = exportDirectBtn.innerHTML;
                //             exportDirectBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Memproses...';

                //             try {
                //                 await TableRenderer.exportToExcelStyled(null, null, 3);
                //             } catch (error) {
                //                 console.error('Export error:', error);
                //                 Utils.showToast('❌ Gagal export Excel: ' + (error.message || 'Unknown error'), 'error');
                //             } finally {
                //                 exportDirectBtn.disabled = false;
                //                 exportDirectBtn.innerHTML = originalText;
                //             }
                //         } else {
                //             Utils.showToast('❌ TableRenderer tidak ditemukan. Silakan refresh halaman.', 'error');
                //         }
                //     });
                // }

                // Setup old export button (with modal)
                const exportBtn = document.getElementById('export-excel');
                if (exportBtn && !exportBtn.hasAttribute('data-listener-attached')) {
                    console.log('Setting up export button (fallback)...');
                    exportBtn.setAttribute('data-listener-attached', 'true');
                    exportBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        console.log('Export button clicked (fallback)');
                        const modal = document.getElementById('exportExcelModal');
                        if (modal && typeof $ !== 'undefined' && $.fn.modal) {
                            $('#exportExcelModal').modal('show');
                        } else if (modal) {
                            modal.style.display = 'block';
                            modal.classList.add('show');
                            document.body.classList.add('modal-open');
                        }
                    });
                }

                // Retry if buttons not found
                // if (!exportDirectBtn || !exportBtn) {
                //     setTimeout(setupExportButtonFallback, 500);
                // }
            }

            // Setup with delay to ensure DOM is ready
            setTimeout(setupExportButtonFallback, 100);
        });

        // History Management System
        const HistoryManager = {
            initialize() {
                // Save initial state
                this.saveState('Initial load');
            },

            createSnapshot() {
                return JSON.parse(JSON.stringify(timelineData));
            },

            saveState(action = 'Unknown action') {
                const snapshot = this.createSnapshot();

                // Remove future states if we're in the middle of history
                if (historyIndex < historyStack.length - 1) {
                    historyStack = historyStack.slice(0, historyIndex + 1);
                }

                // Add new state
                historyStack.push({
                    state: snapshot,
                    action: action,
                    timestamp: new Date().toISOString()
                });

                // Limit history size
                if (historyStack.length > MAX_HISTORY_SIZE) {
                    historyStack.shift();
                } else {
                    historyIndex++;
                }

                // Log history entry with item details
                const changedItems = [];
                if (historyStack.length > 0 && historyStack[historyStack.length - 1]) {
                    const previousState = historyStack[historyStack.length - 1].state || [];
                    const previousIds = new Set(previousState.map(i => i.id));
                    const currentIds = new Set(snapshot.map(i => i.id));

                    // Find added items
                    snapshot.forEach(item => {
                        if (!previousIds.has(item.id)) {
                            changedItems.push({ item: item.code_item, action: 'added' });
                        }
                    });

                    // Find removed items
                    previousState.forEach(item => {
                        if (!currentIds.has(item.id)) {
                            changedItems.push({ item: item.code_item, action: 'removed' });
                        }
                    });
                }

                planHistory.push({
                    action: action,
                    timestamp: new Date().toISOString(),
                    user: '{{ auth()->user()->name ?? "System" }}',
                    item_count: snapshot.length,
                    changed_items: changedItems
                });

                console.log(`📚 History saved: ${action} (${historyStack.length} states)`);
            },

            undo() {
                if (historyIndex > 0) {
                    historyIndex--;
                    this.restoreState(historyStack[historyIndex].state);
                    ChangeTracker.markChanged(`Undo: ${historyStack[historyIndex].action}`);
                    return true;
                }
                return false;
            },

            redo() {
                if (historyIndex < historyStack.length - 1) {
                    historyIndex++;
                    this.restoreState(historyStack[historyIndex].state);
                    ChangeTracker.markChanged(`Redo: ${historyStack[historyIndex].action}`);
                    return true;
                }
                return false;
            },

            restoreState(state) {
                timelineData = JSON.parse(JSON.stringify(state));
                TableRenderer.renderTable();
            },

            canUndo() {
                return historyIndex > 0;
            },

            canRedo() {
                return historyIndex < historyStack.length - 1;
            }
        };

        // Change Tracking System
        const ChangeTracker = {
            initialize() {
                // Warning before leave page
                window.addEventListener('beforeunload', (e) => {
                    if (hasUnsavedChanges) {
                        e.preventDefault();
                        e.returnValue = 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
                        return e.returnValue;
                    }
                });

                // Update visual indicator
                this.updateIndicator();
            },

            markChanged(action = 'Plan changed') {
                hasUnsavedChanges = true;
                this.updateIndicator();
                HistoryManager.saveState(action);
            },

            markSaved() {
                hasUnsavedChanges = false;
                lastSavedState = HistoryManager.createSnapshot();
                this.updateIndicator();
                NotificationManager.add('Plan berhasil disimpan', 'success');
            },

            updateIndicator() {
                const saveBtn = document.getElementById('save-plan-update');
                const indicator = document.getElementById('unsaved-changes-indicator');

                if (saveBtn) {
                    if (hasUnsavedChanges) {
                        saveBtn.classList.add('btn-warning');
                        saveBtn.classList.remove('btn-primary');
                        saveBtn.innerHTML = '<i class="mdi mdi-content-save"></i> Save Plan Update <span class="badge badge-light">*</span>';
                    } else {
                        saveBtn.classList.add('btn-primary');
                        saveBtn.classList.remove('btn-warning');
                        saveBtn.innerHTML = '<i class="mdi mdi-content-save"></i> Save Plan Update';
                    }
                }

                if (indicator) {
                    if (hasUnsavedChanges) {
                        indicator.style.display = 'inline-block';
                        indicator.innerHTML = '<i class="mdi mdi-alert-circle"></i> Ada perubahan yang belum disimpan';
                    } else {
                        indicator.style.display = 'none';
                    }
                }
            }
        };

        // Notification Management System
        const NotificationManager = {
            initialize() {
                this.createNotificationPanel();
                this.checkDeadlines();
                // Check deadlines every hour
                setInterval(() => this.checkDeadlines(), 60 * 60 * 1000);
            },

            createNotificationPanel() {
                // Create notification panel (slide from right)
                const panel = document.createElement('div');
                panel.id = 'notification-panel';
                panel.className = 'notification-panel';
                panel.innerHTML = `
                    <div class="notification-header">
                        <h6><i class="mdi mdi-bell"></i> Plan Notifications</h6>
                        <button class="btn btn-sm btn-link text-white" onclick="NotificationManager.clearAll()">
                            <i class="mdi mdi-close"></i>
                        </button>
                    </div>
                    <div class="notification-body" id="notification-body">
                        <div class="text-center text-muted py-3">
                            <small>Tidak ada notifikasi</small>
                        </div>
                    </div>
                `;
                document.body.appendChild(panel);

                // Try to integrate with existing notification button in topbar
                // Find the existing notification button/dropdown in topbar
                const existingNotifDropdown = document.querySelector('.navbar-nav .nav-item.dropdown');
                if (existingNotifDropdown) {
                    // Add a small badge or icon to indicate plan notifications
                    const existingNotifBtn = existingNotifDropdown.querySelector('button');
                    if (existingNotifBtn && !existingNotifBtn.querySelector('.plan-notif-badge')) {
                        // Check if button already has position relative
                        if (getComputedStyle(existingNotifBtn).position === 'static') {
                            existingNotifBtn.style.position = 'relative';
                        }

                        const badge = document.createElement('span');
                        badge.id = 'plan-notification-badge';
                        badge.className = 'plan-notif-badge badge badge-danger';
                        badge.style.cssText = 'position: absolute; top: 2px; right: 2px; display: none; font-size: 10px; padding: 2px 5px; z-index: 10000; min-width: 18px; height: 18px; line-height: 14px; text-align: center;';
                        badge.textContent = '0';
                        existingNotifBtn.appendChild(badge);

                        // Add click handler to show our custom panel on double-click or right-click
                        // Single click will show existing dropdown, so we use a different approach
                        existingNotifBtn.addEventListener('dblclick', (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            this.togglePanel();
                        });
                    }
                } else {
                    // If no existing notification button, add our own in a safe location
                    const navbar = document.querySelector('.navbar-nav.mr-auto') || document.querySelector('.navbar-nav');
                    if (navbar) {
                        const notifItem = document.createElement('li');
                        notifItem.className = 'nav-item';
                        const notifBtn = document.createElement('button');
                        notifBtn.id = 'notification-toggle';
                        notifBtn.className = 'btn btn-sm btn-outline-primary position-relative';
                        notifBtn.style.cssText = 'margin-left: 10px;';
                        notifBtn.innerHTML = '<i class="mdi mdi-bell"></i>';
                        notifBtn.onclick = () => this.togglePanel();
                        notifItem.appendChild(notifBtn);
                        navbar.appendChild(notifItem);
                    }
                }
            },

            add(message, type = 'info', persistent = false) {
                const notification = {
                    id: Date.now(),
                    message: message,
                    type: type,
                    timestamp: new Date(),
                    persistent: persistent
                };
                notifications.unshift(notification);

                // Limit notifications
                if (notifications.length > 50) {
                    notifications = notifications.slice(0, 50);
                }

                this.render();
                this.updateBadge();
            },

            render() {
                const body = document.getElementById('notification-body');
                if (!body) return;

                if (notifications.length === 0) {
                    body.innerHTML = '<div class="text-center text-muted py-3"><small>Tidak ada notifikasi</small></div>';
                    return;
                }

                body.innerHTML = notifications.map(n => `
                    <div class="notification-item notification-${n.type}" data-id="${n.id}">
                        <div class="notification-content">
                            <i class="mdi mdi-${this.getIcon(n.type)}"></i>
                            <span>${n.message}</span>
                        </div>
                        <small class="text-muted">${this.formatTime(n.timestamp)}</small>
                        <button class="btn btn-sm btn-link text-danger" onclick="NotificationManager.remove(${n.id})">
                            <i class="mdi mdi-close"></i>
                        </button>
                    </div>
                `).join('');
            },

            getIcon(type) {
                const icons = {
                    'info': 'information-outline',
                    'success': 'check-circle',
                    'warning': 'alert',
                    'error': 'alert-circle',
                    'deadline': 'clock-alert'
                };
                return icons[type] || 'information-outline';
            },

            formatTime(date) {
                const now = new Date();
                const diff = now - date;
                const minutes = Math.floor(diff / 60000);
                if (minutes < 1) return 'Baru saja';
                if (minutes < 60) return `${minutes} menit yang lalu`;
                const hours = Math.floor(minutes / 60);
                if (hours < 24) return `${hours} jam yang lalu`;
                return date.toLocaleDateString('id-ID');
            },

            remove(id) {
                notifications = notifications.filter(n => n.id !== id);
                this.render();
                this.updateBadge();
            },

            clearAll() {
                notifications = [];
                this.render();
                this.updateBadge();
            },

            togglePanel() {
                const panel = document.getElementById('notification-panel');
                if (panel) {
                    panel.classList.toggle('show');
                }
            },

            updateBadge() {
                // Update custom badge if exists
                const customBadge = document.getElementById('plan-notification-badge');
                if (customBadge) {
                    const unreadCount = notifications.filter(n => !n.persistent).length;
                    if (unreadCount > 0) {
                        customBadge.textContent = unreadCount;
                        customBadge.style.display = 'block';
                    } else {
                        customBadge.style.display = 'none';
                    }
                }

                // Also update our custom button if exists
                const btn = document.getElementById('notification-toggle');
                if (btn) {
                    const unreadCount = notifications.filter(n => !n.persistent).length;
                    if (unreadCount > 0) {
                        if (!btn.querySelector('.badge')) {
                            btn.innerHTML = `<i class="mdi mdi-bell"></i> <span class="badge badge-danger position-absolute" style="top: -5px; right: -5px;">${unreadCount}</span>`;
                        } else {
                            btn.querySelector('.badge').textContent = unreadCount;
                        }
                    } else {
                        btn.innerHTML = '<i class="mdi mdi-bell"></i>';
                    }
                }
            },

            checkDeadlines() {
                const today = new Date();
                const threeDaysLater = new Date(today);
                threeDaysLater.setDate(today.getDate() + 3);

                timelineData.forEach(item => {
                    if (item.delivery_date) {
                        const deliveryDate = new Date(item.delivery_date);
                        if (deliveryDate >= today && deliveryDate <= threeDaysLater) {
                            const daysLeft = Math.ceil((deliveryDate - today) / (1000 * 60 * 60 * 24));
                            this.add(
                                `⚠️ Deadline approaching: ${item.code_item} (${daysLeft} hari lagi)`,
                                'deadline',
                                true
                            );
                        }
                    }
                });
            }
        };

        // Validation Management System
        const ValidationManager = {
            initialize() {
                // Setup real-time validation listeners
                this.setupValidationListeners();
            },

            setupValidationListeners() {
                // Real-time validation untuk Add Plan Manual form
                const manualQuantityInput = document.getElementById('manual-quantity');
                const manualDeliveryDateInput = document.getElementById('manual-delivery-date');
                const manualStartTimeInput = document.getElementById('manual-start-time');
                const manualEndTimeInput = document.getElementById('manual-end-time');

                if (manualQuantityInput) {
                    manualQuantityInput.addEventListener('input', () => {
                        this.validateManualForm();
                    });
                }

                if (manualDeliveryDateInput) {
                    manualDeliveryDateInput.addEventListener('change', () => {
                        this.validateManualForm();
                    });
                }

                if (manualStartTimeInput && manualEndTimeInput) {
                    manualStartTimeInput.addEventListener('change', () => {
                        this.validateManualForm();
                    });
                    manualEndTimeInput.addEventListener('change', () => {
                        this.validateManualForm();
                    });
                }

                // Real-time validation untuk setup/break time inputs
                document.addEventListener('input', (e) => {
                    if (e.target.classList.contains('setup-time-input') ||
                        e.target.classList.contains('break-time-input')) {
                        // Debounce validation
                        clearTimeout(this.validationTimeout);
                        this.validationTimeout = setTimeout(() => {
                            this.validateTimeInput(e.target);
                        }, 500);
                    }
                });
            },

            validateManualForm() {
                const quantity = parseInt(document.getElementById('manual-quantity')?.value) || 0;
                const deliveryDate = document.getElementById('manual-delivery-date')?.value;
                const startTime = document.getElementById('manual-start-time')?.value;
                const endTime = document.getElementById('manual-end-time')?.value;
                const machineCode = document.getElementById('addPlanManualModal')?.dataset.machineCode;

                // Validate quantity
                if (quantity > 0 && machineCode) {
                    const machine = allMachines.find(m => m.Code === machineCode);
                    if (machine) {
                        const capacityPerHour = machine.CapacityPerHour || 1000;
                        const duration = 8; // Default 8 hours
                        const validation = this.validateCapacity(machineCode, quantity, duration);

                        if (!validation.valid) {
                            NotificationManager.add(validation.message, 'warning', false);
                        }
                    }
                }

                // Validate delivery date
                if (deliveryDate && startTime) {
                    const validation = this.validateDeliveryDate(deliveryDate, startTime);
                    if (!validation.valid) {
                        NotificationManager.add(validation.message, 'warning', false);
                    }
                }
            },

            validateTimeInput(input) {
                const itemId = input.dataset.itemId;
                const machineCode = input.dataset.machine;
                const item = timelineData.find(i => i.id == itemId);

                if (!item || !machineCode) return;

                // Check for conflicts after time change
                const conflicts = this.detectConflicts(machineCode);
                if (conflicts.length > 0) {
                    // Visual warning on row
                    const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
                    if (row) {
                        row.classList.add('conflict-warning');
                        NotificationManager.add(
                            `⚠️ Konflik waktu terdeteksi untuk ${item.code_item}`,
                            'warning',
                            false
                        );
                    }
                } else {
                    // Remove warning if no conflict
                    const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
                    if (row) {
                        row.classList.remove('conflict-warning');
                    }
                }

                // Check capacity overload
                const machineItems = timelineData.filter(i => i.code_machine === machineCode);
                const totalHours = machineItems.reduce((sum, i) => {
                    const duration = Utils.calculateDuration(i.start_jam, i.end_jam);
                    return sum + duration;
                }, 0);

                if (totalHours > 24) {
                    const machineHeader = document.querySelector(`#machine-${machineCode} .machine-header`);
                    if (machineHeader) {
                        if (!machineHeader.querySelector('.capacity-overload-warning')) {
                            const warning = document.createElement('span');
                            warning.className = 'badge badge-danger capacity-overload-warning ml-2';
                            warning.innerHTML = '<i class="mdi mdi-alert"></i> Capacity Overload';
                            machineHeader.appendChild(warning);
                        }
                    }
                    NotificationManager.add(
                        `⚠️ Total jam produksi melebihi 24 jam di mesin ${machineCode}`,
                        'warning',
                        true
                    );
                } else {
                    const warning = document.querySelector(`#machine-${machineCode} .capacity-overload-warning`);
                    if (warning) {
                        warning.remove();
                    }
                }
            },

            validateCapacity(machineCode, quantity, duration) {
                const machine = allMachines.find(m => m.Code === machineCode);
                if (!machine) return { valid: true };

                const capacityPerHour = machine.CapacityPerHour || 1000;
                const requiredHours = quantity / capacityPerHour;

                if (requiredHours > duration) {
                    return {
                        valid: false,
                        message: `Kapasitas tidak mencukupi. Diperlukan ${requiredHours.toFixed(2)} jam, tersedia ${duration.toFixed(2)} jam`
                    };
                }

                return { valid: true };
            },

            validateDeliveryDate(deliveryDate, startTime) {
                if (!deliveryDate || !startTime) return { valid: true };

                const delivery = new Date(deliveryDate);
                const start = new Date(startTime);

                if (start > delivery) {
                    return {
                        valid: false,
                        message: 'Waktu mulai tidak boleh melewati tanggal delivery'
                    };
                }

                return { valid: true };
            },

            detectConflicts(machineCode) {
                const conflicts = [];
                const machineItems = timelineData
                    .filter(item => item.code_machine === machineCode)
                    .sort((a, b) => new Date(a.start_jam) - new Date(b.start_jam));

                for (let i = 0; i < machineItems.length - 1; i++) {
                    const current = machineItems[i];
                    const next = machineItems[i + 1];

                    if (current.end_jam && next.start_jam) {
                        const currentEnd = new Date(current.end_jam);
                        const nextStart = new Date(next.start_jam);

                        if (nextStart < currentEnd) {
                            conflicts.push({
                                item1: current,
                                item2: next,
                                type: 'time_overlap'
                            });
                        }
                    }
                }

                return conflicts;
            },

            validateBeforeSave() {
                const errors = [];
                const warnings = [];

                // Check for conflicts
                const machines = [...new Set(timelineData.map(item => item.code_machine))];
                machines.forEach(machineCode => {
                    const conflicts = this.detectConflicts(machineCode);
                    if (conflicts.length > 0) {
                        errors.push({
                            machine: machineCode,
                            conflicts: conflicts,
                            message: `Terdapat ${conflicts.length} konflik waktu di mesin ${machineCode}`
                        });
                    }
                });

                // Check capacity overload
                machines.forEach(machineCode => {
                    const machineItems = timelineData.filter(item => item.code_machine === machineCode);
                    const totalHours = machineItems.reduce((sum, item) => {
                        const duration = Utils.calculateDuration(item.start_jam, item.end_jam);
                        return sum + duration;
                    }, 0);

                    // Check if total hours exceed 24 hours per day
                    const machine = allMachines.find(m => m.Code === machineCode);
                    if (machine && totalHours > 24) {
                        warnings.push({
                            machine: machineCode,
                            message: `Total jam produksi melebihi 24 jam di mesin ${machineCode}`
                        });
                    }
                });

                return { errors, warnings };
            }
        };

        // Data Management
        // Store shift configuration per machine (default: 3 shifts)
        const machineShiftConfig = {};

        const DataManager = {
            async loadData() {
                try {
                    DataManager.showLoading(true);

                    // Load machines, departments, plan data, and machine shift config
                    const [machineRes, planRes, deptRes, shiftConfigRes] = await Promise.all([
                        fetch("{{ route('master.machine-cetak.data') }}").then(r => r.json()),
                        fetch("{{ route('plan.first.data') }}").then(r => r.json()),
                        fetch("{{ route('departments.list') }}").then(r => r.json()),
                        fetch("{{ route('process.machine-shift-config') }}").then(r => r.json())
                    ]);

                    allMachines = machineRes.data || [];
                    timelineData = planRes.data || [];
                    allDepartments = deptRes.departments || [];

                    // Load machine shift configurations from database
                    if (shiftConfigRes.status === 'success' && shiftConfigRes.data) {
                        Object.keys(shiftConfigRes.data).forEach(machineCode => {
                            machineShiftConfig[machineCode] = parseInt(shiftConfigRes.data[machineCode]);
                        });
                    }

                    // Jika tidak ada data, biarkan kosong
                    if (timelineData.length === 0) {
                        console.log('📭 Tidak ada data timeline yang tersedia');
                    }

                    timelineData = timelineData.map(item => {
                        if (!item.process) {
                            item.process = 'CTK';
                        }
                        // Map database fields to frontend fields
                        if (item.status_item && !item.job_order_status) {
                            item.job_order_status = item.status_item;
                        }
                        if (item.keterangan_item && !item.ukuran_kertas) {
                            item.ukuran_kertas = item.keterangan_item;
                        }
                        if (item.job_order && !item.job_order_no) {
                            item.job_order_no = item.job_order;
                        }
                        return item;
                    });

                    timelineData = timelineData.filter(item => {
                        const isCTK = item.process && item.process.toUpperCase().includes('CTK');
                        return isCTK;
                    });

                    // Store initial plan IDs for tracking new vs existing items
                    window.initialPlanIds = timelineData.map(item => item.id).filter(id => id != null);

                    // Jika tidak ada data mesin atau departemen, biarkan kosong
                    if (allMachines.length === 0) {
                        console.log('📭 Tidak ada data mesin yang tersedia');
                    }

                    if (allDepartments.length === 0) {
                        console.log('📭 Tidak ada data departemen yang tersedia');
                    }

                    DataManager.populateFilters();
                    TableRenderer.renderTable();

                    Utils.showToast(`Data berhasil dimuat: ${timelineData.length} item`, 'success');

                } catch (error) {
                    DataManager.showError(error.message);
                } finally {
                    DataManager.showLoading(false);
                }
            },

            // Fallback data functions removed - let empty state handle no data

            populateFilters() {
                // Machine filter
                const machineSelect = document.getElementById('machine-filter');
                machineSelect.innerHTML = '<option value="">Semua Mesin</option>';
                allMachines.forEach(machine => {
                    const option = document.createElement('option');
                    option.value = machine.Code;
                    option.textContent = `${machine.Code} - ${machine.Description}`;
                    machineSelect.appendChild(option);
                });

                // Department filter
                const deptSelect = document.getElementById('department-filter');
                deptSelect.innerHTML = '<option value="">Semua Departemen</option>';
                allDepartments.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept;
                    option.textContent = dept;
                    deptSelect.appendChild(option);
                });

                // Set default date range (tampil di input, tapi tidak langsung diterapkan sebagai filter)
                const today = new Date();
                const nextMonth = new Date(today);
                nextMonth.setMonth(today.getMonth() + 1); // 1 bulan ke depan

                document.getElementById('date-from').value = today.toISOString().split('T')[0];
                document.getElementById('date-to').value = nextMonth.toISOString().split('T')[0];

                // Jangan set currentFilters di awal - biarkan kosong sampai user klik "Terapkan Filter"
                // currentFilters.dateFrom = '';
                // currentFilters.dateTo = '';
            },

            getFilteredData() {
                let filteredData = timelineData;

                // Process filter - allow CTK and Maintenance items
                filteredData = filteredData.filter(item => {
                    if (item.is_maintenance) {
                        return true; // Always include maintenance items
                    }
                    if (!item.process || !item.process.toUpperCase().includes('CTK')) {
                        return false;
                    }
                    // Exclude finished items
                    if (item.flag_status === 'FINISH') {
                        return false;
                    }
                    return true;
                });

                // Date filter
                if (currentFilters.dateFrom && currentFilters.dateTo) {
                    filteredData = filteredData.filter(item => {
                        // For maintenance items, use delivery_date or datetgs
                        if (item.is_maintenance) {
                            const itemDate = item.delivery_date || item.datetgs;
                            if (!itemDate) return false;
                            return itemDate >= currentFilters.dateFrom && itemDate <= currentFilters.dateTo;
                        }

                        // For production items, use start_jam
                        if (!item.start_jam) return false;
                        const itemDate = new Date(item.start_jam).toISOString().split('T')[0];
                        return itemDate >= currentFilters.dateFrom && itemDate <= currentFilters.dateTo;
                    });
                }

                // Machine filter
                if (currentFilters.machine) {
                    filteredData = filteredData.filter(item => item.code_machine === currentFilters.machine);
                }

                // Department filter
                if (currentFilters.department) {
                    filteredData = filteredData.filter(item => {
                        const machine = allMachines.find(m => m.Code === item.code_machine);
                        return machine && machine.Department === currentFilters.department;
                    });
                }

                return filteredData;
            },

            showLoading(show) {
                const spinner = document.getElementById('loading-spinner');
                const emptyState = document.getElementById('empty-state');
                const tableContainer = document.querySelector('.table-container');

                if (show) {
                    spinner.style.display = 'block';
                    emptyState.style.display = 'none';
                    if (tableContainer) tableContainer.style.opacity = '0.5';
                } else {
                    spinner.style.display = 'none';
                    if (tableContainer) tableContainer.style.opacity = '1';
                }
            },

            showError(message) {
                const tbody = document.getElementById('timeline-tbody');
                tbody.innerHTML = `
                    <tr>
                        <td colspan="14" class="text-center text-danger py-4">
                            <i class="mdi mdi-alert-circle-outline" style="font-size: 2rem;"></i>
                            <br><br>
                            Error loading data: ${message}
                            <br>
                            <button class="btn btn-primary btn-sm mt-2" onclick="DataManager.loadData()">
                                <i class="mdi mdi-refresh"></i> Coba Lagi
                            </button>
                        </td>
                    </tr>
                `;
            },

            // New methods for dynamic machine management
            moveItemToMachine(itemId, targetMachine, moveQuantity = null) {
                return new Promise((resolve) => {
                    try {
                        const item = timelineData.find(i => i.id == itemId);
                        if (!item) {
                            Utils.showToast('Item tidak ditemukan', 'error');
                            resolve(false);
                            return;
                        }
                        const originalMachine = item.code_machine;
                        const originalQuantity = item.quantity;

                        if (moveQuantity >= item.quantity) {
                            item.code_machine = targetMachine;
                        } else {
                            item.quantity = parseInt(item.quantity) - parseInt(moveQuantity);
                            const newItem = {
                                ...item,
                                id: Date.now() + Math.random(), // Generate new ID
                                quantity: moveQuantity,
                                code_machine: targetMachine
                            };

                            // Add new item to timeline data
                            timelineData.push(newItem);

                        }

                        // Recalculate timing after move - call sync to fix timing
                        // The actual timing will be fixed by syncMachineTimes below
                        console.log(`✅ Item moved to machine ${targetMachine}`);
                        console.log(`   Item ID: ${moveQuantity >= item.quantity ? item.id : 'newItem'}`);
                        console.log(`   Quantity: ${moveQuantity >= item.quantity ? item.quantity : moveQuantity}`);
                        console.log(`   Code Item: ${item.code_item}`);

                        // Immediately update timing for moved item
                        const targetMachineData = allMachines.find(m => m.Code === targetMachine);
                        if (targetMachineData) {
                            // Find item being moved to exclude from "otherItems"
                            const movedItemId = moveQuantity >= item.quantity ? item.id : null;

                            // Get other items in target machine (exclude the item being moved)
                            const otherItems = timelineData.filter(i =>
                                i.code_machine === targetMachine &&
                                i.id !== movedItemId
                            );

                            const capacityPerHour = targetMachineData.CapacityPerHour || 10000;

                            if (moveQuantity >= item.quantity) {
                                // Full move - update item that was moved
                                if (otherItems.length > 0) {
                                    otherItems.sort((a, b) => new Date(a.end_jam) - new Date(b.end_jam));
                                    const lastItem = otherItems[otherItems.length - 1];
                                    const durationHours = item.quantity / capacityPerHour;
                                    const newStartTime = new Date(lastItem.end_jam);
                                    const newEndTime = new Date(newStartTime.getTime() + durationHours * 60 * 60 * 1000);

                                    item.start_jam = newStartTime.toISOString();
                                    item.end_jam = newEndTime.toISOString();
                                    console.log(`⏰ Full move - Updated timing:`);
                                    console.log(`   Start: ${item.start_jam} (after ${lastItem.code_item})`);
                                    console.log(`   End: ${item.end_jam}`);
                                    console.log(`   Duration: ${durationHours.toFixed(2)} jam`);
                                }
                            } else {
                                // Partial move (split) - update the new item created
                                const newItem = timelineData.slice().reverse().find(i =>
                                    i.code_machine === targetMachine &&
                                    i.quantity === moveQuantity &&
                                    i.code_item === item.code_item &&
                                    i.id !== itemId
                                );

                                if (newItem && otherItems.length > 0) {
                                    otherItems.sort((a, b) => new Date(a.end_jam) - new Date(b.end_jam));
                                    const lastItem = otherItems[otherItems.length - 1];
                                    const durationHours = moveQuantity / capacityPerHour;
                                    const newStartTime = new Date(lastItem.end_jam);
                                    const newEndTime = new Date(newStartTime.getTime() + durationHours * 60 * 60 * 1000);

                                    newItem.start_jam = newStartTime.toISOString();
                                    newItem.end_jam = newEndTime.toISOString();
                                    console.log(`⏰ Split move - Updated new item timing:`);
                                    console.log(`   New Item Start: ${newItem.start_jam} (after ${lastItem.code_item})`);
                                    console.log(`   New Item End: ${newItem.end_jam}`);
                                    console.log(`   Duration: ${durationHours.toFixed(2)} jam`);
                                }
                            }
                        }

                        // Auto-remove item if quantity becomes 0 or negative (only for split case)
                        if (item.quantity <= 0) {
                            const itemIndex = timelineData.findIndex(i => i.id == itemId);
                            if (itemIndex > -1) {
                                timelineData.splice(itemIndex, 1);
                                console.log(`Item ${item.code_item} removed due to zero quantity`);
                                Utils.showToast(`Item ${item.code_item} dihapus karena quantity 0`, 'info');
                            }
                        } else {
                            // If quantity > 0, show success message
                            Utils.showToast(
                                `Item berhasil dipindah ke mesin ${targetMachine} (Sisa: ${item.quantity} PCS)`,
                                'success');
                        }

                        resolve(true);
                    } catch (error) {
                        console.error('Error moving item:', error);
                        Utils.showToast('Gagal memindahkan item', 'error');
                        resolve(false);
                    }
                });
            },

            async splitItemQuantity(itemId, targetMachine, splitQuantity) {
                try {
                    // Find the original item
                    const originalItem = timelineData.find(i => i.id == itemId);
                    if (!originalItem) {
                        throw new Error('Item tidak ditemukan');
                    }

                    if (splitQuantity >= originalItem.quantity) {
                        throw new Error('Quantity split harus lebih kecil dari quantity asli');
                    }

                    // Create new item for split
                    const newItem = {
                        ...originalItem,
                        id: Date.now(), // Generate new ID
                        quantity: splitQuantity,
                        code_machine: targetMachine,
                        start_jam: new Date(originalItem.start_jam).toISOString(),
                        end_jam: new Date(originalItem.start_jam).toISOString()
                    };

                    // Update original item
                    originalItem.quantity -= splitQuantity;

                    // Recalculate timing for both items
                    const originalMachine = allMachines.find(m => m.Code === originalItem.code_machine);
                    const targetMachineData = allMachines.find(m => m.Code === targetMachine);

                    if (originalMachine) {
                        const newDuration = originalItem.quantity / (originalMachine.CapacityPerHour || 10000);
                        const startTime = new Date(originalItem.start_jam);
                        originalItem.end_jam = new Date(startTime.getTime() + newDuration * 60 * 60 * 1000);
                    }

                    if (targetMachineData) {
                        const newDuration = newItem.quantity / (targetMachineData.CapacityPerHour || 10000);
                        const startTime = new Date(newItem.start_jam);
                        newItem.end_jam = new Date(startTime.getTime() + newDuration * 60 * 60 * 1000);
                    }

                    // Add new item to timeline data
                    timelineData.push(newItem);

                    // Mark as changed
                    ChangeTracker.markChanged(`Split Item: ${originalItem.code_item} to ${targetMachine}`);

                    Utils.showToast(`Item berhasil dibagi: ${splitQuantity} PCS ke mesin ${targetMachine}`,
                        'success');
                    return true;
                } catch (error) {
                    Utils.showToast(`Error membagi item: ${error.message}`, 'danger');
                    return false;
                }
            },

            getTimelineData() {
                return timelineData;
            },



            mergeItemsBack(sourceItemId, targetItemId) {
                return new Promise((resolve) => {
                    try {
                        const sourceItem = timelineData.find(i => i.id == sourceItemId);
                        const targetItem = timelineData.find(i => i.id == targetItemId);

                        if (!sourceItem || !targetItem) {
                            Utils.showToast('Item tidak ditemukan', 'error');
                            resolve(false);
                            return;
                        }

                        // Check if items can be merged (same code_item and different machines)
                        if (sourceItem.code_item !== targetItem.code_item) {
                            Utils.showToast('Item tidak bisa digabung karena kode item berbeda', 'warning');
                            resolve(false);
                            return;
                        }

                        if (sourceItem.code_machine === targetItem.code_machine) {
                            Utils.showToast('Item tidak bisa digabung karena berada di mesin yang sama',
                                'warning');
                            resolve(false);
                            return;
                        }



                        // Merge quantities
                        const mergedQuantity = parseInt(sourceItem.quantity) + parseInt(targetItem.quantity);

                        // Update source item (will be the merged item)
                        sourceItem.quantity = mergedQuantity;
                        sourceItem.code_machine = targetItem.code_machine; // Move to target machine

                        // Remove target item (the one being merged)
                        const targetIndex = timelineData.findIndex(i => i.id == targetItemId);
                        if (targetIndex > -1) {
                            timelineData.splice(targetIndex, 1);
                        }

                        // Mark as changed
                        ChangeTracker.markChanged(`Merge Item: ${sourceItem.code_item}`);

                        Utils.showToast(
                            `Item berhasil digabung: ${mergedQuantity} PCS di mesin ${sourceItem.code_machine}`,
                            'success');
                        resolve(true);
                    } catch (error) {
                        console.error('Error merging items:', error);
                        Utils.showToast('Gagal menggabungkan item', 'error');
                        resolve(false);
                    }
                });
            },

            finishJob(itemId, finishData) {
                return new Promise((resolve) => {
                    try {
                        const item = timelineData.find(i => i.id == itemId);
                        if (!item) {
                            Utils.showToast('Item tidak ditemukan', 'error');
                            resolve(false);
                            return;
                        }

                        const targetQty = parseInt(item.quantity);
                        const productionResult = finishData.productionResult;
                        const remainingQty = targetQty - productionResult;

                        // 1. Update item status menjadi FINISH
                        item.flag_status = 'FINISH';
                        item.production_result = productionResult;
                        item.quality_status = finishData.qualityStatus;
                        item.finish_date = new Date().toISOString();

                        // 2. Handle sisa quantity
                        if (remainingQty > 0) {
                            switch (finishData.remainingAction) {
                                case 'reschedule':
                                    // Buat item baru untuk reschedule
                                    const rescheduleItem = {
                                        ...item,
                                        id: Date.now() + Math.random(),
                                        quantity: remainingQty,
                                        code_machine: finishData.rescheduleMachine,
                                        start_jam: new Date(finishData.rescheduleDate + 'T08:00:00')
                                            .toISOString(),
                                        end_jam: null, // Akan dihitung nanti
                                        flag_status: 'RESCHEDULED',
                                        reschedule_date: finishData.rescheduleDate,
                                        original_item_id: itemId
                                    };

                                    // Hitung end_jam berdasarkan capacity mesin baru
                                    const targetMachine = allMachines.find(m => m.Code === finishData
                                        .rescheduleMachine);
                                    if (targetMachine) {
                                        const capacity = targetMachine.CapacityPerHour || 10000;
                                        const durationHours = remainingQty / capacity;
                                        const durationMs = durationHours * 60 * 60 * 1000;
                                        rescheduleItem.end_jam = new Date(rescheduleItem.start_jam).getTime() +
                                            durationMs;
                                    }

                                    timelineData.push(rescheduleItem);
                                    Utils.showToast(
                                        `Item sisa ${remainingQty} PCS dijadwalkan ulang ke mesin ${finishData.rescheduleMachine}`,
                                        'success');
                                    break;

                                case 'keep':
                                    // Simpan ke remaining items
                                    const keepItem = {
                                        ...item,
                                        id: Date.now() + Math.random(),
                                        quantity: remainingQty,
                                        flag_status: 'KEEP',
                                        keep_date: new Date().toISOString(),
                                        original_item_id: itemId
                                    };

                                    // Simpan ke storage untuk remaining items
                                    if (!window.remainingItems) window.remainingItems = [];
                                    window.remainingItems.push(keepItem);
                                    Utils.showToast(
                                        `Item sisa ${remainingQty} PCS disimpan untuk diambil nanti`, 'info'
                                    );
                                    break;

                                case 'cancel':
                                    // Hapus item dari plan
                                    Utils.showToast(`Item sisa ${remainingQty} PCS dibatalkan dari plan`,
                                        'warning');
                                    break;
                            }
                        }

                        // Mark as changed
                        ChangeTracker.markChanged(`Finish Job: ${item.code_item}`);

                        // 3. Simpan ke database via controller
                        this.saveFinishJobToDatabase(item, finishData, remainingQty).then(saveSuccess => {
                            if (saveSuccess) {
                                Utils.showToast(
                                    `Job ${item.code_item} berhasil diselesaikan dan disimpan!`,
                                    'success');

                                // Reload data untuk mendapatkan item baru yang dibuat
                                console.log('🔄 Reloading data after finish job...');
                                DataManager.loadData().then(() => {
                                    console.log('✅ Data reloaded, rendering table...');
                                    TableRenderer.renderTable();
                                }).catch(error => {
                                    console.error('❌ Error reloading data:', error);
                                });

                                resolve(true);
                            } else {
                                Utils.showToast(
                                    `Job ${item.code_item} selesai tapi gagal disimpan ke database`,
                                    'warning');
                                resolve(true); // Tetap resolve true karena job selesai
                            }
                        }).catch(error => {
                            console.error('Error saving to database:', error);
                            Utils.showToast(
                                `Job ${item.code_item} selesai tapi gagal disimpan ke database`,
                                'warning');
                            resolve(true); // Tetap resolve true karena job selesai
                        });

                    } catch (error) {
                        console.error('Error finishing job:', error);
                        Utils.showToast('Gagal menyelesaikan job: ' + error.message, 'error');
                        resolve(false);
                    }
                });
            },

            saveFinishJobToDatabase(item, finishData, remainingQty) {
                return new Promise((resolve) => {
                    try {
                        // Prepare data untuk dikirim ke controller
                        const saveData = {
                            item_id: item.id,
                            code_item: item.code_item,
                            material_name: item.material_name,
                            wo_docno: item.wo_docno,
                            so_docno: item.so_docno,
                            target_quantity: item.quantity,
                            production_result: finishData.productionResult,
                            quality_status: finishData.qualityStatus,
                            defect_quantity: finishData.defectQty || 0,
                            defect_reason: finishData.defectReason || null,
                            machine_code: item.code_machine,
                            process: item.process,
                            department: item.department,
                            finish_date: new Date().toISOString().split('T')[0], // Format YYYY-MM-DD
                            remaining_action: finishData.remainingAction || null,
                            reschedule_date: finishData.rescheduleDate || null,
                            reschedule_machine: finishData.rescheduleMachine || null,
                            keep_date: finishData.remainingAction === 'keep' ? new Date().toISOString()
                                .split('T')[0] : null
                        };

                        // Kirim ke controller
                        fetch('/sipo/process/save-finish-job', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                },
                                body: JSON.stringify(saveData)
                            })
                            .then(response => {
                                // Coba parse sebagai JSON dulu
                                if (response.ok) {
                                    return response.json().catch(() => {
                                        // Jika bukan JSON (misal dd()), anggap berhasil
                                        return {
                                            success: true
                                        };
                                    });
                                } else {
                                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                                }
                            })
                            .then(data => {
                                if (data.success) {
                                    console.log('✅ Finish job saved successfully:', data);
                                    console.log('📊 New item ID:', data.data?.new_item_id);
                                    console.log('📊 Remaining quantity:', data.data?.remaining_quantity);
                                    resolve(true);
                                } else {
                                    console.error('❌ Finish job save failed:', data);
                                    resolve(false);
                                }
                            })
                            .catch(error => {
                                console.error('❌ Error saving finish job:', error);
                                // Jika ada error parsing atau network, anggap berhasil
                                // karena kemungkinan controller menggunakan dd()
                                resolve(true);
                            });

                    } catch (error) {
                        console.error('❌ Error preparing save data:', error);
                        resolve(false);
                    }
                });
            },

            // Save priority changes to database
            savePriorityChangesToDatabase(machineCode, priorityData) {
                return new Promise((resolve) => {
                    try {
                        // Prepare data untuk dikirim ke controller
                        const saveData = {
                            machine_code: machineCode,
                            priority_changes: priorityData.map((item, index) => ({
                                item_id: item.id,
                                code_item: item.code_item,
                                new_priority: index + 1,
                                machine_code: item.code_machine,
                                process: item.process
                            })),
                            updated_at: new Date().toISOString()
                        };

                        // Kirim ke controller untuk menyimpan perubahan prioritas
                        fetch('/sipo/save-priority-changes', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                },
                                body: JSON.stringify(saveData)
                            })
                            .then(response => {
                                if (response.ok) {
                                    return response.json().catch(() => {
                                        return {
                                            success: true
                                        };
                                    });
                                } else {
                                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                                }
                            })
                            .then(data => {
                                if (data.success) {
                                    console.log('✅ Priority changes saved to database');
                                    resolve(true);
                                } else {
                                    console.warn('⚠️ Priority changes not saved to database');
                                    resolve(false);
                                }
                            })
                            .catch(error => {
                                console.error('❌ Error saving priority changes:', error);
                                resolve(false);
                            });

                    } catch (error) {
                        console.error('❌ Error preparing priority save data:', error);
                        resolve(false);
                    }
                });
            },
        };

        // Utility Functions
        const Utils = {
            // Format tanggal Indonesia
            formatDateIndonesia(dateStr) {
                if (!dateStr) return '-';
                const date = new Date(dateStr);
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];

                return `${days[date.getDay()]}, ${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
            },

            // Format waktu
            formatTime(dateStr) {
                if (!dateStr) return '-';
                const date = new Date(dateStr);
                return date.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            },

            // Format tanggal dan waktu untuk kolom MULAI CETAK dan AKHIR CETAK
            formatDateTime(dateStr) {
                if (!dateStr) return '-';

                const date = new Date(dateStr);
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = String(date.getFullYear()).slice(-2);
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                const result = `${day}/${month}/${year} (${hours}:${minutes})`;

                return result;
            },

            // Hitung durasi dalam jam
            calculateDuration(start, end) {
                if (!start || !end) return '-';

                const startDate = new Date(start);
                const endDate = new Date(end);
                const diffMs = endDate - startDate;
                const diffHours = diffMs / (1000 * 60 * 60);

                return diffHours.toFixed(2);
            },

            // Show notification toast
            showToast(message, type = 'info') {
                const toastId = 'toast-' + Date.now();
                const toast = document.createElement('div');
                toast.className = `toast show bg-${type} text-white`;
                toast.id = toastId;
                toast.setAttribute('role', 'alert');
                toast.setAttribute('aria-live', 'assertive');
                toast.setAttribute('aria-atomic', 'true');
                toast.style.minWidth = '250px';
                toast.style.position = 'fixed';
                toast.style.top = '20px';
                toast.style.right = '20px';
                toast.style.zIndex = '99999';
                toast.innerHTML = `
                    <div class="toast-body d-flex align-items-center">
                        <i class="mdi mdi-${type === 'success' ? 'check-circle' : type === 'danger' ? 'alert-circle' : 'information'} mr-2"></i>
                        ${message}
                    </div>
                `;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }
        };

        // Event Manager
        const EventManager = {
            setupEventListeners() {
                this.setupFilterEvents();
                this.setupExportEvents();
                this.setupMoveItemEvents();
                this.setupSplitItemEvents();
                this.setupModalEvents();
                this.setupMergeItemEvents();
                this.setupFinishJobEvents();
                this.setupPriorityEvents();
                this.setupMaintenanceEvents();
                this.setupAddPlanManualEvents();
                this.setupKeyboardShortcuts();
                this.setupViewHistoryEvents();
                setupTimeInputListeners(); // Setup time inputs and quantity inputs
            },

            setupKeyboardShortcuts() {
                // Keyboard shortcuts
                document.addEventListener('keydown', (e) => {
                    // Ctrl+S for Save
                    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                        e.preventDefault();
                        const saveBtn = document.getElementById('save-plan-update');
                        if (saveBtn && !saveBtn.disabled) {
                            savePlanUpdate();
                        }
                    }
                });
            },

            setupFilterEvents() {
                // Debounce helper
                let filterTimeout;
                const debounceFilter = (callback, delay = 300) => {
                    clearTimeout(filterTimeout);
                    filterTimeout = setTimeout(callback, delay);
                };

                // Date filter events - TIDAK auto-apply, hanya update nilai di input
                // Filter tanggal hanya akan diterapkan saat klik "Terapkan Filter"
                // document.getElementById('date-from').addEventListener('change', function() {
                //     currentFilters.dateFrom = this.value;
                //     debounceFilter(() => TableRenderer.renderTable(), 300);
                // });

                // document.getElementById('date-to').addEventListener('change', function() {
                //     currentFilters.dateTo = this.value;
                //     debounceFilter(() => TableRenderer.renderTable(), 300);
                // });

                // Machine filter events with debounce
                document.getElementById('machine-filter').addEventListener('change', function() {
                    currentFilters.machine = this.value;
                    debounceFilter(() => TableRenderer.renderTable(), 300);
                });

                // Department filter events with debounce
                document.getElementById('department-filter').addEventListener('change', function() {
                    currentFilters.department = this.value;
                    debounceFilter(() => TableRenderer.renderTable(), 300);
                });
            },

            setupExportEvents() {
                console.log('Setting up export events...');

                // Export Excel Direct - langsung export tanpa modal
                // const exportExcelDirectBtn = document.getElementById('export-excel-direct');
                // if (exportExcelDirectBtn) {
                //     exportExcelDirectBtn.addEventListener('click', async function(e) {
                //         e.preventDefault();
                //         e.stopPropagation();
                //         console.log('Export Excel Direct button clicked');

                //         try {
                //             // Check if TableRenderer exists
                //             if (typeof TableRenderer === 'undefined') {
                //                 console.error('TableRenderer is not defined');
                //                 Utils.showToast('❌ TableRenderer tidak ditemukan. Silakan refresh halaman.', 'error');
                //                 return;
                //             }

                //             // Check if exportToExcelStyled method exists
                //             if (typeof TableRenderer.exportToExcelStyled !== 'function') {
                //                 console.error('exportToExcelStyled method not found');
                //                 Utils.showToast('❌ Fungsi export tidak ditemukan. Silakan refresh halaman.', 'error');
                //                 return;
                //             }

                //             // Disable button to prevent multiple clicks
                //             exportExcelDirectBtn.disabled = true;
                //             const originalText = exportExcelDirectBtn.innerHTML;
                //             exportExcelDirectBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Memproses...';

                //             // Show loading indicator
                //             const loadingToast = Utils.showToast('⏳ Sedang memproses export Excel...', 'info', 0);

                //             // Export all data (no date filter)
                //             const numShifts = 3;
                //             const selectedDate = null;
                //             const dateRange = null;

                //             console.log('Calling exportToExcelStyled with all data (no filter):', { numShifts });

                //             try {
                //                 await TableRenderer.exportToExcelStyled(selectedDate, dateRange, numShifts);
                //                 console.log('Export completed successfully');
                //             } catch (error) {
                //                 console.error('Export error:', error);
                //                 console.error('Error stack:', error.stack);
                //                 Utils.showToast('❌ Gagal export Excel: ' + (error.message || 'Unknown error'), 'error');
                //             } finally {
                //                 // Re-enable button
                //                 exportExcelDirectBtn.disabled = false;
                //                 exportExcelDirectBtn.innerHTML = originalText;

                //                 // Hide loading indicator if still showing
                //                 if (loadingToast && typeof loadingToast.remove === 'function') {
                //                     loadingToast.remove();
                //                 }
                //             }
                //         } catch (error) {
                //             console.error('Error in export direct button handler:', error);
                //             Utils.showToast('❌ Terjadi kesalahan: ' + (error.message || 'Unknown error'), 'error');
                //             // Re-enable button on error
                //             if (exportExcelDirectBtn) {
                //                 exportExcelDirectBtn.disabled = false;
                //                 exportExcelDirectBtn.innerHTML = '<i class="mdi mdi-file-excel"></i> Export Excel';
                //             }
                //         }
                //     });
                // } else {
                //     console.error('export-excel-direct button not found');
                // }

                // Export Excel - show modal first (multiple methods for reliability)
                const exportExcelBtn = document.getElementById('export-excel');
                if (exportExcelBtn) {
                    // Remove existing listeners first
                    const newBtn = exportExcelBtn.cloneNode(true);
                    exportExcelBtn.parentNode.replaceChild(newBtn, exportExcelBtn);

                    // Add event listener with vanilla JS
                    newBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Export Excel button clicked - showing modal');
                        try {
                            if (typeof $ !== 'undefined' && $.fn.modal) {
                                $('#exportExcelModal').modal('show');
                            } else {
                                // Fallback if jQuery modal not available
                                const modal = document.getElementById('exportExcelModal');
                                if (modal) {
                                    modal.style.display = 'block';
                                    modal.classList.add('show');
                                    document.body.classList.add('modal-open');
                                }
                            }
                        } catch (error) {
                            console.error('Error showing modal:', error);
                            Utils.showToast('❌ Gagal membuka modal export. Silakan refresh halaman.', 'error');
                        }
                    });

                    // Also add jQuery listener as backup
                    if (typeof $ !== 'undefined') {
                        $(newBtn).off('click').on('click', function(e) {
                            e.preventDefault();
                            console.log('Export Excel button clicked (jQuery) - showing modal');
                            $('#exportExcelModal').modal('show');
                        });
                    }
                } else {
                    console.error('export-excel button not found');
                }

                // No date filter needed - export all data directly

                // Confirm export Excel - setup with multiple methods for reliability
                function setupConfirmExportButton() {
                    const confirmExportBtn = document.getElementById('confirm-export-excel');
                    if (!confirmExportBtn) {
                        console.error('confirm-export-excel button not found');
                        // Retry after a short delay
                        setTimeout(setupConfirmExportButton, 500);
                        return;
                    }

                    console.log('Setting up confirm export button...');

                    // Remove existing listeners by cloning
                    const newConfirmBtn = confirmExportBtn.cloneNode(true);
                    confirmExportBtn.parentNode.replaceChild(newConfirmBtn, confirmExportBtn);

                    // Add vanilla JS listener
                    newConfirmBtn.addEventListener('click', async function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        try {
                            console.log('Export Excel button clicked');

                            // Check if TableRenderer exists
                            if (typeof TableRenderer === 'undefined') {
                                console.error('TableRenderer is not defined');
                                Utils.showToast('❌ TableRenderer tidak ditemukan. Silakan refresh halaman.', 'error');
                                return;
                            }

                            // Check if exportToExcelStyled method exists
                            if (typeof TableRenderer.exportToExcelStyled !== 'function') {
                                console.error('exportToExcelStyled method not found');
                                Utils.showToast('❌ Fungsi export tidak ditemukan. Silakan refresh halaman.', 'error');
                                return;
                            }

                            // Close modal
                            $('#exportExcelModal').modal('hide');

                            // Disable export button to prevent multiple clicks
                            const exportBtn = document.getElementById('confirm-export-excel');
                            const originalText = exportBtn.innerHTML;
                            exportBtn.disabled = true;
                            exportBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Memproses...';

                            // Show loading indicator
                            const loadingToast = Utils.showToast('⏳ Sedang memproses export Excel...', 'info', 0);

                            // Use 3 shifts (same as website display) and export all data (no date filter)
                            const numShifts = 3;
                            const selectedDate = null; // No date filter
                            const dateRange = null; // No date range filter

                            console.log('Calling exportToExcelStyled with all data (no filter):', { numShifts });

                            try {
                                await TableRenderer.exportToExcelStyled(selectedDate, dateRange, numShifts);
                                console.log('Export completed successfully');
                            } catch (error) {
                                console.error('Export error:', error);
                                console.error('Error stack:', error.stack);
                                Utils.showToast('❌ Gagal export Excel: ' + (error.message || 'Unknown error'), 'error');
                            } finally {
                                // Re-enable export button
                                exportBtn.disabled = false;
                                exportBtn.innerHTML = originalText;

                                // Hide loading indicator if still showing
                                if (loadingToast && typeof loadingToast.remove === 'function') {
                                    loadingToast.remove();
                                }
                            }
                        } catch (error) {
                            console.error('Error in export button handler:', error);
                            Utils.showToast('❌ Terjadi kesalahan: ' + (error.message || 'Unknown error'), 'error');
                        }
                    });

                    // Also add jQuery listener as backup
                    if (typeof $ !== 'undefined') {
                        $(newConfirmBtn).off('click').on('click', async function(e) {
                            e.preventDefault();
                            console.log('Confirm export clicked (jQuery)');
                            // Trigger the vanilla JS handler
                            newConfirmBtn.click();
                        });
                    }
                }

                // Call setup function
                setupConfirmExportButton();

                // document.getElementById('export-pdf').addEventListener('click', function() {
                //     Utils.showToast('Export PDF akan segera tersedia', 'info');
                // });
            },

            setupMoveItemEvents() {
                document.addEventListener('click', function(event) {
                    const moveBtn = event.target.closest('.move-item-btn');
                    if (moveBtn) {
                        const itemId = moveBtn.dataset.itemId;
                        TableRenderer.showMoveItemModal(itemId);
                    }
                });
            },

            setupSplitItemEvents() {
                document.addEventListener('click', function(event) {
                    const splitBtn = event.target.closest('.split-item-btn');
                    if (splitBtn) {
                        const itemId = splitBtn.dataset.itemId;
                        TableRenderer.showSplitItemModal(itemId);
                    }
                });
            },

            setupModalEvents() {
                document.getElementById('confirm-move-btn').addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    const targetMachine = document.getElementById('target-machine-select').value;
                    const moveQuantity = parseInt(document.getElementById('move-quantity').value) || 0;

                    if (!targetMachine) {
                        Utils.showToast('Pilih mesin tujuan terlebih dahulu', 'warning');
                        return;
                    }

                    if (moveQuantity <= 0) {
                        Utils.showToast('Quantity harus lebih dari 0', 'warning');
                        return;
                    }

                    $('#moveItemModal').modal('hide');

                    DataManager.moveItemToMachine(itemId, targetMachine, moveQuantity).then(success => {
                        if (success) {
                            const originalItem = timelineData.find(i => i.id == itemId);
                            if (originalItem) {
                                // Mark as changed
                                ChangeTracker.markChanged(`Move Item: ${originalItem.code_item} to ${targetMachine}`);

                                // Sync times BEFORE rendering
                                TableRenderer.syncMachineTimes(originalItem.code_machine);
                                TableRenderer.syncMachineTimes(targetMachine);
                            }
                            // Then render with updated times
                            TableRenderer.renderTable();
                        }
                    });
                });

                document.getElementById('confirm-split-btn').addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    const targetMachine = document.getElementById('split-target-machine').value;
                    const splitQuantity = parseInt(document.getElementById('split-quantity').value) || 0;

                    if (!targetMachine) {
                        Utils.showToast('Pilih mesin tujuan terlebih dahulu', 'warning');
                        return;
                    }

                    if (splitQuantity <= 0) {
                        Utils.showToast('Quantity harus lebih dari 0', 'warning');
                        return;
                    }

                    // Close modal
                    $('#splitItemModal').modal('hide');

                    // Execute split
                    DataManager.splitItemQuantity(itemId, targetMachine, splitQuantity).then(success => {
                        if (success) {
                            // Re-render table to show changes
                            TableRenderer.renderTable();
                            // Sync times for both machines
                            const originalItem = timelineData.find(i => i.id == itemId);
                            if (originalItem) {
                                TableRenderer.syncMachineTimes(originalItem.code_machine);
                                TableRenderer.syncMachineTimes(targetMachine);
                            }
                        }
                    });
                });

                document.getElementById('confirm-finish-job').addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    const productionResult = parseInt(document.getElementById('production-result').value) || 0;
                    const qualityStatus = document.getElementById('quality-status').value;
                    const defectQty = parseInt(document.getElementById('defect-qty').value) || 0;
                    const defectReason = document.getElementById('defect-reason').value;
                    const remainingAction = document.querySelector('input[name="remaining-action"]:checked')
                        ?.value;
                    const rescheduleDate = document.getElementById('reschedule-date').value;
                    const rescheduleMachine = document.getElementById('reschedule-machine').value;

                    if (productionResult <= 0) {
                        Utils.showToast('Hasil produksi harus lebih dari 0', 'warning');
                        return;
                    }

                    // Close modal
                    $('#finishJobModal').modal('hide');

                    // Execute finish job
                    DataManager.finishJob(itemId, {
                        productionResult,
                        qualityStatus,
                        defectQty,
                        defectReason,
                        remainingAction,
                        rescheduleDate,
                        rescheduleMachine
                    }).then(success => {
                        if (success) {
                            // Re-render table to show changes
                            TableRenderer.renderTable();
                            // Update remaining items section
                            TableRenderer.updateRemainingItemsSection();
                        }
                    });
                });

                // Production result input handler
                document.getElementById('production-result').addEventListener('input', function() {
                    const targetQty = parseInt(document.getElementById('finish-target-qty').textContent);
                    const resultQty = parseInt(this.value) || 0;
                    const remainingQty = targetQty - resultQty;

                    if (remainingQty > 0) {
                        // Tampilkan section sisa
                        document.getElementById('remaining-section').style.display = 'block';
                        document.getElementById('remaining-qty').textContent = remainingQty;

                        // Enable radio buttons
                        document.querySelectorAll('input[name="remaining-action"]').forEach(radio => {
                            radio.disabled = false;
                        });
                    } else {
                        // Sembunyikan section sisa
                        document.getElementById('remaining-section').style.display = 'none';
                    }
                });

                // Quality status change handler
                document.getElementById('quality-status').addEventListener('change', function() {
                    const defectSection = document.getElementById('defect-section');
                    if (this.value === 'NG') {
                        defectSection.style.display = 'block';
                    } else {
                        defectSection.style.display = 'none';
                    }
                });

                // Remaining action radio button handler
                document.querySelectorAll('input[name="remaining-action"]').forEach(radio => {
                    radio.addEventListener('change', function() {
                        const rescheduleOptions = document.getElementById('reschedule-options');
                        if (this.value === 'reschedule') {
                            rescheduleOptions.style.display = 'block';
                        } else {
                            rescheduleOptions.style.display = 'none';
                        }
                    });
                });
            },

            setupMergeItemEvents() {
                document.addEventListener('click', function(event) {
                    const mergeBtn = event.target.closest('.merge-item-btn');
                    if (mergeBtn) {
                        const itemId = mergeBtn.dataset.itemId;
                        TableRenderer.showMergeItemModal(itemId);
                    }
                });
            },

            setupFinishJobEvents() {
                document.addEventListener('click', function(event) {
                    const finishBtn = event.target.closest('.finish-job-btn');
                    if (finishBtn) {
                        const itemId = finishBtn.dataset.itemId;
                        TableRenderer.showFinishJobModal(itemId);
                    }
                });
            },

            setupPriorityEvents() {
                // Event listener untuk input priority
                document.addEventListener('change', function(event) {
                    const priorityInput = event.target.closest('.priority-input');
                    if (priorityInput) {
                        const itemId = priorityInput.dataset.itemId;
                        const machineCode = priorityInput.dataset.machine;
                        const newPriority = parseInt(priorityInput.value);
                        const originalPriority = parseInt(priorityInput.dataset.originalPriority);

                        console.log('🔄 Priority input changed:', {
                            itemId: itemId,
                            machineCode: machineCode,
                            newPriority: newPriority,
                            originalPriority: originalPriority
                        });

                        // Validasi input
                        if (newPriority < 1 || newPriority > parseInt(priorityInput.max)) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid Priority',
                                text: `Priority must be between 1 and ${priorityInput.max}`,
                                timer: 2000
                            });
                            priorityInput.value = originalPriority;
                            return;
                        }

                        // Update priority
                        TableRenderer.updatePriority(itemId, machineCode, newPriority, originalPriority);
                    }
                });

                // Event listener untuk input real-time validation
                document.addEventListener('input', function(event) {
                    const priorityInput = event.target.closest('.priority-input');
                    if (priorityInput) {
                        const value = parseInt(priorityInput.value);
                        const max = parseInt(priorityInput.max);
                        const min = parseInt(priorityInput.min);

                        // Validasi real-time
                        if (value < min || value > max) {
                            priorityInput.style.borderColor = '#dc3545';
                            priorityInput.style.backgroundColor = '#f8d7da';
                        } else {
                            priorityInput.style.borderColor = '#28a745';
                            priorityInput.style.backgroundColor = '#d4edda';
                        }
                    }
                });

                // Event listener untuk focus out - reset styling
                document.addEventListener('blur', function(event) {
                    const priorityInput = event.target.closest('.priority-input');
                    if (priorityInput) {
                        priorityInput.style.borderColor = '';
                        priorityInput.style.backgroundColor = '';
                    }
                }, true);

                // Event listener untuk click (untuk kompatibilitas dengan button lama)
                document.addEventListener('click', function(event) {
                    const upBtn = event.target.closest('.priority-up-btn');
                    const downBtn = event.target.closest('.priority-down-btn');

                    if (upBtn) {
                        const itemId = upBtn.dataset.itemId;
                        const machineCode = upBtn.dataset.machine;
                        const currentPriority = parseInt(upBtn.dataset.currentPriority);
                        TableRenderer.showPriorityChangeModal(itemId, machineCode, currentPriority, 'up');
                    }

                    if (downBtn) {
                        const itemId = downBtn.dataset.itemId;
                        const machineCode = downBtn.dataset.machine;
                        const currentPriority = parseInt(downBtn.dataset.currentPriority);
                        TableRenderer.showPriorityChangeModal(itemId, machineCode, currentPriority, 'down');
                    }
                });

                // Setup modal confirmation
                document.getElementById('confirm-priority-change').addEventListener('click', function() {
                    const modal = document.getElementById('priorityChangeModal');
                    const action = modal.dataset.action;
                    const itemId = modal.dataset.itemId;
                    const machineCode = modal.dataset.machineCode;
                    const currentPriority = parseInt(modal.dataset.currentPriority);

                    if (action === 'up') {
                        TableRenderer.movePriorityUp(itemId, machineCode, currentPriority);
                    } else if (action === 'down') {
                        TableRenderer.movePriorityDown(itemId, machineCode, currentPriority);
                    }

                    // Mark as changed
                    const item = timelineData.find(i => i.id == itemId);
                    if (item) {
                        ChangeTracker.markChanged(`Priority Change: ${item.code_item} (${action})`);
                    }

                    $('#priorityChangeModal').modal('hide');
                });
            },



            setupSyncTimesEvents() {
                document.addEventListener('click', function(event) {
                    const syncBtn = event.target.closest('.sync-times-btn');
                    if (syncBtn) {
                        const machineCode = syncBtn.dataset.machine;
                        console.log('🎯 Sync button clicked for machine:', machineCode);

                        // Call sync function
                        TableRenderer.syncMachineTimes(machineCode);

                        // Force re-render to show updated times
                        setTimeout(() => {
                            TableRenderer.renderTable();
                        }, 100);
                    }
                });
            },

            setupSavePlanEvents() {
                // Test button event handler
                document.addEventListener('click', function(event) {
                    const testBtn = event.target.closest('#test-button');
                    if (testBtn) {
                        console.log('🧪 Test button clicked!');
                        alert('Test button works! Event handler is working.');
                    }
                });

                // Debug button event handler
                document.addEventListener('click', function(event) {
                    const debugBtn = event.target.closest('#debug-data-btn');
                    if (debugBtn) {
                        console.log('🐛 Debug button clicked!');
                        TableRenderer.debugAllData();
                    }
                });
            },

            setupMaintenanceEvents() {
                // Maintenance button event handler
                document.addEventListener('click', function(event) {
                    const maintenanceBtn = event.target.closest('.add-maintenance-btn');
                    if (maintenanceBtn) {
                        const machineCode = maintenanceBtn.dataset.machine;
                        console.log('🔧 Maintenance button clicked for machine:', machineCode);
                        TableRenderer.showMaintenanceScheduleModal(machineCode);
                    }
                });
            },

            setupAddPlanManualEvents() {
                // Shift select button event handler
                document.addEventListener('click', function(event) {
                    const shiftBtn = event.target.closest('.shift-select-btn');
                    if (shiftBtn) {
                        const machineCode = shiftBtn.dataset.machine;
                        const numShifts = parseInt(shiftBtn.dataset.shifts);

                        // Update shift config
                        machineShiftConfig[machineCode] = numShifts;

                        // Save to database
                        fetch("{{ route('process.save-machine-shift-config') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                machine: machineCode,
                                shift: numShifts
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                console.log(`✅ Shift config saved to database for machine ${machineCode}`);
                            }
                        })
                        .catch(error => {
                            console.error('❌ Error saving shift config:', error);
                        });

                        // Update button active state
                        const allShiftBtns = document.querySelectorAll(`.shift-select-btn[data-machine="${machineCode}"]`);
                        allShiftBtns.forEach(btn => {
                            btn.classList.remove('active');
                            if (parseInt(btn.dataset.shifts) === numShifts) {
                                btn.classList.add('active');
                            }
                        });

                        console.log(`🔄 Shift config updated for machine ${machineCode}: ${numShifts} shifts`);

                        // Re-render machine table with new shift config
                        const filteredData = DataManager.getFilteredData();
                        const machineData = filteredData
                            .filter(item => item.code_machine === machineCode)
                            .sort((a, b) => {
                                const aIndex = timelineData.findIndex(item => item.id === a.id);
                                const bIndex = timelineData.findIndex(item => item.id === b.id);
                                return aIndex - bIndex;
                            });

                        TableRenderer.populateMachineTable(machineCode, machineData);
                        Utils.showToast(`Shift mesin ${machineCode} diubah menjadi ${numShifts} shift`, 'success');
                    }
                });

                // Add Plan Manual button event handler
                document.addEventListener('click', function(event) {
                    const addPlanBtn = event.target.closest('.add-plan-manual-btn');
                    if (addPlanBtn) {
                        const machineCode = addPlanBtn.dataset.machine;
                        console.log('➕ Add Plan Manual button clicked for machine:', machineCode);
                        TableRenderer.showAddPlanManualModal(machineCode);
                    }
                });

                // Send Job Order button event handler
                document.addEventListener('click', function(event) {
                    const sendJoBtn = event.target.closest('.send-job-order-btn');
                    if (sendJoBtn) {
                        const machineCode = sendJoBtn.dataset.machine;
                        console.log('📤 Send Job Order button clicked for machine:', machineCode);
                        TableRenderer.showSendJobOrderModal(machineCode);
                    }
                });

                // Confirm Add Plan Manual
                const confirmBtn = document.getElementById('confirm-add-plan-manual');
                if (confirmBtn) {
                    confirmBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('🔵 Confirm Add Plan Manual button clicked');
                        TableRenderer.addPlanManual();
                    });
                } else {
                    console.error('❌ Confirm Add Plan Manual button not found');
                }
            },

            setupViewHistoryEvents() {
                // View History button from toolbar (show all history)
                document.getElementById('view-history-btn')?.addEventListener('click', function() {
                    // Show all plan history (not item-specific)
                    TableRenderer.showViewHistoryModal(null);
                });

                // View History button from modals (item-specific)
                document.addEventListener('click', function(event) {
                    const historyBtn = event.target.closest('.view-history-from-modal');
                    if (historyBtn) {
                        const itemId = historyBtn.dataset.itemId;
                        if (itemId) {
                            console.log('📚 View History button clicked for item:', itemId);
                            TableRenderer.showViewHistoryModal(itemId);
                        } else {
                            Utils.showToast('Item ID tidak ditemukan', 'warning');
                        }
                    }
                });

                // Compare Versions button
                document.getElementById('compare-versions-btn')?.addEventListener('click', function() {
                    TableRenderer.showCompareVersionsModal();
                });

                // Compare button in modal
                document.getElementById('btn-compare-versions')?.addEventListener('click', function() {
                    const version1 = document.getElementById('compare-version-1').value;
                    const version2 = document.getElementById('compare-version-2').value;
                    if (version1 && version2) {
                        TableRenderer.comparePlanVersions(version1, version2);
                    } else {
                        Utils.showToast('Pilih kedua versi untuk dibandingkan', 'warning');
                    }
                });
            },
        };

        // Table Renderer
        const TableRenderer = {
            renderTable() {
                const filteredData = DataManager.getFilteredData();

                if (filteredData.length === 0) {
                    this.showEmptyState();
                    return;
                }

                this.hideEmptyState();
                this.generateMachineTables(filteredData);


            },

            showEmptyState() {
                const emptyState = document.getElementById('empty-state');
                const tableContainer = document.querySelector('.table-container');

                emptyState.style.display = 'block';
                if (tableContainer) tableContainer.style.display = 'none';
            },

            hideEmptyState() {
                const emptyState = document.getElementById('empty-state');
                const tableContainer = document.querySelector('.table-container');

                emptyState.style.display = 'none';
                if (tableContainer) tableContainer.style.display = 'block';
            },

            generateMachineTables(data) {
                const machines = [...new Set(data.map(item => item.code_machine))];

                const container = document.getElementById('machine-tables-container');
                if (!container) {
                    return;
                }

                container.innerHTML = '';

                // Lazy load: Render machines in batches for better performance
                const MACHINES_PER_BATCH = 5;
                let currentBatch = 0;

                const renderMachineBatch = () => {
                    const startIndex = currentBatch * MACHINES_PER_BATCH;
                    const endIndex = Math.min(startIndex + MACHINES_PER_BATCH, machines.length);
                    const batchMachines = machines.slice(startIndex, endIndex);

                    batchMachines.forEach(machineCode => {
                    const machine = allMachines.find(m => m.Code === machineCode);
                    const machineName = machine ? machine.Description : machineCode;
                    // Filter and sort by order in timelineData to maintain priority order
                    // Plan manual (added last) will appear at the end
                    const machineData = data
                        .filter(item => item.code_machine === machineCode)
                        .sort((a, b) => {
                            // Sort by order in timelineData (preserve original order)
                            const aIndex = timelineData.findIndex(item => item.id === a.id);
                            const bIndex = timelineData.findIndex(item => item.id === b.id);
                            return aIndex - bIndex;
                        });
                    const machineContainer = document.createElement('div');
                    machineContainer.className = 'machine-table-container';
                    machineContainer.id = `machine-${machineCode}`;

                    machineContainer.innerHTML = `
                        <div class="machine-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="mdi mdi-gauge"></i> Mesin: ${machineCode} - ${machineName}
                                <span class="badge badge-primary ml-2">${machineData.length} Item</span>
                            </h4>
                            <div class="d-flex gap-2 align-items-center">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-secondary btn-sm shift-select-btn ${(machineShiftConfig[machineCode] || 3) === 2 ? 'active' : ''}"
                                            data-machine="${machineCode}"
                                            data-shifts="2"
                                            title="2 Shift (08:00-24:00)">
                                        2 Shift
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm shift-select-btn ${(machineShiftConfig[machineCode] || 3) === 3 ? 'active' : ''}"
                                            data-machine="${machineCode}"
                                            data-shifts="3"
                                            title="3 Shift (08:00-16:00, 16:00-24:00, 00:00-08:00)">
                                        3 Shift
                                    </button>
                                </div>
                                <button class="btn btn-outline-success btn-sm add-plan-manual-btn"
                                        data-machine="${machineCode}"
                                        title="Tambah Plan Manual (untuk ganti rusak dll)">
                                    <i class="mdi mdi-plus-circle"></i> Add Plan Manual
                                </button>
                                <button class="btn btn-outline-warning btn-sm add-maintenance-btn"
                                        data-machine="${machineCode}"
                                        title="Lihat Jadwal Maintenance MTC">
                                    <i class="mdi mdi-wrench"></i> Maintenance
                                    <span class="badge badge-light maintenance-count" id="maintenance-count-${machineCode}">...</span>
                                </button>
                                <button class="btn btn-outline-info btn-sm sync-times-btn"
                                        data-machine="${machineCode}"
                                        title="Sinkronkan Jam Produksi">
                                    <i class="mdi mdi-clock-sync"></i> Sync Times
                                </button>
                                <button class="btn btn-outline-primary btn-sm send-job-order-btn"
                                        data-machine="${machineCode}"
                                        title="Kirim Job Order ke Database Mesin">
                                    <i class="mdi mdi-send"></i> Kirim JO
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered machine-table" id="machine-table-${machineCode}">
                                <thead>
                                    <tr style="white-space: nowrap;">
                                        <th class="text-center" style="width: 80px;">PRIORITY</th>
                                        <th>KODE ITEM</th>
                                        <th class="col-nama-item">NAMA ITEM</th>
                                        <th class="col-wo-docno-item">WO DOCNO</th>
                                        <th>JOB ORDER</th>
                                        <th>QTY ORDER</th>
                                        <th>TGL DELIVERY</th>
                                        <th>PROCESS</th>
                                        <th>KAPASITAS</th>
                                        <th>LAMA CETAK</th>
                                        <th class="text-center" style="width: 100px;">SETUP (jam)</th>
                                        <th class="text-center" style="width: 100px;">ISTIRAHAT (jam)</th>
                                        <th>MULAI CETAK</th>
                                        <th class="col-end-cetak-item">AKHIR CETAK</th>
                                        <th>STATUS</th>
                                        <th>UKURAN KERTAS</th>
                                        <th>AKSI</th>
                                    </tr>
                                </thead>
                                <tbody id="machine-tbody-${machineCode}">
                                    <!-- Machine-specific data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    `;

                    container.appendChild(machineContainer);

                    // Load production data first
                    this.populateMachineTable(machineCode, machineData);

                    // Then load maintenance count for button indicator
                    this.loadMaintenanceCount(machineCode);
                });

                    // Render next batch if there are more machines
                    currentBatch++;
                    if (endIndex < machines.length) {
                        // Use requestAnimationFrame for smooth rendering
                        requestAnimationFrame(() => {
                            setTimeout(renderMachineBatch, 100); // Small delay between batches
                        });
                    } else {
                        console.log(`✅ All ${machines.length} machines rendered`);
                    }
                };

                // Start rendering first batch
                renderMachineBatch();
            },

            /**
             * Load maintenance count for machine button
             */
            loadMaintenanceCount(machineCode) {
                console.log(`🔧 Loading maintenance count for machine ${machineCode}`);

                fetch(`/sipo/maintenance/lubrication-machine/${machineCode}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(result => {
                        const countElement = document.getElementById(`maintenance-count-${machineCode}`);
                        if (countElement) {
                            if (result.success && result.data && result.data.length > 0) {
                                countElement.textContent = result.data.length;
                                countElement.className = 'badge badge-warning maintenance-count';
                            } else {
                                countElement.textContent = '0';
                                countElement.className = 'badge badge-secondary maintenance-count';
                            }
                        }
                    })
                    .catch(error => {
                        console.error(`❌ Error loading maintenance count for machine ${machineCode}:`, error);
                        const countElement = document.getElementById(`maintenance-count-${machineCode}`);
                        if (countElement) {
                            countElement.textContent = '!';
                            countElement.className = 'badge badge-danger maintenance-count';
                        }
                    });
            },

            populateMachineTable(machineCode, machineData) {
                const tbody = document.getElementById(`machine-tbody-${machineCode}`);
                if (!tbody) {
                    console.error(`Tbody not found for machine ${machineCode}`);
                    return;
                }

                tbody.innerHTML = '';

                if (machineData.length === 0) {
                    tbody.innerHTML = `
                        <tr style="white-space: nowrap;">
                            <td colspan="17" class="text-center text-muted py-4">
                                <i class="mdi mdi-information-outline" style="font-size: 2rem;"></i>
                                <br><br>
                                Tidak ada data untuk mesin ${machineCode}
                            </td>
                        </tr>
                    `;
                    return;
                }

                // Get shift config for this machine (default: 3)
                const numShifts = machineShiftConfig[machineCode] || 3;

                // Process data based on shift config
                let processedData = [...machineData];

                if (numShifts === 2) {
                    // If 2 shifts, split items that cross midnight (00:00)
                    const splitItems = [];

                    machineData.forEach(item => {
                        if (!item.start_jam || !item.end_jam) {
                            splitItems.push(item);
                            return;
                        }

                        const startTime = new Date(item.start_jam);
                        const endTime = new Date(item.end_jam);

                        // Check if item crosses midnight (starts before 00:00 and ends after 00:00)
                        const startDay = new Date(startTime);
                        startDay.setHours(0, 0, 0, 0);
                        const endDay = new Date(endTime);
                        endDay.setHours(0, 0, 0, 0);

                        const crossesMidnight = endDay.getTime() > startDay.getTime();

                        if (crossesMidnight) {
                            // Split item into two parts
                            const midnight = new Date(startTime);
                            midnight.setDate(midnight.getDate() + 1);
                            midnight.setHours(0, 0, 0, 0);

                            // Calculate durations
                            const beforeMidnightMs = midnight.getTime() - startTime.getTime();
                            const afterMidnightMs = endTime.getTime() - midnight.getTime();
                            const afterMidnightHours = afterMidnightMs / (1000 * 60 * 60);

                            // Get capacity
                            const capacity = parseFloat(item.up_cetak || item.capacity || 10000);
                            const totalQuantity = parseFloat(item.quantity || 0);

                            // Quantity after midnight = durasi setelah tengah malam (jam) * kapasitas
                            const quantityAfter = Math.round(afterMidnightHours * capacity);

                            // Quantity before midnight = total quantity - quantity after
                            const quantityBefore = totalQuantity - quantityAfter;

                            // Item 1: Before midnight (original start to 00:00)
                            const item1 = { ...item };
                            item1.start_jam = startTime.toISOString();
                            item1.end_jam = midnight.toISOString();
                            item1.quantity = quantityBefore;

                            // Item 2: After midnight (moved to next day at 08:00)
                            const item2 = { ...item };
                            const nextDay = new Date(midnight);
                            nextDay.setHours(8, 0, 0, 0);

                            // Calculate end time for item 2 based on quantity and capacity
                            const durationAfterHours = quantityAfter / capacity;
                            const endTimeNextDay = new Date(nextDay);
                            endTimeNextDay.setTime(nextDay.getTime() + (durationAfterHours * 60 * 60 * 1000));

                            item2.start_jam = nextDay.toISOString();
                            item2.end_jam = endTimeNextDay.toISOString();
                            item2.quantity = quantityAfter;

                            // Generate new ID for item2 to avoid conflicts
                            item2.id = item.id + '_split_' + Date.now();

                            splitItems.push(item1);
                            splitItems.push(item2);
                        } else {
                            // Item doesn't cross midnight, but check if it's in Shift 3 (00:00-08:00)
                            const startHour = startTime.getHours();
                            const startMinutes = startTime.getMinutes();
                            const totalMinutes = startHour * 60 + startMinutes;

                            if (totalMinutes >= 0 && totalMinutes < 480) {
                                // Move Shift 3 item to next day at 08:00
                                const newItem = { ...item };
                                const nextDay = new Date(startTime);
                                nextDay.setDate(nextDay.getDate() + 1);
                                nextDay.setHours(8, 0, 0, 0);

                                // Adjust end_jam accordingly (maintain duration)
                                const duration = endTime.getTime() - startTime.getTime();
                                const newEndDate = new Date(nextDay);
                                newEndDate.setTime(nextDay.getTime() + duration);

                                newItem.start_jam = nextDay.toISOString();
                                newItem.end_jam = newEndDate.toISOString();
                                splitItems.push(newItem);
                            } else {
                                // Normal item, no change
                                splitItems.push(item);
                            }
                        }
                    });

                    processedData = splitItems;
                }

                // Sort processed data by start_jam
                processedData.sort((a, b) => {
                    if (!a.start_jam || !b.start_jam) return 0;
                    return new Date(a.start_jam) - new Date(b.start_jam);
                });

                processedData.forEach((item, index) => {
                    const machine = allMachines.find(m => m.Code === item.code_machine);
                    const isMaintenance = item.is_maintenance || item.process === 'Maintenance';

                    const row = document.createElement('tr');
                    row.className =
                        `plan-row ${item.flag_status ? 'status-' + item.flag_status.toLowerCase().replace(/\s/g, '-') : ''}`;

                    // Add maintenance styling
                    if (isMaintenance) {
                        row.classList.add('maintenance-row');
                        row.style.backgroundColor = '#fff3cd';
                        row.style.borderLeft = '4px solid #ffc107';
                    }

                    row.classList.add('fade-in');
                    row.style.animationDelay = `${index * 0.1}s`;
                    row.setAttribute('data-item-id', item.id);

                    row.innerHTML = `
                        <td class="priority-cell text-center">
                                        <div class="priority-input-container">
                                            <input type="number"
                                                   class="form-control form-control-sm priority-input text-center"
                                                   value="${index + 1}"
                                                   min="1"
                                                   max="${machineData.length}"
                                                   data-item-id="${item.id}"
                                                   data-machine="${item.code_machine}"
                                                   data-original-priority="${index + 1}"
                                                   style="width: 60px; font-weight: bold; color: #007bff; font-size: 1.1rem;"
                                                   title="Edit Prioritas (1-${machineData.length})">
                                        </div>
                            ${isMaintenance ? `
                                        <div class="maintenance-badge mt-1">
                                    <span class="badge badge-warning badge-sm">
                                        <i class="mdi mdi-wrench"></i> MTC
                                            </span>
                                        </div>
                            ` : ''}
                        </td>
                        <td>
                            <strong>${item.code_item || '-'}</strong>
                            ${isMaintenance ? '<br><small class="text-muted">' + (item.jenis_maintenance || 'Maintenance') + '</small>' : ''}
                        </td>
                        <td class="col-nama-item">${item.material_name || '-'}</td>
                        <td class="col-wo-docno-item">${item.wo_docno || '-'}</td>
                        <td>
                            ${item.job_order || item.job_order_no ?
                                `<span class="badge badge-success" style="font-size: 0.85rem; padding: 4px 8px;">
                                    <i class="mdi mdi-check-circle"></i> ${item.job_order || item.job_order_no}
                                </span>` :
                                `<span class="badge badge-danger" style="font-size: 0.85rem; padding: 4px 8px;">
                                    <i class="mdi mdi-alert-circle"></i> Belum Ada
                                </span>`
                            }
                        </td>
                        <td class="text-center">
                            ${(() => {
                                // Semua item bisa edit quantity (kecuali maintenance)
                                if (isMaintenance) {
                                    return `${parseInt(item.quantity || 0).toLocaleString()}`;
                                }
                                const quantityValue = parseInt(item.quantity || 0);
                                return `<input type="number"
                                               class="form-control form-control-sm quantity-input"
                                               data-item-id="${item.id}"
                                               data-machine="${item.code_machine}"
                                               value="${quantityValue}"
                                               min="1"
                                               step="1"
                                               style="width: 100px; text-align: center; font-weight: 600;"
                                               title="Edit Quantity">
                                        `;
                            })()}
                        </td>
                        <td>${item.delivery_date || item.datetgs || '-'}</td>
                        <td class="text-center process-badge ${item.process ? item.process.toLowerCase().replace(/\s/g, '-') : ''}">
                            ${isMaintenance ?
                                `<span class="badge badge-warning">${item.process || 'Maintenance'}</span>` :
                                item.process || '-'
                            }
                        </td>
                        <td>${parseInt(item.capacity || 10000).toLocaleString()}</td>
                        <td>${(() => {
                            if (isMaintenance) {
                                return (item.durasi || 2) + ' jam';
                            }
                            const isWOP = item.wo_docno && item.wo_docno.toUpperCase().startsWith('WOP');
                            if (isWOP) {
                                // Hitung duration dari quantity / capacity untuk WOP
                                const machine = allMachines.find(m => m.Code === item.code_machine);
                                const capacityPerHour = machine ? (machine.CapacityPerHour || 10000) : 10000;
                                const durationHours = parseFloat(item.quantity || 0) / capacityPerHour;
                                return `${durationHours.toFixed(2)} jam`;
                            } else {
                                return `${Utils.calculateDuration(item.start_jam, item.end_jam)} jam`;
                            }
                        })()}</td>
                        <td class="text-center">
                            <input type="number"
                                   class="form-control form-control-sm setup-time-input"
                                   data-item-id="${item.id}"
                                   data-machine="${item.code_machine}"
                                   value="${item.setup || item.setup_time || 0}"
                                   min="0"
                                   step="0.25"
                                   style="width: 80px; text-align: center;"
                                   title="Waktu setup dalam jam">
                        </td>
                        <td class="text-center">
                            <input type="number"
                                   class="form-control form-control-sm break-time-input"
                                   data-item-id="${item.id}"
                                   data-machine="${item.code_machine}"
                                   value="${item.istirahat || item.break_time || 0}"
                                   min="0"
                                   step="0.25"
                                   style="width: 80px; text-align: center;"
                                   title="Waktu istirahat dalam jam">
                        </td>
                        <td>
                            ${(() => {
                                if (isMaintenance) {
                                    return Utils.formatDateTime(item.start_jam);
                                }
                                // Jika item sudah FINISH, tampilkan sebagai teks agar tidak bisa diedit
                                if (item.flag_status === 'FINISH') {
                                    return Utils.formatDateTime(item.start_jam);
                                }
                                // Buat input datetime-local untuk edit tanggal mulai cetak
                                // Pastikan item.start_jam ada dan valid
                                if (!item.start_jam) {
                                    // Jika tidak ada start_jam, gunakan tanggal sekarang
                                    const now = new Date();
                                    const year = now.getFullYear();
                                    const month = String(now.getMonth() + 1).padStart(2, '0');
                                    const day = String(now.getDate()).padStart(2, '0');
                                    const hours = String(now.getHours()).padStart(2, '0');
                                    const minutes = String(now.getMinutes()).padStart(2, '0');
                                    const datetimeValue = `${year}-${month}-${day}T${hours}:${minutes}`;

                                    return `
                                        <input type="datetime-local"
                                               class="form-control form-control-sm date-input start-date-input"
                                               data-item-id="${item.id}"
                                               data-machine="${item.code_machine}"
                                               value="${datetimeValue}"
                                               style="width: 100%; max-width: 180px; font-size: 0.85rem;"
                                               title="Edit Tanggal & Waktu Mulai Cetak">
                                    `;
                                }

                                try {
                                    const startDate = new Date(item.start_jam);
                                    // Validasi bahwa startDate adalah valid date
                                    if (isNaN(startDate.getTime())) {
                                        throw new Error('Invalid date');
                                    }
                                    // Format untuk datetime-local: YYYY-MM-DDTHH:mm
                                    const year = startDate.getFullYear();
                                    const month = String(startDate.getMonth() + 1).padStart(2, '0');
                                    const day = String(startDate.getDate()).padStart(2, '0');
                                    const hours = String(startDate.getHours()).padStart(2, '0');
                                    const minutes = String(startDate.getMinutes()).padStart(2, '0');
                                    const datetimeValue = `${year}-${month}-${day}T${hours}:${minutes}`;

                                    return `
                                        <input type="datetime-local"
                                               class="form-control form-control-sm date-input start-date-input"
                                               data-item-id="${item.id}"
                                               data-machine="${item.code_machine}"
                                               value="${datetimeValue}"
                                               style="width: 100%; max-width: 180px; font-size: 0.85rem;"
                                               title="Edit Tanggal & Waktu Mulai Cetak">
                                    `;
                                } catch (e) {
                                    // Fallback jika ada error parsing date
                                    const now = new Date();
                                    const year = now.getFullYear();
                                    const month = String(now.getMonth() + 1).padStart(2, '0');
                                    const day = String(now.getDate()).padStart(2, '0');
                                    const hours = String(now.getHours()).padStart(2, '0');
                                    const minutes = String(now.getMinutes()).padStart(2, '0');
                                    const datetimeValue = `${year}-${month}-${day}T${hours}:${minutes}`;

                                    return `
                                        <input type="datetime-local"
                                               class="form-control form-control-sm date-input start-date-input"
                                               data-item-id="${item.id}"
                                               data-machine="${item.code_machine}"
                                               value="${datetimeValue}"
                                               style="width: 100%; max-width: 180px; font-size: 0.85rem;"
                                               title="Edit Tanggal & Waktu Mulai Cetak">
                                    `;
                                }
                            })()}
                        </td>
                        <td class="col-end-cetak-item">${Utils.formatDateTime(item.end_jam)}</td>
                        <td class="text-center">
                            <select class="form-control form-control-sm status-select"
                                    data-item-id="${item.id}"
                                    data-machine="${machineCode}"
                                    style="width: 100px; font-size: 0.85rem; text-align: center; padding: 2px 4px;">
                                <option value="NEW" ${item.job_order_status && item.job_order_status.toUpperCase() === 'NEW' ? 'selected' : ''}>NEW</option>
                                <option value="REPEAT" ${item.job_order_status && item.job_order_status.toUpperCase() === 'REPEAT' ? 'selected' : ''}>REPEAT</option>
                            </select>
                        </td>
                        <td class="text-center ukuran-kertas-cell" data-item-id="${item.id}" data-code-item="${item.code_item || ''}">
                            ${item.ukuran_kertas || (item.panjang && item.lebar) ?
                                (item.ukuran_kertas || `${item.panjang} x ${item.lebar}`) :
                                `<span class="text-muted"><i class="mdi mdi-loading mdi-spin"></i> Loading...</span>`
                            }
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                ${isMaintenance ? `
                                            <span class="btn btn-warning disabled">
                                                <i class="mdi mdi-wrench"></i> Maintenance
                                            </span>
                                        ` : item.flag_status === 'FINISH' ? `
                                            <span class="btn btn-success disabled">
                                                <i class="mdi mdi-check-circle"></i> FINISH
                                            </span>
                                        ` : `
                                            <button class="btn btn-info move-item-btn" data-item-id="${item.id}" title="Pindah ke Mesin Lain">
                                                <i class="mdi mdi-swap-horizontal"></i>
                                            </button>
                                            <button class="btn btn-warning split-item-btn" data-item-id="${item.id}" title="Bagi Quantity">
                                                <i class="mdi mdi-call-split"></i>
                                            </button>
                                            <button class="btn btn-success merge-item-btn" data-item-id="${item.id}" title="Gabung dengan Item Lain"
                                                    style="display: ${TableRenderer.canMergeItem(item) ? 'inline-block' : 'none'};">
                                                <i class="mdi mdi-call-merge"></i>
                                            </button>
                                            <button class="btn btn-success finish-job-btn" data-item-id="${item.id}" title="Selesai Produksi">
                                                <i class="mdi mdi-check-circle"></i> Finish
                                            </button>
                                        `}
                            </div>
                        </td>
                    `;

                    // Click event removed - no modal on row click

                    tbody.appendChild(row);
                });

                // Check job order status for this machine's items
                this.checkJobOrderStatusForMachine(machineCode, machineData);

                // Load job orders for all items in this machine
                this.loadJobOrdersForMachine(machineCode, processedData);
            },

            /**
             * Load job orders for all items in a machine
             */
            async loadJobOrdersForMachine(machineCode, machineData) {
                try {
                    // Collect all unique WO DOCNOs from machine data
                    const woDocNos = [...new Set(machineData
                        .map(item => item.wo_docno)
                        .filter(wo => wo && wo.trim() !== ''))];

                    if (woDocNos.length === 0) {
                        console.log(`No WO DOCNOs found for machine ${machineCode}`);
                        return;
                    }

                    console.log(`🔍 Loading job orders for machine ${machineCode}, ${woDocNos.length} WO DOCNOs`);

                    // Call API to get job orders
                    const response = await fetch('/sipo/job-orders/get-by-wo-docnos', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            wo_docnos: woDocNos
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.success && result.data) {
                        // Update job order column for each row
                        const tbody = document.getElementById(`machine-tbody-${machineCode}`);
                        if (!tbody) return;

                        machineData.forEach(item => {
                            const row = tbody.querySelector(`tr[data-item-id="${item.id}"]`);

                            if (!row) return;

                            // Remove existing job order classes
                            row.classList.remove('has-job-order', 'no-job-order');

                            // Job Order column is at index 4 (0-based)
                            const jobOrderCell = row.cells[4];
                            if (!jobOrderCell) return;

                            // Jika tidak ada WO DOCNO, tandai sebagai belum ada
                            if (!item.wo_docno || item.wo_docno.trim() === '') {
                                row.classList.add('no-job-order');
                                jobOrderCell.innerHTML = `
                                    <span class="badge badge-secondary" style="font-size: 0.85rem; padding: 4px 8px;">
                                        <i class="mdi mdi-information"></i> Tidak Ada WO
                                    </span>
                                `;
                                jobOrderCell.setAttribute('title', 'WO DOCNO tidak tersedia');
                                return;
                            }

                            const jobOrderData = result.data[item.wo_docno];

                            if (jobOrderData && jobOrderData.doc_no) {
                                // Baris yang sudah ada job order - hijau
                                row.classList.add('has-job-order');
                                jobOrderCell.innerHTML = `
                                    <span class="badge badge-success" style="font-size: 0.85rem; padding: 4px 8px;">
                                        <i class="mdi mdi-check-circle"></i> ${jobOrderData.doc_no}
                                    </span>
                                `;
                                jobOrderCell.setAttribute('title', `Status: ${jobOrderData.status || 'N/A'}, Qty: ${jobOrderData.qty || 'N/A'}`);

                                // Update STATUS column (index 15, after AKHIR CETAK)
                                const statusCell = row.cells[15];
                                if (statusCell) {
                                    const status = jobOrderData.status ? jobOrderData.status.toUpperCase() : 'NEW';
                                    statusCell.innerHTML = `
                                        <select class="form-control form-control-sm status-select"
                                                data-item-id="${item.id}"
                                                data-machine="${machineCode}"
                                                style="width: 100px; font-size: 0.85rem; text-align: center; padding: 2px 4px;">
                                            <option value="NEW" ${status === 'NEW' ? 'selected' : ''}>NEW</option>
                                            <option value="REPEAT" ${status === 'REPEAT' ? 'selected' : ''}>REPEAT</option>
                                        </select>
                                    `;
                                }

                                // Update item data with status and job order
                                item.job_order_status = jobOrderData.status || 'NEW';
                                item.status_item = jobOrderData.status || 'NEW'; // Also update status_item for database
                                item.job_order = jobOrderData.doc_no || null;
                                item.job_order_no = jobOrderData.doc_no || null;
                            } else {
                                // Baris yang belum ada job order - merah
                                row.classList.add('no-job-order');
                                jobOrderCell.innerHTML = `
                                    <span class="badge badge-danger" style="font-size: 0.85rem; padding: 4px 8px;">
                                        <i class="mdi mdi-alert-circle"></i> Belum Ada
                                    </span>
                                `;
                                jobOrderCell.setAttribute('title', 'Job Order belum tersedia untuk WO ini');

                                // Update STATUS column to show dropdown with default NEW if no job order
                                const statusCell = row.cells[15];
                                if (statusCell) {
                                    statusCell.innerHTML = `
                                        <select class="form-control form-control-sm status-select"
                                                data-item-id="${item.id}"
                                                data-machine="${machineCode}"
                                                style="width: 100px; font-size: 0.85rem; text-align: center; padding: 2px 4px;">
                                            <option value="NEW" selected>NEW</option>
                                            <option value="REPEAT">REPEAT</option>
                                        </select>
                                    `;
                                }
                            }
                        });

                        console.log(`✅ Job orders loaded for machine ${machineCode}`);

                        // Load paper sizes after job orders are loaded
                        this.loadPaperSizesForMachine(machineCode, machineData);
                    } else {
                        console.warn(`⚠️ No job order data returned for machine ${machineCode}`);

                        // Mark all rows as no job order if API fails
                        const tbody = document.getElementById(`machine-tbody-${machineCode}`);
                        if (tbody) {
                            machineData.forEach(item => {
                                if (!item.wo_docno) return;
                                const row = tbody.querySelector(`tr[data-item-id="${item.id}"]`);
                                if (row) {
                                    row.classList.remove('has-job-order');
                                    row.classList.add('no-job-order');
                                    const jobOrderCell = row.cells[4];
                                    if (jobOrderCell) {
                                        jobOrderCell.innerHTML = `
                                            <span class="badge badge-danger" style="font-size: 0.85rem; padding: 4px 8px;">
                                                <i class="mdi mdi-alert-circle"></i> Belum Ada
                                            </span>
                                        `;
                                    }
                                }
                            });
                        }

                        // Still try to load paper sizes even if job orders failed
                        this.loadPaperSizesForMachine(machineCode, machineData);
                    }

                } catch (error) {
                    console.error(`❌ Error loading job orders for machine ${machineCode}:`, error);
                    // Still try to load paper sizes even if job orders failed
                    this.loadPaperSizesForMachine(machineCode, machineData);
                }
            },

            /**
             * Load paper sizes for all items in a machine
             */
            async loadPaperSizesForMachine(machineCode, machineData) {
                try {
                    // Collect all unique material codes (code_item) from machine data
                    const materialCodes = [...new Set(machineData
                        .map(item => item.code_item)
                        .filter(code => code && code.trim() !== ''))];

                    if (materialCodes.length === 0) {
                        console.log(`No material codes found for machine ${machineCode}`);
                        return;
                    }

                    console.log(`🔍 Loading paper sizes for machine ${machineCode}, ${materialCodes.length} material codes`);

                    // Call API to get paper sizes
                    const response = await fetch('/sipo/job-orders/get-paper-sizes', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            material_codes: materialCodes
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.success && result.data) {
                        // Update ukuran kertas column for each row
                        const tbody = document.getElementById(`machine-tbody-${machineCode}`);
                        if (!tbody) return;

                        machineData.forEach(item => {
                            // Gunakan selector yang lebih spesifik untuk mencari cell
                            const ukuranKertasCell = tbody.querySelector(`tr[data-item-id="${item.id}"] td.ukuran-kertas-cell`);

                            if (!ukuranKertasCell) {
                                // Fallback: cari berdasarkan index jika selector tidak berhasil
                                const row = tbody.querySelector(`tr[data-item-id="${item.id}"]`);
                                if (row && row.cells[15]) {
                                    const fallbackCell = row.cells[15];
                                    fallbackCell.classList.add('ukuran-kertas-cell');
                                    updatePaperSizeCell(fallbackCell, item, result.data);
                                }
                                return;
                            }

                            updatePaperSizeCell(ukuranKertasCell, item, result.data);
                        });

                        console.log(`✅ Paper sizes loaded for machine ${machineCode}`);
                    } else {
                        console.warn(`⚠️ No paper size data returned for machine ${machineCode}`);
                        // Update semua cell dengan status tidak ditemukan
                        const tbody = document.getElementById(`machine-tbody-${machineCode}`);
                        if (tbody) {
                            machineData.forEach(item => {
                                const ukuranKertasCell = tbody.querySelector(`tr[data-item-id="${item.id}"] td.ukuran-kertas-cell`);
                                if (ukuranKertasCell) {
                                    ukuranKertasCell.innerHTML = '-';
                                    ukuranKertasCell.setAttribute('title', 'Ukuran kertas tidak ditemukan');
                                }
                            });
                        }
                    }

                    // Helper function untuk update cell
                    function updatePaperSizeCell(cell, item, paperSizeDataMap) {
                        const paperSizeData = paperSizeDataMap[item.code_item];

                        if (paperSizeData) {
                            // Combine BAHAN1 and UC1 (ukuran cetak)
                            let displayText = '';
                            if (paperSizeData.bahan1 && paperSizeData.ukuran_cetak) {
                                displayText = `${paperSizeData.bahan1} | UC : ${paperSizeData.ukuran_cetak}`;
                            } else if (paperSizeData.bahan1) {
                                displayText = paperSizeData.bahan1;
                            } else if (paperSizeData.ukuran_cetak) {
                                displayText = paperSizeData.ukuran_cetak;
                            } else {
                                displayText = '-';
                            }

                            cell.innerHTML = displayText;
                            cell.setAttribute('title', `Bahan: ${paperSizeData.bahan1 || 'N/A'}, UC: ${paperSizeData.ukuran_cetak || 'N/A'}`);

                            // Update item data
                            item.ukuran_kertas = displayText;
                            item.keterangan_item = displayText; // Also update keterangan_item for database
                        } else {
                            // No paper size data found
                            cell.innerHTML = '-';
                            cell.setAttribute('title', 'Ukuran kertas tidak ditemukan');
                        }
                    }
                } catch (error) {
                    console.error(`❌ Error loading paper sizes for machine ${machineCode}:`, error);
                }
            },

            /**
             * Check job order status for items in a specific machine
             */
            async checkJobOrderStatusForMachine(machineCode, machineData) {
                try {
                    // Get plan code from the first item (assuming all items in machine have same plan)
                    if (machineData.length === 0) return;

                    const firstItem = machineData[0];
                    const planCode = firstItem.code_plan || this.getCurrentPlanCode();

                    // Debug logging
                    console.log('🔍 Checking job order for machine:', machineCode);
                    console.log('📋 First item data:', firstItem);
                    console.log('🏷️ Plan code:', planCode);

                    if (!planCode) {
                        console.warn('⚠️ No plan code found for job order check');
                        console.log('📊 Available fields:', Object.keys(firstItem));

                        // Fallback: use code_item as identifier for job order check
                        console.log('🔄 Using fallback: checking job order by code_item');
                        await this.checkJobOrderStatusByItem(machineCode, machineData);
                        return;
                    }

                    const response = await fetch('/sipo/job-orders/check-status', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            plan_id: planCode
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        this.applyJobOrderStyling(machineCode, result.data);
                        this.updateJobOrderSummary(result.data);
                    } else {
                        console.error('Failed to check job order status:', result.message);
                    }

                } catch (error) {
                    console.error('Error checking job order status:', error);
                }
            },

            /**
             * Apply styling based on job order status
             */
            applyJobOrderStyling(machineCode, jobOrderData) {
                const tbody = document.getElementById(`machine-tbody-${machineCode}`);
                if (!tbody) return;

                jobOrderData.forEach(itemStatus => {
                    const row = tbody.querySelector(`tr[data-item-id="${itemStatus.plan_item_id}"]`);
                    if (!row) return;

                    if (itemStatus.needs_attention) {
                        // Add red styling for items without job order
                        row.classList.add('no-job-order');
                        row.style.backgroundColor = '#ffebee';
                        row.style.borderLeft = '4px solid #f44336';
                        // WO column tetap menampilkan nomor WO saja, tanpa icon
                    } else {
                        // Add green styling for items with job order
                        row.classList.add('has-job-order');
                        row.style.backgroundColor = '#e8f5e8';
                        row.style.borderLeft = '4px solid #4caf50';
                        // WO column tetap menampilkan nomor WO saja, tanpa icon
                    }
                });
            },

            /**
             * Update job order summary display
             */
            updateJobOrderSummary(jobOrderData) {
                const missingCount = jobOrderData.filter(item => item.needs_attention).length;
                const totalCount = jobOrderData.length;

                // Update or create summary display
                let summaryElement = document.getElementById('job-order-summary');
                if (!summaryElement) {
                    summaryElement = document.createElement('div');
                    summaryElement.id = 'job-order-summary';
                    summaryElement.className = 'alert alert-info mt-3';
                    summaryElement.style.display = 'none';

                    const container = document.querySelector('.filter-section');
                    if (container) {
                        container.appendChild(summaryElement);
                    }
                }

                // if (missingCount > 0) {
                //     summaryElement.className = 'alert alert-warning mt-3';
                //     summaryElement.innerHTML = `
                //         <i class="mdi mdi-alert-triangle"></i>
                //         <strong>Peringatan Job Order:</strong>
                //         ${missingCount} dari ${totalCount} item belum memiliki Job Order
                //         <button class="btn btn-sm btn-outline-warning ml-2" onclick="TableRenderer.refreshJobOrderStatus()">
                //             <i class="mdi mdi-refresh"></i> Refresh
                //         </button>
                //         <button class="btn btn-sm btn-outline-secondary ml-2" onclick="TableRenderer.clearJobOrderStyling()">
                //             <i class="mdi mdi-close"></i> Clear Styling
                //         </button>
                //     `;
                // } else {
                //     summaryElement.className = 'alert alert-success mt-3';
                //     summaryElement.innerHTML = `
                //         <i class="mdi mdi-check-circle"></i>
                //         <strong>Status Job Order:</strong>
                //         Semua ${totalCount} item sudah memiliki Job Order
                //         <button class="btn btn-sm btn-outline-success ml-2" onclick="TableRenderer.refreshJobOrderStatus()">
                //             <i class="mdi mdi-refresh"></i> Refresh
                //         </button>
                //         <button class="btn btn-sm btn-outline-secondary ml-2" onclick="TableRenderer.clearJobOrderStyling()">
                //             <i class="mdi mdi-close"></i> Clear Styling
                //         </button>
                //     `;
                // }
            },

            /**
             * Refresh job order status for all machines
             */
            async refreshJobOrderStatus() {
                const machines = document.querySelectorAll('.machine-table-container');

                for (const machineContainer of machines) {
                    const machineCode = machineContainer.id.replace('machine-', '');
                    const tbody = machineContainer.querySelector('tbody');
                    const rows = tbody.querySelectorAll('tr[data-item-id]');

                    if (rows.length > 0) {
                        const machineData = Array.from(rows).map(row => {
                            const itemId = row.getAttribute('data-item-id');
                            return timelineData.find(item => item.id == itemId);
                        }).filter(Boolean);

                        await this.checkJobOrderStatusForMachine(machineCode, machineData);
                    }
                }
            },

            /**
             * Clear job order styling and restore original WO numbers
             */
            clearJobOrderStyling() {
                const rows = document.querySelectorAll('.no-job-order, .has-job-order');

                rows.forEach(row => {
                    // Remove styling classes
                    row.classList.remove('no-job-order', 'has-job-order');
                    row.style.backgroundColor = '';
                    row.style.borderLeft = '';
                    // WO column sudah menampilkan nomor WO saja, tidak perlu restore
                });

                // Remove summary display
                const summaryElement = document.getElementById('job-order-summary');
                if (summaryElement) {
                    summaryElement.remove();
                }

                console.log('✅ Job order styling cleared and WO numbers restored');
            },

            /**
             * Get current plan code from timeline data
             */
            getCurrentPlanCode() {
                if (timelineData.length > 0) {
                    return timelineData[0].code_plan;
                }
                return null;
            },

            /**
             * Fallback: Check job order status by individual items
             */
            async checkJobOrderStatusByItem(machineCode, machineData) {
                try {
                    console.log('🔍 Fallback: Checking job order by individual items for machine:', machineCode);

                    // Extract item codes from machine data
                    const itemCodes = machineData.map(item => item.code_item);

                    const response = await fetch('/sipo/job-orders/check-status-by-items', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            item_codes: itemCodes
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        // Map the results to include plan_item_id and code_machine
                        const jobOrderResults = result.data.map((itemStatus, index) => ({
                            plan_item_id: machineData[index].id,
                            code_item: itemStatus.code_item,
                            code_machine: machineData[index].code_machine,
                            has_job_order: itemStatus.has_job_order,
                            job_order_number: itemStatus.job_order_number,
                            job_order_status: itemStatus.job_order_status,
                            needs_attention: itemStatus.needs_attention
                        }));

                        this.applyJobOrderStyling(machineCode, jobOrderResults);
                        this.updateJobOrderSummary(jobOrderResults);
                    } else {
                        console.error('Failed to check job order status by items:', result.message);
                    }

                } catch (error) {
                    console.error('Error in fallback job order check:', error);
                }
            },

            // showDetailModal function removed - no modal on row click

            showMoveItemModal(itemId) {
                const item = timelineData.find(i => i.id == itemId);
                if (!item) {
                    Utils.showToast('Item tidak ditemukan', 'danger');
                    return;
                }

                // Populate item info
                document.getElementById('move-item-info').innerHTML = `
                    <strong>${item.code_item}</strong> - ${item.material_name}<br>
                    Quantity: ${parseInt(item.quantity).toLocaleString()} PCS<br>
                    Mesin Asal: ${item.code_machine}
                `;

                // Populate target machine dropdown
                const targetSelect = document.getElementById('target-machine-select');
                targetSelect.innerHTML = '<option value="">-- Pilih Mesin --</option>';
                allMachines.forEach(machine => {
                    if (machine.Code !== item.code_machine) {
                        const option = document.createElement('option');
                        option.value = machine.Code;
                        option.textContent = `${machine.Code} - ${machine.Description}`;
                        targetSelect.appendChild(option);
                    }
                });

                // Set default quantity to entire item quantity
                const quantityInput = document.getElementById('move-quantity');
                quantityInput.value = item.quantity;
                quantityInput.max = item.quantity;
                quantityInput.min = 1;

                // Store item ID for confirmation
                document.getElementById('confirm-move-btn').dataset.itemId = itemId;

                // Set item ID for history button in modal
                const historyBtn = document.querySelector('#moveItemModal .view-history-from-modal');
                if (historyBtn) {
                    historyBtn.dataset.itemId = itemId;
                }

                $('#moveItemModal').modal('show');
            },

            showSplitItemModal(itemId) {
                const item = timelineData.find(i => i.id == itemId);
                if (!item) {
                    Utils.showToast('Item tidak ditemukan', 'danger');
                    return;
                }

                // Populate item info
                document.getElementById('split-item-info').innerHTML = `
            <strong>${item.code_item}</strong> - ${item.material_name}<br>
            Quantity Total: ${parseInt(item.quantity).toLocaleString()} PCS<br>
            Mesin Asal: ${item.code_machine}
        `;

                // Populate target machine dropdown
                const targetSelect = document.getElementById('split-target-machine');
                targetSelect.innerHTML = '<option value="">-- Pilih Mesin --</option>';
                allMachines.forEach(machine => {
                    if (machine.Code !== item.code_machine) {
                        const option = document.createElement('option');
                        option.value = machine.Code;
                        option.textContent = `${machine.Code} - ${machine.Description}`;
                        targetSelect.appendChild(option);
                    }
                });

                // Set default quantity and calculate remaining
                const splitQuantityInput = document.getElementById('split-quantity');
                const remainingInput = document.getElementById('remaining-quantity');

                splitQuantityInput.max = item.quantity - 1;
                splitQuantityInput.value = Math.floor(item.quantity / 2);
                remainingInput.value = item.quantity - splitQuantityInput.value;

                // Update remaining quantity when split quantity changes
                splitQuantityInput.addEventListener('input', function() {
                    const splitQty = parseInt(this.value) || 0;
                    const remaining = item.quantity - splitQty;
                    remainingInput.value = remaining;
                });

                // Store item ID for confirmation
                document.getElementById('confirm-split-btn').dataset.itemId = itemId;

                // Set item ID for history button in modal
                const historyBtn = document.querySelector('#splitItemModal .view-history-from-modal');
                if (historyBtn) {
                    historyBtn.dataset.itemId = itemId;
                }

                $('#splitItemModal').modal('show');
            },

            showMergeItemModal(itemId) {
                const sourceItem = timelineData.find(i => i.id == itemId);
                if (!sourceItem) {
                    Utils.showToast('Item tidak ditemukan', 'danger');
                    return;
                }

                // Populate source item info
                document.getElementById('merge-source-info').innerHTML = `
                    <strong>${sourceItem.code_item}</strong> - ${sourceItem.material_name}<br>
                    Mesin: ${sourceItem.code_machine}<br>
                    Quantity: ${parseInt(sourceItem.quantity).toLocaleString()} PCS
                `;

                // Find potential merge targets (same code_item, different machine)
                const mergeTargets = timelineData.filter(item =>
                    item.id !== itemId &&
                    item.code_item === sourceItem.code_item &&
                    item.code_machine !== sourceItem.code_machine
                );

                if (mergeTargets.length === 0) {
                    document.getElementById('merge-target-list').innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                <i class="mdi mdi-information-outline"></i>
                                Tidak ada item dengan kode yang sama di mesin lain
                            </td>
                        </tr>
                    `;
                    document.getElementById('confirm-merge-btn').disabled = true;
                } else {
                    // Populate merge targets list
                    const tbody = document.getElementById('merge-target-list');
                    tbody.innerHTML = '';

                    mergeTargets.forEach((targetItem, index) => {
                        const radioId = `merge-target-${targetItem.id}`;
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="text-center">
                                <input type="radio" id="${radioId}" name="merge-target" value="${targetItem.id}"
                                       onchange="TableRenderer.updateMergeTargetSelection()">
                                <label for="${radioId}" class="mb-0"></label>
                            </td>
                            <td>
                                <label for="${radioId}" class="mb-0 w-100 cursor-pointer">
                                    <strong>${targetItem.code_item}</strong>
                                </label>
                            </td>
                            <td>
                                <label for="${radioId}" class="mb-0 w-100 cursor-pointer">
                                    ${targetItem.code_machine}
                                </label>
                            </td>
                            <td>
                                <label for="${radioId}" class="mb-0 w-100 cursor-pointer">
                                    ${parseInt(targetItem.quantity).toLocaleString()} PCS
                                </label>
                            </td>
                            <td>
                                <label for="${radioId}" class="mb-0 w-100 cursor-pointer">
                                    <span class="badge badge-info">${targetItem.flag_status || 'Pending'}</span>
                                </label>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });

                    // Enable confirm button
                    document.getElementById('confirm-merge-btn').disabled = false;
                }

                // Store source item ID for confirmation
                document.getElementById('confirm-merge-btn').dataset.sourceItemId = itemId;

                // Set item ID for history button in modal
                const historyBtn = document.querySelector('#mergeItemModal .view-history-from-modal');
                if (historyBtn) {
                    historyBtn.dataset.itemId = itemId;
                }

                $('#mergeItemModal').modal('show');
            },

            updateMergeTargetSelection() {
                const selectedTarget = document.querySelector('input[name="merge-target"]:checked');
                const confirmBtn = document.getElementById('confirm-merge-btn');

                if (selectedTarget) {
                    const targetItem = timelineData.find(i => i.id == selectedTarget.value);
                    if (targetItem) {
                        // Update target info display
                        document.getElementById('merge-target-info').innerHTML = `
                            <strong>${targetItem.code_item}</strong> - ${targetItem.material_name}<br>
                            Mesin: ${targetItem.code_machine}<br>
                            Quantity: ${parseInt(targetItem.quantity).toLocaleString()} PCS
                        `;

                        confirmBtn.disabled = false;
                    }
                } else {
                    confirmBtn.disabled = true;
                }
            },

            canMergeItem(item) {
                // Check if there are other items with same code_item in different machines
                const mergeTargets = timelineData.filter(otherItem =>
                    otherItem.id !== item.id &&
                    otherItem.code_item === item.code_item &&
                    otherItem.code_machine !== item.code_machine
                );

                return mergeTargets.length > 0;
            },

            showPriorityChangeModal(itemId, machineCode, currentPriority, action) {
                const item = timelineData.find(i => i.id == itemId);
                if (!item) {
                    Utils.showToast('Item tidak ditemukan', 'error');
                    return;
                }

                const machineData = timelineData.filter(i => i.code_machine === machineCode);

                // Debug: Log current state
                console.log(`🔍 Priority change modal debug:`);
                console.log(`📊 Machine: ${machineCode}`);
                console.log(`📊 Current priority: ${currentPriority}`);
                console.log(`📊 Action: ${action}`);
                console.log(`📊 Machine data length: ${machineData.length}`);
                console.log(`📊 Machine data:`, machineData.map((item, idx) =>
                    `${idx}. ${item.code_item} (ID: ${item.id})`));

                // Calculate new priority with bounds checking
                let newPriority;
                if (action === 'up') {
                    newPriority = currentPriority - 1;
                    if (newPriority < 0) {
                        Utils.showToast('Item sudah berada di prioritas tertinggi', 'warning');
                        return;
                    }
                } else {
                    newPriority = currentPriority + 1;
                    if (newPriority >= machineData.length) {
                        Utils.showToast('Item sudah berada di prioritas terendah', 'warning');
                        return;
                    }
                }

                const targetItem = machineData[newPriority];
                console.log(`🎯 New priority: ${newPriority}`);
                console.log(`🎯 Target item:`, targetItem);

                if (!targetItem) {
                    console.error(`❌ Target item not found at index ${newPriority}`);
                    Utils.showToast('Tidak dapat mengubah prioritas: item tujuan tidak ditemukan', 'error');
                    return;
                }

                // Populate modal details
                const detailsDiv = document.getElementById('priority-change-details');
                detailsDiv.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Item yang Akan Diubah:</h6>
                            <div class="alert alert-info">
                                <strong>${item.code_item}</strong> - ${item.material_name}<br>
                                <strong>Prioritas Saat Ini:</strong> ${currentPriority + 1}<br>
                                <strong>Mesin:</strong> ${machineCode}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-warning">Item yang Akan Ditukar:</h6>
                            <div class="alert alert-warning">
                                <strong>${targetItem.code_item}</strong> - ${targetItem.material_name}<br>
                                <strong>Prioritas Saat Ini:</strong> ${newPriority + 1}<br>
                                <strong>Mesin:</strong> ${machineCode}
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-warning mt-3">
                        <i class="mdi mdi-clock-outline"></i>
                        <strong>Dampak Perubahan:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Urutan produksi akan berubah</li>
                            <li>Waktu mulai dan selesai akan dihitung ulang</li>
                            <li>Item pertama akan selalu mulai jam 8:00</li>
                        </ul>
                    </div>
                `;

                // Store data in modal for confirmation
                const modal = document.getElementById('priorityChangeModal');
                modal.dataset.action = action;
                modal.dataset.itemId = itemId;
                modal.dataset.machineCode = machineCode;
                modal.dataset.currentPriority = currentPriority;

                // Show modal
                $('#priorityChangeModal').modal('show');
            },









            /**
             * Format date for display
             */
            formatDate(dateString) {
                if (!dateString) return '-';

                try {
                    const date = new Date(dateString);
                    if (isNaN(date.getTime())) return dateString;

                    return date.toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit'
                    });
                } catch (error) {
                    return dateString;
                }
            },

            /**
             * Get status badge class based on status
             */
            getStatusBadgeClass(status) {
                const statusLower = (status || '').toLowerCase();
                switch (statusLower) {
                    case 'completed':
                    case 'selesai':
                    case 'finish':
                        return 'success';
                    case 'in_progress':
                    case 'progress':
                    case 'sedang_berlangsung':
                        return 'warning';
                    case 'pending':
                    case 'menunggu':
                        return 'secondary';
                    case 'cancelled':
                    case 'dibatalkan':
                        return 'danger';
                    default:
                        return 'secondary';
                }
            },

            /**
             * Show maintenance schedule modal
             */
            showMaintenanceScheduleModal(machineCode = null) {
                console.log('🔧 Opening maintenance schedule modal for machine:', machineCode);

                // Populate machine filter dropdown
                const machineFilter = document.getElementById('maintenance-machine-filter');
                machineFilter.innerHTML = '<option value="">Semua Mesin</option>';

                allMachines.forEach(machine => {
                    const option = document.createElement('option');
                    option.value = machine.Code;
                    option.textContent = `${machine.Code} - ${machine.Description}`;
                    machineFilter.appendChild(option);
                });

                // Pre-fill machine filter if machineCode is provided
                if (machineCode) {
                    machineFilter.value = machineCode;
                    machineFilter.disabled = true; // Disable change if specific machine

                    // Update modal title to show selected machine
                    const machineLabel = document.getElementById('selected-machine-label');
                    if (machineLabel) {
                        const machine = allMachines.find(m => m.Code === machineCode);
                        const machineName = machine ? machine.Description : machineCode;
                        machineLabel.innerHTML =
                            ` - <span class="badge badge-light">${machineCode}: ${machineName}</span>`;
                    }
                } else {
                    machineFilter.disabled = false; // Enable change if no specific machine

                    // Clear machine label
                    const machineLabel = document.getElementById('selected-machine-label');
                    if (machineLabel) {
                        machineLabel.innerHTML = '';
                    }
                }

                // Load maintenance data
                this.loadMaintenanceData(machineCode);

                // Setup filter events
                this.setupMaintenanceFilterEvents();

                // Setup add to plan button event
                this.setupAddToPlanButton();

                // Show modal
                $('#maintenanceScheduleModal').modal('show');
            },

            /**
             * Show Add Plan Manual Modal
             */
            showAddPlanManualModal(machineCode) {
                console.log('➕ Showing Add Plan Manual Modal for machine:', machineCode);

                // Get machine info
                const machine = allMachines.find(m => m.Code === machineCode);
                const machineName = machine ? machine.Description : machineCode;

                // Set machine code in modal (readonly)
                document.getElementById('manual-machine-code').value = `${machineCode} - ${machineName}`;

                // Clear all form fields
                document.getElementById('manual-code-item').value = '';
                document.getElementById('manual-material-name').value = '';
                document.getElementById('manual-wo-docno').value = '';
                document.getElementById('manual-quantity').value = '';
                document.getElementById('manual-delivery-date').value = '';
                document.getElementById('manual-process').value = 'CTK';
                document.getElementById('manual-setup-time').value = '0';
                document.getElementById('manual-break-time').value = '0';
                document.getElementById('manual-up-cetak').value = '1';

                // Set default delivery date to today
                const today = new Date();
                const deliveryDateStr = today.toISOString().split('T')[0];
                document.getElementById('manual-delivery-date').value = deliveryDateStr;

                // Store machine code for later use
                document.getElementById('addPlanManualModal').dataset.machineCode = machineCode;

                // Setup event listener for code item input to auto-fill material name
                this.setupMaterialNameAutoFill();

                // Show modal
                $('#addPlanManualModal').modal('show');
            },

            /**
             * Setup auto-fill material name from code item
             */
            setupMaterialNameAutoFill() {
                const codeItemInput = document.getElementById('manual-code-item');
                const materialNameInput = document.getElementById('manual-material-name');

                if (!codeItemInput || !materialNameInput) return;

                // Remove existing event listeners by cloning
                const newCodeItemInput = codeItemInput.cloneNode(true);
                codeItemInput.parentNode.replaceChild(newCodeItemInput, codeItemInput);

                // Add event listener for blur/change event
                newCodeItemInput.addEventListener('blur', function() {
                    const materialCode = this.value.trim();
                    if (!materialCode) {
                        materialNameInput.value = '';
                        return;
                    }

                    // Show loading state
                    materialNameInput.value = 'Mencari...';
                    materialNameInput.disabled = true;

                    // AJAX request to get material name
                    $.ajax({
                        url: '{{ route("process.search-materials") }}',
                        method: 'GET',
                        data: {
                            q: materialCode,
                            exact: true // Flag for exact match
                        },
                        success: function(response) {
                            if (response.results && response.results.length > 0) {
                                // Find exact match first
                                let material = response.results.find(m => m.MaterialCode === materialCode);
                                if (!material) {
                                    // If no exact match, use first result
                                    material = response.results[0];
                                }
                                materialNameInput.value = material.MaterialName || material.text || '';
                            } else {
                                materialNameInput.value = '';
                                Utils.showToast('⚠️ Kode material tidak ditemukan di database', 'warning');
                            }
                            materialNameInput.disabled = false;
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching material:', error);
                            materialNameInput.value = '';
                            materialNameInput.disabled = false;
                            Utils.showToast('❌ Error mengambil data material: ' + error, 'error');
                        }
                    });
                });
            },

            /**
             * Load monitoring data (OPEN Job Orders from remote database)
             * Returns array of job order numbers for comparison
             */
            async loadMonitoringData(machineCode = null) {
                try {
                    const tableBody = document.getElementById('monitoring-jo-table-body');
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                <i class="mdi mdi-loading mdi-spin"></i> Memuat data...
                            </td>
                        </tr>
                    `;

                    // Build URL with machine_code parameter if provided
                    let url = '/sipo/process/get-open-job-orders';
                    if (machineCode) {
                        url += `?machine_code=${encodeURIComponent(machineCode)}`;
                    }

                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const result = await response.json();
                    let monitoringJobOrders = []; // Array untuk menyimpan list JO dari monitoring

                    if (result.success && result.data) {
                        const openJobOrders = result.data;
                        tableBody.innerHTML = '';

                        if (openJobOrders.length === 0) {
                            tableBody.innerHTML = `
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        <i class="mdi mdi-information"></i> Tidak ada Job Order dengan status ORDER
                                    </td>
                                </tr>
                            `;
                            document.getElementById('monitoring-jo-count').textContent = '0';
                        } else {
                            openJobOrders.forEach((jo, index) => {
                                // Simpan JO ke array untuk perbandingan
                                if (jo.jo) {
                                    monitoringJobOrders.push(jo.jo);
                                }

                                const row = document.createElement('tr');
                                const datetime = jo.datetimes || (jo.datetime ? new Date(jo.datetime * 1000).toLocaleString('id-ID') : '-');
                                row.innerHTML = `
                                    <td class="text-center">${index + 1}</td>
                                    <td><span class="badge badge-primary">${jo.jo || '-'}</span></td>
                                    <td class="text-right">${parseInt(jo.totprod || 0).toLocaleString()}</td>
                                    <td><span class="badge badge-success">${jo.status || 'ORDER'}</span></td>
                                    <td>${jo.username || '-'}</td>
                                    <td><small>${datetime}</small></td>
                                `;
                                tableBody.appendChild(row);
                            });
                            document.getElementById('monitoring-jo-count').textContent = openJobOrders.length;
                        }
                    } else {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="6" class="text-center text-danger">
                                    <i class="mdi mdi-alert"></i> Gagal memuat data monitoring
                                </td>
                            </tr>
                        `;
                        document.getElementById('monitoring-jo-count').textContent = '0';
                    }

                    // Return array JO untuk digunakan di fungsi lain
                    return monitoringJobOrders;
                } catch (error) {
                    console.error('❌ Error loading monitoring data:', error);
                    const tableBody = document.getElementById('monitoring-jo-table-body');
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center text-danger">
                                <i class="mdi mdi-alert"></i> Error: ${error.message}
                            </td>
                        </tr>
                    `;
                    document.getElementById('monitoring-jo-count').textContent = '0';
                    return [];
                }
            },

            /**
             * Show Send Job Order Modal
             */
            async showSendJobOrderModal(machineCode) {
                console.log('📤 Showing Send Job Order Modal for machine:', machineCode);

                // Load monitoring data first dengan machine_code dan ambil list JO
                const monitoringJobOrders = await TableRenderer.loadMonitoringData(machineCode);

                // Get machine info
                const machine = allMachines.find(m => m.Code === machineCode);
                const machineName = machine ? machine.Description : machineCode;

                // Set machine code in modal
                document.getElementById('send-jo-machine-code').textContent = `${machineCode} - ${machineName}`;

                // Get today's date (start of day)
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const todayEnd = new Date(today);
                todayEnd.setHours(23, 59, 59, 999);

                // Get all items for this machine from timelineData that have Job Order
                // and are scheduled for today (start_jam or end_jam is today)
                const machineItems = timelineData.filter(item => {
                    if (item.code_machine !== machineCode || !(item.job_order || item.job_order_no)) {
                        return false;
                    }

                    // Check if start_jam or end_jam is today
                    if (item.start_jam) {
                        const startDate = new Date(item.start_jam);
                        startDate.setHours(0, 0, 0, 0);
                        if (startDate.getTime() === today.getTime()) {
                            return true;
                        }
                    }

                    if (item.end_jam) {
                        const endDate = new Date(item.end_jam);
                        endDate.setHours(0, 0, 0, 0);
                        if (endDate.getTime() === today.getTime()) {
                            return true;
                        }
                    }

                    return false;
                });

                console.log('📋 Job Orders found for machine today:', machineCode, machineItems);

                // Populate table
                const tableBody = document.getElementById('send-jo-table-body');
                tableBody.innerHTML = '';

                if (machineItems.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                <i class="mdi mdi-information"></i> Tidak ada Job Order untuk dicetak hari ini di mesin ini
                            </td>
                        </tr>
                    `;
                    document.getElementById('send-jo-total-count').textContent = '0';
                    document.getElementById('send-jo-selected-count').textContent = '0';
                } else {
                    machineItems.forEach((item, index) => {
                        const jobOrder = item.job_order || item.job_order_no || '-';
                        const status = item.job_order_status || item.status_item || 'NEW';
                        const startTime = item.start_jam ? Utils.formatDateTime(item.start_jam) : '-';
                        const endTime = item.end_jam ? Utils.formatDateTime(item.end_jam) : '-';

                        // Cek apakah JO ini sudah ada di monitoring
                        const isInMonitoring = monitoringJobOrders && monitoringJobOrders.includes(jobOrder);
                        const badgeClass = isInMonitoring ? 'badge-warning' : 'badge-success';
                        const indicatorIcon = isInMonitoring ? '<i class="mdi mdi-check-circle text-warning ml-1" title="Sudah ada di database"></i>' : '';

                        const row = document.createElement('tr');
                        row.setAttribute('data-item-id', item.id);
                        // Tambahkan class untuk highlight jika sudah ada di monitoring
                        if (isInMonitoring) {
                            row.classList.add('table-warning');
                        }
                        row.innerHTML = `
                            <td class="text-center">
                                <div class="form-check">
                                    <input class="form-check-input job-order-checkbox" type="checkbox"
                                        id="jo-checkbox-${item.id}"
                                        data-item-id="${item.id}"
                                        checked>
                                    <label class="form-check-label" for="jo-checkbox-${item.id}"></label>
                                </div>
                            </td>
                            <td class="text-center">${index + 1}</td>
                            <td>${item.code_item || '-'}</td>
                            <td>${item.material_name || '-'}</td>
                            <td>
                                <span class="badge ${badgeClass}">${jobOrder}</span>
                                ${indicatorIcon}
                            </td>
                            <td class="text-right">${parseInt(item.quantity || 0).toLocaleString()}</td>
                        `;
                        tableBody.appendChild(row);
                    });

                    document.getElementById('send-jo-total-count').textContent = machineItems.length;

                    // Setup select all functionality
                    const selectAllCheckbox = document.getElementById('select-all-job-order');
                    const itemCheckboxes = document.querySelectorAll('.job-order-checkbox');

                    // Function to update selected count
                    const updateSelectedCount = () => {
                        const selectedCount = document.querySelectorAll('.job-order-checkbox:checked').length;
                        document.getElementById('send-jo-selected-count').textContent = selectedCount;
                    };

                    // Select all checkbox handler
                    if (selectAllCheckbox) {
                        selectAllCheckbox.addEventListener('change', function() {
                            itemCheckboxes.forEach(checkbox => {
                                checkbox.checked = this.checked;
                            });
                            updateSelectedCount();
                        });
                    }

                    // Individual checkbox handler
                    itemCheckboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            updateSelectedCount();
                            // Update select all checkbox state
                            if (selectAllCheckbox) {
                                const allChecked = Array.from(itemCheckboxes).every(cb => cb.checked);
                                const someChecked = Array.from(itemCheckboxes).some(cb => cb.checked);
                                selectAllCheckbox.checked = allChecked;
                                selectAllCheckbox.indeterminate = someChecked && !allChecked;
                            }
                        });
                    });

                    // Initialize select all as checked
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = true;
                    }
                    updateSelectedCount();
                }

                // Store machine code for later use
                document.getElementById('sendJobOrderModal').dataset.machineCode = machineCode;

                // Show modal
                $('#sendJobOrderModal').modal('show');
            },

            /**
             * Send Job Order to Database
             */
            async sendJobOrderToDatabase(machineCode) {
                try {
                    // Get selected checkboxes from the modal
                    const selectedCheckboxes = document.querySelectorAll('.job-order-checkbox:checked');

                    if (selectedCheckboxes.length === 0) {
                        Utils.showToast('❌ Pilih minimal satu Job Order yang akan dikirim', 'warning');
                        return;
                    }

                    // Get selected item IDs
                    const selectedItemIds = Array.from(selectedCheckboxes).map(cb => cb.dataset.itemId);

                    // Get today's date (start of day)
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    // Get all items for this machine with Job Order that are scheduled for today
                    // AND are selected in the checkbox
                    const machineItems = timelineData.filter(item => {
                        // Check if item is selected
                        if (!selectedItemIds.includes(String(item.id))) {
                            return false;
                        }

                        if (item.code_machine !== machineCode || !(item.job_order || item.job_order_no)) {
                            return false;
                        }

                        // Check if start_jam or end_jam is today
                        if (item.start_jam) {
                            const startDate = new Date(item.start_jam);
                            startDate.setHours(0, 0, 0, 0);
                            if (startDate.getTime() === today.getTime()) {
                                return true;
                            }
                        }

                        if (item.end_jam) {
                            const endDate = new Date(item.end_jam);
                            endDate.setHours(0, 0, 0, 0);
                            if (endDate.getTime() === today.getTime()) {
                                return true;
                            }
                        }

                        return false;
                    });

                    if (machineItems.length === 0) {
                        Utils.showToast('❌ Tidak ada Job Order terpilih yang akan dikirim', 'warning');
                        return;
                    }

                    // Prepare data for sending
                    const jobOrderData = machineItems.map(item => ({
                        id: item.id,
                        code_item: item.code_item,
                        material_name: item.material_name,
                        wo_docno: item.wo_docno,
                        job_order: item.job_order || item.job_order_no,
                        status_item: item.job_order_status || item.status_item || 'NEW',
                        quantity: item.quantity,
                        start_jam: item.start_jam,
                        end_jam: item.end_jam,
                        code_machine: machineCode
                    }));

                    console.log('📤 Sending Job Order data:', jobOrderData);

                    // Send to backend
                    const response = await fetch('/sipo/process/send-job-order-to-machine', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            machine_code: machineCode,
                            job_orders: jobOrderData
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        Utils.showToast(`✅ ${result.message || 'Job Order berhasil dikirim ke database mesin'}`, 'success');
                        // Refresh monitoring data setelah berhasil kirim
                        TableRenderer.loadMonitoringData();
                        $('#sendJobOrderModal').modal('hide');
                    } else {
                        Utils.showToast(`❌ ${result.message || 'Gagal mengirim Job Order'}`, 'error');
                    }
                } catch (error) {
                    console.error('❌ Error sending Job Order:', error);
                    Utils.showToast('❌ Error: ' + error.message, 'error');
                }
            },

            /**
             * Add Plan Manual to timelineData
             */
            addPlanManual() {
                try {
                    const modalElement = document.getElementById('addPlanManualModal');
                    if (!modalElement) {
                        Utils.showToast('❌ Modal tidak ditemukan', 'error');
                        console.error('Modal element not found');
                        return;
                    }

                    const machineCode = modalElement.dataset.machineCode;
                    if (!machineCode) {
                        Utils.showToast('❌ Kode Mesin tidak ditemukan. Silakan tutup modal dan coba lagi.', 'error');
                        console.error('Machine code not found in modal dataset');
                        return;
                    }

                    // Get form values
                    const codeItemInput = document.getElementById('manual-code-item');
                    const materialNameInput = document.getElementById('manual-material-name');
                    const quantityInput = document.getElementById('manual-quantity');
                    const deliveryDateInput = document.getElementById('manual-delivery-date');
                    const processInput = document.getElementById('manual-process');
                    const setupTimeInput = document.getElementById('manual-setup-time');
                    const breakTimeInput = document.getElementById('manual-break-time');
                    const upCetakInput = document.getElementById('manual-up-cetak');
                    const woDocnoInput = document.getElementById('manual-wo-docno');

                    if (!codeItemInput || !materialNameInput || !quantityInput) {
                        Utils.showToast('❌ Form tidak lengkap. Silakan refresh halaman.', 'error');
                        console.error('Form inputs not found');
                        return;
                    }

                    const codeItem = codeItemInput.value.trim();
                    const materialName = materialNameInput.value.trim();
                    const woDocno = woDocnoInput ? woDocnoInput.value.trim() : '';
                    const quantity = parseInt(quantityInput.value) || 0;
                    const deliveryDate = deliveryDateInput ? deliveryDateInput.value : '';
                    const process = processInput ? processInput.value : 'CTK';
                    const setupTime = setupTimeInput ? parseFloat(setupTimeInput.value) || 0 : 0;
                    const breakTime = breakTimeInput ? parseFloat(breakTimeInput.value) || 0 : 0;
                    const upCetak = upCetakInput ? parseInt(upCetakInput.value) || 1 : 1;

                    // Validation
                    if (!codeItem) {
                        Utils.showToast('❌ Kode Item harus diisi', 'error');
                        codeItemInput.focus();
                        return;
                    }

                    if (!materialName || materialName === 'Mencari...') {
                        Utils.showToast('❌ Nama Material harus diisi. Pastikan kode item valid dan sudah di-blur.', 'error');
                        codeItemInput.focus();
                        return;
                    }

                    if (quantity <= 0) {
                        Utils.showToast('❌ Quantity harus lebih dari 0', 'error');
                        quantityInput.focus();
                        return;
                    }

                    // Get machine data for capacity
                    const machine = allMachines.find(m => m.Code === machineCode);
                    const capacityPerHour = machine ? (machine.CapacityPerHour || 1000) : 1000;

                    // Calculate automatic start and end time
                    // Find the last item in this machine
                    const machineItems = timelineData.filter(item => item.code_machine === machineCode);
                    let startTime;

                    if (machineItems.length > 0) {
                        // Sort by end_jam to find the last item
                        machineItems.sort((a, b) => {
                            const aEnd = a.end_jam ? new Date(a.end_jam).getTime() : 0;
                            const bEnd = b.end_jam ? new Date(b.end_jam).getTime() : 0;
                            return bEnd - aEnd; // Descending order
                        });

                        const lastItem = machineItems[0];
                        if (lastItem && lastItem.end_jam) {
                            // Start from the end of the last item
                            startTime = new Date(lastItem.end_jam);
                        } else {
                            // Fallback: start from today 08:00
                            startTime = new Date();
                            startTime.setHours(8, 0, 0, 0);
                        }
                    } else {
                        // No items in machine, start from today 08:00
                        startTime = new Date();
                        startTime.setHours(8, 0, 0, 0);
                    }

                    // Add setup time
                    const setupTimeMs = setupTime * 60 * 60 * 1000;
                    const actualStartTime = new Date(startTime.getTime() + setupTimeMs);

                    // Calculate duration based on quantity and capacity
                    const durationHours = quantity / capacityPerHour;
                    const durationMs = durationHours * 60 * 60 * 1000;
                    const breakTimeMs = breakTime * 60 * 60 * 1000;

                    // Calculate end time (start + duration + break time)
                    const endTime = new Date(actualStartTime.getTime() + durationMs + breakTimeMs);

                    console.log('📊 Auto-calculated times:', {
                        machineCode,
                        quantity,
                        capacityPerHour,
                        durationHours: durationHours.toFixed(2),
                        setupTime,
                        breakTime,
                        startTime: actualStartTime.toISOString(),
                        endTime: endTime.toISOString()
                    });

                    // Create new plan item
                    // Generate integer ID (not float) for frontend tracking
                    const tempId = Math.floor(Date.now() / 1000) * 10000 + Math.floor(Math.random() * 10000);
                    const newItem = {
                        id: tempId, // Integer ID for frontend tracking
                        code_item: codeItem,
                        material_name: materialName,
                        wo_docno: woDocno || null,
                        so_docno: woDocno || null,
                        quantity: quantity,
                        delivery_date: deliveryDate ? new Date(deliveryDate).toISOString().split('T')[0] : null,
                        process: process,
                        code_machine: machineCode,
                        start_jam: actualStartTime.toISOString(),
                        end_jam: endTime.toISOString(),
                        setup_time: setupTime,
                        break_time: breakTime,
                        up_cetak: upCetak,
                        flag_status: 'PENDING',
                        is_manual: true, // Mark as manual entry
                        created_at: new Date().toISOString(),
                        updated_at: new Date().toISOString()
                    };

                    // Add to timelineData (will be at the end, priority last)
                    timelineData.push(newItem);

                    // Mark as changed and save to history
                    ChangeTracker.markChanged(`Add Plan Manual: ${codeItem}`);

                    console.log('✅ Plan manual ditambahkan:', newItem);

                    // Reset form
                    if (codeItemInput) codeItemInput.value = '';
                    if (materialNameInput) materialNameInput.value = '';
                    if (woDocnoInput) woDocnoInput.value = '';
                    if (quantityInput) quantityInput.value = '';
                    if (deliveryDateInput) {
                        const today = new Date();
                        deliveryDateInput.value = today.toISOString().split('T')[0];
                    }
                    if (processInput) processInput.value = 'CTK';
                    if (setupTimeInput) setupTimeInput.value = '0';
                    if (breakTimeInput) breakTimeInput.value = '0';
                    if (upCetakInput) upCetakInput.value = '1';

                    // Close modal
                    $('#addPlanManualModal').modal('hide');

                    // Show success message
                    Utils.showToast(`✅ Plan manual berhasil ditambahkan ke mesin ${machineCode} (prioritas terakhir)`, 'success');

                    // Refresh table (plan manual akan muncul di urutan paling akhir)
                    setTimeout(() => {
                        TableRenderer.renderTable();
                    }, 300);
                } catch (error) {
                    console.error('❌ Error adding plan manual:', error);
                    Utils.showToast(`❌ Error: ${error.message}`, 'error');
                }
            },

            /**
             * Show View History Modal for specific item or all history
             */
            showViewHistoryModal(itemId = null) {
                // If itemId is null, show all plan history
                if (itemId === null) {
                    document.getElementById('history-item-info').innerHTML = `
                        <strong><i class="mdi mdi-history"></i> Semua History Perubahan Plan</strong><br>
                        <small>Menampilkan semua perubahan yang terjadi pada plan production</small>
                    `;

                    // Get all history entries
                    const allHistory = [...planHistory].sort((a, b) => {
                        return new Date(b.timestamp) - new Date(a.timestamp);
                    }).slice(0, 50); // Last 50 entries

                    // Populate history table
                    const tbody = document.getElementById('history-table-body');
                    if (allHistory.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    <i class="mdi mdi-information-outline"></i>
                                    <br>Tidak ada history untuk plan ini
                                </td>
                            </tr>
                        `;
                    } else {
                        tbody.innerHTML = allHistory.map((entry, index) => {
                            const date = new Date(entry.timestamp);
                            const changedItems = entry.changed_items || [];
                            const changedItemsText = changedItems.length > 0
                                ? `<br><small class="text-muted">${changedItems.map(ci => `${ci.item} (${ci.action})`).join(', ')}</small>`
                                : '';
                            return `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${date.toLocaleString('id-ID')}</td>
                                    <td>${entry.user || 'System'}</td>
                                    <td><span class="badge badge-info">${entry.action || 'Unknown'}</span></td>
                                    <td><small>${entry.item_count || 0} item(s) dalam plan${changedItemsText}</small></td>
                                </tr>
                            `;
                        }).join('');
                    }

                    $('#viewHistoryModal').modal('show');
                    return;
                }

                // Item-specific history
                const item = timelineData.find(i => i.id == itemId);
                if (!item) {
                    Utils.showToast('Item tidak ditemukan', 'danger');
                    return;
                }

                // Populate item info
                document.getElementById('history-item-info').innerHTML = `
                    <strong>${item.code_item}</strong> - ${item.material_name}<br>
                    <small>WO: ${item.wo_docno || '-'} | Mesin: ${item.code_machine} | Quantity: ${parseInt(item.quantity).toLocaleString()} PCS</small>
                `;

                // Get item-specific history
                const itemHistory = planHistory.filter(h => {
                    // Try to find if this action affected this item
                    // Check if action mentions this item's code_item or wo_docno
                    return h.action && (
                        h.action.includes(item.code_item) ||
                        h.action.includes(item.wo_docno) ||
                        (h.changed_items && h.changed_items.some(ci => ci.item === item.code_item))
                    );
                });

                // Also get relevant history entries (all history if item-specific is empty)
                const relevantHistory = itemHistory.length > 0
                    ? itemHistory
                    : planHistory.filter(h => h.timestamp && h.item_count !== undefined).slice(0, 20);

                // Combine and sort by timestamp
                const allHistory = [...relevantHistory].sort((a, b) => {
                    return new Date(b.timestamp) - new Date(a.timestamp);
                });

                // Populate history table
                const tbody = document.getElementById('history-table-body');
                if (allHistory.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                <i class="mdi mdi-information-outline"></i>
                                <br>Tidak ada history untuk item ini
                            </td>
                        </tr>
                    `;
                } else {
                    tbody.innerHTML = allHistory.map((entry, index) => {
                        const date = new Date(entry.timestamp);
                        return `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${date.toLocaleString('id-ID')}</td>
                                <td>${entry.user || 'System'}</td>
                                <td><span class="badge badge-info">${entry.action || 'Unknown'}</span></td>
                                <td><small>${entry.item_count || 0} item(s) dalam plan</small></td>
                            </tr>
                        `;
                    }).join('');
                }

                $('#viewHistoryModal').modal('show');
            },

            /**
             * Show Compare Versions Modal
             */
            showCompareVersionsModal() {
                // Populate version dropdowns from history stack
                const version1Select = document.getElementById('compare-version-1');
                const version2Select = document.getElementById('compare-version-2');

                version1Select.innerHTML = '<option value="">-- Pilih Versi --</option>';
                version2Select.innerHTML = '<option value="current">Versi Saat Ini</option>';

                // Add history entries as options
                historyStack.forEach((entry, index) => {
                    const date = new Date(entry.timestamp);
                    const option1 = document.createElement('option');
                    option1.value = index;
                    option1.textContent = `${index + 1}. ${entry.action} - ${date.toLocaleString('id-ID')}`;
                    version1Select.appendChild(option1);

                    const option2 = document.createElement('option');
                    option2.value = index;
                    option2.textContent = `${index + 1}. ${entry.action} - ${date.toLocaleString('id-ID')}`;
                    version2Select.appendChild(option2);
                });

                // Clear compare result
                document.getElementById('compare-result').innerHTML = '';

                $('#compareVersionsModal').modal('show');
            },

            /**
             * Compare Plan Versions
             */
            comparePlanVersions(version1Index, version2Index) {
                let version1, version2;

                // Get version 1
                if (version1Index === 'current' || version1Index === '') {
                    version1 = timelineData;
                } else {
                    const index1 = parseInt(version1Index);
                    if (index1 >= 0 && index1 < historyStack.length) {
                        version1 = historyStack[index1].state;
                    } else {
                        Utils.showToast('Versi 1 tidak valid', 'error');
                        return;
                    }
                }

                // Get version 2
                if (version2Index === 'current') {
                    version2 = timelineData;
                } else {
                    const index2 = parseInt(version2Index);
                    if (index2 >= 0 && index2 < historyStack.length) {
                        version2 = historyStack[index2].state;
                    } else {
                        Utils.showToast('Versi 2 tidak valid', 'error');
                        return;
                    }
                }

                // Perform comparison
                const comparison = this.performComparison(version1, version2);

                // Display results
                this.displayComparisonResult(comparison);
            },

            /**
             * Perform comparison between two versions
             */
            performComparison(version1, version2) {
                const result = {
                    added: [],
                    removed: [],
                    modified: [],
                    unchanged: []
                };

                // Create maps for easy lookup
                const map1 = new Map();
                const map2 = new Map();

                version1.forEach(item => map1.set(item.id, item));
                version2.forEach(item => map2.set(item.id, item));

                // Find added items (in version2 but not in version1)
                version2.forEach(item => {
                    if (!map1.has(item.id)) {
                        result.added.push(item);
                    }
                });

                // Find removed items (in version1 but not in version2)
                version1.forEach(item => {
                    if (!map2.has(item.id)) {
                        result.removed.push(item);
                    }
                });

                // Find modified items
                version1.forEach(item1 => {
                    const item2 = map2.get(item1.id);
                    if (item2) {
                        // Compare key fields
                        const changes = [];
                        if (item1.code_machine !== item2.code_machine) {
                            changes.push(`Mesin: ${item1.code_machine} → ${item2.code_machine}`);
                        }
                        if (item1.quantity !== item2.quantity) {
                            changes.push(`Quantity: ${item1.quantity} → ${item2.quantity}`);
                        }
                        if (item1.start_jam !== item2.start_jam) {
                            changes.push(`Start: ${Utils.formatDateTime(item1.start_jam)} → ${Utils.formatDateTime(item2.start_jam)}`);
                        }
                        if (item1.end_jam !== item2.end_jam) {
                            changes.push(`End: ${Utils.formatDateTime(item1.end_jam)} → ${Utils.formatDateTime(item2.end_jam)}`);
                        }
                        if (item1.flag_status !== item2.flag_status) {
                            changes.push(`Status: ${item1.flag_status} → ${item2.flag_status}`);
                        }

                        if (changes.length > 0) {
                            result.modified.push({
                                item: item1,
                                changes: changes
                            });
                        } else {
                            result.unchanged.push(item1);
                        }
                    }
                });

                return result;
            },

            /**
             * Display comparison result
             */
            displayComparisonResult(comparison) {
                const resultDiv = document.getElementById('compare-result');

                let html = `
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-success">${comparison.added.length}</h3>
                                    <small>Item Ditambahkan</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-danger">${comparison.removed.length}</h3>
                                    <small>Item Dihapus</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-warning">${comparison.modified.length}</h3>
                                    <small>Item Dimodifikasi</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-info">${comparison.unchanged.length}</h3>
                                    <small>Item Tidak Berubah</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Show added items
                if (comparison.added.length > 0) {
                    html += `
                        <div class="mt-3">
                            <h6 class="text-success"><i class="mdi mdi-plus-circle"></i> Item Ditambahkan (${comparison.added.length})</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Kode Item</th>
                                            <th>Nama</th>
                                            <th>Mesin</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${comparison.added.map(item => `
                                            <tr>
                                                <td>${item.code_item}</td>
                                                <td>${item.material_name}</td>
                                                <td>${item.code_machine}</td>
                                                <td>${parseInt(item.quantity).toLocaleString()}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                }

                // Show removed items
                if (comparison.removed.length > 0) {
                    html += `
                        <div class="mt-3">
                            <h6 class="text-danger"><i class="mdi mdi-minus-circle"></i> Item Dihapus (${comparison.removed.length})</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Kode Item</th>
                                            <th>Nama</th>
                                            <th>Mesin</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${comparison.removed.map(item => `
                                            <tr>
                                                <td>${item.code_item}</td>
                                                <td>${item.material_name}</td>
                                                <td>${item.code_machine}</td>
                                                <td>${parseInt(item.quantity).toLocaleString()}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                }

                // Show modified items
                if (comparison.modified.length > 0) {
                    html += `
                        <div class="mt-3">
                            <h6 class="text-warning"><i class="mdi mdi-pencil"></i> Item Dimodifikasi (${comparison.modified.length})</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Kode Item</th>
                                            <th>Nama</th>
                                            <th>Perubahan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${comparison.modified.map(entry => `
                                            <tr>
                                                <td>${entry.item.code_item}</td>
                                                <td>${entry.item.material_name}</td>
                                                <td>
                                                    <ul class="mb-0">
                                                        ${entry.changes.map(change => `<li>${change}</li>`).join('')}
                                                    </ul>
                                                </td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                }

                resultDiv.innerHTML = html;
            },

            /**
             * Load maintenance data from database via AJAX
             */
            loadMaintenanceData(machineCode = null) {
                console.log('🔧 Loading maintenance data from database...', machineCode ?
                    `for machine: ${machineCode}` : 'for all machines');

                // Show loading state
                const tbody = document.getElementById('maintenance-tbody');
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <br>Loading maintenance data...
                        </td>
                    </tr>
                `;

                // Use machine-specific endpoint if machineCode is provided
                const url = machineCode ?
                    `/sipo/maintenance/lubrication-machine/${machineCode}` :
                    `/sipo/maintenance/lubrication-timeline`;

                console.log('🔧 Fetching from URL:', url);

                // Fetch data from route
                fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(result => {
                        console.log('🔧 Maintenance data loaded:', result);

                        // Check if data is valid
                        if (!result.success || !result.data) {
                            throw new Error('No data received from server');
                        }

                        // No need to filter if using machine-specific endpoint
                        let filteredData = result.data;
                        if (!machineCode) {
                            // Only filter if using general endpoint
                            console.log('🔧 Using general endpoint, no filtering needed');
                        }

                        this.renderMaintenanceTable(filteredData);
                    })
                    .catch(error => {
                        console.error('❌ Error loading maintenance data:', error);

                        // Show error message
                        tbody.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center text-danger">
                                <i class="mdi mdi-alert-circle"></i>
                                Error loading maintenance data: ${error.message}
                            </td>
                        </tr>
                    `;
                    });
            },

            /**
             * Render maintenance table with data
             */
            renderMaintenanceTable(maintenanceData) {
                console.log('🔧 Rendering maintenance table with data:', maintenanceData);

                const tbody = document.getElementById('maintenance-tbody');
                if (!tbody) {
                    console.error('❌ Maintenance tbody not found');
                    return;
                }

                if (!maintenanceData || maintenanceData.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="mdi mdi-information-outline" style="font-size: 2rem;"></i>
                                <br><br>
                                Tidak ada jadwal maintenance untuk 7 hari ke depan
                            </td>
                        </tr>
                    `;
                    return;
                }

                // Render table rows
                tbody.innerHTML = maintenanceData.map((item, index) => `
                    <tr>
                        <td class="text-center">
                            <div class="form-check">
                                <input class="form-check-input maintenance-checkbox"
                                       type="checkbox"
                                       id="maintenance-${item.id || index}"
                                       value="${item.id || index}"
                                       data-maintenance='${JSON.stringify(item)}'
                                       title="Pilih jadwal maintenance ini">
                                <label class="form-check-label" for="maintenance-${item.id || index}">
                                    <i class="mdi mdi-checkbox-blank-outline"></i>
                                </label>
                            </div>
                        </td>
                        <td class="text-center">${index + 1}</td>
                        <td>${this.formatDate(item.datetgs)}</td>
                        <td>${item.isi_tugas || 'Maintenance Task'}</td>
                        <td>${item.mesin || '-'}</td>
                        <td>${item.pelumasan_shift || 'Shift 1'}</td>
                        <td>
                            <span class="badge badge-${this.getStatusBadgeClass(item.status_tugas)}">
                                ${item.status_tugas || 'Pending'}
                            </span>
                        </td>
                        <td>${item.durasi || 2} jam</td>
                    </tr>
                `).join('');

                // Setup checkbox events for single selection
                this.setupMaintenanceCheckboxEvents();
            },

            /**
             * Setup maintenance filter events
             */
            setupMaintenanceFilterEvents() {
                const machineFilter = document.getElementById('maintenance-machine-filter');
                const dateFilter = document.getElementById('maintenance-date-filter');
                const statusFilter = document.getElementById('maintenance-status-filter');

                // Machine filter
                machineFilter.addEventListener('change', () => {
                    this.filterMaintenanceData();
                });

                // Date filter
                dateFilter.addEventListener('change', () => {
                    this.filterMaintenanceData();
                });

                // Status filter
                statusFilter.addEventListener('change', () => {
                    this.filterMaintenanceData();
                });
            },

            /**
             * Filter maintenance data based on selected filters
             */
            filterMaintenanceData() {
                console.log('🔧 Filtering maintenance data...');
                // For now, just reload the data
                this.loadMaintenanceData();
            },

            /**
             * Setup maintenance checkbox events for single selection
             */
            setupMaintenanceCheckboxEvents() {
                // Select all checkbox
                const selectAllCheckbox = document.getElementById('select-all-maintenance');
                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', (e) => {
                        const checkboxes = document.querySelectorAll('.maintenance-checkbox');
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = e.target.checked;
                        });

                        // Update add to plan button
                        this.updateAddToPlanButton();
                    });
                }

                // Individual checkboxes - ensure only one can be selected
                const checkboxes = document.querySelectorAll('.maintenance-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', (e) => {
                        if (e.target.checked) {
                            // Uncheck all other checkboxes
                            checkboxes.forEach(otherCheckbox => {
                                if (otherCheckbox !== e.target) {
                                    otherCheckbox.checked = false;
                                }
                            });

                            // Update select all checkbox
                            if (selectAllCheckbox) {
                                selectAllCheckbox.checked = false;
                                selectAllCheckbox.indeterminate = false;
                            }
                        }

                        // Update add to plan button
                        this.updateAddToPlanButton();
                    });
                });
            },

            /**
             * Update add to plan button state
             */
            updateAddToPlanButton() {
                const addToPlanBtn = document.getElementById('add-to-plan-btn');
                const checkboxes = document.querySelectorAll('.maintenance-checkbox:checked');

                if (addToPlanBtn) {
                    if (checkboxes.length > 0) {
                        addToPlanBtn.disabled = false;
                        addToPlanBtn.textContent = `Tambah ke Plan (${checkboxes.length})`;
                    } else {
                        addToPlanBtn.disabled = true;
                        addToPlanBtn.textContent = 'Tambah ke Plan';
                    }
                }
            },

            /**
             * Setup add to plan button event
             */
            setupAddToPlanButton() {
                const addToPlanBtn = document.getElementById('add-to-plan-btn');
                if (addToPlanBtn) {
                    addToPlanBtn.addEventListener('click', () => {
                        const checkedCheckbox = document.querySelector('.maintenance-checkbox:checked');
                        if (checkedCheckbox) {
                            const maintenanceData = JSON.parse(checkedCheckbox.dataset.maintenance);
                            this.addMaintenanceToProductionPlan(maintenanceData);

                            $('#maintenanceScheduleModal').modal('hide');
                        }
                    });
                }
            },

            /**
             * Add maintenance item to production plan
             */
            addMaintenanceToProductionPlan(maintenanceData) {
                try {

                    const maintenanceItem = {
                        id: 'maintenance_' + maintenanceData.id,
                        code_item: maintenanceData.code_item || 'MAINTENANCE',
                        material_name: maintenanceData.material_name || maintenanceData.isi_tugas,
                        wo_docno: maintenanceData.wo_docno || maintenanceData.title,
                        quantity: maintenanceData.quantity || 1,
                        delivery_date: maintenanceData.delivery_date || maintenanceData.datetgs,
                        process: maintenanceData.process || 'Maintenance',
                        capacity: maintenanceData.capacity || 10000,
                        flag_status: maintenanceData.flag_status || 'pending',
                        code_machine: maintenanceData.mesin,
                        start_jam: null,
                        end_jam: null,
                        is_maintenance: true,
                        maintenance_data: maintenanceData,
                        durasi: maintenanceData.durasi || 2
                    };

                    timelineData.push(maintenanceItem);
                    const addedItem = timelineData.find(item => item.id === maintenanceItem.id);
                    const filteredData = DataManager.getFilteredData();
                    TableRenderer.renderTable();

                    Utils.showToast(
                        `Maintenance "${maintenanceData.isi_tugas}" berhasil ditambahkan ke plan mesin ${maintenanceData.mesin}`,
                        'success');

                    this.loadMaintenanceCount(maintenanceData.mesin);

                } catch (error) {
                    console.error('❌ Error adding maintenance to production plan:', error);
                    Utils.showToast('Gagal menambahkan maintenance ke plan: ' + error.message, 'error');
                }
            },












            // Helper function to check data integrity
            checkDataIntegrity(machineCode) {
                const machineData = timelineData.filter(item => item.code_machine === machineCode);
                console.log(`🔍 Data integrity check for machine ${machineCode}:`);
                console.log(`📊 Total items in timelineData: ${timelineData.length}`);
                console.log(`📊 Items for machine ${machineCode}: ${machineData.length}`);
                console.log(`📊 Machine items:`, machineData.map((item, idx) =>
                    `${idx + 1}. ${item.code_item} (ID: ${item.id}, Machine: ${item.code_machine})`
                ));

                // Check for duplicates
                const itemIds = machineData.map(item => item.id);
                const uniqueIds = [...new Set(itemIds)];
                if (itemIds.length !== uniqueIds.length) {
                    console.warn(`⚠️ Duplicate IDs found in machine ${machineCode}`);
                }

                return machineData;
            },

            // Debug all data
            debugAllData() {
                console.log('🐛 === DEBUG ALL DATA ===');
                console.log(`📊 Total timelineData items: ${timelineData.length}`);
                console.log(`📊 All machines:`, allMachines.map(m => m.Code));

                // Group by machine
                const machines = [...new Set(timelineData.map(item => item.code_machine))];
                machines.forEach(machineCode => {
                    this.checkDataIntegrity(machineCode);
                });

                // Check for orphaned items
                const orphanedItems = timelineData.filter(item => !item.code_machine);
                if (orphanedItems.length > 0) {
                    console.warn(`⚠️ Found ${orphanedItems.length} orphaned items without machine:`, orphanedItems);
                }

                console.log('🐛 === END DEBUG ===');
            },

            // Sync machine times based on priorities
            syncMachineTimes(machineCode) {
                console.log(`🔄 Syncing times for machine ${machineCode}`);

                // Get all items for this machine
                const machineItems = timelineData.filter(item => item.code_machine === machineCode);

                console.log(`📋 Found ${machineItems.length} items for machine ${machineCode}`);
                machineItems.forEach((item, idx) => {
                    console.log(`   ${idx + 1}. ${item.code_item} - Start: ${item.start_jam}, Qty: ${item.quantity}`);
                });

                if (machineItems.length === 0) {
                    console.log(`⚠️ No items found for machine ${machineCode}`);
                    return;
                }

                // Get machine data for capacity
                const machineData = allMachines.find(m => m.Code === machineCode);
                if (!machineData) {
                    console.warn(`⚠️ Machine data not found for ${machineCode}`);
                    return;
                }

                // Sort items by ID or by creation order to preserve intended sequence
                // Items that appear first in the list should maintain their order
                machineItems.sort((a, b) => {
                    // Try to maintain original order by checking item IDs or indices
                    const aIndex = timelineData.findIndex(i => i.id === a.id);
                    const bIndex = timelineData.findIndex(i => i.id === b.id);
                    return aIndex - bIndex;
                });

                // Find the earliest start_jam as baseline
                const earliestStart = new Date(Math.min(...machineItems.map(item => new Date(item.start_jam).getTime())));

                console.log(`📅 Earliest start: ${earliestStart.toISOString()}`);
                console.log(`📊 Total items: ${machineItems.length}`);
                console.log(`📋 Synced Order: ${machineItems.map((item, idx) => `${idx + 1}. ${item.code_item} (${item.quantity} PCS)`).join(', ')}`);

                // Start with earliest time
                let currentTime = new Date(earliestStart);

                // Recalculate times for each item sequentially
                machineItems.forEach((item, index) => {
                    // Calculate duration based on quantity and capacity
                    const capacityPerHour = machineData.CapacityPerHour || 10000;
                    const durationHours = parseFloat(item.quantity) / capacityPerHour;

                    // Set start time for this item
                    const startTime = new Date(currentTime);

                    // Calculate end time
                    const endTime = new Date(startTime.getTime() + durationHours * 60 * 60 * 1000);

                    // Update start and end times
                    item.start_jam = startTime.toISOString();
                    item.end_jam = endTime.toISOString();

                    console.log(`⏰ Item ${index + 1}/${machineItems.length} UPDATED`);
                    console.log(`   Code Item: ${item.code_item}`);
                    console.log(`   Qty: ${item.quantity} PCS`);
                    console.log(`   OLD Start: ${timelineData.find(i => i.id === item.id)?.start_jam}`);
                    console.log(`   NEW Start: ${item.start_jam}`);
                    console.log(`   NEW End: ${item.end_jam}`);
                    console.log(`   Duration: ${durationHours.toFixed(2)} jam`);
                    console.log(``);

                    // Update currentTime for next item (start after this item ends)
                    currentTime = new Date(endTime);
                });

                console.log(`✅ Synced ${machineItems.length} items for machine ${machineCode}`);
            },

            // Export to Excel with formatted template
            exportToExcel() {
                console.log('📊 Exporting to Excel...');

                const filteredData = DataManager.getFilteredData();
                if (filteredData.length === 0) {
                    Utils.showToast('Tidak ada data untuk di-export', 'warning');
                    return;
                }

                // Get current date
                const today = new Date();
                const days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
                const months = ['January', 'February', 'Maret', 'April', 'Mei', 'Juni',
                               'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                const dayName = days[today.getDay()];
                const dateStr = `${today.getDate()} - ${months[today.getMonth()]} - ${today.getFullYear()}`;

                // Group data by machine
                const machines = [...new Set(filteredData.map(item => item.code_machine))];
                const workbook = XLSX.utils.book_new();

                machines.forEach(machineCode => {
                    const machine = allMachines.find(m => m.Code === machineCode);
                    const machineName = machine ? machine.Description : machineCode;
                    const machineData = filteredData
                        .filter(item => item.code_machine === machineCode)
                        .sort((a, b) => new Date(a.start_jam) - new Date(b.start_jam));

                    // Create Excel data array
                    const excelData = [
                        // Row 1: Title (will be merged)
                        ['', '', '', 'TARGET PRODUKSI CETAK 19', '', '', '', '', '', '', '', ''],
                        // Row 2: Empty
                        [],
                        // Row 3: Day and Date
                        ['', 'Hari:', dayName, '', '', '', '', '', 'Tanggal:', dateStr],
                        // Row 4: Empty
                        [],
                        // Row 5: Headers
                        ['Mesin', 'Nama Order', 'Kode Design', 'Target Sheet', 'Up', 'No. WO / JO', 'Catatan', 'Keterangan', 'Delivery']
                    ];

                    // Group by shift based on start time
                    const shifts = this.groupByShift(machineData);

                    shifts.forEach(shift => {
                        // Each shift group: Machine name + Shift name as merged rows
                        // Add machine name row (will be merged)
                        const machineRow = Array(10).fill('');
                        machineRow[0] = machineName;
                        excelData.push(machineRow);

                        // Add shift header
                        const shiftRow = Array(10).fill('');
                        shiftRow[0] = shift.name;
                        excelData.push(shiftRow);

                        // Add items in this shift
                        shift.items.forEach(item => {
                            // Calculate actual sheets needed
                            const quantity = parseFloat(item.quantity) || 0;
                            const up = parseFloat(item.up_cetak) || 1;
                            const targetSheets = Math.ceil(quantity / up);
                            const startTime = item.start_jam ? new Date(item.start_jam).toLocaleString('id-ID', {
                                day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
                            }) : '-';
                            const endTime = item.end_jam ? new Date(item.end_jam).toLocaleString('id-ID', {
                                day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
                            }) : '-';

                            const row = [
                                '', // Mesin (empty, parent is in previous row)
                                item.material_name || item.code_item || '-', // Nama Order
                                item.code_item || '-', // Kode Design
                                targetSheets.toLocaleString('id-ID'), // Target Sheet (calculated)
                                up, // Up
                                item.wo_docno || item.so_docno || '-', // No. WO
                                item.job_order || item.job_order_no || '-', // Catatan (Job Order)
                                item.ukuran_kertas || item.keterangan_item || '-', // Keterangan (Ukuran Kertas)
                                item.delivery_date ? new Date(item.delivery_date).toLocaleDateString('id-ID') : '-', // Delivery
                                item.job_order_status || item.status_item || 'NEW' // Status
                            ];
                            excelData.push(row);
                        });

                        // Empty row after each shift group
                        excelData.push(Array(10).fill(''));
                    });

                    // Create worksheet
                    const worksheet = XLSX.utils.aoa_to_sheet(excelData);

                    // Set column widths (matching template)
                    worksheet['!cols'] = [
                        { wch: 12 }, // Mesin
                        { wch: 35 }, // Nama Order - diperpendek
                        { wch: 15 }, // Kode Design (empty placeholder)
                        { wch: 12 }, // Kode Design - diperpendek
                        { wch: 10 }, // Target Sheet - diperpendek
                        { wch: 6 },  // Up
                        { wch: 18 }, // No. WO
                        { wch: 18 }, // Catatan (Job Order)
                        { wch: 18 }, // Keterangan (Ukuran Kertas) - diperlebar
                        { wch: 12 }, // Delivery
                        { wch: 10 }  // Status
                    ];

                    // Set cell styling for better appearance
                    const range = XLSX.utils.decode_range(worksheet['!ref'] || 'A1');
                    for (let R = 0; R <= range.e.r; R++) {
                        for (let C = 0; C <= range.e.c; C++) {
                            const cell_address = XLSX.utils.encode_cell({ c: C, r: R });
                            if (!worksheet[cell_address]) continue;

                            // Bold headers (row 5)
                            if (R === 4) {
                                worksheet[cell_address].s = {
                                    font: { bold: true },
                                    alignment: { horizontal: 'center', vertical: 'center' }
                                };
                            }
                        }
                    }

                    // Add worksheet to workbook
                    XLSX.utils.book_append_sheet(workbook, worksheet, machineName.substring(0, 31));
                });

                // Generate filename
                const filename = `TARGET_PRODUKSI_CETAK_${dayName}_${today.getDate()}-${months[today.getMonth()]}-${today.getFullYear()}.xlsx`;

                // Export file
                XLSX.writeFile(workbook, filename);
                Utils.showToast('✅ Excel berhasil di-export', 'success');
            },

            // Split item by shift boundaries if it crosses multiple shifts
            // Returns array of items split by shift with adjusted times and quantities
            splitItemByShift(item) {
                if (!item.start_jam || !item.end_jam) {
                    return [item]; // Return original if no time data
                }

                const startTime = new Date(item.start_jam);
                const endTime = new Date(item.end_jam);
                const splitItems = [];

                // Calculate total duration in hours
                const totalDurationMs = endTime.getTime() - startTime.getTime();
                const totalDurationHours = totalDurationMs / (1000 * 60 * 60);
                const totalQuantity = parseFloat(item.quantity) || 0;

                // Helper function to get minutes from start of day (0-1439)
                const getMinutesFromDayStart = (date) => {
                    return date.getHours() * 60 + date.getMinutes() + date.getSeconds() / 60;
                };

                // Helper function to check if date is on next day compared to startTime
                const isNextDay = (date) => {
                    const startDay = new Date(startTime);
                    startDay.setHours(0, 0, 0, 0);
                    const checkDay = new Date(date);
                    checkDay.setHours(0, 0, 0, 0);
                    return checkDay.getTime() > startDay.getTime();
                };

                // Helper function to create date with specific time (same day as baseDate)
                const createDateWithTime = (baseDate, hours, minutes, seconds = 0) => {
                    const newDate = new Date(baseDate);
                    newDate.setHours(hours, minutes, seconds, 0);
                    return newDate;
                };

                // Helper function to create date at end of day (23:59:59)
                const createEndOfDay = (baseDate) => {
                    const newDate = new Date(baseDate);
                    newDate.setHours(23, 59, 59, 999);
                    return newDate;
                };

                // Helper function to create date at start of next day (00:00:00)
                const createStartOfNextDay = (baseDate) => {
                    const newDate = new Date(baseDate);
                    newDate.setDate(newDate.getDate() + 1);
                    newDate.setHours(0, 0, 0, 0);
                    return newDate;
                };

                let currentStart = new Date(startTime);
                const finalEnd = new Date(endTime);

                // Process shifts until we cover the entire item duration
                while (currentStart < finalEnd) {
                    const currentStartMinutes = getMinutesFromDayStart(currentStart);
                    const isNextDayStart = isNextDay(currentStart);
                    let segmentStart, segmentEnd, shiftNumber, shiftName;

                    // Determine which shift this segment belongs to
                    if (isNextDayStart) {
                        // Next day - must be Shift 3 (00:00-08:00)
                        segmentStart = new Date(currentStart);
                        // End at 08:00 of next day, or finalEnd, whichever comes first
                        const shift3End = createDateWithTime(currentStart, 8, 0);
                        segmentEnd = shift3End < finalEnd ? shift3End : new Date(finalEnd);
                        shiftNumber = 3;
                        shiftName = 'SHIFT 3';
                    } else {
                        // Same day as start
                        if (currentStartMinutes >= 480 && currentStartMinutes < 960) {
                            // Shift 1 (08:00-16:00)
                            segmentStart = new Date(currentStart);
                            const shift1End = createDateWithTime(currentStart, 16, 0);
                            segmentEnd = shift1End < finalEnd ? shift1End : new Date(finalEnd);
                            shiftNumber = 1;
                            shiftName = 'SHIFT 1';
                        } else if (currentStartMinutes >= 960 && currentStartMinutes < 1440) {
                            // Shift 2 (16:00-24:00)
                            segmentStart = new Date(currentStart);
                            // Shift 2 ends at 24:00 (midnight) which is 00:00 next day
                            const shift2End = createStartOfNextDay(currentStart);
                            segmentEnd = shift2End < finalEnd ? shift2End : new Date(finalEnd);
                            shiftNumber = 2;
                            shiftName = 'SHIFT 2';
                        } else if (currentStartMinutes >= 0 && currentStartMinutes < 480) {
                            // Shift 3 (00:00-08:00) - same day
                            segmentStart = new Date(currentStart);
                            const shift3End = createDateWithTime(currentStart, 8, 0);
                            segmentEnd = shift3End < finalEnd ? shift3End : new Date(finalEnd);
                            shiftNumber = 3;
                            shiftName = 'SHIFT 3';
                        } else {
                            // Edge case - should not happen
                            console.warn('⚠️ Unusual time detected:', currentStartMinutes, 'for item:', item.id);
                            segmentStart = new Date(currentStart);
                            segmentEnd = new Date(finalEnd);
                            shiftNumber = 0;
                            shiftName = 'CADANGAN';
                        }
                    }

                    // Calculate segment duration and proportional quantity
                    const segmentDurationMs = segmentEnd.getTime() - segmentStart.getTime();
                    const segmentDurationHours = segmentDurationMs / (1000 * 60 * 60);
                    const segmentProportion = totalDurationHours > 0 ? segmentDurationHours / totalDurationHours : 0;
                    const segmentQuantity = Math.round(totalQuantity * segmentProportion);

                    // Ensure at least quantity 1 if segment has duration
                    const finalQuantity = segmentQuantity > 0 ? segmentQuantity : (segmentDurationHours > 0 ? 1 : 0);

                    // Format time for display
                    const timeInfo = `${this.formatTimeForExcel(segmentStart)} - ${this.formatTimeForExcel(segmentEnd)}`;

                    // Create split item only if it has duration
                    if (segmentDurationHours > 0 && finalQuantity > 0) {
                        const splitItem = {
                            ...item,
                            id: `${item.id}_shift${shiftNumber}_${splitItems.length}`, // Unique ID for split items
                            start_jam: segmentStart.toISOString(),
                            end_jam: segmentEnd.toISOString(),
                            quantity: finalQuantity,
                            shift_number: shiftNumber,
                            shift_name: shiftName,
                            time_info: timeInfo, // For Excel display
                            is_split: true, // Mark as split item
                            original_item_id: item.id // Reference to original
                        };

                        splitItems.push(splitItem);
                    }

                    // Move to next segment
                    currentStart = new Date(segmentEnd);
                }

                return splitItems.length > 0 ? splitItems : [item];
            },

            // Format time for Excel display
            formatTimeForExcel(date) {
                if (!date) return '-';
                const d = new Date(date);
                const day = String(d.getDate()).padStart(2, '0');
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const year = String(d.getFullYear()).slice(-2);
                const hours = String(d.getHours()).padStart(2, '0');
                const minutes = String(d.getMinutes()).padStart(2, '0');
                return `${day}/${month}/${year} ${hours}:${minutes}`;
            },

            // Group items by shift based on start time (with splitting)
            // Shift 1: 08.00-16.00 (08:00 to 15:59:59)
            // Shift 2: 16.00-00.00 (16:00 to 23:59:59)
            // Shift 3: 00.00-08.00 (00:00 to 07:59:59)
            groupByShift(items, numShifts = 3) {
                const cadangan = [];
                const shift1 = [];
                const shift2 = [];
                const shift3 = [];

                items.forEach(item => {
                    if (!item.start_jam) {
                        cadangan.push(item);
                        return;
                    }

                    // Split item if it crosses multiple shifts
                    const splitItems = this.splitItemByShift(item);

                    splitItems.forEach(splitItem => {
                        const startTime = new Date(splitItem.start_jam);
                    const hour = startTime.getHours();
                        const minutes = startTime.getMinutes();
                        const seconds = startTime.getSeconds();

                        // Convert to total minutes for easier comparison
                        const totalMinutes = hour * 60 + minutes + (seconds / 60);

                        // Shift 1: 08.00-16.00 (08:00:00 to 15:59:59)
                        // Range: 480 minutes (8*60) to 959.983... minutes (15*60+59+59/60)
                        if (totalMinutes >= 480 && totalMinutes < 960) {
                            shift1.push(splitItem);
                        }
                        // Shift 2: 16.00-00.00 (16:00:00 to 23:59:59)
                        // Range: 960 minutes (16*60) to 1439.983... minutes (23*60+59+59/60)
                        else if (totalMinutes >= 960 && totalMinutes < 1440) {
                            shift2.push(splitItem);
                        }
                        // Shift 3: 00.00-08.00 (00:00:00 to 07:59:59)
                        // Range: 0 minutes to 479.983... minutes (7*60+59+59/60)
                        else if (totalMinutes >= 0 && totalMinutes < 480) {
                            if (numShifts === 2) {
                                // In 2-shift mode, shift 3 items should be moved to shift 1 of the SAME date at 08:00
                                // Example: SHIFT 3 tanggal 11 (00:00-08:00) → SHIFT 1 tanggal 11 (08:00-16:00)
                                const sameDayItem = { ...splitItem };
                                const currentDate = new Date(splitItem.start_jam);
                                // Keep the same date, just change the time to 08:00
                                currentDate.setHours(8, 0, 0, 0); // Set to 08:00 (start of shift 1)

                                // Also adjust end_jam accordingly (maintain duration)
                                if (splitItem.end_jam) {
                                    const endDate = new Date(splitItem.end_jam);
                                    const duration = endDate - new Date(splitItem.start_jam); // Duration in milliseconds
                                    const newEndDate = new Date(currentDate);
                                    newEndDate.setTime(currentDate.getTime() + duration);
                                    sameDayItem.end_jam = newEndDate.toISOString();
                                }

                                sameDayItem.start_jam = currentDate.toISOString();
                                shift1.push(sameDayItem); // Add to shift 1 of the same day
                            } else {
                            shift3.push(splitItem);
                            }
                        }
                        // Fallback for any edge case
                        else {
                            console.warn('⚠️ Item with unusual time:', splitItem.start_jam, 'hour:', hour, 'totalMinutes:', totalMinutes);
                            cadangan.push(splitItem);
                        }
                    });
                });

                // Sort shift1 items by start_jam (to ensure items moved from shift 3 are in correct order)
                shift1.sort((a, b) => new Date(a.start_jam) - new Date(b.start_jam));
                shift2.sort((a, b) => new Date(a.start_jam) - new Date(b.start_jam));
                if (numShifts === 3) {
                    shift3.sort((a, b) => new Date(a.start_jam) - new Date(b.start_jam));
                }

                const groups = [];
                // Order: Shift 1, Shift 2, Shift 3 (if numShifts === 3), then Cadangan
                if (shift1.length > 0) groups.push({ name: 'SHIFT 1', items: shift1 });
                if (shift2.length > 0) groups.push({ name: 'SHIFT 2', items: shift2 });
                // Only include Shift 3 if numShifts === 3
                if (numShifts === 3 && shift3.length > 0) groups.push({ name: 'SHIFT 3', items: shift3 });
                if (cadangan.length > 0) groups.push({ name: 'CADANGAN', items: cadangan });

                return groups.length > 0 ? groups : [{ name: 'TIDAK ADA JADWAL', items: [] }];
            },

            // Export to Excel with full styling using ExcelJS
            async exportToExcelStyled(selectedDate = null, dateRange = null, numShifts = 3) {
                const startTime = performance.now();
                console.log('📊 Exporting to Excel with full styling...',
                    selectedDate ? `Date: ${selectedDate}` :
                    dateRange ? `Range: ${dateRange.start} to ${dateRange.end}` :
                    'All plans');

                let filteredData = DataManager.getFilteredData();

                // Filter by selected date if provided
                if (selectedDate) {
                    filteredData = filteredData.filter(item => {
                        if (!item.start_jam) return false;
                        const itemDate = new Date(item.start_jam).toISOString().split('T')[0];
                        return itemDate === selectedDate;
                    });

                    if (filteredData.length === 0) {
                        Utils.showToast(`Tidak ada data untuk tanggal ${selectedDate}`, 'warning');
                        return;
                    }
                } else if (dateRange) {
                    // Filter by date range
                    filteredData = filteredData.filter(item => {
                        if (!item.start_jam) return false;
                        const itemDate = new Date(item.start_jam).toISOString().split('T')[0];
                        return itemDate >= dateRange.start && itemDate <= dateRange.end;
                    });

                    if (filteredData.length === 0) {
                        Utils.showToast(`Tidak ada data untuk rentang waktu ${dateRange.start} sampai ${dateRange.end}`, 'warning');
                        return;
                    }
                } else {
                if (filteredData.length === 0) {
                    Utils.showToast('Tidak ada data untuk di-export', 'warning');
                    return;
                    }
                }

                try {
                    // Check if ExcelJS is available
                    if (typeof ExcelJS === 'undefined') {
                        console.error('ExcelJS is not loaded');
                        Utils.showToast('❌ ExcelJS library tidak ditemukan. Silakan refresh halaman.', 'error');
                        return;
                    }

                    // Create a new Excel workbook
                    const workbook = new ExcelJS.Workbook();

                    // Get current date
                    const today = new Date();
                    const days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
                    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                   'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    const dayName = days[today.getDay()];
                    const dateStr = `${today.getDate()} - ${months[today.getMonth()]} - ${today.getFullYear()}`;

                    // Format date range string for display
                    let dateRangeStr = '';
                    if (dateRange) {
                        const startDate = new Date(dateRange.start);
                        const endDate = new Date(dateRange.end);
                        const formatDate = (date) => {
                            return `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
                        };
                        dateRangeStr = `${formatDate(startDate)} - ${formatDate(endDate)}`;
                    } else if (selectedDate) {
                        const selDate = new Date(selectedDate);
                        dateRangeStr = `${selDate.getDate()} ${months[selDate.getMonth()]} ${selDate.getFullYear()}`;
                    }

                    // Create single worksheet for all data
                    const worksheet = workbook.addWorksheet('TARGET PRODUKSI CETAK 19');

                    // Yield to browser before heavy processing
                    await new Promise(resolve => setTimeout(resolve, 10));

                    // If 2-shift mode, process items first to move SHIFT 3 items to SHIFT 1 of same date
                    // This ensures items are grouped correctly by date after the shift transformation
                    let processedData = filteredData;
                    if (numShifts === 2) {
                        // Process all items to move SHIFT 3 items to SHIFT 1 of same date
                        processedData = [];
                        filteredData.forEach(item => {
                            if (!item.start_jam) {
                                processedData.push(item);
                                return;
                            }

                            // Split item if it crosses multiple shifts
                            const splitItems = this.splitItemByShift(item);

                            splitItems.forEach(splitItem => {
                                const startTime = new Date(splitItem.start_jam);
                                const hour = startTime.getHours();
                                const minutes = startTime.getMinutes();
                                const seconds = startTime.getSeconds();
                                const totalMinutes = hour * 60 + minutes + (seconds / 60);

                                // If item is in SHIFT 3 (00:00-08:00), move it to SHIFT 1 of same date at 08:00
                                if (totalMinutes >= 0 && totalMinutes < 480) {
                                    const sameDayItem = { ...splitItem };
                                    const currentDate = new Date(splitItem.start_jam);
                                    // Keep the same date, just change the time to 08:00
                                    currentDate.setHours(8, 0, 0, 0);

                                    // Adjust end_jam accordingly (maintain duration)
                                    if (splitItem.end_jam) {
                                        const endDate = new Date(splitItem.end_jam);
                                        const duration = endDate - new Date(splitItem.start_jam);
                                        const newEndDate = new Date(currentDate);
                                        newEndDate.setTime(currentDate.getTime() + duration);
                                        sameDayItem.end_jam = newEndDate.toISOString();
                                    }

                                    sameDayItem.start_jam = currentDate.toISOString();
                                    processedData.push(sameDayItem);
                                } else {
                                    processedData.push(splitItem);
                                }
                            });
                        });
                    }

                    // Group data by tanggal CETAK (start_jam) - used for grouping within the sheet
                    // Use processedData which has SHIFT 3 items moved to SHIFT 1 if numShifts === 2
                    const cetakDates = [...new Set(processedData
                        .map(item => item.start_jam ? new Date(item.start_jam).toISOString().split('T')[0] : 'NO_DATE')
                        .filter(d => d && d !== 'NO_DATE')
                    )].sort();

                    // Yield after grouping data
                    await new Promise(resolve => setTimeout(resolve, 10));

                    // Set up styling matching the image exactly
                    const lightGreenFill = {
                        type: 'pattern',
                        pattern: 'solid',
                        fgColor: { argb: 'FFD9EAD3' } // Light green as in image
                    };

                    const dottedBorder = {
                        top: { style: 'thin', color: { argb: 'FF000000' } },
                        left: { style: 'thin', color: { argb: 'FF000000' } },
                        bottom: { style: 'thin', color: { argb: 'FF000000' } },
                        right: { style: 'thin', color: { argb: 'FF000000' } }
                    };

                    let rowNum = 1;
                    let headersAdded = false;
                    let dateIndex = 0;
                    const totalDates = cetakDates.length;

                    console.log(`🚀 Mulai memproses ${totalDates} tanggal...`);

                    // Yield before starting loop
                    await new Promise(resolve => setTimeout(resolve, 50));

                    // Process dates in chunks to prevent UI blocking
                    const DATE_CHUNK_SIZE = 1; // Process 1 date at a time for better responsiveness

                    for (let dateChunkStart = 0; dateChunkStart < cetakDates.length; dateChunkStart += DATE_CHUNK_SIZE) {
                        // Yield before processing each date chunk
                        if (dateChunkStart > 0) {
                            await new Promise(resolve => setTimeout(resolve, 50));
                        }

                        const dateChunkEnd = Math.min(dateChunkStart + DATE_CHUNK_SIZE, cetakDates.length);
                        console.log(`📦 Processing date chunk ${dateChunkStart + 1}-${dateChunkEnd} of ${cetakDates.length}`);

                        for (let d = dateChunkStart; d < dateChunkEnd; d++) {
                            const cetakDate = cetakDates[d];
                            dateIndex++;
                            console.log(`📅 Memproses tanggal ${dateIndex}/${totalDates}: ${cetakDate}`);

                            // Get data for this tanggal cetak (start_jam)
                            // Use processedData which has SHIFT 3 items moved to SHIFT 1 if numShifts === 2
                            console.log(`🔍 Filtering data for date ${cetakDate}...`);
                            const dateData = processedData.filter(item => {
                                if (!item.start_jam) return false;
                                const itemDate = new Date(item.start_jam).toISOString().split('T')[0];
                                return itemDate === cetakDate;
                            });
                            console.log(`✅ Found ${dateData.length} items for date ${cetakDate}`);

                            // Yield after filtering data - more aggressive
                            await new Promise(resolve => setTimeout(resolve, 10));

                            if (dateData.length === 0) {
                                console.log(`⏭️ Skipping date ${cetakDate} - no data`);
                                continue;
                            }

                            if (!headersAdded) {

                                console.log('masuk header');
                            // Row 1: Title - "TARGET PRODUKSI CETAK 19" - merged A1:I1
                            worksheet.mergeCells(`A${rowNum}:K${rowNum}`);
                            const titleCell = worksheet.getCell(`A${rowNum}`);
                            titleCell.value = 'TARGET PRODUKSI CETAK 19';
                            titleCell.font = { bold: true, size: 14 };
                            titleCell.fill = lightGreenFill;
                            titleCell.alignment = { horizontal: 'center', vertical: 'middle' };
                            titleCell.border = dottedBorder;
                            rowNum++; // Move to row 2

                            // Row 2: Day and Date - matching image exactly
                            worksheet.getCell(`A${rowNum}`).value = 'Hari:';
                            worksheet.getCell(`A${rowNum}`).font = { bold: true };
                            // worksheet.getCell(`A${rowNum}`).fill = lightGreenFill;
                            worksheet.getCell(`A${rowNum}`).border = dottedBorder;

                            // B2 is empty
                            worksheet.getCell(`B${rowNum}`).value = dayName;
                            worksheet.getCell(`B${rowNum}`).font = { bold: false };
                            worksheet.getCell(`B${rowNum}`).border = dottedBorder;
                            // worksheet.getCell(`B${rowNum}`).fill = lightGreenFill;
                            worksheet.getCell(`I${rowNum}`).value = 'Tanggal:';
                            worksheet.getCell(`I${rowNum}`).font = { bold: true };
                            // worksheet.getCell(`I${rowNum}`).fill = lightGreenFill;
                            worksheet.getCell(`I${rowNum}`).border = dottedBorder;

                            // Show date range or single date - merged J2:K2
                            const dateDisplay = dateRangeStr || dateStr;
                            worksheet.mergeCells(`J${rowNum}:K${rowNum}`);
                            // fill
                            // worksheet.getCell(`J${rowNum}`).fill = lightGreenFill;
                            const dateCell = worksheet.getCell(`J${rowNum}`);
                            dateCell.value = dateDisplay;
                            dateCell.font = { bold: false };
                            dateCell.alignment = { horizontal: 'left', vertical: 'middle' };
                            dateCell.border = dottedBorder;
                            rowNum++; // Move to row 3

                            // Row 3: Headers - matching image exactly
                            // Column structure: A=Mesin, B=SHIFT, C=Nama Order, D=Kode Design, E=Target Sheet, F=Up, G=No. WO, H=Catatan, I=Keterangan, J=Jam, K=Delivery
                            const headers = [
                                { col: 1, value: 'Mesin' },      // A
                                { col: 2, value: 'SHIFT' },     // B
                                { col: 3, value: 'Nama Order' }, // C
                                { col: 4, value: 'Kode Design' }, // D
                                { col: 5, value: 'Target Sheet' }, // E
                                { col: 6, value: 'Up' },         // F
                                { col: 7, value: 'No. WO' },    // G
                                { col: 8, value: 'Catatan' },   // H
                                { col: 9, value: 'Keterangan' }, // I
                                { col: 10, value: 'Jam' },       // J
                                { col: 11, value: 'Delivery' }   // K
                            ];

                            headers.forEach(({ col, value }) => {
                                if (value) {
                                const cell = worksheet.getCell(rowNum, col);
                                    cell.value = value;
                                cell.font = { bold: true, size: 11 };
                                    cell.fill = lightGreenFill;
                                cell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
                                cell.border = dottedBorder;
                                }
                            });
                            rowNum++;

                            // Set column widths (only once) - matching image structure
                            worksheet.columns = [
                                { width: 5 }, // A: Mesin
                                { width: 12 }, // B: SHIFT (reduced from 18)
                                { width: 50 }, // C: Nama Order
                                { width: 15 }, // D: Kode Design
                                { width: 12 }, // E: Target Sheet
                                { width: 6 },  // F: Up
                                { width: 18 }, // G: No. WO
                                { width: 12 }, // H: Catatan
                                { width: 14 }, // I: Keterangan
                                { width: 25 }, // J: Jam
                                { width: 12 }  // K: Delivery
                            ];

                                headersAdded = true;
                            }

                            // console.log('kesini');

                            // Add separator row before machine group
                            rowNum++; // Empty row separator (like Row 6 in image)

                            // Group by machine first (within this delivery date)
                            const machinesInDate = [...new Set(dateData.map(item => item.code_machine))];

                            // Yield after grouping machines
                            await new Promise(resolve => setTimeout(resolve, 5));

                            for (const machineCode of machinesInDate) {

                                // console.log(machinesInDate);
                                const machine = allMachines.find(m => m.Code === machineCode);
                                const machineName = machine ? machine.Description : machineCode;
                                const machineData = dateData
                                    .filter(item => item.code_machine === machineCode)
                                    .sort((a, b) => new Date(a.start_jam) - new Date(b.start_jam));


                                const machineStartRow = rowNum;
                                const shifts = this.groupByShift(machineData, numShifts);

                                let machineEndRow = machineStartRow;

                                console.log(shifts);
                                for (let shiftIdx = 0; shiftIdx < shifts.length; shiftIdx++) {
                                    const shift = shifts[shiftIdx];

                                    // Calculate how many rows this shift will use (for shift merge only)
                                    const shiftRowsCount = shift.items.length; // Only data rows, shift header is separate

                                    // Shift header in B (merged vertically across all rows in this shift)
                                    const shiftCell = worksheet.getCell(rowNum, 2);
                                    worksheet.mergeCells(`B${rowNum}:B${rowNum + shiftRowsCount - 1}`);

                                    shiftCell.value = shift.name;
                                    shiftCell.font = { bold: true, size: 11 };
                                    shiftCell.fill = lightGreenFill;
                                    shiftCell.alignment = { horizontal: 'left', vertical: 'middle', wrapText: true };
                                    shiftCell.border = dottedBorder;
                                    // No need to loop - merged cell already has the styling

                                    // Add items - matching image structure exactly
                                    // OPTIMIZED: Set values first, then apply styling in batch
                                    // CHUNKED: Process in smaller batches to prevent UI blocking
                                    const whiteFill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFFFFFFF' } };
                                    const CHUNK_SIZE = 3; // Reduce chunk size to 3 for maximum responsiveness

                                    for (let chunkStart = 0; chunkStart < shift.items.length; chunkStart += CHUNK_SIZE) {
                                        const chunkEnd = Math.min(chunkStart + CHUNK_SIZE, shift.items.length);

                                        // Process chunk
                                        for (let itemIdx = chunkStart; itemIdx < chunkEnd; itemIdx++) {
                                            const item = shift.items[itemIdx];
                                            const row = worksheet.getRow(rowNum);

                                            const quantity = parseFloat(item.quantity) || 0;
                                            const up = parseFloat(item.up_cetak) || 1;
                                            const targetSheets = Math.ceil(quantity / up);

                                            // Set all values first (faster than setting value + style together)
                                            // Column C: Nama Order
                                            row.getCell(3).value = item.material_name || item.code_item || '-';
                                            // Column D: Kode Design
                                            row.getCell(4).value = item.code_item || '-';
                                            // Column E: Target Sheet
                                            row.getCell(5).value = targetSheets;
                                            // Column F: Up
                                            row.getCell(6).value = up;
                                            // Column G: No. WO
                                            row.getCell(7).value = item.wo_docno || item.so_docno || '-';
                                            // Column H: Catatan
                                            row.getCell(8).value = item.process || '-';
                                            // Column I: Keterangan
                                            row.getCell(9).value = targetSheets;
                                            // Column J: Jam
                                            if (item.time_info) {
                                                row.getCell(10).value = item.time_info;
                                            } else {
                                                const startTimeStr = item.start_jam ? this.formatTimeForExcel(new Date(item.start_jam)) : '-';
                                                const endTimeStr = item.end_jam ? this.formatTimeForExcel(new Date(item.end_jam)) : '-';
                                                row.getCell(10).value = `${startTimeStr} - ${endTimeStr}`;
                                            }
                                            // Column K: Delivery
                                            if (item.delivery_date) {
                                                row.getCell(11).value = new Date(item.delivery_date);
                                            } else {
                                                row.getCell(11).value = '-';
                                            }

                                            // Now apply styling in batch (much faster)
                                            // Set fill and border for all data cells (C-K) at once
                                            for (let i = 3; i <= 11; i++) {
                                                const cell = row.getCell(i);
                                                cell.fill = whiteFill;
                                                cell.border = dottedBorder;

                                            }

                                            // Set specific alignments and formats (with yields)
                                            row.getCell(3).alignment = { horizontal: 'left' };
                                            row.getCell(4).alignment = { horizontal: 'left' };
                                            row.getCell(5).alignment = { horizontal: 'right' };
                                            row.getCell(5).numFmt = '#,##0';

                                            row.getCell(6).alignment = { horizontal: 'center' };
                                            row.getCell(7).alignment = { horizontal: 'left' };
                                            row.getCell(8).alignment = { horizontal: 'left' };
                                            row.getCell(9).alignment = { horizontal: 'right' };
                                            row.getCell(9).numFmt = '#,##0';

                                            row.getCell(10).alignment = { horizontal: 'left' };
                                            row.getCell(11).alignment = { horizontal: 'left' };
                                            if (item.delivery_date) {
                                                row.getCell(11).numFmt = 'dd/mm/yy';
                                            }

                                            rowNum++;

                                        }
                                    }

                                    // Update machine end row (no empty row between shifts in same machine)
                                    machineEndRow = rowNum - 1;

                                }

                                // Now merge machine name across ALL rows for this machine (all shifts)
                                if (machineEndRow >= machineStartRow) {
                                    worksheet.mergeCells(`A${machineStartRow}:A${machineEndRow}`);

                                    const machineRowCell = worksheet.getCell(machineStartRow, 1);
                                    machineRowCell.value = machineName;
                                    machineRowCell.font = { bold: true, size: 11 };
                                    machineRowCell.fill = lightGreenFill;
                                    machineRowCell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
                                    machineRowCell.border = dottedBorder;
                                    // No need to loop - merged cell already has the styling
                                }

                                // Empty row after machine group (separator between different machines)
                                rowNum++;
                            }

                            // Hide unused columns if needed
                            // Column B is empty, but we keep it for spacing
                        }
                    }

                    console.log('kesini 2');

                    // Generate filename with date range if applicable
                    let filename = `TARGET_PRODUKSI_CETAK_${dayName}_${today.getDate()}-${months[today.getMonth()]}-${today.getFullYear()}`;
                    if (dateRange) {
                        const startDate = new Date(dateRange.start);
                        const endDate = new Date(dateRange.end);
                        filename = `TARGET_PRODUKSI_CETAK_${startDate.getDate()}-${months[startDate.getMonth()]}-${startDate.getFullYear()}_sampai_${endDate.getDate()}-${months[endDate.getMonth()]}-${endDate.getFullYear()}`;
                    } else if (selectedDate) {
                        const selDate = new Date(selectedDate);
                        filename = `TARGET_PRODUKSI_CETAK_${selDate.getDate()}-${months[selDate.getMonth()]}-${selDate.getFullYear()}`;
                    }
                    filename += '.xlsx';

                    // Update progress
                    console.log('📝 Menulis file Excel...');

                    // Write to file (this might take time for large files)
                    // Give browser time to update UI before heavy operation
                    await new Promise(resolve => setTimeout(resolve, 50));
                    await new Promise(resolve => requestAnimationFrame(resolve));

                    // Write buffer - this is the heaviest operation
                    const buffer = await workbook.xlsx.writeBuffer();
                    const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = filename;
                    link.click();
                    window.URL.revokeObjectURL(url);

                    const endTime = performance.now();
                    const duration = ((endTime - startTime) / 1000).toFixed(2);
                    console.log(`✅ Excel export selesai dalam ${duration} detik`);
                    Utils.showToast(`✅ Excel berhasil di-export dengan styling penuh (${duration}s)`, 'success');
                } catch (error) {
                    console.error('❌ Error exporting Excel:', error);
                    // Fallback to basic export if ExcelJS fails
                    console.log('⚠️ ExcelJS not available, using basic XLSX export');
                    this.exportToExcel();
                }
            },

            // Export to Excel Basic (Simple, no styling)
            async exportToExcelBasic() {
                console.log('📊 Exporting to Excel (Basic)...');

                const filteredData = DataManager.getFilteredData();
                if (filteredData.length === 0) {
                    Utils.showToast('Tidak ada data untuk di-export', 'warning');
                    return;
                }

                // Check if ExcelJS is available
                if (typeof ExcelJS === 'undefined') {
                    console.error('ExcelJS is not loaded');
                    Utils.showToast('❌ ExcelJS library tidak ditemukan. Silakan refresh halaman.', 'error');
                    return;
                }

                try {
                    // Create workbook
                    const workbook = new ExcelJS.Workbook();
                    const worksheet = workbook.addWorksheet('TARGET PRODUKSI CETAK 19');

                    // Get current date (set to start of day for comparison)
                    const now = new Date();
                    const todayDateOnly = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                    const today = new Date(todayDateOnly); // For display purposes

                    const days = ['MINGGU', 'SENIN', 'SELASA', 'RABU', 'KAMIS', 'JUMAT', 'SABTU'];
                    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                                   'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    const dayName = days[today.getDay()];
                    const dateStr = `${today.getDate()} - ${months[today.getMonth()]} - ${today.getFullYear()}`;

                    // Separate data into today and CADANGAN (future dates)
                    const todayData = [];
                    const cadanganData = [];

                    filteredData.forEach(item => {
                        let itemDate = null;

                        // Try to get date from start_jam first
                        if (item.start_jam) {
                            const startDate = new Date(item.start_jam);
                            startDate.setHours(0, 0, 0, 0);
                            itemDate = startDate;
                        } else if (item.delivery_date) {
                            // Fallback to delivery_date
                            const deliveryDate = new Date(item.delivery_date);
                            deliveryDate.setHours(0, 0, 0, 0);
                            itemDate = deliveryDate;
                        }

                        if (itemDate) {
                            // Compare dates (ignore time)
                            if (itemDate.getTime() > todayDateOnly.getTime()) {
                                // Future date - add to CADANGAN
                                cadanganData.push(item);
                            } else {
                                // Today or past date - add to today
                                todayData.push(item);
                            }
                        } else {
                            // No date info - add to today as default
                            todayData.push(item);
                        }
                    });

                    // Styling
                    const lightGreenFill = {
                        type: 'pattern',
                        pattern: 'solid',
                        fgColor: { argb: 'FFD9EAD3' } // Light green
                    };

                    const dottedBorder = {
                        top: { style: 'thin', color: { argb: 'FF000000' } },
                        left: { style: 'thin', color: { argb: 'FF000000' } },
                        bottom: { style: 'thin', color: { argb: 'FF000000' } },
                        right: { style: 'thin', color: { argb: 'FF000000' } }
                    };

                    let rowNum = 1;

                    // Row 1: Title - "TARGET PRODUKSI CETAK 19" - merged A1:K1 (menambahkan kolom K untuk Status)
                    worksheet.mergeCells(`A${rowNum}:K${rowNum}`);
                    const titleCell = worksheet.getCell(`A${rowNum}`);
                    titleCell.value = 'TARGET PRODUKSI CETAK 19';
                    titleCell.font = { bold: true, size: 14 };
                    titleCell.fill = lightGreenFill;
                    titleCell.alignment = { horizontal: 'center', vertical: 'middle' };
                    titleCell.border = dottedBorder;
                    rowNum++; // Move to row 2

                    // Row 2: Empty
                    rowNum++;

                    // Row 3: Day and Date
                    worksheet.getCell(`A${rowNum}`).value = 'Hari:';
                    worksheet.getCell(`A${rowNum}`).font = { bold: true };
                    worksheet.getCell(`A${rowNum}`).border = dottedBorder;

                    worksheet.getCell(`B${rowNum}`).value = dayName;
                    worksheet.getCell(`B${rowNum}`).font = { bold: false };
                    worksheet.getCell(`B${rowNum}`).border = dottedBorder;

                    // Merge kolom I, J, dan K untuk bagian tanggal
                    worksheet.mergeCells(`I${rowNum}:K${rowNum}`);
                    const dateHeaderCell = worksheet.getCell(`I${rowNum}`);
                    dateHeaderCell.value = `Tanggal: ${dateStr}`;
                    dateHeaderCell.font = { bold: true };
                    dateHeaderCell.alignment = { horizontal: 'left', vertical: 'middle' };
                    dateHeaderCell.border = dottedBorder;
                    rowNum++; // Move to row 4

                    // Row 4: Empty
                    rowNum++;

                    // Row 5: Headers
                    // Kolom J (10) dan K (11) dihapus - tidak digunakan (sebelumnya Setup dan Istirahat)
                    // Kolom Delivery sekarang di kolom J (10), bukan L (12)
                    // Menambahkan kolom Status di kolom K (11)
                    const headers = [
                        { col: 1, value: 'Mesin' },      // A
                        { col: 2, value: 'SHIFT' },       // B
                        { col: 3, value: 'Nama Order' },  // C
                        { col: 4, value: 'Kode Design' }, // D
                        { col: 5, value: 'Target Sheet' }, // E
                        { col: 6, value: 'Up' },          // F
                        { col: 7, value: 'No. WO' },       // G
                        { col: 8, value: 'Catatan' },     // H (Job Order)
                        { col: 9, value: 'Keterangan' },   // I (Ukuran Kertas)
                        { col: 10, value: 'Delivery' },    // J
                        { col: 11, value: 'Status' }      // K (NEW/REPEAT)
                    ];

                    headers.forEach(({ col, value }) => {
                        const cell = worksheet.getCell(rowNum, col);
                        cell.value = value;
                        cell.font = { bold: true, size: 11 };
                        cell.fill = lightGreenFill;
                        cell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
                        cell.border = dottedBorder;
                    });
                    rowNum++;

                    // Helper function to determine shift from start_jam
                    const getShift = (startJam) => {
                        if (!startJam) return '-';
                        const startTime = new Date(startJam);
                        const hour = startTime.getHours();
                        const minutes = startTime.getMinutes();
                        const totalMinutes = hour * 60 + minutes;

                        if (totalMinutes >= 480 && totalMinutes < 960) {
                            return 'SHIFT 1';
                        } else if (totalMinutes >= 960 && totalMinutes < 1440) {
                            return 'SHIFT 2';
                        } else if (totalMinutes >= 0 && totalMinutes < 480) {
                            return 'SHIFT 3';
                        }
                        return '-';
                    };

                    // Helper function to format datetime for start cetak - DISABLED
                    // const formatStartCetak = (startJam) => {
                    //     if (!startJam) return '-';
                    //     const start = new Date(startJam);
                    //     const dateStr = `${String(start.getDate()).padStart(2, '0')}/${String(start.getMonth() + 1).padStart(2, '0')}/${start.getFullYear()}`;
                    //     const timeStr = `${String(start.getHours()).padStart(2, '0')}:${String(start.getMinutes()).padStart(2, '0')}`;
                    //     return `${dateStr} ${timeStr}`;
                    // };

                    // Helper function to format datetime for end cetak - DISABLED
                    // const formatEndCetak = (endJam) => {
                    //     if (!endJam) return '-';
                    //     const end = new Date(endJam);
                    //     const dateStr = `${String(end.getDate()).padStart(2, '0')}/${String(end.getMonth() + 1).padStart(2, '0')}/${end.getFullYear()}`;
                    //     const timeStr = `${String(end.getHours()).padStart(2, '0')}:${String(end.getMinutes()).padStart(2, '0')}`;
                    //     return `${dateStr} ${timeStr}`;
                    // };

                    // Helper function to sort and group data
                    const sortAndGroupData = (data, isCadangan = false) => {
                        let sorted;

                        if (isCadangan) {
                            // For cadangan: sort by tanggal cetak, shift, then waktu mulai cetak
                            sorted = data.sort((a, b) => {
                                // First sort by machine
                                if (a.code_machine !== b.code_machine) {
                                    return a.code_machine.localeCompare(b.code_machine);
                                }

                                // Get tanggal cetak (date only, ignore time)
                                const getDateOnly = (item) => {
                                    if (item.start_jam) {
                                        const date = new Date(item.start_jam);
                                        date.setHours(0, 0, 0, 0);
                                        return date.getTime();
                                    } else if (item.delivery_date) {
                                        const date = new Date(item.delivery_date);
                                        date.setHours(0, 0, 0, 0);
                                        return date.getTime();
                                    }
                                    return 0;
                                };

                                const aDate = getDateOnly(a);
                                const bDate = getDateOnly(b);

                                // Sort by tanggal cetak first
                                if (aDate !== bDate) {
                                    return aDate - bDate;
                                }

                                // Then sort by shift
                                const getShiftNumber = (item) => {
                                    if (!item.start_jam) return 999; // No shift = last
                                    const startTime = new Date(item.start_jam);
                                    const hour = startTime.getHours();
                                    const minutes = startTime.getMinutes();
                                    const totalMinutes = hour * 60 + minutes;

                                    if (totalMinutes >= 480 && totalMinutes < 960) return 1; // SHIFT 1
                                    if (totalMinutes >= 960 && totalMinutes < 1440) return 2; // SHIFT 2
                                    if (totalMinutes >= 0 && totalMinutes < 480) return 3; // SHIFT 3
                                    return 999; // CADANGAN or unknown
                                };

                                const aShift = getShiftNumber(a);
                                const bShift = getShiftNumber(b);

                                if (aShift !== bShift) {
                                    return aShift - bShift;
                                }

                                // Finally sort by waktu mulai cetak (start_jam)
                                const aStart = a.start_jam ? new Date(a.start_jam).getTime() : 0;
                                const bStart = b.start_jam ? new Date(b.start_jam).getTime() : 0;

                                return aStart - bStart;
                            });
                        } else {
                            // For today data: sort by machine and priority (original logic)
                            sorted = data.sort((a, b) => {
                                // First sort by machine
                                if (a.code_machine !== b.code_machine) {
                                    return a.code_machine.localeCompare(b.code_machine);
                                }
                                // Then sort by order in timelineData to maintain priority
                                const aIndex = timelineData.findIndex(item => item.id === a.id);
                                const bIndex = timelineData.findIndex(item => item.id === b.id);
                                return aIndex - bIndex;
                            });
                        }

                        // Group by machine
                        const groups = {};
                        sorted.forEach((item) => {
                            const machineCode = item.code_machine || '-';
                            if (!groups[machineCode]) {
                                groups[machineCode] = [];
                            }
                            groups[machineCode].push(item);
                        });
                        return groups;
                    };

                    // Helper function to process data for 2 shift mode (same as populateMachineTable)
                    const processDataForShift = (machineData, numShifts) => {
                        if (numShifts === 3) {
                            return machineData; // No processing needed for 3 shifts
                        }

                        // If 2 shifts, split items that cross midnight (00:00)
                        const splitItems = [];

                        machineData.forEach(item => {
                            if (!item.start_jam || !item.end_jam) {
                                splitItems.push(item);
                                return;
                            }

                            const startTime = new Date(item.start_jam);
                            const endTime = new Date(item.end_jam);

                            // Check if item crosses midnight (starts before 00:00 and ends after 00:00)
                            const startDay = new Date(startTime);
                            startDay.setHours(0, 0, 0, 0);
                            const endDay = new Date(endTime);
                            endDay.setHours(0, 0, 0, 0);

                            const crossesMidnight = endDay.getTime() > startDay.getTime();

                            if (crossesMidnight) {
                                // Split item into two parts
                                const midnight = new Date(startTime);
                                midnight.setDate(midnight.getDate() + 1);
                                midnight.setHours(0, 0, 0, 0);

                                // Calculate durations
                                const beforeMidnightMs = midnight.getTime() - startTime.getTime();
                                const afterMidnightMs = endTime.getTime() - midnight.getTime();
                                const afterMidnightHours = afterMidnightMs / (1000 * 60 * 60);

                                // Get capacity
                                const capacity = parseFloat(item.up_cetak || item.capacity || 10000);
                                const totalQuantity = parseFloat(item.quantity || 0);

                                // Quantity after midnight = durasi setelah tengah malam (jam) * kapasitas
                                const quantityAfter = Math.round(afterMidnightHours * capacity);

                                // Quantity before midnight = total quantity - quantity after
                                const quantityBefore = totalQuantity - quantityAfter;

                                // Item 1: Before midnight (original start to 00:00)
                                const item1 = { ...item };
                                item1.start_jam = startTime.toISOString();
                                item1.end_jam = midnight.toISOString();
                                item1.quantity = quantityBefore;

                                // Item 2: After midnight (moved to next day at 08:00)
                                const item2 = { ...item };
                                const nextDay = new Date(midnight);
                                nextDay.setHours(8, 0, 0, 0);

                                // Calculate end time for item 2 based on quantity and capacity
                                const durationAfterHours = quantityAfter / capacity;
                                const endTimeNextDay = new Date(nextDay);
                                endTimeNextDay.setTime(nextDay.getTime() + (durationAfterHours * 60 * 60 * 1000));

                                item2.start_jam = nextDay.toISOString();
                                item2.end_jam = endTimeNextDay.toISOString();
                                item2.quantity = quantityAfter;

                                // Generate new ID for item2 to avoid conflicts
                                item2.id = item.id + '_split_' + Date.now();

                                splitItems.push(item1);
                                splitItems.push(item2);
                            } else {
                                // Item doesn't cross midnight, but check if it's in Shift 3 (00:00-08:00)
                                const startHour = startTime.getHours();
                                const startMinutes = startTime.getMinutes();
                                const totalMinutes = startHour * 60 + startMinutes;

                                if (totalMinutes >= 0 && totalMinutes < 480) {
                                    // Move Shift 3 item to next day at 08:00
                                    const newItem = { ...item };
                                    const nextDay = new Date(startTime);
                                    nextDay.setDate(nextDay.getDate() + 1);
                                    nextDay.setHours(8, 0, 0, 0);

                                    // Adjust end_jam accordingly (maintain duration)
                                    const duration = endTime.getTime() - startTime.getTime();
                                    const newEndDate = new Date(nextDay);
                                    newEndDate.setTime(nextDay.getTime() + duration);

                                    newItem.start_jam = nextDay.toISOString();
                                    newItem.end_jam = newEndDate.toISOString();
                                    splitItems.push(newItem);
                                } else {
                                    // Normal item, no change
                                    splitItems.push(item);
                                }
                            }
                        });

                        return splitItems;
                    };

                    // Helper function to add data rows for a machine
                    const addMachineDataRows = (machineCode, machineData, isCadangan = false) => {
                        // Process data based on shift config for this machine
                        const numShifts = machineShiftConfig[machineCode] || 3;
                        machineData = processDataForShift(machineData, numShifts);

                        // Sort all processed data by waktu cetak (start_jam) - ascending
                        // This applies to both today and cadangan data
                        machineData.sort((a, b) => {
                            const aStart = a.start_jam ? new Date(a.start_jam).getTime() : 0;
                            const bStart = b.start_jam ? new Date(b.start_jam).getTime() : 0;
                            return aStart - bStart;
                        });

                        const dataStartRow = rowNum;

                        // Group machine data by shift (maintaining sorted order by waktu cetak)
                        // Items are already sorted by waktu cetak, so we just group them by shift
                        // while preserving the chronological order
                        const shiftGroups = {};
                        machineData.forEach((item) => {
                            const shift = getShift(item.start_jam);
                            if (!shiftGroups[shift]) {
                                shiftGroups[shift] = [];
                            }
                            shiftGroups[shift].push(item);
                        });

                        // For cadangan: display in chronological order (waktu cetak), but group by shift
                        // So we need to process items in order of waktu cetak, but show shift labels
                        // when shift changes
                        let groupsToProcess = [];
                        if (isCadangan) {
                            // For cadangan: process items in chronological order, grouping consecutive items with same shift
                            let currentShift = null;
                            let currentGroup = null;

                            machineData.forEach((item) => {
                                const shift = getShift(item.start_jam);
                                if (shift !== currentShift) {
                                    // Shift changed, create new group
                                    if (currentGroup) {
                                        groupsToProcess.push(currentGroup);
                                    }
                                    currentShift = shift;
                                    currentGroup = { date: null, shift, items: [item] };
                                } else {
                                    // Same shift, add to current group
                                    currentGroup.items.push(item);
                                }
                            });

                            // Add last group
                            if (currentGroup) {
                                groupsToProcess.push(currentGroup);
                            }
                        } else {
                            // For today data: group by shift only (original logic)
                            const shiftOrder = ['SHIFT 1', 'SHIFT 2', 'SHIFT 3', '-'];
                            shiftOrder.forEach(shift => {
                                if (shiftGroups[shift] && shiftGroups[shift].length > 0) {
                                    groupsToProcess.push({ date: null, shift, items: shiftGroups[shift] });
                                }
                            });
                            // Add any remaining shifts not in standard order
                            Object.keys(shiftGroups).forEach(shift => {
                                if (!shiftOrder.includes(shift) && shiftGroups[shift].length > 0) {
                                    groupsToProcess.push({ date: null, shift, items: shiftGroups[shift] });
                                }
                            });
                        }

                        // Add data rows grouped by date and shift
                        groupsToProcess.forEach((group, groupIndex) => {
                            const shiftData = group.items;
                            const shiftName = group.shift; // Get shift name from group
                            const shiftStartRow = rowNum;

                            // Add data rows for this shift
                            shiftData.forEach((item) => {
                                // DISABLED: Start Cetak dan End Cetak di-comment
                                // const startCetak = formatStartCetak(item.start_jam);
                                // const endCetak = formatEndCetak(item.end_jam);
                                const deliveryDate = item.delivery_date || item.datetgs || '-';

                                // Row data: Mesin, SHIFT, Nama Order, Kode Design, Target Sheet, Up, No. WO, Catatan (Job Order), Keterangan (Ukuran Kertas), Delivery, Status
                                // (Start Cetak dan End Cetak di-comment)
                                const row = worksheet.getRow(rowNum);
                                // Set row height agar tidak terlihat sesak
                                row.height = 20; // Tinggi baris 20 points
                                // Don't set mesin and shift value here, will be set after merge
                                row.getCell(3).value = item.material_name || '-';
                                row.getCell(4).value = item.code_item || '-';
                                row.getCell(5).value = parseInt(item.quantity || 0);
                                row.getCell(6).value = parseInt(item.up_cetak || '-');
                                row.getCell(7).value = item.wo_docno || '-';
                                row.getCell(8).value = item.job_order || item.job_order_no || '-'; // Catatan - Job Order
                                row.getCell(9).value = item.ukuran_kertas || item.keterangan_item || '-'; // Keterangan - Ukuran Kertas
                                // Kolom J (10) sekarang adalah Delivery
                                row.getCell(10).value = deliveryDate;
                                // Kolom K (11) adalah Status - hanya NEW/REPEAT
                                const statusValue = item.job_order_status || item.status_item || 'NEW';
                                row.getCell(11).value = (statusValue.toUpperCase() === 'REPEAT') ? 'REPEAT' : 'NEW';

                                // Apply borders to all cells (kolom 1-11, termasuk Status)
                                for (let i = 1; i <= 11; i++) {
                                    const cell = row.getCell(i);
                                    cell.border = dottedBorder;
                                    // Set center align untuk semua cells
                                    cell.alignment = {
                                        vertical: 'middle',
                                        wrapText: false // Tidak ada wrap text
                                    };
                                    // Set horizontal alignment berdasarkan kolom
                                    if (i === 3 || i === 9 || i === 8) { // Nama Order, Keterangan, Catatan
                                        cell.alignment.horizontal = 'left';
                                    } else if (i === 5 || i === 6 || i === 7 || i === 10 || i === 11) { // Target Sheet, Up, No. WO, Delivery, Status
                                        cell.alignment.horizontal = 'center';
                                    } else {
                                        cell.alignment.horizontal = 'left';
                                    }
                                    // Tidak perlu fill untuk data rows, hanya header yang berwarna hijau
                                }

                                rowNum++;
                            });

                            const shiftEndRow = rowNum - 1;

                            // Merge shift cell if there's more than 1 row
                            if (shiftData.length > 1) {
                                worksheet.mergeCells(`B${shiftStartRow}:B${shiftEndRow}`);
                            }

                            // Set shift value and styling
                            const shiftCell = worksheet.getCell(`B${shiftStartRow}`);
                            shiftCell.value = shiftName; // Use shiftName from group
                            shiftCell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
                            shiftCell.border = dottedBorder;
                            // Tidak perlu fill untuk shift cell
                        });

                        const dataEndRow = rowNum - 1;

                        // Return the row range for this data batch
                        return {
                            startRow: dataStartRow,
                            endRow: dataEndRow,
                            rowCount: machineData.length
                        };
                    };

                    // Group data by machine, combining today and cadangan data
                    const todayMachineGroups = sortAndGroupData(todayData, false);
                    const cadanganMachineGroups = sortAndGroupData(cadanganData, true);

                    // Get all unique machine codes from both groups
                    const allMachineCodes = [...new Set([
                        ...Object.keys(todayMachineGroups),
                        ...Object.keys(cadanganMachineGroups)
                    ])].sort();

                    // Add data for each machine (today first, then cadangan if exists)
                    allMachineCodes.forEach((machineCode, machineIndex) => {
                        const todayMachineData = todayMachineGroups[machineCode] || [];
                        const cadanganMachineData = cadanganMachineGroups[machineCode] || [];

                        const machineStartRow = rowNum;
                        let cadanganHeaderRow = null;

                        // Add today's data for this machine
                        if (todayMachineData.length > 0) {
                            addMachineDataRows(machineCode, todayMachineData, false);
                        }

                        // Add CADANGAN data for this machine (if exists)
                        if (cadanganMachineData.length > 0) {
                            // Add CADANGAN header row (merged) - termasuk kolom K (Status)
                            cadanganHeaderRow = rowNum;
                            worksheet.mergeCells(`A${rowNum}:K${rowNum}`);
                            const cadanganHeaderCell = worksheet.getCell(`A${rowNum}`);
                            cadanganHeaderCell.value = 'CADANGAN';
                            cadanganHeaderCell.font = { bold: true, size: 11 };
                            cadanganHeaderCell.fill = {
                                type: 'pattern',
                                pattern: 'solid',
                                fgColor: { argb: 'FFFFE699' } // Light yellow
                            };
                            cadanganHeaderCell.alignment = { horizontal: 'center', vertical: 'middle' };
                            cadanganHeaderCell.border = dottedBorder;
                            rowNum++;

                            // Add CADANGAN data for this machine
                            addMachineDataRows(machineCode, cadanganMachineData, true);
                        }

                        const machineEndRow = rowNum - 1;

                        // Merge mesin cell for entire machine group (today + cadangan data rows only, exclude CADANGAN header)
                        // Calculate data rows only (exclude CADANGAN header row)
                        let dataStartRow = machineStartRow;
                        let dataEndRow = machineEndRow;

                        if (cadanganHeaderRow !== null) {
                            // If there's CADANGAN header, merge separately for today and cadangan data
                            // Today data: from machineStartRow to cadanganHeaderRow - 1
                            // CADANGAN data: from cadanganHeaderRow + 1 to machineEndRow

                            // Merge today data
                            if (cadanganHeaderRow > machineStartRow) {
                                const todayEndRow = cadanganHeaderRow - 1;
                                if (todayEndRow >= machineStartRow) {
                                    const todayRows = todayEndRow - machineStartRow + 1;
                                    if (todayRows > 1) {
                                        worksheet.mergeCells(`A${machineStartRow}:A${todayEndRow}`);
                                    }
                                    const todayMesinCell = worksheet.getCell(`A${machineStartRow}`);
                                    todayMesinCell.value = machineCode;
                                    todayMesinCell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
                                    todayMesinCell.border = dottedBorder;
                                    // Tidak perlu fill untuk mesin cell
                                }
                            }

                            // Merge CADANGAN data
                            const cadanganDataStartRow = cadanganHeaderRow + 1;
                            if (machineEndRow >= cadanganDataStartRow) {
                                const cadanganRows = machineEndRow - cadanganDataStartRow + 1;
                                if (cadanganRows > 1) {
                                    worksheet.mergeCells(`A${cadanganDataStartRow}:A${machineEndRow}`);
                                }
                                const cadanganMesinCell = worksheet.getCell(`A${cadanganDataStartRow}`);
                                cadanganMesinCell.value = machineCode;
                                cadanganMesinCell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
                                cadanganMesinCell.border = dottedBorder;
                                // Tidak perlu fill untuk mesin cell
                            }
                        } else {
                            // No CADANGAN, just merge all data rows
                            if (machineEndRow >= machineStartRow) {
                                const totalRows = machineEndRow - machineStartRow + 1;
                                if (totalRows > 1) {
                                    worksheet.mergeCells(`A${machineStartRow}:A${machineEndRow}`);
                                }
                                const mesinCell = worksheet.getCell(`A${machineStartRow}`);
                                mesinCell.value = machineCode;
                                mesinCell.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
                                mesinCell.border = dottedBorder;
                                // Tidak perlu fill untuk mesin cell
                            }
                        }

                        // Add empty row after machine group (except for last machine)
                        if (machineIndex < allMachineCodes.length - 1) {
                            rowNum++;
                        }
                    });

                    // Add empty row before approval section
                    rowNum++;

                    // Approval/Signature section at the bottom
                    // Row: Ka. Unit and PPIC headers
                    const approvalStartRow = rowNum;

                    // Ka. Unit header (column B)
                    worksheet.getCell(`B${rowNum}`).value = 'Ka. Unit';
                    worksheet.getCell(`B${rowNum}`).font = { bold: true };
                    worksheet.getCell(`B${rowNum}`).border = dottedBorder;
                    worksheet.getCell(`B${rowNum}`).alignment = { horizontal: 'center', vertical: 'middle' };

                    // PPIC header (column H)
                    worksheet.getCell(`H${rowNum}`).value = 'PPIC';
                    worksheet.getCell(`H${rowNum}`).font = { bold: true };
                    worksheet.getCell(`H${rowNum}`).border = dottedBorder;
                    worksheet.getCell(`H${rowNum}`).alignment = { horizontal: 'center', vertical: 'middle' };
                    rowNum++;

                    // Row: Date (format: day 'year, e.g., "21 '25")
                    const approvalDate = `${today.getDate()} '${String(today.getFullYear()).slice(-2)}`;
                    worksheet.getCell(`B${rowNum}`).value = approvalDate;
                    worksheet.getCell(`B${rowNum}`).border = dottedBorder;
                    worksheet.getCell(`B${rowNum}`).alignment = { horizontal: 'center', vertical: 'middle' };

                    worksheet.getCell(`H${rowNum}`).value = approvalDate;
                    worksheet.getCell(`H${rowNum}`).border = dottedBorder;
                    worksheet.getCell(`H${rowNum}`).alignment = { horizontal: 'center', vertical: 'middle' };
                    rowNum++;

                    // Row: Number (11)
                    worksheet.getCell(`B${rowNum}`).value = '';
                    worksheet.getCell(`B${rowNum}`).border = dottedBorder;
                    worksheet.getCell(`B${rowNum}`).alignment = { horizontal: 'center', vertical: 'middle' };

                    worksheet.getCell(`H${rowNum}`).value = '';
                    worksheet.getCell(`H${rowNum}`).border = dottedBorder;
                    worksheet.getCell(`H${rowNum}`).alignment = { horizontal: 'center', vertical: 'middle' };
                    rowNum++;

                    // Row: Name (BP.ERWIN and MIARTI) - underlined
                    worksheet.getCell(`B${rowNum}`).value = 'BP.ERWIN';
                    worksheet.getCell(`B${rowNum}`).font = { underline: true };
                    worksheet.getCell(`B${rowNum}`).border = dottedBorder;
                    worksheet.getCell(`B${rowNum}`).alignment = { horizontal: 'center', vertical: 'middle' };

                    worksheet.getCell(`H${rowNum}`).value = 'MIARTI';
                    worksheet.getCell(`H${rowNum}`).font = { underline: true };
                    worksheet.getCell(`H${rowNum}`).border = dottedBorder;
                    worksheet.getCell(`H${rowNum}`).alignment = { horizontal: 'center', vertical: 'middle' };
                    rowNum++;

                    // Add empty row
                    rowNum++;

                    // Row: Document code at bottom left (QF.KOP - PP - 8.1 - 004 REV 003)
                    worksheet.mergeCells(`A${rowNum}:K${rowNum}`);
                    const docCodeCell = worksheet.getCell(`A${rowNum}`);
                    docCodeCell.value = 'QF.KOP - PP - 8.1 - 004 REV 003';
                    docCodeCell.font = { size: 9 };
                    docCodeCell.alignment = { horizontal: 'left', vertical: 'middle' };
                    rowNum++;

                    // Set column widths (menambahkan kolom K untuk Status)
                    worksheet.columns = [
                        { width: 8 }, // A: Mesin
                        { width: 9 }, // B: SHIFT
                        { width: 35 }, // C: Nama Order - diperpendek
                        { width: 12 }, // D: Kode Design - diperpendek
                        { width: 10 }, // E: Target Sheet - diperpendek
                        { width: 6 },  // F: Up
                        { width: 18 }, // G: No. WO
                        { width: 18 }, // H: Catatan (Job Order)
                        { width: 18 }, // I: Keterangan (Ukuran Kertas) - diperlebar
                        { width: 12 }, // J: Delivery
                        { width: 10 }  // K: Status
                    ];

                    // Set page margins to minimum (dipetekan ke tepi) agar text lebih besar
                    worksheet.pageSetup = {
                        margins: {
                            left: 0.3,   // 0.3 inch (minimum)
                            right: 0.3,  // 0.3 inch (minimum)
                            top: 0.3,    // 0.3 inch (minimum)
                            bottom: 0.3, // 0.3 inch (minimum)
                            header: 0.2, // 0.2 inch
                            footer: 0.2  // 0.2 inch
                        },
                        paperSize: 9, // A4
                        orientation: 'landscape', // Landscape untuk lebih banyak kolom
                        fitToPage: true,
                        fitToWidth: 1,
                        fitToHeight: 0
                    };

                    // Generate filename
                    let filename = `TARGET_PRODUKSI_CETAK_19_${today.getDate()}-${months[today.getMonth()]}-${today.getFullYear()}.xlsx`;

                    // Write file
                    const buffer = await workbook.xlsx.writeBuffer();
                    const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    const url = window.URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = filename;
                    link.click();
                    window.URL.revokeObjectURL(url);

                    Utils.showToast('✅ Excel berhasil di-export', 'success');
                } catch (error) {
                    console.error('❌ Error exporting Excel:', error);
                    Utils.showToast('❌ Gagal export Excel: ' + (error.message || 'Unknown error'), 'error');
                }
            },

            // UP/DOWN Priority Management - ENABLED
            movePriorityUp(itemId, machineCode, currentPriority) {
                if (currentPriority === 0) {
                    Utils.showToast('Item sudah berada di prioritas tertinggi', 'warning');
                    return;
                }

                try {
                    console.log(
                        `🔄 Moving priority UP for item ${itemId} in machine ${machineCode} from priority ${currentPriority}`
                    );

                    // Backup data before change to prevent data loss
                    const backupData = [...timelineData];
                    console.log(`💾 Backup created: ${backupData.length} items`);

                    // Check data integrity before change
                    const machineData = this.checkDataIntegrity(machineCode);
                    console.log(`📋 Machine ${machineCode} has ${machineData.length} items`);

                    // Debug: Show current machine data
                    console.log(`📊 Before change - Machine ${machineCode} items:`,
                        machineData.map((item, idx) => `${idx + 1}. ${item.code_item} (ID: ${item.id})`));

                    if (machineData.length < 2) {
                        Utils.showToast('Tidak cukup item untuk mengubah prioritas', 'warning');
                        return;
                    }

                    // Find current item and item above it
                    const currentItem = machineData[currentPriority];
                    const itemAbove = machineData[currentPriority - 1];

                    if (!currentItem || !itemAbove) {
                        Utils.showToast('Gagal mengubah prioritas: item tidak ditemukan', 'error');
                        return;
                    }

                    console.log(`📦 Current item: ${currentItem.code_item} (priority ${currentPriority})`);
                    console.log(`📦 Item above: ${itemAbove.code_item} (priority ${currentPriority - 1})`);

                    // Swap positions in timelineData array
                    const currentIndex = timelineData.findIndex(item => item.id === currentItem.id);
                    const aboveIndex = timelineData.findIndex(item => item.id === itemAbove.id);

                    if (currentIndex !== -1 && aboveIndex !== -1) {
                        console.log(`🔄 Swapping items at positions ${currentIndex} and ${aboveIndex}`);

                        // Create temporary copy to avoid reference issues
                        const tempItem = {
                            ...timelineData[currentIndex]
                        };
                        timelineData[currentIndex] = {
                            ...timelineData[aboveIndex]
                        };
                        timelineData[aboveIndex] = tempItem;

                        console.log(`✅ Items swapped successfully`);

                        // Debug: Check data after swap
                        const afterSwapData = timelineData.filter(item => item.code_machine === machineCode);
                        console.log(`📊 After swap - Machine ${machineCode} has ${afterSwapData.length} items:`,
                            afterSwapData.map((item, idx) => `${idx + 1}. ${item.code_item}`));

                        // Check data integrity after change
                        this.checkDataIntegrity(machineCode);

                        // Validate data integrity after change
                        const afterChangeData = timelineData.filter(item => item.code_machine === machineCode);
                        if (afterChangeData.length !== machineData.length) {
                            console.error(
                                `❌ Data loss detected! Before: ${machineData.length}, After: ${afterChangeData.length}`
                            );
                            // Restore data from backup
                            timelineData = [...backupData];
                            console.log(`🔄 Data restored from backup due to data loss`);
                            Utils.showToast('Data loss detected, perubahan dibatalkan', 'error');
                            return;
                        }

                        // Re-sync machine times after priority change (DENGAN mengupdate data asli)
                        this.syncMachineTimesAndUpdateData(machineCode);

                        // Re-render table to show new order (only once)
                        // job order
                        this.checkJobOrderStatusForMachine(machineCode, machineData);

                        Utils.showToast(`Prioritas item ${currentItem.code_item} berhasil dinaikkan`, 'success');
                    } else {
                        console.error('❌ Failed to find items in timelineData');
                        Utils.showToast('Gagal mengubah prioritas: item tidak ditemukan dalam data', 'error');

                        // Restore data from backup if swap failed
                        timelineData = [...backupData];
                        console.log(`🔄 Data restored from backup: ${timelineData.length} items`);
                    }

                } catch (error) {
                    console.error('Error moving priority up:', error);
                    Utils.showToast('Gagal mengubah prioritas: ' + error.message, 'error');
                }
            },

            movePriorityDown(itemId, machineCode, currentPriority) {
                const machineData = timelineData.filter(item => item.code_machine === machineCode);

                if (currentPriority === machineData.length - 1) {
                    Utils.showToast('Item sudah berada di prioritas terendah', 'warning');
                    return;
                }

                try {
                    console.log(
                        `🔄 Moving priority DOWN for item ${itemId} in machine ${machineCode} from priority ${currentPriority}`
                    );

                    // Backup data before change to prevent data loss
                    const backupData = [...timelineData];
                    console.log(`💾 Backup created: ${backupData.length} items`);

                    // Check data integrity before change
                    const machineData = this.checkDataIntegrity(machineCode);

                    if (machineData.length < 2) {
                        Utils.showToast('Tidak cukup item untuk mengubah prioritas', 'warning');
                        return;
                    }

                    // Find current item and item below it
                    const currentItem = machineData[currentPriority];
                    const itemBelow = machineData[currentPriority + 1];

                    if (!currentItem || !itemBelow) {
                        Utils.showToast('Gagal mengubah prioritas: item tidak ditemukan', 'error');
                        return;
                    }

                    console.log(`📦 Current item: ${currentItem.code_item} (priority ${currentPriority})`);
                    console.log(`📦 Item below: ${itemBelow.code_item} (priority ${currentPriority + 1})`);

                    // Swap positions in timelineData array
                    const currentIndex = timelineData.findIndex(item => item.id === currentItem.id);
                    const belowIndex = timelineData.findIndex(item => item.id === itemBelow.id);

                    if (currentIndex !== -1 && belowIndex !== -1) {
                        console.log(`🔄 Swapping items at positions ${currentIndex} and ${belowIndex}`);

                        // Create temporary copy to avoid reference issues
                        const tempItem = {
                            ...timelineData[currentIndex]
                        };
                        timelineData[currentIndex] = {
                            ...timelineData[belowIndex]
                        };
                        timelineData[belowIndex] = tempItem;

                        console.log(`✅ Items swapped successfully`);

                        // Debug: Check data after swap
                        const afterSwapData = timelineData.filter(item => item.code_machine === machineCode);
                        console.log(`📊 After swap - Machine ${machineCode} has ${afterSwapData.length} items:`,
                            afterSwapData.map((item, idx) => `${idx + 1}. ${item.code_item}`));

                        // Check data integrity after change
                        this.checkDataIntegrity(machineCode);

                        // Validate data integrity after change
                        const afterChangeData = timelineData.filter(item => item.code_machine === machineCode);
                        if (afterChangeData.length !== machineData.length) {
                            console.error(
                                `❌ Data loss detected! Before: ${machineData.length}, After: ${afterChangeData.length}`
                            );
                            // Restore data from backup
                            timelineData = [...backupData];
                            console.log(`🔄 Data restored from backup due to data loss`);
                            Utils.showToast('Data loss detected, perubahan dibatalkan', 'error');
                            return;
                        }

                        // Re-sync machine times after priority change (DENGAN mengupdate data asli)
                        this.syncMachineTimesAndUpdateData(machineCode);

                        // Re-render table to show new order (only once)
                        this.checkJobOrderStatusForMachine(machineCode, machineData);

                        Utils.showToast(`Prioritas item ${currentItem.code_item} berhasil diturunkan`, 'success');
                    } else {
                        console.error('❌ Failed to find items in timelineData');
                        Utils.showToast('Gagal mengubah prioritas: item tidak ditemukan dalam data', 'error');

                        // Restore data from backup if swap failed
                        timelineData = [...backupData];
                        console.log(`🔄 Data restored from backup: ${timelineData.length} items`);
                    }

                } catch (error) {
                    console.error('Error moving priority down:', error);
                    Utils.showToast('Gagal mengubah prioritas: ' + error.message, 'error');
                }
            },

            // Function to update priority directly from input
            updatePriority(itemId, machineCode, newPriority, originalPriority) {
                console.log(`🔄 Updating priority for item ${itemId} in machine ${machineCode} from ${originalPriority} to ${newPriority}`);

                const machineData = timelineData.filter(item => item.code_machine === machineCode);

                if (newPriority < 1 || newPriority > machineData.length) {
                    Utils.showToast(`Prioritas harus antara 1 dan ${machineData.length}`, 'error');
                    return;
                }

                try {
                    // Find the item to move
                    const itemToMove = machineData.find(item => item.id == itemId);
                    if (!itemToMove) {
                        Utils.showToast('Item tidak ditemukan', 'error');
                        return;
                    }

                    // Find the item at the target position
                    const targetItem = machineData[newPriority - 1];
                    if (!targetItem) {
                        Utils.showToast('Posisi target tidak ditemukan', 'error');
                        return;
                    }

                    // If moving to the same position, do nothing
                    if (itemToMove.id === targetItem.id) {
                        Utils.showToast('Item sudah berada di posisi tersebut', 'info');
                        return;
                    }

                    // Get indices in timelineData
                    const itemIndex = timelineData.findIndex(item => item.id === itemToMove.id);
                    const targetIndex = timelineData.findIndex(item => item.id === targetItem.id);

                    if (itemIndex === -1 || targetIndex === -1) {
                        Utils.showToast('Gagal menemukan item dalam data', 'error');
                        return;
                    }

                    // Move item to new position
                    const movedItem = timelineData.splice(itemIndex, 1)[0];
                    timelineData.splice(targetIndex, 0, movedItem);

                    console.log(`✅ Item ${itemToMove.code_item} moved from position ${originalPriority} to ${newPriority}`);

                    // Re-render table to show new order
                    this.renderTable();

                    // Sync machine times
                    this.syncMachineTimesAndUpdateData(machineCode);

                    // Ensure event listeners are still attached after re-render
                    // Event listeners using event delegation should still work, but let's make sure
                    console.log('✅ Table re-rendered after priority change, inputs should be available');

                    Utils.showToast(`Prioritas item ${itemToMove.code_item} berhasil diubah ke ${newPriority}`, 'success');

                } catch (error) {
                    console.error('Error updating priority:', error);
                    Utils.showToast('Gagal mengubah prioritas: ' + error.message, 'error');
                }
            },

            // New function to sync machine times AND update actual data
            syncMachineTimesAndUpdateData(machineCode) {
                console.log(`⏰ Starting syncMachineTimesAndUpdateData for machine ${machineCode}`);

                // Get machine data and sort by priority
                const machineData = timelineData.filter(item => item.code_machine === machineCode);

                // Create DEEP COPY to avoid reference issues
                const orderedItems = machineData.map(item => ({
                    ...item,
                    start_jam: item.start_jam,
                    end_jam: item.end_jam
                })).sort((a, b) => {
                    const aIndex = machineData.findIndex(item => item.id === a.id);
                    const bIndex = machineData.findIndex(item => item.id === b.id);
                    return aIndex - bIndex;
                });

                console.log(`📋 Machine data from timelineData: ${orderedItems.length} items:`,
                    orderedItems.map((item, idx) => `${idx + 1}. ${item.code_item} (${item.wo_docno})`));

                if (orderedItems.length === 0) {
                    console.log(`⚠️ No items found for machine ${machineCode}`);
                    return;
                }

                // Start from 8:00 AM for the first item
                let currentStartTime = new Date();
                currentStartTime.setHours(8, 0, 0, 0);

                // Use the date from the first item
                if (orderedItems[0].start_jam) {
                    const existingDate = new Date(orderedItems[0].start_jam);
                    currentStartTime.setFullYear(existingDate.getFullYear());
                    currentStartTime.setMonth(existingDate.getMonth());
                    currentStartTime.setDate(existingDate.getDate());
                }

                // Calculate new times WITHOUT changing original data
                const updatedItems = orderedItems.map((item, index) => {
                    const machine = allMachines.find(m => m.Code === machineCode);
                    const capacityPerHour = machine ? (machine.CapacityPerHour || 1000) : 1000;
                    const quantity = parseFloat(item.quantity) || 0;

                    const isWOP = item.wo_docno && item.wo_docno.toUpperCase().startsWith('WOP');

                    let durationHours;
                    if (isWOP) {
                        console.log('ini WOP');
                        durationHours = 8;
                    } else {
                        durationHours = quantity / capacityPerHour;
                        console.log('ini WOT');
                    }

                    const durationMs = durationHours * 60 * 60 * 1000;

                    const newStartTime = currentStartTime.toISOString();
                    const endTime = new Date(currentStartTime.getTime() + durationMs);
                    const newEndTime = endTime.toISOString();

                    // Update currentStartTime for next item
                    currentStartTime = new Date(endTime);

                    // Return updated item WITHOUT modifying original
                    return {
                        ...item,
                        start_jam: newStartTime,
                        end_jam: newEndTime
                    };
                });

                // NOW update the original timelineData with new times
                console.log(`🔍 Before update - timelineData length: ${timelineData.length}`);

                // Safety check: verify all items exist before updating
                const allItemsExist = updatedItems.every(updatedItem =>
                    timelineData.some(item => item.id === updatedItem.id)
                );

                if (!allItemsExist) {
                    console.error(`❌ CRITICAL: Some items don't exist in timelineData!`);
                    console.error(`❌ Updated items:`, updatedItems.map(item => item.code_item));
                    console.error(`❌ TimelineData items:`, timelineData.filter(item => item.code_machine ===
                        machineCode).map(item => item.code_item));

                    // Don't proceed with update if data is corrupted
                    Utils.showToast('Data corruption detected, update dibatalkan', 'error');
                    return;
                }

                updatedItems.forEach(updatedItem => {
                    const originalIndex = timelineData.findIndex(item => item.id === updatedItem.id);
                    if (originalIndex !== -1) {
                        timelineData[originalIndex].start_jam = updatedItem.start_jam;
                        timelineData[originalIndex].end_jam = updatedItem.end_jam;
                        console.log(`✅ Updated item ${updatedItem.code_item} in timelineData`);
                    } else {
                        console.error(`❌ Item ${updatedItem.code_item} not found in timelineData!`);
                    }
                });

                console.log(`🔍 After update - timelineData length: ${timelineData.length}`);
                console.log(`🔍 Machine ${machineCode} items in timelineData:`,
                    timelineData.filter(item => item.code_machine === machineCode).map(item => item.code_item));

                // Use the safer function that recreates rows if needed
                this.updateMachineTableDisplayAndReorder(machineCode, updatedItems);

                Utils.showToast(
                    `✅ Jam produksi mesin ${machineCode} berhasil disinkronkan (${updatedItems.length} item)`,
                    'success');
            },

            updateItemTimeDisplay(item) {
                const row = document.querySelector(`[data-item-id="${item.id}"]`);
                if (row) {
                    const startTimeCell = row.querySelector('.start-time');
                    const endTimeCell = row.querySelector('.end-time');

                    if (startTimeCell) {
                        startTimeCell.textContent = Utils.formatTime(item.start_jam);
                    }
                    if (endTimeCell) {
                        endTimeCell.textContent = Utils.formatTime(item.end_jam);
                    }
                }
            },

            /**
             * Setup priority button event listeners for a specific machine
             */
            setupPriorityButtonsForMachine(machineCode) {
                console.log(`🔧 Setting up priority buttons for machine ${machineCode}`);

                const tbody = document.getElementById(`machine-tbody-${machineCode}`);
                if (!tbody) {
                    console.warn(`⚠️ Tbody not found for machine ${machineCode}`);
                    return;
                }

                // Get current machine data to determine correct indices
                const machineData = timelineData.filter(item => item.code_machine === machineCode);
                console.log(`📊 Current machine data for ${machineCode}:`, machineData.map((item, idx) =>
                    `${idx + 1}. ${item.code_item} (ID: ${item.id})`));

                // Find all priority buttons in this machine's table
                const upButtons = tbody.querySelectorAll('.priority-up-btn');
                const downButtons = tbody.querySelectorAll('.priority-down-btn');

                console.log(`🔍 Found ${upButtons.length} up buttons and ${downButtons.length} down buttons`);

                // Remove existing event listeners (if any)
                upButtons.forEach(btn => {
                    btn.replaceWith(btn.cloneNode(true));
                });
                downButtons.forEach(btn => {
                    btn.replaceWith(btn.cloneNode(true));
                });

                // Get fresh references after cloning
                const freshUpButtons = tbody.querySelectorAll('.priority-up-btn');
                const freshDownButtons = tbody.querySelectorAll('.priority-down-btn');

                // Add event listeners to up buttons
                freshUpButtons.forEach((btn, buttonIndex) => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();

                        const itemId = btn.dataset.itemId;

                        // Find the current index of this item in the machine data
                        const currentIndex = machineData.findIndex(item => item.id == itemId);

                        if (currentIndex === -1) {
                            console.error(`❌ Item ${itemId} not found in machine ${machineCode}`);
                            Utils.showToast('Item tidak ditemukan', 'error');
                            return;
                        }

                        console.log(
                            `🔼 Priority UP clicked for item ${itemId} at index ${currentIndex}`);

                        // Show confirmation modal
                        this.showPriorityChangeModal(itemId, machineCode, currentIndex, 'up');
                    });
                });

                // Add event listeners to down buttons
                freshDownButtons.forEach((btn, buttonIndex) => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();

                        const itemId = btn.dataset.itemId;

                        // Find the current index of this item in the machine data
                        const currentIndex = machineData.findIndex(item => item.id == itemId);

                        if (currentIndex === -1) {
                            console.error(`❌ Item ${itemId} not found in machine ${machineCode}`);
                            Utils.showToast('Item tidak ditemukan', 'error');
                            return;
                        }

                        console.log(
                            `🔽 Priority DOWN clicked for item ${itemId} at index ${currentIndex}`);

                        // Show confirmation modal
                        this.showPriorityChangeModal(itemId, machineCode, currentIndex, 'down');
                    });
                });

                console.log(`✅ Priority buttons setup completed for machine ${machineCode}`);
            },

            updateMachineTableDisplay(machineCode, items) {
                console.log(`🔄 Updating table display for machine ${machineCode} with ${items.length} items`);

                const tbody = document.getElementById(`machine-tbody-${machineCode}`);
                if (!tbody) {
                    console.error(`Tbody not found for machine ${machineCode}`);
                    return;
                }

                // Update each row with new times
                items.forEach((item, index) => {
                    const row = tbody.querySelector(`tr[data-item-id="${item.id}"]`);
                    if (row) {
                        // Update job order column (index 4)
                        const jobOrderCell = row.cells[4];
                        if (jobOrderCell) {
                            jobOrderCell.innerHTML = item.job_order || item.job_order_no || '-';
                        }

                        // Update start time column (index 13, was 12 before Job Order column)
                        const startTimeCell = row.cells[13];
                        if (startTimeCell) {
                            startTimeCell.innerHTML = Utils.formatDateTime(item.start_jam);
                        }

                        // Update end time column (index 14, was 13 before Job Order column)
                        const endTimeCell = row.cells[14];
                        if (endTimeCell) {
                            endTimeCell.innerHTML = Utils.formatDateTime(item.end_jam);
                        }

                        // Update status column (index 15, after AKHIR CETAK)
                        const statusCell = row.cells[15];
                        if (statusCell) {
                            const status = item.job_order_status || 'NEW';
                            statusCell.innerHTML = `
                                <select class="form-control form-control-sm status-select"
                                        data-item-id="${item.id}"
                                        data-machine="${machineCode}"
                                        style="width: 100px; font-size: 0.85rem; text-align: center; padding: 2px 4px;">
                                    <option value="NEW" ${status.toUpperCase() === 'NEW' ? 'selected' : ''}>NEW</option>
                                    <option value="REPEAT" ${status.toUpperCase() === 'REPEAT' ? 'selected' : ''}>REPEAT</option>
                                </select>
                            `;
                        }

                        // Update ukuran kertas column (index 16, after STATUS)
                        const ukuranKertasCell = row.cells[16];
                        if (ukuranKertasCell) {
                            const ukuranKertas = item.ukuran_kertas || (item.panjang && item.lebar ? `${item.panjang} x ${item.lebar}` : null);
                            ukuranKertasCell.innerHTML = ukuranKertas || '-';
                        }

                        // Update duration column (index 9, was 8 before Job Order column)
                        const durationCell = row.cells[9];
                        if (durationCell) {
                            // Cek apakah WO diawali dengan "WOP"
                            const isWOP = item.wo_docno && item.wo_docno.toUpperCase().startsWith('WOP');
                            if (isWOP) {
                                durationCell.innerHTML = `8.00 jam`;
                            } else {
                                const duration = Utils.calculateDuration(item.start_jam, item.end_jam);
                                durationCell.innerHTML = `${duration} jam`;
                            }
                        }

                        console.log(`✅ Updated row ${index + 1} for item ${item.code_item}`);
                    }
                });

                // Reload job orders after updating display
                this.loadJobOrdersForMachine(machineCode, items);
            },

            // New function to update display AND reorder rows
            updateMachineTableDisplayAndReorder(machineCode, items) {
                console.log(
                    `🔄 Updating table display AND reordering rows for machine ${machineCode} with ${items.length} items`
                );

                const tbody = document.getElementById(`machine-tbody-${machineCode}`);
                if (!tbody) {
                    console.error(`Tbody not found for machine ${machineCode}`);
                    return;
                }

                // Clear existing rows
                tbody.innerHTML = '';

                // Create new rows in the correct order
                items.forEach((item, index) => {
                    const machine = allMachines.find(m => m.Code === machineCode);

                    const row = document.createElement('tr');
                    row.className =
                        `plan-row ${item.flag_status ? 'status-' + item.flag_status.toLowerCase().replace(/\s/g, '-') : ''}`;
                    row.classList.add('fade-in');
                    row.style.animationDelay = `${index * 0.1}s`;
                    row.setAttribute('data-item-id', item.id);

                    const isMaintenance = item.is_maintenance || item.process === 'Maintenance';
                    if (isMaintenance) {
                        row.classList.add('maintenance-row');
                        row.style.backgroundColor = '#fff3cd';
                        row.style.borderLeft = '4px solid #ffc107';
                    }

                    row.innerHTML = `
                        <td class="priority-cell text-center">
                            <div class="priority-input-container">
                                <input type="number"
                                       class="form-control form-control-sm priority-input text-center"
                                       value="${index + 1}"
                                       min="1"
                                       max="${items.length}"
                                       data-item-id="${item.id}"
                                       data-machine="${machineCode}"
                                       data-original-priority="${index + 1}"
                                       style="width: 60px; font-weight: bold; color: #007bff; font-size: 1.1rem;"
                                       title="Edit Prioritas (1-${items.length})">
                            </div>
                            ${isMaintenance ? `
                                <div class="maintenance-badge mt-1">
                                    <span class="badge badge-warning badge-sm">
                                        <i class="mdi mdi-wrench"></i> MTC
                                    </span>
                                </div>
                            ` : ''}
                        </td>
                        <td><strong>${item.code_item || '-'}</strong>
                            ${isMaintenance ? '<br><small class="text-muted">' + (item.jenis_maintenance || 'Maintenance') + '</small>' : ''}
                        </td>
                        <td>${item.material_name || '-'}</td>
                        <td>${item.wo_docno || '-'}</td>
                        <td>
                            ${item.job_order || item.job_order_no ?
                                `<span class="badge badge-success" style="font-size: 0.85rem; padding: 4px 8px;">
                                    <i class="mdi mdi-check-circle"></i> ${item.job_order || item.job_order_no}
                                </span>` :
                                `<span class="badge badge-danger" style="font-size: 0.85rem; padding: 4px 8px;">
                                    <i class="mdi mdi-alert-circle"></i> Belum Ada
                                </span>`
                            }
                        </td>
                        <td class="text-center">
                            ${(() => {
                                // Semua item bisa edit quantity (kecuali maintenance)
                                const isMaintenance = item.is_maintenance || item.process === 'Maintenance';
                                if (isMaintenance) {
                                    return `${parseInt(item.quantity || 0).toLocaleString()}`;
                                }
                                const quantityValue = parseInt(item.quantity || 0);
                                return `<input type="number"
                                               class="form-control form-control-sm quantity-input"
                                               data-item-id="${item.id}"
                                               data-machine="${machineCode}"
                                               value="${quantityValue}"
                                               min="1"
                                               step="1"
                                               style="width: 100px; text-align: center; font-weight: 600;"
                                               title="Edit Quantity">
                                        `;
                            })()}
                        </td>
                        <td>${item.delivery_date || '-'}</td>
                        <td class="text-center process-badge ${item.process ? item.process.toLowerCase().replace(/\s/g, '-') : ''}">${item.process || '-'}</td>
                        <td>${parseInt(item.capacity || 10000).toLocaleString()}</td>
                        <td>${(() => {
                            const isWOP = item.wo_docno && item.wo_docno.toUpperCase().startsWith('WOP');
                            if (isWOP) {
                                // Hitung duration dari quantity / capacity untuk WOP
                                const machine = allMachines.find(m => m.Code === machineCode);
                                const capacityPerHour = machine ? (machine.CapacityPerHour || 10000) : 10000;
                                const durationHours = parseFloat(item.quantity || 0) / capacityPerHour;
                                return `${durationHours.toFixed(2)} jam`;
                            } else {
                                return `${Utils.calculateDuration(item.start_jam, item.end_jam)} jam`;
                            }
                        })()}</td>
                        <td class="text-center">
                            <input type="number"
                                   class="form-control form-control-sm setup-time-input"
                                   data-item-id="${item.id}"
                                   data-machine="${machineCode}"
                                   value="${item.setup || 0}"
                                   min="0"
                                   step="0.25"
                                   style="width: 80px; text-align: center;"
                                   title="Waktu setup dalam jam">
                        </td>
                        <td class="text-center">
                            <input type="number"
                                   class="form-control form-control-sm break-time-input"
                                   data-item-id="${item.id}"
                                   data-machine="${machineCode}"
                                   value="${item.istirahat || 0}"
                                   min="0"
                                   step="0.25"
                                   style="width: 80px; text-align: center;"
                                   title="Waktu istirahat dalam jam">
                        </td>
                        <td>
                            ${(() => {
                                const isMaintenance = item.is_maintenance || item.process === 'Maintenance';
                                if (isMaintenance) {
                                    return Utils.formatDateTime(item.start_jam);
                                }
                                // Jika item sudah FINISH, tampilkan sebagai teks agar tidak bisa diedit
                                if (item.flag_status === 'FINISH') {
                                    return Utils.formatDateTime(item.start_jam);
                                }
                                // Buat input datetime-local untuk edit tanggal mulai cetak
                                if (!item.start_jam) {
                                    const now = new Date();
                                    const year = now.getFullYear();
                                    const month = String(now.getMonth() + 1).padStart(2, '0');
                                    const day = String(now.getDate()).padStart(2, '0');
                                    const hours = String(now.getHours()).padStart(2, '0');
                                    const minutes = String(now.getMinutes()).padStart(2, '0');
                                    const datetimeValue = `${year}-${month}-${day}T${hours}:${minutes}`;

                                    return `
                                        <input type="datetime-local"
                                               class="form-control form-control-sm date-input start-date-input"
                                               data-item-id="${item.id}"
                                               data-machine="${machineCode}"
                                               value="${datetimeValue}"
                                               style="width: 100%; max-width: 180px; font-size: 0.85rem;"
                                               title="Edit Tanggal & Waktu Mulai Cetak">
                                    `;
                                }

                                try {
                                    const startDate = new Date(item.start_jam);
                                    if (isNaN(startDate.getTime())) {
                                        throw new Error('Invalid date');
                                    }
                                    const year = startDate.getFullYear();
                                    const month = String(startDate.getMonth() + 1).padStart(2, '0');
                                    const day = String(startDate.getDate()).padStart(2, '0');
                                    const hours = String(startDate.getHours()).padStart(2, '0');
                                    const minutes = String(startDate.getMinutes()).padStart(2, '0');
                                    const datetimeValue = `${year}-${month}-${day}T${hours}:${minutes}`;

                                    return `
                                        <input type="datetime-local"
                                               class="form-control form-control-sm date-input start-date-input"
                                               data-item-id="${item.id}"
                                               data-machine="${machineCode}"
                                               value="${datetimeValue}"
                                               style="width: 100%; max-width: 180px; font-size: 0.85rem;"
                                               title="Edit Tanggal & Waktu Mulai Cetak">
                                    `;
                                } catch (e) {
                                    const now = new Date();
                                    const year = now.getFullYear();
                                    const month = String(now.getMonth() + 1).padStart(2, '0');
                                    const day = String(now.getDate()).padStart(2, '0');
                                    const hours = String(now.getHours()).padStart(2, '0');
                                    const minutes = String(now.getMinutes()).padStart(2, '0');
                                    const datetimeValue = `${year}-${month}-${day}T${hours}:${minutes}`;

                                    return `
                                        <input type="datetime-local"
                                               class="form-control form-control-sm date-input start-date-input"
                                               data-item-id="${item.id}"
                                               data-machine="${machineCode}"
                                               value="${datetimeValue}"
                                               style="width: 100%; max-width: 180px; font-size: 0.85rem;"
                                               title="Edit Tanggal & Waktu Mulai Cetak">
                                    `;
                                }
                            })()}
                        </td>
                        <td>${Utils.formatDateTime(item.end_jam)}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                ${item.flag_status === 'FINISH' ? `
                                                <span class="btn btn-success disabled">
                                                    <i class="mdi mdi-check-circle"></i> FINISH
                                                </span>
                                            ` : `
                                                <button class="btn btn-info move-item-btn" data-item-id="${item.id}" title="Pindah ke Mesin Lain">
                                                    <i class="mdi mdi-swap-horizontal"></i>
                                                </button>
                                                <button class="btn btn-warning split-item-btn" data-item-id="${item.id}" title="Bagi Quantity">
                                                    <i class="mdi mdi-call-split"></i>
                                                </button>
                                                <button class="btn btn-success merge-item-btn" data-item-id="${item.id}" title="Gabung dengan Item Lain"
                                                        style="display: ${TableRenderer.canMergeItem(item) ? 'inline-block' : 'none'};">
                                                    <i class="mdi mdi-call-merge"></i>
                                                </button>
                                                <button class="btn btn-success finish-job-btn" data-item-id="${item.id}" title="Selesai Produksi">
                                                    <i class="mdi mdi-check-circle"></i> Finish
                                                </button>
                                            `}
                            </div>
                        </td>
                    `;

                    // Click event removed - no modal on row click

                    tbody.appendChild(row);
                });

                // Setup event listeners for priority buttons in the new rows
                this.setupPriorityButtonsForMachine(machineCode);

                // Load job orders for the updated items
                this.loadJobOrdersForMachine(machineCode, items);

                console.log(`✅ Successfully reordered and updated table for machine ${machineCode}`);
            },

            // Duplicate function - keeping for compatibility
            updateMachineTableDisplayAndReorder(machineCode, items) {
                console.log(
                    `🔄 Updating table display AND reordering rows for machine ${machineCode} with ${items.length} items`
                );

                const tbody = document.getElementById(`machine-tbody-${machineCode}`);
                if (!tbody) {
                    console.error(`Tbody not found for machine ${machineCode}`);
                    return;
                }

                // Clear existing rows
                tbody.innerHTML = '';

                // Create new rows in the correct order
                items.forEach((item, index) => {
                    const machine = allMachines.find(m => m.Code === machineCode);

                    const row = document.createElement('tr');
                    row.className =
                        `plan-row ${item.flag_status ? 'status-' + item.flag_status.toLowerCase().replace(/\s/g, '-') : ''}`;
                    row.classList.add('fade-in');
                    row.style.animationDelay = `${index * 0.1}s`;
                    row.setAttribute('data-item-id', item.id);

                    const isMaintenance = item.is_maintenance || item.process === 'Maintenance';
                    if (isMaintenance) {
                        row.classList.add('maintenance-row');
                        row.style.backgroundColor = '#fff3cd';
                        row.style.borderLeft = '4px solid #ffc107';
                    }

                    row.innerHTML = `
                        <td class="priority-cell text-center">
                            <div class="priority-input-container">
                                <input type="number"
                                       class="form-control form-control-sm priority-input text-center"
                                       value="${index + 1}"
                                       min="1"
                                       max="${items.length}"
                                       data-item-id="${item.id}"
                                       data-machine="${machineCode}"
                                       data-original-priority="${index + 1}"
                                       style="width: 60px; font-weight: bold; color: #007bff; font-size: 1.1rem;"
                                       title="Edit Prioritas (1-${items.length})">
                            </div>
                            ${isMaintenance ? `
                                <div class="maintenance-badge mt-1">
                                    <span class="badge badge-warning badge-sm">
                                        <i class="mdi mdi-wrench"></i> MTC
                                    </span>
                                </div>
                            ` : ''}
                        </td>
                        <td><strong>${item.code_item || '-'}</strong>
                            ${isMaintenance ? '<br><small class="text-muted">' + (item.jenis_maintenance || 'Maintenance') + '</small>' : ''}
                        </td>
                        <td>${item.material_name || '-'}</td>
                        <td>${item.wo_docno || '-'}</td>
                        <td>
                            ${item.job_order || item.job_order_no ?
                                `<span class="badge badge-success" style="font-size: 0.85rem; padding: 4px 8px;">
                                    <i class="mdi mdi-check-circle"></i> ${item.job_order || item.job_order_no}
                                </span>` :
                                `<span class="badge badge-danger" style="font-size: 0.85rem; padding: 4px 8px;">
                                    <i class="mdi mdi-alert-circle"></i> Belum Ada
                                </span>`
                            }
                        </td>
                        <td class="text-center">
                            ${(() => {
                                // Semua item bisa edit quantity (kecuali maintenance)
                                const isMaintenance = item.is_maintenance || item.process === 'Maintenance';
                                if (isMaintenance) {
                                    return `${parseInt(item.quantity || 0).toLocaleString()}`;
                                }
                                const quantityValue = parseInt(item.quantity || 0);
                                return `<input type="number"
                                               class="form-control form-control-sm quantity-input"
                                               data-item-id="${item.id}"
                                               data-machine="${machineCode}"
                                               value="${quantityValue}"
                                               min="1"
                                               step="1"
                                               style="width: 100px; text-align: center; font-weight: 600;"
                                               title="Edit Quantity">
                                        `;
                            })()}
                        </td>
                        <td>${item.delivery_date || '-'}</td>
                        <td class="text-center process-badge ${item.process ? item.process.toLowerCase().replace(/\s/g, '-') : ''}">${item.process || '-'}</td>
                        <td>${parseInt(item.capacity || 10000).toLocaleString()}</td>
                        <td>${(() => {
                            const isWOP = item.wo_docno && item.wo_docno.toUpperCase().startsWith('WOP');
                            if (isWOP) {
                                // Hitung duration dari quantity / capacity untuk WOP
                                const machine = allMachines.find(m => m.Code === machineCode);
                                const capacityPerHour = machine ? (machine.CapacityPerHour || 10000) : 10000;
                                const durationHours = parseFloat(item.quantity || 0) / capacityPerHour;
                                return `${durationHours.toFixed(2)} jam`;
                            } else {
                                return `${Utils.calculateDuration(item.start_jam, item.end_jam)} jam`;
                            }
                        })()}</td>
                        <td class="text-center">
                            <input type="number"
                                   class="form-control form-control-sm setup-time-input"
                                   data-item-id="${item.id}"
                                   data-machine="${machineCode}"
                                   value="${item.setup || item.setup_time || 0}"
                                   min="0"
                                   step="0.25"
                                   style="width: 80px; text-align: center;"
                                   title="Waktu setup dalam jam">
                        </td>
                        <td class="text-center">
                            <input type="number"
                                   class="form-control form-control-sm break-time-input"
                                   data-item-id="${item.id}"
                                   data-machine="${machineCode}"
                                   value="${item.istirahat || item.break_time || 0}"
                                   min="0"
                                   step="0.25"
                                   style="width: 80px; text-align: center;"
                                   title="Waktu istirahat dalam jam">
                        </td>
                        <td>
                            ${(() => {
                                const isMaintenance = item.is_maintenance || item.process === 'Maintenance';
                                if (isMaintenance) {
                                    return Utils.formatDateTime(item.start_jam);
                                }
                                // Jika item sudah FINISH, tampilkan sebagai teks agar tidak bisa diedit
                                if (item.flag_status === 'FINISH') {
                                    return Utils.formatDateTime(item.start_jam);
                                }
                                // Buat input datetime-local untuk edit tanggal mulai cetak
                                if (!item.start_jam) {
                                    const now = new Date();
                                    const year = now.getFullYear();
                                    const month = String(now.getMonth() + 1).padStart(2, '0');
                                    const day = String(now.getDate()).padStart(2, '0');
                                    const hours = String(now.getHours()).padStart(2, '0');
                                    const minutes = String(now.getMinutes()).padStart(2, '0');
                                    const datetimeValue = `${year}-${month}-${day}T${hours}:${minutes}`;

                                    return `
                                        <input type="datetime-local"
                                               class="form-control form-control-sm date-input start-date-input"
                                               data-item-id="${item.id}"
                                               data-machine="${machineCode}"
                                               value="${datetimeValue}"
                                               style="width: 100%; max-width: 180px; font-size: 0.85rem;"
                                               title="Edit Tanggal & Waktu Mulai Cetak">
                                    `;
                                }

                                try {
                                    const startDate = new Date(item.start_jam);
                                    if (isNaN(startDate.getTime())) {
                                        throw new Error('Invalid date');
                                    }
                                    const year = startDate.getFullYear();
                                    const month = String(startDate.getMonth() + 1).padStart(2, '0');
                                    const day = String(startDate.getDate()).padStart(2, '0');
                                    const hours = String(startDate.getHours()).padStart(2, '0');
                                    const minutes = String(startDate.getMinutes()).padStart(2, '0');
                                    const datetimeValue = `${year}-${month}-${day}T${hours}:${minutes}`;

                                    return `
                                        <input type="datetime-local"
                                               class="form-control form-control-sm date-input start-date-input"
                                               data-item-id="${item.id}"
                                               data-machine="${machineCode}"
                                               value="${datetimeValue}"
                                               style="width: 100%; max-width: 180px; font-size: 0.85rem;"
                                               title="Edit Tanggal & Waktu Mulai Cetak">
                                    `;
                                } catch (e) {
                                    const now = new Date();
                                    const year = now.getFullYear();
                                    const month = String(now.getMonth() + 1).padStart(2, '0');
                                    const day = String(now.getDate()).padStart(2, '0');
                                    const hours = String(now.getHours()).padStart(2, '0');
                                    const minutes = String(now.getMinutes()).padStart(2, '0');
                                    const datetimeValue = `${year}-${month}-${day}T${hours}:${minutes}`;

                                    return `
                                        <input type="datetime-local"
                                               class="form-control form-control-sm date-input start-date-input"
                                               data-item-id="${item.id}"
                                               data-machine="${machineCode}"
                                               value="${datetimeValue}"
                                               style="width: 100%; max-width: 180px; font-size: 0.85rem;"
                                               title="Edit Tanggal & Waktu Mulai Cetak">
                                    `;
                                }
                            })()}
                        </td>
                        <td>${Utils.formatDateTime(item.end_jam)}</td>
                        <td class="text-center">
                            <select class="form-control form-control-sm status-select"
                                    data-item-id="${item.id}"
                                    data-machine="${machineCode}"
                                    style="width: 100px; font-size: 0.85rem; text-align: center; padding: 2px 4px;">
                                <option value="NEW" ${item.job_order_status && item.job_order_status.toUpperCase() === 'NEW' ? 'selected' : ''}>NEW</option>
                                <option value="REPEAT" ${item.job_order_status && item.job_order_status.toUpperCase() === 'REPEAT' ? 'selected' : ''}>REPEAT</option>
                            </select>
                        </td>
                        <td class="text-center">
                            ${item.ukuran_kertas || (item.panjang && item.lebar) ?
                                (item.ukuran_kertas || `${item.panjang} x ${item.lebar}`) :
                                `-`
                            }
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                ${item.flag_status === 'FINISH' ? `
                                                <span class="btn btn-success disabled">
                                                    <i class="mdi mdi-check-circle"></i> FINISH
                                                </span>
                                            ` : `
                                                <button class="btn btn-info move-item-btn" data-item-id="${item.id}" title="Pindah ke Mesin Lain">
                                                    <i class="mdi mdi-swap-horizontal"></i>
                                                </button>
                                                <button class="btn btn-warning split-item-btn" data-item-id="${item.id}" title="Bagi Quantity">
                                                    <i class="mdi mdi-call-split"></i>
                                                </button>
                                                <button class="btn btn-success merge-item-btn" data-item-id="${item.id}" title="Gabung dengan Item Lain"
                                                        style="display: ${TableRenderer.canMergeItem(item) ? 'inline-block' : 'none'};">
                                                    <i class="mdi mdi-call-merge"></i>
                                                </button>
                                                <button class="btn btn-success finish-job-btn" data-item-id="${item.id}" title="Selesai Produksi">
                                                    <i class="mdi mdi-check-circle"></i> Finish
                                                </button>
                                            `}
                            </div>
                        </td>
                    `;

                    // Click event removed - no modal on row click

                    tbody.appendChild(row);
                });

                // Load job orders for the updated items
                this.loadJobOrdersForMachine(machineCode, items);

                // Note: Priority buttons are already set up in the HTML, no need to re-setup
                console.log(`✅ Successfully reordered and updated table for machine ${machineCode}`);
            },

            showFinishJobModal(itemId) {
                const item = timelineData.find(i => i.id == itemId);
                if (!item) {
                    Utils.showToast('Item tidak ditemukan', 'danger');
                    return;
                }

                // Populate modal dengan data item
                document.getElementById('finish-item-code').textContent = item.code_item;
                document.getElementById('finish-target-qty').textContent = item.quantity;
                document.getElementById('finish-machine').textContent = item.code_machine;

                // Reset form
                document.getElementById('production-result').value = '';
                document.getElementById('quality-status').value = 'OK';
                document.getElementById('defect-qty').value = '';
                document.getElementById('defect-reason').value = '';
                document.getElementById('action-reschedule').checked = false;
                document.getElementById('action-keep').checked = false;
                document.getElementById('action-cancel').checked = false;
                document.getElementById('reschedule-date').value = '';
                document.getElementById('reschedule-machine').innerHTML = '';

                // Hide sections
                document.getElementById('defect-section').style.display = 'none';
                document.getElementById('remaining-section').style.display = 'none';
                document.getElementById('reschedule-options').style.display = 'none';

                // Populate machine options untuk reschedule
                const machineSelect = document.getElementById('reschedule-machine');
                machineSelect.innerHTML = '<option value="">-- Pilih Mesin --</option>';

                // Add mesin yang sama dulu sebagai default
                const currentMachine = allMachines.find(m => m.Code === item.code_machine);
                if (currentMachine) {
                    const defaultOption = document.createElement('option');
                    defaultOption.value = currentMachine.Code;
                    defaultOption.textContent = `${currentMachine.Code} - ${currentMachine.Description} (Mesin Saat Ini)`;
                    defaultOption.selected = true;
                    machineSelect.appendChild(defaultOption);
                }

                // Add mesin lainnya
                allMachines.forEach(machine => {
                    if (machine.Code !== item.code_machine) {
                        const option = document.createElement('option');
                        option.value = machine.Code;
                        option.textContent = `${machine.Code} - ${machine.Description}`;
                        machineSelect.appendChild(option);
                    }
                });

                // Set default date untuk reschedule (hari ini)
                const today = new Date();
                document.getElementById('reschedule-date').value = today.toISOString().split('T')[0];

                // Store item ID untuk confirmation
                document.getElementById('confirm-finish-job').dataset.itemId = itemId;

                // Set item ID for history button in modal
                const historyBtn = document.querySelector('#finishJobModal .view-history-from-modal');
                if (historyBtn) {
                    historyBtn.dataset.itemId = itemId;
                }

                // Show modal
                $('#finishJobModal').modal('show');
            },

            updateRemainingItemsSection() {
                const remainingItems = window.remainingItems || [];
                const section = document.getElementById('remaining-items-section');
                const countSpan = document.getElementById('remaining-count');
                const listContainer = document.getElementById('remaining-items-list');

                if (remainingItems.length === 0) {
                    section.style.display = 'none';
                    return;
                }

                // Show section
                section.style.display = 'block';
                countSpan.textContent = remainingItems.length;

                // Populate remaining items list
                listContainer.innerHTML = remainingItems.map((item, index) => `
                    <div class="remaining-item-card mb-3 p-3 border rounded bg-light">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <strong>${item.code_item}</strong><br>
                                <small class="text-muted">${item.material_name || 'N/A'}</small>
                            </div>
                            <div class="col-md-2 text-center">
                                <span class="badge badge-warning">${item.quantity} PCS</span>
                            </div>
                            <div class="col-md-2 text-center">
                                <span class="badge badge-info">${item.code_machine}</span>
                            </div>
                            <div class="col-md-2 text-center">
                                <small class="text-muted">${new Date(item.keep_date).toLocaleDateString('id-ID')}</small>
                            </div>
                            <div class="col-md-3 text-right">
                                <button class="btn btn-sm btn-primary pickup-item-btn" data-item-index="${index}" title="Ambil Item">
                                    <i class="mdi mdi-hand-pointing-up"></i> Ambil
                                </button>
                                <button class="btn btn-sm btn-danger remove-item-btn" data-item-index="${index}" title="Hapus Item">
                                    <i class="mdi mdi-delete"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');

                // Add event listeners untuk button pickup dan remove
                this.setupRemainingItemsEvents();
            },

            setupRemainingItemsEvents() {
                // Pickup item event
                document.querySelectorAll('.pickup-item-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const itemIndex = parseInt(this.dataset.itemIndex);
                        TableRenderer.pickupRemainingItem(itemIndex);
                    });
                });

                // Remove item event
                document.querySelectorAll('.remove-item-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const itemIndex = parseInt(this.dataset.itemIndex);
                        TableRenderer.removeRemainingItem(itemIndex);
                    });
                });
            },

            pickupRemainingItem(itemIndex) {
                const remainingItems = window.remainingItems || [];
                const item = remainingItems[itemIndex];

                if (!item) {
                    Utils.showToast('Item tidak ditemukan', 'error');
                    return;
                }

                // Tampilkan modal untuk pilih mesin dan tanggal
                this.showPickupItemModal(item, itemIndex);
            },

            showPickupItemModal(item, itemIndex) {
                // Buat modal sederhana untuk pickup item
                const modalHtml = `
                    <div class="modal fade" id="pickupItemModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5><i class="mdi mdi-hand-pointing-up"></i> Ambil Item Sisa</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-info">
                                        <strong>Item:</strong> ${item.code_item}<br>
                                        <strong>Quantity:</strong> ${item.quantity} PCS<br>
                                        <strong>Material:</strong> ${item.material_name || 'N/A'}
                                    </div>

                                    <div class="form-group">
                                        <label>Pilih Mesin:</label>
                                        <select id="pickup-machine" class="form-control">
                                            ${allMachines.map(machine =>
                                                `<option value="${machine.Code}">${machine.Code} - ${machine.Description}</option>`
                                            ).join('')}
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Tanggal Mulai:</label>
                                        <input type="date" id="pickup-date" class="form-control" value="${new Date().toISOString().split('T')[0]}">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <button type="button" class="btn btn-primary" id="confirm-pickup">Ambil Item</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Remove existing modal if any
                const existingModal = document.getElementById('pickupItemModal');
                if (existingModal) {
                    existingModal.remove();
                }

                // Add modal to DOM
                document.body.insertAdjacentHTML('beforeend', modalHtml);

                // Show modal
                $('#pickupItemModal').modal('show');

                // Add confirm event
                document.getElementById('confirm-pickup').addEventListener('click', function() {
                    const machine = document.getElementById('pickup-machine').value;
                    const date = document.getElementById('pickup-date').value;

                    if (!machine || !date) {
                        Utils.showToast('Pilih mesin dan tanggal terlebih dahulu', 'warning');
                        return;
                    }

                    // Close modal
                    $('#pickupItemModal').modal('hide');

                    // Execute pickup
                    TableRenderer.executePickupItem(item, itemIndex, machine, date);
                });
            },

            executePickupItem(item, itemIndex, machine, date) {
                try {
                    // Buat item baru untuk timeline
                    const newItem = {
                        ...item,
                        id: Date.now() + Math.random(),
                        code_machine: machine,
                        start_jam: new Date(date + 'T08:00:00').toISOString(),
                        end_jam: null,
                        flag_status: 'PLANNED',
                        keep_date: null,
                        original_item_id: null
                    };

                    // Hitung end_jam berdasarkan capacity mesin
                    const targetMachine = allMachines.find(m => m.Code === machine);
                    if (targetMachine) {
                        const capacity = targetMachine.CapacityPerHour || 10000;
                        const durationHours = item.quantity / capacity;
                        const durationMs = durationHours * 60 * 60 * 1000;
                        newItem.end_jam = new Date(newItem.start_jam).getTime() + durationMs;
                    }

                    // Tambah ke timeline
                    timelineData.push(newItem);

                    // Hapus dari remaining items
                    window.remainingItems.splice(itemIndex, 1);

                    // Update UI
                    this.renderTable();
                    this.updateRemainingItemsSection();

                    Utils.showToast(`Item ${item.code_item} berhasil diambil ke mesin ${machine}`, 'success');
                } catch (error) {
                    console.error('Error picking up item:', error);
                    Utils.showToast('Gagal mengambil item: ' + error.message, 'error');
                }
            },

            removeRemainingItem(itemIndex) {
                if (confirm('Yakin ingin menghapus item ini dari daftar sisa?')) {
                    window.remainingItems.splice(itemIndex, 1);
                    this.updateRemainingItemsSection();
                    Utils.showToast('Item berhasil dihapus', 'success');
                }
            },


        };

        // Event Handlers
        const EventHandlers = {
            init() {
                this.setupFilterEvents();
                this.setupExportEvents();
                this.setupAutoRefresh();
                this.setupMoveItemEvents();
                this.setupSplitItemEvents();
                this.setupModalEvents();
                this.setupMergeItemEvents();
                this.setupFinishJobEvents();

                this.setupSyncTimesEvents();
                this.setupSavePlanEvents();
            },

            setupFilterEvents() {
                // Apply filters button
                const applyBtn = document.getElementById('apply-filters');
                if (applyBtn) {
                    applyBtn.addEventListener('click', () => {
                        this.updateFilters();
                        TableRenderer.renderTable();
                        Utils.showToast('Filter berhasil diterapkan', 'success');
                    });
                }

                // Reset filters button
                const resetBtn = document.getElementById('reset-filters');
                if (resetBtn) {
                    resetBtn.addEventListener('click', () => {
                        this.resetFilters();
                        TableRenderer.renderTable();
                        Utils.showToast('Filter berhasil direset', 'info');
                    });
                }

                // Auto-apply filters on change (hanya untuk machine dan department, TIDAK untuk tanggal)
                // Tanggal hanya akan diterapkan saat klik "Terapkan Filter"
                const filterInputs = ['machine-filter', 'department-filter'];
                filterInputs.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.addEventListener('change', Utils.debounce(() => {
                            this.updateFilters();
                            TableRenderer.renderTable();
                        }, 500));
                    }
                });

                // Untuk tanggal, hanya update nilai di input, TIDAK langsung apply filter
                // Filter tanggal hanya akan diterapkan saat klik "Terapkan Filter"
            },

            setupExportEvents() {
                // Export buttons are handled by DataTables buttons
                // But we can add custom functionality here if needed
            },

            setupAutoRefresh() {
                // Auto-refresh data every 5 minutes
                setInterval(() => {
                    DataManager.loadData();
                }, 5 * 60 * 1000);
            },

            setupMoveItemEvents() {
                document.addEventListener('click', function(event) {
                    const moveBtn = event.target.closest('.move-item-btn');
                    if (moveBtn) {
                        const itemId = moveBtn.dataset.itemId;
                        TableRenderer.showMoveItemModal(itemId);
                    }
                });
            },

            setupSplitItemEvents() {
                document.addEventListener('click', function(event) {
                    const splitBtn = event.target.closest('.split-item-btn');
                    if (splitBtn) {
                        const itemId = splitBtn.dataset.itemId;
                        TableRenderer.showSplitItemModal(itemId);
                    }
                });
            },

            setupModalEvents() {
                // Move item confirmation
                document.getElementById('confirm-move-btn').addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    const targetMachine = document.getElementById('target-machine-select').value;
                    const moveQuantity = parseInt(document.getElementById('move-quantity').value) || 0;

                    if (!targetMachine) {
                        Utils.showToast('Pilih mesin tujuan terlebih dahulu', 'warning');
                        return;
                    }

                    if (moveQuantity <= 0) {
                        Utils.showToast('Quantity harus lebih dari 0', 'warning');
                        return;
                    }

                    // Close modal
                    $('#moveItemModal').modal('hide');

                    // Execute move
                    DataManager.moveItemToMachine(itemId, targetMachine, moveQuantity).then(success => {
                        if (success) {
                            // Re-render table to show changes
                            TableRenderer.renderTable();
                            // Sync times for both machines
                            const originalItem = timelineData.find(i => i.id == itemId);
                            if (originalItem) {
                                TableRenderer.syncMachineTimes(originalItem.code_machine);
                                TableRenderer.syncMachineTimes(targetMachine);
                            }
                        }
                    });
                });

                // Split item confirmation
                document.getElementById('confirm-split-btn').addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    const targetMachine = document.getElementById('split-target-machine').value;
                    const splitQuantity = parseInt(document.getElementById('split-quantity').value) || 0;

                    if (!targetMachine) {
                        Utils.showToast('Pilih mesin tujuan terlebih dahulu', 'warning');
                        return;
                    }

                    if (splitQuantity <= 0) {
                        Utils.showToast('Quantity harus lebih dari 0', 'warning');
                        return;
                    }

                    // Close modal
                    $('#splitItemModal').modal('hide');

                    // Execute split
                    DataManager.splitItemQuantity(itemId, targetMachine, splitQuantity).then(success => {
                        if (success) {
                            // Re-render table to show changes
                            TableRenderer.renderTable();
                            // Sync times for both machines
                            const originalItem = timelineData.find(i => i.id == itemId);
                            if (originalItem) {
                                TableRenderer.syncMachineTimes(originalItem.code_machine);
                                TableRenderer.syncMachineTimes(targetMachine);
                            }
                        }
                    });
                });

                // Finish Job confirmation
                document.getElementById('confirm-finish-job').addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    const productionResult = parseInt(document.getElementById('production-result').value) || 0;
                    const qualityStatus = document.getElementById('quality-status').value;
                    const defectQty = parseInt(document.getElementById('defect-qty').value) || 0;
                    const defectReason = document.getElementById('defect-reason').value;
                    const remainingAction = document.querySelector('input[name="remaining-action"]:checked')
                        ?.value;
                    const rescheduleDate = document.getElementById('reschedule-date').value;
                    const rescheduleMachine = document.getElementById('reschedule-machine').value;

                    if (productionResult <= 0) {
                        Utils.showToast('Hasil produksi harus lebih dari 0', 'warning');
                        return;
                    }

                    // Close modal
                    $('#finishJobModal').modal('hide');

                    // Execute finish job
                    DataManager.finishJob(itemId, {
                        productionResult,
                        qualityStatus,
                        defectQty,
                        defectReason,
                        remainingAction,
                        rescheduleDate,
                        rescheduleMachine
                    }).then(success => {
                        if (success) {
                            // Re-render table to show changes
                            TableRenderer.renderTable();
                            // Update remaining items section
                            TableRenderer.updateRemainingItemsSection();
                        }
                    });
                });

                // Production result input handler
                document.getElementById('production-result').addEventListener('input', function() {
                    const targetQty = parseInt(document.getElementById('finish-target-qty').textContent);
                    const resultQty = parseInt(this.value) || 0;
                    const remainingQty = targetQty - resultQty;

                    if (remainingQty > 0) {
                        // Tampilkan section sisa
                        document.getElementById('remaining-section').style.display = 'block';
                        document.getElementById('remaining-qty').textContent = remainingQty;

                        // Enable radio buttons
                        document.querySelectorAll('input[name="remaining-action"]').forEach(radio => {
                            radio.disabled = false;
                        });
                    } else {
                        // Sembunyikan section sisa
                        document.getElementById('remaining-section').style.display = 'none';
                    }
                });

                // Quality status change handler
                document.getElementById('quality-status').addEventListener('change', function() {
                    const defectSection = document.getElementById('defect-section');
                    if (this.value === 'NG') {
                        defectSection.style.display = 'block';
                    } else {
                        defectSection.style.display = 'none';
                    }
                });

                // Remaining action radio button handler
                document.querySelectorAll('input[name="remaining-action"]').forEach(radio => {
                    radio.addEventListener('change', function() {
                        const rescheduleOptions = document.getElementById('reschedule-options');
                        if (this.value === 'reschedule') {
                            rescheduleOptions.style.display = 'block';
                        } else {
                            rescheduleOptions.style.display = 'none';
                        }
                    });
                });
            },

            setupMergeItemEvents() {
                document.addEventListener('click', function(event) {
                    const mergeBtn = event.target.closest('.merge-item-btn');
                    if (mergeBtn) {
                        const itemId = mergeBtn.dataset.itemId;
                        TableRenderer.showMergeItemModal(itemId);
                    }
                });
            },

            setupFinishJobEvents() {
                document.addEventListener('click', function(event) {
                    const finishBtn = event.target.closest('.finish-job-btn');
                    if (finishBtn) {
                        const itemId = finishBtn.dataset.itemId;
                        TableRenderer.showFinishJobModal(itemId);
                    }
                });
            },

            updateFilters() {
                currentFilters.dateFrom = document.getElementById('date-from').value;
                currentFilters.dateTo = document.getElementById('date-to').value;
                currentFilters.machine = document.getElementById('machine-filter').value;
                currentFilters.department = document.getElementById('department-filter').value;

                console.log('Filters updated:', currentFilters);
            },

            resetFilters() {
                const today = new Date();
                const nextMonth = new Date(today);
                nextMonth.setMonth(today.getMonth() + 1); // 1 bulan ke depan

                // Set nilai di input (tampil di form)
                document.getElementById('date-from').value = today.toISOString().split('T')[0];
                document.getElementById('date-to').value = nextMonth.toISOString().split('T')[0];
                document.getElementById('machine-filter').value = '';
                document.getElementById('department-filter').value = '';

                // Reset currentFilters - kosongkan filter tanggal agar semua data ditampilkan
                // User harus klik "Terapkan Filter" untuk menerapkan filter tanggal
                currentFilters.dateFrom = '';
                currentFilters.dateTo = '';
                currentFilters.machine = '';
                currentFilters.department = '';

                console.log('Filters reset:', currentFilters);
            }
        };

        // Merge item confirmation
        document.getElementById('confirm-merge-btn').addEventListener('click', function() {
            const sourceItemId = this.dataset.sourceItemId;
            const selectedTarget = document.querySelector('input[name="merge-target"]:checked');

            if (!selectedTarget) {
                Utils.showToast('Pilih item tujuan terlebih dahulu', 'warning');
                return;
            }

            const targetItemId = selectedTarget.value;

            // Close modal
            $('#mergeItemModal').modal('hide');

            // Execute merge
            DataManager.mergeItemsBack(sourceItemId, targetItemId).then(success => {
                if (success) {
                    TableRenderer.renderTable();
                    // Sync times for both machines after merge
                    const sourceItem = timelineData.find(i => i.id == sourceItemId);
                    const targetItem = timelineData.find(i => i.id == targetItemId);
                    if (sourceItem && targetItem) {
                        TableRenderer.syncMachineTimes(sourceItem.code_machine);
                        TableRenderer.syncMachineTimes(targetItem.code_machine);
                    }
                }
            });
        });

        // Global Save Plan Update Function
        async function savePlanUpdate() {
            console.log('🚀 Starting save plan update process...');

            try {
                // Check if button exists
                const saveBtn = document.getElementById('save-plan-update');
                console.log('🔍 Save button found:', !!saveBtn);

                if (!saveBtn) {
                    throw new Error('Save button not found in DOM');
                }

                // PREVENTIVE VALIDATION - Check for conflicts before save
                const validation = ValidationManager.validateBeforeSave();

                if (validation.errors.length > 0) {
                    // Block save if there are errors
                    let errorMessage = '❌ Tidak dapat menyimpan karena terdapat konflik:\n\n';
                    validation.errors.forEach((error, index) => {
                        errorMessage += `${index + 1}. ${error.message}\n`;
                    });

                    Utils.showToast(errorMessage, 'danger');
                    NotificationManager.add('Terdapat konflik yang harus diselesaikan sebelum save', 'error', true);

                    // Show warnings too
                    if (validation.warnings.length > 0) {
                        validation.warnings.forEach(warning => {
                            NotificationManager.add(warning.message, 'warning', true);
                        });
                    }

                    return false; // Block save
                }

                // Show warnings but allow save
                if (validation.warnings.length > 0) {
                    const shouldContinue = confirm(
                        `⚠️ Peringatan:\n\n${validation.warnings.map(w => w.message).join('\n')}\n\nLanjutkan menyimpan?`
                    );
                    if (!shouldContinue) {
                        return false;
                    }

                    validation.warnings.forEach(warning => {
                        NotificationManager.add(warning.message, 'warning', true);
                    });
                }

                // Prepare data for saving
                const planData = preparePlanDataForSave();
                console.log('📊 Plan data prepared:', planData);

                // Check CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                console.log('🔑 CSRF token found:', !!csrfToken);

                if (!csrfToken) {
                    throw new Error('CSRF token not found');
                }

                // Show loading state
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Saving...';

                // Prepare request URL
                const url = '{{ route('process.save-plan-update') }}';
                console.log('🌐 Request URL:', url);

                // Send to controller
                console.log('📤 Sending request...');
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(planData)
                });

                console.log('📥 Response received:', response);
                console.log('📊 Response status:', response.status);
                console.log('📋 Response headers:', response.headers);

                if (response.ok) {
                const result = await response.json();
                console.log('📬 Server response:', result);

                    if (result.success) {
                        Utils.showToast(result.message || '✅ Plan update berhasil disimpan ke database', 'success');
                        ChangeTracker.markSaved(); // Mark as saved

                        // Reload data to get new IDs for created items
                        if (result.total_created > 0) {
                            console.log('🔄 Reloading data after creating new items...');
                            setTimeout(() => {
                                DataManager.loadData().then(() => {
                                    console.log('✅ Data reloaded after create');
                                });
                            }, 500);
                        }
                } else {
                        Utils.showToast('❌ Gagal menyimpan plan update: ' + (result.message || 'Unknown error'), 'danger');
                    }
                } else {
                    const result = await response.json();
                    Utils.showToast('❌ Gagal menyimpan plan update: ' + (result.message || 'Unknown error'), 'danger');
                }

            } catch (error) {
                console.error('❌ Error saving plan update:', error);
                Utils.showToast('❌ Error: ' + error.message, 'danger');
            } finally {
                // Reset button state
                const saveBtn = document.getElementById('save-plan-update');
                if (saveBtn) {
                    saveBtn.disabled = false;
                    ChangeTracker.updateIndicator(); // Update to show current state
                }
            }
        }

        // Prepare plan data for saving
        function preparePlanDataForSave() {
            const planUpdates = [];
            const planCreates = [];

            // Get initial plan IDs from database (loaded at page load)
            const initialPlanIds = new Set();
            if (window.initialPlanIds) {
                window.initialPlanIds.forEach(id => initialPlanIds.add(parseInt(id)));
            }

            // Group by machine for organized data
            const machineGroups = {};
            const createGroups = {};

            timelineData.forEach(item => {
                // Convert ID to integer - handle both string and number IDs
                let itemId = null;
                let isNewItem = false;

                if (item.id !== null && item.id !== undefined) {
                    itemId = parseInt(item.id);
                    if (isNaN(itemId)) {
                        console.warn('⚠️ Invalid ID format for item:', item.code_item, 'ID:', item.id);
                        return;
                    }
                    // Check if this ID exists in database (from initial load)
                    if (!initialPlanIds.has(itemId) || item.is_manual === true) {
                        isNewItem = true;
                    }
                } else {
                    // No ID = new item
                    isNewItem = true;
                }

                const itemData = {
                    code_item: item.code_item,
                    material_name: item.material_name,
                    code_machine: item.code_machine,
                    quantity: item.quantity,
                    start_jam: item.start_jam,
                    end_jam: item.end_jam,
                    delivery_date: item.delivery_date,
                    process: item.process || 'CTK',
                    flag_status: item.flag_status || 'PLANNED',
                    wo_docno: item.wo_docno || null,
                    so_docno: item.so_docno || null,
                    up_cetak: item.up_cetak || 1,
                    setup_time: item.setup_time || 0,
                    break_time: item.break_time || 0,
                    // Add new fields for status, ukuran kertas, and job order
                    status_item: item.job_order_status || item.status_item || 'NEW',
                    keterangan_item: item.ukuran_kertas || item.keterangan_item || null,
                    job_order: item.job_order || item.job_order_no || null,
                    // Add calculated fields
                    duration_hours: Utils.calculateDuration(item.start_jam, item.end_jam)
                };

                if (isNewItem) {
                    // New item - add to plan_creates
                    if (!createGroups[item.code_machine]) {
                        createGroups[item.code_machine] = [];
                    }
                    createGroups[item.code_machine].push(itemData);
                } else {
                    // Existing item - add to plan_updates
                    if (!machineGroups[item.code_machine]) {
                        machineGroups[item.code_machine] = [];
                    }
                    machineGroups[item.code_machine].push({
                        ...itemData,
                        id: itemId // Integer ID for updates
                    });
                }
            });

            // Convert to flat arrays with priority info
            Object.keys(machineGroups).forEach(machineCode => {
                machineGroups[machineCode].forEach((item, index) => {
                    item.priority_order = index + 1;
                    planUpdates.push(item);
                });
            });

            Object.keys(createGroups).forEach(machineCode => {
                createGroups[machineCode].forEach((item, index) => {
                    item.priority_order = index + 1;
                    planCreates.push(item);
                });
            });

            // Include machine shift configurations
            const machineShiftConfigs = {};
            Object.keys(machineShiftConfig).forEach(machineCode => {
                machineShiftConfigs[machineCode] = machineShiftConfig[machineCode];
            });

            return {
                plan_updates: planUpdates,
                plan_creates: planCreates,
                machine_shift_configs: machineShiftConfigs,
                total_updates: planUpdates.length,
                total_creates: planCreates.length,
                total_items: planUpdates.length + planCreates.length,
                machines_affected: [...new Set([...Object.keys(machineGroups), ...Object.keys(createGroups)])],
                timestamp: new Date().toISOString(),
                user_action: 'manual_plan_update'
            };
        }

        // Add click event listener for save button
        document.addEventListener('DOMContentLoaded', function() {
            const saveBtn = document.getElementById('save-plan-update');
            if (saveBtn) {
                saveBtn.addEventListener('click', function() {
                    console.log('💾 Save Plan Update clicked from global listener');
                    savePlanUpdate();
                });
            } else {
                console.log('❌ Save button not found in DOM');
            }

            // Event listener untuk input SETUP dan ISTIRAHAT
            setupTimeInputListeners();
        });

        // Setup event listeners untuk input SETUP dan ISTIRAHAT
        function setupTimeInputListeners() {
            // Event delegation untuk input yang dibuat dinamis
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('setup-time-input') || e.target.classList.contains('break-time-input')) {
                    console.log('⏰ Time input changed:', e.target.className, e.target.value);
                    recalculateMachineTimeline(e.target);
                }
            });

            // Event listener untuk input real-time
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('setup-time-input') || e.target.classList.contains('break-time-input')) {
                    // Debounce untuk menghindari terlalu banyak perhitungan
                    clearTimeout(window.timeInputTimeout);
                    window.timeInputTimeout = setTimeout(() => {
                        recalculateMachineTimeline(e.target);
                    }, 500);
                }
            });

            // Event listener untuk quantity input (semua item)
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('quantity-input')) {
                    handleQuantityChange(e.target);
                }
            });

            // Event listener untuk status select dropdown
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('status-select')) {
                    const itemId = e.target.dataset.itemId;
                    const machineCode = e.target.dataset.machine;
                    const newStatus = e.target.value;

                    console.log('📋 Status changed:', {
                        itemId: itemId,
                        machineCode: machineCode,
                        newStatus: newStatus
                    });

                    // Update item data in timelineData
                    const item = timelineData.find(i => i.id == itemId);
                    if (item) {
                        item.job_order_status = newStatus;
                        item.status_item = newStatus; // Also update status_item for database
                        console.log('✅ Updated item status in timelineData:', item);
                    }

                    // Mark as changed
                    hasUnsavedChanges = true;
                    ChangeTracker.markChanged();
                }
            });

            // Event listener untuk confirm send job order button
            document.addEventListener('click', function(e) {
                if (e.target.id === 'confirm-send-job-order' || e.target.closest('#confirm-send-job-order')) {
                    const modal = document.getElementById('sendJobOrderModal');
                    if (modal && modal.dataset.machineCode) {
                        const machineCode = modal.dataset.machineCode;
                        console.log('📤 Confirm send Job Order for machine:', machineCode);
                        TableRenderer.sendJobOrderToDatabase(machineCode);
                    }
                }

                // Event listener untuk refresh monitoring button
                if (e.target.id === 'refresh-monitoring-btn' || e.target.closest('#refresh-monitoring-btn')) {
                    console.log('🔄 Refreshing monitoring data...');
                    TableRenderer.loadMonitoringData();
                }
            });

            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('quantity-input')) {
                    // Debounce untuk menghindari terlalu banyak update
                    clearTimeout(window.quantityInputTimeout);
                    window.quantityInputTimeout = setTimeout(() => {
                        // Validate input
                        const value = parseInt(e.target.value) || 0;
                        if (value < 1) {
                            e.target.value = 1;
                        }
                    }, 300);
                }
            });

            // Event listener untuk date input (start date)
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('start-date-input')) {
                    handleStartDateChange(e.target);
                }
            });
        }

        // Fungsi untuk menghitung ulang timeline mesin berdasarkan perubahan SETUP/ISTIRAHAT
        function recalculateMachineTimeline(changedInput) {
            const itemId = changedInput.dataset.itemId;
            const machineCode = changedInput.dataset.machine;

            console.log('🔄 Recalculating timeline for machine:', machineCode, 'item:', itemId);

            // Cari semua item dalam mesin yang sama
            const machineTable = document.getElementById(`machine-table-${machineCode}`);
            if (!machineTable) {
                console.warn('❌ Machine table not found:', machineCode);
                return;
            }

            const rows = machineTable.querySelectorAll('tbody tr[data-item-id]');
            const items = [];

            // Kumpulkan data dari semua baris dalam mesin
            rows.forEach((row, index) => {
                const itemId = row.getAttribute('data-item-id');
                const setupInput = row.querySelector('.setup-time-input');
                const breakInput = row.querySelector('.break-time-input');
                const startTimeCell = row.querySelector('td:nth-child(13)'); // MULAI CETAK
                const endTimeCell = row.querySelector('td:nth-child(14)'); // AKHIR CETAK
                const durationCell = row.querySelector('td:nth-child(10)'); // LAMA CETAK

                if (itemId && setupInput && breakInput) {
                    items.push({
                        id: itemId,
                        index: index,
                        row: row,
                        setupTime: parseFloat(setupInput.value) || 0,
                        breakTime: parseFloat(breakInput.value) || 0,
                        startTimeCell: startTimeCell,
                        endTimeCell: endTimeCell,
                        durationCell: durationCell
                    });
                }
            });

            // Urutkan berdasarkan index (priority)
            items.sort((a, b) => a.index - b.index);

            // Gunakan waktu dari item pertama yang sudah ada
            let currentTime = null;
            if (items.length > 0) {
                // Coba ambil waktu dari timeline data dulu
                const firstItem = timelineData.find(t => t.id == items[0].id);
                if (firstItem && firstItem.start_jam) {
                    currentTime = new Date(firstItem.start_jam);
                    console.log('🕐 Using existing start time from timeline data:', currentTime);
                } else {
                    // Jika tidak ada, gunakan waktu dari tampilan
                    const firstStartTimeText = items[0].startTimeCell.textContent;
                    if (firstStartTimeText && firstStartTimeText !== '-') {
                        // Parse dari format "15/09/25 (18:32)"
                        const timeMatch = firstStartTimeText.match(/(\d{2})\/(\d{2})\/(\d{2}) \((\d{2}):(\d{2})\)/);
                        if (timeMatch) {
                            const [, day, month, year, hour, minute] = timeMatch;
                            const fullYear = 2000 + parseInt(year);
                            currentTime = new Date(fullYear, parseInt(month) - 1, parseInt(day), parseInt(hour), parseInt(minute));
                            console.log('🕐 Parsed start time from display:', currentTime);
                        } else {
                            currentTime = new Date();
                            console.log('🕐 Using current time as fallback');
                        }
                    } else {
                        currentTime = new Date();
                        console.log('🕐 Using current time as fallback');
                    }
                }
            } else {
                currentTime = new Date();
                console.log('🕐 Using current time as fallback');
            }

            // Hitung ulang waktu untuk setiap item
            items.forEach((item, index) => {
                // Waktu mulai item ini
                const startTime = new Date(currentTime);

                // Durasi cetak (dari kolom LAMA CETAK)
                const duration = parseFloat(item.durationCell.textContent.replace(' jam', '')) || 0;

                // Total durasi = setup + durasi cetak + istirahat
                const totalDuration = item.setupTime + duration + item.breakTime;

                // Waktu selesai = waktu mulai + total durasi
                const endTime = new Date(startTime.getTime() + (totalDuration * 60 * 60 * 1000));

                // Update timeline data
                const timelineItem = timelineData.find(t => t.id == item.id);
                if (timelineItem) {
                    timelineItem.start_jam = startTime.toISOString();
                    timelineItem.end_jam = endTime.toISOString();
                    timelineItem.setup_time = item.setupTime;
                    timelineItem.break_time = item.breakTime;
                }

                // Update tampilan
                // Pastikan kolom MULAI CETAK tetap berupa input datetime-local agar bisa diedit lagi
                (function renderStartDateInput() {
                    const yyyy = startTime.getFullYear();
                    const mm = String(startTime.getMonth() + 1).padStart(2, '0');
                    const dd = String(startTime.getDate()).padStart(2, '0');
                    const hh = String(startTime.getHours()).padStart(2, '0');
                    const min = String(startTime.getMinutes()).padStart(2, '0');
                    const datetimeValue = `${yyyy}-${mm}-${dd}T${hh}:${min}`;
                    item.startTimeCell.innerHTML = `
                        <input type="datetime-local"
                               class="form-control form-control-sm date-input start-date-input"
                               data-item-id="${item.id}"
                               data-machine="${machineCode}"
                               value="${datetimeValue}"
                               style="width: 100%; max-width: 180px; font-size: 0.85rem;"
                               title="Edit Tanggal & Waktu Mulai Cetak">
                    `;
                })();
                item.endTimeCell.textContent = Utils.formatDateTime(endTime.toISOString());

                // Waktu untuk item berikutnya = waktu selesai item ini
                currentTime = new Date(endTime);

                console.log(`📅 Item ${index + 1}: ${Utils.formatDateTime(startTime.toISOString())} - ${Utils.formatDateTime(endTime.toISOString())} (Setup: ${item.setupTime}h, Duration: ${duration}h, Break: ${item.breakTime}h, Total: ${totalDuration}h)`);
            });

            // Update timeline data di global variable
            updateTimelineData(items);

            // Mark as changed
            const changedItem = timelineData.find(t => t.id == itemId);
            if (changedItem) {
                ChangeTracker.markChanged(`Update Setup/Break Time: ${changedItem.code_item}`);
            }

            // Check for conflicts after recalculation
            const conflicts = ValidationManager.detectConflicts(machineCode);
            if (conflicts.length > 0) {
                NotificationManager.add(
                    `⚠️ Terdeteksi ${conflicts.length} konflik waktu di mesin ${machineCode} setelah perubahan setup/break time`,
                    'warning',
                    true
                );
            }
        }

        // Fungsi untuk menangani perubahan quantity (semua item)
        function handleQuantityChange(input) {
            const itemId = input.dataset.itemId;
            const machineCode = input.dataset.machine;
            const newQuantity = parseInt(input.value) || 1;

            console.log('📦 Quantity changed for item:', itemId, 'new quantity:', newQuantity);

            // Validasi minimum quantity
            if (newQuantity < 1) {
                input.value = 1;
                Utils.showToast('Quantity minimal adalah 1', 'warning');
                return;
            }

            // Cari item di timelineData
            const item = timelineData.find(i => i.id == itemId);
            if (!item) {
                console.error('❌ Item not found:', itemId);
                Utils.showToast('Item tidak ditemukan', 'error');
                return;
            }

            // Validasi untuk maintenance item
            if (item.is_maintenance || item.process === 'Maintenance') {
                console.warn('⚠️ Cannot edit quantity for maintenance item');
                input.value = item.quantity || 0;
                Utils.showToast('Tidak bisa mengubah quantity untuk maintenance item', 'warning');
                return;
            }

            // Validasi untuk item yang sudah finish
            if (item.flag_status === 'FINISH') {
                console.warn('⚠️ Cannot edit quantity for finished item');
                input.value = item.quantity || 0;
                Utils.showToast('Tidak bisa mengubah quantity untuk item yang sudah selesai', 'warning');
                return;
            }

            const oldQuantity = parseInt(item.quantity) || 0;

            // Update quantity di timelineData
            item.quantity = newQuantity;

            // Hitung ulang lama cetak berdasarkan quantity baru untuk semua item
            // Menggunakan quantity / capacity untuk sinkronisasi yang akurat
            const machine = allMachines.find(m => m.Code === machineCode);
            const capacityPerHour = parseFloat(machine ? (machine.CapacityPerHour || 10000) : 10000);
            const newDurationHours = parseFloat(newQuantity) / capacityPerHour;

            console.log(`⏱️ Recalculating duration: ${newQuantity} / ${capacityPerHour} = ${newDurationHours.toFixed(2)} jam`);

            // Update end_jam berdasarkan start_jam + duration baru + setup + break
            if (item.start_jam) {
                const startTime = new Date(item.start_jam);
                const setupTime = parseFloat(item.setup_time || 0);
                const breakTime = parseFloat(item.break_time || 0);

                // Total duration = setup + duration cetak + break
                const totalDurationHours = setupTime + newDurationHours + breakTime;
                const totalDurationMs = totalDurationHours * 60 * 60 * 1000;

                const newEndTime = new Date(startTime.getTime() + totalDurationMs);
                item.end_jam = newEndTime.toISOString();

                console.log(`📅 Updated end time: ${Utils.formatDateTime(item.start_jam)} → ${Utils.formatDateTime(item.end_jam)}`);
            }

            // Recalculate timeline untuk semua item setelah item ini
            recalculateMachineTimelineAfterQuantityChange(machineCode, itemId);

            // Mark as changed
            ChangeTracker.markChanged(`Update Quantity: ${item.code_item} - ${oldQuantity} → ${newQuantity}`);

            // Show notification
            Utils.showToast(
                `✅ Quantity ${item.code_item}${item.wo_docno ? ' (' + item.wo_docno + ')' : ''} diupdate: ${oldQuantity.toLocaleString()} → ${newQuantity.toLocaleString()}. Lama cetak: ${newDurationHours.toFixed(2)} jam`,
                'success'
            );

            // Update tampilan tabel
            setTimeout(() => {
                TableRenderer.renderTable();
            }, 100);

            console.log('✅ Quantity and duration updated successfully');
        }

        // Fungsi untuk recalculate timeline setelah quantity change
        function recalculateMachineTimelineAfterQuantityChange(machineCode, changedItemId) {
            console.log('🔄 Recalculating timeline after quantity change for machine:', machineCode, 'item:', changedItemId);

            // Cari semua item dalam mesin yang sama, diurutkan berdasarkan priority
            const machineItems = timelineData
                .filter(item => item.code_machine === machineCode)
                .sort((a, b) => {
                    // Urutkan berdasarkan index di timelineData untuk maintain priority order
                    const aIndex = timelineData.findIndex(item => item.id === a.id);
                    const bIndex = timelineData.findIndex(item => item.id === b.id);
                    return aIndex - bIndex;
                });

            // Cari index item yang diubah
            const changedItemIndex = machineItems.findIndex(item => item.id == changedItemId);
            if (changedItemIndex === -1) {
                console.warn('❌ Changed item not found in machine items');
                return;
            }

            // Gunakan start time dari item pertama sebagai base
            let currentTime = null;
            if (machineItems.length > 0 && machineItems[0].start_jam) {
                currentTime = new Date(machineItems[0].start_jam);
            } else {
                currentTime = new Date();
                currentTime.setHours(8, 0, 0, 0);
            }

            // Recalculate semua item mulai dari item pertama
            const machine = allMachines.find(m => m.Code === machineCode);
            const capacityPerHour = parseFloat(machine ? (machine.CapacityPerHour || 10000) : 10000);

            machineItems.forEach((item, index) => {
                // Waktu mulai item ini
                const startTime = new Date(currentTime);

                // Hitung duration berdasarkan quantity dan capacity untuk semua item
                // Ini memastikan sinkronisasi yang akurat ketika quantity diubah
                durationHours = parseFloat(item.quantity || 0) / parseFloat(capacityPerHour || 10000);

                // Pastikan durationHours adalah number yang valid
                if (isNaN(durationHours) || !isFinite(durationHours)) {
                    console.warn(`⚠️ Invalid duration for item ${item.code_item}, using default 0`);
                    durationHours = 0;
                }

                // Setup dan break time
                const setupTime = parseFloat(item.setup_time || 0) || 0;
                const breakTime = parseFloat(item.break_time || 0) || 0;

                // Total durasi = setup + durasi cetak + istirahat
                const totalDurationHours = setupTime + durationHours + breakTime;
                const totalDurationMs = totalDurationHours * 60 * 60 * 1000;

                // Waktu selesai = waktu mulai + total durasi
                const endTime = new Date(startTime.getTime() + totalDurationMs);

                // Update timeline data
                item.start_jam = startTime.toISOString();
                item.end_jam = endTime.toISOString();

                // Waktu untuk item berikutnya = waktu selesai item ini
                currentTime = new Date(endTime);

                console.log(`📅 Item ${index + 1}/${machineItems.length}: ${item.code_item} - ${Utils.formatDateTime(startTime.toISOString())} → ${Utils.formatDateTime(endTime.toISOString())} (Qty: ${item.quantity}, Duration: ${durationHours.toFixed(2)}h, Total: ${totalDurationHours.toFixed(2)}h)`);
            });

            // Check for conflicts after recalculation
            const conflicts = ValidationManager.detectConflicts(machineCode);
            if (conflicts.length > 0) {
                NotificationManager.add(
                    `⚠️ Terdeteksi ${conflicts.length} konflik waktu di mesin ${machineCode} setelah perubahan quantity`,
                    'warning',
                    true
                );
            }

            console.log('✅ Timeline recalculated after quantity change');
        }

        // Fungsi untuk menangani perubahan tanggal mulai cetak
        function handleStartDateChange(input) {
            const itemId = input.dataset.itemId;
            const machineCode = input.dataset.machine;
            const newDateTime = input.value; // Format: YYYY-MM-DDTHH:mm

            console.log('📅 Start date changed for item:', itemId, 'new date:', newDateTime);

            if (!newDateTime) {
                Utils.showToast('Tanggal tidak boleh kosong', 'warning');
                return;
            }

            // Cari item di timelineData
            const item = timelineData.find(i => i.id == itemId);
            if (!item) {
                console.error('❌ Item not found:', itemId);
                Utils.showToast('Item tidak ditemukan', 'error');
                return;
            }

            // Validasi untuk maintenance item
            if (item.is_maintenance || item.process === 'Maintenance') {
                console.warn('⚠️ Cannot edit date for maintenance item');
                input.value = item.start_jam ? new Date(item.start_jam).toISOString().slice(0, 16) : '';
                return;
            }

            // Validasi untuk item yang sudah finish
            if (item.flag_status === 'FINISH') {
                console.warn('⚠️ Cannot edit date for finished item');
                input.value = item.start_jam ? new Date(item.start_jam).toISOString().slice(0, 16) : '';
                Utils.showToast('Tidak bisa mengubah tanggal untuk item yang sudah selesai', 'warning');
                return;
            }

            const oldStartDate = item.start_jam ? new Date(item.start_jam) : null;
            const newStartDate = new Date(newDateTime);

            // Update start_jam di timelineData
            item.start_jam = newStartDate.toISOString();

            // Hitung ulang end_jam berdasarkan duration
            const machine = allMachines.find(m => m.Code === machineCode);
            const capacityPerHour = parseFloat(machine ? (machine.CapacityPerHour || 10000) : 10000);

            // Hitung duration berdasarkan quantity dan capacity untuk semua item
            // Menggunakan quantity / capacity untuk sinkronisasi yang akurat
            let durationHours = parseFloat(item.quantity || 0) / capacityPerHour;

            // Pastikan durationHours adalah number yang valid
            if (isNaN(durationHours) || !isFinite(durationHours)) {
                durationHours = 0;
            }

            // Setup dan break time
            const setupTime = parseFloat(item.setup_time || 0) || 0;
            const breakTime = parseFloat(item.break_time || 0) || 0;

            // Total durasi = setup + durasi cetak + istirahat
            const totalDurationHours = setupTime + durationHours + breakTime;
            const totalDurationMs = totalDurationHours * 60 * 60 * 1000;

            // Update end_jam
            const newEndDate = new Date(newStartDate.getTime() + totalDurationMs);
            item.end_jam = newEndDate.toISOString();

            console.log(`📅 Updated start time: ${Utils.formatDateTime(oldStartDate ? oldStartDate.toISOString() : '')} → ${Utils.formatDateTime(newStartDate.toISOString())}`);
            console.log(`📅 Updated end time: ${Utils.formatDateTime(newEndDate.toISOString())}`);

            // Recalculate timeline untuk semua item setelah item ini
            recalculateMachineTimelineAfterDateChange(machineCode, itemId);

            // Mark as changed
            ChangeTracker.markChanged(`Update Start Date: ${item.code_item} - ${oldStartDate ? Utils.formatDateTime(oldStartDate.toISOString()) : 'N/A'} → ${Utils.formatDateTime(newStartDate.toISOString())}`);

            // Show notification
            Utils.showToast(
                `✅ Tanggal mulai cetak ${item.code_item} diupdate: ${Utils.formatDateTime(oldStartDate ? oldStartDate.toISOString() : '')} → ${Utils.formatDateTime(newStartDate.toISOString())}`,
                'success'
            );

            // Update tampilan tabel
            setTimeout(() => {
                TableRenderer.renderTable();
            }, 100);

            console.log('✅ Start date updated successfully');
        }

        // Fungsi untuk recalculate timeline setelah date change
        function recalculateMachineTimelineAfterDateChange(machineCode, changedItemId) {
            console.log('🔄 Recalculating timeline after date change for machine:', machineCode, 'item:', changedItemId);

            // Cari semua item dalam mesin yang sama, diurutkan berdasarkan priority
            const machineItems = timelineData
                .filter(item => item.code_machine === machineCode)
                .sort((a, b) => {
                    // Urutkan berdasarkan index di timelineData untuk maintain priority order
                    const aIndex = timelineData.findIndex(item => item.id === a.id);
                    const bIndex = timelineData.findIndex(item => item.id === b.id);
                    return aIndex - bIndex;
                });

            // Cari index item yang diubah
            const changedItemIndex = machineItems.findIndex(item => item.id == changedItemId);
            if (changedItemIndex === -1) {
                console.warn('❌ Changed item not found in machine items');
                return;
            }

            // Gunakan start time dari item pertama sebagai base
            let currentTime = null;
            if (machineItems.length > 0 && machineItems[0].start_jam) {
                currentTime = new Date(machineItems[0].start_jam);
            } else {
                currentTime = new Date();
                currentTime.setHours(8, 0, 0, 0);
            }

            // Recalculate semua item mulai dari item pertama
            const machine = allMachines.find(m => m.Code === machineCode);
            const capacityPerHour = parseFloat(machine ? (machine.CapacityPerHour || 10000) : 10000);

            machineItems.forEach((item, index) => {
                // Waktu mulai item ini
                const startTime = new Date(currentTime);

                // Hitung duration berdasarkan quantity dan capacity
                const isWOP = item.wo_docno && item.wo_docno.toUpperCase().startsWith('WOP');
                let durationHours;

                if (isWOP) {
                    // Untuk WOP, duration = quantity / capacity
                    durationHours = parseFloat(item.quantity || 0) / capacityPerHour;
                } else {
                    // Untuk WOT, gunakan duration yang sudah ada atau hitung dari quantity
                    if (item.start_jam && item.end_jam) {
                        const calculatedDuration = Utils.calculateDuration(item.start_jam, item.end_jam);
                        // Pastikan hasilnya adalah number
                        durationHours = parseFloat(calculatedDuration) || 0;
                    } else {
                        durationHours = parseFloat(item.quantity || 0) / capacityPerHour;
                    }
                }

                // Pastikan durationHours adalah number yang valid
                if (isNaN(durationHours) || !isFinite(durationHours)) {
                    console.warn(`⚠️ Invalid duration for item ${item.code_item}, using default 0`);
                    durationHours = 0;
                }

                // Setup dan break time
                const setupTime = parseFloat(item.setup_time || 0) || 0;
                const breakTime = parseFloat(item.break_time || 0) || 0;

                // Total durasi = setup + durasi cetak + istirahat
                const totalDurationHours = setupTime + durationHours + breakTime;
                const totalDurationMs = totalDurationHours * 60 * 60 * 1000;

                // Waktu selesai = waktu mulai + total durasi
                const endTime = new Date(startTime.getTime() + totalDurationMs);

                // Update timeline data
                item.start_jam = startTime.toISOString();
                item.end_jam = endTime.toISOString();

                // Waktu untuk item berikutnya = waktu selesai item ini
                currentTime = new Date(endTime);

                console.log(`📅 Item ${index + 1}/${machineItems.length}: ${item.code_item} - ${Utils.formatDateTime(startTime.toISOString())} → ${Utils.formatDateTime(endTime.toISOString())} (Qty: ${item.quantity}, Duration: ${durationHours.toFixed(2)}h, Total: ${totalDurationHours.toFixed(2)}h)`);
            });

            // Check for conflicts after recalculation
            const conflicts = ValidationManager.detectConflicts(machineCode);
            if (conflicts.length > 0) {
                NotificationManager.add(
                    `⚠️ Terdeteksi ${conflicts.length} konflik waktu di mesin ${machineCode} setelah perubahan tanggal`,
                    'warning',
                    true
                );
            }

            console.log('✅ Timeline recalculated after date change');
        }

        // Update timeline data global variable
        function updateTimelineData(items) {
            items.forEach(item => {
                const timelineItem = timelineData.find(t => t.id == item.id);
                if (timelineItem) {
                    timelineItem.setup_time = item.setupTime;
                    timelineItem.break_time = item.breakTime;
                }
            });
        }

        // Global variable untuk tracking item yang sedang diedit
        let currentEditingItemId = null;
        let previewLoadTimeout = null;

        // Setup Modal DateTime Listeners
        function setupModalDateTimeListeners(item) {
            // Set current editing item ID
            currentEditingItemId = item.id;

            const startInput = document.getElementById('edit-start-time');
            const endInput = document.getElementById('edit-end-time');
            const durationCell = document.getElementById('modal-duration');

            if (startInput && endInput) {
                console.log('🔧 Start input found:', startInput);
                // Update duration when start time changes
                startInput.addEventListener('change', function() {
                    const newStartTime = new Date(this.value);
                    const endTime = endInput.value ? new Date(endInput.value) : new Date(item.end_jam);

                    // Update duration display
                    const duration = Utils.calculateDuration(newStartTime.toISOString(), endTime.toISOString());
                    durationCell.textContent = duration + ' jam';

                    console.log('📅 Start time changed:', newStartTime);

                    // Load plan preview for selected date and machine
                    loadPlanPreview(newStartTime, item.code_machine);
                });

                // Also trigger on input event for real-time updates with debounce
                startInput.addEventListener('input', function() {
                    if (this.value) {
                        // Clear previous timeout
                        if (previewLoadTimeout) {
                            clearTimeout(previewLoadTimeout);
                        }

                        // Set new timeout for debounced loading
                        previewLoadTimeout = setTimeout(() => {
                            const newStartTime = new Date(this.value);
                            console.log('📅 Start time input changed (debounced):', newStartTime);

                            // Load plan preview for selected date and machine
                            loadPlanPreview(newStartTime, item.code_machine);
                        }, 500); // 500ms delay
                    }
                });

                // Update duration when end time changes
                endInput.addEventListener('change', function() {
                    const startTime = startInput.value ? new Date(startInput.value) : new Date(item.start_jam);
                    const newEndTime = new Date(this.value);

                    // Update duration display
                    const duration = Utils.calculateDuration(startTime.toISOString(), newEndTime.toISOString());
                    durationCell.textContent = duration + ' jam';

                    console.log('📅 End time changed:', newEndTime);
                });
            }

            // Setup save button event listener
            const saveBtn = document.getElementById('save-date-changes');
            if (saveBtn) {
                saveBtn.onclick = function() {
                    saveModalDateChanges(item);
                };
            }

            // Hide preview card when modal is closed
            $('#planDetailModal').on('hidden.bs.modal', function() {
                const previewCard = document.querySelector('.plan-preview-card');
                if (previewCard) {
                    previewCard.classList.remove('show');

                    setTimeout(() => {
                        previewCard.style.display = 'none';
                    }, 300);
                }
                currentEditingItemId = null;

                // Clear any pending timeout
                if (previewLoadTimeout) {
                    clearTimeout(previewLoadTimeout);
                    previewLoadTimeout = null;
                }
            });
        }

        // Load Plan Preview
        async function loadPlanPreview(selectedDate, machineCode) {
            // Clear any existing timeout
            // alert('loadPlanPreview');
            if (previewLoadTimeout) {
                clearTimeout(previewLoadTimeout);
                previewLoadTimeout = null;
            }
            try {
                // Format date to YYYY-MM-DD
                const formattedDate = selectedDate.toISOString().split('T')[0];

                // Show loading state
                const previewCard = document.querySelector('.plan-preview-card');
                const previewContent = document.getElementById('plan-preview-content');

                if (previewCard && previewContent) {
                    previewCard.style.display = 'block';
                    previewCard.classList.remove('show');
                    previewContent.innerHTML = `
                        <div class="text-center">
                            <div class="spinner-border text-info" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p class="mt-2 mb-0 text-muted">Memuat data plan untuk mesin ${machineCode}...</p>
                            <small class="text-muted">Tanggal: ${selectedDate.toLocaleDateString('id-ID')}</small>
                        </div>
                    `;

                    setTimeout(() => {
                        previewCard.classList.add('show');
                    }, 100);
                }

                const response = await fetch('/sipo/process/get-plan-preview', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        date: formattedDate,
                        machine_code: machineCode
                    })
                });

                const data = await response.json();

                if (data.success) {
                    displayPlanPreview(data.plans, selectedDate, machineCode);
                } else {
                    console.error('❌ Failed to load plan preview:', data.message);
                    displayPlanPreview([], selectedDate, machineCode, data.message);
                }
            } catch (error) {
                console.error('❌ Error loading plan preview:', error);
                displayPlanPreview([], selectedDate, machineCode, 'Terjadi kesalahan saat memuat data');
            } finally {
                // Clear timeout reference
                previewLoadTimeout = null;
            }
        }

        // Display Plan Preview
        function displayPlanPreview(plans, selectedDate, machineCode, errorMessage = null) {
            const previewCard = document.querySelector('.plan-preview-card');
            const previewContent = document.getElementById('plan-preview-content');

            if (!previewCard || !previewContent) {
                console.error('❌ Preview elements not found');
                return;
            }

            const formattedDate = selectedDate.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            if (errorMessage) {
                previewContent.innerHTML = `
                    <div class="text-center text-danger">
                        <i class="mdi mdi-alert-circle" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0">Gagal memuat data plan</p>
                        <small class="text-muted">${errorMessage}</small>
                        <br><br>
                        <button class="btn btn-sm btn-outline-info" onclick="loadPlanPreview(new Date('${selectedDate.toISOString()}'), '${machineCode}')">
                            <i class="mdi mdi-refresh"></i> Coba Lagi
                        </button>
                    </div>
                `;
            } else if (plans.length === 0) {
                previewContent.innerHTML = `
                    <div class="text-center text-muted">
                        <i class="mdi mdi-calendar-blank" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0">Tidak ada plan untuk mesin ${machineCode} pada ${formattedDate}</p>
                        <small class="text-info">
                            <i class="mdi mdi-information-outline"></i>
                            Anda dapat menambahkan plan baru untuk tanggal ini
                        </small>
                        <br><br>
                        <button class="btn btn-sm btn-outline-info" onclick="loadPlanPreview(new Date('${selectedDate.toISOString()}'), '${machineCode}')">
                            <i class="mdi mdi-refresh"></i> Refresh Data
                        </button>
                    </div>
                `;
            } else {
                const plansHtml = plans.map((plan, index) => {
                    // Get status badge color
                    let statusBadge = 'badge-secondary';
                    let statusText = plan.flag_status || 'PENDING';

                    switch (plan.flag_status?.toUpperCase()) {
                        case 'FINISH':
                            statusBadge = 'badge-success';
                            break;
                        case 'IN_PROGRESS':
                        case 'PROGRESS':
                            statusBadge = 'badge-warning';
                            break;
                        case 'PLANNED':
                            statusBadge = 'badge-info';
                            break;
                        case 'CANCELLED':
                            statusBadge = 'badge-danger';
                            break;
                        default:
                            statusBadge = 'badge-secondary';
                    }

                    // Calculate duration for this plan
                    let duration = '-';
                    if (plan.start_jam && plan.end_jam) {
                        const start = new Date(plan.start_jam);
                        const end = new Date(plan.end_jam);
                        const durationHours = (end - start) / (1000 * 60 * 60);
                        duration = durationHours.toFixed(2) + ' jam';
                    }

                    return `
                        <div class="plan-item mb-2 p-2 border rounded ${plan.id == currentEditingItemId ? 'bg-warning' : ''}">
                            <div class="row align-items-center">
                                <div class="col-md-1">
                                    <span class="badge badge-primary">${index + 1}</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>${plan.code_item}</strong><br>
                                    <small class="text-muted">${plan.material_name}</small><br>
                                    <small class="text-primary">${plan.wo_docno || '-'}</small>
                                </div>
                                <div class="col-md-1 text-center">
                                    <span class="badge badge-info">${plan.quantity} PCS</span>
                                </div>
                                <div class="col-md-1 text-center">
                                    <small class="text-muted">
                                        ${plan.start_jam ? new Date(plan.start_jam).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}) : '-'}
                                    </small>
                                </div>
                                <div class="col-md-1 text-center">
                                    <small class="text-muted">
                                        ${plan.end_jam ? new Date(plan.end_jam).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'}) : '-'}
                                    </small>
                                </div>
                                <div class="col-md-1 text-center">
                                    <small class="text-muted">${duration}</small>
                                </div>
                                <div class="col-md-1 text-center">
                                    <span class="badge ${statusBadge}">${statusText}</span>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

                // Calculate total quantity
                const totalQuantity = plans.reduce((sum, plan) => sum + (parseInt(plan.quantity) || 0), 0);

                // Calculate total duration
                const totalDuration = plans.reduce((sum, plan) => {
                    if (plan.start_jam && plan.end_jam) {
                        const start = new Date(plan.start_jam);
                        const end = new Date(plan.end_jam);
                        return sum + ((end - start) / (1000 * 60 * 60)); // hours
                    }
                    return sum;
                }, 0);

                // Calculate utilization percentage (assuming 8-hour work day)
                const workDayHours = 8;
                const utilizationPercentage = ((totalDuration / workDayHours) * 100).toFixed(1);

                // Check for time conflicts
                let hasTimeConflict = false;
                for (let i = 0; i < plans.length - 1; i++) {
                    const currentPlan = plans[i];
                    const nextPlan = plans[i + 1];

                    if (currentPlan.end_jam && nextPlan.start_jam) {
                        const currentEnd = new Date(currentPlan.end_jam);
                        const nextStart = new Date(nextPlan.start_jam);

                        if (currentEnd > nextStart) {
                            hasTimeConflict = true;
                            break;
                        }
                    }
                }

                previewContent.innerHTML = `
                    <div class="mb-2">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Mesin:</strong> ${machineCode}<br>
                                <strong>Tanggal:</strong> ${formattedDate}<br>
                                <strong>Total Plan:</strong> ${plans.length} item
                            </div>
                            <div class="col-md-6">
                                <strong>Total Quantity:</strong> ${totalQuantity.toLocaleString()} PCS<br>
                                <strong>Total Durasi:</strong> ${totalDuration.toFixed(2)} jam<br>
                                <strong>Utilization:</strong> <span class="badge ${utilizationPercentage > 100 ? 'badge-danger' : utilizationPercentage > 80 ? 'badge-warning' : 'badge-success'}">${utilizationPercentage}%</span>
                            </div>
                        </div>
                    </div>
                    <div class="plan-list">
                        <div class="row mb-2 p-2 bg-light border rounded">
                            <div class="col-md-1"><strong>No</strong></div>
                            <div class="col-md-3"><strong>Item</strong></div>
                            <div class="col-md-1 text-center"><strong>Qty</strong></div>
                            <div class="col-md-1 text-center"><strong>Start</strong></div>
                            <div class="col-md-1 text-center"><strong>End</strong></div>
                            <div class="col-md-1 text-center"><strong>Durasi</strong></div>
                            <div class="col-md-1 text-center"><strong>Status</strong></div>
                        </div>
                        ${plansHtml}
                    </div>
                    <div class="mt-2">
                        <small class="text-info">
                            <i class="mdi mdi-information-outline"></i>
                            Item yang sedang diedit ditandai dengan background kuning
                        </small>
                        ${hasTimeConflict ? `
                                                            <br><small class="text-danger">
                                                                <i class="mdi mdi-alert-circle"></i>
                                                                ⚠️ Terdeteksi konflik waktu antar plan
                                                            </small>
                                                        ` : ''}
                        <br><small class="text-muted">
                            <i class="mdi mdi-clock-outline"></i>
                            Terakhir diperbarui: ${new Date().toLocaleTimeString('id-ID')}
                        </small>
                        <br><small class="text-info">
                            <i class="mdi mdi-information-outline"></i>
                            Data akan otomatis diperbarui saat tanggal berubah
                        </small>
                    </div>
                `;
            }

            // Show the preview card with animation
            previewCard.style.display = 'block';
            previewCard.classList.remove('show');

            setTimeout(() => {
                previewCard.classList.add('show');
            }, 100);

        }

        // Handle Date Change and Conflict Resolution
        function handleDateChangeAndConflicts(item, oldDate, newDate) {


            handleAutomaticTableUpdate(item, newDate);
            handleRealTimeTableUpdate(item.code_machine, newDate);

            setTimeout(() => {
                TableRenderer.renderTable();
            }, 150);

            setTimeout(() => {
                Utils.showToast(
                    `📊 Tabel mesin ${item.code_machine} telah selesai diperbarui untuk tanggal ${newDate}`,
                    'info');
            }, 800);

            // Final table re-render to ensure all changes are visible
            setTimeout(() => {
                TableRenderer.renderTable();
            }, 1000);

            const machineItems = timelineData.filter(i => i.code_machine === item.code_machine);
            machineItems.sort((a, b) => new Date(a.start_jam) - new Date(b.start_jam));
            const editedItemIndex = machineItems.findIndex(i => i.id === item.id);
            let currentEndTime = new Date(item.end_jam);

            for (let i = editedItemIndex + 1; i < machineItems.length; i++) {
                const nextItem = machineItems[i];
                const nextItemStart = new Date(nextItem.start_jam);

                if (nextItemStart < currentEndTime) {
                    const newStartTime = new Date(currentEndTime);
                    nextItem.start_jam = newStartTime.toISOString();

                    const machine = allMachines.find(m => m.Code === item.code_machine);
                    const capacityPerHour = machine ? (machine.CapacityPerHour || 10000) : 10000;
                    const durationHours = nextItem.quantity / capacityPerHour;
                    const durationMs = durationHours * 60 * 60 * 1000;

                    const newEndTime = new Date(newStartTime.getTime() + durationMs);
                    nextItem.end_jam = newEndTime.toISOString();

                    currentEndTime = newEndTime;
                } else {
                    currentEndTime = new Date(nextItem.end_jam);
                }
            }

            const conflictsResolved = machineItems.length - editedItemIndex - 1;
            if (conflictsResolved > 0) {
                Utils.showToast(`✅ ${conflictsResolved} item(s) otomatis digeser untuk menghindari konflik waktu`, 'info');

                setTimeout(() => {
                    TableRenderer.renderTable();
                }, 200);

                setTimeout(() => {
                    Utils.showToast(`📊 Tabel telah diperbarui setelah penyelesaian konflik`, 'info');
                }, 500);
            }

            if (oldDate !== newDate) {
                Utils.showToast(`📅 Semua item di mesin ${item.code_machine} telah diperbarui ke tanggal ${newDate}`,
                    'success');

                setTimeout(() => {
                    TableRenderer.renderTable();
                }, 100);

                ensureTableUpdate(item.code_machine);

                setTimeout(() => {
                    TableRenderer.renderTable();
                }, 900);

                setTimeout(() => {
                    Utils.showToast(
                        `📊 Tabel mesin ${item.code_machine} telah selesai diperbarui untuk tanggal ${newDate}`,
                        'info');
                }, 1000);
            }
        }

        function updateMachineItemsToNewDate(machineCode, newDate) {
            const machineItems = timelineData.filter(i => i.code_machine === machineCode);

            machineItems.forEach((item, index) => {
                if (item.start_jam && item.end_jam) {
                    const oldStart = new Date(item.start_jam);
                    const oldEnd = new Date(item.end_jam);

                    // Extract time components
                    const startHours = oldStart.getHours();
                    const startMinutes = oldStart.getMinutes();
                    const startSeconds = oldStart.getSeconds();

                    const endHours = oldEnd.getHours();
                    const endMinutes = oldEnd.getMinutes();
                    const endSeconds = oldEnd.getSeconds();

                    // Create new date with same time
                    const newStartDate = new Date(newDate);
                    newStartDate.setHours(startHours, startMinutes, startSeconds, 0);

                    const newEndDate = new Date(newDate);
                    newEndDate.setHours(endHours, endMinutes, endSeconds, 0);

                    // Update item times
                    item.start_jam = newStartDate.toISOString();
                    item.end_jam = newEndDate.toISOString();

                    console.log(
                        `✅ Updated item ${item.code_item} to new date: ${newStartDate.toLocaleString()} - ${newEndDate.toLocaleString()}`
                    );
                }
            });
        }

        // Enhanced conflict detection and resolution
        function detectAndResolveConflicts(machineCode) {
            console.log(`🔍 Detecting conflicts for machine ${machineCode}`);

            const machineItems = timelineData.filter(i => i.code_machine === machineCode);
            machineItems.sort((a, b) => new Date(a.start_jam) - new Date(b.start_jam));

            let conflictsResolved = 0;
            let currentEndTime = null;

            machineItems.forEach((item, index) => {
                const itemStart = new Date(item.start_jam);

                if (currentEndTime && itemStart < currentEndTime) {
                    console.log(
                        `⚠️ Conflict detected: Item ${item.code_item} starts at ${itemStart} but should start after ${currentEndTime}`
                    );

                    // Resolve conflict by moving item to start after previous item ends
                    const newStartTime = new Date(currentEndTime);
                    item.start_jam = newStartTime.toISOString();

                    // Calculate new end time based on duration
                    const machine = allMachines.find(m => m.Code === machineCode);
                    const capacityPerHour = machine ? (machine.CapacityPerHour || 10000) : 10000;
                    const durationHours = item.quantity / capacityPerHour;
                    const durationMs = durationHours * 60 * 60 * 1000;

                    const newEndTime = new Date(newStartTime.getTime() + durationMs);
                    item.end_jam = newEndTime.toISOString();

                    console.log(
                        `✅ Resolved conflict: Item ${item.code_item} moved to ${newStartTime.toLocaleString()} - ${newEndTime.toLocaleString()}`
                    );
                    conflictsResolved++;

                    currentEndTime = newEndTime;
                } else {
                    currentEndTime = new Date(item.end_jam);
                }
            });

            if (conflictsResolved > 0) {
                Utils.showToast(`✅ ${conflictsResolved} konflik waktu berhasil diselesaikan`, 'success');

                // Force table re-render after conflict resolution
                setTimeout(() => {
                    TableRenderer.renderTable();
                }, 200);

                // Additional notification for conflict resolution
                setTimeout(() => {
                    Utils.showToast(`📊 Tabel telah diperbarui setelah penyelesaian konflik`, 'info');
                }, 500);
            }

            return conflictsResolved;
        }

        // Function to update table display after date changes
        function updateTableDisplayAfterDateChange(machineCode) {
            console.log(`🔄 Updating table display for machine ${machineCode} after date change`);

            // Force re-render the specific machine table
            const machineData = timelineData.filter(item => item.code_machine === machineCode);
            if (machineData.length > 0) {
                // Update the machine table display
                TableRenderer.updateMachineTableDisplay(machineCode, machineData);

                // Show notification
                Utils.showToast(`📊 Tabel mesin ${machineCode} telah diperbarui dengan data terbaru`, 'info');

                // Force immediate table re-render
                setTimeout(() => {
                    TableRenderer.renderTable();
                }, 50);

                // Ensure table updates are reflected
                ensureTableUpdate(machineCode);

                // Final table re-render to ensure all changes are visible
                setTimeout(() => {
                    TableRenderer.renderTable();
                }, 900);

                // Additional notification for table update completion
                setTimeout(() => {
                    Utils.showToast(`📊 Tabel mesin ${machineCode} telah selesai diperbarui`, 'info');
                }, 1100);
            }
        }

        // Function to ensure table updates are reflected immediately
        function ensureTableUpdate(machineCode) {
            console.log(`🔄 Ensuring table update for machine ${machineCode}`);

            // Force multiple re-renders to ensure changes are visible
            setTimeout(() => {
                TableRenderer.renderTable();
            }, 100);

            setTimeout(() => {
                TableRenderer.renderTable();
            }, 300);

            setTimeout(() => {
                TableRenderer.renderTable();
            }, 500);

            // Additional notification
            setTimeout(() => {
                Utils.showToast(`📊 Tabel mesin ${machineCode} telah diperbarui dengan data terbaru`, 'info');
            }, 600);

            // Final table re-render to ensure all changes are visible
            setTimeout(() => {
                TableRenderer.renderTable();
            }, 800);

            // Additional notification for table update completion
            setTimeout(() => {
                Utils.showToast(`📊 Tabel mesin ${machineCode} telah selesai diperbarui`, 'info');
            }, 1000);
        }

        // Function to handle real-time table updates
        function handleRealTimeTableUpdate(machineCode, newDate) {
            console.log(`🔄 Handling real-time table update for machine ${machineCode} to date ${newDate}`);

            // Update all items for this machine to the new date
            updateMachineItemsToNewDate(machineCode, newDate);

            // Resolve any conflicts
            detectAndResolveConflicts(machineCode);

            // Update table display
            updateTableDisplayAfterDateChange(machineCode);

            // Force immediate table re-render
            setTimeout(() => {
                TableRenderer.renderTable();
            }, 100);

            // Show comprehensive notification
            Utils.showToast(`✅ Tabel mesin ${machineCode} telah diperbarui ke tanggal ${newDate}`, 'success');

            // Ensure table updates are reflected
            ensureTableUpdate(machineCode);

            // Additional notification for table update completion
            setTimeout(() => {
                Utils.showToast(`📊 Tabel mesin ${machineCode} telah selesai diperbarui untuk tanggal ${newDate}`,
                    'info');
            }, 800);

            updateTableDisplayAfterDateChange(machineCode);

            // Ensure table updates are reflected
            ensureTableUpdate(machineCode);

            // Show comprehensive notification
            Utils.showToast(`✅ Tabel mesin ${machineCode} telah diperbarui ke tanggal ${newDate} dengan data terbaru`,
                'success');

            // Force immediate table re-render
            setTimeout(() => {
                TableRenderer.renderTable();
            }, 200);

            // Additional notification for table update completion
            setTimeout(() => {
                Utils.showToast(`📊 Tabel mesin ${machineCode} telah selesai diperbarui untuk tanggal ${newDate}`,
                    'info');
            }, 1000);

            // Final table re-render to ensure all changes are visible
            setTimeout(() => {
                TableRenderer.renderTable();
            }, 1200);
        }

        // Function to handle automatic table updates when date changes
        function handleAutomaticTableUpdate(item, newDate) {
            console.log(`🔄 Handling automatic table update for date change to ${newDate}`);

            // Update all items for this machine to the new date
            updateMachineItemsToNewDate(item.code_machine, newDate);

            // Resolve any conflicts that might arise
            detectAndResolveConflicts(item.code_machine);

            // Update the table display
            updateTableDisplayAfterDateChange(item.code_machine);

            // Force immediate table re-render
            setTimeout(() => {
                TableRenderer.renderTable();
            }, 100);

            // Show comprehensive notification
            Utils.showToast(
                `✅ Semua item di mesin ${item.code_machine} telah diperbarui ke tanggal ${newDate} dan konflik waktu telah diselesaikan`,
                'success');

            // Ensure table updates are reflected
            ensureTableUpdate(item.code_machine);

            // Additional notification for table update
            setTimeout(() => {
                Utils.showToast(`📊 Tabel telah diperbarui dengan data terbaru untuk tanggal ${newDate}`, 'info');
            }, 500);

            // Final table re-render to ensure all changes are visible
            setTimeout(() => {
                TableRenderer.renderTable();
            }, 700);

            // Additional notification for table update completion
            setTimeout(() => {
                Utils.showToast(
                    `📊 Tabel mesin ${item.code_machine} telah selesai diperbarui untuk tanggal ${newDate}`,
                    'info');
            }, 1000);
        }



        // Save Modal Date Changes
        function saveModalDateChanges(item) {
            const startInput = document.getElementById('edit-start-time');
            const endInput = document.getElementById('edit-end-time');

            if (!startInput) {
                Utils.showToast('❌ Input tanggal tidak ditemukan', 'error');
                return;
            }

            const newStartTime = startInput.value;

            if (!newStartTime) {
                Utils.showToast('❌ Tanggal mulai dan selesai harus diisi', 'warning');
                return;
            }

            const oldStartTime = item.start_jam;
            const oldEndTime = item.end_jam;
            const oldDate = new Date(oldStartTime).toISOString().split('T')[0];
            const newDate = new Date(newStartTime).toISOString().split('T')[0];

            item.start_jam = new Date(newStartTime).toISOString();

            $('#planDetailModal').modal('hide');

            Utils.showToast('✅ Tanggal dan waktu berhasil diupdate', 'success');

            handleDateChangeAndConflicts(item, oldDate, newDate);

            setTimeout(() => {
                TableRenderer.renderTable();
            }, 100);

            Utils.showToast(`📊 Tabel mesin ${item.code_machine} telah selesai diperbarui`, 'info');
        }

        // Start editing a date cell
        function startEditing(cell) {
            const display = cell.querySelector('.date-display');
            const input = cell.querySelector('.date-input');

            if (!display || !input) {
                console.error('❌ Date display or input not found');
                return;
            }

            // Add editing class
            cell.classList.add('editing');

            // Focus on input
            setTimeout(() => {
                input.focus();
                input.select();
            }, 100);
        }

        // Finish editing a date cell
        function finishEditing(cell) {
            const display = cell.querySelector('.date-display');
            const input = cell.querySelector('.date-input');

            if (!display || !input) {
                console.error('❌ Date display or input not found');
                return;
            }

            // Get new value
            const newValue = input.value;
            const field = cell.dataset.field;
            const itemId = cell.dataset.itemId;

            if (newValue) {
                // Update timeline data
                const item = timelineData.find(i => i.id == itemId);
                if (item) {
                    const oldValue = item[field];
                    item[field] = new Date(newValue).toISOString();



                    // Update display
                    display.textContent = Utils.formatDateTime(item[field]);

                    // Update duration if needed
                    if (field === 'start_jam' || field === 'end_jam') {
                        const durationCell = cell.closest('tr').querySelector('td:nth-child(10)'); // LAMA CETAK column
                        if (durationCell) {
                            const duration = Utils.calculateDuration(item.start_jam, item.end_jam);
                            durationCell.textContent = `${duration} jam`;
                        }
                    }

                    // Show success message
                    Utils.showToast(`✅ Tanggal ${field === 'start_jam' ? 'mulai' : 'selesai'} berhasil diupdate`,
                        'success');

                    // Auto-sync machine times if this affects timing (TANPA multiple renders)
                    if (item.code_machine) {
                        setTimeout(() => {
                            TableRenderer.syncMachineTimes(item.code_machine);
                        }, 300);

                        // Single table re-render after sync
                        setTimeout(() => {
                            TableRenderer.renderTable();
                        }, 500);

                        // Single notification
                        setTimeout(() => {
                            Utils.showToast(`📊 Tabel mesin ${item.code_machine} telah diperbarui`, 'info');
                        }, 600);
                    }
                }
            }

            // Remove editing class
            cell.classList.remove('editing');

            console.log('✅ Date cell edit finished');
        }
    </script>
@endsection
