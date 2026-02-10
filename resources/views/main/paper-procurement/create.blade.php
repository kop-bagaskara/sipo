@extends('main.layouts.main')
@section('title')
    Buat Pengajuan Meeting Kertas
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .excel-container {
            background: #fff;
            padding: 20px;
            border-radius: 4px;
        }
        .excel-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #000;
        }
        .excel-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .excel-table th {
            background-color: #4472C4;
            color: white;
            padding: 8px 4px;
            text-align: center;
            border: 1px solid #000;
            font-weight: bold;
            vertical-align: middle;
        }
        .excel-table td {
            padding: 4px 6px;
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
        }
        .excel-table .col-product {
            text-align: left;
            padding-left: 8px;
            font-weight: 500;
            width: 200px;
            min-width: 200px;
        }
        .excel-table .col-month {
            width: 100px;
            text-align: right;
        }
        .excel-table .col-total {
            width: 120px;
            text-align: right;
            background-color: #E7E6E6;
            font-weight: 600;
        }
        .excel-table .col-paper {
            width: 180px;
            text-align: center;
            background-color: #F2F2F2;
        }
        .excel-input {
            width: 100%;
            border: none;
            background: transparent;
            text-align: right;
            padding: 2px 4px;
            font-size: 11px;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .excel-input:focus {
            outline: 2px solid #0078D4;
            background-color: #E7F3FF;
        }
        .excel-input:read-only {
            background-color: #E7E6E6;
            cursor: not-allowed;
        }
        .category-carton {
            background-color: #FFFFFF !important;
        }
        .category-pack {
            background-color: #D1F2EB !important;
        }
        .category-inner {
            background-color: #FEF3C7 !important;
        }
        .category-esse {
            background-color: #DBEAFE !important;
        }
        .paper-header {
            font-size: 9px;
            line-height: 1.2;
            padding: 4px 2px;
        }
        .paper-code {
            font-size: 9px;
            color: #666;
            margin-top: 2px;
        }
        .paper-input {
            width: 100%;
            border: 1px solid #ccc;
            padding: 2px 4px;
            text-align: right;
            font-size: 11px;
            margin-top: 4px;
        }
        .formula-bar {
            background-color: #F2F2F2;
            padding: 5px 10px;
            border: 1px solid #ccc;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            margin-bottom: 10px;
        }
        .formula-cell {
            background-color: #FFF2CC;
        }
    </style>
@endsection
@section('page-title')
    Buat Pengajuan Meeting Kertas
@endsection
@section('body')
    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Buat Pengajuan Meeting Kertas</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('paper-procurement.index') }}">Pengajuan Kertas</a></li>
                    <li class="breadcrumb-item active">Buat Pengajuan</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Info Card -->
                        <div class="alert alert-info mb-4">
                            <h5 class="alert-heading">
                                <i class="mdi mdi-information-outline"></i> Informasi Pengajuan
                            </h5>
                            <p class="mb-2">
                                <strong>Bulan Meeting:</strong> {{ $meetingMonth ?? ($allMonths[$month] ?? $month) . ' ' . date('Y') }}<br>
                                <strong>Periode (3 Bulan):</strong>
                                <span class="badge bg-primary">{{ $allMonths[$periodMonth1] ?? $periodMonth1 }}</span>,
                                <span class="badge bg-primary">{{ $allMonths[$periodMonth2] ?? $periodMonth2 }}</span>,
                                <span class="badge bg-primary">{{ $allMonths[$periodMonth3] ?? $periodMonth3 }}</span>
                                <br>
                                <strong>Customer Group:</strong> <span class="badge bg-info">{{ $customerGroup }}</span>
                            </p>
                        </div>

                        <form id="paper-meeting-form" method="POST" action="{{ route('paper-procurement.store') }}">
                            @csrf
                            <input type="hidden" name="meeting_month" value="{{ $meetingMonth ?? ($allMonths[$month] ?? $month) . ' ' . date('Y') }}">
                            <input type="hidden" name="customer_name" value="{{ $customerGroup }}">
                            <input type="hidden" name="period_month_1" value="{{ $periodMonth1 }}">
                            <input type="hidden" name="period_month_2" value="{{ $periodMonth2 }}">
                            <input type="hidden" name="period_month_3" value="{{ $periodMonth3 }}">
                            <input type="hidden" name="tolerance_percentage" value="10.00">

                            <div class="excel-container">
                                <div class="excel-title">
                                    KEBUTUHAN MEETING KERTAS BULANAN PPIC
                                </div>

                                <div class="table-responsive" style="overflow-x: auto;">
                                    <table class="excel-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;">No</th>
                                                <th class="col-product">PRODUK</th>
                                                <th class="col-month">{{ strtoupper($periodMonth1) }}</th>
                                                <th class="col-month">{{ strtoupper($periodMonth2) }}</th>
                                                <th class="col-month">{{ strtoupper($periodMonth3) }}</th>
                                                <th class="col-total">TOTAL {{ strtoupper($periodMonth1) }} - {{ strtoupper($periodMonth3) }}</th>
                                                <th class="col-total formula-cell">TOTAL {{ strtoupper($periodMonth1) }} - {{ strtoupper($periodMonth3) }} + TOLERANSI</th>
                                                @php
                                                    $paperColumns = [];
                                                    foreach($productCategories as $cat) {
                                                        foreach($cat['paper_types'] as $pt) {
                                                            if(isset($paperTypes[$pt]) && !in_array($pt, $paperColumns)) {
                                                                $paperColumns[] = $pt;
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                @foreach($paperColumns as $paperType)
                                                    @if(isset($paperTypes[$paperType]))
                                                        @foreach($paperTypes[$paperType] as $paper)
                                                            <th class="col-paper">
                                                                <div class="paper-header">
                                                                    {{ $paperType }}<br>
                                                                    {{ $paper['size'] }}<br>
                                                                    <span class="paper-code">{{ $paper['code'] }}</span>
                                                                </div>
                                                            </th>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $rowNum = 1; @endphp
                                            @foreach($productCategories as $categoryIndex => $category)
                                                @foreach($category['products'] as $productIndex => $productName)
                                                    @php
                                                        $rowClass = '';
                                                        if($category['name'] === 'Carton Juara') {
                                                            $rowClass = 'category-carton';
                                                        } elseif($category['name'] === 'Pack Packaging Juara') {
                                                            $rowClass = 'category-pack';
                                                        } elseif($category['name'] === 'Inner Frame Juara') {
                                                            $rowClass = 'category-inner';
                                                        } elseif(strpos($category['name'], 'Esse') !== false) {
                                                            $rowClass = 'category-esse';
                                                        }
                                                        $productKey = $categoryIndex . '_' . $productIndex;
                                                    @endphp
                                                    <tr class="{{ $rowClass }}" data-product="{{ $productKey }}">
                                                        <td>{{ $rowNum++ }}</td>
                                                        <td class="col-product">{{ $productName }}</td>

                                                        <!-- Quantity Inputs -->
                                                        <td>
                                                            <input type="number"
                                                                class="excel-input qty-month-1"
                                                                name="products[{{ $categoryIndex }}][{{ $productIndex }}][qty_month_1]"
                                                                data-product="{{ $productKey }}"
                                                                value="0"
                                                                min="0"
                                                                step="1">
                                                        </td>
                                                        <td>
                                                            <input type="number"
                                                                class="excel-input qty-month-2"
                                                                name="products[{{ $categoryIndex }}][{{ $productIndex }}][qty_month_2]"
                                                                data-product="{{ $productKey }}"
                                                                value="0"
                                                                min="0"
                                                                step="1">
                                                        </td>
                                                        <td>
                                                            <input type="number"
                                                                class="excel-input qty-month-3"
                                                                name="products[{{ $categoryIndex }}][{{ $productIndex }}][qty_month_3]"
                                                                data-product="{{ $productKey }}"
                                                                value="0"
                                                                min="0"
                                                                step="1">
                                                        </td>

                                                        <!-- Total -->
                                                        <td class="col-total">
                                                            <input type="text"
                                                                class="excel-input total-qty"
                                                                data-product="{{ $productKey }}"
                                                                value="0"
                                                                readonly>
                                                        </td>

                                                        <!-- Total with Tolerance -->
                                                        <td class="col-total formula-cell">
                                                            <input type="text"
                                                                class="excel-input total-with-tolerance"
                                                                data-product="{{ $productKey }}"
                                                                value="0"
                                                                readonly>
                                                        </td>

                                                        <!-- Paper Columns -->
                                                        @foreach($paperColumns as $paperType)
                                                            @if(isset($paperTypes[$paperType]))
                                                                @php
                                                                    $hasPaper = in_array($paperType, $category['paper_types']);
                                                                @endphp
                                                                @foreach($paperTypes[$paperType] as $paperIndex => $paper)
                                                                    <td class="col-paper">
                                                                        @if($hasPaper)
                                                                            <input type="number"
                                                                                class="paper-input paper-qty"
                                                                                name="products[{{ $categoryIndex }}][{{ $productIndex }}][papers][{{ $paperType }}][{{ $paperIndex }}][quantity]"
                                                                                data-product="{{ $productKey }}"
                                                                                data-paper-type="{{ $paperType }}"
                                                                                value="0"
                                                                                min="0"
                                                                                step="1">
                                                                            <input type="hidden"
                                                                                name="products[{{ $categoryIndex }}][{{ $productIndex }}][papers][{{ $paperType }}][{{ $paperIndex }}][paper_type]"
                                                                                value="{{ $paperType }}">
                                                                            <input type="hidden"
                                                                                name="products[{{ $categoryIndex }}][{{ $productIndex }}][papers][{{ $paperType }}][{{ $paperIndex }}][paper_code]"
                                                                                value="{{ $paper['code'] }}">
                                                                            <input type="hidden"
                                                                                name="products[{{ $categoryIndex }}][{{ $productIndex }}][papers][{{ $paperType }}][{{ $paperIndex }}][paper_size]"
                                                                                value="{{ $paper['size'] }}">
                                                                        @else
                                                                            <span style="color: #ccc;">-</span>
                                                                        @endif
                                                                    </td>
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12 text-end">
                                    <a href="{{ route('paper-procurement.index') }}" class="btn btn-outline-secondary me-2">
                                        <i class="mdi mdi-arrow-left me-1"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-content-save me-1"></i> Simpan Pengajuan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                $(document).ready(function() {
                    const tolerance = 10; // 10%

                    // Format number dengan separator
                    function formatNumber(num) {
                        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    }

                    // Parse number dari formatted string
                    function parseNumber(str) {
                        return parseFloat(str.toString().replace(/\./g, '')) || 0;
                    }

                    // Auto calculate total and total with tolerance
                    function calculateTotals(productKey) {
                        const row = $(`.excel-table tbody tr[data-product="${productKey}"]`);
                        if(row.length === 0) return;

                        const qty1 = parseNumber(row.find('.qty-month-1').val()) || 0;
                        const qty2 = parseNumber(row.find('.qty-month-2').val()) || 0;
                        const qty3 = parseNumber(row.find('.qty-month-3').val()) || 0;

                        const total = qty1 + qty2 + qty3;
                        const totalWithTolerance = Math.ceil(total * (1 + tolerance / 100));

                        row.find('.total-qty[data-product="' + productKey + '"]').val(formatNumber(total));
                        row.find('.total-with-tolerance[data-product="' + productKey + '"]').val(formatNumber(totalWithTolerance));

                        // Auto fill paper quantity with total with tolerance (only if visible and has value)
                        row.find('.paper-qty[data-product="' + productKey + '"]').each(function() {
                            if($(this).is(':visible') && $(this).closest('td').find('span').length === 0) {
                                const currentVal = parseNumber($(this).val());
                                if(currentVal === 0 && totalWithTolerance > 0) {
                                    $(this).val(formatNumber(totalWithTolerance));
                                }
                            }
                        });
                    }

                    // Event listener for quantity inputs
                    $(document).on('input', '.qty-month-1, .qty-month-2, .qty-month-3', function() {
                        const productKey = $(this).data('product');
                        calculateTotals(productKey);
                    });

                    // Format on blur
                    $(document).on('blur', '.excel-input:not([readonly])', function() {
                        const value = parseNumber($(this).val());
                        $(this).val(formatNumber(value));
                    });

                    // Remove formatting on focus
                    $(document).on('focus', '.excel-input:not([readonly])', function() {
                        const value = parseNumber($(this).val());
                        $(this).val(value || '');
                    });

                    // Initialize all totals
                    $('.qty-month-1, .qty-month-2, .qty-month-3').each(function() {
                        const productKey = $(this).data('product');
                        calculateTotals(productKey);
                    });
                });
            </script>
        @endpush
    @endsection
