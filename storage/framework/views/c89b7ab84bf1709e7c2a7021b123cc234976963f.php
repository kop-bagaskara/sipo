<?php $__env->startSection('title'); ?>
    Plan First Production
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="https://unpkg.com/vis-timeline@latest/styles/vis-timeline-graph2d.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        #visualization {
            width: 100%;
            min-height: 300px;
            border: 1px solid #e0e0e0;
            background: #fafdff;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
            padding: 4px 0 4px 0;
        }

        .vis-item.vis-range.status-planned.vis-editable {
            background-color: #1976d2 !important;
            color: white !important;
            font-weight: bold !important;
            border-radius: 3px !important;
        }

        .vis-item.vis-range.status-finish.vis-editable {
            background-color: #1976d2 !important;
            color: white !important;
            font-weight: bold !important;
            border-radius: 3px !important;
        }

        /* State untuk item yang sedang aktif/diklik */
        .vis-item.vis-range.status-planned.vis-editable:active,
        .vis-item.vis-range.status-finish.vis-editable:active {
            background-color: #bbc460 !important;
            color: white !important;
            font-weight: bold !important;
            transform: scale(0.98);
            box-shadow: 0 1px 4px rgba(25, 118, 210, 0.5) !important;
        }

        /* State untuk item yang sedang di-focus */
        .vis-item.vis-range.status-planned.vis-editable:focus,
        .vis-item.vis-range.status-finish.vis-editable:focus {
            background-color: #bbc460 !important;
            color: white !important;
            font-weight: bold !important;
            box-shadow: 0 0 0 3px rgba(25, 118, 210, 0.3) !important;
        }

        /* Custom class untuk item yang sedang aktif */
        .vis-item.vis-range.status-planned.vis-editable.active,
        .vis-item.vis-range.status-finish.vis-editable.active {
            background-color: #bbc460 !important;
            color: white !important;
            font-weight: bold !important;
            box-shadow: 0 0 0 3px rgba(156, 250, 74, 0.5) !important;
            transform: scale(1.02);
        }

        /* CSS yang sangat spesifik untuk item aktif */
        #visualization .vis-timeline .vis-item.active,
        #visualization .vis-item.active,
        .vis-timeline .vis-item.active,
        .vis-item.active {
            background-color: #bbc460 !important;
            color: white !important;
            font-weight: bold !important;
            box-shadow: 0 0 0 3px rgba(156, 250, 74, 0.5) !important;
            transform: scale(1.02);
        }

        /* CSS untuk item yang memiliki inline style */
        .vis-item[style*="background-color: #9cfa4a"] {
            background-color: #bbc460 !important;
            color: white !important;
            font-weight: bold !important;
        }

        /* CSS untuk item yang memiliki inline style dengan rgb */
        .vis-item[style*="rgb(156, 250, 74)"] {
            background-color: #bbc460 !important;
            color: white !important;
            font-weight: bold !important;
        }

        .vis-item.vis-background {
            background-color: rgba(255, 0, 0, 0.2);
            border: none;
        }

        /* Background styling untuk berbagai tipe holiday */
        .vis-item.vis-background.bg-holiday {
            background-color: rgba(0, 123, 255, 0.15);
            border: none;
        }

        .vis-item.vis-background.bg-sunday {
            background-color: rgba(0, 123, 255, 0.15);
            border: none;
        }

        .vis-item.vis-background.bg-saturday {
            background-color: rgba(0, 123, 255, 0.15);
            border: none;
        }

        .vis-item.vis-background.bg-national-holiday {
            background-color: rgba(0, 123, 255, 0.2);
            border: none;
        }

        .vis-item.vis-background.bg-company-holiday {
            background-color: rgba(0, 123, 255, 0.15);
            border: none;
        }

        /* Highlight untuk plan terdekat */
        .vis-item.nearest-plan-highlight {
            animation: pulse-highlight 2s ease-in-out infinite;
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.8) !important;
            border: 3px solid #1976d2 !important;
            z-index: 1000 !important;
        }

        @keyframes  pulse-highlight {
            0% {
                transform: scale(1);
                box-shadow: 0 0 20px rgba(0, 123, 255, 0.8);
            }

            50% {
                transform: scale(1.05);
                box-shadow: 0 0 30px rgba(0, 123, 255, 1);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 0 20px rgba(0, 123, 255, 0.8);
            }
        }

        .vis-item {
            border-radius: 8px !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: none !important;
            outline: none !important;
            color: #222;
            font-size: 1rem;
            font-family: 'Segoe UI', 'Arial', sans-serif;
            transition: box-shadow 0.2s;
        }

        /* Override untuk item yang sedang aktif */
        .vis-item.active {
            background-color: #9cfa4a !important;
            color: white !important;
            font-weight: bold !important;
            box-shadow: 0 0 0 3px rgba(156, 250, 74, 0.5) !important;
            transform: scale(1.02);
        }

        .vis-item:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            /* background: #e3f2fd !important; */
            /* Disabled hover background change */
            border: none !important;
            outline: none !important;
        }

        /* Ensure no borders or outlines on timeline items */
        .vis-item:focus {
            outline: none !important;
            border: none !important;
        }

        .vis-item:active {
            outline: none !important;
            border: none !important;
        }

        .vis-item .plan-title {
            font-weight: 600;
            font-size: 0.95rem;
            color: #007bff;
        }

        .vis-item .plan-detail {
            font-size: 0.85rem;
            color: #555;
        }

        /* Status coloring */
        .vis-item.status-pending {
            background: rgba(0, 123, 255, 0.1) !important;
            border-left: 4px solid #007bff !important;
        }

        .vis-item.status-progress {
            background: rgba(0, 123, 255, 0.2) !important;
            border-left: 4px solid #007bff !important;
        }

        .vis-item.status-completed {
            background: rgba(0, 123, 255, 0.15) !important;
            border-left: 4px solid #007bff !important;
        }

        .vis-item.status-urgent {
            background: rgba(0, 123, 255, 0.25) !important;
            border-left: 4px solid #007bff !important;
        }

        /* Timeline axis & grid */
        .vis-time-axis {
            background: #f6f8fa;
            font-family: 'Segoe UI', 'Arial', sans-serif;
            font-size: 0.92rem;
        }

        /* Styling untuk tanggal 25 Agustus */
        .special-date-25-august {
            background: #ffebee !important;
            color: #d32f2f !important;
            font-weight: 600;
        }

        .vis-grid.vis-odd {
            background: #f6f8fa;
        }

        /* Styling untuk hari kerja - Normal */
        .vis-time-axis .vis-minor.vis-monday,
        .vis-time-axis .vis-minor.vis-tuesday,
        .vis-time-axis .vis-minor.vis-wednesday,
        .vis-time-axis .vis-minor.vis-thursday,
        .vis-time-axis .vis-minor.vis-friday {
            background: #f8f9fa !important;
            color: #495057 !important;
            font-weight: 500 !important;
        }

        .vis-grid.vis-even {
            background: #fff;
        }

        /* Remove thick black border from timeline */
        .vis-timeline {
            border: none !important;
            outline: none !important;
        }

        .vis-timeline .vis-panel {
            border: none !important;
            outline: none !important;
        }

        .vis-timeline .vis-panel.vis-center {
            border: none !important;
            outline: none !important;
        }

        .vis-timeline .vis-panel.vis-left {
            border: none !important;
            outline: none !important;
        }

        .vis-timeline .vis-panel.vis-right {
            border: none !important;
            outline: none !important;
        }

        .vis-timeline .vis-panel.vis-top {
            border: none !important;
            outline: none !important;
        }

        .vis-timeline .vis-panel.vis-bottom {
            border: none !important;
            outline: none !important;
        }

        /* Remove borders from grid cells */
        .vis-timeline .vis-grid {
            border: none !important;
            outline: none !important;
        }

        .vis-timeline .vis-grid.vis-odd {
            border: none !important;
            outline: none !important;
        }

        .vis-timeline .vis-grid.vis-even {
            border: none !important;
            outline: none !important;
        }

        /* Keep time axis borders for grid lines */
        .vis-timeline .vis-time-axis {
            border-bottom: 1px solid #e0e0e0 !important;
            border-left: none !important;
            border-right: none !important;
            border-top: none !important;
        }

        .vis-timeline .vis-time-axis .vis-minor {
            border-right: 1px solid #e0e0e0 !important;
            border-bottom: none !important;
            border-left: none !important;
            border-top: none !important;
        }

        .vis-timeline .vis-time-axis .vis-major {
            border-right: 1px solid #e0e0e0 !important;
            border-bottom: none !important;
            border-left: none !important;
            border-top: none !important;
        }

        /* Keep label borders for grid lines */
        .vis-timeline .vis-labelset {
            border-right: 1px solid #e0e0e0 !important;
            border-bottom: none !important;
            border-left: none !important;
            border-top: none !important;
        }

        .vis-timeline .vis-labelset .vis-label {
            border-bottom: 1px solid #e0e0e0 !important;
            border-left: none !important;
            border-right: none !important;
            border-top: none !important;
        }

        /* Remove borders from timeline container */
        #visualization {
            border: none !important;
            outline: none !important;
        }

        #visualization .vis-timeline {
            border: none !important;
            outline: none !important;
        }

        /* Remove only outer borders, keep internal grid lines */
        .vis-timeline {
            border: none !important;
            outline: none !important;
        }

        .vis-timeline .vis-panel {
            border: none !important;
            outline: none !important;
        }

        /* Keep internal grid lines but remove outer borders */
        .vis-timeline .vis-grid {
            border-right: 1px solid #e0e0e0 !important;
            border-bottom: 1px solid #e0e0e0 !important;
            border-left: none !important;
            border-top: none !important;
        }

        .vis-timeline .vis-grid.vis-odd {
            border-right: 1px solid #e0e0e0 !important;
            border-bottom: 1px solid #e0e0e0 !important;
            border-left: none !important;
            border-top: none !important;
        }

        .vis-timeline .vis-grid.vis-even {
            border-right: 1px solid #e0e0e0 !important;
            border-bottom: 1px solid #e0e0e0 !important;
            border-left: none !important;
            border-top: none !important;
        }

        /* Keep timeline items with their borders */
        .vis-timeline .vis-item {
            border: inherit !important;
            outline: inherit !important;
        }

        /* Resource label */
        .vis-labelset .vis-label {
            font-weight: 500;
            color: #333;
            font-size: 0.85rem;
            line-height: 1.1;
            padding-top: 2px;
            padding-bottom: 2px;
            white-space: normal;
            word-break: break-word;
        }

        .machine-label {
            font-weight: 500;
            font-size: 0.8rem;
            color: #1976d2;
            letter-spacing: 0.01em;
        }

        /* Card & form tweaks */
        .card {
            border-radius: 12px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .card-body {
            padding-bottom: 0.5rem;
            background: #f8fafc;
            border-radius: 12px;
        }

        .spinner-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.7);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Machine management modal styles */
        .machine-item {
            transition: background-color 0.2s;
        }

        .machine-item:hover {
            background-color: #f8f9fa;
        }

        .machine-item .btn {
            min-width: 60px;
        }

        #available-machines-list,
        #active-machines-list {
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .form-check-input:checked {
            background-color: #1976d2;
            border-color: #1976d2;
        }

        /* Select2 styling for modal */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 34px;
            padding-left: 12px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .select2-dropdown {
            border: 2px solid #e1e5e9;
            border-radius: 6px;
        }

        .select2-search__field {
            border: 1px solid #e1e5e9 !important;
            border-radius: 4px !important;
            padding: 8px !important;
        }
    </style>
    <style>
        .timeline-container {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
        }

        .timeline-header {
            background: #1976d2;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
        }

        .timeline-title {
            font-size: 28px;
            font-weight: 600;
            margin: 0;
            text-align: center;
        }

        .timeline-subtitle {
            font-size: 16px;
            text-align: center;
            margin-top: 10px;
            opacity: 0.9;
        }

        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-group label {
            font-weight: 600;
            color: #333;
            min-width: 120px;
        }

        .filter-group select,
        .filter-group input[type="checkbox"] {
            padding: 8px 12px;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .filter-group select:focus,
        .filter-group input[type="checkbox"]:focus {
            outline: none;
            border-color: #667eea;
        }

        .timeline-grid {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .timeline-header-row {
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        .timeline-header-cell {
            padding: 15px 10px;
            text-align: center;
            font-weight: 600;
            color: #495057;
            border-right: 1px solid #dee2e6;
            min-width: 120px;
            position: relative;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .timeline-header-cell:last-child {
            border-right: none;
        }

        .timeline-header-cell.holiday {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%) !important;
            color: white !important;
            box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3) !important;
            font-weight: 700 !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
        }

        /* .timeline-header-cell.sunday {
                                background: linear-gradient(135deg, #ff8a80 0%, #ff7043 100%) !important;
                                color: white !important;
                                box-shadow: 0 2px 8px rgba(255, 138, 128, 0.3) !important;
                                font-weight: 700 !important;
                                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
                            } */

        .timeline-header-cell.national-holiday {
            background: linear-gradient(135deg, #d32f2f 0%, #b71c1c 100%) !important;
            color: white !important;
            box-shadow: 0 2px 8px rgba(211, 50, 47, 0.3) !important;
            font-weight: 700 !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
        }

        /* Custom style untuk tanggal 26 Agustus */
        .timeline-header-cell.special-date-26-august {
            background: linear-gradient(135deg, #ff1744 0%, #d50000 100%) !important;
            color: white !important;
            box-shadow: 0 2px 8px rgba(255, 23, 68, 0.4) !important;
            font-weight: 700 !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
            border: 2px solid #ff4081 !important;
        }

        /* Custom style untuk tanggal 27 Agustus */
        .timeline-header-cell.special-date-27-august {
            background: linear-gradient(135deg, #d32f2f 0%, #b71c1c 100%) !important;
            color: white !important;
            box-shadow: 0 2px 8px rgba(211, 50, 47, 0.4) !important;
            font-weight: 700 !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
            border: 2px solid #d32f2f !important;
        }

        .timeline-header-cell.workday {
            background: #007bff !important;
            color: white !important;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3) !important;
            font-weight: 700 !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3) !important;
        }

        .timeline-header-cell:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .holiday-indicator {
            position: absolute;
            top: 5px;
            right: 5px;
            font-size: 10px;
            background: rgba(255, 255, 255, 0.25);
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .timeline-header-cell.holiday .holiday-indicator {
            background: rgba(255, 255, 255, 0.3);
        }

        .timeline-header-cell.sunday .holiday-indicator {
            background: rgba(255, 255, 255, 0.3);
        }

        .timeline-header-cell.national-holiday .holiday-indicator {
            background: rgba(255, 255, 255, 0.3);
        }

        .timeline-header-cell.special-date-26-august .holiday-indicator {
            background: rgba(255, 255, 255, 0.4);
            color: #d50000;
            font-weight: 700;
        }

        .timeline-header-cell.workday .holiday-indicator {
            background: rgba(255, 255, 255, 0.3);
        }

        .timeline-row {
            display: flex;
            border-bottom: 1px solid #e9ecef;
            min-height: 80px;
        }

        .timeline-row:last-child {
            border-bottom: none;
        }

        .machine-cell {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15px 20px;
            border-right: 2px solid #dee2e6;
            min-width: 200px;
            display: flex;
            align-items: center;
            font-weight: 600;
            color: #495057;
            position: relative;
            transition: all 0.3s ease;
        }

        .machine-cell:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            transform: translateX(5px);
        }

        .machine-cell::after {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(180deg, transparent 0%, #007bff 50%, transparent 100%);
        }

        .machine-cell .machine-icon {
            margin-right: 10px;
            font-size: 18px;
            color: #007bff;
        }

        .timeline-cell {
            flex: 1;
            border-right: 1px solid #dee2e6;
            min-width: 120px;
            position: relative;
            background: white;
            transition: all 0.3s ease;
            position: relative;
        }

        .timeline-cell:last-child {
            border-right: none;
        }

        .timeline-cell.holiday {
            background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
            border-left: 4px solid #ff6b6b;
        }

        .timeline-cell.sunday {
            background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
            border-left: 4px solid #ff8a80;
        }

        .timeline-cell.national-holiday {
            background: linear-gradient(135deg, #ffcdd2 0%, #ef9a9a 100%);
            border-left: 4px solid #d32f2f;
        }

        /* Custom style untuk timeline cell tanggal 26 Agustus */
        .timeline-cell.special-date-26-august {
            background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
            border-left: 4px solid #ff1744;
            border-right: 2px solid #ff4081;
        }

        /* Custom style untuk timeline cell tanggal 27 Agustus */
        .timeline-cell.special-date-27-august {
            background: linear-gradient(135deg, #ffcdd2 0%, #ef9a9a 100%);
            border-left: 4px solid #d32f2f;
            border-right: 2px solid #d32f2f;
        }

        .timeline-cell.workday {
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.1) 0%, rgba(0, 123, 255, 0.05) 100%);
            border-left: 4px solid #007bff;
        }

        .timeline-cell.drag-over {
            background: #e3f2fd;
            border: 2px dashed #2196f3;
        }

        .timeline-cell.drag-over {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            border: 2px dashed #2196f3;
            transform: scale(1.02);
        }

        .timeline-cell::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.1) 50%, transparent 100%);
        }

        .timeline-item {
            background: linear-gradient(135deg, #4fc3f7 0%, #29b6f6 100%);
            color: white;
            padding: 8px 12px;
            margin: 5px;
            border-radius: 6px;
            font-size: 12px;
            cursor: move;
            user-select: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            position: absolute;
            z-index: 10;
        }

        .timeline-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .timeline-item.dragging {
            opacity: 0.8;
            transform: rotate(5deg);
            z-index: 1000;
        }

        .timeline-item.drag-over {
            background: linear-gradient(135deg, #66bb6a 0%, #4caf50 100%);
        }

        .timeline-item.drag-invalid {
            background: linear-gradient(135deg, #ef5350 0%, #e53935 100%);
        }

        .time-slot {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            color: #6c757d;
            background: rgba(248, 249, 250, 0.5);
            border-bottom: 1px solid #e9ecef;
        }

        .holiday-settings {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .holiday-settings h4 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .holiday-form {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            padding: 8px 12px;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 14px;
            min-width: 150px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
        }

        .holiday-list {
            margin-top: 20px;
        }

        .holiday-accordion {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
        }

        .holiday-accordion-header {
            background: #1976d2;
            color: white;
            padding: 15px 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s ease;
        }

        .holiday-accordion-header:hover {
            background: #007bff;
        }

        .holiday-accordion-header h6 {
            margin: 0;
            font-weight: 600;
        }

        .holiday-accordion-icon {
            transition: transform 0.3s ease;
        }

        .holiday-accordion-header.collapsed .holiday-accordion-icon {
            transform: rotate(-90deg);
        }

        .holiday-accordion-body {
            background: white;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .holiday-accordion-body.expanded {
            max-height: 500px;
        }

        .holiday-accordion-content {
            padding: 20px;
        }

        .holiday-category {
            margin-bottom: 20px;
        }

        .holiday-category:last-child {
            margin-bottom: 0;
        }

        .holiday-category-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .holiday-category-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 12px;
            color: white;
        }

        .holiday-category-icon.sunday {
            background: #ff8a80;
        }

        .holiday-category-icon.database {
            background: #4fc3f7;
        }

        .holiday-category-icon.workday {
            background: #66bb6a;
        }

        .holiday-category-icon.holiday {
            background: #ff6b6b;
        }

        .holiday-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            background: #f8f9fa;
            border-radius: 6px;
            margin-bottom: 8px;
            border-left: 4px solid #ff6b6b;
            transition: all 0.3s ease;
        }

        .holiday-item:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .holiday-item:last-child {
            margin-bottom: 0;
        }

        .holiday-info {
            display: flex;
            gap: 20px;
            align-items: center;
            flex: 1;
        }

        .holiday-date {
            font-weight: 600;
            color: #333;
            min-width: 120px;
        }

        .holiday-name {
            color: #666;
            flex: 1;
        }

        .holiday-type {
            background: #ff6b6b;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            min-width: 60px;
            text-align: center;
        }

        .holiday-type.workday {
            background: #66bb6a;
        }

        .holiday-type.sunday {
            background: #ff8a80;
        }

        .holiday-details {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .holiday-working-hours {
            background: #e3f2fd;
            color: #1565c0;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .holiday-source {
            background: #4fc3f7;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .holiday-summary {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 15px;
            margin-top: 15px;
        }

        .holiday-summary-stats {
            display: flex;
            gap: 20px;
            justify-content: space-around;
            text-align: center;
        }

        .holiday-stat {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .holiday-stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
        }

        .holiday-stat-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .empty-holiday-message {
            text-align: center;
            padding: 30px 20px;
            color: #666;
        }

        .empty-holiday-message i {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 15px;
        }

        .drag-drop-instructions {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #1565c0;
        }

        .drag-drop-instructions h5 {
            margin-top: 0;
            color: #0d47a1;
        }

        .drag-drop-instructions ul {
            margin: 10px 0;
            padding-left: 20px;
        }

        .drag-drop-instructions li {
            margin-bottom: 5px;
        }

        .sync-button {
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-left: auto;
        }

        .sync-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4);
        }

        .sync-button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        @media (max-width: 768px) {
            .filter-section {
                flex-direction: column;
                align-items: stretch;
            }

            .holiday-form {
                flex-direction: column;
                align-items: stretch;
            }

            .timeline-header-cell,
            .timeline-cell {
                min-width: 80px;
            }
        }

        .timeline-cell.workday::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, rgba(0, 0, 0, 0.1) 50%, transparent 100%);
        }

        /* Holiday Overlay Styles untuk Visualization Div */
        .holiday-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 107, 107, 0.15);
            border: 2px solid #ff6b6b;
            border-radius: 8px;
            pointer-events: none;
            z-index: 10;
        }

        .holiday-overlay.sunday {
            background: rgba(255, 138, 128, 0.15);
            border-color: #ff8a80;
        }

        .holiday-overlay.national-holiday {
            background: rgba(211, 50, 47, 0.15);
            border-color: #d32f2f;
        }

        /* Custom overlay untuk tanggal 26 Agustus */
        .holiday-overlay.special-date-26-august {
            background: rgba(255, 23, 68, 0.2);
            border-color: #ff1744;
            border-width: 3px;
        }

        /* Custom overlay untuk tanggal 27 Agustus */
        .holiday-overlay.special-date-27-august {
            background: rgba(211, 50, 47, 0.25);
            border-color: #d32f2f;
            border-width: 3px;
        }

        .holiday-overlay.workday {
            background: rgba(102, 187, 106, 0.15);
            border-color: #66bb6a;
        }

        .holiday-label {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .holiday-overlay.holiday .holiday-label {
            background: rgba(255, 107, 107, 0.9);
            color: white;
        }

        .holiday-overlay.sunday .holiday-label {
            background: rgba(255, 138, 128, 0.9);
            color: white;
        }

        .holiday-overlay.national-holiday .holiday-label {
            background: rgba(211, 50, 47, 0.9);
            color: white;
        }

        .holiday-overlay.special-date-26-august .holiday-label {
            background: rgba(255, 23, 68, 0.95);
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .holiday-overlay.special-date-27-august .holiday-label {
            background: rgba(211, 50, 47, 0.95);
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .holiday-overlay.workday .holiday-label {
            background: rgba(102, 187, 106, 0.9);
            color: white;
        }

        /* Animation untuk holiday overlay */
        .holiday-overlay {
            animation: holidayPulse 2s ease-in-out infinite;
        }

        /* Styling untuk item pelumasan maintenance */
        .vis-item.lubrication-maintenance {
            background-color: #ff9800 !important;
            color: white !important;
            font-weight: bold !important;
            border-radius: 8px !important;
            border: 2px solid #f57c00 !important;
        }

        .vis-item.lubrication-maintenance:hover {
            background-color: #f57c00 !important;
            transform: scale(1.02);
            box-shadow: 0 4px 16px rgba(255, 152, 0, 0.4);
        }

        .lubrication-title {
            font-weight: 600;
            font-size: 0.9rem;
        }

        @keyframes  holidayPulse {

            0%,
            100% {
                opacity: 0.8;
            }

            50% {
                opacity: 1;
            }
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
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

                    <!-- View Toggle -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="btn-group mb-3" role="group" aria-label="View Toggle">
                                <button type="button" class="btn btn-info">
                                    <i class="mdi mdi-chart-gantt"></i> Timeline View
                                </button>
                                <a href="<?php echo e(route('process.plan-first-table')); ?>" class="btn btn-outline-info">
                                    <i class="mdi mdi-widgets"></i> PLANNING CETAK
                                </a>
                                <a href="<?php echo e(route('process.plan-first-table-plong')); ?>" class="btn btn-outline-info">
                                    <i class="mdi mdi-widgets"></i> PLANNING PLONG
                                </a>
                                <a href="<?php echo e(route('process.plan-first-table-glueing')); ?>" class="btn btn-outline-info">
                                    <i class="mdi mdi-widgets"></i> PLANNING GLUEING
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Department</label>
                                <select class="form-control" id="department-filter">
                                    <option value="">All Departments</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Filter Active Machines</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="active-machines-filter" checked>
                                    <label class="form-check-label" for="active-machines-filter">
                                        Show Active Machines Only
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label>
                            <div class="form-group">
                                <button type="button" class="btn btn-outline-info" id="manage-active-machines">
                                    <i class="mdi mdi-gauge"></i> Manage Active Machines
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline View Controls -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center">
                                        <label class="mb-0 mr-3 font-weight-bold text-muted">Timeline View:</label>
                                        <div class="btn-group" role="group" aria-label="Timeline View Controls">
                                            <button type="button" class="btn btn-outline-info" id="view-day"
                                                data-view="day">
                                                <i class="mdi mdi-calendar-today"></i> Day
                                            </button>
                                            <button type="button" class="btn btn-outline-info" id="view-week"
                                                data-view="week">
                                                <i class="mdi mdi-calendar-week"></i> Week
                                            </button>
                                            <button type="button" class="btn btn-outline-info" id="view-month"
                                                data-view="month">
                                                <i class="mdi mdi-calendar-month"></i> Month
                                            </button>
                                        </div>
                                        <div class="ml-auto d-flex align-items-center">
                                            <button type="button" class="btn btn-outline-warning btn-sm mr-2"
                                                id="lubrication-maintenance-btn" style="display: none;">
                                                <i class="mdi mdi-oil"></i> Pelumasan Maintenance
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                                id="focus-today">
                                                <i class="mdi mdi-crosshairs-gps"></i> Focus Today
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div id="visualization"></div>

                    <div class="holiday-list" id="holiday-list" style="margin-bottom: 20px;">
                        <!-- Daftar hari libur akan ditampilkan di sini -->
                    </div>

                    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 99999;"></div>
                    <div id="spinner" class="spinner-overlay" style="display:none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <!-- Modal Detail Plan -->
                    <div class="modal fade" id="planDetailModal" tabindex="-1" role="dialog"
                        aria-labelledby="planDetailModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="planDetailModalLabel">Detail Plan</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-sm table-bordered">
                                        <tr class="thead-light">
                                            <th>SO</th>
                                            <td id="modal-so"></td>
                                        </tr>
                                        <tr class="thead-light">
                                            <th>WO</th>
                                            <td id="modal-wo"></td>
                                        </tr>
                                        <tr class="thead-light">
                                            <th>Item</th>
                                            <td id="modal-item"></td>
                                        </tr>
                                        <tr class="thead-light">
                                            <th>Qty</th>
                                            <td id="modal-qty"></td>
                                        </tr>
                                        <tr class="thead-light">
                                            <th>Start</th>
                                            <td id="modal-start"></td>
                                        </tr>
                                        <tr class="thead-light">
                                            <th>End</th>
                                            <td id="modal-end"></td>
                                        </tr>
                                        <tr class="thead-light">
                                            <th>Mesin</th>
                                            <td id="modal-mesin"></td>
                                        </tr>
                                        <tr class="thead-light">
                                            <th>Status</th>
                                            <td id="modal-status"></td>
                                        </tr>
                                        <tr class="thead-light">
                                            <th>Process</th>
                                            <td id="modal-process"></td>
                                        </tr>
                                        <tr class="thead-light">
                                            <th>BOM</th>
                                            <td>
                                                <button type="button" class="btn btn-info btn-sm" id="view-bom-btn">
                                                    <i class="mdi mdi-file-tree"></i> View BOM & Materials
                                                </button>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Manage Active Machines -->
                    <div class="modal fade" id="manageActiveMachinesModal" tabindex="-1" role="dialog"
                        aria-labelledby="manageActiveMachinesModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="manageActiveMachinesModalLabel">Manage Active Machines
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Available Machines</h6>
                                            <div id="available-machines-list"
                                                style="max-height: 400px; overflow-y: auto;">
                                                <!-- Available machines will be loaded here -->
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Active Machines</h6>
                                            <div id="active-machines-list" style="max-height: 400px; overflow-y: auto;">
                                                <!-- Active machines will be loaded here -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="save-active-machines">Save
                                        Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal BOM -->
    <div class="modal fade" id="bomModal" tabindex="-1" role="dialog" aria-labelledby="bomModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bomModalLabel">Bill of Materials (BOM) & Raw Materials</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="bom-loading" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p>Loading BOM data...</p>
                    </div>

                    <div id="bom-content" style="display: none;">
                        <!-- Select BOM Dropdown -->
                        <div class="row mb-3" id="bom-selector" style="display: none;">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-info">
                                        <h6 class="mb-0" style="color: white;">
                                            <i class="mdi mdi-format-list-bulleted"></i> Select BOM Version
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        
                                        <label for="bom-dropdown"><strong>Available BOM Versions for this
                                                Product:</strong></label>
                                        <select class="form-control" id="bom-dropdown">
                                            <option value="">-- Select BOM Version --</option>
                                        </select>
                                        <small class="form-text text-muted">
                                            <i class="mdi mdi-information"></i>
                                            Choose a BOM version to view the list of components and raw materials needed
                                            to produce this product.
                                        </small>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6><i class="mdi mdi-package-variant"></i> Finished Product Information</h6>
                                <table class="table table-sm table-bordered">
                                    <tr>
                                        <th>Product Code:</th>
                                        <td id="bom-material-code"></td>
                                    </tr>
                                    <tr>
                                        <th>Available BOMs:</th>
                                        <td id="bom-formula"></td>
                                    </tr>
                                    <tr>
                                        <th>Total Components:</th>
                                        <td id="bom-total-items"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="mdi mdi-cogs"></i> BOM Information</h6>
                                <div id="bom-processes" class="mb-2"></div>
                                <small class="text-muted">BOM details and processes</small>
                            </div>
                        </div>

                        <h6><i class="mdi mdi-format-list-bulleted"></i> BOM Components & Raw Materials</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="bom-materials-table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Component Code</th>
                                        <th style="white-space: nowrap">Component Name</th>
                                        <th>Required Qty</th>
                                        <th>Unit</th>
                                        <th>Stock Status</th>
                                        <th>Available Stock</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody id="bom-materials-body">
                                    <!-- BOM materials will be loaded here -->
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="mdi mdi-information"></i>
                            <strong>Note:</strong> Stock checking feature akan diimplementasikan nanti.
                            Saat ini hanya menampilkan daftar komponen dan bahan baku yang dibutuhkan dari database
                            <code>masterbomd</code> untuk BOM yang dipilih.
                        </div>
                    </div>

                    <div id="bom-not-found" style="display: none;">
                        <div class="alert alert-warning">
                            <h6><i class="mdi mdi-alert"></i> BOM Data Not Found</h6>
                            <p>No BOM data found for this material in database.</p>
                            <div id="debug-info"></div>
                            <div id="suggested-tables"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="mdi mdi-close"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Lubrication Maintenance -->
    <div class="modal fade" id="lubricationMaintenanceModal" tabindex="-1" role="dialog"
        aria-labelledby="lubricationMaintenanceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="lubricationMaintenanceModalLabel">
                        <i class="mdi mdi-oil"></i> Pelumasan Maintenance
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Machine Selection -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="lubrication-machine-select"><strong>Pilih Mesin:</strong></label>
                                <select class="form-control" id="lubrication-machine-select">
                                    <option value="">-- Pilih Mesin --</option>
                                </select>
                                <small class="form-text text-muted">
                                    <i class="mdi mdi-information"></i>
                                    Pilih mesin untuk melihat jadwal pelumasan maintenance
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-warning btn-block" id="load-lubrication-data">
                                    <i class="mdi mdi-refresh"></i> Load Data
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div id="lubrication-loading" class="text-center" style="display: none;">
                        <div class="spinner-border text-warning" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p>Loading data pelumasan...</p>
                    </div>

                    <!-- Content -->
                    <div id="lubrication-content" style="display: none;">
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="mdi mdi-information"></i> Informasi Mesin</h6>
                                    <p class="mb-0">Mesin: <strong id="lubrication-machine-name">-</strong></p>
                                    <p class="mb-0">Total Jadwal: <strong id="lubrication-total-count">0</strong> jadwal
                                    </p>
                                </div>
                            </div>
                        </div>

                        <h6><i class="mdi mdi-calendar-clock"></i> Jadwal Pelumasan Maintenance</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="lubrication-table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Select</th>
                                        <th>Nomor Jadwal</th>
                                        <th>Tanggal</th>
                                        <th>Informasi Tugas</th>
                                        
                                        <th>Shift</th>
                                        <th>Shift</th>
                                        <th>Status</th>
                                        
                                    </tr>
                                </thead>
                                <tbody id="lubrication-tbody">
                                    <!-- Lubrication data will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-success" id="add-selected-to-plan" disabled>
                                <i class="mdi mdi-plus-circle"></i> Add Selected to Plan
                            </button>
                            <span class="ml-2 text-muted" id="selected-count">0 item selected</span>
                        </div>
                    </div>

                    <!-- Not Found State -->
                    <div id="lubrication-not-found" style="display: none;">
                        <div class="alert alert-warning">
                            <h6><i class="mdi mdi-alert"></i> Data Tidak Ditemukan</h6>
                            <p>Tidak ada jadwal pelumasan maintenance untuk mesin yang dipilih.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="mdi mdi-close"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add Lubrication from Timeline -->
    <div class="modal fade" id="addLubricationFromTimelineModal" tabindex="-1" role="dialog"
        aria-labelledby="addLubricationFromTimelineModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="addLubricationFromTimelineModalLabel">
                        <i class="mdi mdi-oil"></i> Add Pelumasan Maintenance ke Timeline
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Timeline Info -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6><i class="mdi mdi-information"></i> Informasi Timeline</h6>
                                <p class="mb-0">Tanggal: <strong id="timeline-date-info">-</strong></p>
                                <p class="mb-0">Mesin: <strong id="timeline-machine-info">-</strong></p>
                                <p class="mb-0">Shift: <strong id="timeline-shift-info">-</strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- Shift Selection -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="timeline-shift-select"><strong>Pilih Shift:</strong></label>
                                <select class="form-control" id="timeline-shift-select">
                                    <option value="1">Shift 1 (08:00 - 16:00)</option>
                                    <option value="2">Shift 2 (16:00 - 00:00)</option>
                                    <option value="3">Shift 3 (00:00 - 08:00)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="timeline-start-time"><strong>Jam Mulai:</strong></label>
                                <input type="time" class="form-control" id="timeline-start-time" value="08:00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="timeline-end-time"><strong>Jam Selesai:</strong></label>
                                <input type="time" class="form-control" id="timeline-end-time" value="16:00">
                            </div>
                        </div>
                    </div>

                    <!-- Lubrication Data -->
                    <h6><i class="mdi mdi-calendar-clock"></i> Data Pelumasan Maintenance</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="timeline-lubrication-table">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Select</th>
                                    <th>Nomor Jadwal</th>
                                    <th>Tanggal</th>
                                    <th>Informasi Tugas</th>
                                    
                                    <th>Mesin</th>
                                    <th>Shift</th>
                                    <th>Status</th>
                                    
                                </tr>
                            </thead>
                            <tbody id="timeline-lubrication-tbody">
                                <!-- Lubrication data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-success" id="add-timeline-lubrication" disabled>
                            <i class="mdi mdi-plus-circle"></i> Add Selected to Timeline
                        </button>
                        <span class="ml-2 text-muted" id="timeline-selected-count">0 item selected</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="mdi mdi-close"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script src="https://unpkg.com/vis-timeline@latest/standalone/umd/vis-timeline-graph2d.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script>
        function showToast(msg) {
            const toastId = 'toast-' + Date.now();
            const toast = document.createElement('div');
            toast.className = 'toast show bg-primary text-white';
            toast.id = toastId;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            toast.style.minWidth = '200px';
            toast.innerHTML = `<div class="toast-body">${msg}</div>`;
            document.getElementById('toast-container').appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('show');
                toast.remove();
            }, 2500);
        }

        let allGroups = [];
        let allPlans = [];
        let timeline = null;
        let groupsDS = null;
        let itemsDS = null;
        let activeMachineCodes = [];
        let showActiveMachinesOnly = true;
        let currentTimelineView = 'week'; // Default view
        let currentLubricationData = []; // Store current lubrication data

        function formatDateLocal(val) {
            if (!val) return '-';
            let d = (val instanceof Date) ? val : new Date(val);
            // Jika string tanpa Z/+07:00, paksa parse sebagai lokal
            if (!(val instanceof Date) && typeof val === 'string' && val.length === 19 && val[10] === 'T') {
                d = new Date(val.replace('T', ' '));
            }
            if (isNaN(d)) return '-';
            // Format ke YYYY-MM-DD HH:mm:ss lokal
            return d.getFullYear() + '-' +
                String(d.getMonth() + 1).padStart(2, '0') + '-' +
                String(d.getDate()).padStart(2, '0') + ' ' +
                String(d.getHours()).padStart(2, '0') + ':' +
                String(d.getMinutes()).padStart(2, '0') + ':' +
                String(d.getSeconds()).padStart(2, '0');
        }

        function renderTimeline(selectedDept = '') {
            let mappedGroups = allGroups.map(m => ({
                id: m.Code,
                content: `<span class=\"machine-label\">${m.Description}</span><br><span class='text-muted'>${m.Department || ''}</span>`,
                dept: m.Department,
                raw: m
            }));

            if (selectedDept) {
                mappedGroups = mappedGroups.filter(g => g.dept === selectedDept);
            }
            if (showActiveMachinesOnly && activeMachineCodes.length > 0) {
                mappedGroups = mappedGroups.filter(g => activeMachineCodes.includes(g.id));
            }

            groupsDS = new vis.DataSet(mappedGroups.map(g => ({
                id: g.id,
                content: g.content,
                dept: g.dept
            })));

            const groupIds = mappedGroups.map(g => g.id);
            itemsDS = new vis.DataSet(allPlans.filter(p => groupIds.includes(p.code_machine)).map(p => ({
                id: p.id,
                group: p.code_machine,
                content: `<span class='plan-title'>${p.so_docno} | ${p.code_item} | ${p.quantity}</span>`,
                start: p.start_jam,
                end: p.end_jam,
                className: p.flag_status ? 'status-' + p.flag_status.toLowerCase().replace(/\s/g, '-') : ''
            })));

            // setelah itemsDS dibuat
            const bgItems = [];

            // Fetch holiday data dari controller
            fetchHolidayBackgrounds(bgItems).then(() => {
                // gabungkan setelah fetch selesai
                itemsDS.add(bgItems);
            }).catch(error => {
                console.error('Error fetching holiday data:', error);
                // Jika fetch gagal, tambahkan fallback data
                addFallbackHolidays(bgItems);
                itemsDS.add(bgItems);
            });

            const options = {
                // zoomKey: 'ctrlKey',
                stack: false,
                editable: {
                    updateTime: true,
                    updateGroup: true,
                    remove: false,
                    add: false,
                    overrideItems: false,
                    updateTime: true,
                    updateGroup: true
                },
                margin: {
                    item: 10,
                    axis: 5
                },
                orientation: 'top',
                // Auto focus ke hari ini
                start: new Date(),
                end: new Date(Date.now() + 24 * 60 * 60 * 1000), // 24 jam ke depan
                onMove: function(item, callback) {
                    const groupId = item.group;
                    const thisId = item.id;
                    const eventsOnTarget = itemsDS.get({
                        filter: ev => ev.group === groupId && ev.id !== thisId
                    });

                    // Find the most recent event that ends before or at the dropped position
                    let prevEvent = null;
                    let prevEnd = null;
                    eventsOnTarget.forEach(ev => {
                        const evEnd = new Date(ev.end);
                        if (evEnd <= new Date(item.start)) {
                            if (!prevEnd || evEnd > prevEnd) {
                                prevEnd = evEnd;
                                prevEvent = ev;
                            }
                        }
                    });

                    // Only snap to previous item if there's an actual overlap
                    // Check if the dropped item would overlap with any existing item
                    let hasOverlap = false;
                    eventsOnTarget.forEach(ev => {
                        const evStart = new Date(ev.start);
                        const evEnd = new Date(ev.end);
                        const itemStart = new Date(item.start);

                        // Calculate item end based on plan duration
                        const plan = allPlans.find(p => p.id == item.id);
                        let itemEnd = itemStart;
                        if (plan) {
                            const end = new Date(plan.end_jam);
                            const origStart = new Date(plan.start_jam);
                            const dur = end - origStart;
                            itemEnd = new Date(itemStart.getTime() + dur);
                        }

                        // Check for overlap
                        if (itemStart < evEnd && itemEnd > evStart) {
                            hasOverlap = true;
                        }
                    });

                    // Only snap to previous item if there's an overlap
                    if (hasOverlap && prevEnd) {
                        item.start = new Date(prevEnd);
                    }

                    const plan = allPlans.find(p => p.id == item.id);
                    if (plan) {
                        const start = new Date(item.start);
                        const end = new Date(plan.end_jam);
                        const origStart = new Date(plan.start_jam);
                        const dur = end - origStart;
                        item.end = new Date(start.getTime() + dur);
                    }
                    const mesin = groupsDS.get(item.group).content.replace(/<[^>]+>/g, '');
                    showToast(`Plan dipindah ke mesin ${mesin} mulai ${new Date(item.start).toLocaleString()}`);
                    callback(item); // simpan perubahan
                },
                groupOrder: 'content',
                tooltip: {
                    followMouse: false
                }
            };

            const container = document.getElementById('visualization');
            if (timeline) timeline.destroy();
            timeline = new vis.Timeline(container, itemsDS, groupsDS, options);

            // Auto focus ke plan terdekat setelah timeline selesai render
            setTimeout(() => {
                focusToNearestPlan();
            }, 1000);

            timeline.on('doubleClick', function(props) {
                if (!props || !props.item) return;
                const id = props.item;
                const plan = allPlans.find(p => p.id == id);
                const itemData = itemsDS.get(id);
                if (!plan) return;

                window.currentPlanData = plan;

                document.getElementById('modal-so').textContent = plan.so_docno || '-';
                document.getElementById('modal-wo').textContent = plan.wo_docno || '-';
                document.getElementById('modal-item').textContent = plan.code_item || '-';
                document.getElementById('modal-qty').textContent = plan.quantity || '-';
                document.getElementById('modal-start').textContent = formatDateLocal(itemData && itemData.start ?
                    itemData.start : plan.start_jam);
                document.getElementById('modal-end').textContent = formatDateLocal(itemData && itemData.end ?
                    itemData.end : plan.end_jam);
                const mesin = allGroups.find(g => g.Code == plan.Code);
                document.getElementById('modal-mesin').textContent = mesin ? mesin.Description : plan.code_machine;
                document.getElementById('modal-status').textContent = plan.flag_status || '-';
                document.getElementById('modal-process').textContent = plan.process || '-';
                $('#planDetailModal').modal('show');

            });

            // Event untuk single click untuk highlight item
            timeline.on('click', function(props) {
                if (!props || !props.item) return;

                // Hapus class active dari semua item
                document.querySelectorAll('.vis-item').forEach(item => {
                    item.classList.remove('active');
                });

                // Tambah class active ke item yang diklik
                const clickedItem = document.querySelector(`[data-id="${props.item}"]`);
                if (clickedItem) {
                    clickedItem.classList.add('active');
                }
            });

            // Event untuk double click pada timeline untuk add pelumasan
            timeline.on('doubleClick', function(props) {
                if (!props || !props.item) {
                    // Double click pada area kosong timeline
                    const clickedDate = timeline.getEventProperties(props.event).time;
                    const clickedGroup = timeline.getEventProperties(props.event).group;

                    if (clickedDate && clickedGroup) {
                        console.log('🖱️ Double click on timeline - Date:', clickedDate, 'Group:', clickedGroup);
                        openAddLubricationModal(clickedDate, clickedGroup);
                    }
                } else {
                    // Double click pada item existing (existing behavior)
                    const id = props.item;
                    const plan = allPlans.find(p => p.id == id);
                    const itemData = itemsDS.get(id);
                    if (!plan) return;

                    window.currentPlanData = plan;

                    document.getElementById('modal-so').textContent = plan.so_docno || '-';
                    document.getElementById('modal-wo').textContent = plan.wo_docno || '-';
                    document.getElementById('modal-item').textContent = plan.code_item || '-';
                    document.getElementById('modal-qty').textContent = plan.quantity || '-';
                    document.getElementById('modal-start').textContent = formatDateLocal(itemData && itemData
                        .start ?
                        itemData.start : plan.start_jam);
                    document.getElementById('modal-end').textContent = formatDateLocal(itemData && itemData.end ?
                        itemData.end : plan.end_jam);
                    const mesin = allGroups.find(g => g.Code == plan.Code);
                    document.getElementById('modal-mesin').textContent = mesin ? mesin.Description : plan
                        .code_machine;
                    document.getElementById('modal-status').textContent = plan.flag_status || '-';
                    document.getElementById('modal-process').textContent = plan.process || '-';
                    $('#planDetailModal').modal('show');
                }
            });

            // Event untuk mousedown untuk visual feedback
            timeline.on('mousedown', function(props) {
                if (!props || !props.item) return;

                const clickedItem = document.querySelector(`[data-id="${props.item}"]`);
                if (clickedItem) {
                    clickedItem.style.backgroundColor = '#9cfa4a';
                    clickedItem.style.color = 'white';
                    clickedItem.style.fontWeight = 'bold';
                }
            });

            // Event untuk mouseup untuk mempertahankan warna
            timeline.on('mouseup', function(props) {
                if (!props || !props.item) return;

                const clickedItem = document.querySelector(`[data-id="${props.item}"]`);
                if (clickedItem && clickedItem.classList.contains('active')) {
                    // Jika item sudah aktif, pertahankan warna hijau
                    clickedItem.style.backgroundColor = '#9cfa4a';
                    clickedItem.style.color = 'white';
                    clickedItem.style.fontWeight = 'bold';
                }
            });
        }

        // Function untuk auto focus ke plan terdekat
        function focusToNearestPlan() {
            if (!timeline || !allPlans || allPlans.length === 0) {
                console.log('⚠️ Timeline atau plans tidak tersedia untuk auto focus');
                return;
            }

            console.log('🎯 Mencari plan terdekat untuk auto focus...');

            const now = new Date();
            let nearestPlan = null;
            let minTimeDiff = Infinity;

            // Cari plan terdekat dengan waktu sekarang
            allPlans.forEach(plan => {
                const planStart = new Date(plan.start_jam);
                const planEnd = new Date(plan.end_jam);

                // Jika plan sedang berlangsung (now berada di antara start dan end)
                if (now >= planStart && now <= planEnd) {
                    nearestPlan = plan;
                    minTimeDiff = 0;
                    return;
                }

                // Jika plan sudah selesai, cari yang paling baru selesai
                if (now > planEnd) {
                    const timeDiff = now - planEnd;
                    if (timeDiff < minTimeDiff) {
                        minTimeDiff = timeDiff;
                        nearestPlan = plan;
                    }
                }

                // Jika plan belum dimulai, cari yang paling dekat akan dimulai
                if (now < planStart) {
                    const timeDiff = planStart - now;
                    if (timeDiff < minTimeDiff) {
                        minTimeDiff = timeDiff;
                        nearestPlan = plan;
                    }
                }
            });

            if (nearestPlan) {
                console.log(`✅ Plan terdekat ditemukan: ${nearestPlan.so_docno} | ${nearestPlan.code_item}`);
                console.log(`   Start: ${nearestPlan.start_jam}`);
                console.log(`   End: ${nearestPlan.end_jam}`);

                // Focus timeline ke plan terdekat
                const planStart = new Date(nearestPlan.start_jam);
                const planEnd = new Date(nearestPlan.end_jam);

                // Tambah margin 2 jam sebelum dan sesudah plan
                const focusStart = new Date(planStart.getTime() - 2 * 60 * 60 * 1000);
                const focusEnd = new Date(planEnd.getTime() + 2 * 60 * 60 * 1000);

                timeline.setWindow(focusStart, focusEnd);

                // Highlight plan terdekat dengan animasi
                highlightNearestPlan(nearestPlan.id);

            } else {
                console.log('⚠️ Tidak ada plan yang ditemukan, focus ke hari ini');
                // Fallback: focus ke hari ini
                const todayStart = new Date();
                todayStart.setHours(0, 0, 0, 0);
                const todayEnd = new Date();
                todayEnd.setHours(23, 59, 59, 999);
                timeline.setWindow(todayStart, todayEnd);
            }
        }

        function highlightNearestPlan(planId) {
            const planElement = document.querySelector(`[data-id="${planId}"]`);
            if (planElement) {
                planElement.classList.add('nearest-plan-highlight');
                setTimeout(() => {
                    planElement.classList.remove('nearest-plan-highlight');
                }, 3000);
            }
        }

        function focusToToday() {
            if (!timeline) {
                console.log('⚠️ Timeline tidak tersedia');
                return;
            }

            const todayStart = new Date();
            todayStart.setHours(0, 0, 0, 0);

            const todayEnd = new Date();
            todayEnd.setHours(23, 59, 59, 999);

            timeline.setWindow(todayStart, todayEnd);

            showToast('Timeline focused to today');
        }

        function changeTimelineView(view) {
            if (!timeline) {
                console.log('⚠️ Timeline tidak tersedia');
                return;
            }
            currentTimelineView = view;
            document.querySelectorAll('[data-view]').forEach(btn => {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            });
            document.querySelector(`[data-view="${view}"]`).classList.remove('btn-outline-primary');
            document.querySelector(`[data-view="${view}"]`).classList.add('btn-primary');

            const now = new Date();
            let start, end;
            switch (view) {
                case 'day':
                    start = new Date(now);
                    start.setHours(0, 0, 0, 0);
                    end = new Date(now);
                    end.setHours(23, 59, 59, 999);
                    break;
                case 'week':
                    start = new Date(now);
                    start.setDate(now.getDate() - now.getDay()); // Start of week (Sunday)
                    start.setHours(0, 0, 0, 0);
                    end = new Date(start);
                    end.setDate(start.getDate() + 6); // End of week (Saturday)
                    end.setHours(23, 59, 59, 999);
                    break;
                case 'month':
                    start = new Date(now.getFullYear(), now.getMonth(), 1); // First day of month
                    start.setHours(0, 0, 0, 0);
                    end = new Date(now.getFullYear(), now.getMonth() + 1, 0); // Last day of month
                    end.setHours(23, 59, 59, 999);
                    break;
                default:
                    start = new Date(now);
                    start.setHours(0, 0, 0, 0);
                    end = new Date(now);
                    end.setHours(23, 59, 59, 999);
            }

            // Set timeline window
            timeline.setWindow(start, end);

            // Update timeline options based on view
            let newOptions = {};

            switch (view) {
                case 'day':
                    newOptions = {
                        zoomMin: 60 * 60 * 1000, // 1 hour minimum zoom
                        zoomMax: 24 * 60 * 60 * 1000, // 1 day maximum zoom
                        format: {
                            minorLabels: {
                                hour: 'HH:mm'
                            },
                            majorLabels: {
                                hour: 'dddd D MMMM'
                            }
                        }
                    };
                    break;
                case 'week':
                    newOptions = {
                        zoomMin: 24 * 60 * 60 * 1000, // 1 day minimum zoom
                        zoomMax: 7 * 24 * 60 * 60 * 1000, // 1 week maximum zoom
                        format: {
                            minorLabels: {
                                day: 'dddd'
                            },
                            majorLabels: {
                                day: 'D MMMM'
                            }
                        }
                    };
                    break;
                case 'month':
                    newOptions = {
                        zoomMin: 24 * 60 * 60 * 1000, // 1 day minimum zoom
                        zoomMax: 31 * 24 * 60 * 60 * 1000, // 1 month maximum zoom
                        format: {
                            minorLabels: {
                                day: 'D'
                            },
                            majorLabels: {
                                day: 'MMMM YYYY'
                            }
                        }
                    };
                    break;
            }

            // Apply new options to timeline
            if (Object.keys(newOptions).length > 0) {
                timeline.setOptions(newOptions);
            }

            showToast(`Timeline view changed to ${view} view`);
        }

        // Function untuk fetch holiday backgrounds dari controller
        async function fetchHolidayBackgrounds(bgItems) {
            try {
                console.log('🔄 Fetching holiday data dari controller...');

                const response = await fetch("<?php echo e(route('process.holiday-data')); ?>", {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success && data.holidays) {
                    console.log(`✅ Holiday data berhasil di-fetch: ${data.holidays.length} holidays`);

                    // Helper: rentang 1 hari penuh
                    function oneDayRange(isoDate) {
                        const start = new Date(isoDate + 'T00:00:00');
                        const end = new Date(start);
                        end.setDate(end.getDate() + 1);
                        return {
                            start,
                            end
                        };
                    }

                    // Process setiap holiday
                    data.holidays.forEach(holiday => {
                        const {
                            start,
                            end
                        } = oneDayRange(holiday.date);

                        // Tentukan className berdasarkan tipe holiday
                        let className = 'bg-holiday';
                        if (holiday.type === 'sunday') {
                            className = 'bg-sunday';
                        } else if (holiday.type === 'saturday') {
                            className = 'bg-saturday';
                        } else if (holiday.type === 'national_holiday') {
                            className = 'bg-national-holiday';
                        } else if (holiday.type === 'company_holiday') {
                            className = 'bg-company-holiday';
                        }

                        bgItems.push({
                            id: `bg-holiday-${holiday.date}`,
                            type: 'background',
                            start,
                            end,
                            className,
                            title: holiday.name || holiday.type // Tooltip untuk hover
                        });
                    });

                    console.log(`📅 Background items created: ${bgItems.length} items`);
                } else {
                    console.warn('⚠️ No holiday data received or invalid response');
                }

            } catch (error) {
                console.error('❌ Error fetching holiday data:', error);

                // Fallback: gunakan data default jika fetch gagal
                console.log('🔄 Using fallback holiday data...');
                addFallbackHolidays(bgItems);
            }
        }

        // Function untuk fallback holiday data
        function addFallbackHolidays(bgItems) {
            // Helper: rentang 1 hari penuh
            function oneDayRange(isoDate) {
                const start = new Date(isoDate + 'T00:00:00');
                const end = new Date(start);
                end.setDate(end.getDate() + 1);
                return {
                    start,
                    end
                };
            }

            // Data fallback (contoh)
            const fallbackHolidays = [{
                    date: '2025-08-28',
                    type: 'national_holiday',
                    name: 'Hari Kemerdekaan'
                },
                {
                    date: '2025-08-31',
                    type: 'sunday',
                    name: 'Minggu'
                },
                {
                    date: '2025-09-07',
                    type: 'sunday',
                    name: 'Minggu'
                }
            ];

            fallbackHolidays.forEach(holiday => {
                const {
                    start,
                    end
                } = oneDayRange(holiday.date);

                let className = 'bg-holiday';
                if (holiday.type === 'sunday') {
                    className = 'bg-sunday';
                } else if (holiday.type === 'saturday') {
                    className = 'bg-saturday';
                } else if (holiday.type === 'national_holiday') {
                    className = 'bg-national-holiday';
                }

                bgItems.push({
                    id: `bg-holiday-${holiday.date}`,
                    type: 'background',
                    start,
                    end,
                    className,
                    title: holiday.name
                });
            });

            console.log(`📅 Fallback background items created: ${bgItems.length} items`);
        }

        // Function untuk refresh holiday backgrounds
        async function refreshHolidayBackgrounds() {
            if (!timeline) {
                console.log('⚠️ Timeline tidak tersedia');
                return;
            }

            try {
                console.log('🔄 Refreshing holiday backgrounds...');

                // Hapus background items yang ada
                const existingBgItems = itemsDS.get({
                    filter: function(item) {
                        return item.type === 'background';
                    }
                });

                if (existingBgItems.length > 0) {
                    itemsDS.remove(existingBgItems);
                    console.log(`🗑️ Removed ${existingBgItems.length} existing background items`);
                }

                // Fetch dan tambah holiday data baru
                const bgItems = [];
                await fetchHolidayBackgrounds(bgItems);

                if (bgItems.length > 0) {
                    itemsDS.add(bgItems);
                    console.log(`✅ Added ${bgItems.length} new holiday background items`);
                    showToast(`Holiday data refreshed: ${bgItems.length} holidays loaded`);
                } else {
                    console.log('⚠️ No holiday data to add');
                    showToast('No holiday data available');
                }

            } catch (error) {
                console.error('❌ Error refreshing holiday backgrounds:', error);
                showToast('Error refreshing holiday data');
            }
        }

        // Load active machines setting
        async function loadActiveMachines() {
            try {
                const response = await fetch("<?php echo e(route('settings.active-machines')); ?>");
                const data = await response.json();
                if (data.success) {
                    activeMachineCodes = data.active_codes || [];
                }
            } catch (error) {
                console.error('Error loading active machines:', error);
            }
        }

        // Load all machines for management modal
        async function loadAllMachines() {
            try {
                const response = await fetch("<?php echo e(route('settings.all-machines')); ?>");
                const data = await response.json();
                if (data.success) {
                    return data.machines;
                }
            } catch (error) {
                console.error('Error loading all machines:', error);
            }
            return [];
        }

        // Render machines in management modal
        function renderMachinesInModal(machines) {
            const availableList = document.getElementById('available-machines-list');
            const activeList = document.getElementById('active-machines-list');

            availableList.innerHTML = '';
            activeList.innerHTML = '';

            machines.forEach(machine => {
                const isActive = activeMachineCodes.includes(machine.Code);
                const listItem = document.createElement('div');
                listItem.className = 'machine-item p-2 border-bottom';
                listItem.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${machine.Description}</strong><br>
                            <small class="text-muted">${machine.Department} - ${machine.Code}</small>
                        </div>
                        <button type="button" class="btn btn-sm ${isActive ? 'btn-danger' : 'btn-success'}"
                                onclick="toggleMachineInModal('${machine.Code}')">
                            ${isActive ? 'Remove' : 'Add'}
                        </button>
                    </div>
                `;

                if (isActive) {
                    activeList.appendChild(listItem.cloneNode(true));
                } else {
                    availableList.appendChild(listItem);
                }
            });
        }

        // Toggle machine in modal
        function toggleMachineInModal(machineCode) {
            if (activeMachineCodes.includes(machineCode)) {
                activeMachineCodes = activeMachineCodes.filter(code => code !== machineCode);
            } else {
                activeMachineCodes.push(machineCode);
            }
            loadAllMachines().then(machines => renderMachinesInModal(machines));
        }

        // Save active machines
        async function saveActiveMachines() {
            try {
                const response = await fetch("<?php echo e(route('settings.set-active-machines')); ?>", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        machine_codes: activeMachineCodes
                    })
                });
                const data = await response.json();
                if (data.success) {
                    showToast('Active machines updated successfully');
                    $('#manageActiveMachinesModal').modal('hide');
                    renderTimeline(document.getElementById('department-filter').value);
                } else {
                    showToast('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error saving active machines:', error);
                showToast('Error saving active machines');
            }
        }

        // Function untuk styling kolom timeline berdasarkan tanggal
        function styleTimelineColumns() {
            console.log('🎨 Styling timeline columns...');

            const timeline = document.querySelector('.vis-timeline');
            if (!timeline) {
                console.log('❌ Timeline not found');
                return;
            }

            // Cari semua kolom grid
            const gridCells = timeline.querySelectorAll('.vis-grid.vis-odd, .vis-grid.vis-even');
            console.log(`🔍 Found ${gridCells.length} grid cells`);

            if (gridCells.length === 0) {
                console.log('❌ No grid cells found');
                return;
            }

            // Cari header tanggal untuk referensi
            const dateHeaders = document.querySelectorAll('.vis-time-axis .vis-minor, .vis-time-axis .vis-major');
            const dateInfo = [];

            dateHeaders.forEach(header => {
                const text = header.textContent || '';
                if (text.includes('Mon') || text.includes('Tue') || text.includes('Wed') ||
                    text.includes('Thu') || text.includes('Fri') || text.includes('Sat') ||
                    text.includes('Sun') || text.includes('Monday') || text.includes('Tuesday') ||
                    text.includes('Wednesday') || text.includes('Thursday') || text.includes('Friday') ||
                    text.includes('Saturday') || text.includes('Sunday')) {

                    // Cek apakah ini hari libur
                    let isHoliday = false;
                    let isWeekend = false;
                    let isSunday = false;
                    let isSaturday = false;

                    if (text.includes('Sun') || text.includes('Sunday')) {
                        isSunday = true;
                        isWeekend = true;
                    } else if (text.includes('Sat') || text.includes('Saturday')) {
                        isSaturday = true;
                        isWeekend = true;
                    }

                    // Cek tanggal libur database
                    if (text.includes('27') && (text.includes('Wed') || text.includes('Wednesday'))) {
                        isHoliday = true;
                    }

                    dateInfo.push({
                        text: text,
                        isHoliday: isHoliday,
                        isWeekend: isWeekend,
                        isSunday: isSunday,
                        isSaturday: isSaturday
                    });
                }
            });

            console.log('📅 Date info found:', dateInfo);

            // Styling kolom berdasarkan posisi
            let currentColumn = 0;
            const columnWidth = 120; // Approximate column width

            dateInfo.forEach((date, index) => {
                console.log(`🎨 Styling column ${index}: ${date.text}`);

                // Cari semua grid cells di kolom ini
                gridCells.forEach(cell => {
                    const rect = cell.getBoundingClientRect();
                    const timelineRect = timeline.getBoundingClientRect();
                    const cellColumn = Math.floor((rect.left - timelineRect.left) / columnWidth);

                    if (cellColumn === index) {
                        // Reset styling dulu
                        cell.style.background = '';
                        cell.style.borderLeft = '';

                        // Styling berdasarkan hari
                        if (date.isHoliday) {
                            // Hari libur database - Merah
                            cell.style.background = 'rgba(255, 235, 238, 0.3)';
                            cell.style.borderLeft = '3px solid #d32f2f';
                            console.log(`🔴 Styled column ${index} as holiday (red)`);
                        } else if (date.isSunday) {
                            // Hari Minggu - Merah
                            cell.style.background = 'rgba(255, 235, 238, 0.3)';
                            cell.style.borderLeft = '3px solid #d32f2f';
                            console.log(`🔴 Styled column ${index} as Sunday (red)`);
                        } else if (date.isSaturday) {
                            // Hari Sabtu - Orange
                            cell.style.background = 'rgba(255, 243, 224, 0.3)';
                            cell.style.borderLeft = '3px solid #ef6c00';
                            console.log(`🟠 Styled column ${index} as Saturday (orange)`);
                        } else {
                            // Hari kerja - Normal
                            cell.style.background = 'rgba(248, 249, 250, 0.3)';
                            cell.style.borderLeft = '3px solid #6c757d';
                            console.log(`⚪ Styled column ${index} as workday (normal)`);
                        }
                    }
                });
            });
        }


        document.addEventListener('DOMContentLoaded', function() {
            const spinner = document.getElementById('spinner');
            spinner.style.display = 'flex';

            // Initialize HolidayManager globally
            window.holidayManager = new HolidayManager();

            // Initialize timeline view buttons
            document.getElementById('view-week').classList.remove('btn-outline-primary');
            document.getElementById('view-week').classList.add('btn-primary');

            // Load active machines first
            loadActiveMachines().then(() => {
                // Fetch departments
                fetch("<?php echo e(route('departments.list')); ?>")
                    .then(r => r.json())
                    .then(data => {
                        const select = document.getElementById('department-filter');
                        data.departments.forEach(dept => {
                            const option = document.createElement('option');
                            option.value = dept;
                            option.textContent = dept;
                            select.appendChild(option);
                        });
                    });
                // Fetch mesin/resources & plan
                Promise.all([
                    fetch("<?php echo e(route('master.machine.data')); ?>").then(r => r.json()),
                    fetch("<?php echo e(route('plan.first.data')); ?>").then(r => r.json())
                ]).then(([mesinRes, planRes]) => {
                    spinner.style.display = 'none';
                    allGroups = mesinRes.data;
                    allPlans = planRes.data;
                    renderTimeline('');
                });
            });

            // Department filter event
            document.getElementById('department-filter').addEventListener('change', function() {
                renderTimeline(this.value);
            });

            // Active machines filter event
            document.getElementById('active-machines-filter').addEventListener('change', function() {
                showActiveMachinesOnly = this.checked;
                renderTimeline(document.getElementById('department-filter').value);
            });

            // Manage active machines button
            document.getElementById('manage-active-machines').addEventListener('click', function() {
                loadAllMachines().then(machines => {
                    renderMachinesInModal(machines);
                    $('#manageActiveMachinesModal').modal('show');
                });
            });

            // Save active machines button
            document.getElementById('save-active-machines').addEventListener('click', saveActiveMachines);

            // Timeline view controls event listeners
            document.getElementById('view-day').addEventListener('click', () => changeTimelineView('day'));
            document.getElementById('view-week').addEventListener('click', () => changeTimelineView('week'));
            document.getElementById('view-month').addEventListener('click', () => changeTimelineView('month'));
            document.getElementById('focus-today').addEventListener('click', focusToToday);

            // BOM button event listener
            document.addEventListener('click', function(e) {
                if (e.target && e.target.id === 'view-bom-btn') {
                    const materialCode = window.currentPlanData?.code_item;
                    const process = window.currentPlanData?.process;
                    if (materialCode) {
                        loadBomData(materialCode, process);
                    }
                }
            });

            // Lubrication Maintenance button event listener
            document.getElementById('lubrication-maintenance-btn').addEventListener('click', function() {
                openLubricationMaintenanceModal();
            });

            // Load Lubrication Data button event listener
            document.addEventListener('click', function(e) {
                if (e.target && e.target.id === 'load-lubrication-data') {
                    loadLubricationData();
                }
            });
        });

        // BOM Functions
        window.currentPlanData = null;

        function loadBomData(materialCode, process) {
            document.getElementById('bom-loading').style.display = 'block';
            document.getElementById('bom-content').style.display = 'none';
            document.getElementById('bom-not-found').style.display = 'none';

            $('#bomModal').modal('show');

            fetch(`/sipo/bom/${materialCode}/${process}`)
                .then(response => response.json())
                .then(data => {
                    console.log('ini data', data);

                    document.getElementById('bom-loading').style.display = 'none';

                    if (data.success) {
                        displayBomData(data);
                    } else {
                        displayBomNotFound(materialCode, data);
                    }
                })
                .catch(error => {
                    console.error('Error loading BOM data:', error);
                    document.getElementById('bom-loading').style.display = 'none';
                    alert('Error loading BOM data');
                });
        }

        function displayBomData(data) {
            const header = data.header;
            const details = data.details;

            // Always show BOM selector dropdown for better UX
            document.getElementById('bom-selector').style.display = 'block';

            // Populate dropdown with available BOM options
            const dropdown = document.getElementById('bom-dropdown');
            dropdown.innerHTML = '<option value="">-- Select BOM Version --</option>';
            dropdown.disabled = false;

            if (data.bom_options && data.bom_options.length > 0) {
                // Multiple BOMs available for this material
                data.bom_options.forEach((option, index) => {
                    const optionElement = document.createElement('option');
                    optionElement.value = option.formula;
                    optionElement.textContent =
                        `BOM ${index + 1}: ${option.formula} (${option.material_code || 'N/A'})`;
                    dropdown.appendChild(optionElement);
                });
            } else if (header && header.Formula) {
                // Single BOM available
                const optionElement = document.createElement('option');
                optionElement.value = header.Formula;
                optionElement.textContent = `BOM: ${header.Formula}`;
                dropdown.appendChild(optionElement);
            } else {
                // No BOM options available
                dropdown.innerHTML = '<option value="">-- No BOM Available --</option>';
                dropdown.disabled = true;
            }

            // Add change event listener
            dropdown.onchange = function() {
                const selectedFormula = this.value;
                if (selectedFormula) {
                    loadBomDetailsByFormula(selectedFormula);
                } else {
                    // Clear table if no BOM selected
                    const tbody = document.getElementById('bom-materials-body');
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                <i class="mdi mdi-information"></i> Please select a BOM version to view components and raw materials
                            </td>
                        </tr>
                    `;
                    document.getElementById('bom-total-items').textContent = '0 items';
                }
            };

            // Auto-select first option if only one BOM available
            if (data.bom_options && data.bom_options.length === 1) {
                dropdown.value = data.bom_options[0].formula;
                loadBomDetailsByFormula(data.bom_options[0].formula);
            } else if (header && header.Formula && (!data.bom_options || data.bom_options.length === 0)) {
                // If no BOM options but header has formula, auto-select it
                dropdown.value = header.Formula;
                loadBomDetailsByFormula(header.Formula);
            }

            // Display material info
            document.getElementById('bom-material-code').textContent = header.MaterialCode || 'N/A';
            document.getElementById('bom-formula').textContent = header.Formula || 'N/A';
            document.getElementById('bom-total-items').textContent = '0 items'; // Will be updated when BOM is selected

            // Display processes
            const processesDiv = document.getElementById('bom-processes');
            processesDiv.innerHTML = '';
            if (header.processes && header.processes.length > 0) {
                header.processes.forEach(process => {
                    const badge = document.createElement('span');
                    badge.className = 'badge badge-primary mr-1 mb-1';
                    badge.textContent = process;
                    processesDiv.appendChild(badge);
                });
            } else {
                processesDiv.innerHTML = '<span class="text-muted">No processes defined</span>';
            }

            // Display BOM materials table - always show selection message first
            const tbody = document.getElementById('bom-materials-body');
            if (data.bom_options && data.bom_options.length > 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            <i class="mdi mdi-information"></i> Please select a BOM version from dropdown above to view components and raw materials
                        </td>
                    </tr>
                `;
            } else if (header && header.Formula) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            <i class="mdi mdi-information"></i> Loading BOM components for: ${header.Formula}
                        </td>
                    </tr>
                `;
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center text-danger">
                            <i class="mdi mdi-alert"></i> No BOM configuration found for this product
                        </td>
                    </tr>
                `;
            }

            document.getElementById('bom-content').style.display = 'block';
            document.getElementById('bom-not-found').style.display = 'none';
        }

        function loadBomDetailsByFormula(formula) {
            console.log('=== LOAD BOM DETAILS BY FORMULA ===');
            console.log('Formula yang diterima:', formula);
            console.log('Formula type:', typeof formula);
            console.log('Formula length:', formula ? formula.length : 'null');

            const url = `/sipo/bom/details/${encodeURIComponent(formula)}`;
            console.log('URL yang dipanggil:', url);
            console.log('URL encoded:', encodeURIComponent(formula));

            // Show loading state
            const tbody = document.getElementById('bom-materials-body');
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        Loading BOM details...
                    </td>
                </tr>
            `;

            fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response ok:', response.ok);
                    console.log('Response headers:', response.headers);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Raw response data:', data);
                    console.log('data.success:', data.success);
                    console.log('data.details:', data.details);
                    console.log('data.details type:', typeof data.details);
                    console.log('data.details is array:', Array.isArray(data.details));

                    if (data.success && data.details) {
                        displayBomMaterialsTable(data.details);
                        document.getElementById('bom-total-items').textContent = (data.total_items || 0) + ' items';
                    } else {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="8" class="text-center text-danger">
                                    <i class="mdi mdi-alert"></i> Error loading BOM details: ${data.message || 'Unknown error'}
                                </td>
                            </tr>
                        `;
                        document.getElementById('bom-total-items').textContent = '0 items';
                    }
                })
                .catch(error => {
                    console.error('Error loading BOM details:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="8" class="text-center text-danger">
                                <i class="mdi mdi-alert"></i> Error loading BOM details
                            </td>
                        </tr>
                    `;
                });
        }

        function displayBomMaterialsTable(details) {
            const tbody = document.getElementById('bom-materials-body');
            tbody.innerHTML = '';

            // Check if details is valid array
            if (!details || !Array.isArray(details) || details.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            <i class="mdi mdi-information"></i> No raw materials found in masterbomd table
                        </td>
                    </tr>
                `;
                return;
            }

            details.forEach((item, index) => {
                // Check if item and stock_info exist
                if (!item) return;

                const stockStatus = getStockStatusBadge(item.stock_info || {});
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td><strong>${item.ItemCode || '-'}</strong></td>
                    <td style="white-space:nowrap;">${item.ItemName || '-'}</td>
                    <td class="text-right">${parseFloat(item.Quantity || 0).toLocaleString()}</td>
                    <td>${item.Unit || '-'}</td>
                    <td>${stockStatus}</td>
                    <td class="text-right">${(item.stock_info && item.stock_info.free_stock) ? item.stock_info.free_stock.toLocaleString() : '0'}</td>
                    <td><small class="text-muted">${item.Notes || '-'}</small></td>
                `;
                tbody.appendChild(row);
            });
        }

        function getStockStatusBadge(stockInfo) {
            // Check if stockInfo exists
            if (!stockInfo) {
                return '<span class="badge badge-secondary">N/A</span>';
            }

            // Placeholder for stock status - will be implemented later
            return '<span class="badge badge-secondary">N/A</span>';
        }

        function displayBomNotFound(materialCode, data) {
            document.getElementById('bom-not-found').style.display = 'block';
            document.getElementById('bom-content').style.display = 'none';

            // Show BOM selector with no options
            document.getElementById('bom-selector').style.display = 'block';
            const dropdown = document.getElementById('bom-dropdown');
            dropdown.innerHTML = '<option value="">-- No BOM Found --</option>';
            dropdown.disabled = true;

            // Clear table
            const tbody = document.getElementById('bom-materials-body');
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-danger">
                        <i class="mdi mdi-alert"></i> No BOM found for product: ${materialCode}
                    </td>
                </tr>
            `;

            // Display debug info if available
            const debugDiv = document.getElementById('debug-info');
            if (data.debug_info) {
                let debugHtml = `
                    <div class="alert alert-info">
                        <h6><i class="mdi mdi-bug"></i> Debug Information</h6>
                        <p><strong>Searched Material:</strong> ${data.debug_info.searched_material}</p>
                `;

                if (data.debug_info.sample_materials && data.debug_info.sample_materials.length > 0) {
                    debugHtml += `
                        <p><strong>Sample Materials (Model Query):</strong></p>
                        <ul>${data.debug_info.sample_materials.map(material => `<li>${material}</li>`).join('')}</ul>
                    `;
                }

                if (data.debug_info.sample_materials_raw && data.debug_info.sample_materials_raw.length > 0) {
                    debugHtml += `
                        <p><strong>Sample Materials (Raw Query):</strong></p>
                        <ul>${data.debug_info.sample_materials_raw.map(material => `<li>${material}</li>`).join('')}</ul>
                    `;
                }

                if (data.debug_info.raw_query_results && data.debug_info.raw_query_results.length > 0) {
                    debugHtml += `
                        <p><strong>Raw Query Results:</strong></p>
                        <pre>${JSON.stringify(data.debug_info.raw_query_results, null, 2)}</pre>
                    `;
                }

                debugHtml += '</div>';
                debugDiv.innerHTML = debugHtml;
            }

            // Display suggested tables
            const suggestedDiv = document.getElementById('suggested-tables');
            if (data.suggested_tables) {
                suggestedDiv.innerHTML = '<h6>Required Database Tables:</h6>';

                const list = document.createElement('ul');
                list.className = 'list-unstyled';

                Object.entries(data.suggested_tables).forEach(([table, description]) => {
                    const item = document.createElement('li');
                    item.innerHTML = `<strong>${table}:</strong> ${description}`;
                    list.appendChild(item);
                });

                suggestedDiv.appendChild(list);
            }
        }

        // Holiday Management System
        class HolidayManager {
            constructor() {
                this.holidays = new Map();
                this.sundays = new Set();
                this.initHolidays();
            }

            async initHolidays() {
                // Generate Sundays untuk bulan yang sedang aktif
                const now = new Date();
                const currentYear = now.getFullYear();
                const currentMonth = now.getMonth(); // 0-11

                console.log(`🎯 Initializing holidays for month: ${currentYear}-${currentMonth + 1}`);

                // Generate Sundays hanya untuk bulan ini
                this.generateSundaysForMonth(currentYear, currentMonth);

                // Fetch holidays dari database
                await this.fetchHolidaysFromDatabase();

                this.renderHolidayList();
                this.updateTimelineHolidays();

                // Tambah holiday overlays ke visualization
                setTimeout(() => {
                    this.addHolidayOverlaysToVisualization();
                    // Force update tanggal 27 Agustus sudah tidak diperlukan karena menggunakan hard-coded data
                }, 1000); // Delay sedikit untuk pastikan DOM sudah ready
            }

            async fetchHolidaysFromDatabase() {
                try {
                    console.log('🔄 Fetching holidays dari database...');

                    const response = await fetch('/sipo/process/get-holidays');
                    const result = await response.json();

                    if (result.success) {
                        console.log('✅ Holidays berhasil di-fetch:', result.data);

                        // Clear existing holidays
                        this.holidays.clear();

                        // Process database holidays
                        result.data.forEach(holiday => {
                            const dateStr = holiday.date;
                            const holidayType = this.getHolidayTypeFromDatabase(holiday.override_type);

                            this.holidays.set(dateStr, {
                                name: holiday.description || holiday.override_type,
                                type: holidayType,
                                workingHours: holiday.working_hours,
                                source: 'database'
                            });
                        });

                        // HARD CODE: Tambah tanggal 27 Agustus sebagai hari libur
                        this.holidays.set('2025-08-27', {
                            name: 'Tanggal Merah 27 Agustus',
                            type: 'holiday',
                            workingHours: '0.00',
                            source: 'hardcoded'
                        });

                        // Tambah juga untuk tahun lain agar robust
                        this.holidays.set('2024-08-27', {
                            name: 'Tanggal Merah 27 Agustus',
                            type: 'holiday',
                            workingHours: '0.00',
                            source: 'hardcoded'
                        });

                        this.holidays.set('2026-08-27', {
                            name: 'Tanggal Merah 27 Agustus',
                            type: 'holiday',
                            workingHours: '0.00',
                            source: 'hardcoded'
                        });

                        console.log('📅 Holidays processed:', this.holidays);
                        console.log('🔴 Hard-coded August 27 holiday added');
                    } else {
                        console.error('❌ Gagal fetch holidays:', result.message);
                    }
                } catch (error) {
                    console.error('❌ Error fetching holidays:', error);
                }
            }

            getHolidayTypeFromDatabase(overrideType) {
                // Map database override_type ke holiday type untuk CSS
                const typeMapping = {
                    'Hari Libur': 'holiday',
                    'Hari Kerja': 'workday',
                    'Hari Libur 1': 'holiday',
                    'Hari Kerja 1': 'workday'
                };

                return typeMapping[overrideType] || 'holiday';
            }

            generateSundays(year) {
                const startDate = new Date(year, 0, 1);
                const endDate = new Date(year, 11, 31);

                for (let date = new Date(startDate); date <= endDate; date.setDate(date.getDate() + 1)) {
                    if (date.getDay() === 0) { // Sunday
                        const dateStr = date.toISOString().split('T')[0];
                        this.sundays.add(dateStr);
                    }
                }
            }

            generateSundaysForMonth(year, month) {
                // Clear existing Sundays
                this.sundays.clear();

                // Generate Sundays hanya untuk bulan yang ditampilkan
                const startDate = new Date(year, month, 1);
                const endDate = new Date(year, month + 1, 0); // Last day of month

                console.log(
                    `📅 Generating Sundays for ${year}-${month + 1}: ${startDate.toDateString()} to ${endDate.toDateString()}`
                );

                for (let date = new Date(startDate); date <= endDate; date.setDate(date.getDate() + 1)) {
                    if (date.getDay() === 0) { // Sunday
                        const dateStr = date.toISOString().split('T')[0];
                        this.sundays.add(dateStr);
                        console.log(`   🟡 Sunday found: ${dateStr}`);
                    }
                }

                console.log(`✅ Total Sundays for month: ${this.sundays.size}`);
            }

            isHoliday(date) {
                const dateStr = date.toISOString().split('T')[0];
                return this.holidays.has(dateStr) || this.sundays.has(dateStr);
            }

            getHolidayType(date) {
                const dateStr = date.toISOString().split('T')[0];

                // Check if it's Sunday
                if (this.sundays.has(dateStr)) {
                    return 'holiday';
                }

                // Check if it's holiday from database
                const holiday = this.holidays.get(dateStr);
                if (holiday) {
                    return 'holiday';
                }

                // Check if it's special date 27 August
                const day = date.getDate();
                const month = date.getMonth(); // 0-11, so August is 7
                if (day === 27 && month === 7) {
                    return 'special-date-27-august';
                }

                return 'workday';
            }

            renderHolidayList() {
                const container = document.getElementById('holiday-list');
                if (!container) return;

                const currentMonthInfo = this.getCurrentMonthInfo();

                // Count holidays by category
                const sundayCount = this.sundays.size;
                const databaseHolidayCount = Array.from(this.holidays.entries()).filter(([date]) => {
                    const holidayDate = new Date(date);
                    return holidayDate.getFullYear() === currentMonthInfo.year &&
                        holidayDate.getMonth() === currentMonthInfo.month;
                }).length;

                // Create accordion structure
                const accordionHtml = `
                    <div class="holiday-accordion">
                        <div class="holiday-accordion-header" id="holiday-accordion-header">
                            <h4 style="color:white;">
                                📅 ${currentMonthInfo.monthName} ${currentMonthInfo.year}
                                <span class="badge badge-light ml-2">${sundayCount + databaseHolidayCount} hari libur</span>
                            </h4>
                            <i class="mdi mdi-chevron-down"></i>
                        </div>

                        <div class="holiday-accordion-body" id="holiday-accordion-body">
                            <div class="holiday-accordion-content">
                                ${this.renderHolidayCategories(currentMonthInfo)}

                                <!-- Summary Stats -->
                                <div class="holiday-summary">
                                    <div class="holiday-summary-stats">
                                        <div class="holiday-stat">
                                            <div class="holiday-stat-number">${sundayCount}</div>
                                            <div class="holiday-stat-label">Hari Minggu</div>
                                        </div>
                                        <div class="holiday-stat">
                                            <div class="holiday-stat-number">${databaseHolidayCount}</div>
                                            <div class="holiday-stat-label">Libur Database</div>
                                        </div>
                                        <div class="holiday-stat">
                                            <div class="holiday-stat-number">${sundayCount + databaseHolidayCount}</div>
                                            <div class="holiday-stat-label">Total Libur</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                container.innerHTML = accordionHtml;

                // Setup accordion events after rendering
                this.setupAccordionEvents();
            }

            renderHolidayCategories(currentMonthInfo) {
                const categories = [];

                // Category 1: Sundays
                if (this.sundays.size > 0) {
                    const sundayItems = Array.from(this.sundays).map(date => `
                        <div class="holiday-item">
                            <div class="holiday-info">
                                <span class="holiday-date">${this.formatDate(date)}</span>
                                <span class="holiday-name">Hari Minggu</span>
                                <span class="holiday-type sunday">Minggu</span>
                            </div>
                            <div class="holiday-details">
                                <span class="holiday-working-hours">0.00 jam</span>
                            </div>
                        </div>
                    `).join('');

                    categories.push(`
                        <div class="holiday-category">
                            <div class="holiday-category-title">
                                <span class="holiday-category-icon sunday">🟡</span>
                                Hari Minggu (${this.sundays.size})
                            </div>
                            ${sundayItems}
                        </div>
                    `);
                }

                // Category 2: Database Holidays
                const monthHolidays = Array.from(this.holidays.entries()).filter(([date]) => {
                    const holidayDate = new Date(date);
                    return holidayDate.getFullYear() === currentMonthInfo.year &&
                        holidayDate.getMonth() === currentMonthInfo.month;
                });

                if (monthHolidays.length > 0) {
                    const databaseItems = monthHolidays.map(([date, holiday]) => {
                        const typeLabel = holiday.type === 'holiday' ? 'Libur' : 'Kerja';
                        const typeClass = holiday.type === 'holiday' ? 'holiday' : 'workday';
                        const categoryIcon = holiday.type === 'holiday' ? 'holiday' : 'workday';

                        return `
                            <div class="holiday-item">
                                <div class="holiday-info">
                                    <span class="holiday-date">${this.formatDate(date)}</span>
                                    <span class="holiday-name">${holiday.name}</span>
                                    <span class="holiday-type ${typeClass}">${typeLabel}</span>
                                </div>
                                <div class="holiday-details">
                                    <span class="holiday-working-hours">${holiday.workingHours} jam</span>
                                    <span class="holiday-source">Database</span>
                                </div>
                            </div>
                        `;
                    }).join('');

                    categories.push(`
                        <div class="holiday-category">
                            <div class="holiday-category-title">
                                <span class="holiday-category-icon database">💾</span>
                                Libur dari Database (${monthHolidays.length})
                            </div>
                            ${databaseItems}
                        </div>
                    `);
                }

                // If no holidays at all
                if (categories.length === 0) {
                    return `
                        <div class="empty-holiday-message">
                            <i class="fas fa-calendar-times"></i>
                            <h6>Tidak ada hari libur di bulan ini</h6>
                            <p>Semua hari adalah hari kerja normal</p>
                        </div>
                    `;
                }

                return categories.join('');
            }

            toggleAccordion() {
                const accordionBody = document.getElementById('holiday-accordion-body');
                const accordionHeader = accordionBody.previousElementSibling;

                if (accordionBody.classList.contains('expanded')) {
                    // Collapse
                    accordionBody.classList.remove('expanded');
                    accordionHeader.classList.add('collapsed');
                } else {
                    // Expand
                    accordionBody.classList.add('expanded');
                    accordionHeader.classList.remove('collapsed');
                }
            }

            formatDate(dateStr) {
                const date = new Date(dateStr);
                return date.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }

            updateTimelineHolidays() {
                // Update header cells
                const headerCells = document.querySelectorAll('.timeline-header-cell');
                headerCells.forEach(cell => {
                    const date = cell.dataset.date;
                    if (date) {
                        const holidayType = this.getHolidayType(new Date(date));
                        const holidayInfo = this.getHolidayInfo(new Date(date));

                        // Reset classes
                        cell.className = 'timeline-header-cell';

                        // Add holiday type class
                        if (holidayType) {
                            cell.classList.add(holidayType);
                        } else {
                            cell.classList.add('workday');
                        }

                        // Update holiday indicator
                        this.updateHolidayIndicator(cell, holidayType, holidayInfo);
                    }
                });

                // Update timeline cells
                const timelineCells = document.querySelectorAll('.timeline-cell');
                timelineCells.forEach(cell => {
                    const date = cell.dataset.date;
                    if (date) {
                        const holidayType = this.getHolidayType(new Date(date));
                        cell.className = 'timeline-cell';
                        if (holidayType) {
                            cell.classList.add(holidayType);
                        } else {
                            cell.classList.add('workday');
                        }
                    }
                });

                // Force update tanggal 27 Agustus sudah tidak diperlukan karena menggunakan hard-coded data

                // Tambah holiday overlays ke visualization div
                this.addHolidayOverlaysToVisualization();
            }

            getHolidayInfo(date) {
                const dateStr = date.toISOString().split('T')[0];

                if (this.sundays.has(dateStr)) {
                    return {
                        type: 'holiday',
                        name: 'Hari Minggu',
                        icon: '🟡'
                    };
                }

                const holiday = this.holidays.get(dateStr);
                if (holiday) {
                    return {
                        type: 'holiday',
                        name: holiday.name || 'Hari Libur',
                        icon: '🔴'
                    };
                }

                // Check if it's special date 27 August
                const day = date.getDate();
                const month = date.getMonth(); // 0-11, so August is 7
                if (day === 27 && month === 7) {
                    return {
                        type: 'special-date-27-august',
                        name: 'Tanggal Merah 27 Agustus',
                        icon: '🔴'
                    };
                }

                return {
                    type: 'workday',
                    name: 'Hari Kerja',
                    icon: '🟢'
                };
            }

            updateHolidayIndicator(cell, holidayType, holidayInfo) {
                // Remove existing indicator
                const existingIndicator = cell.querySelector('.holiday-indicator');
                if (existingIndicator) {
                    existingIndicator.remove();
                }

                // Add new indicator
                const indicator = document.createElement('div');
                indicator.className = 'holiday-indicator';

                if (holidayType === 'holiday') {
                    indicator.textContent = 'LIBUR';
                } else {
                    indicator.textContent = 'KERJA';
                }

                cell.appendChild(indicator);

                // Add tooltip
                cell.title = `${holidayInfo.name} - ${holidayInfo.icon}`;
            }

            // Function untuk tambah holiday overlay di visualization div
            addHolidayOverlaysToVisualization() {
                console.log('🎨 Adding holiday overlays to visualization...');

                // Hapus overlay yang sudah ada
                const existingOverlays = document.querySelectorAll('.holiday-overlay');
                existingOverlays.forEach(overlay => overlay.remove());

                // Ambil semua header cells untuk dapat tanggal
                const headerCells = document.querySelectorAll('.timeline-header-cell');

                headerCells.forEach((headerCell, index) => {
                    const date = headerCell.dataset.date;
                    if (date) {
                        const holidayType = this.getHolidayType(new Date(date));
                        const holidayInfo = this.getHolidayInfo(new Date(date));

                        console.log(`📅 Adding overlay for date ${date}: ${holidayType}`);

                        // Buat overlay untuk setiap kolom tanggal
                        this.createHolidayOverlayForColumn(index, holidayType, holidayInfo);
                    }
                });

                console.log('✅ Holiday overlays added to visualization');
            }

            createHolidayOverlayForColumn(columnIndex, holidayType, holidayInfo) {
                // Cari visualization div
                const visualizationDiv = document.getElementById('visualization');
                if (!visualizationDiv) {
                    console.error('❌ Visualization div not found');
                    return;
                }

                // Buat overlay container
                const overlay = document.createElement('div');
                overlay.className = `holiday-overlay ${holidayType || 'workday'}`;

                // Set position berdasarkan kolom
                const columnWidth = 120; // Sesuaikan dengan min-width header
                const leftPosition = columnIndex * columnWidth;

                overlay.style.position = 'absolute';
                overlay.style.left = `${leftPosition}px`;
                overlay.style.top = '0';
                overlay.style.width = `${columnWidth}px`;
                overlay.style.height = '100%';

                // Tambah label
                const label = document.createElement('div');
                label.className = 'holiday-label';

                if (holidayType === 'sunday') {
                    label.textContent = 'MINGGU';
                } else if (holidayType === 'holiday') {
                    label.textContent = 'LIBUR';
                } else if (holidayType === 'national-holiday') {
                    label.textContent = 'NASIONAL';
                } else if (holidayType === 'special-date-27-august') {
                    label.textContent = 'MERAH';
                } else if (holidayType === 'workday') {
                    label.textContent = 'KERJA';
                }

                overlay.appendChild(label);
                visualizationDiv.appendChild(overlay);

                console.log(`✅ Added ${holidayType || 'workday'} overlay for column ${columnIndex}`);
            }

            // Refresh holidays dari database
            async refreshHolidays() {
                await this.fetchHolidaysFromDatabase();
                this.renderHolidayList();
                this.updateTimelineHolidays();
                this.setupAccordionEvents();
            }

            // Setup accordion event listeners
            setupAccordionEvents() {
                const accordionHeader = document.getElementById('holiday-accordion-header');
                if (accordionHeader) {
                    // Remove existing listeners to avoid duplicates
                    accordionHeader.removeEventListener('click', this.toggleAccordion.bind(this));
                    // Add new listener
                    accordionHeader.addEventListener('click', this.toggleAccordion.bind(this));
                }
            }

            // Force update tanggal 27 Agustus dengan styling merah
            forceUpdateAugust27() {
                console.log('🔴 Force updating August 27 styling...');

                // Cari semua cell yang berhubungan dengan tanggal 27 Agustus
                const allCells = document.querySelectorAll('.timeline-header-cell, .timeline-cell');

                allCells.forEach(cell => {
                    const date = cell.dataset.date;
                    if (date) {
                        const cellDate = new Date(date);
                        const day = cellDate.getDate();
                        const month = cellDate.getMonth(); // 0-11, August = 7

                        if (day === 27 && month === 7) {
                            console.log(`🔴 Found August 27 cell: ${date}`);

                            // Reset classes dulu
                            cell.className = cell.className.replace(/special-date-27-august/g, '');

                            // Tambah class khusus untuk tanggal 27 Agustus
                            if (cell.classList.contains('timeline-header-cell')) {
                                cell.classList.add('special-date-27-august');
                                console.log('✅ Added special-date-27-august to header cell');
                            } else if (cell.classList.contains('timeline-cell')) {
                                cell.classList.add('special-date-27-august');
                                console.log('✅ Added special-date-27-august to timeline cell');
                            }
                        }
                    }
                });

                console.log('🔴 August 27 styling update completed');
            }

            // Change month dan update Sundays
            changeMonth(year, month) {
                console.log(`🔄 Changing to month: ${year}-${month + 1}`);

                // Generate Sundays untuk bulan baru
                this.generateSundaysForMonth(year, month);

                // Update UI
                this.renderHolidayList();
                this.updateTimelineHolidays();
            }

            // Get current month info
            getCurrentMonthInfo() {
                const now = new Date();
                return {
                    year: now.getFullYear(),
                    month: now.getMonth(),
                    monthName: now.toLocaleDateString('id-ID', {
                        month: 'long'
                    })
                };
            }
        }

        // Drag & Drop System
        class DragDropManager {
            constructor() {
                this.draggedItem = null;
                this.dragData = null;
                this.setupEventListeners();
            }

            setupEventListeners() {
                // Timeline item drag events
                document.addEventListener('mousedown', this.handleMouseDown.bind(this));
                document.addEventListener('mousemove', this.handleMouseMove.bind(this));
                document.addEventListener('mouseup', this.handleMouseUp.bind(this));

                // Timeline cell drop events
                document.addEventListener('dragover', this.handleDragOver.bind(this));
                document.addEventListener('drop', this.handleDrop.bind(this));
            }

            handleMouseDown(event) {
                const timelineItem = event.target.closest('.timeline-item');
                if (!timelineItem) return;

                this.draggedItem = timelineItem;
                this.dragData = {
                    itemId: timelineItem.dataset.itemId,
                    machine: timelineItem.dataset.machine,
                    startTime: timelineItem.dataset.startTime,
                    endTime: timelineItem.dataset.endTime,
                    quantity: timelineItem.dataset.quantity
                };

                timelineItem.classList.add('dragging');

                // Create ghost element
                this.createGhostElement(timelineItem);
            }

            handleMouseMove(event) {
                if (!this.draggedItem) return;

                // Update ghost element position
                if (this.ghostElement) {
                    this.ghostElement.style.left = event.clientX + 'px';
                    this.ghostElement.style.top = event.clientY + 'px';
                }

                // Highlight valid drop zones
                this.highlightDropZones(event);
            }

            handleMouseUp(event) {
                if (!this.draggedItem) return;

                this.draggedItem.classList.remove('dragging');
                this.removeGhostElement();
                this.clearDropZoneHighlights();

                this.draggedItem = null;
                this.dragData = null;
            }

            createGhostElement(originalItem) {
                this.ghostElement = originalItem.cloneNode(true);
                this.ghostElement.classList.add('ghost');
                this.ghostElement.style.position = 'fixed';
                this.ghostElement.style.pointerEvents = 'none';
                this.ghostElement.style.zIndex = '9999';
                document.body.appendChild(this.ghostElement);
            }

            removeGhostElement() {
                if (this.ghostElement) {
                    this.ghostElement.remove();
                    this.ghostElement = null;
                }
            }

            highlightDropZones(event) {
                const timelineCell = event.target.closest('.timeline-cell');
                if (!timelineCell) return;

                // Check if cell is holiday
                const isHoliday = timelineCell.classList.contains('holiday') ||
                    timelineCell.classList.contains('sunday') ||
                    timelineCell.classList.contains('national-holiday');

                if (isHoliday) {
                    timelineCell.classList.add('drag-invalid');
                } else {
                    timelineCell.classList.add('drag-over');
                }
            }

            clearDropZoneHighlights() {
                document.querySelectorAll('.timeline-cell.drag-over, .timeline-cell.drag-invalid')
                    .forEach(cell => {
                        cell.classList.remove('drag-over', 'drag-invalid');
                    });
            }

            handleDragOver(event) {
                event.preventDefault();
                const timelineCell = event.target.closest('.timeline-cell');
                if (timelineCell) {
                    const isHoliday = timelineCell.classList.contains('holiday') ||
                        timelineCell.classList.contains('sunday') ||
                        timelineCell.classList.contains('national-holiday');

                    if (!isHoliday) {
                        timelineCell.classList.add('drag-over');
                    }
                }
            }

            handleDrop(event) {
                event.preventDefault();
                const timelineCell = event.target.closest('.timeline-cell');
                if (!timelineCell || !this.dragData) return;

                // Check if cell is holiday
                const isHoliday = timelineCell.classList.contains('holiday') ||
                    timelineCell.classList.contains('sunday') ||
                    timelineCell.classList.contains('national-holiday');

                if (isHoliday) {
                    alert('❌ Tidak bisa drop item di hari libur!');
                    return;
                }

                // Get new date from cell
                const newDate = timelineCell.dataset.date;
                if (!newDate) return;

                // Calculate new start and end times
                const newStartTime = this.calculateNewStartTime(newDate, this.dragData.startTime);
                const newEndTime = this.calculateNewEndTime(newStartTime, this.dragData.startTime, this.dragData
                    .endTime);

                // Update item position
                this.updateItemPosition(this.dragData.itemId, newDate, newStartTime, newEndTime);

                // Clear highlights
                this.clearDropZoneHighlights();
            }

            calculateNewStartTime(newDate, oldStartTime) {
                const oldDate = new Date(oldStartTime);
                const newDateObj = new Date(newDate);

                // Preserve time, change date
                newDateObj.setHours(oldDate.getHours());
                newDateObj.setMinutes(oldDate.getMinutes());
                newDateObj.setSeconds(oldDate.getSeconds());

                return newDateObj.toISOString();
            }

            calculateNewEndTime(newStartTime, oldStartTime, oldEndTime) {
                const duration = new Date(oldEndTime) - new Date(oldStartTime);
                const newStart = new Date(newStartTime);
                return new Date(newStart.getTime() + duration).toISOString();
            }

            updateItemPosition(itemId, newDate, newStartTime, newEndTime) {
                // Update timeline data
                if (window.timelineData) {
                    const item = window.timelineData.find(i => i.id == itemId);
                    if (item) {
                        item.start_jam = newStartTime;
                        item.end_jam = newEndTime;

                        console.log(`✅ Item ${item.code_item} berhasil dipindah ke ${newDate}`);
                        console.log(`   Start: ${newStartTime}`);
                        console.log(`   End: ${newEndTime}`);

                        // Trigger sync to timeline-table
                        this.syncToTimelineTable();
                    }
                }
            }

            syncToTimelineTable() {
                // Send message to timeline-table page
                if (window.opener && !window.opener.closed) {
                    window.opener.postMessage({
                        type: 'TIMELINE_UPDATE',
                        data: window.timelineData
                    }, '*');
                }

                // Show sync success message
                this.showSyncMessage();
            }

            showSyncMessage() {
                const message = document.createElement('div');
                message.innerHTML = `
                    <div style="position: fixed; top: 20px; right: 20px; background: #4caf50; color: white; padding: 15px; border-radius: 6px; z-index: 10000; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                        ✅ Timeline berhasil diupdate! Data akan sync ke timeline-table.
                    </div>
                `;
                document.body.appendChild(message);

                setTimeout(() => {
                    message.remove();
                }, 3000);
            }
        }

        // Timeline Manager
        class TimelineManager {
            constructor() {
                this.holidayManager = new HolidayManager();
                this.dragDropManager = new DragDropManager();
                this.setupSyncButton();
                this.setupRefreshHolidaysButton();

                // Setup accordion events after holiday manager is ready
                this.setupAccordionEvents();
            }

            setupSyncButton() {
                document.getElementById('sync-timeline-table')?.addEventListener('click', () => {
                    this.syncToTimelineTable();
                });
            }

            setupRefreshHolidaysButton() {
                document.getElementById('refresh-holidays')?.addEventListener('click', async () => {
                    console.log('🔄 Refreshing holidays dari database...');
                    await this.holidayManager.refreshHolidays();
                    console.log('✅ Holidays berhasil di-refresh!');

                    // Tambah holiday overlays setelah refresh
                    setTimeout(() => {
                        this.holidayManager.addHolidayOverlaysToVisualization();
                    }, 500);
                });
            }

            syncToTimelineTable() {
                // Open timeline-table in new window/tab
                const timelineTableUrl = '/sipo/process/timeline-table';
                window.open(timelineTableUrl, '_blank');
            }

            setupAccordionEvents() {
                // Wait for holiday manager to be ready
                setTimeout(() => {
                    if (this.holidayManager) {
                        this.holidayManager.setupAccordionEvents();
                    }
                }, 100);
            }
        }

        // Initialize when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            window.timelineManager = new TimelineManager();
            console.log('🎯 Timeline Manager initialized with holiday support and drag & drop!');

            // Add event listener for lubrication maintenance button
            const lubricationBtn = document.getElementById('lubrication-maintenance-btn');
            if (lubricationBtn) {
                lubricationBtn.addEventListener('click', function() {
                    openLubricationMaintenanceModal();
                });
            }
        });

        // Lubrication Maintenance Functions
        function openLubricationMaintenanceModal() {
            // Populate machine dropdown
            populateLubricationMachineDropdown();

            // Show modal
            $('#lubricationMaintenanceModal').modal('show');


        }

        function populateLubricationMachineDropdown() {
            const select = document.getElementById('lubrication-machine-select');
            select.innerHTML = '<option value="">-- Pilih Mesin --</option>';

            fetch("<?php echo e(route('process.get-lubrication-machines')); ?>")
                .then(response => response.json())
                .then(data => {
                    console.log('Machine data received:', data);
                    if (data.success && data.machines) {
                        data.machines.forEach(machine => {
                            const option = document.createElement('option');
                            option.value = machine.namaMesin;
                            option.textContent = `${machine.namaMesin}`;
                            select.appendChild(option);
                            console.log('Added machine:', machine.namaMesin);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading machines:', error);
                });
        }

        function loadLubricationData() {
            // Show loading state
            document.getElementById('lubrication-loading').style.display = 'block';
            document.getElementById('lubrication-content').style.display = 'none';
            document.getElementById('lubrication-not-found').style.display = 'none';

            // Fetch lubrication data
            fetch("<?php echo e(route('process.get-lubrication-maintenance')); ?>", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('lubrication-loading').style.display = 'none';

                    if (data.success && data.lubrication_data && data.lubrication_data.length > 0) {
                        displayLubricationData(data.lubrication_data, data.total_count);
                    } else {
                        showLubricationNotFound();
                    }
                })
                .catch(error => {
                    console.error('Error loading lubrication data:', error);
                    document.getElementById('lubrication-loading').style.display = 'none';
                    showLubricationNotFound();
                });
        }

        function displayLubricationData(lubricationData, totalCount) {
            // Store data globally
            currentLubricationData = lubricationData;

            // Update machine info
            document.getElementById('lubrication-machine-name').textContent = 'Semua Mesin';
            document.getElementById('lubrication-total-count').textContent = totalCount;

            // Populate table
            const tbody = document.getElementById('lubrication-tbody');
            tbody.innerHTML = '';

            lubricationData.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <div class="form-check">
                            <input type="checkbox" id="lubrication_${item.id}" class="form-check-input lubrication-checkbox" data-id="${item.id}" onchange="handleLubricationSelection()">
                            <label class="form-check-label" for="lubrication_${item.id}">
                                ${item.id}
                            </label>
                        </div>
                    </td>
                    <td><strong>${item.title || '-'}</strong></td>
                    <td>${formatDate(item.datetgs)}</td>
                    <td>${item.isi_tugas || '-'}</td>
                    <td>${item.mesin || '-'}</td>
                    <td>${item.pelumasan_shift || '-'}</td>
                    <td>
                        <span class="badge badge-${getStatusBadgeClass(item.status_tugas)}">
                            ${item.status_tugas || '-'}
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addSingleLubricationToPlan(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                            <i class="mdi mdi-plus"></i> Add
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            // Show content
            document.getElementById('lubrication-content').style.display = 'block';
        }

        function showLubricationNotFound() {
            document.getElementById('lubrication-content').style.display = 'none';
            document.getElementById('lubrication-not-found').style.display = 'block';
        }

        function formatDate(dateString) {
            if (!dateString) return '-';

            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            } catch (error) {
                return dateString;
            }
        }

        function getStatusBadgeClass(status) {
            switch (status?.toLowerCase()) {
                case 'plan':
                    return 'info';
                case 'dikerjakan':
                case 'in progress':
                    return 'warning';
                case 'selesai':
                case 'completed':
                    return 'success';
                case 'batal':
                case 'cancelled':
                    return 'danger';
                default:
                    return 'secondary';
            }
        }

        // Add event listener for machine selection change
        document.addEventListener('DOMContentLoaded', function() {
            const machineSelect = document.getElementById('lubrication-machine-select');
            if (machineSelect) {
                machineSelect.addEventListener('change', function() {
                    const selectedMachine = this.value;

                    if (selectedMachine) {
                        // Load data for selected machine from server
                        loadLubricationDataForMachine(selectedMachine);
                    } else {
                        // Show all data
                        loadLubricationData();
                    }
                });
            }

            // Add event listener for Load Data button
            const loadDataBtn = document.getElementById('load-lubrication-data');
            if (loadDataBtn) {
                loadDataBtn.addEventListener('click', function() {
                    const selectedMachine = document.getElementById('lubrication-machine-select').value;

                    if (selectedMachine) {
                        // Load data for selected machine
                        loadLubricationDataForMachine(selectedMachine);
                    } else {
                        // Show all data if no machine selected
                        loadLubricationData();
                    }
                });
            }

            // Add event listener for Add Selected to Plan button
            const addSelectedBtn = document.getElementById('add-selected-to-plan');
            if (addSelectedBtn) {
                addSelectedBtn.addEventListener('click', addSelectedLubricationsToPlan);
            }

            // Add event listener for Add Selected to Timeline button
            const addTimelineBtn = document.getElementById('add-timeline-lubrication');
            if (addTimelineBtn) {
                addTimelineBtn.addEventListener('click', addSelectedTimelineLubrications);
            }

            // Add event listener for shift selection in timeline modal
            const shiftSelect = document.getElementById('timeline-shift-select');
            if (shiftSelect) {
                shiftSelect.addEventListener('change', updateTimelineShiftTimes);
            }
        });

        // Add new function to load data for specific machine
        function loadLubricationDataForMachine(machineName) {
            // Show loading state
            document.getElementById('lubrication-loading').style.display = 'block';
            document.getElementById('lubrication-content').style.display = 'none';
            document.getElementById('lubrication-not-found').style.display = 'none';

            // Fetch lubrication data for specific machine
            fetch("<?php echo e(route('process.get-lubrication-maintenance')); ?>", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        machine: machineName
                    })
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('lubrication-loading').style.display = 'none';

                    if (data.success && data.lubrication_data && data.lubrication_data.length > 0) {
                        displayLubricationDataForMachine(data.lubrication_data, data.total_count, machineName);
                    } else {
                        showLubricationNotFound();
                    }
                })
                .catch(error => {
                    console.error('Error loading lubrication data for machine:', error);
                    document.getElementById('lubrication-loading').style.display = 'none';
                    showLubricationNotFound();
                });
        }

        // Add new function to display data for specific machine
        function displayLubricationDataForMachine(lubricationData, totalCount, machineName) {
            // Store data globally
            currentLubricationData = lubricationData;

            // Update machine info
            document.getElementById('lubrication-machine-name').textContent = machineName;
            document.getElementById('lubrication-total-count').textContent = totalCount;

            // Populate table
            const tbody = document.getElementById('lubrication-tbody');
            tbody.innerHTML = '';

            lubricationData.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <div class="form-check">
                            <input type="checkbox" id="lubrication_${item.id}" class="form-check-input lubrication-checkbox" data-id="${item.id}" onchange="handleLubricationSelection()">
                            <label class="form-check-label" for="lubrication_${item.id}">
                                ${item.id}
                            </label>
                        </div>
                    </td>
                    <td><strong>${item.title || '-'}</strong></td>
                    <td>${formatDate(item.datetgs)}</td>
                    <td>${item.isi_tugas || '-'}</td>
                    <td>${item.mesin || '-'}</td>
                    <td>${item.pelumasan_shift || '1'}</td>
                    <td>
                        <span class="badge badge-${getStatusBadgeClass(item.status_tugas)}">
                            ${item.status_tugas || '-'}
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addSingleLubricationToPlan(${JSON.stringify(item).replace(/"/g, '&quot;')})">
                            <i class="mdi mdi-plus"></i> Add
                            </button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            // Show content
            document.getElementById('lubrication-content').style.display = 'block';
        }

        // Function to open add lubrication modal from timeline
        function openAddLubricationModal(clickedDate, clickedGroup) {
            console.log('🖱️ Opening add lubrication modal for date:', clickedDate, 'group:', clickedGroup);

            // Get machine info from group
            const machineInfo = allGroups.find(g => g.Code === clickedGroup);
            if (!machineInfo) {
                console.error('❌ Machine info not found for group:', clickedGroup);
                return;
            }

            // Format date for display
            const dateObj = new Date(clickedDate);
            const formattedDate = dateObj.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            // Update modal info
            document.getElementById('timeline-date-info').textContent = formattedDate;
            document.getElementById('timeline-machine-info').textContent = machineInfo.Code;
            document.getElementById('timeline-shift-info').textContent = 'Shift 1 (08:00 - 16:00)';

            // Set default times based on shift 1
            document.getElementById('timeline-start-time').value = '08:00';
            document.getElementById('timeline-end-time').value = '16:00';

            // Load lubrication data for this machine
            loadTimelineLubricationData(machineInfo.Description);

            // Show modal
            $('#addLubricationFromTimelineModal').modal('show');
        }

        // Function to load lubrication data for timeline modal
        function loadTimelineLubricationData(machineName) {
            console.log('🔄 Loading lubrication data for timeline modal, machine:', machineName);

            // Show loading state
            const tbody = document.getElementById('timeline-lubrication-tbody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        Loading data pelumasan...
                    </td>
                </tr>
            `;

            // Fetch lubrication data for specific machine
            fetch("<?php echo e(route('process.get-lubrication-maintenance')); ?>", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        machine: machineName
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('📊 Lubrication data received for timeline modal:', data);

                    if (data.success && data.lubrication_data && data.lubrication_data.length > 0) {
                        displayTimelineLubricationData(data.lubrication_data, machineName);
                    } else {
                        showTimelineLubricationNotFound();
                    }
                })
                .catch(error => {
                    console.error('❌ Error loading lubrication data for timeline modal:', error);
                    showTimelineLubricationNotFound();
                });
        }

        // Function to display lubrication data in timeline modal
        function displayTimelineLubricationData(lubricationData, machineName) {
            console.log('📊 Displaying lubrication data in timeline modal:', lubricationData);

            // Store data globally for timeline modal
            currentLubricationData = lubricationData;

            // Populate table
            const tbody = document.getElementById('timeline-lubrication-tbody');
            tbody.innerHTML = '';

            lubricationData.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <div class="form-check">
                            <input type="checkbox" id="timeline_lubrication_${item.id}" class="form-check-input timeline-lubrication-checkbox" data-id="${item.id}" onchange="handleTimelineLubricationSelection()">
                            <label class="form-check-label" for="timeline_lubrication_${item.id}">
                                ${item.id}
                            </label>
                        </div>
                    </td>
                    <td><strong>${item.title || '-'}</strong></td>
                    <td>${formatDate(item.datetgs)}</td>
                    <td>${item.isi_tugas || '-'}</td>
                    <td>${item.namaMesinSim || '-'}</td>
                    <td>${item.pelumasan_shift || '1'}</td>
                    <td>
                        <span class="badge badge-${getStatusBadgeClass(item.status_tugas)}">
                            ${item.status_tugas || '-'}
                        </span>
                    </td>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        // Function to show not found state in timeline modal
        function showTimelineLubricationNotFound() {
            const tbody = document.getElementById('timeline-lubrication-tbody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center">
                        <div class="alert alert-warning mb-0">
                            <i class="mdi mdi-alert"></i> Tidak ada jadwal pelumasan maintenance untuk mesin ini.
                        </div>
                    </td>
                </tr>
            `;
        }

        // Function to handle timeline lubrication selection
        function handleTimelineLubricationSelection() {
            const checkboxes = document.querySelectorAll('.timeline-lubrication-checkbox:checked');
            const addButton = document.getElementById('add-timeline-lubrication');
            const selectedCount = document.getElementById('timeline-selected-count');

            if (checkboxes.length > 0) {
                addButton.disabled = false;
                selectedCount.textContent = `${checkboxes.length} item selected`;
            } else {
                addButton.disabled = true;
                selectedCount.textContent = '0 item selected';
            }
        }

        // Function to add single lubrication from timeline modal
        function addSingleTimelineLubrication(itemData) {
            console.log('🛢️ Adding single lubrication from timeline modal:', itemData);

            // Get selected shift and times
            const selectedShift = document.getElementById('timeline-shift-select').value;
            const startTime = document.getElementById('timeline-start-time').value;
            const endTime = document.getElementById('timeline-end-time').value;

            // Get clicked date and machine from modal info
            const dateInfo = document.getElementById('timeline-date-info').textContent;
            const machineInfo = document.getElementById('timeline-machine-info').textContent;

            console.log('📅 Modal info - Date:', dateInfo, 'Machine:', machineInfo, 'Shift:', selectedShift, 'Start:',
                startTime, 'End:', endTime);

            // Create custom lubrication item for timeline
            addCustomLubricationToTimeline(itemData, dateInfo, machineInfo, selectedShift, startTime, endTime);
        }

        // Function to add custom lubrication to timeline
        function addCustomLubricationToTimeline(itemData, dateInfo, machineInfo, selectedShift, startTime, endTime) {
            console.log('🛢️ Adding custom lubrication to timeline:', {
                itemData,
                dateInfo,
                machineInfo,
                selectedShift,
                startTime,
                endTime
            });

            if (!timeline || !itemsDS) {
                console.log('❌ Timeline or itemsDS not available');
                return;
            }

            // Debug allGroups status
            console.log('🔍 allGroups status:', {
                length: allGroups ? allGroups.length : 'undefined',
                data: allGroups,
                machineInfo: machineInfo
            });

            // Use namaMesinSim from itemData instead of machineInfo from modal
            const actualMachineName = itemData.codeMesinSim || itemData.mesin;
            console.log('🔍 Using machine name from itemData:', actualMachineName);

            // Find machine code from actual machine name
            let machineCode = allGroups.find(g => g.Code === actualMachineName)?.Code;
            console.log('🔍 Machine code lookup result:', machineCode);

            if (!machineCode) {
                console.log('❌ Machine code not found for:', actualMachineName);
                console.log('🔍 Available groups:', allGroups?.map(g => ({
                    Code: g.Code,
                    Description: g.Description
                })));

                // Debug: Show first few groups to see the format
                console.log('🔍 First 5 groups:', allGroups?.slice(0, 5).map(g => ({
                    Code: g.Code,
                    Description: g.Description
                })));

                // Try to find partial matches
                const partialMatches = allGroups?.filter(g =>
                    g.Description.includes(actualMachineName) ||
                    actualMachineName.includes(g.Code)
                );
                console.log('🔍 Partial matches found:', partialMatches?.map(g => ({
                    Code: g.Code,
                    Description: g.Description
                })));

                // Try smart matching
                const smartMatch = findSmartMachineMatch(actualMachineName, allGroups);
                if (smartMatch) {
                    console.log('🎯 Smart match found:', smartMatch);
                    // Continue with smart match
                    machineCode = smartMatch.Code; // Remove 'const' to reassign
                    console.log('🎯 Using smart match machine code:', machineCode);
                } else {
                    console.log('❌ No smart match found either');
                    return;
                }
            }

            // Parse date from dateInfo (format: "Senin, 1 September 2025")
            const dateMatch = dateInfo.match(/(\d+)\s+(\w+)\s+(\d+)/);
            if (!dateMatch) {
                console.log('❌ Could not parse date from:', dateInfo);
                return;
            }

            const day = parseInt(dateMatch[1]);
            const monthName = dateMatch[2];
            const year = parseInt(dateMatch[3]);

            // Convert month name to number
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            const month = monthNames.indexOf(monthName);
            if (month === -1) {
                console.log('❌ Could not parse month:', monthName);
                return;
            }

            // Create start and end dates
            const startDate = new Date(year, month, day);
            const [startHour, startMinute] = startTime.split(':').map(Number);
            startDate.setHours(startHour, startMinute, 0, 0);

            const endDate = new Date(year, month, day);
            const [endHour, endMinute] = endTime.split(':').map(Number);
            endDate.setHours(endHour, endMinute, 0, 0);

            // Handle shift 2 and 3 (next day)
            if (selectedShift === '2' || selectedShift === '3') {
                if (endHour < startHour) {
                    endDate.setDate(endDate.getDate() + 1);
                }
            }

            console.log('⏰ Calculated dates - Start:', startDate, 'End:', endDate);

            // Check for conflicts
            const conflicts = checkTimelineConflicts(startDate, endDate, machineInfo);
            if (conflicts.length > 0) {
                console.log('⚠️ Conflicts found:', conflicts);
                showConflictModal(itemData, startDate, endDate, conflicts);
            } else {
                console.log('✅ No conflicts, adding directly');
                console.log('🔍 About to call addLubricationItem with:', {
                    itemData,
                    startDate,
                    endDate
                });
                addLubricationItem(itemData, startDate, endDate);
                console.log('✅ addLubricationItem completed');

                // Close modal
                $('#addLubricationFromTimelineModal').modal('hide');

                // Show success message
                showToast(`Pelumasan maintenance berhasil ditambahkan ke timeline untuk ${machineInfo} pada ${dateInfo}!`);
            }
        }

        // Function to add multiple selected lubrications from timeline modal
        function addSelectedTimelineLubrications() {
            console.log('🚀 Starting to add selected timeline lubrications...');

            const checkboxes = document.querySelectorAll('.timeline-lubrication-checkbox:checked');
            console.log('🔍 Found checked timeline checkboxes:', checkboxes.length);

            if (checkboxes.length === 0) {
                showToast('Pilih minimal satu item pelumasan!');
                return;
            }

            // Get selected shift and times
            const selectedShift = document.getElementById('timeline-shift-select').value;
            const startTime = document.getElementById('timeline-start-time').value;
            const endTime = document.getElementById('timeline-end-time').value;

            // Get clicked date and machine from modal info
            const dateInfo = document.getElementById('timeline-date-info').textContent;
            const machineInfo = document.getElementById('timeline-machine-info').textContent;

            let addedCount = 0;
            let errorCount = 0;

            checkboxes.forEach((checkbox, index) => {
                console.log(`🔄 Processing timeline checkbox ${index + 1}/${checkboxes.length}`);

                const itemId = checkbox.getAttribute('data-id');
                console.log('🔍 Item ID:', itemId);

                const itemData = getLubricationItemData(itemId);
                console.log('🔍 Item data:', itemData);

                if (itemData) {
                    try {
                        console.log('✅ Calling addCustomLubricationToTimeline for item:', itemData.id);
                        addCustomLubricationToTimeline(itemData, dateInfo, machineInfo, selectedShift, startTime,
                            endTime);
                        addedCount++;
                        console.log('✅ Successfully added timeline item:', itemData.id);
                    } catch (error) {
                        console.error('❌ Error adding timeline lubrication:', error);
                        errorCount++;
                    }
                } else {
                    console.log('❌ No item data found for ID:', itemId);
                }
            });

            // Reset selection
            checkboxes.forEach(checkbox => checkbox.checked = false);
            handleTimelineLubricationSelection();

            console.log('📊 Final timeline results - Added:', addedCount, 'Errors:', errorCount);

            if (addedCount > 0) {
                showToast(`Berhasil menambahkan ${addedCount} item pelumasan ke timeline!`);
            }
            if (errorCount > 0) {
                showToast(`Gagal menambahkan ${errorCount} item pelumasan!`);
            }
        }

        // Function to update shift times automatically
        function updateTimelineShiftTimes() {
            const selectedShift = document.getElementById('timeline-shift-select').value;
            const startTimeInput = document.getElementById('timeline-start-time');
            const endTimeInput = document.getElementById('timeline-end-time');
            const shiftInfo = document.getElementById('timeline-shift-info');

            let startTime, endTime, shiftText;

            switch (selectedShift) {
                case '1':
                    startTime = '08:00';
                    endTime = '16:00';
                    shiftText = 'Shift 1 (08:00 - 16:00)';
                    break;
                case '2':
                    startTime = '16:00';
                    endTime = '00:00';
                    shiftText = 'Shift 2 (16:00 - 00:00)';
                    break;
                case '3':
                    startTime = '00:00';
                    endTime = '08:00';
                    shiftText = 'Shift 3 (00:00 - 08:00)';
                    break;
                default:
                    startTime = '08:00';
                    endTime = '16:00';
                    shiftText = 'Shift 1 (08:00 - 16:00)';
            }

            startTimeInput.value = startTime;
            endTimeInput.value = endTime;
            shiftInfo.textContent = shiftText;

            console.log('🔄 Updated shift times - Shift:', selectedShift, 'Start:', startTime, 'End:', endTime);
        }

        // Smart machine matching function
        function findSmartMachineMatch(machineName, allGroups) {
            console.log('🧠 Smart matching for:', machineName);

            if (!allGroups || !machineName) return null;

            // Normalize machine name (remove spaces, dashes, convert to lowercase)
            const normalizedMachineName = machineName.replace(/[\s\-]/g, '').toLowerCase();
            console.log('🧠 Normalized machine name:', normalizedMachineName);

            // Try exact match first
            let match = allGroups.find(g =>
                g.Code === machineName ||
                g.Description === machineName
            );
            if (match) {
                console.log('🎯 Exact match found:', match);
                return match;
            }

            // Try normalized match
            match = allGroups.find(g => {
                const normalizedCode = g.Code.replace(/[\s\-]/g, '').toLowerCase();
                const normalizedDesc = g.Description.replace(/[\s\-]/g, '').toLowerCase();

                return normalizedCode === normalizedMachineName ||
                    normalizedDesc === normalizedMachineName ||
                    normalizedCode.includes(normalizedMachineName) ||
                    normalizedDesc.includes(normalizedMachineName) ||
                    normalizedMachineName.includes(normalizedCode) ||
                    normalizedMachineName.includes(normalizedDesc);
            });

            if (match) {
                console.log('🎯 Normalized match found:', match);
                return match;
            }

            // Try partial match with higher tolerance
            const partialMatches = allGroups.filter(g => {
                const normalizedCode = g.Code.replace(/[\s\-]/g, '').toLowerCase();
                const normalizedDesc = g.Description.replace(/[\s\-]/g, '').toLowerCase();

                // Check if any part of the machine name matches
                const machineParts = normalizedMachineName.split(/[\s\-]/);
                return machineParts.some(part =>
                    normalizedCode.includes(part) ||
                    normalizedDesc.includes(part)
                );
            });

            if (partialMatches.length > 0) {
                console.log('🎯 Partial matches found:', partialMatches);
                // Return the best match (first one)
                return partialMatches[0];
            }

            console.log('❌ No smart match found');
            return null;
        }

        // Handle lubrication checkbox selection
        function handleLubricationCheckbox(checkbox, itemData) {
            if (checkbox.checked) {
                addLubricationToTimeline(itemData);
            } else {
                removeLubricationFromTimeline(itemData.id);
            }
        }

        // Add lubrication to timeline
        function addLubricationToTimeline(itemData) {
            console.log('🛢️ Adding lubrication to timeline:', itemData);
            console.log('📊 allGroups:', allGroups);
            console.log('📊 allPlans:', allPlans);

            const date = new Date(itemData.datetgs);
            const shift = parseInt(itemData.pelumasan_shift) || 1;

            // Calculate start and end time based on shift
            let startHour, endHour;
            switch (shift) {
                case 1: // Shift 1: 08:00-16:00
                    startHour = 8;
                    endHour = 16;
                    break;
                case 2: // Shift 2: 16:00-00:00
                    startHour = 16;
                    endHour = 0;
                    break;
                case 3: // Shift 3: 00:00-08:00
                    startHour = 0;
                    endHour = 8;
                    break;
                default:
                    startHour = 8;
                    endHour = 16;
            }

            const startTime = new Date(date);
            startTime.setHours(startHour, 0, 0, 0);

            const endTime = new Date(date);
            if (endHour === 0) {
                endTime.setDate(endTime.getDate() + 1);
                endTime.setHours(0, 0, 0, 0);
            } else {
                endTime.setHours(endHour, 0, 0, 0);
            }

            console.log('⏰ Calculated times - Start:', startTime, 'End:', endTime);

            // Check for conflicts with existing items
            // Use namaMesinSim from itemData instead of mesin
            const actualMachineName = itemData.namaMesinSim || itemData.mesin;
            const conflicts = checkTimelineConflicts(startTime, endTime, actualMachineName);

            if (conflicts.length > 0) {
                console.log('⚠️ Conflicts found:', conflicts);
                showConflictModal(itemData, startTime, endTime, conflicts);
            } else {
                console.log('✅ No conflicts, adding directly');
                // No conflicts, add directly
                addLubricationItem(itemData, startTime, endTime);
            }
        }

        // Check for timeline conflicts
        function checkTimelineConflicts(startTime, endTime, machineName) {
            console.log('🔍 Checking conflicts for:', {
                startTime,
                endTime,
                machineName
            });
            const conflicts = [];

            if (!timeline || !itemsDS) {
                console.log('❌ Timeline or itemsDS not available');
                return conflicts;
            }

            // For conflict checking, we need to use the machine name that matches allGroups
            // This function is called with machineInfo from modal, but we need to find the actual machine code
            const machineCode = allGroups.find(g => g.Description === machineName)?.Code;
            console.log('🔍 Found machine code:', machineCode, 'for machine:', machineName);

            if (!machineCode) {
                console.log('❌ Machine code not found for:', machineName);
                console.log('📊 Available groups:', allGroups);
                return conflicts;
            }

            const existingItems = itemsDS.get({
                filter: item => item.group === machineCode
            });
            console.log('🔍 Existing items for machine:', existingItems);

            existingItems.forEach(item => {
                const itemStart = new Date(item.start);
                const itemEnd = new Date(item.end);

                // Check for overlap
                if (startTime < itemEnd && endTime > itemStart) {
                    console.log('⚠️ Conflict detected with item:', item);
                    conflicts.push({
                        id: item.id,
                        start: itemStart,
                        end: itemEnd,
                        content: item.content,
                        type: 'existing_item'
                    });
                }
            });

            console.log('🔍 Total conflicts found:', conflicts.length);
            return conflicts;
        }

        // Show conflict resolution modal
        function showConflictModal(itemData, startTime, endTime, conflicts) {
            const conflictList = conflicts.map(conflict =>
                `<li><strong>${conflict.content}</strong> (${formatTime(conflict.start)} - ${formatTime(conflict.end)})</li>`
            ).join('');

            const modalHtml = `
                <div class="modal fade" id="conflictModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title">⚠️ Jadwal Konflik Ditemukan</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <h6>Pelumasan Maintenance:</h6>
                                <p><strong>${itemData.title}</strong></p>
                                <p>Tanggal: ${formatDate(itemData.datetgs)}</p>
                                <p>Shift: ${itemData.pelumasan_shift || 1}</p>
                                <p>Waktu: ${formatTime(startTime)} - ${formatTime(endTime)}</p>

                                <h6>Item yang Konflik:</h6>
                                <ul>${conflictList}</ul>

                                <h6>Pilih Opsi:</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="conflictOption" id="option1" value="moveLubrication" checked>
                                    <label class="form-check-label" for="option1">
                                        Geser Pelumasan ke jadwal kosong
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="conflictOption" id="option2" value="moveItems">
                                    <label class="form-check-label" for="option2">
                                        Geser Item yang ada (cari space kosong)
                                    </label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="button" class="btn btn-primary" onclick="resolveConflict('${itemData.id}', '${startTime.toISOString()}', '${endTime.toISOString()}', '${itemData.mesin}')">
                                    Resolve Conflict
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal if any
            const existingModal = document.getElementById('conflictModal');
            if (existingModal) {
                existingModal.remove();
            }

            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Show modal
            $('#conflictModal').modal('show');
        }

        // Resolve conflict based on user choice
        function resolveConflict(itemId, startTime, endTime, machineName) {
            const option = document.querySelector('input[name="conflictOption"]:checked').value;

            if (option === 'moveLubrication') {
                // Find next available slot for lubrication
                const newSlot = findNextAvailableSlot(new Date(startTime), new Date(endTime), machineName);
                if (newSlot) {
                    addLubricationItem({
                        id: itemId
                    }, newSlot.start, newSlot.end);
                }
            } else {
                // Move existing items to find space for lubrication
                const spaceFound = findSpaceForLubrication(new Date(startTime), new Date(endTime), machineName);
                if (spaceFound) {
                    addLubricationItem({
                        id: itemId
                    }, new Date(startTime), new Date(endTime));
                }
            }

            $('#conflictModal').modal('hide');
        }

        // Find next available slot for lubrication
        function findNextAvailableSlot(startTime, endTime, machineName) {
            const duration = endTime - startTime;
            let currentTime = new Date(startTime);

            // Try for next 7 days
            for (let day = 0; day < 7; day++) {
                currentTime.setDate(currentTime.getDate() + day);

                for (let hour = 0; hour < 24; hour += 8) {
                    const testStart = new Date(currentTime);
                    testStart.setHours(hour, 0, 0, 0);

                    const testEnd = new Date(testStart.getTime() + duration);

                    const conflicts = checkTimelineConflicts(testStart, testEnd, machineName);
                    if (conflicts.length === 0) {
                        return {
                            start: testStart,
                            end: testEnd
                        };
                    }
                }
            }

            return null;
        }

        // Find space for lubrication by moving items
        function findSpaceForLubrication(startTime, endTime, machineName) {
            const machineCode = allGroups.find(g => g.Description === machineName)?.Code;
            if (!machineCode) return false;

            const existingItems = itemsDS.get({
                filter: item => item.group === machineCode
            });

            // Sort items by start time
            existingItems.sort((a, b) => new Date(a.start) - new Date(b.start));

            // Find gaps between items
            for (let i = 0; i < existingItems.length - 1; i++) {
                const currentItem = existingItems[i];
                const nextItem = existingItems[i + 1];

                const gapStart = new Date(currentItem.end);
                const gapEnd = new Date(nextItem.start);
                const gapDuration = gapEnd - gapStart;
                const requiredDuration = endTime - startTime;

                if (gapDuration >= requiredDuration) {
                    // Found suitable gap
                    const newStart = new Date(gapStart);
                    const newEnd = new Date(newStart.getTime() + requiredDuration);

                    // Update lubrication time
                    startTime.setTime(newStart.getTime());
                    endTime.setTime(newEnd.getTime());

                    return true;
                }
            }

            return false;
        }

        // Add lubrication item to timeline
        function addLubricationItem(itemData, startTime, endTime) {
            console.log('🛢️ Adding lubrication item:', {
                itemData,
                startTime,
                endTime
            });

            if (!timeline || !itemsDS) {
                console.log('❌ Timeline or itemsDS not available');
                return;
            }

            // Use namaMesinSim from itemData instead of mesin
            const actualMachineName = itemData.namaMesinSim || itemData.mesin;
            console.log('🔍 Using machine name from itemData:', actualMachineName);

            let machineCode = allGroups.find(g => g.Description === actualMachineName)?.Code;
            console.log('🔍 Machine code found:', machineCode, 'for machine:', actualMachineName);

            if (!machineCode) {
                console.log('❌ Machine code not found for:', actualMachineName);
                console.log('📊 Available groups:', allGroups);

                // Debug: Show first few groups to see the format
                console.log('🔍 First 5 groups:', allGroups?.slice(0, 5).map(g => ({
                    Code: g.Code,
                    Description: g.Description
                })));

                // Try to find partial matches
                const partialMatches = allGroups?.filter(g =>
                    g.Description.includes(actualMachineName) ||
                    actualMachineName.includes(g.Code)
                );
                console.log('🔍 Partial matches found:', partialMatches?.map(g => ({
                    Code: g.Code,
                    Description: g.Description
                })));

                // Try smart matching
                const smartMatch = findSmartMachineMatch(actualMachineName, allGroups);
                if (smartMatch) {
                    console.log('🎯 Smart match found:', smartMatch);
                    // Continue with smart match
                    machineCode = smartMatch.Code; // Remove 'const' to reassign
                    console.log('🎯 Using smart match machine code:', machineCode);
                } else {
                    console.log('❌ No smart match found either');
                    return;
                }
            }

            const lubricationItem = {
                id: `lubrication_${itemData.id}`,
                group: machineCode,
                content: `<span class='lubrication-title'>🛢️ ${itemData.title || 'Pelumasan Maintenance'}</span>`,
                start: startTime,
                end: endTime,
                className: 'lubrication-maintenance'
            };

            console.log('🛢️ Created lubrication item:', lubricationItem);

            itemsDS.add(lubricationItem);
            console.log('✅ Lubrication item added to timeline');

            // Uncheck the checkbox
            const checkbox = document.getElementById(`lubrication_${itemData.id}`);
            if (checkbox) {
                checkbox.checked = false;
            }

            showToast('Pelumasan maintenance berhasil ditambahkan ke timeline!');
        }

        // Remove lubrication from timeline
        function removeLubricationFromTimeline(itemId) {
            if (!timeline || !itemsDS) return;

            const itemToRemove = itemsDS.get(`lubrication_${itemId}`);
            if (itemToRemove) {
                itemsDS.remove(`lubrication_${itemId}`);
                showToast('Pelumasan maintenance dihapus dari timeline!');
            }
        }

        // Format time helper
        function formatTime(date) {
            return date.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // Handle multiple lubrication selection
        function handleLubricationSelection() {
            const checkboxes = document.querySelectorAll('.lubrication-checkbox:checked');
            const addButton = document.getElementById('add-selected-to-plan');
            const selectedCount = document.getElementById('selected-count');

            if (checkboxes.length > 0) {
                addButton.disabled = false;
                selectedCount.textContent = `${checkboxes.length} item selected`;
            } else {
                addButton.disabled = true;
                selectedCount.textContent = '0 item selected';
            }
        }

        // Add single lubrication to plan
        function addSingleLubricationToPlan(itemData) {
            addLubricationToTimeline(itemData);
        }

        // Add multiple selected lubrications to plan
        function addSelectedLubricationsToPlan() {
            console.log('🚀 Starting to add selected lubrications to plan...');

            const checkboxes = document.querySelectorAll('.lubrication-checkbox:checked');
            console.log('🔍 Found checked checkboxes:', checkboxes.length);

            if (checkboxes.length === 0) {
                showToast('Pilih minimal satu item pelumasan!');
                return;
            }

            let addedCount = 0;
            let errorCount = 0;

            checkboxes.forEach((checkbox, index) => {
                console.log(`🔄 Processing checkbox ${index + 1}/${checkboxes.length}`);

                const itemId = checkbox.getAttribute('data-id');
                console.log('🔍 Item ID:', itemId);

                const itemData = getLubricationItemData(itemId);
                console.log('🔍 Item data:', itemData);

                if (itemData) {
                    try {
                        console.log('✅ Calling addLubricationToTimeline for item:', itemData.id);
                        addLubricationToTimeline(itemData);
                        addedCount++;
                        console.log('✅ Successfully added item:', itemData.id);
                    } catch (error) {
                        console.error('❌ Error adding lubrication:', error);
                        errorCount++;
                    }
                } else {
                    console.log('❌ No item data found for ID:', itemId);
                }
            });

            // Reset selection
            checkboxes.forEach(checkbox => checkbox.checked = false);
            handleLubricationSelection();

            console.log('📊 Final results - Added:', addedCount, 'Errors:', errorCount);

            if (addedCount > 0) {
                showToast(`Berhasil menambahkan ${addedCount} item pelumasan ke timeline!`);
            }
            if (errorCount > 0) {
                showToast(`Gagal menambahkan ${errorCount} item pelumasan!`);
            }
        }

        // Get lubrication item data by ID
        function getLubricationItemData(itemId) {
            console.log('🔍 Getting lubrication item data for ID:', itemId);
            console.log('📊 Current lubrication data:', currentLubricationData);

            const foundItem = currentLubricationData.find(item => item.id == itemId);
            console.log('🔍 Found item:', foundItem);

            return foundItem || null;
        }
    </script>
    <script src="https://unpkg.com/vis-data@latest/peer/umd/vis-data.min.js"></script>
    <script src="https://unpkg.com/vis-timeline@latest/peer/umd/vis-timeline-graph2d.min.js"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('main.layouts.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/main/process/vis-timeline.blade.php ENDPATH**/ ?>