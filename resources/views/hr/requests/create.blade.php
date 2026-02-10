@extends('main.layouts.main')
@section('title')
    Permohonan Data Karyawan
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

    <style>
        .cust-col {
            white-space: nowrap;
        }
    </style>
@endsection
@section('page-title')
    Permohonan Data Karyawan
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Permohonan Data Karyawan</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">Permohonan Data Karyawan</li>
                </ol>
            </div>
        </div>

        <!-- Form Content -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            @if ($type == 'shift_change')
                                Permohonan Tukar Shift
                            @elseif($type == 'absence')
                                Permohonan Tidak Masuk Kerja
                            @elseif($type == 'overtime')
                                Surat Perintah Lembur
                            @elseif($type == 'vehicle_asset')
                                Permintaan Membawa Kendaraan/Inventaris
                            @endif
                        </h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('hr.requests.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="request_type" value="{{ $type }}">

                            @if ($type == 'shift_change')
                                @include('hr.requests.forms.shift-change')
                            @elseif($type == 'absence')
                                @include('hr.requests.forms.absence')
                            @elseif($type == 'overtime')
                                @include('hr.requests.forms.overtime')
                            @elseif($type == 'vehicle_asset')
                                @include('hr.requests.forms.vehicle-asset')
                            @endif

                            <!-- Common Fields -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">Catatan Tambahan</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label" id="attachment_label">Lampiran</label>
                                    <input type="file" name="attachment" class="form-control"
                                        accept=".pdf,.jpg,.jpeg,.png">
                                    <div class="form-text">Format yang diperbolehkan: PDF, JPG, JPEG, PNG (Maksimal 2MB)
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('hr.requests.index') }}" class="btn btn-secondary">
                                            <i class="mdi mdi-arrow-left me-2"></i>Kembali
                                        </a>
                                        <button type="submit" class="btn btn-info">
                                            <i class="mdi mdi-content-save me-4 "></i>Simpan Pengajuan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>

        @push('styles')
            <style>
                .btn.active {
                    background-color: var(--bs-primary);
                    color: white;
                    border-color: var(--bs-primary);
                }
            </style>
        @endpush

        @section('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {
                // Handle form submission with AJAX
                $('form').on('submit', function(e) {
                    e.preventDefault();

                    var form = $(this);
                    var formData = new FormData(this);
                    var submitButton = form.find('button[type="submit"]');
                    var originalButtonText = submitButton.html();

                    // Disable submit button to prevent double submission
                    submitButton.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin me-2"></i>Mengirim...');

                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: response.message || 'Pengajuan berhasil dibuat',
                                    icon: 'success',
                                    confirmButtonText: 'OK',
                                    timer: 3000,
                                    timerProgressBar: true
                                }).then(() => {
                                    // Redirect to dashboard
                                    window.location.href = "{{ route('hr.requests.index') }}";
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: response.message || 'Terjadi kesalahan saat mengirim data',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                                submitButton.prop('disabled', false).html(originalButtonText);
                            }
                        },
                        error: function(xhr) {
                            submitButton.prop('disabled', false).html(originalButtonText);

                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                // Handle validation errors with SweetAlert2
                                var errorMessages = [];
                                $.each(xhr.responseJSON.errors, function(field, messages) {
                                    errorMessages.push(messages.join(', '));
                                });

                                Swal.fire({
                                    title: 'Validasi Error',
                                    html: errorMessages.join('<br>'),
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                Swal.fire({
                                    title: 'Error',
                                    text: xhr.responseJSON.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Terjadi kesalahan saat mengirim data',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        }
                    });
                });
            });
        </script>
        @endsection
    @endsection
