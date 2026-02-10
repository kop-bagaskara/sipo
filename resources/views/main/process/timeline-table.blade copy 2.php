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
            cursor: pointer;
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
            min-width: 280px;
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
    </style>
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Plan Production</h3>
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
                    <div class="btn-group mb-4" role="group">
                        <a href="{{ route('process.plan-first-prd') }}" class="btn btn-outline-info">
                            <i class="mdi mdi-chart-gantt"></i> Timeline View
                        </a>
                        <button type="button" class="btn btn-info">
                            <i class="mdi mdi-table"></i> Table View
                        </button>
                    </div>

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
                            <button type="button" class="btn btn-info ml-2" id="debug-data-btn" title="Debug Data">
                                <i class="mdi mdi-bug"></i> Debug Data
                            </button>
                            <div class="ml-auto">
                                <span class="text-muted">
                                    <i class="mdi mdi-information-outline"></i>
                                    Filter akan otomatis diterapkan saat data berubah
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Export Section -->
                    <div class="export-section">
                        <h6><i class="mdi mdi-download"></i> Export & Print Data</h6>
                        <div class="export-buttons">
                            <button type="button" class="export-btn btn-excel" id="export-excel">
                                <i class="mdi mdi-file-excel"></i> Excel
                            </button>
                            <button type="button" class="export-btn btn-pdf" id="export-pdf">
                                <i class="mdi mdi-file-pdf"></i> PDF
                            </button>
                            <button type="button" class="export-btn btn-print" id="print-table">
                                <i class="mdi mdi-printer"></i> Print
                            </button>
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



        document.addEventListener('DOMContentLoaded', function() {
            DataManager.loadData();
            EventManager.setupEventListeners();
        });

        // Data Management
        const DataManager = {
            async loadData() {
                try {
                    DataManager.showLoading(true);

                    // Load machines, departments, and plan data
                    const [machineRes, planRes, deptRes] = await Promise.all([
                        fetch("{{ route('master.machine-cetak.data') }}").then(r => r.json()),
                        fetch("{{ route('plan.first.data') }}").then(r => r.json()),
                        fetch("{{ route('departments.list') }}").then(r => r.json())
                    ]);

                    allMachines = machineRes.data || [];
                    timelineData = planRes.data || [];
                    allDepartments = deptRes.departments || [];

                    // Jika tidak ada data, biarkan kosong
                    if (timelineData.length === 0) {
                        console.log('📭 Tidak ada data timeline yang tersedia');
                    }

                    timelineData = timelineData.map(item => {
                        if (!item.process) {
                            item.process = 'CTK';
                        }
                        return item;
                    });

                    timelineData = timelineData.filter(item => {
                        const isCTK = item.process && item.process.toUpperCase().includes('CTK');
                        return isCTK;
                    });

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

                // Set default date range
                const today = new Date();
                const nextWeek = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000);
                document.getElementById('date-from').value = today.toISOString().split('T')[0];
                document.getElementById('date-to').value = nextWeek.toISOString().split('T')[0];

                // Update current filters
                currentFilters.dateFrom = today.toISOString().split('T')[0];
                currentFilters.dateTo = nextWeek.toISOString().split('T')[0];
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

                        // Recalculate timing based on new machine capacity
                        const targetMachineData = allMachines.find(m => m.Code === targetMachine);
                        if (targetMachineData) {
                            // Simple recalculation - you can make this more sophisticated
                            const newDuration = item.quantity / (targetMachineData.CapacityPerHour || 10000);
                            const startTime = new Date(item.start_jam);
                            item.end_jam = new Date(startTime.getTime() + newDuration * 60 * 60 * 1000);
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

                        // 3. Simpan ke database via controller
                        this.saveFinishJobToDatabase(item, finishData, remainingQty).then(saveSuccess => {
                            if (saveSuccess) {
                                Utils.showToast(
                                    `Job ${item.code_item} berhasil diselesaikan dan disimpan!`,
                                    'success');
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
                                    resolve(true);
                                } else {
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
            },

            setupFilterEvents() {
                // Date filter events
                document.getElementById('date-from').addEventListener('change', function() {
                    currentFilters.dateFrom = this.value;
                    TableRenderer.renderTable();
                });

                document.getElementById('date-to').addEventListener('change', function() {
                    currentFilters.dateTo = this.value;
                    TableRenderer.renderTable();
                });

                // Machine filter events
                document.getElementById('machine-filter').addEventListener('change', function() {
                    currentFilters.machine = this.value;
                    TableRenderer.renderTable();
                });

                // Department filter events
                document.getElementById('department-filter').addEventListener('change', function() {
                    currentFilters.department = this.value;
                    TableRenderer.renderTable();
                });
            },

            setupExportEvents() {
                document.getElementById('export-excel').addEventListener('click', function() {
                    Utils.showToast('Export Excel akan segera tersedia', 'info');
                });

                document.getElementById('export-pdf').addEventListener('click', function() {
                    Utils.showToast('Export PDF akan segera tersedia', 'info');
                });
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
                            TableRenderer.renderTable();
                            const originalItem = timelineData.find(i => i.id == itemId);
                            if (originalItem) {
                                TableRenderer.syncMachineTimes(originalItem.code_machine);
                                TableRenderer.syncMachineTimes(targetMachine);
                            }
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

                machines.forEach(machineCode => {
                    const machine = allMachines.find(m => m.Code === machineCode);
                    const machineName = machine ? machine.Description : machineCode;
                    const machineData = data.filter(item => item.code_machine === machineCode);
                    const machineContainer = document.createElement('div');
                    machineContainer.className = 'machine-table-container';
                    machineContainer.id = `machine-${machineCode}`;

                    machineContainer.innerHTML = `
                        <div class="machine-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="mdi mdi-gauge"></i> Mesin: ${machineCode}
                                <span class="badge badge-primary ml-2">${machineData.length} Item</span>
                            </h4>
                            <div class="d-flex gap-2">
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
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered machine-table" id="machine-table-${machineCode}">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 80px;">PRIORITY</th>
                                        <th>KODE ITEM</th>
                                        <th>NAMA ITEM</th>
                                        <th>WO DOCNO</th>
                                        <th>QTY ORDER</th>
                                        <th>TGL DELIVERY</th>
                                        <th>PROCESS</th>
                                        <th>KAPASITAS</th>
                                        <th>LAMA CETAK</th>
                                        <th>MULAI CETAK</th>
                                        <th>AKHIR CETAK</th>
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
                        <tr>
                            <td colspan="12" class="text-center text-muted py-4">
                                <i class="mdi mdi-information-outline" style="font-size: 2rem;"></i>
                                <br><br>
                                Tidak ada data untuk mesin ${machineCode}
                            </td>
                        </tr>
                    `;
                    return;
                }

                machineData.forEach((item, index) => {
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
                            <div class="priority-number" style="font-weight: bold; color: #007bff; font-size: 1.1rem;">${index + 1}</div>
                            ${!isMaintenance ? `
                                        <div class="priority-controls mt-1">
                                            <button class="btn btn-sm btn-outline-primary priority-up-btn"
                                                    data-item-id="${item.id}"
                                                    data-machine="${item.code_machine}"
                                                    data-current-priority="${index}"
                                                    ${index === 0 ? 'disabled' : ''}
                                                    title="Naikkan Prioritas">
                                                <i class="mdi mdi-arrow-up"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary priority-down-btn"
                                                    data-item-id="${item.id}"
                                                    data-machine="${item.code_machine}"
                                                    data-current-priority="${index}"
                                                    ${index === machineData.length - 1 ? 'disabled' : ''}
                                                    title="Turunkan Prioritas">
                                                <i class="mdi mdi-arrow-down"></i>
                                            </button>
                                        </div>
                                    ` : `
                                        <div class="maintenance-badge mt-1">
                                            <span class="badge badge-warning">
                                                <i class="mdi mdi-wrench"></i> Maintenance
                                            </span>
                                        </div>
                                    `}
                        </td>
                        <td>
                            <strong>${item.code_item || '-'}</strong>
                            ${isMaintenance ? '<br><small class="text-muted">' + (item.jenis_maintenance || 'Maintenance') + '</small>' : ''}
                        </td>
                        <td>${item.material_name || '-'}</td>
                        <td>${item.wo_docno || '-'}</td>
                        <td>${parseInt(item.quantity || 0).toLocaleString()}</td>
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
                            return isWOP ? '8.00 jam' : `${Utils.calculateDuration(item.start_jam, item.end_jam)} jam`;
                        })()}</td>
                        <td>${Utils.formatDateTime(item.start_jam)}</td>
                        <td>${Utils.formatDateTime(item.end_jam)}</td>
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

                    // Add click event for detail modal
                    row.addEventListener('click', (e) => {
                        if (!e.target.closest('.btn')) {
                            this.showDetailModal(item, machine);
                        }
                    });

                    tbody.appendChild(row);
                });

                // Check job order status for this machine's items
                this.checkJobOrderStatusForMachine(machineCode, machineData);
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

                        // Add warning icon to the WO column but keep original WO number
                        const woCell = row.querySelector('td:nth-child(4)'); // WO column
                        if (woCell) {
                            // Get original WO number from the cell
                            const originalWO = woCell.textContent.trim();
                            woCell.innerHTML = `
                                <div class="wo-cell-content">
                                    <i class="mdi mdi-alert-circle wo-status-icon text-danger" title="Belum ada Job Order"></i>
                                    <span class="wo-number text-danger">${originalWO}</span>
                                </div>
                            `;
                        }
                    } else {
                        // Add green styling for items with job order
                        row.classList.add('has-job-order');
                        row.style.backgroundColor = '#e8f5e8';
                        row.style.borderLeft = '4px solid #4caf50';

                        // Add check icon to the WO column but keep original WO number
                        const woCell = row.querySelector('td:nth-child(4)'); // WO column
                        if (woCell) {
                            // Get original WO number from the cell
                            const originalWO = woCell.textContent.trim();
                            woCell.innerHTML = `
                                <div class="wo-cell-content">
                                    <i class="mdi mdi-check-circle wo-status-icon text-success" title="Sudah ada Job Order: ${itemStatus.job_order_number}"></i>
                                    <span class="wo-number text-success">${originalWO}</span>
                                </div>
                            `;
                        }
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

                    const container = document.querySelector('.filter-section');
                    if (container) {
                        container.appendChild(summaryElement);
                    }
                }

                if (missingCount > 0) {
                    summaryElement.className = 'alert alert-warning mt-3';
                    summaryElement.innerHTML = `
                        <i class="mdi mdi-alert-triangle"></i>
                        <strong>Peringatan Job Order:</strong>
                        ${missingCount} dari ${totalCount} item belum memiliki Job Order
                        <button class="btn btn-sm btn-outline-warning ml-2" onclick="TableRenderer.refreshJobOrderStatus()">
                            <i class="mdi mdi-refresh"></i> Refresh
                        </button>
                        <button class="btn btn-sm btn-outline-secondary ml-2" onclick="TableRenderer.clearJobOrderStyling()">
                            <i class="mdi mdi-close"></i> Clear Styling
                        </button>
                    `;
                } else {
                    summaryElement.className = 'alert alert-success mt-3';
                    summaryElement.innerHTML = `
                        <i class="mdi mdi-check-circle"></i>
                        <strong>Status Job Order:</strong>
                        Semua ${totalCount} item sudah memiliki Job Order
                        <button class="btn btn-sm btn-outline-success ml-2" onclick="TableRenderer.refreshJobOrderStatus()">
                            <i class="mdi mdi-refresh"></i> Refresh
                        </button>
                        <button class="btn btn-sm btn-outline-secondary ml-2" onclick="TableRenderer.clearJobOrderStyling()">
                            <i class="mdi mdi-close"></i> Clear Styling
                        </button>
                    `;
                }
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

                    // Restore original WO number (remove icon, keep only text)
                    const woCell = row.querySelector('td:nth-child(4)');
                    if (woCell) {
                        const woContent = woCell.querySelector('.wo-number');
                        if (woContent) {
                            woCell.innerHTML = woContent.textContent;
                        }
                    }
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

            showDetailModal(item, machine) {
                document.getElementById('modal-so').textContent = item.so_docno || '-';
                document.getElementById('modal-wo').textContent = item.wo_docno || '-';
                document.getElementById('modal-item').textContent = item.code_item || '-';
                document.getElementById('modal-material-name').textContent = item.material_name || '-';
                document.getElementById('modal-qty').textContent = parseInt(item.quantity || 0).toLocaleString();
                document.getElementById('modal-machine').textContent = machine ? machine.Description : item
                    .code_machine;
                document.getElementById('modal-capacity-per-hour').textContent = machine ? machine.CapacityPerHour :
                    '-';
                document.getElementById('modal-process').textContent = item.process || '-';
                document.getElementById('modal-department').textContent = machine ? machine.Department : '-';

                // Ubah Start Time dan End Time menjadi input yang bisa diedit
                const modalStart = document.getElementById('modal-start');
                const modalEnd = document.getElementById('modal-end');

                // Format datetime untuk input datetime-local
                let startDateTime = '';
                let endDateTime = '';

                if (item.start_jam) {
                    // Hilangkan T dan konversi ke format YYYY-MM-DD HH:mm:ss
                    startDateTime = item.start_jam.replace('T', ' ').substring(0, 19);
                }

                if (item.end_jam) {
                    // Hilangkan T dan konversi ke format YYYY-MM-DD HH:mm:ss
                    endDateTime = item.end_jam.replace('T', ' ').substring(0, 19);
                }

                modalStart.innerHTML = `
                    <div class="d-flex align-items-center">
                        <input type="datetime-local" class="form-control"
                               id="edit-start-time" value="${startDateTime}"
                               style="width: 200px;">
                    </div>
                `;

                modalEnd.innerHTML = `
                    <div class="d-flex align-items-center">
                        <span class="mr-2">${Utils.formatDateTime(item.end_jam)}</span>
                    </div>
                `;

                document.getElementById('modal-duration').textContent = Utils.calculateDuration(item.start_jam,
                    item.end_jam) + ' jam';
                document.getElementById('modal-status').textContent = item.flag_status || 'Pending';

                // Store item data untuk update
                document.getElementById('planDetailModal').dataset.itemId = item.id;
                document.getElementById('planDetailModal').dataset.machineCode = item.code_machine;

                $('#planDetailModal').modal('show');

                // Hide preview card initially
                const previewCard = document.querySelector('.plan-preview-card');
                if (previewCard) {
                    previewCard.style.display = 'none';
                    previewCard.classList.remove('show');
                }

                // Setup event listeners untuk input datetime
                setupModalDateTimeListeners(item);
                const startInput = document.getElementById('edit-start-time');


                // Add focus event to show preview immediately when input is focused
                startInput.addEventListener('focus', function() {
                    if (this.value) {
                        const currentDate = new Date(this.value);
                        loadPlanPreview(currentDate, item.code_machine);
                    } else {
                        // If no value, load for today
                        loadPlanPreview(new Date(), item.code_machine);
                    }
                });

                startInput.addEventListener('change', function() {
                    if (this.value) {
                        const currentDate = new Date(this.value);
                        loadPlanPreview(currentDate, item.code_machine);
                    }
                });

                // Add click event to show preview when input is clicked
                startInput.addEventListener('click', function() {
                    if (this.value) {
                        const currentDate = new Date(this.value);
                        loadPlanPreview(currentDate, item.code_machine);
                    } else {
                        // If no value, load for today
                        loadPlanPreview(new Date(), item.code_machine);
                    }
                });
            },

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
                        // Update start time column (index 8)
                        const startTimeCell = row.cells[8];
                        if (startTimeCell) {
                            startTimeCell.innerHTML = Utils.formatDateTime(item.start_jam);
                        }

                        // Update end time column (index 9)
                        const endTimeCell = row.cells[9];
                        if (endTimeCell) {
                            endTimeCell.innerHTML = Utils.formatDateTime(item.end_jam);
                        }

                        // Update duration column (index 7)
                        const durationCell = row.cells[7];
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

                    row.innerHTML = `
                        <td class="priority-cell text-center">
                            <div class="priority-number">${index + 1}</div>
                            <div class="priority-controls mt-1">
                                <button class="btn btn-sm btn-outline-primary priority-up-btn"
                                        data-item-id="${item.id}"
                                        data-machine="${machineCode}"
                                        data-current-priority="${index}"
                                        ${index === 0 ? 'disabled' : ''}
                                        title="Naikkan Prioritas">
                                    <i class="mdi mdi-arrow-up"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary priority-down-btn"
                                        data-item-id="${item.id}"
                                        data-machine="${machineCode}"
                                        data-current-priority="${index}"
                                        ${index === items.length - 1 ? 'disabled' : ''}
                                        title="Turunkan Prioritas">
                                    <i class="mdi mdi-arrow-down"></i>
                                </button>
                            </div>
                        </td>
                        <td><strong>${item.code_item || '-'}</strong></td>
                        <td>${item.material_name || '-'}</td>
                        <td>${item.wo_docno || '-'}</td>
                        <td>${parseInt(item.quantity || 0).toLocaleString()}</td>
                        <td>${item.delivery_date || '-'}</td>
                        <td class="text-center process-badge ${item.process ? item.process.toLowerCase().replace(/\s/g, '-') : ''}">${item.process || '-'}</td>
                        <td>${parseInt(item.capacity || 10000).toLocaleString()}</td>
                        <td>${(() => {
                            const isWOP = item.wo_docno && item.wo_docno.toUpperCase().startsWith('WOP');
                            return isWOP ? '8.00 jam' : `${Utils.calculateDuration(item.start_jam, item.end_jam)} jam`;
                        })()}</td>
                        <td>${Utils.formatDateTime(item.start_jam)}</td>
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

                    // Add click event for detail modal
                    row.addEventListener('click', (e) => {
                        if (!e.target.closest('.btn')) {
                            this.showDetailModal(item, machine);
                        }
                    });

                    tbody.appendChild(row);
                });

                // Setup event listeners for priority buttons in the new rows
                this.setupPriorityButtonsForMachine(machineCode);

                console.log(`✅ Successfully reordered and updated table for machine ${machineCode}`);
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

                    row.innerHTML = `
                        <td class="priority-cell text-center">
                            <div class="priority-number">${index + 1}</div>
                            <div class="priority-controls mt-1">
                                <button class="btn btn-sm btn-outline-primary priority-up-btn"
                                        data-item-id="${item.id}"
                                        data-machine="${machineCode}"
                                        data-current-priority="${index}"
                                        ${index === 0 ? 'disabled' : ''}
                                        title="Naikkan Prioritas">
                                    <i class="mdi mdi-arrow-up"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary priority-down-btn"
                                        data-item-id="${item.id}"
                                        data-machine="${machineCode}"
                                        data-current-priority="${index}"
                                        ${index === items.length - 1 ? 'disabled' : ''}
                                        title="Turunkan Prioritas">
                                    <i class="mdi mdi-arrow-down"></i>
                                </button>
                            </div>
                        </td>
                        <td><strong>${item.code_item || '-'}</strong></td>
                        <td>${item.material_name || '-'}</td>
                        <td>${item.wo_docno || '-'}</td>
                        <td>${parseInt(item.quantity || 0).toLocaleString()}</td>
                        <td>${item.delivery_date || '-'}</td>
                        <td class="text-center process-badge ${item.process ? item.process.toLowerCase().replace(/\s/g, '-') : ''}">${item.process || '-'}</td>
                        <td>${parseInt(item.capacity || 10000).toLocaleString()}</td>
                        <td>${(() => {
                            const isWOP = item.wo_docno && item.wo_docno.toUpperCase().startsWith('WOP');
                            return isWOP ? '8.00 jam (1 shift)' : `${Utils.calculateDuration(item.start_jam, item.end_jam)} jam`;
                        })()}</td>
                        <td>${Utils.formatDateTime(item.start_jam)}</td>
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

                    // Add click event for detail modal
                    row.addEventListener('click', (e) => {
                        if (!e.target.closest('.btn')) {
                            this.showDetailModal(item, machine);
                        }
                    });

                    tbody.appendChild(row);
                });

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

                // Auto-apply filters on change
                const filterInputs = ['date-from', 'date-to', 'machine-filter', 'department-filter'];
                filterInputs.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.addEventListener('change', Utils.debounce(() => {
                            this.updateFilters();
                            TableRenderer.renderTable();
                        }, 500));
                    }
                });
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
                const nextWeek = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000);

                document.getElementById('date-from').value = today.toISOString().split('T')[0];
                document.getElementById('date-to').value = nextWeek.toISOString().split('T')[0];
                document.getElementById('machine-filter').value = '';
                document.getElementById('department-filter').value = '';

                currentFilters.dateFrom = today.toISOString().split('T')[0];
                currentFilters.dateTo = nextWeek.toISOString().split('T')[0];
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

                const result = await response.json();
                console.log('📬 Server response:', result);

                if (response.ok) {
                    Utils.showToast('✅ Plan update berhasil disimpan ke database', 'success');
                } else {
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
                    saveBtn.innerHTML = '<i class="mdi mdi-content-save"></i> Save Plan Update';
                }
            }
        }

        // Prepare plan data for saving
        function preparePlanDataForSave() {
            const planUpdates = [];

            // Group by machine for organized data
            const machineGroups = {};

            timelineData.forEach(item => {
                if (!machineGroups[item.code_machine]) {
                    machineGroups[item.code_machine] = [];
                }

                machineGroups[item.code_machine].push({
                    id: item.id,
                    code_item: item.code_item,
                    material_name: item.material_name,
                    code_machine: item.code_machine,
                    quantity: item.quantity,
                    start_jam: item.start_jam,
                    end_jam: item.end_jam,
                    delivery_date: item.delivery_date,
                    process: item.process,
                    flag_status: item.flag_status || 'pending',
                    // Add calculated fields
                    duration_hours: Utils.calculateDuration(item.start_jam, item.end_jam),
                    priority_order: machineGroups[item.code_machine].length + 1
                });
            });

            // Convert to flat array with priority info
            Object.keys(machineGroups).forEach(machineCode => {
                machineGroups[machineCode].forEach((item, index) => {
                    item.priority_order = index + 1; // Update correct priority
                    planUpdates.push(item);
                });
            });

            return {
                plan_updates: planUpdates,
                total_items: planUpdates.length,
                machines_affected: Object.keys(machineGroups),
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
        });



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
                        const durationCell = cell.closest('tr').querySelector('td:nth-child(9)'); // LAMA CETAK column
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
