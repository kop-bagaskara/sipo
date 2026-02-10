@extends('main.layouts.main')
@section('title')
    Check Stock
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .query-result-container {
            max-height: 600px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
@endsection
@section('page-title')
    Check Stock
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Check Stock</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('paper-procurement.index') }}">Paper Procurement</a></li>
                <li class="breadcrumb-item active">Check Stock</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="card-title mb-1">Form Check Stock</h4>
                            <p class="text-muted mb-0">Pilih periode dan location untuk mengecek stock material</p>
                        </div>
                        <div>
                            <a href="{{ route('paper-procurement.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>

                    <form id="checkStockForm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="period">Periode/Bulan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="period" name="period" required>
                                        <option value="">-- Pilih Bulan --</option>
                                        @foreach($months as $key => $month)
                                            @php
                                                $currentYear = date('Y');
                                                $monthNum = array_search($key, array_keys($months)) + 1;
                                                $periodValue = $currentYear . '-' . str_pad($monthNum, 2, '0', STR_PAD_LEFT);
                                            @endphp
                                            <option value="{{ $periodValue }}"
                                                    {{ $periodValue == date('Y-m') ? 'selected' : '' }}>
                                                {{ $month }} {{ $currentYear }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Format: YYYY-MM (contoh: 2025-01)</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="location">Location <span class="text-danger">*</span></label>
                                    <select class="form-control" id="location" name="location" required>
                                        <option value="">-- Pilih Location --</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->Code }}">{{ $location->Code }} - {{ $location->Name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="unit_option">Unit Option</label>
                                    <select class="form-control" id="unit_option" name="unit_option">
                                        <option value="Smallest" selected>Smallest Unit</option>
                                        <option value="Sold">Sold Unit</option>
                                        <option value="SKU">SKU Unit</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-search"></i> Check Stock
                                </button>
                                <button type="button" class="btn btn-secondary" id="btnReset">
                                    <i class="mdi mdi-refresh"></i> Reset
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Query Display -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Query SQL:</h5>
                            <pre id="queryDisplay" class="bg-light p-3" style="border: 1px solid #ddd; border-radius: 4px; font-size: 11px;"></pre>
                        </div>
                    </div>

                    <!-- Results -->
                    <div class="row mt-4" id="resultsSection" style="display: none;">
                        <div class="col-12">
                            <h5>Hasil Query (<span id="resultCount">0</span> baris)</h5>
                            <div class="query-result-container">
                                <table class="table table-bordered table-striped table-sm" id="resultsTable">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>Location</th>
                                            <th>Unit</th>
                                            <th>Stock Qty</th>
                                            <th>Detail Stock Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody id="resultsBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#checkStockForm').on('submit', function(e) {
                e.preventDefault();

                const period = $('#period').val();
                const location = $('#location').val();
                const unitOption = $('#unit_option').val();

                if (!period || !location) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Form Tidak Lengkap',
                        text: 'Silakan pilih periode dan location terlebih dahulu'
                    });
                    return;
                }

                // Show loading
                Swal.fire({
                    title: 'Mengecek Stock...',
                    text: 'Sedang menjalankan query...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Build query untuk display
                const queryDisplay = `SELECT
    mm.Code,
    mm.Name,
    sb.Location,
    (CASE
        WHEN 'SKU' = '${unitOption}' THEN mm.SKUUnit
        WHEN 'Sold' = '${unitOption}' THEN mm.SoldUnit
        ELSE mm.SmallestUnit
    END) AS Unit,
    IFNULL(SUM(sb.QtyEnd - sb.QtyBook) / muc.Content, 0) AS StockQty,
    func_splitstockbyunit(mm.Code, IFNULL(SUM(sb.QtyEnd - sb.QtyBook), 0)) AS DetailStockQty
FROM mastermaterial AS mm
INNER JOIN masterunitconversion AS muc ON muc.MaterialCode = mm.Code
    AND muc.Unit = (CASE
        WHEN 'SKU' = '${unitOption}' THEN mm.SKUUnit
        WHEN 'Sold' = '${unitOption}' THEN mm.SoldUnit
        ELSE mm.SmallestUnit
    END)
LEFT JOIN stockbalance AS sb ON sb.MaterialCode = mm.Code
    AND sb.Periode = '${period}'
    AND sb.Location = '${location}'
WHERE TRUE
GROUP BY mm.Code, mm.Name, sb.Location, Unit
ORDER BY mm.Code`;

                $('#queryDisplay').text(queryDisplay);

                $.ajax({
                    url: '{{ route("paper-procurement.execute-stock-query") }}',
                    method: 'POST',
                    data: {
                        period: period,
                        location: location,
                        unit_option: unitOption,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.close();

                        if (response.success) {
                            displayResults(response.data);
                            $('#resultCount').text(response.count);
                            $('#resultsSection').show();

                            // Update query display dengan query yang sebenarnya digunakan
                            if (response.query) {
                                $('#queryDisplay').text(response.query);
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Query berhasil dijalankan. Ditemukan ' + response.count + ' baris data.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        let errorMsg = 'Terjadi kesalahan saat menjalankan query';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg,
                            html: xhr.responseJSON && xhr.responseJSON.trace ?
                                '<pre style="text-align: left; font-size: 11px; max-height: 300px; overflow-y: auto;">' +
                                xhr.responseJSON.trace + '</pre>' : errorMsg
                        });
                    }
                });
            });

            $('#btnReset').on('click', function() {
                $('#checkStockForm')[0].reset();
                $('#period').val('{{ date("Y-m") }}');
                $('#unit_option').val('Smallest');
                $('#queryDisplay').text('');
                $('#resultsSection').hide();
                $('#resultsBody').empty();
            });

            function displayResults(data) {
                const tbody = $('#resultsBody');
                tbody.empty();

                if (data.length === 0) {
                    tbody.append('<tr><td colspan="6" class="text-center">Tidak ada data ditemukan</td></tr>');
                    return;
                }

                data.forEach(function(row) {
                    const tr = $('<tr>');
                    tr.append($('<td>').text(row.Code || '-'));
                    tr.append($('<td>').text(row.Name || '-'));
                    tr.append($('<td>').text(row.Location || '-'));
                    tr.append($('<td>').text(row.Unit || '-'));
                    tr.append($('<td>').text(formatNumber(row.StockQty || 0)));
                    tr.append($('<td>').text(row.DetailStockQty || '-'));
                    tbody.append(tr);
                });
            }

            function formatNumber(num) {
                if (num === null || num === undefined) return '0';
                return parseFloat(num).toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 4
                });
            }
        });
    </script>
@endsection

