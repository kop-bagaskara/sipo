@extends('main.layouts.main')
@section('title')
    {{ $mode == 'create' ? 'Tambah' : 'Edit' }} Customer Label
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container {
            width: 100% !important;
        }
        .customer-search-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
    </style>
@endsection
@section('page-title')
    {{ $mode == 'create' ? 'Tambah' : 'Edit' }} Customer Label
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ $mode == 'create' ? 'Tambah' : 'Edit' }} Customer Label</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('label-management.index') }}">Label Management</a></li>
                <li class="breadcrumb-item active">{{ $mode == 'create' ? 'Tambah' : 'Edit' }} Customer</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ $mode == 'create' ? 'Form Tambah Customer' : 'Form Edit Customer' }}</h4>

                    @if($mode == 'create')
                    <div class="customer-search-section">
                        <h5 style="margin-bottom: 15px; color: #4472C4;">
                            <i class="mdi mdi-magnify"></i> Cari Customer dari Master Customer
                        </h5>
                        <div class="form-group">
                            <label for="master_customer_search">Pilih Customer</label>
                            <select class="form-control" id="master_customer_search" style="width: 100%;">
                                <option value="">-- Ketik untuk mencari customer --</option>
                            </select>
                            <small class="form-text text-muted">Ketik kode atau nama customer untuk mencari dari database master customer</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="clearMasterCustomer()">
                            <i class="mdi mdi-refresh"></i> Clear
                        </button>
                    </div>
                    @endif

                    <form method="POST" action="{{ $mode == 'create' ? route('label-management.customer.store') : route('label-management.customer.update', $customer->id) }}" id="customerForm">
                        @csrf
                        @if($mode == 'edit')
                            @method('PUT')
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_code">Kode Customer <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('customer_code') is-invalid @enderror"
                                           id="customer_code" name="customer_code"
                                           value="{{ old('customer_code', $customer->customer_code ?? '') }}"
                                           placeholder="Masukkan kode customer" required>
                                    @error('customer_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Kode unik untuk customer (contoh: CUST001)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_name">Nama Customer <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror"
                                           id="customer_name" name="customer_name"
                                           value="{{ old('customer_name', $customer->customer_name ?? '') }}"
                                           placeholder="Masukkan nama customer" required>
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="brand_name">Nama Brand</label>
                                    <input type="text" class="form-control @error('brand_name') is-invalid @enderror"
                                           id="brand_name" name="brand_name"
                                           value="{{ old('brand_name', $customer->brand_name ?? '') }}"
                                           placeholder="Masukkan nama brand (opsional)">
                                    @error('brand_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Nama brand jika berbeda dengan nama customer</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_person">Contact Person</label>
                                    <input type="text" class="form-control @error('contact_person') is-invalid @enderror"
                                           id="contact_person" name="contact_person"
                                           value="{{ old('contact_person', $customer->contact_person ?? '') }}"
                                           placeholder="Masukkan nama contact person">
                                    @error('contact_person')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email"
                                           value="{{ old('email', $customer->email ?? '') }}"
                                           placeholder="Masukkan email">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Telepon</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone"
                                           value="{{ old('phone', $customer->phone ?? '') }}"
                                           placeholder="Masukkan nomor telepon">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">Alamat</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror"
                                              id="address" name="address" rows="3"
                                              placeholder="Masukkan alamat customer">{{ old('address', $customer->address ?? '') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Deskripsi</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="3"
                                              placeholder="Masukkan deskripsi customer">{{ old('description', $customer->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                               {{ old('is_active', $customer->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Aktif
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Centang jika customer aktif</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> Simpan
                            </button>
                            <a href="{{ route('label-management.index') }}" class="btn btn-secondary">
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        @if($mode == 'create')
        // Initialize Select2 for master customer search
        $('#master_customer_search').select2({
            placeholder: 'Ketik kode atau nama customer...',
            allowClear: true,
            ajax: {
                url: '{{ route("label-management.search-master-customer") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.customers, function (customer) {
                            return {
                                id: customer.Code,
                                text: customer.Code + ' - ' + customer.Name,
                                customer: customer
                            };
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2
        });

        // When customer is selected, auto-fill the form
        $('#master_customer_search').on('select2:select', function (e) {
            const data = e.params.data;
            const customer = data.customer;

            // Fill form fields
            $('#customer_code').val(customer.Code);
            $('#customer_name').val(customer.Name);
            $('#address').val(customer.Address || '');
            $('#phone').val(customer.Phone || '');
            $('#email').val(customer.Email || '');
            $('#contact_person').val(customer.Contact || '');

            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Customer ditemukan!',
                text: 'Data customer telah diisi otomatis',
                timer: 2000,
                showConfirmButton: false
            });
        });

        // Clear master customer selection
        function clearMasterCustomer() {
            $('#master_customer_search').val(null).trigger('change');
            $('#customer_code').val('');
            $('#customer_name').val('');
            $('#address').val('');
            $('#phone').val('');
            $('#email').val('');
            $('#contact_person').val('');
        }
        @endif

        // Form validation and confirmation with AJAX
        const form = $('#customerForm');

        form.on('submit', function(e) {
            e.preventDefault(); // Always prevent default

            const customerCode = $('#customer_code').val();
            const customerName = $('#customer_name').val();
            const mode = '{{ $mode }}';
            const formElement = this;

            // Validation
            if (!customerCode || !customerName) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Kode Customer dan Nama Customer wajib diisi'
                });
                return false;
            }

            // Confirmation before submit
            Swal.fire({
                title: 'Simpan Data?',
                text: mode === 'create'
                    ? 'Apakah Anda yakin ingin menyimpan customer baru ini?'
                    : 'Apakah Anda yakin ingin mengupdate data customer ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    // Prepare form data
                    const formData = new FormData(formElement);
                    const url = form.attr('action');
                    const method = form.find('input[name="_method"]').val() || 'POST';

                    // Submit via AJAX
                    return $.ajax({
                        url: url,
                        type: method,
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val(),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }).then(function(response) {
                        // Success - show success message then redirect
                        if (response && response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Data berhasil disimpan',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = response.redirect || '{{ route("label-management.index") }}';
                            });
                        } else {
                            // Fallback redirect
                            window.location.href = '{{ route("label-management.index") }}';
                        }
                    }).catch(function(xhr) {
                        // Error handling
                        let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                        let errors = {};

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.errors) {
                                // Validation errors
                                errors = xhr.responseJSON.errors;
                                let errorList = '';
                                $.each(errors, function(field, messages) {
                                    $.each(messages, function(index, message) {
                                        errorList += '<li>' + message + '</li>';
                                    });
                                });
                                errorMessage = '<div style="text-align: left;"><strong>Validasi gagal:</strong><ul style="margin-top: 10px;">' + errorList + '</ul></div>';
                            } else if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                        } else if (xhr.responseText) {
                            try {
                                const errorData = JSON.parse(xhr.responseText);
                                if (errorData.message) {
                                    errorMessage = errorData.message;
                                }
                            } catch (e) {
                                errorMessage = 'Terjadi kesalahan: ' + xhr.statusText;
                            }
                        }

                        // Show error via SweetAlert
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: errorMessage,
                            confirmButtonText: 'OK'
                        });

                        // Show field errors if any
                        if (Object.keys(errors).length > 0) {
                            // Remove previous error messages
                            $('.invalid-feedback').remove();
                            $('.is-invalid').removeClass('is-invalid');

                            $.each(errors, function(field, messages) {
                                const input = $('#' + field);
                                if (input.length) {
                                    input.addClass('is-invalid');
                                    const errorDiv = $('<div class="invalid-feedback">' + messages[0] + '</div>');
                                    input.after(errorDiv);
                                }
                            });
                        }

                        return Promise.reject(xhr);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            });
        });
    </script>
@endsection

