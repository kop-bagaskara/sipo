@extends('main.layouts.main')
@section('title')
    Order Fukumi
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .code-display {
            font-family: 'Courier New', monospace;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .codes-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .code-item {
            padding: 8px;
            margin: 5px 0;
            background-color: white;
            border-left: 3px solid #4472C4;
            border-radius: 3px;
        }
    </style>
@endsection
@section('page-title')
    Order Fukumi
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Order Fukumi - Generator Kode Acak</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active">Order Fukumi</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Generator Kode Acak 10 Karakter</h4>
                    <p class="text-muted">Generate kode acak dengan kombinasi angka dan huruf untuk Order Fukumi</p>

                    <form id="generateForm">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="quantity">Jumlah Kode</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity"
                                           value="100" min="1" max="10000000" required>
                                    <small class="form-text text-muted">
                                        Masukkan jumlah kode yang ingin di-generate (1-10.000.000)<br>
                                        <strong>Catatan:</strong> Jika jumlah > 1.000, file Excel akan langsung di-download tanpa ditampilkan di halaman.
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="mdi mdi-refresh"></i> Generate Kode
                                        </button>
                                        <button type="button" class="btn btn-success" id="exportBtn" disabled>
                                            <i class="mdi mdi-file-excel"></i> Export ke Excel
                                        </button>
                                        <button type="button" class="btn btn-secondary" id="clearBtn" disabled>
                                            <i class="mdi mdi-delete"></i> Clear
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Hasil Generate (<span id="codeCount">0</span> kode)</h5>
                            <div class="codes-container" id="codesContainer">
                                <p class="text-muted text-center">Belum ada kode yang di-generate</p>
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
        let generatedCodes = [];

        $(document).ready(function() {
            $('#generateForm').on('submit', function(e) {
                e.preventDefault();

                const quantity = parseInt($('#quantity').val());
                const THRESHOLD = 1000; // Jika lebih dari 1000, langsung export

                if (quantity < 1 || quantity > 10000000) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Jumlah kode harus antara 1 dan 10000000'
                    });
                    return;
                }

                // Jika quantity besar, langsung generate dan export
                if (quantity > THRESHOLD) {
                    Swal.fire({
                        title: 'Generating & Exporting...',
                        text: 'Sedang generate ' + quantity.toLocaleString('id-ID') + ' kode dan membuat file Excel. Mohon tunggu...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Create form for POST request
                    const form = $('<form>', {
                        'method': 'POST',
                        'action': '{{ route("order-fukumi.generate-export") }}'
                    });

                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '_token',
                        'value': '{{ csrf_token() }}'
                    }));

                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'quantity',
                        'value': quantity
                    }));

                    $('body').append(form);
                    form.submit();

                    // Close loading after a delay (file download will start)
                    setTimeout(function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'File Excel sedang didownload. File berisi ' + quantity.toLocaleString('id-ID') + ' kode acak.',
                            timer: 3000,
                            showConfirmButton: true
                        });
                    }, 2000);

                    return;
                }

                // Jika quantity kecil, tampilkan di halaman
                Swal.fire({
                    title: 'Generating...',
                    text: 'Sedang generate kode acak',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route("order-fukumi.generate") }}',
                    method: 'POST',
                    data: {
                        quantity: quantity,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.close();

                        if (response.success) {
                            generatedCodes = response.codes;
                            displayCodes(generatedCodes);
                            $('#exportBtn').prop('disabled', false);
                            $('#clearBtn').prop('disabled', false);

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Berhasil generate ' + response.count + ' kode acak',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        let errorMsg = 'Terjadi kesalahan saat generate kode';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg
                        });
                    }
                });
            });

            $('#exportBtn').on('click', function() {
                if (generatedCodes.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Tidak ada kode yang bisa di-export. Silakan generate kode terlebih dahulu.'
                    });
                    return;
                }

                // Show loading
                Swal.fire({
                    title: 'Exporting...',
                    text: 'Sedang membuat file Excel',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Create form for POST request
                const form = $('<form>', {
                    'method': 'POST',
                    'action': '{{ route("order-fukumi.export") }}'
                });

                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '_token',
                    'value': '{{ csrf_token() }}'
                }));

                generatedCodes.forEach(function(code, index) {
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': 'codes[' + index + ']',
                        'value': code
                    }));
                });

                $('body').append(form);
                form.submit();

                // Remove form after submission
                setTimeout(function() {
                    form.remove();
                    Swal.close();
                }, 1000);
            });

            $('#clearBtn').on('click', function() {
                Swal.fire({
                    title: 'Yakin?',
                    text: 'Apakah Anda yakin ingin menghapus semua kode yang sudah di-generate?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        generatedCodes = [];
                        displayCodes([]);
                        $('#exportBtn').prop('disabled', true);
                        $('#clearBtn').prop('disabled', true);

                        Swal.fire({
                            icon: 'success',
                            title: 'Dihapus!',
                            text: 'Semua kode telah dihapus',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            });
        });

        function displayCodes(codes) {
            const container = $('#codesContainer');
            const count = $('#codeCount');

            count.text(codes.length);

            if (codes.length === 0) {
                container.html('<p class="text-muted text-center">Belum ada kode yang di-generate</p>');
                return;
            }

            let html = '<div class="row">';
            codes.forEach(function(code, index) {
                html += '<div class="col-md-3 mb-2">';
                html += '<div class="code-item">';
                html += '<span class="code-display">' + code + '</span>';
                html += '</div>';
                html += '</div>';
            });
            html += '</div>';

            container.html(html);
        }
    </script>
@endsection

