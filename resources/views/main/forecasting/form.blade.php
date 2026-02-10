@extends('main.layouts.main')

@section('title')
    {{ $mode == 'create' ? 'Tambah' : 'Edit' }} Forecast
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .item-row {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
        }
        .weekly-data-row {
            margin-top: 10px;
        }
        .weekly-input {
            margin-bottom: 5px;
        }
        .item-header {
            background-color: #667eea;
            color: white;
            padding: 10px;
            border-radius: 5px 5px 0 0;
            margin: -15px -15px 15px -15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .week-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #495057;
        }
    </style>
@endsection

@section('page-title')
    {{ $mode == 'create' ? 'Tambah' : 'Edit' }} Forecast
@endsection

@section('body')
    <body data-sidebar="colored">
    @endsection

    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">{{ $mode == 'create' ? 'Tambah' : 'Edit' }} Forecast</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('forecasting.index') }}">Forecasting</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('forecasting.list') }}">Forecast List</a></li>
                    <li class="breadcrumb-item active">{{ $mode == 'create' ? 'Tambah' : 'Edit' }}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ $mode == 'create' ? route('forecasting.store') : route('forecasting.update', $forecast->id ?? '') }}" id="forecastForm">
                            @csrf
                            @if($mode == 'edit')
                                @method('PUT')
                            @endif

                            <!-- Forecast Header -->
                            <h4 class="card-title mb-4">Informasi Forecast</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="customer_name">Customer <span class="text-danger">*</span></label>
                                        <select class="form-control @error('customer_name') is-invalid @enderror"
                                                id="customer_name" name="customer_name" required>
                                            <option value="">-- Pilih Customer --</option>
                                            <option value="Unilever" {{ old('customer_name', $forecast->customer_name ?? '') == 'Unilever' ? 'selected' : '' }}>Unilever</option>
                                            <option value="Nabati" {{ old('customer_name', $forecast->customer_name ?? '') == 'Nabati' ? 'selected' : '' }}>Nabati</option>
                                            <option value="OTHERS" {{ old('customer_name', $forecast->customer_name ?? '') == 'OTHERS' ? 'selected' : '' }}>OTHERS</option>
                                        </select>
                                        @error('customer_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="period_month">Bulan Periode <span class="text-danger">*</span></label>
                                        <select class="form-control @error('period_month') is-invalid @enderror"
                                                id="period_month" name="period_month" required>
                                            <option value="">-- Pilih Bulan --</option>
                                            <option value="Januari" {{ old('period_month', $forecast->period_month ?? '') == 'Januari' ? 'selected' : '' }}>Januari</option>
                                            <option value="Februari" {{ old('period_month', $forecast->period_month ?? '') == 'Februari' ? 'selected' : '' }}>Februari</option>
                                            <option value="Maret" {{ old('period_month', $forecast->period_month ?? '') == 'Maret' ? 'selected' : '' }}>Maret</option>
                                            <option value="April" {{ old('period_month', $forecast->period_month ?? '') == 'April' ? 'selected' : '' }}>April</option>
                                            <option value="Mei" {{ old('period_month', $forecast->period_month ?? '') == 'Mei' ? 'selected' : '' }}>Mei</option>
                                            <option value="Juni" {{ old('period_month', $forecast->period_month ?? '') == 'Juni' ? 'selected' : '' }}>Juni</option>
                                            <option value="Juli" {{ old('period_month', $forecast->period_month ?? '') == 'Juli' ? 'selected' : '' }}>Juli</option>
                                            <option value="Agustus" {{ old('period_month', $forecast->period_month ?? '') == 'Agustus' ? 'selected' : '' }}>Agustus</option>
                                            <option value="September" {{ old('period_month', $forecast->period_month ?? '') == 'September' ? 'selected' : '' }}>September</option>
                                            <option value="Oktober" {{ old('period_month', $forecast->period_month ?? '') == 'Oktober' ? 'selected' : '' }}>Oktober</option>
                                            <option value="November" {{ old('period_month', $forecast->period_month ?? '') == 'November' ? 'selected' : '' }}>November</option>
                                            <option value="Desember" {{ old('period_month', $forecast->period_month ?? '') == 'Desember' ? 'selected' : '' }}>Desember</option>
                                        </select>
                                        @error('period_month')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="period_year">Tahun Periode <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('period_year') is-invalid @enderror"
                                               id="period_year" name="period_year"
                                               value="{{ old('period_year', $forecast->period_year ?? date('Y')) }}"
                                               min="2020" max="2100" required>
                                        @error('period_year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="notes">Catatan</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                                  id="notes" name="notes" rows="3"
                                                  placeholder="Catatan tambahan">{{ old('notes', $forecast->notes ?? '') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Forecast Items -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="card-title mb-0">Item Forecast</h4>
                                <button type="button" class="btn btn-primary btn-sm" id="btn-add-item">
                                    <i class="mdi mdi-plus"></i> Tambah Item
                                </button>
                            </div>

                            <div id="items-container">
                                @if($mode == 'edit' && isset($forecast) && $forecast->items->count() > 0)
                                    @foreach($forecast->items as $index => $item)
                                        <div class="item-row" data-item-index="{{ $index }}">
                                            @include('main.forecasting.partials.item-row', ['itemIndex' => $index, 'item' => $item, 'forecast' => $forecast])
                                        </div>
                                    @endforeach
                                @else
                                    <!-- Default empty item row -->
                                    <div class="item-row" data-item-index="0">
                                        @include('main.forecasting.partials.item-row', ['itemIndex' => 0, 'item' => null, 'forecast' => $forecast ?? null])
                                    </div>
                                @endif
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-success">
                                    <i class="mdi mdi-content-save"></i> Simpan Forecast
                                </button>
                                <a href="{{ route('forecasting.list') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-close"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
        <script>
            $(document).ready(function() {
                let itemIndex = {{ $mode == 'edit' && isset($forecast) ? $forecast->items->count() : 1 }};

                // Add new item row
                $('#btn-add-item').on('click', function() {
                    const container = $('#items-container');
                    const newItemRow = `
                        <div class="item-row" data-item-index="${itemIndex}">
                            ${getItemRowHtml(itemIndex)}
                        </div>
                    `;
                    container.append(newItemRow);
                    itemIndex++;
                });

                // Remove item row
                $(document).on('click', '.remove-item', function() {
                    const itemRow = $(this).closest('.item-row');
                    const container = $('#items-container');

                    if (container.children().length > 1) {
                        Swal.fire({
                            title: 'Hapus Item?',
                            text: "Item ini akan dihapus dari forecast",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                itemRow.remove();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tidak Bisa Dihapus',
                            text: 'Minimal harus ada 1 item dalam forecast'
                        });
                    }
                });

                // Calculate totals for each item
                $(document).on('input', '.weekly-forecast-qty, .weekly-forecast-ton', function() {
                    const itemRow = $(this).closest('.item-row');
                    calculateItemTotals(itemRow);
                });

                // Form submission
                $('#forecastForm').on('submit', function(e) {
                    e.preventDefault();

                    // Validate form
                    if (!$('#customer_name').val() || !$('#period_month').val() || !$('#period_year').val()) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: 'Harap lengkapi semua field yang wajib diisi'
                        });
                        return;
                    }

                    // Check if there's at least one item
                    if ($('#items-container .item-row').length === 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            text: 'Minimal harus ada 1 item dalam forecast'
                        });
                        return;
                    }

                    // Show loading
                    Swal.fire({
                        title: 'Menyimpan...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit form
                    this.submit();
                });
            });

            function getItemRowHtml(index) {
                return `
                    <div class="item-header">
                        <span><strong>Item #${index + 1}</strong></span>
                        <button type="button" class="btn btn-sm btn-danger remove-item">
                            <i class="mdi mdi-delete"></i> Hapus
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Material Code</label>
                                <input type="text" class="form-control" name="items[${index}][material_code]"
                                       placeholder="Kode material">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Design Code</label>
                                <input type="text" class="form-control" name="items[${index}][design_code]"
                                       placeholder="DS.0230.0092">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Item Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="items[${index}][item_name]"
                                       placeholder="Nama item" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">DPC Group</label>
                                <input type="text" class="form-control" name="items[${index}][dpc_group]"
                                       placeholder="DPC 310 42,5 x 83">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Remarks</label>
                                <input type="text" class="form-control" name="items[${index}][remarks]"
                                       placeholder="By PO, By PO + Forecast, dll">
                            </div>
                        </div>
                    </div>
                    <div class="weekly-data-row">
                        <h6 class="mb-3">Weekly Data</h6>
                        <div class="row">
                            ${getWeeklyInputsHtml(index, 1)}${getWeeklyInputsHtml(index, 2)}${getWeeklyInputsHtml(index, 3)}${getWeeklyInputsHtml(index, 4)}${getWeeklyInputsHtml(index, 5)}
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <div class="alert alert-info mb-0">
                                    <strong>Total:</strong>
                                    Forecast QTY: <span class="item-total-qty">0</span> |
                                    Forecast TON: <span class="item-total-ton">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            function getWeeklyInputsHtml(itemIndex, weekNumber) {
                const year = $('#period_year').val() || new Date().getFullYear();
                return `
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="week-label">W${weekNumber}.${year}</label>
                            <input type="hidden" name="items[${itemIndex}][weekly_data][${weekNumber}][week_number]" value="${weekNumber}">
                            <input type="hidden" name="items[${itemIndex}][weekly_data][${weekNumber}][year]" value="${year}">
                            <input type="hidden" name="items[${itemIndex}][weekly_data][${weekNumber}][week_label]" value="W${weekNumber}.${year}">
                            <input type="number" class="form-control weekly-forecast-qty weekly-input"
                                   name="items[${itemIndex}][weekly_data][${weekNumber}][forecast_qty]"
                                   placeholder="QTY" step="0.01" min="0">
                            <input type="number" class="form-control weekly-forecast-ton weekly-input"
                                   name="items[${itemIndex}][weekly_data][${weekNumber}][forecast_ton]"
                                   placeholder="TON" step="0.0001" min="0">
                        </div>
                    </div>
                `;
            }

            // Update week labels when year changes
            $('#period_year').on('change', function() {
                const year = $(this).val() || new Date().getFullYear();
                $('#items-container .item-row').each(function() {
                    $(this).find('.week-label').each(function(index) {
                        const weekNumber = index + 1;
                        $(this).text(`W${weekNumber}.${year}`);
                        $(this).nextAll('input[type="hidden"]').first().val(weekNumber);
                        $(this).nextAll('input[type="hidden"]').eq(1).val(year);
                        $(this).nextAll('input[type="hidden"]').eq(2).val(`W${weekNumber}.${year}`);
                    });
                });
            });

            function calculateItemTotals(itemRow) {
                let totalQty = 0;
                let totalTon = 0;

                itemRow.find('.weekly-forecast-qty').each(function() {
                    const val = parseFloat($(this).val()) || 0;
                    totalQty += val;
                });

                itemRow.find('.weekly-forecast-ton').each(function() {
                    const val = parseFloat($(this).val()) || 0;
                    totalTon += val;
                });

                itemRow.find('.item-total-qty').text(totalQty.toLocaleString('id-ID'));
                itemRow.find('.item-total-ton').text(totalTon.toLocaleString('id-ID', {minimumFractionDigits: 4, maximumFractionDigits: 4}));
            }
        </script>
    @endsection

