@extends('main.layouts.main')
@section('title')
    Job Order Prepress
@endsection
@section('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

    <style>
        /* Mobile First Responsive Design */
        @media (max-width: 768px) {
            .container-fluid {
                padding: 10px;
            }

            .card {
                margin: 0;
                border-radius: 8px;
            }

            .card-body {
                padding: 15px;
            }

            h3 {
                font-size: 1.4rem;
                margin-bottom: 15px;
            }

            /* Form responsive adjustments */
            .form-control {
                font-size: 16px;
                /* Prevent zoom on iOS */
                min-height: 44px;
                /* Touch-friendly size */
            }

            /* Modern Date Input Styling */
            .date-input-wrapper {
                position: relative;
                display: flex;
                align-items: center;
            }

            .date-input-custom {
                width: 100%;
                min-height: 52px;
                padding: 15px 50px 15px 20px;
                border: 2px solid #e1e5e9;
                border-radius: 12px;
                background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
                font-size: 16px;
                font-weight: 500;
                color: #2d3748;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
                cursor: pointer;
            }

            .date-input-custom:focus {
                outline: none;
                border-color: #4299e1;
                background: #ffffff;
                box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1), 0 4px 12px rgba(0, 0, 0, 0.08);
                transform: translateY(-1px);
            }

            .date-input-custom:hover {
                border-color: #cbd5e0;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.06);
            }

            .date-input-custom.has-value {
                background: linear-gradient(145deg, #f0f8ff 0%, #e6f3ff 100%);
                border-color: #4299e1;
                color: #2b6cb0;
            }

            /* Date input validation states */
            .date-input-custom.is-valid {
                border-color: #38a169 !important;
                background: linear-gradient(145deg, #f0fff4 0%, #e6fffa 100%) !important;
                box-shadow: 0 0 0 3px rgba(56, 161, 105, 0.1) !important;
            }

            .date-input-custom.is-invalid {
                border-color: #e53e3e !important;
                background: linear-gradient(145deg, #fff5f5 0%, #fed7d7 100%) !important;
                box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1) !important;
                animation: shake 0.5s ease-in-out;
            }

            /* Shake animation for invalid dates */
            @keyframes shake {

                0%,
                100% {
                    transform: translateX(0);
                }

                25% {
                    transform: translateX(-5px);
                }

                75% {
                    transform: translateX(5px);
                }
            }

            /* Desktop date input validation */
            input[type="date"].is-valid {
                border-color: #38a169;
                background-color: #f0fff4;
                box-shadow: 0 0 0 0.2rem rgba(56, 161, 105, 0.25);
            }

            input[type="date"].is-invalid {
                border-color: #e53e3e;
                background-color: #fff5f5;
                box-shadow: 0 0 0 0.2rem rgba(229, 62, 62, 0.25);
                animation: shake 0.5s ease-in-out;
            }

            .date-input-icon {
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                width: 24px;
                height: 24px;
                background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 12px;
                pointer-events: none;
                box-shadow: 0 2px 4px rgba(66, 153, 225, 0.3);
            }

            .date-input-placeholder {
                color: #a0aec0;
                font-style: italic;
            }

            /* Flatpickr Custom Styling */
            .flatpickr-calendar {
                border-radius: 16px !important;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
                border: none !important;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            }

            .flatpickr-day.selected {
                background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%) !important;
                border-color: #3182ce !important;
            }

            .flatpickr-day:hover {
                background: #e6f3ff !important;
                border-color: #4299e1 !important;
            }

            .flatpickr-months .flatpickr-month {
                background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%) !important;
                color: white !important;
            }

            .flatpickr-weekdays {
                background: #f7fafc !important;
            }

            .flatpickr-weekday {
                color: #4a5568 !important;
                font-weight: 600 !important;
            }

            /* Mobile date input enhancements */
            @media (max-width: 768px) {
                .date-input-custom {
                    font-size: 16px !important;
                    /* Prevent zoom on iOS */
                    -webkit-appearance: none;
                    -moz-appearance: none;
                    appearance: none;
                }

                .flatpickr-calendar {
                    width: 95vw !important;
                    max-width: 350px !important;
                    left: 50% !important;
                    transform: translateX(-50%) !important;
                }

                .flatpickr-day {
                    height: 44px !important;
                    line-height: 44px !important;
                    font-size: 16px !important;
                }

                .flatpickr-months {
                    padding: 15px !important;
                }

                .flatpickr-current-month {
                    font-size: 18px !important;
                    font-weight: 600 !important;
                }

                .flatpickr-prev-month,
                .flatpickr-next-month {
                    width: 44px !important;
                    height: 44px !important;
                }

                /* Better touch targets */
                .flatpickr-day:hover,
                .flatpickr-day:focus {
                    background: #e6f3ff !important;
                    transform: scale(1.05) !important;
                    transition: all 0.2s ease !important;
                }
            }

            /* Enhanced mobile form group for dates */
            .mobile-date-group {
                margin-bottom: 20px;
                padding: 18px;
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                border-radius: 12px;
                border-left: 4px solid #007bff;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            }

            .mobile-date-group .mobile-form-label {
                font-weight: 700;
                color: #495057;
                margin-bottom: 10px;
                display: flex;
                align-items: center;
                font-size: 15px;
            }

            .mobile-date-group .mobile-form-label::before {
                content: "📅";
                margin-right: 8px;
                font-size: 16px;
            }

            /* iOS Safari specific fixes */
            @supports (-webkit-touch-callout: none) {
                input[type="date"] {
                    -webkit-appearance: none;
                    -moz-appearance: none;
                    appearance: none;
                }

                input[type="date"]::-webkit-date-and-time-value {
                    text-align: left;
                }
            }

            /* Android Chrome fixes */
            input[type="date"]::-webkit-inner-spin-button,
            input[type="date"]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            /* Firefox mobile fixes */
            @-moz-document url-prefix() {
                input[type="date"] {
                    background: #fff;
                }
            }

            /* Additional mobile date enhancements */
            .mobile-date-group input[type="date"] {
                -webkit-tap-highlight-color: transparent;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }

            .mobile-date-group input[type="date"]:focus {
                outline: none;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
            }

            /* Help text styling */
            .mobile-date-group small {
                font-size: 12px;
                color: #6c757d;
                font-style: italic;
            }

            .mobile-date-group small i {
                color: #007bff;
                margin-right: 4px;
            }

            .btn {
                min-height: 44px;
                font-size: 14px;
                padding: 10px 15px;
            }

            /* Table responsive */
            .table-responsive-mobile {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .mobile-form-group {
                margin-bottom: 20px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 8px;
                border-left: 4px solid #007bff;
            }

            .mobile-form-label {
                font-weight: 600;
                color: #495057;
                margin-bottom: 8px;
                display: block;
                font-size: 14px;
            }

            /* Job Order Mobile Layout */
            .job-order-mobile {
                background: #fff;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: 15px;
                margin-bottom: 15px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .job-order-mobile .form-label {
                font-size: 13px;
                font-weight: 600;
                margin-bottom: 5px;
            }

            .job-order-mobile .form-control {
                margin-bottom: 15px;
            }

            /* Color inputs mobile */
            .color-inputs-mobile {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                margin-top: 10px;
            }

            .color-input-group {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .color-input-group label {
                font-size: 12px;
                font-weight: 600;
                min-width: 20px;
            }

            .color-input-group input {
                flex: 1;
                min-height: 38px;
                font-size: 14px;
            }

            /* File data checkboxes mobile */
            .file-data-mobile {
                display: grid;
                grid-template-columns: 1fr;
                gap: 15px;
                margin-top: 10px;
            }

            .file-data-item {
                display: flex;
                align-items: center;
                padding: 12px;
                background: #f8f9fa;
                border-radius: 6px;
                border: 1px solid #dee2e6;
            }

            .file-data-item input[type="checkbox"] {
                margin-right: 12px;
                transform: scale(1.2);
            }

            .file-data-item label {
                margin: 0;
                font-weight: 500;
                cursor: pointer;
                flex: 1;
            }

            /* Signature section mobile */
            .signature-mobile {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin-top: 20px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 8px;
            }

            .signature-mobile .signature-box {
                text-align: center;
                padding: 15px;
                background: #fff;
                border-radius: 6px;
                border: 1px solid #dee2e6;
            }

            /* Hide desktop table on mobile */
            .desktop-table {
                display: none;
            }

            /* Page titles responsive */
            .page-titles h3 {
                font-size: 1.3rem;
            }

            .breadcrumb {
                font-size: 12px;
                margin-bottom: 10px;
            }

            /* Submit button mobile */
            .btn.w-100 {
                padding: 15px;
                font-size: 16px;
                font-weight: 600;
                border-radius: 8px;
            }

            /* Select2 mobile adjustments */
            .select2-container--default .select2-selection--single {
                height: 44px !important;
                line-height: 44px !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 44px !important;
                padding-left: 12px !important;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 42px !important;
            }
        }

        /* File Upload Area Styling */
        .file-upload-area {
            border: 2px dashed #cbd5e0;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }

        .file-upload-area:hover {
            border-color: #4299e1;
            background: linear-gradient(135deg, #e6f3ff 0%, #dbeafe 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(66, 153, 225, 0.15);
        }

        .file-upload-area i {
            color: #4299e1;
            margin-bottom: 15px;
        }

        .file-upload-area h5 {
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .file-upload-area p {
            color: #6c757d;
            margin-bottom: 5px;
            font-size: 13px;
        }

        .file-upload-area .text-warning {
            color: #f59e0b !important;
            font-weight: 600;
        }

        .file-list {
            margin-top: 15px;
        }

        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.2s ease;
        }

        .file-item:hover {
            background: #e9ecef;
            border-color: #adb5bd;
        }

        .file-info {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .file-info i {
            color: #4299e1;
            font-size: 18px;
        }

        .file-info span {
            font-weight: 500;
            color: #2d3748;
        }

        .file-size {
            color: #6c757d;
            font-size: 12px;
            margin-left: 8px;
        }

        .file-size.large {
            color: #f59e0b;
            font-weight: 600;
        }

        .remove-file {
            color: #e53e3e;
            cursor: pointer;
            font-size: 20px;
            font-weight: bold;
            padding: 0 8px;
            transition: all 0.2s ease;
        }

        .remove-file:hover {
            color: #c53030;
            transform: scale(1.2);
        }

        /* Tablet styles */
        @media (min-width: 769px) and (max-width: 1024px) {
            .card-body {
                padding: 20px;
            }

            .job-order-item .col-md-6 {
                margin-bottom: 15px;
            }

            .job-order-item .col-md-4 {
                margin-bottom: 15px;
            }
        }

        /* Desktop styles */
        @media (min-width: 769px) {
            .mobile-form-layout {
                display: none;
            }
        }

        /* Large desktop optimizations */
        @media (min-width: 1200px) {
            .container-fluid {
                max-width: 1140px;
                margin: 0 auto;
            }
        }

        /* Loading overlay to block UI while submitting */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.35);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }

        .loading-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            background: rgba(0, 0, 0, 0.55);
            padding: 18px 22px;
            border-radius: 8px;
            color: #fff;
            min-width: 180px;
        }

        .loading-spinner {
            width: 46px;
            height: 46px;
            border: 4px solid #ffffff;
            border-top-color: #764ba2;
            border-radius: 50%;
            animation: spin 0.9s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Enhanced Error Alert Styling */
        .alert-custom {
            border: none;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .alert-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
        }

        .alert-custom.alert-danger {
            background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
            color: #c53030;
            border-left: 6px solid #e53e3e;
        }

        .alert-custom.alert-danger::before {
            background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        }

        .alert-custom.alert-warning {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            color: #d97706;
            border-left: 6px solid #f59e0b;
        }

        .alert-custom.alert-warning::before {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .alert-custom.alert-info {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            color: #2563eb;
            border-left: 6px solid #3b82f6;
        }

        .alert-custom.alert-info::before {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .alert-custom .alert-icon {
            font-size: 24px;
            margin-right: 12px;
            float: left;
        }

        .alert-custom .alert-content {
            overflow: hidden;
        }

        .alert-custom .alert-title {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .alert-custom .alert-message {
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 0;
        }

        .alert-custom .alert-details {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 8px;
            padding: 12px;
            margin-top: 12px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #4a5568;
        }

        .alert-custom .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 20px;
            color: inherit;
            opacity: 0.7;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .alert-custom .close-btn:hover {
            opacity: 1;
        }

        /* Field Error Styling */
        .form-control.is-invalid {
            border-color: #e53e3e;
            box-shadow: 0 0 0 0.2rem rgba(229, 62, 62, 0.25);
        }

        .error-message {
            color: #e53e3e;
            font-size: 12px;
            margin-top: 4px;
            display: none;
            background: #fff5f5;
            padding: 6px 10px;
            border-radius: 6px;
            border-left: 3px solid #e53e3e;
        }

        /* Success Alert Styling */
        .alert-custom.alert-success {
            background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);
            color: #22543d;
            border-left: 6px solid #38a169;
        }

        .alert-custom.alert-success::before {
            background: linear-gradient(135deg, #38a169 0%, #22543d 100%);
        }

        /* Small Info Alert Styling */
        .alert-info.alert-sm {
            padding: 8px 12px;
            font-size: 12px;
            border-radius: 6px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-left: 3px solid #3b82f6;
            color: #1e40af;
        }

        .alert-info.alert-sm i {
            margin-right: 6px;
        }

        /* SweetAlert Custom Styling */
        .swal-wide {
            min-width: 500px !important;
            max-width: 600px !important;
        }

        .swal-title {
            font-size: 1.5rem !important;
            font-weight: 700 !important;
            color: #2d3748 !important;
        }

        .swal-content {
            font-size: 1rem !important;
            line-height: 1.6 !important;
            color: #4a5568 !important;
        }

        .swal2-popup {
            border-radius: 15px !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
        }

        .swal2-title {
            margin-bottom: 1rem !important;
        }

        .swal2-html-container {
            margin: 1rem 0 !important;
        }

        .swal2-confirm {
            border-radius: 25px !important;
            padding: 12px 30px !important;
            font-weight: 600 !important;
            font-size: 1rem !important;
            transition: all 0.3s ease !important;
        }

        .swal2-confirm:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2) !important;
        }

        .swal2-close {
            color: #a0aec0 !important;
            transition: color 0.3s ease !important;
        }

        .swal2-close:hover {
            color: #4a5568 !important;
        }

        /* Job Order Styling */
        .job-order-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            transition: all 0.3s ease;
        }

        .job-order-item:hover {
            background: #e9ecef;
            border-color: #adb5bd;
        }

        .job-order-item .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .btn-add-job-order {
            border-radius: 20px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add-job-order:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-remove-job {
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .btn-remove-job:hover {
            transform: scale(1.05);
        }

        .unit-job-input {
            background-color: #e9ecef;
            font-weight: 600;
            color: #495057;
        }

        .unit-job-input:focus {
            background-color: #fff;
            color: #495057;
        }

        .unit-job-input.is-valid {
            border-color: #38a169;
            box-shadow: 0 0 0 0.2rem rgba(56, 161, 105, 0.25);
            background-color: #f0fff4;
        }

        .unit-job-input.is-invalid {
            border-color: #e53e3e;
            box-shadow: 0 0 0 0.2rem rgba(229, 62, 62, 0.25);
            background-color: #fff5f5;
        }

        .unit-job-dropdown {
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .unit-job-dropdown.is-valid {
            border-color: #38a169;
            box-shadow: 0 0 0 0.2rem rgba(56, 161, 105, 0.25);
        }

        .unit-job-dropdown.is-invalid {
            border-color: #e53e3e;
            box-shadow: 0 0 0 0.2rem rgba(229, 62, 62, 0.25);
        }

        /* Mobile SweetAlert Responsive */
        @media (max-width: 768px) {
            .swal2-popup {
                width: 95% !important;
                max-width: 350px !important;
                margin: 10px !important;
                padding: 20px 15px !important;
                font-size: 14px !important;
            }

            .swal2-title {
                font-size: 1.2rem !important;
                margin-bottom: 15px !important;
                line-height: 1.3 !important;
            }

            .swal2-html-container {
                font-size: 13px !important;
                line-height: 1.4 !important;
                margin: 10px 0 !important;
            }

            .swal2-actions {
                margin-top: 20px !important;
                flex-direction: column !important;
                gap: 10px !important;
            }

            .swal2-confirm,
            .swal2-cancel {
                width: 100% !important;
                margin: 0 !important;
                padding: 12px 20px !important;
                font-size: 14px !important;
                border-radius: 6px !important;
            }

            .swal2-icon {
                width: 60px !important;
                height: 60px !important;
                margin: 10px auto 15px !important;
            }

            /* Loading overlay mobile */
            .loading-box {
                min-width: 150px;
                padding: 15px 18px;
            }

            .loading-spinner {
                width: 40px;
                height: 40px;
            }
        }
    </style>
@endsection
@section('page-title')
    Job Order Prepress
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Data Job Order Prepress</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Data Job Order Prepress</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h3>JOB ORDER TO PREPRESS</h3>
                        <form id="submitJobPrepress" method="POST" action="{{ route('prepress.job-order.submit-new') }}">
                            @csrf
                            @method('POST')
                            <hr>
                            <input type="text" name="status_job" id="status_job" class="form-control" value="OPEN"
                                hidden>
                            <!-- Desktop Date Layout -->
                            <div class="row d-none d-md-flex">
                                <div class="col-md-6">
                                    <label for="tanggal">Tanggal</label>
                                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}"
                                        min="{{ date('Y-m-d') }}" required>
                                    <div class="error-message"></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="job_deadline">Job Deadline</label>
                                    <input type="date" name="job_deadline" class="form-control"
                                        min="{{ date('Y-m-d') }}">
                                    <div class="error-message"></div>
                                </div>
                            </div>

                            <!-- Mobile Date Layout -->
                            <div class="d-md-none">
                                <div class="mobile-date-group">
                                    <label class="mobile-form-label" for="tanggal_mobile">📅 Tanggal</label>
                                    <div class="date-input-wrapper">
                                        <input type="text" data-sync="tanggal" id="tanggal_mobile"
                                            class="date-input-custom mobile-sync-input" placeholder="Pilih tanggal..."
                                            value="{{ date('Y-m-d') }}" readonly required>
                                        <div class="date-input-icon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                    </div>
                                    <div class="error-message"></div>
                                </div>
                                <div class="mobile-date-group">
                                    <label class="mobile-form-label" for="job_deadline_mobile">⏰ Job Deadline</label>
                                    <div class="date-input-wrapper">
                                        <input type="text" data-sync="job_deadline" id="job_deadline_mobile"
                                            class="date-input-custom mobile-sync-input"
                                            placeholder="Pilih tanggal deadline..." readonly>
                                        <div class="date-input-icon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                    </div>
                                    <div class="error-message"></div>
                                    <small class="text-muted mt-2 d-block">
                                        <i class="fa fa-info-circle"></i> Pilih tanggal deadline untuk job order ini
                                    </small>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Desktop Table Layout -->
                                    <table class="table table-bordered mb-2 desktop-table">
                                        <tr>
                                            <td style="width:5%"><b>No. </b></td>
                                            <td style="width:20%"><b>Customer</b></td>
                                            <td>
                                                <input type="text" name="customer" class="form-control" required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>1. </b></td>
                                            <td style="width:20%"><b>Product</b></td>
                                            <td>
                                                <input type="text" name="product" class="form-control" required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>2. </b></td>
                                            <td style="width:20%"><b>Kode Design</b></td>
                                            <td>
                                                <input type="text" name="kode_design" class="form-control" required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>3. </b></td>
                                            <td style="width:20%"><b>Dimension</b></td>
                                            <td>
                                                <input type="text" name="dimension" class="form-control" required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>4. </b></td>
                                            <td style="width:20%"><b>Material</b></td>
                                            <td>
                                                <input type="text" name="material" class="form-control" required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>5. </b></td>
                                            <td style="width:20%"><b>Total Color</b></td>
                                            <td>
                                                <input type="text" name="total_color" class="form-control" required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"></td>
                                            <td>
                                                <table class="table table-bordered mb-2" style="background:#fff;">
                                                    <tr>
                                                        <td style="width:10%">1. </td>
                                                        <td style="width:40%"><input type="text" name="color[1]"
                                                                id="color" class="form-control"></td>
                                                        <td style="width:10%">6. </td>
                                                        <td style="width:40%"><input type="text" name="color[]"
                                                                id="color" class="form-control"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:10%">2. </td>
                                                        <td style="width:40%"><input type="text" name="color[2]"
                                                                id="color" class="form-control"></td>
                                                        <td style="width:10%">7. </td>
                                                        <td style="width:40%"><input type="text" name="color[7]"
                                                                id="color" class="form-control"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:10%">3. </td>
                                                        <td style="width:40%"><input type="text" name="color[3]"
                                                                id="color" class="form-control"></td>
                                                        <td style="width:10%">8. </td>
                                                        <td style="width:40%"><input type="text" name="color[8]"
                                                                id="color" class="form-control"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:10%">4. </td>
                                                        <td style="width:40%"><input type="text" name="color[4]"
                                                                id="color" class="form-control"></td>
                                                        <td style="width:10%">9. </td>
                                                        <td style="width:40%"><input type="text" name="color[9]"
                                                                id="color" class="form-control"></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:10%">5. </td>
                                                        <td style="width:40%"><input type="text" name="color[5]"
                                                                id="color" class="form-control"></td>
                                                        <td style="width:10%">10. </td>
                                                        <td style="width:40%"><input type="text" name="color[10]"
                                                                id="color" class="form-control"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>6. </b></td>
                                            <td style="width:20%"><b>Qty Order Estimation</b></td>
                                            <td>
                                                <input type="text" name="qty_order_estimation" class="form-control"
                                                    required>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>7. </b></td>
                                            <td style="width:20%"><b>Job Order</b></td>
                                            <td>
                                                <div id="job-order-container">
                                                    <!-- Job Order Item 1 -->
                                                    <div class="job-order-item mb-3" data-job-id="1">
                                                        <div class="row align-items-end">
                                                            <div class="col-md-6">
                                                                <label class="form-label">Jenis Pekerjaan</label>
                                                                <select name="job_order[1][jenis_pekerjaan]"
                                                                    class="form-control select2-job-order" required>
                                                                    <option value="" disabled selected>-- Pilih Jenis
                                                                        Pekerjaan --</option>
                                                                    @foreach ($jenisPekerjaan as $item)
                                                                        <option value="{{ $item->id }}">
                                                                            {{ $item->nama_jenis }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <label class="form-label">Unit Job</label>
                                                                <input type="text" name="job_order[1][unit_job]"
                                                                    class="form-control unit-job-input">
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm btn-remove-job"
                                                                    style="display: none;">
                                                                    <i class="fa fa-trash"></i> Hapus
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Tombol Add Job Order -->
                                                <div class="text-center mt-2">
                                                    <button type="button" class="btn btn-success btn-sm"
                                                        id="btn-add-job-order">
                                                        <i class="fa fa-plus"></i> Tambah Job Order
                                                    </button>
                                                </div>

                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>8. </b></td>
                                            <td style="width:20%"><b>File atau Data <span class="text-danger">*</span></b>
                                            </td>
                                            <td>
                                                <div class="alert alert-info alert-sm mb-2">
                                                    <small><i class="fa fa-info-circle"></i> Pilih minimal satu jenis file
                                                        data yang akan disediakan</small>
                                                </div>
                                                <table class="table table-bordered mb-2" style="background:#fff;">
                                                    <tr>
                                                        <td style="width:10%"><input type="checkbox" name="file_data[]"
                                                                id="file_data_contoh_cetak" value="Contoh Cetak">
                                                            <label for="file_data_contoh_cetak">&nbsp;</label>
                                                        </td>
                                                        <td style="width:40%"><label for="file_data_contoh_cetak">Contoh
                                                                Cetak</label></td>
                                                        <td style="width:10%"><input type="checkbox" name="file_data[]"
                                                                id="file_data_contoh_produk" value="Contoh Produk">
                                                            <label for="file_data_contoh_produk">&nbsp;</label>
                                                        </td>
                                                        <td style="width:30%"><label for="file_data_contoh_produk">Contoh
                                                                Produk</label></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:10%"><input type="checkbox" name="file_data[]"
                                                                id="file_data_softcopy" value="File Softcopy">
                                                            <label for="file_data_softcopy">&nbsp;</label>
                                                        </td>
                                                        <td style="width:40%"><label for="file_data_softcopy">File
                                                                Softcopy</label></td>
                                                    </tr>
                                                </table>
                                                <div class="error-message" id="file_data_error"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>9. </b></td>
                                            <td style="width:20%"><b>Prioritas</b></td>
                                            <td>
                                                <select name="prioritas_job" id="prioritas_job" class="form-control"
                                                    required>
                                                    <option value disabled selected>-- Pilih Prioritas --</option>
                                                    <option value="Urgent">Urgent</option>
                                                    <option value="Normal">Normal</option>
                                                </select>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>10. </b></td>
                                            <td style="width:20%"><b>Catatan</b></td>
                                            <td>
                                                <textarea name="catatan" id="catatan" class="form-control"></textarea>
                                                <div class="error-message"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width:5%"><b>11. </b></td>
                                            <td style="width:20%"><b>Attachment</b></td>
                                            <td>
                                                <div class="file-upload-area" id="fileUploadArea">
                                                    <i class="fa fa-cloud-upload-alt"
                                                        style="font-size: 48px; color: #4299e1; margin-bottom: 15px;"></i>
                                                    <h5>Drag & Drop file atau klik untuk memilih</h5>
                                                    <p class="text-muted">Format yang didukung: PDF, DOC, DOCX, XLS, XLSX,
                                                        JPG, JPEG, PNG</p>
                                                    <p class="text-warning"><strong>⚠️ Diusahakan file di bawah 100
                                                            MB</strong></p>
                                                    <p class="text-muted"><small>Jika file melebihi 100 MB, file akan
                                                            disimpan di folder Public</small></p>
                                                    <p class="text-muted"><strong>💡 Bisa upload multiple files
                                                            sekaligus!</strong></p>
                                                    <input type="file" name="attachments[]" id="attachments"
                                                        class="form-control" style="display: none;"
                                                        accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" multiple>
                                                    <button type="button" class="btn btn-outline-primary mt-2"
                                                        onclick="document.getElementById('attachments').click()">
                                                        <i class="fa fa-folder-open"></i> Pilih File(s)
                                                    </button>
                                                </div>
                                                <div id="selectedFiles" style="display: none; margin-top: 15px;">
                                                    <h6><i class="fa fa-files-o"></i> File yang dipilih (<span
                                                            id="fileCount">0</span>):</h6>
                                                    <div id="fileList"></div>
                                                    <div class="text-center mt-2">
                                                        <button type="button" class="btn btn-outline-success btn-sm"
                                                            onclick="document.getElementById('attachments').click()">
                                                            <i class="fa fa-plus"></i> Tambah File Lagi
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="error-message" id="attachment_error"></div>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Mobile Form Layout -->
                                    <div class="mobile-form-layout">
                                        <!-- Customer -->
                                        <div class="mobile-form-group">
                                            <label class="mobile-form-label">Customer</label>
                                            <input type="text" data-sync="customer"
                                                class="form-control mobile-sync-input" required>
                                            <div class="error-message"></div>
                                        </div>

                                        <!-- Product -->
                                        <div class="mobile-form-group">
                                            <label class="mobile-form-label">1. Product</label>
                                            <input type="text" data-sync="product"
                                                class="form-control mobile-sync-input" required>
                                            <div class="error-message"></div>
                                        </div>

                                        <!-- Kode Design -->
                                        <div class="mobile-form-group">
                                            <label class="mobile-form-label">2. Kode Design</label>
                                            <input type="text" data-sync="kode_design"
                                                class="form-control mobile-sync-input" required>
                                            <div class="error-message"></div>
                                        </div>

                                        <!-- Dimension -->
                                        <div class="mobile-form-group">
                                            <label class="mobile-form-label">3. Dimension</label>
                                            <input type="text" data-sync="dimension"
                                                class="form-control mobile-sync-input" required>
                                            <div class="error-message"></div>
                                        </div>

                                        <!-- Material -->
                                        <div class="mobile-form-group">
                                            <label class="mobile-form-label">4. Material</label>
                                            <input type="text" data-sync="material"
                                                class="form-control mobile-sync-input" required>
                                            <div class="error-message"></div>
                                        </div>

                                        <!-- Total Color -->
                                        <div class="mobile-form-group">
                                            <label class="mobile-form-label">5. Total Color</label>
                                            <input type="text" data-sync="total_color"
                                                class="form-control mobile-sync-input" required>
                                            <div class="error-message"></div>

                                            <!-- Color Inputs Mobile -->
                                            <div class="color-inputs-mobile">
                                                <div class="color-input-group">
                                                    <label>1.</label>
                                                    <input type="text" name="color[1]" class="form-control">
                                                </div>
                                                <div class="color-input-group">
                                                    <label>6.</label>
                                                    <input type="text" name="color[]" class="form-control">
                                                </div>
                                                <div class="color-input-group">
                                                    <label>2.</label>
                                                    <input type="text" name="color[2]" class="form-control">
                                                </div>
                                                <div class="color-input-group">
                                                    <label>7.</label>
                                                    <input type="text" name="color[7]" class="form-control">
                                                </div>
                                                <div class="color-input-group">
                                                    <label>3.</label>
                                                    <input type="text" name="color[3]" class="form-control">
                                                </div>
                                                <div class="color-input-group">
                                                    <label>8.</label>
                                                    <input type="text" name="color[8]" class="form-control">
                                                </div>
                                                <div class="color-input-group">
                                                    <label>4.</label>
                                                    <input type="text" name="color[4]" class="form-control">
                                                </div>
                                                <div class="color-input-group">
                                                    <label>9.</label>
                                                    <input type="text" name="color[9]" class="form-control">
                                                </div>
                                                <div class="color-input-group">
                                                    <label>5.</label>
                                                    <input type="text" name="color[5]" class="form-control">
                                                </div>
                                                <div class="color-input-group">
                                                    <label>10.</label>
                                                    <input type="text" name="color[10]" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Qty Order Estimation -->
                                        <div class="mobile-form-group">
                                            <label class="mobile-form-label">6. Qty Order Estimation</label>
                                            <input type="text" data-sync="qty_order_estimation"
                                                class="form-control mobile-sync-input" required>
                                            <div class="error-message"></div>
                                        </div>

                                        <!-- Job Order -->
                                        <div class="mobile-form-group">
                                            <label class="mobile-form-label">7. Job Order</label>
                                            <div id="job-order-container-mobile">
                                                <!-- Job Order Item 1 -->
                                                <div class="job-order-mobile" data-job-id="1">
                                                    <div class="mb-3">
                                                        <label class="form-label">Jenis Pekerjaan</label>
                                                        <select name="job_order[1][jenis_pekerjaan]"
                                                            class="form-control select2-job-order mobile-sync-input"
                                                            required>
                                                            <option value="" disabled selected>-- Pilih Jenis
                                                                Pekerjaan --</option>
                                                            @foreach ($jenisPekerjaan as $item)
                                                                <option value="{{ $item->id }}">
                                                                    {{ $item->nama_jenis }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Unit Job</label>
                                                        <input type="text" name="job_order[1][unit_job]"
                                                            class="form-control unit-job-input mobile-sync-input">
                                                    </div>
                                                    <div class="text-center">
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm btn-remove-job"
                                                            style="display: none;">
                                                            <i class="fa fa-trash"></i> Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Tombol Add Job Order -->
                                            <div class="text-center mt-2">
                                                <button type="button" class="btn btn-success btn-sm"
                                                    id="btn-add-job-order-mobile">
                                                    <i class="fa fa-plus"></i> Tambah Job Order
                                                </button>
                                            </div>

                                            <div class="error-message"></div>
                                        </div>

                                        <!-- File atau Data -->
                                        <div class="mobile-form-group">
                                            <label class="mobile-form-label">8. File atau Data <span
                                                    class="text-danger">*</span></label>
                                            <div class="alert alert-info alert-sm mb-2">
                                                <small><i class="fa fa-info-circle"></i> Pilih minimal satu jenis file
                                                    data yang akan disediakan</small>
                                            </div>

                                            <div class="file-data-mobile">
                                                <div class="file-data-item">
                                                    <input type="checkbox" name="file_data[]"
                                                        id="file_data_contoh_cetak_mobile" value="Contoh Cetak">
                                                    <label for="file_data_contoh_cetak_mobile">Contoh Cetak</label>
                                                </div>
                                                <div class="file-data-item">
                                                    <input type="checkbox" name="file_data[]"
                                                        id="file_data_contoh_produk_mobile" value="Contoh Produk">
                                                    <label for="file_data_contoh_produk_mobile">Contoh Produk</label>
                                                </div>
                                                <div class="file-data-item">
                                                    <input type="checkbox" name="file_data[]"
                                                        id="file_data_softcopy_mobile" value="File Softcopy">
                                                    <label for="file_data_softcopy_mobile">File Softcopy</label>
                                                </div>
                                            </div>
                                            <div class="error-message" id="file_data_error_mobile"></div>
                                        </div>

                                        <!-- Prioritas -->
                                        <div class="mobile-form-group">
                                            <label class="mobile-form-label">9. Prioritas</label>
                                            <select data-sync="prioritas_job" class="form-control mobile-sync-input"
                                                required>
                                                <option value disabled selected>-- Pilih Prioritas --</option>
                                                <option value="Urgent">Urgent</option>
                                                <option value="Normal">Normal</option>
                                            </select>
                                            <div class="error-message"></div>
                                        </div>

                                        <!-- Catatan -->
                                        <div class="mobile-form-group">
                                            <label class="mobile-form-label">10. Catatan</label>
                                            <textarea data-sync="catatan" class="form-control mobile-sync-input"></textarea>
                                            <div class="error-message"></div>
                                        </div>

                                        <!-- Attachment -->
                                        <div class="mobile-form-group">
                                            <label class="mobile-form-label">11. Attachment</label>
                                            <div class="file-upload-area" id="fileUploadAreaMobile">
                                                <i class="fa fa-cloud-upload-alt"
                                                    style="font-size: 36px; color: #4299e1; margin-bottom: 10px;"></i>
                                                <h6 style="font-size: 14px; font-weight: 600; margin-bottom: 8px;">Klik
                                                    untuk upload file</h6>
                                                <p class="text-muted" style="font-size: 12px; margin-bottom: 5px;">PDF,
                                                    DOC, DOCX, XLS, XLSX, JPG, PNG</p>
                                                <p class="text-warning" style="font-size: 11px; margin-bottom: 5px;">
                                                    <strong>⚠️ Diusahakan < 100 MB</strong>
                                                </p>
                                                <p class="text-muted" style="font-size: 11px; margin-bottom: 10px;">Jika >
                                                    100 MB, file disimpan di Public</p>
                                                <input type="file" name="attachments[]" id="attachments_mobile"
                                                    class="form-control" style="display: none;"
                                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" multiple>
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                    onclick="document.getElementById('attachments_mobile').click()">
                                                    <i class="fa fa-folder-open"></i> Pilih File
                                                </button>
                                            </div>
                                            <div id="selectedFilesMobile" style="display: none; margin-top: 15px;">
                                                <h6 style="font-size: 13px;"><i class="fa fa-files-o"></i> File dipilih
                                                    (<span id="fileCountMobile">0</span>):</h6>
                                                <div id="fileListMobile"></div>
                                                <div class="text-center mt-2">
                                                    <button type="button" class="btn btn-outline-success btn-sm"
                                                        onclick="document.getElementById('attachments_mobile').click()">
                                                        <i class="fa fa-plus"></i> Tambah File
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="error-message" id="attachment_error_mobile"></div>
                                        </div>
                                    </div>

                                    <hr>

                                    <!-- Desktop Signature -->
                                    <div class="row d-none d-md-flex">
                                        <div class="col" style="text-align: center;">
                                            <span> <b>Issued by</b></span>
                                            <br>
                                            <br>
                                            <br>
                                            <span>{{ auth()->user()->name }}</span>
                                            <br>
                                            <span>{{ date('Y-m-d') }}</span>
                                        </div>
                                        <div class="col" style="text-align: center;">
                                            <span> <b>Received by</b></span>
                                            <br>
                                            <br>
                                            <br>
                                            <span>-</span>
                                            <br>
                                            <span>-</span>
                                        </div>
                                    </div>

                                    <!-- Mobile Signature -->
                                    <div class="signature-mobile d-md-none">
                                        <div class="signature-box">
                                            <div><strong>Issued by</strong></div>
                                            <div style="height: 40px;"></div>
                                            <div>{{ auth()->user()->name }}</div>
                                            <div>{{ date('Y-m-d') }}</div>
                                        </div>
                                        <div class="signature-box">
                                            <div><strong>Received by</strong></div>
                                            <div style="height: 40px;"></div>
                                            <div>-</div>
                                            <div>-</div>
                                        </div>
                                    </div>
                                </div>

                            </div>


                            <button type="submit" class="btn btn-info my-4 w-100" id="submitButton">Submit</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Loading Overlay -->
        <div id="loadingOverlay" class="loading-overlay">
            <div class="loading-box">
                <div class="loading-spinner"></div>
                <div>Sedang menyimpan...</div>
            </div>
        </div>
    @endsection
    @section('scripts')
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.flash.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.print.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.keyTable.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.select.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/pdfmake.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/vfs_fonts.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/assets/pages/datatables-demo.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>


        <script>
            // Setup CSRF token untuk semua AJAX request
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Helper function untuk menampilkan SweetAlert yang lebih user-friendly
            function showSweetAlert(type, title, message, details = null) {
                let icon = '';
                let confirmButtonColor = '';

                switch (type) {
                    case 'error':
                        icon = 'error';
                        confirmButtonColor = '#e53e3e';
                        break;
                    case 'warning':
                        icon = 'warning';
                        confirmButtonColor = '#f59e0b';
                        break;
                    case 'info':
                        icon = 'info';
                        confirmButtonColor = '#3b82f6';
                        break;
                    case 'success':
                        icon = 'success';
                        confirmButtonColor = '#38a169';
                        break;
                }

                let htmlContent = `<div class="text-left">${message}`;
                if (details) {
                    htmlContent += `<hr><div class="mt-3"><strong>Detail:</strong><br>${details}</div>`;
                }
                htmlContent += '</div>';

                Swal.fire({
                    icon: icon,
                    title: title,
                    html: htmlContent,
                    confirmButtonText: 'OK',
                    confirmButtonColor: confirmButtonColor,
                    showCloseButton: true,
                    customClass: {
                        popup: 'swal-wide',
                        title: 'swal-title',
                        content: 'swal-content'
                    }
                });
            }

            // Helper function untuk menampilkan field errors
            function showFieldErrors(errors) {
                // Reset semua field error
                $('.form-control').removeClass('is-invalid');
                $('.error-message').hide();

                // Tampilkan error untuk setiap field
                $.each(errors, function(field, messages) {
                    let fieldElement = $(`[name="${field}"]`);
                    if (fieldElement.length) {
                        fieldElement.addClass('is-invalid');

                        // Buat atau update error message
                        let errorDiv = fieldElement.next('.error-message');
                        if (errorDiv.length === 0) {
                            errorDiv = $('<div class="error-message"></div>');
                            fieldElement.after(errorDiv);
                        }

                        errorDiv.html(Array.isArray(messages) ? messages.join('<br>') : messages).show();
                    }
                });
            }

            // Helper function untuk mengkonversi menit ke format jam
            function formatMinutesToHours(minutes) {
                if (minutes === null || minutes === undefined || minutes === '') {
                    return '0 jam 0 menit';
                }

                minutes = parseInt(minutes);
                if (isNaN(minutes) || minutes < 0) {
                    return '0 jam 0 menit';
                }

                const hours = Math.floor(minutes / 60);
                const remainingMinutes = minutes % 60;

                if (hours === 0) {
                    return `${remainingMinutes} menit`;
                } else if (remainingMinutes === 0) {
                    return `${hours} jam`;
                } else {
                    return `${hours} jam ${remainingMinutes} menit`;
                }
            }

            $(document).ready(function() {
                var isSubmittingJob = false;
                var jobOrderCounter = 1;
                var isSyncing = false; // Flag untuk mencegah infinite loop sinkronisasi

                // Inisialisasi Date Picker untuk Mobile
                initializeDatePickers();

                // Inisialisasi sinkronisasi input mobile-desktop
                initializeInputSync();

                // Inisialisasi File Upload
                initializeFileUpload();

                // Inisialisasi Select2 untuk job order
                initializeSelect2JobOrder();

                // Event handler untuk tombol add job order (desktop dan mobile)
                $(document).on('click', '#btn-add-job-order, #btn-add-job-order-mobile', function() {
                    addJobOrder();
                });

                // Event handler untuk tombol remove job order
                $(document).on('click', '.btn-remove-job', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    console.log('Remove button clicked');
                    removeJobOrder($(this));
                });

                // Event handler untuk perubahan jenis pekerjaan
                $(document).on('change', '.select2-job-order', function() {
                    if (isSyncing) return; // Skip jika sedang sinkronisasi

                    var jobOrderId = $(this).closest('[data-job-id]').data('job-id');
                    var selectedValue = $(this).val();

                    // Sinkronisasi jenis pekerjaan antara desktop dan mobile
                    syncJenisPekerjaanBetweenLayouts(jobOrderId, selectedValue);

                    // Update unit job
                    updateUnitJob($(this));
                });

                // Cek limit job order saat tanggal deadline berubah
                // $('input[name="job_deadline"]').on('change', function() {
                //     checkJobOrderLimit($(this).val());
                // });

                // Fungsi untuk inisialisasi Date Picker
                function initializeDatePickers() {
                    // Konfigurasi Flatpickr
                    const flatpickrConfig = {
                        dateFormat: "Y-m-d",
                        locale: "id",
                        allowInput: true,
                        clickOpens: true,
                        theme: "material_blue",
                        animate: true,
                        position: "auto center",
                        mobile: true,
                        monthSelectorType: "dropdown",
                        yearSelectorType: "dropdown",
                        onOpen: function(selectedDates, dateStr, instance) {
                            instance.element.classList.add('has-value');
                        },
                        onClose: function(selectedDates, dateStr, instance) {
                            if (dateStr) {
                                instance.element.classList.add('has-value');
                                instance.element.value = dateStr;
                            } else {
                                instance.element.classList.remove('has-value');
                            }
                        },
                        onChange: function(selectedDates, dateStr, instance) {
                            if (dateStr) {
                                instance.element.classList.add('has-value');
                                // Trigger change event untuk validasi
                                $(instance.element).trigger('change');
                            } else {
                                instance.element.classList.remove('has-value');
                            }
                        }
                    };

                    // Inisialisasi untuk mobile date inputs
                    if ($('#tanggal_mobile').length) {
                        const tanggalPicker = flatpickr("#tanggal_mobile", {
                            ...flatpickrConfig,
                            defaultDate: "{{ date('Y-m-d') }}",
                            minDate: "{{ date('Y-m-d') }}", // Tidak bisa pilih tanggal sebelum hari ini
                            maxDate: null, // Tidak ada batas maksimal
                            disable: [
                                function(date) {
                                    // Disable semua tanggal sebelum hari ini
                                    return date < new Date().setHours(0, 0, 0, 0);
                                }
                            ]
                        });

                        // Set initial state
                        if ($('#tanggal_mobile').val()) {
                            $('#tanggal_mobile').addClass('has-value');
                        }
                    }

                    if ($('#job_deadline_mobile').length) {
                        const deadlinePicker = flatpickr("#job_deadline_mobile", {
                            ...flatpickrConfig,
                            minDate: "{{ date('Y-m-d') }}", // Tidak bisa pilih tanggal sebelum hari ini
                            maxDate: null, // Tidak ada batas maksimal
                            placeholder: "Pilih tanggal deadline...",
                            disable: [
                                function(date) {
                                    // Disable semua tanggal sebelum hari ini
                                    return date < new Date().setHours(0, 0, 0, 0);
                                }
                            ]
                        });
                    }

                    // Sync dengan desktop inputs jika ada
                    $('#tanggal_mobile').on('change', function() {
                        $('input[name="tanggal"]').not('#tanggal_mobile').val($(this).val());
                    });

                    $('#job_deadline_mobile').on('change', function() {
                        $('input[name="job_deadline"]').not('#job_deadline_mobile').val($(this).val());
                    });

                    // Sync dari desktop ke mobile
                    $('input[name="tanggal"]').not('#tanggal_mobile').on('change', function() {
                        $('#tanggal_mobile').val($(this).val());
                        if ($(this).val()) {
                            $('#tanggal_mobile').addClass('has-value');
                        }
                    });

                    $('input[name="job_deadline"]').not('#job_deadline_mobile').on('change', function() {
                        $('#job_deadline_mobile').val($(this).val());
                        if ($(this).val()) {
                            $('#job_deadline_mobile').addClass('has-value');
                        }
                    });

                    // Validasi tanggal untuk memastikan tidak mundur dari hari ini
                    function validateDateNotPast(inputElement) {
                        var selectedDate = new Date(inputElement.val());
                        var today = new Date();
                        today.setHours(0, 0, 0, 0); // Set ke awal hari

                        if (selectedDate < today) {
                            inputElement.addClass('is-invalid');
                            var errorDiv = inputElement.next('.error-message');
                            if (errorDiv.length === 0) {
                                errorDiv = $('<div class="error-message"></div>');
                                inputElement.after(errorDiv);
                            }
                            errorDiv.html('Tanggal tidak boleh mundur dari hari ini').show();

                            // Reset ke tanggal hari ini
                            var todayString = today.toISOString().split('T')[0];
                            inputElement.val(todayString);

                            // Show alert
                            showSweetAlert('warning', 'Tanggal Tidak Valid',
                                'Tanggal yang dipilih tidak boleh mundur dari hari ini.',
                                'Tanggal otomatis diubah ke hari ini.');

                            return false;
                        } else {
                            inputElement.removeClass('is-invalid');
                            inputElement.next('.error-message').hide();
                            return true;
                        }
                    }

                    // Event listener untuk validasi tanggal desktop
                    $('input[name="tanggal"], input[name="job_deadline"]').on('change', function() {
                        validateDateNotPast($(this));
                    });

                    // Event listener untuk validasi tanggal mobile
                    $('#tanggal_mobile, #job_deadline_mobile').on('change', function() {
                        validateDateNotPast($(this));
                    });
                }

                // Fungsi untuk inisialisasi sinkronisasi input mobile-desktop
                function initializeInputSync() {
                    // Sinkronisasi dari mobile ke desktop
                    $('.mobile-sync-input').on('input change', function() {
                        var syncTarget = $(this).data('sync');
                        var value = $(this).val();

                        // Update desktop input dengan name yang sama
                        $(`[name="${syncTarget}"]`).not('.mobile-sync-input').val(value);

                        console.log(`Syncing ${syncTarget}: ${value}`);
                    });

                    // Sinkronisasi dari desktop ke mobile
                    $('input[name], select[name], textarea[name]').not('.mobile-sync-input').on('input change',
                        function() {
                            var fieldName = $(this).attr('name');
                            var value = $(this).val();

                            // Update mobile input dengan data-sync yang sama
                            $(`.mobile-sync-input[data-sync="${fieldName}"]`).val(value);

                            console.log(`Syncing from desktop ${fieldName}: ${value}`);
                        });

                    // Disable validation untuk mobile inputs di desktop view
                    function toggleMobileValidation() {
                        if (window.innerWidth >= 768) {
                            // Desktop view - disable mobile inputs, enable desktop
                            $('.mobile-sync-input').removeAttr('required').prop('disabled', true);
                            $('input[name], select[name], textarea[name]').not('.mobile-sync-input').prop('disabled',
                                false);
                        } else {
                            // Mobile view - enable mobile inputs, disable desktop
                            $('.mobile-sync-input').prop('disabled', false);
                            // Enable checkbox file_data di mobile (jangan disable)
                            $('input[name="file_data[]"]').prop('disabled', false);
                            // Disable desktop inputs kecuali checkbox file_data
                            $('input[name], select[name], textarea[name]').not('.mobile-sync-input').not('input[name="file_data[]"]').removeAttr(
                                'required').prop('disabled', true);
                        }
                    }

                    // Initial call
                    toggleMobileValidation();

                    // On window resize
                    $(window).on('resize', function() {
                        toggleMobileValidation();
                    });

                    // Before form submit, enable all inputs and sync data
                    $('#submitJobPrepress').on('submit', function(e) {
                        // Validasi tanggal sebelum submit
                        var isDateValid = true;
                        var today = new Date();
                        today.setHours(0, 0, 0, 0);

                        // Cek tanggal
                        var tanggalValue = $('input[name="tanggal"]').val();
                        if (tanggalValue) {
                            var tanggalDate = new Date(tanggalValue);
                            if (tanggalDate < today) {
                                showSweetAlert('error', 'Tanggal Tidak Valid',
                                    'Tanggal tidak boleh mundur dari hari ini.',
                                    'Silakan pilih tanggal hari ini atau setelahnya.');
                                isDateValid = false;
                            }
                        }

                        // Cek job deadline
                        var deadlineValue = $('input[name="job_deadline"]').val();
                        if (deadlineValue) {
                            var deadlineDate = new Date(deadlineValue);
                            if (deadlineDate < today) {
                                showSweetAlert('error', 'Job Deadline Tidak Valid',
                                    'Job deadline tidak boleh mundur dari hari ini.',
                                    'Silakan pilih tanggal hari ini atau setelahnya.');
                                isDateValid = false;
                            }
                        }

                        if (!isDateValid) {
                            e.preventDefault();
                            return false;
                        }

                        // Enable all inputs for submission
                        $('input, select, textarea').prop('disabled', false);

                        // Final sync from active layout to hidden layout
                        if (window.innerWidth >= 768) {
                            // Desktop active - sync to mobile
                            $('input[name], select[name], textarea[name]').not('.mobile-sync-input').each(
                                function() {
                                    var fieldName = $(this).attr('name');
                                    var value = $(this).val();
                                    $(`.mobile-sync-input[data-sync="${fieldName}"]`).val(value);
                                });
                        } else {
                            // Mobile active - sync to desktop
                            $('.mobile-sync-input').each(function() {
                                var syncTarget = $(this).data('sync');
                                var value = $(this).val();
                                $(`[name="${syncTarget}"]`).not('.mobile-sync-input').val(value);
                            });
                        }
                    });
                }

                // Event listener untuk menghapus is-invalid saat user mengetik
                $('input[name="dimension"]').on('input change', function() {
                    if ($(this).val().trim().toLowerCase() !== 'cek file' && $(this).val().trim() !== '') {
                        $(this).removeClass('is-invalid');
                    }
                });

                // Event listener untuk mobile dimension
                $('.mobile-sync-input[data-sync="dimension"]').on('input change', function() {
                    if ($(this).val().trim().toLowerCase() !== 'cek file' && $(this).val().trim() !== '') {
                        $(this).removeClass('is-invalid');
                        // Sync ke desktop dan remove is-invalid juga
                        $('input[name="dimension"]').removeClass('is-invalid');
                    }
                });

                // Event listener untuk menghapus is-invalid saat user mengetik material
                $('input[name="material"]').on('input change', function() {
                    if ($(this).val().trim().toLowerCase() !== 'cek file' && $(this).val().trim() !== '') {
                        $(this).removeClass('is-invalid');
                    }
                });

                // Event listener untuk mobile material
                $('.mobile-sync-input[data-sync="material"]').on('input change', function() {
                    if ($(this).val().trim().toLowerCase() !== 'cek file' && $(this).val().trim() !== '') {
                        $(this).removeClass('is-invalid');
                        // Sync ke desktop dan remove is-invalid juga
                        $('input[name="material"]').removeClass('is-invalid');
                    }
                });

                // Fungsi untuk inisialisasi File Upload
                function initializeFileUpload() {
                    var selectedFiles = [];
                    const MAX_FILE_SIZE = 100 * 1024 * 1024; // 100 MB dalam bytes

                    // Fungsi untuk format file size
                    function formatFileSize(bytes) {
                        if (bytes === 0) return '0 Bytes';
                        const k = 1024;
                        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                        const i = Math.floor(Math.log(bytes) / Math.log(k));
                        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
                    }

                    // Fungsi untuk update file list
                    function updateFileList() {
                        var fileListHtml = '';
                        var totalSize = 0;

                        selectedFiles.forEach(function(file, index) {
                            var fileSize = file.size;
                            totalSize += fileSize;
                            var fileSizeFormatted = formatFileSize(fileSize);
                            var isLarge = fileSize >= MAX_FILE_SIZE;

                            fileListHtml += `
                                <div class="file-item">
                                    <div class="file-info">
                                        <i class="fa fa-file"></i>
                                        <span>${file.name}</span>
                                        <span class="file-size ${isLarge ? 'large' : ''}">(${fileSizeFormatted})</span>
                                        ${isLarge ? '<span class="badge badge-warning ml-2">Akan disimpan di Public</span>' : ''}
                                    </div>
                                    <span class="remove-file" onclick="removeFile(${index})" title="Hapus file">×</span>
                                </div>
                            `;
                        });

                        // Update desktop
                        $('#fileList').html(fileListHtml);
                        $('#fileCount').text(selectedFiles.length);

                        // Update mobile
                        $('#fileListMobile').html(fileListHtml);
                        $('#fileCountMobile').text(selectedFiles.length);

                        // Show/hide file list
                        if (selectedFiles.length > 0) {
                            $('#selectedFiles').show();
                            $('#fileUploadArea').hide();
                            $('#selectedFilesMobile').show();
                            $('#fileUploadAreaMobile').hide();
                        } else {
                            $('#selectedFiles').hide();
                            $('#fileUploadArea').show();
                            $('#selectedFilesMobile').hide();
                            $('#fileUploadAreaMobile').show();
                        }
                    }

                    // Fungsi untuk remove file
                    window.removeFile = function(index) {
                        selectedFiles.splice(index, 1);
                        updateFileList();

                        // Update file input
                        var dt = new DataTransfer();
                        selectedFiles.forEach(function(file) {
                            dt.items.add(file);
                        });
                        $('#attachments')[0].files = dt.files;
                        $('#attachments_mobile')[0].files = dt.files;
                    };

                    // Validasi file
                    function validateFile(file) {
                        var allowedTypes = ['application/pdf', 'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'image/jpeg', 'image/jpg', 'image/png'
                        ];
                        var maxSize = 500 * 1024 * 1024; // 500 MB max (untuk file besar yang akan disimpan di Public)

                        // Cek tipe file
                        if (!allowedTypes.includes(file.type)) {
                            showSweetAlert('error', 'Format File Tidak Didukung',
                                `File "${file.name}" memiliki format yang tidak didukung.`,
                                'Format yang didukung: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG');
                            return false;
                        }

                        // Cek ukuran file
                        if (file.size > maxSize) {
                            showSweetAlert('error', 'File Terlalu Besar',
                                `File "${file.name}" terlalu besar (${formatFileSize(file.size)}).`,
                                `Maksimal ukuran file: ${formatFileSize(maxSize)}`);
                            return false;
                        }

                        return true;
                    }

                    // Handle file selection (desktop)
                    $('#attachments').on('change', function(e) {
                        var files = Array.from(e.target.files);
                        var validFiles = [];

                        files.forEach(function(file) {
                            if (validateFile(file)) {
                                // Cek duplikasi
                                var exists = selectedFiles.some(function(existingFile) {
                                    return existingFile.name === file.name && existingFile
                                        .size === file.size;
                                });

                                if (!exists) {
                                    validFiles.push(file);
                                }
                            }
                        });

                        // Tambahkan file yang valid
                        selectedFiles = selectedFiles.concat(validFiles);

                        // Update file input
                        var dt = new DataTransfer();
                        selectedFiles.forEach(function(file) {
                            dt.items.add(file);
                        });
                        $('#attachments')[0].files = dt.files;
                        $('#attachments_mobile')[0].files = dt.files;

                        updateFileList();
                    });

                    // Handle file selection (mobile)
                    $('#attachments_mobile').on('change', function(e) {
                        var files = Array.from(e.target.files);
                        var validFiles = [];

                        files.forEach(function(file) {
                            if (validateFile(file)) {
                                // Cek duplikasi
                                var exists = selectedFiles.some(function(existingFile) {
                                    return existingFile.name === file.name && existingFile
                                        .size === file.size;
                                });

                                if (!exists) {
                                    validFiles.push(file);
                                }
                            }
                        });

                        // Tambahkan file yang valid
                        selectedFiles = selectedFiles.concat(validFiles);

                        // Update file input
                        var dt = new DataTransfer();
                        selectedFiles.forEach(function(file) {
                            dt.items.add(file);
                        });
                        $('#attachments')[0].files = dt.files;
                        $('#attachments_mobile')[0].files = dt.files;

                        updateFileList();
                    });

                    // Drag and drop untuk desktop
                    $('#fileUploadArea').on('dragover', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $(this).css('border-color', '#4299e1');
                        $(this).css('background', 'linear-gradient(135deg, #e6f3ff 0%, #dbeafe 100%)');
                    });

                    $('#fileUploadArea').on('dragleave', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $(this).css('border-color', '#cbd5e0');
                        $(this).css('background', 'linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%)');
                    });

                    $('#fileUploadArea').on('drop', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $(this).css('border-color', '#cbd5e0');
                        $(this).css('background', 'linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%)');

                        var files = Array.from(e.originalEvent.dataTransfer.files);
                        var validFiles = [];

                        files.forEach(function(file) {
                            if (validateFile(file)) {
                                var exists = selectedFiles.some(function(existingFile) {
                                    return existingFile.name === file.name && existingFile
                                        .size === file.size;
                                });

                                if (!exists) {
                                    validFiles.push(file);
                                }
                            }
                        });

                        selectedFiles = selectedFiles.concat(validFiles);

                        var dt = new DataTransfer();
                        selectedFiles.forEach(function(file) {
                            dt.items.add(file);
                        });
                        $('#attachments')[0].files = dt.files;
                        $('#attachments_mobile')[0].files = dt.files;

                        updateFileList();
                    });

                    // Click to upload
                    $('#fileUploadArea').on('click', function() {
                        $('#attachments').click();
                    });

                    $('#fileUploadAreaMobile').on('click', function() {
                        $('#attachments_mobile').click();
                    });
                }

                // Fungsi untuk inisialisasi Select2 untuk job order
                function initializeSelect2JobOrder() {
                    $('.select2-job-order').select2({
                        placeholder: "Pilih jenis pekerjaan...",
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('body')
                    });
                }

                // Fungsi untuk menambah job order baru
                function addJobOrder() {
                    jobOrderCounter++;

                    // Desktop layout
                    var newJobOrderDesktop = `
                        <div class="job-order-item mb-3" data-job-id="${jobOrderCounter}">
                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label">Jenis Pekerjaan</label>
                                    <select name="job_order[${jobOrderCounter}][jenis_pekerjaan]" class="form-control select2-job-order" required>
                                        <option value="" disabled selected>-- Pilih Jenis Pekerjaan --</option>
                                        @foreach ($jenisPekerjaan as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->nama_jenis }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Unit Job</label>
                                    <input type="text" name="job_order[${jobOrderCounter}][unit_job]" class="form-control unit-job-input" readonly>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-danger btn-sm btn-remove-job">
                                        <i class="fa fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;

                    // Mobile layout
                    var newJobOrderMobile = `
                        <div class="job-order-mobile" data-job-id="${jobOrderCounter}">
                            <div class="mb-3">
                                <label class="form-label">Jenis Pekerjaan</label>
                                <select name="job_order[${jobOrderCounter}][jenis_pekerjaan]" class="form-control select2-job-order mobile-sync-input" required>
                                    <option value="" disabled selected>-- Pilih Jenis Pekerjaan --</option>
                                    @foreach ($jenisPekerjaan as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->nama_jenis }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Unit Job</label>
                                <input type="text" name="job_order[${jobOrderCounter}][unit_job]" class="form-control unit-job-input mobile-sync-input" readonly>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-danger btn-sm btn-remove-job">
                                    <i class="fa fa-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    `;

                    $('#job-order-container').append(newJobOrderDesktop);
                    $('#job-order-container-mobile').append(newJobOrderMobile);

                    // Inisialisasi Select2 untuk job order baru
                    $(`[name="job_order[${jobOrderCounter}][jenis_pekerjaan]"]`).select2({
                        placeholder: "Pilih jenis pekerjaan...",
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('body')
                    });

                    // Update tombol hapus untuk semua item
                    updateRemoveButtons();
                }

                // Fungsi untuk menghapus job order
                function removeJobOrder(button) {
                    var jobId = button.closest('[data-job-id]').data('job-id');

                    console.log('Removing job order with ID:', jobId);
                    console.log('Before removal - Desktop items:', $('.job-order-item').length, 'Mobile items:', $(
                        '.job-order-mobile').length);

                    // SUPER AGGRESSIVE CLEANUP - Remove everything related to this job ID

                    // 1. Remove by data-job-id
                    $(`[data-job-id="${jobId}"]`).remove();

                    // 2. Remove by name attribute patterns
                    $(`[name*="job_order[${jobId}]"]`).closest('.job-order-item, .job-order-mobile, .mb-3, .row')
                        .remove();

                    // 3. Remove any parent containers that contain this job ID
                    $(`select[name*="job_order[${jobId}]"], input[name*="job_order[${jobId}]"]`).each(function() {
                        $(this).closest('.job-order-item, .job-order-mobile, .mb-3, .row, div').remove();
                    });

                    // 4. Remove any remaining elements with this job ID
                    $('*').each(function() {
                        var $this = $(this);
                        if ($this.find(`[name*="job_order[${jobId}]"]`).length > 0) {
                            $this.remove();
                        }
                    });

                    // 5. Force remove any remaining unit-job-input with this job ID
                    $(`.unit-job-input[name*="job_order[${jobId}]"]`).closest('*').remove();

                    // 6. Remove any select2-job-order with this job ID
                    $(`.select2-job-order[name*="job_order[${jobId}]"]`).closest('*').remove();

                    console.log('After removal - Desktop items:', $('.job-order-item').length, 'Mobile items:', $(
                        '.job-order-mobile').length);

                    // Final cleanup - remove any remaining orphaned elements
                    cleanupOrphanedJobOrders();

                    updateRemoveButtons();
                }

                // Fungsi untuk membersihkan elemen job order yang tersisa
                function cleanupOrphanedJobOrders() {
                    console.log('Starting orphaned cleanup...');

                    // Remove any job order items that don't have proper structure
                    $('.job-order-item, .job-order-mobile').each(function() {
                        var $this = $(this);
                        var hasSelect = $this.find('select[name*="jenis_pekerjaan"]').length > 0;
                        var hasInput = $this.find('input[name*="unit_job"]').length > 0;

                        if (!hasSelect || !hasInput) {
                            console.log('Removing orphaned job order element:', $this);
                            $this.remove();
                        }
                    });

                    // EXTREME CLEANUP - Remove any remaining job order elements
                    $('*').each(function() {
                        var $this = $(this);
                        var hasJobOrderElements = $this.find(
                            'select[name*="job_order"], input[name*="job_order"]').length > 0;
                        var isJobOrderContainer = $this.hasClass('job-order-item') || $this.hasClass(
                            'job-order-mobile');

                        if (hasJobOrderElements && !isJobOrderContainer) {
                            console.log('Removing element with job order children:', $this);
                            $this.remove();
                        }
                    });

                    // Ensure desktop and mobile are in sync
                    var desktopCount = $('.job-order-item').length;
                    var mobileCount = $('.job-order-mobile').length;

                    console.log('Final count - Desktop:', desktopCount, 'Mobile:', mobileCount);

                    if (desktopCount !== mobileCount) {
                        console.log('Count mismatch detected - Desktop:', desktopCount, 'Mobile:', mobileCount);

                        // If mobile has more items, remove extras
                        if (mobileCount > desktopCount) {
                            $('.job-order-mobile').slice(desktopCount).remove();
                        }
                        // If desktop has more items, remove extras
                        else if (desktopCount > mobileCount) {
                            $('.job-order-item').slice(mobileCount).remove();
                        }
                    }

                    // Final verification
                    var finalDesktopCount = $('.job-order-item').length;
                    var finalMobileCount = $('.job-order-mobile').length;
                    console.log('Final verification - Desktop:', finalDesktopCount, 'Mobile:', finalMobileCount);
                }

                // Fungsi untuk update tombol hapus
                function updateRemoveButtons() {
                    var jobOrderItemsDesktop = $('.job-order-item');
                    var jobOrderItemsMobile = $('.job-order-mobile');

                    console.log('Desktop items:', jobOrderItemsDesktop.length, 'Mobile items:', jobOrderItemsMobile
                        .length);

                    if (jobOrderItemsDesktop.length <= 1) {
                        // Jika hanya ada 1 item atau kurang, sembunyikan tombol hapus
                        jobOrderItemsDesktop.find('.btn-remove-job').hide();
                        jobOrderItemsMobile.find('.btn-remove-job').hide();
                    } else {
                        // Jika ada lebih dari 1 item, tampilkan semua tombol hapus
                        jobOrderItemsDesktop.find('.btn-remove-job').show();
                        jobOrderItemsMobile.find('.btn-remove-job').show();
                    }

                    // Force sync between desktop and mobile
                    syncJobOrders();
                }

                // Fungsi untuk sinkronisasi job orders antara desktop dan mobile
                function syncJobOrders() {
                    var desktopItems = $('.job-order-item');
                    var mobileItems = $('.job-order-mobile');

                    // Pastikan jumlah item sama
                    if (desktopItems.length !== mobileItems.length) {
                        console.log('Job order count mismatch - Desktop:', desktopItems.length, 'Mobile:', mobileItems
                            .length);

                        // Jika ada perbedaan, hapus semua dan buat ulang yang pertama
                        if (desktopItems.length > 0 && mobileItems.length === 0) {
                            // Desktop ada, mobile tidak - buat ulang mobile
                            var firstDesktop = desktopItems.first();
                            var jobId = firstDesktop.data('job-id');
                            // Recreate mobile version
                        } else if (mobileItems.length > 0 && desktopItems.length === 0) {
                            // Mobile ada, desktop tidak - buat ulang desktop
                            var firstMobile = mobileItems.first();
                            var jobId = firstMobile.data('job-id');
                            // Recreate desktop version
                        }
                    }
                }

                // Fungsi untuk update unit job berdasarkan jenis pekerjaan yang dipilih
                function updateUnitJob(selectElement) {
                    var selectedOption = selectElement.find('option:selected');
                    var jenisPekerjaan = selectedOption.val();
                    var jobOrderItem = selectElement.closest('.job-order-item, .job-order-mobile');
                    var unitJobInput = jobOrderItem.find('.unit-job-input');
                    var jobOrderId = jobOrderItem.data('job-id');

                    console.log('Updating unit job for Job Order ID:', jobOrderId);
                    console.log('Selected jenis pekerjaan:', jenisPekerjaan);

                    // Reset unit job input dan hapus dropdown yang ada
                    resetUnitJobField(unitJobInput, jobOrderId);

                    if (!jenisPekerjaan) {
                        return;
                    }

                    // AJAX call untuk mengambil unit job
                    $.ajax({
                        url: "{{ route('prepress.job-order.get-unit-job') }}",
                        type: "POST",
                        data: {
                            jenis_pekerjaan: jenisPekerjaan,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log('AJAX Response for Job Order ID:', jobOrderId, response);

                            if (response.success && response.unit_jobs && response.unit_jobs.length > 0) {
                                // Update unit job untuk desktop dan mobile
                                updateUnitJobForBothLayouts(jobOrderId, response.unit_jobs);
                            } else {
                                // Reset untuk desktop dan mobile
                                resetUnitJobForBothLayouts(jobOrderId);
                                console.error('Unit job tidak ditemukan untuk Job Order ID:', jobOrderId,
                                    response.message);
                            }
                        },
                        error: function(xhr) {
                            console.error('Error mengambil unit job untuk Job Order ID:', jobOrderId, xhr);

                            // Reset untuk desktop dan mobile
                            resetUnitJobForBothLayouts(jobOrderId);

                            // Tampilkan error message
                            var errorMessage = 'Gagal mengambil unit job';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            showSweetAlert('error', 'Error Unit Job', errorMessage,
                                'Silakan coba lagi atau hubungi administrator.');
                        }
                    });
                }

                // Fungsi untuk reset unit job field dan hapus dropdown
                function resetUnitJobField(unitJobInput, jobOrderId) {
                    console.log('Resetting unit job field for Job Order ID:', jobOrderId);

                    // Reset input value
                    unitJobInput.val('');
                    unitJobInput.removeClass('is-invalid is-valid');

                    // Hapus dropdown yang ada (jika ada)
                    var existingDropdown = unitJobInput.next('.unit-job-dropdown');
                    if (existingDropdown.length > 0) {
                        console.log('Removing existing dropdown for Job Order ID:', jobOrderId);
                        existingDropdown.remove();
                    }

                    // Tampilkan kembali input field
                    unitJobInput.show();

                    console.log('Unit job field reset completed for Job Order ID:', jobOrderId);
                }

                // Fungsi untuk membuat dropdown unit job
                function createUnitJobDropdown(unitJobInput, unitJobs, jobOrderId) {
                    console.log('Creating dropdown for Job Order ID:', jobOrderId, 'with unit jobs:', unitJobs);

                    // Hapus dropdown yang sudah ada dengan lebih spesifik
                    var existingDropdown = unitJobInput.siblings('.unit-job-dropdown');
                    if (existingDropdown.length > 0) {
                        console.log('Removing existing dropdown for Job Order ID:', jobOrderId);
                        existingDropdown.remove();
                    }

                    // Buat dropdown baru dengan ID yang unik
                    var dropdownId = 'unit-job-dropdown-' + jobOrderId;
                    var dropdown = $('<select class="form-control unit-job-dropdown" id="' + dropdownId + '" name="' +
                        unitJobInput.attr('name') + '">');

                    // Jika hanya ada 1 unit job, set sebagai selected
                    if (unitJobs.length === 1) {
                        dropdown.append('<option value="' + unitJobs[0] + '" selected>' + unitJobs[0] + '</option>');
                        // Auto-set value ke hidden input
                        unitJobInput.val(unitJobs[0]);
                    } else {
                        dropdown.append('<option value="" disabled selected>-- Pilih Unit Job --</option>');
                        unitJobs.forEach(function(unitJob) {
                            dropdown.append('<option value="' + unitJob + '">' + unitJob + '</option>');
                        });
                    }

                    // Event handler untuk dropdown
                    dropdown.on('change', function() {
                        var selectedUnitJob = $(this).val();
                        console.log('Dropdown changed for Job Order ID:', jobOrderId, 'Selected:',
                            selectedUnitJob);

                        // Update current input
                        if (selectedUnitJob) {
                            unitJobInput.val(selectedUnitJob);
                            unitJobInput.removeClass('is-invalid');
                            unitJobInput.addClass('is-valid');
                            $(this).removeClass('is-invalid');
                            $(this).addClass('is-valid');
                        } else {
                            unitJobInput.val('');
                            unitJobInput.removeClass('is-valid');
                            unitJobInput.addClass('is-invalid');
                            $(this).removeClass('is-valid');
                            $(this).addClass('is-invalid');
                        }

                        // Sinkronisasi dengan layout lain (desktop <-> mobile)
                        syncUnitJobBetweenLayouts(jobOrderId, selectedUnitJob);
                    });

                    // Jika hanya ada 1 unit job, trigger change event untuk set valid state
                    if (unitJobs.length === 1) {
                        dropdown.trigger('change');
                    }

                    // Ganti input dengan dropdown
                    unitJobInput.hide();
                    unitJobInput.after(dropdown);

                    // Inisialisasi Select2 untuk dropdown
                    dropdown.select2({
                        placeholder: "Pilih Unit Job",
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('body')
                    });

                    console.log('Dropdown created successfully for Job Order ID:', jobOrderId);
                }

                // Fungsi untuk update unit job di desktop dan mobile
                function updateUnitJobForBothLayouts(jobOrderId, unitJobs) {
                    // Update desktop
                    var desktopItem = $(`.job-order-item[data-job-id="${jobOrderId}"]`);
                    var desktopUnitJobInput = desktopItem.find('.unit-job-input');
                    if (desktopUnitJobInput.length > 0) {
                        createUnitJobDropdown(desktopUnitJobInput, unitJobs, jobOrderId);
                    }

                    // Update mobile
                    var mobileItem = $(`.job-order-mobile[data-job-id="${jobOrderId}"]`);
                    var mobileUnitJobInput = mobileItem.find('.unit-job-input');
                    if (mobileUnitJobInput.length > 0) {
                        createUnitJobDropdown(mobileUnitJobInput, unitJobs, jobOrderId);
                    }
                }

                // Fungsi untuk reset unit job di desktop dan mobile
                function resetUnitJobForBothLayouts(jobOrderId) {
                    // Reset desktop
                    var desktopItem = $(`.job-order-item[data-job-id="${jobOrderId}"]`);
                    var desktopUnitJobInput = desktopItem.find('.unit-job-input');
                    if (desktopUnitJobInput.length > 0) {
                        resetUnitJobField(desktopUnitJobInput, jobOrderId);
                        desktopUnitJobInput.removeClass('is-valid');
                        desktopUnitJobInput.addClass('is-invalid');
                    }

                    // Reset mobile
                    var mobileItem = $(`.job-order-mobile[data-job-id="${jobOrderId}"]`);
                    var mobileUnitJobInput = mobileItem.find('.unit-job-input');
                    if (mobileUnitJobInput.length > 0) {
                        resetUnitJobField(mobileUnitJobInput, jobOrderId);
                        mobileUnitJobInput.removeClass('is-valid');
                        mobileUnitJobInput.addClass('is-invalid');
                    }
                }

                // Fungsi untuk sinkronisasi jenis pekerjaan antara desktop dan mobile
                function syncJenisPekerjaanBetweenLayouts(jobOrderId, selectedValue) {
                    if (isSyncing) return; // Prevent infinite loop

                    console.log('Syncing jenis pekerjaan between layouts for Job Order ID:', jobOrderId, 'Value:',
                        selectedValue);

                    isSyncing = true;

                    // Update desktop
                    var desktopItem = $(`.job-order-item[data-job-id="${jobOrderId}"]`);
                    var desktopSelect = desktopItem.find('select[name*="[jenis_pekerjaan]"]');

                    if (desktopSelect.length > 0 && desktopSelect.val() !== selectedValue) {
                        desktopSelect.val(selectedValue).trigger('change.select2');
                    }

                    // Update mobile
                    var mobileItem = $(`.job-order-mobile[data-job-id="${jobOrderId}"]`);
                    var mobileSelect = mobileItem.find('select[name*="[jenis_pekerjaan]"]');

                    if (mobileSelect.length > 0 && mobileSelect.val() !== selectedValue) {
                        mobileSelect.val(selectedValue).trigger('change.select2');
                    }

                    setTimeout(function() {
                        isSyncing = false;
                    }, 100);
                }

                // Fungsi untuk sinkronisasi unit job antara desktop dan mobile
                function syncUnitJobBetweenLayouts(jobOrderId, selectedValue) {
                    console.log('Syncing unit job between layouts for Job Order ID:', jobOrderId, 'Value:',
                        selectedValue);

                    // Update desktop
                    var desktopItem = $(`.job-order-item[data-job-id="${jobOrderId}"]`);
                    var desktopUnitJobInput = desktopItem.find('.unit-job-input');
                    var desktopDropdown = desktopItem.find('.unit-job-dropdown');

                    if (desktopUnitJobInput.length > 0) {
                        desktopUnitJobInput.val(selectedValue);
                        if (desktopDropdown.length > 0 && desktopDropdown.val() !== selectedValue) {
                            desktopDropdown.val(selectedValue).trigger('change.select2');
                        }

                        if (selectedValue) {
                            desktopUnitJobInput.removeClass('is-invalid').addClass('is-valid');
                            desktopDropdown.removeClass('is-invalid').addClass('is-valid');
                        } else {
                            desktopUnitJobInput.removeClass('is-valid').addClass('is-invalid');
                            desktopDropdown.removeClass('is-valid').addClass('is-invalid');
                        }
                    }

                    // Update mobile
                    var mobileItem = $(`.job-order-mobile[data-job-id="${jobOrderId}"]`);
                    var mobileUnitJobInput = mobileItem.find('.unit-job-input');
                    var mobileDropdown = mobileItem.find('.unit-job-dropdown');

                    if (mobileUnitJobInput.length > 0) {
                        mobileUnitJobInput.val(selectedValue);
                        if (mobileDropdown.length > 0 && mobileDropdown.val() !== selectedValue) {
                            mobileDropdown.val(selectedValue).trigger('change.select2');
                        }

                        if (selectedValue) {
                            mobileUnitJobInput.removeClass('is-invalid').addClass('is-valid');
                            mobileDropdown.removeClass('is-invalid').addClass('is-valid');
                        } else {
                            mobileUnitJobInput.removeClass('is-valid').addClass('is-invalid');
                            mobileDropdown.removeClass('is-valid').addClass('is-invalid');
                        }
                    }
                }

                // Update tombol hapus saat halaman dimuat
                updateRemoveButtons();

                // Inisialisasi Select2 untuk mobile job order yang sudah ada
                $('.job-order-mobile .select2-job-order').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            placeholder: "Pilih jenis pekerjaan...",
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('body')
                        });
                    }
                });

                var submitJobPrepress = "{{ route('prepress.job-order.submit-new') }}";

                console.log('Submit URL:', submitJobPrepress);
                console.log('Form ID:', $('#submitJobPrepress').attr('id'));
                console.log('Form action:', $('#submitJobPrepress').attr('action'));
                console.log('Form element found:', $('#submitJobPrepress').length);

                // Fungsi untuk melanjutkan proses submit setelah konfirmasi limit
                function proceedWithSubmit() {
                    if (isSubmittingJob) {
                        return; // prevent double submit
                    }

                    $('.form-control').removeClass('is-invalid');
                    $('.error-message').hide();

                    var fileDataCheckboxes = $('input[name="file_data[]"]:checked');

                    if (fileDataCheckboxes.length === 0) {
                        $('#file_data_error').html(
                            'File data wajib dipilih minimal satu jenis').show();
                        $('html, body').animate({
                            scrollTop: $('#file_data_error').offset().top -
                                100
                        }, 500);
                        showSweetAlert('error', 'File Data Wajib Dipilih',
                            'Mohon pilih minimal satu jenis file data yang akan disediakan.',
                            'Pilih salah satu atau lebih dari: Contoh Cetak, Contoh Produk, atau File Softcopy.'
                        );

                        return;
                    } else {
                        // Hide error jika sudah valid
                        $('#file_data_error').hide();
                    }

                    // Validasi job order - pastikan minimal ada satu yang diisi
                    var jobOrderItems = $('.job-order-item, .job-order-mobile');
                    var validJobOrders = 0;
                    var processedIds = new Set(); // Untuk menghindari duplikasi validasi

                    console.log('Validating job orders...');
                    jobOrderItems.each(function(index) {
                        var jobOrderId = $(this).data('job-id');

                        // Skip jika ID sudah diproses (menghindari duplikasi desktop/mobile)
                        if (processedIds.has(jobOrderId)) {
                            return;
                        }
                        processedIds.add(jobOrderId);

                        var jenisPekerjaan = $(this).find(
                            'select[name*="[jenis_pekerjaan]"]').val();
                        var unitJob = $(this).find(
                            'input[name*="[unit_job]"]').val();

                        console.log('Job Order', index + 1, 'ID:',
                            jobOrderId, 'Jenis:', jenisPekerjaan,
                            'Unit:', unitJob);

                        if (jenisPekerjaan && unitJob) {
                            validJobOrders++;
                            console.log('Job Order', index + 1, 'is VALID');
                        } else {
                            console.log('Job Order', index + 1,
                                'is INVALID');
                        }
                    });

                    console.log('Total valid job orders:', validJobOrders);

                    if (validJobOrders === 0) {
                        showSweetAlert('error', 'Job Order Wajib Diisi',
                            'Mohon pilih minimal satu jenis pekerjaan dan pastikan unit job terisi.',
                            'Pilih jenis pekerjaan untuk mengisi unit job secara otomatis.'
                        );
                        return;
                    }

                    // Validasi dimensi - cek jika berisi 'cek file'
                    var dimensionValue = $('input[name="dimension"]').val().trim().toLowerCase();
                    if (dimensionValue === 'cek file' || dimensionValue === '') {
                        showSweetAlert('error', 'Dimensi Wajib Diisi',
                            'Mohon diisikan informasi dimensi yang lengkap.',
                            'Silakan isi dimensi dengan ukuran yang sebenarnya, bukan "cek file".'
                        );
                        $('input[name="dimension"]').addClass('is-invalid').focus();
                        $('html, body').animate({
                            scrollTop: $('input[name="dimension"]').offset().top - 100
                        }, 500);
                        return;
                    }

                    // Validasi material - cek jika berisi 'cek file'
                    var materialValue = $('input[name="material"]').val().trim().toLowerCase();
                    if (materialValue === 'cek file' || materialValue === '') {
                        showSweetAlert('error', 'Material Wajib Diisi',
                            'Mohon diisikan informasi material yang lengkap.',
                            'Silakan isi material dengan jenis bahan yang sebenarnya, bukan "cek file".'
                        );
                        $('input[name="material"]').addClass('is-invalid').focus();
                        $('html, body').animate({
                            scrollTop: $('input[name="material"]').offset().top - 100
                        }, 500);
                        return;
                    }

                    // Buat FormData untuk support file upload
                    var formData = new FormData($('#submitJobPrepress')[0]);

                    // Hapus semua attachments[] dari FormData untuk menghindari duplikasi
                    // Karena FormData bisa mengambil dari kedua input (desktop & mobile)
                    formData.delete('attachments[]');

                    // Hanya tambahkan file dari input desktop (karena sudah di-sync dengan mobile)
                    var desktopFiles = $('#attachments')[0].files;
                    for (var i = 0; i < desktopFiles.length; i++) {
                        formData.append('attachments[]', desktopFiles[i]);
                    }

                    // Debug: Log form data yang akan dikirim
                    console.log('Form data to be submitted:');
                    for (var pair of formData.entries()) {
                        if (pair[1] instanceof File) {
                            console.log(pair[0] + ':', pair[1].name, '(' + (pair[1].size / 1024 / 1024).toFixed(2) +
                                ' MB)');
                        } else {
                            console.log(pair[0] + ':', pair[1]);
                        }
                    }

                    isSubmittingJob = true;
                    $('#submitButton').prop('disabled', true).text('Menyimpan...');
                    $('#loadingOverlay').fadeIn(120);

                    $.ajax({
                        url: submitJobPrepress,
                        data: formData,
                        type: "POST",
                        processData: false, // Penting: jangan process data untuk FormData
                        contentType: false, // Penting: jangan set content type untuk FormData
                        dataType: 'json',
                        success: function(response) {
                            console.log(response);
                            if (response.errors) {
                                showFieldErrors(response.errors);
                                showSweetAlert('error',
                                    'Validasi Error',
                                    'Mohon periksa kembali data yang diinput. Beberapa field memiliki kesalahan validasi.',
                                    'Silakan perbaiki field yang ditandai dengan border merah.'
                                );

                                // Reset loading state
                                isSubmittingJob = false;
                                $('#submitButton').prop('disabled', false).text('Submit');
                                $('#loadingOverlay').fadeOut(120);
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Job Order Prepress berhasil disubmit! Mohon ditunggu Job diproses oleh Team Prepress. Untuk monitoring, silahkan klik Dashboard Prepress!',
                                    showConfirmButton: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href =
                                            "{{ route('prepress.job-order.data.index') }}";
                                    }
                                });

                                // Reset loading state
                                isSubmittingJob = false;
                                $('#submitButton').prop('disabled', false).text('Submit');
                                $('#loadingOverlay').fadeOut(120);
                            }
                        },
                        error: function(xhr) {
                            let errorMessage =
                                'Terjadi kesalahan pada server. Silakan coba lagi nanti.';
                            let errorDetails = '';

                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.errors) {
                                    showFieldErrors(xhr.responseJSON
                                        .errors);
                                    showSweetAlert('error',
                                        'Validasi Error',
                                        'Mohon periksa kembali data yang diinput. Beberapa field memiliki kesalahan validasi.',
                                        'Silakan perbaiki field yang ditandai dengan border merah.'
                                    );

                                    // Reset loading state
                                    isSubmittingJob = false;
                                    $('#submitButton').prop('disabled', false).text('Submit');
                                    $('#loadingOverlay').fadeOut(120);
                                    return;
                                }

                                errorMessage = xhr.responseJSON.message ||
                                    errorMessage;

                                if (xhr.responseJSON.is_over_limit) {
                                    showSweetAlert('warning',
                                        'Limit Pengerjaan Tim Prepress Tercapai',
                                        `Tanggal deadline ${xhr.responseJSON.deadline_date || 'yang dipilih'} sudah mencapai limit pengerjaan tim prepress.`,
                                        `Total waktu: <strong>${xhr.responseJSON.total_work_time}</strong> menit<br>Limit: <strong>${xhr.responseJSON.limit_setting}</strong> menit<br><br><strong>Silakan ambil deadline lain!</strong>`
                                    );

                                    // Reset loading state
                                    isSubmittingJob = false;
                                    $('#submitButton').prop('disabled', false).text('Submit');
                                    $('#loadingOverlay').fadeOut(120);
                                    return;
                                }

                                if (xhr.responseJSON.type ===
                                    'data_type_error') {
                                    errorMessage =
                                        'Kesalahan tipe data pada input. Mohon periksa kembali.';
                                    errorDetails = xhr.responseJSON
                                        .details ||
                                        'Pastikan format input sesuai dengan yang diminta (angka untuk field numerik, tanggal untuk field tanggal, dll).';
                                } else if (xhr.responseJSON.type ===
                                    'database_error') {
                                    errorMessage =
                                        'Kesalahan pada database. Data tidak dapat disimpan.';
                                    errorDetails = xhr.responseJSON
                                        .details ||
                                        'Silakan coba lagi atau hubungi administrator jika masalah berlanjut.';
                                } else if (xhr.responseJSON.type ===
                                    'validation_error') {
                                    errorMessage =
                                        'Data tidak lengkap atau tidak valid.';
                                    errorDetails = xhr.responseJSON
                                        .details ||
                                        'Mohon periksa kembali semua field yang wajib diisi.';
                                }
                            }

                            if (xhr.status === 419) {
                                errorMessage =
                                    'Session expired. Silakan refresh halaman dan coba lagi.';
                                errorDetails =
                                    'Ini terjadi karena session login sudah berakhir. Silakan login ulang.';
                            }

                            if (xhr.status >= 500) {
                                errorMessage =
                                    'Terjadi kesalahan pada server. Silakan coba lagi nanti.';
                                errorDetails =
                                    `Error Code: ${xhr.status}<br>Silakan hubungi administrator jika masalah berlanjut.`;
                            }

                            if (xhr.status === 400) {
                                if (xhr.responseJSON && xhr.responseJSON
                                    .message) {
                                    errorMessage = xhr.responseJSON.message;
                                    if (xhr.responseJSON.details) {
                                        errorDetails = xhr.responseJSON
                                            .details;
                                    }
                                } else {
                                    errorMessage =
                                        'Data yang dikirim tidak valid atau tidak lengkap.';
                                    errorDetails =
                                        'Mohon periksa kembali semua field yang wajib diisi.';
                                }
                            }

                            showSweetAlert('error',
                                'Gagal Submit Job Order',
                                errorMessage,
                                errorDetails);

                            // Reset loading state
                            isSubmittingJob = false;
                            $('#submitButton').prop('disabled', false).text('Submit');
                            $('#loadingOverlay').fadeOut(120);
                        }
                    });
                }

                // Register event handler
                $('#submitJobPrepress').submit(function(e) {
                    console.log('Form submit event triggered');
                    console.log('Event object:', e);
                    console.log('Form data:', $(this).serializeArray());

                    var deadlineDate = $('input[name="job_deadline"]').val();

                    // Prevent default form submission
                    e.preventDefault();

                    console.log('Preventing default form submission');
                    console.log('Event handler working correctly');

                    $.ajax({
                        url: "{{ route('prepress.job-order.check-limit-time') }}",
                        type: "POST",
                        data: {
                            deadline_date: deadlineDate,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log('Limit check response:', response);

                            // Hapus alert sebelumnya jika ada
                            $('.alert-custom').remove();

                            // Enable tombol submit terlebih dahulu
                            $('#submitButton').prop('disabled', false);

                            if (response.is_over_limit) {
                                // Tampilkan warning dengan konfirmasi
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Limit Pengerjaan Tim Prepress Tercapai',
                                    html: `Tanggal deadline <strong>${deadlineDate}</strong> sudah mencapai limit pengerjaan tim prepress.<br><br>
                                                   <strong>Detail Kapasitas:</strong><br>
                                                   • Total Waktu Pengerjaan Job: <strong>${response.total_work_time}</strong> menit<br>
                                                   • Limit Waktu Pengerjaan Job: <strong>${response.limit_setting}</strong> menit<br>
                                                   • Job Aktif: <strong>${response.active_jobs_count}</strong> job<br><br>
                                                   <strong>Silahkan pilih deadline Tanggal lain!</strong>`,
                                    showCancelButton: true,
                                    confirmButtonText: 'Ya, Pilih Deadline Lain',
                                    cancelButtonText: 'Batal',
                                    confirmButtonColor: '#f59e0b',
                                    cancelButtonColor: '#6c757d',
                                    reverseButtons: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // User memilih untuk melanjutkan, jalankan proses submit
                                        // proceedWithSubmit();
                                        isSubmittingJob = false;
                                        $('#submitButton').prop('disabled', false).text(
                                            'Submit');
                                    } else {
                                        // User memilih batal, reset state
                                        isSubmittingJob = false;
                                        $('#submitButton').prop('disabled', false).text(
                                            'Submit');
                                    }
                                });
                            } else {
                                // Tampilkan info dengan konfirmasi
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Info Kapasitas Tim Prepress',
                                    html: `Tanggal deadline <strong>${deadlineDate}</strong> masih tersedia untuk pengerjaan.<br><br>
                                                   <strong>Detail Kapasitas:</strong><br>
                                                   • Total waktu: <strong>${response.total_work_time}</strong> menit<br>
                                                   • Limit: <strong>${response.limit_setting}</strong> menit<br>
                                                   • Sisa waktu: <strong>${response.remaining_time}</strong> menit<br>
                                                   • Job aktif: <strong>${response.active_jobs_count}</strong> job<br><br>
                                                   <strong>Apakah Anda ingin melanjutkan submit?</strong>`,
                                    showCancelButton: true,
                                    confirmButtonText: 'Ya, Lanjutkan Submit',
                                    cancelButtonText: 'Batal',
                                    confirmButtonColor: '#3b82f6',
                                    cancelButtonColor: '#6c757d',
                                    reverseButtons: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // User memilih untuk melanjutkan, jalankan proses submit
                                        proceedWithSubmit();
                                    } else {
                                        // User memilih batal, reset state
                                        isSubmittingJob = false;
                                        $('#submitButton').prop('disabled', false).text(
                                            'Submit');
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            console.log('Error checking limit:', xhr);
                            // Jika error, hapus alert dan enable tombol
                            $('.alert-custom').remove();
                            $('#submitButton').prop('disabled', false);

                            showSweetAlert('error', 'Error Cek Limit',
                                'Gagal memeriksa limit job order.',
                                'Silakan coba lagi atau hubungi administrator.');
                        }
                    });
                });

                // Fungsi untuk cek limit job order
                function checkJobOrderLimit(deadlineDate) {
                    if (!deadlineDate) {
                        // Jika tanggal kosong, hapus alert dan enable tombol
                        $('.alert-custom').remove();
                        $('#submitButton').prop('disabled', false);
                        return;
                    }

                    $.ajax({
                        url: "{{ route('prepress.job-order.check-limit') }}",
                        type: "POST",
                        data: {
                            deadline_date: deadlineDate
                        },
                        success: function(response) {
                            // Hapus alert sebelumnya jika ada
                            $('.alert-custom').remove();

                            // Enable tombol submit terlebih dahulu
                            $('#submitButton').prop('disabled', false);

                            if (response.current_count > 0) {
                                let remainingSlots = response.limit - response.current_count;

                                if (remainingSlots <= 0) {
                                    showSweetAlert('warning', 'Limit Job Order Tercapai',
                                        `Tanggal deadline ${deadlineDate} sudah mencapai limit maksimal.`,
                                        `Limit: ${response.limit} job order<br>Current: ${response.current_count} job order<br><br><strong>Silakan koordinasi dengan Head/SPV Prepress!</strong>`
                                    );
                                    $('#submitButton').prop('disabled', true);
                                } else {
                                    showSweetAlert('info', 'Info Limit Job Order',
                                        `Tanggal deadline ${deadlineDate} sudah memiliki ${response.current_count} job order.`,
                                        `Sisa slot tersedia: <strong>${remainingSlots}</strong> job order.`
                                    );
                                }
                            }
                        },
                        error: function(xhr) {
                            console.log('Error checking limit:', xhr);
                            // Jika error, hapus alert dan enable tombol
                            $('.alert-custom').remove();
                            $('#submitButton').prop('disabled', false);

                            showSweetAlert('error', 'Error Cek Limit', 'Gagal memeriksa limit job order.',
                                'Silakan coba lagi atau hubungi administrator.');
                        }
                    });
                }
            });
        </script>
    @endsection
