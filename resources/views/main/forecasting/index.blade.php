@extends('main.layouts.main')

@section('title')
    Forecasting
@endsection

@section('css')
    <style>
        .forecast-card {
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .forecast-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .forecast-card .card-body {
            padding: 2rem;
            text-align: center;
        }

        .forecast-card .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #333;
        }

        .forecast-card .card-text {
            color: #666;
            margin-bottom: 1.5rem;
        }

        .forecast-btn {
            padding: 12px 30px;
            font-size: 1rem;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .forecast-btn:hover {
            transform: scale(1.05);
        }

        .btn-forecast {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }

        .btn-forecast:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
        }

        .btn-ppic {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            color: white;
        }

        .btn-ppic:hover {
            background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
            color: white;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Forecasting</h4>
                    <p class="text-muted">Pilih menu forecasting yang ingin Anda akses</p>

                    <div class="row mt-4">
                        <!-- Card Forecast -->
                        <div class="col-md-6 mb-4">
                            <div class="card forecast-card h-100">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <i class="mdi mdi-chart-line" style="font-size: 48px; color: #667eea;"></i>
                                    </div>
                                    <h5 class="card-title">Forecast</h5>
                                    <p class="card-text">Akses halaman untuk melakukan forecasting</p>
                                    <button type="button" class="btn btn-info btn-forecast">
                                        <i class="mdi mdi-chart-line mr-2"></i>Forecast
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Card Pengajuan PPIC -->
                        <div class="col-md-6 mb-4">
                            <div class="card forecast-card h-100">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <i class="mdi mdi-file-document" style="font-size: 48px; color: #f5576c;"></i>
                                    </div>
                                    <h5 class="card-title">Pengajuan PPIC</h5>
                                    <p class="card-text">Akses halaman untuk melakukan pengajuan PPIC</p>
                                    <button type="button" class="btn btn-info btn-ppic">
                                        <i class="mdi mdi-file-document-edit mr-2"></i>Pengajuan PPIC
                                    </button>
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
    <script>
        $(document).ready(function() {
            // Button Forecast Click Handler
            $('.btn-forecast').on('click', function() {
                window.location.href = "{{ route('forecasting.list') }}";
            });

            // Button Pengajuan PPIC Click Handler
            $('.btn-ppic').on('click', function() {
                // TODO: Add route for PPIC page
                alert('Halaman Pengajuan PPIC akan segera tersedia');
            });
        });
    </script>
@endsection
