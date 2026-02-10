<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Form Pengajuan Cuti - SIPO Krisan</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Material Design Icons -->
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.9.96/css/materialdesignicons.min.css" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .error-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            text-align: center;
            max-width: 800px;
            width: 100%;
        }

        .error-icon {
            font-size: 6rem;
            color: #dc3545;
            margin-bottom: 1.5rem;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        .error-title {
            color: #dc3545;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .error-subtitle {
            color: #6c757d;
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .error-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 2rem 0;
            text-align: left;
            border-left: 4px solid #dc3545;
        }

        .suggestions {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 2rem 0;
            text-align: left;
        }

        .suggestion-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .suggestion-item i {
            color: #2196f3;
            margin-right: 0.75rem;
            font-size: 1.2rem;
        }

        .solution-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }

        .solution-card:hover {
            transform: translateY(-5px);
        }

        .solution-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin: 2rem 0;
        }

        .btn {
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .navbar {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
        }

        .navbar-brand {
            color: white !important;
            font-weight: 700;
        }

        .error-code {
            font-family: 'Courier New', monospace;
            background: #e9ecef;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            display: inline-block;
            margin: 0.5rem 0;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="mdi mdi-office-building me-2"></i>
                SIPO Krisan
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text">
                    <i class="mdi mdi-alert-circle me-1"></i>
                    Error - Form Pengajuan Cuti
                </span>
            </div>
        </div>
    </nav>

    <!-- Error Content -->
    <div class="error-container">
        <div class="container">
            <div class="error-card">
                <div class="error-icon">
                    <i class="mdi mdi-alert-circle"></i>
                </div>

                <h1 class="error-title">Terjadi Kesalahan</h1>
                <p class="error-subtitle">Maaf, terjadi kesalahan saat memproses permintaan Anda.</p>

                @if(session('error'))
                <div class="error-details">
                    <h5 class="mb-3">
                        <i class="mdi mdi-information text-info me-2"></i>
                        Detail Error
                    </h5>
                    <p class="mb-0">{{ session('error') }}</p>
                </div>
                @endif

                <div class="suggestions">
                    <h5 class="mb-3">
                        <i class="mdi mdi-lightbulb text-warning me-2"></i>
                        Kemungkinan Penyebab
                    </h5>
                    <div class="suggestion-item">
                        <i class="mdi mdi-checkbox-blank-circle text-danger"></i>
                        <span>Barcode tidak valid atau sudah expired</span>
                    </div>
                    <div class="suggestion-item">
                        <i class="mdi mdi-checkbox-blank-circle text-danger"></i>
                        <span>Data karyawan tidak ditemukan dalam sistem</span>
                    </div>
                    <div class="suggestion-item">
                        <i class="mdi mdi-checkbox-blank-circle text-danger"></i>
                        <span>Karyawan belum memiliki status "Diterima"</span>
                    </div>
                    <div class="suggestion-item">
                        <i class="mdi mdi-checkbox-blank-circle text-danger"></i>
                        <span>Koneksi internet tidak stabil</span>
                    </div>
                </div>

                <div class="mt-4">
                    <h5 class="mb-3">Solusi yang Disarankan</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="solution-card">
                                <div class="solution-icon text-primary">
                                    <i class="mdi mdi-qrcode-scan"></i>
                                </div>
                                <h6>Scan Ulang Barcode</h6>
                                <p class="text-muted small">Pastikan barcode terlihat jelas dan tidak rusak</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="solution-card">
                                <div class="solution-icon text-success">
                                    <i class="mdi mdi-account-search"></i>
                                </div>
                                <h6>Hubungi HR</h6>
                                <p class="text-muted small">Konfirmasi data karyawan ke bagian HR</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="solution-card">
                                <div class="solution-icon text-warning">
                                    <i class="mdi mdi-refresh"></i>
                                </div>
                                <h6>Refresh Halaman</h6>
                                <p class="text-muted small">Coba refresh halaman dan scan ulang</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="btn-group">
                    <a href="{{ route('public.leave-request.form') }}" class="btn btn-primary">
                        <i class="mdi mdi-arrow-left me-2"></i>Kembali ke Form
                    </a>
                    <button onclick="window.location.reload()" class="btn btn-outline-primary">
                        <i class="mdi mdi-refresh me-2"></i>Coba Lagi
                    </button>
                    <button onclick="window.history.back()" class="btn btn-outline-secondary">
                        <i class="mdi mdi-arrow-left-bold me-2"></i>Kembali
                    </button>
                </div>

                <div class="mt-4">
                    <p class="text-muted small">
                        <i class="mdi mdi-information-outline me-1"></i>
                        Jika masalah berlanjut, silakan hubungi tim IT atau HR untuk bantuan lebih lanjut.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>

    <script>
        // Auto redirect after 30 seconds if user doesn't interact
        let autoRedirectTimer;

        function startAutoRedirect() {
            autoRedirectTimer = setTimeout(() => {
                Swal.fire({
                    title: 'Auto Redirect',
                    text: 'Anda akan diarahkan kembali ke form dalam 5 detik...',
                    icon: 'info',
                    timer: 5000,
                    showConfirmButton: false,
                    allowOutsideClick: false
                }).then(() => {
                    window.location.href = "{{ route('public.leave-request.form') }}";
                });
            }, 30000);
        }

        function stopAutoRedirect() {
            clearTimeout(autoRedirectTimer);
        }

        // Start auto redirect when page loads
        document.addEventListener('DOMContentLoaded', function() {
            startAutoRedirect();

            // Stop auto redirect if user interacts
            document.addEventListener('click', stopAutoRedirect);
            document.addEventListener('keypress', stopAutoRedirect);
        });

        // Show error details if available
        @if(session('error'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Detail Error',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
        @endif
    </script>
</body>
</html>
