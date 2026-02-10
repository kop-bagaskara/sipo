@extends('main.layouts.main')
@section('title')
    {{ $mode == 'create' ? 'Tambah' : 'Edit' }} Template Label
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .template-form-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        .template-form-section h5 {
            color: #4472C4;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4472C4;
        }
        .file-path-input-group {
            position: relative;
        }
        .file-path-input-group .btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
        }
        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #4472C4;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .info-box i {
            color: #4472C4;
            margin-right: 8px;
        }
        .form-section-divider {
            border-top: 2px solid #dee2e6;
            margin: 25px 0;
        }
    </style>
@endsection
@section('page-title')
    {{ $mode == 'create' ? 'Tambah' : 'Edit' }} Template Label
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">{{ $mode == 'create' ? 'Tambah' : 'Edit' }} Template Label</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('label-management.index') }}">Label Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('label-management.customer.show', $customer->id) }}">{{ $customer->customer_name }}</a></li>
                <li class="breadcrumb-item active">{{ $mode == 'create' ? 'Tambah' : 'Edit' }} Template</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="card-title mb-1">{{ $mode == 'create' ? 'Form Tambah Template' : 'Form Edit Template' }}</h4>
                            <p class="text-muted mb-0">
                                <i class="mdi mdi-account"></i> Customer: <strong>{{ $customer->customer_name }}</strong>
                                @if($customer->customer_code)
                                    <span class="ml-2">({{ $customer->customer_code }})</span>
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('label-management.customer.show', $customer->id) }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-left"></i> Kembali
                        </a>
                    </div>

                    <div class="info-box">
                        <i class="mdi mdi-information"></i>
                        <strong>Informasi:</strong> Template label akan digunakan untuk generate label packaging produk. Pastikan path file template sudah benar dan file dapat diakses.
                    </div>

                    <form method="POST" action="{{ $mode == 'create' ? route('label-management.template.store', $customer->id) : route('label-management.template.update', $template->id) }}" id="templateForm" enctype="multipart/form-data">
                        @csrf
                        @if($mode == 'edit')
                            @method('PUT')
                        @endif

                        <!-- Informasi Template & Produk -->
                        <div class="template-form-section">
                            <h5><i class="mdi mdi-file-document"></i> Informasi Template & Produk</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="template_name">Nama Template <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('template_name') is-invalid @enderror"
                                               id="template_name" name="template_name"
                                               value="{{ old('template_name', $template->template_name ?? '') }}"
                                               placeholder="Contoh: PEPSO HERBAL 150G ALLURE Y2" required>
                                        @error('template_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Nama template untuk identifikasi</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="template_type">Tipe Template <span class="text-danger">*</span></label>
                                        <select class="form-control @error('template_type') is-invalid @enderror"
                                                id="template_type" name="template_type" required>
                                            <option value="">-- Pilih Tipe --</option>
                                            <option value="besar" {{ old('template_type', $template->template_type ?? '') == 'besar' ? 'selected' : '' }}>Label Besar</option>
                                            <option value="kecil" {{ old('template_type', $template->template_type ?? '') == 'kecil' ? 'selected' : '' }}>Label Kecil</option>
                                            <option value="custom" {{ old('template_type', $template->template_type ?? '') != 'besar' && old('template_type', $template->template_type ?? '') != 'kecil' && old('template_type', $template->template_type ?? '') != '' ? 'selected' : '' }}>Custom (Input Manual)</option>
                                        </select>
                                        @error('template_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Pilih ukuran label yang sesuai atau pilih Custom untuk tipe lain</small>
                                    </div>
                                </div>
                                <div class="col-md-6" id="custom_template_type_wrapper" style="display: none;">
                                    <div class="form-group">
                                        <label for="custom_template_type">Nama Tipe Custom <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('custom_template_type') is-invalid @enderror"
                                               id="custom_template_type" name="custom_template_type"
                                               value="{{ old('custom_template_type', ($template->template_type ?? '') != 'besar' && ($template->template_type ?? '') != 'kecil' ? ($template->template_type ?? '') : '') }}"
                                               placeholder="Contoh: TSPM, Medium, dll">
                                        @error('custom_template_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Masukkan nama tipe template custom</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="brand_name">Nama Brand</label>
                                        <input type="text" class="form-control @error('brand_name') is-invalid @enderror"
                                               id="brand_name" name="brand_name"
                                               value="{{ old('brand_name', $template->brand_name ?? $customer->brand_name ?? '') }}"
                                               placeholder="Contoh: Unilever, Pepsodent">
                                        @error('brand_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Nama brand produk (opsional)</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_name">Nama Produk</label>
                                        <input type="text" class="form-control @error('product_name') is-invalid @enderror"
                                               id="product_name" name="product_name"
                                               value="{{ old('product_name', $template->product_name ?? '') }}"
                                               placeholder="Contoh: Pepsodent Herbal 150G Allure Y2">
                                        @error('product_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Nama lengkap produk (opsional)</small>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Field Mapping - Field yang bisa diisi -->
                        <div class="template-form-section" style="background-color: #fff3cd; border-left-color: #ffc107;">
                            <h5><i class="mdi mdi-map"></i> Field yang Bisa Diisi (Field Mapping)</h5>
                            <p class="text-muted mb-3">
                                <i class="mdi mdi-information"></i>
                                <strong>Field yang perlu diisi admin saat generate label.</strong><br>
                                Header (PT. KRISANTHIUM OFFSET PRINTING) dan Customer sudah PATEN di template Excel.<br>
                                Format: <code>Cell Excel</code> → <code>Nama Field</code> → <code>Label Tampilan</code>
                            </p>

                            <div id="fieldMappingContainer">
                                @php
                                    // Field yang perlu diisi admin (yang berubah per produksi)
                                    // Header dan Customer sudah PATEN di template Excel
                                    // Default fields berdasarkan customer (dari controller)
                                    $defaultFieldsFromController = $defaultFields ?? [];
                                    
                                    // Fallback default fields jika tidak ada dari controller
                                    if (empty($defaultFieldsFromController)) {
                                        $defaultFieldsFromController = [
                                            ['excel_cell' => 'D20', 'field_name' => 'ITEM', 'label' => 'Item', 'required' => true],
                                            ['excel_cell' => 'D5', 'field_name' => 'PC_NO', 'label' => 'PC NO.', 'required' => true],
                                            ['excel_cell' => 'D7', 'field_name' => 'MC_NO', 'label' => 'MC NO.', 'required' => true],
                                            ['excel_cell' => 'D9', 'field_name' => 'WOT', 'label' => 'No.WOT', 'required' => true],
                                            ['excel_cell' => 'D10', 'field_name' => 'TGL_PRODUKSI', 'label' => 'TGL.PRODUKSI', 'required' => true],
                                            ['excel_cell' => 'D11', 'field_name' => 'MESIN_SHIFT', 'label' => 'MESIN/SHIFT', 'required' => false],
                                            ['excel_cell' => 'D12', 'field_name' => 'OPERATOR', 'label' => 'OPERATOR', 'required' => false],
                                            ['excel_cell' => 'D13', 'field_name' => 'BATCH_NO', 'label' => 'BATCH NO.', 'required' => false],
                                            ['excel_cell' => 'D14', 'field_name' => 'NO_BOX', 'label' => 'NO.BOX', 'required' => false],
                                            ['excel_cell' => 'D15', 'field_name' => 'TANGGAL_KIRIM', 'label' => 'TANGGAL KIRIM', 'required' => false],
                                            ['excel_cell' => 'F5', 'field_name' => 'ISI', 'label' => 'isi (pcs)', 'required' => false],
                                        ];
                                    }
                                    
                                    $existingMapping = old('field_mapping', $template->field_mapping ?? []);
                                    $fieldMapping = !empty($existingMapping) ? $existingMapping : $defaultFieldsFromController;
                                @endphp

                                @foreach($fieldMapping as $index => $field)
                                    <div class="field-mapping-row mb-3 p-3 border rounded" data-index="{{ $index }}">
                                        <div class="row align-items-end">
                                            <div class="col-md-3">
                                                <label class="small">Cell Excel</label>
                                                <input type="text" class="form-control form-control-sm"
                                                       name="field_mapping[{{ $index }}][excel_cell]"
                                                       value="{{ $field['excel_cell'] ?? '' }}"
                                                       placeholder="D5">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="small">Nama Field</label>
                                                <input type="text" class="form-control form-control-sm"
                                                       name="field_mapping[{{ $index }}][field_name]"
                                                       value="{{ $field['field_name'] ?? '' }}"
                                                       placeholder="PC_NO">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="small">Label (Tampilan)</label>
                                                <input type="text" class="form-control form-control-sm"
                                                       name="field_mapping[{{ $index }}][label]"
                                                       value="{{ $field['label'] ?? '' }}"
                                                       placeholder="PC NO.">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-sm btn-danger" onclick="removeFieldMapping(this)">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input"
                                                           name="field_mapping[{{ $index }}][required]"
                                                           value="1"
                                                           {{ isset($field['required']) && $field['required'] ? 'checked' : '' }}>
                                                    <label class="form-check-label small">Wajib diisi</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="btn btn-sm btn-success" onclick="addFieldMapping()">
                                <i class="mdi mdi-plus"></i> Tambah Field
                            </button>
                        </div>

                        <!-- File Template (Untuk Generate PDF) -->
                        <div class="template-form-section" style="background-color: #fff3cd; border-left-color: #ffc107;">
                            <h5><i class="mdi mdi-file-excel"></i> File Template Excel</h5>
                            <p class="text-muted mb-3">
                                <i class="mdi mdi-information"></i>
                                <strong>Wajib upload file template Excel untuk bisa generate PDF.</strong> File akan disimpan di folder <code>public/label/</code>
                            </p>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="template_file">Upload File Template Excel <span class="text-danger">*</span></label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input @error('template_file') is-invalid @enderror"
                                                   id="template_file" name="template_file"
                                                   accept=".xlsx,.xls"
                                                   {{ $mode == 'create' ? 'required' : '' }}>
                                            <label class="custom-file-label" for="template_file">
                                                {{ $template->file_name ?? 'Pilih file Excel...' }}
                                            </label>
                                        </div>
                                        @error('template_file')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="mdi mdi-information"></i> Upload file template Excel (.xlsx atau .xls). File akan disimpan di <code>public/label/</code>
                                        </small>
                                        @if($mode == 'edit' && $template->file_path)
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <i class="mdi mdi-file-excel"></i> File saat ini: <code>{{ basename($template->file_path) }}</code>
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="display: none;">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="file_path">Path File Template (Auto-generated)</label>
                                        <input type="text" class="form-control"
                                               id="file_path" name="file_path"
                                               value="{{ old('file_path', $template->file_path ?? '') }}"
                                               readonly>
                                        <small class="form-text text-muted">Path akan otomatis terisi setelah upload file</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="display: none;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="file_name">Nama File (Auto-generated)</label>
                                        <input type="text" class="form-control"
                                               id="file_name" name="file_name"
                                               value="{{ old('file_name', $template->file_name ?? '') }}"
                                               readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="file_size">Ukuran File (Auto-generated)</label>
                                        <input type="number" class="form-control"
                                               id="file_size" name="file_size"
                                               value="{{ old('file_size', $template->file_size ?? '') }}"
                                               readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi & Catatan -->
                        <div class="template-form-section">
                            <h5><i class="mdi mdi-note-text"></i> Deskripsi & Catatan</h5>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description">Deskripsi Template</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror"
                                                  id="description" name="description" rows="3"
                                                  placeholder="Masukkan deskripsi atau catatan tentang template ini...">{{ old('description', $template->description ?? '') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Catatan tambahan tentang template (opsional)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section-divider"></div>

                        <div class="form-group d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="mdi mdi-content-save"></i> {{ $mode == 'create' ? 'Simpan Template' : 'Update Template' }}
                                </button>
                                <a href="{{ route('label-management.customer.show', $customer->id) }}" class="btn btn-secondary btn-lg">
                                    <i class="mdi mdi-close"></i> Batal
                                </a>
                            </div>
                            <div>
                                <small class="text-muted">
                                    <i class="mdi mdi-information"></i> Pastikan semua field yang wajib (*) sudah diisi
                                </small>
                            </div>
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

        // Field Mapping Functions
        let fieldMappingIndex = {{ count($fieldMapping ?? []) }};

        function addFieldMapping() {
            const html = `
                <div class="field-mapping-row mb-3 p-3 border rounded" data-index="${fieldMappingIndex}">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label class="small">Cell Excel</label>
                            <input type="text" class="form-control form-control-sm"
                                   name="field_mapping[${fieldMappingIndex}][excel_cell]"
                                   placeholder="D5">
                        </div>
                        <div class="col-md-4">
                            <label class="small">Nama Field</label>
                            <input type="text" class="form-control form-control-sm"
                                   name="field_mapping[${fieldMappingIndex}][field_name]"
                                   placeholder="PC_NO">
                        </div>
                        <div class="col-md-4">
                            <label class="small">Label (Tampilan)</label>
                            <input type="text" class="form-control form-control-sm"
                                   name="field_mapping[${fieldMappingIndex}][label]"
                                   placeholder="PC NO.">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeFieldMapping(this)">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input"
                                       name="field_mapping[${fieldMappingIndex}][required]"
                                       value="1">
                                <label class="form-check-label small">Wajib diisi</label>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#fieldMappingContainer').append(html);
            fieldMappingIndex++;
        }

        function removeFieldMapping(btn) {
            Swal.fire({
                title: 'Hapus Field?',
                text: 'Apakah Anda yakin ingin menghapus field mapping ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(btn).closest('.field-mapping-row').remove();
                }
            });
        }
        // Form validation and confirmation with AJAX
        const form = $('#templateForm');

        form.on('submit', function(e) {
            e.preventDefault(); // Always prevent default

            const templateName = $('#template_name').val();
            const templateType = $('#template_type').val();
            const mode = '{{ $mode }}';

            // Validation
            if (!templateName || !templateType) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Nama Template dan Tipe Template wajib diisi'
                });
                return false;
            }

            // Confirmation before submit
            Swal.fire({
                title: 'Simpan Data?',
                text: mode === 'create'
                    ? 'Apakah Anda yakin ingin menyimpan template baru ini?'
                    : 'Apakah Anda yakin ingin mengupdate template ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    // Prepare form data
                    const formElement = document.getElementById('templateForm');
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
                                window.location.href = response.redirect || '{{ route("label-management.customer.show", $customer->id) }}';
                            });
                        } else {
                            // Fallback redirect
                            window.location.href = '{{ route("label-management.customer.show", $customer->id) }}';
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

        // Auto-fill file_name from file_path
        $('#file_path').on('blur', function() {
            const filePath = $(this).val();
            if (filePath && !$('#file_name').val()) {
                // Extract filename from path
                const fileName = filePath.split('\\').pop().split('/').pop();
                if (fileName) {
                    $('#file_name').val(fileName);
                }
            }
        });

        // Handle template type change (show/hide custom input)
        function toggleCustomTemplateType() {
            const templateType = $('#template_type').val();
            const currentTemplateType = '{{ old('template_type', $template->template_type ?? '') }}';

            // Check if current template type is not 'besar' or 'kecil' (meaning it's custom)
            if (templateType === 'custom' || (currentTemplateType && currentTemplateType !== 'besar' && currentTemplateType !== 'kecil')) {
                $('#custom_template_type_wrapper').show();
                $('#custom_template_type').prop('required', true);
                // If editing and template type is custom, set select to 'custom'
                if (currentTemplateType && currentTemplateType !== 'besar' && currentTemplateType !== 'kecil') {
                    $('#template_type').val('custom');
                }
            } else {
                $('#custom_template_type_wrapper').hide();
                $('#custom_template_type').prop('required', false);
            }
        }

        // Initialize on page load
        toggleCustomTemplateType();

        // Handle change event
        $('#template_type').on('change', function() {
            toggleCustomTemplateType();
        });

        // Update template_type value before submit if custom is selected
        $('#templateForm').on('submit', function(e) {
            if ($('#template_type').val() === 'custom') {
                const customType = $('#custom_template_type').val();
                if (!customType || customType.trim() === '') {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Mohon isi nama tipe template custom'
                    });
                    return false;
                }
                // The custom_template_type will be handled in controller
            }
        });

        // Handle file upload
        $('#template_file').on('change', function() {
            const file = this.files[0];
            if (file) {
                // Update label
                $(this).next('.custom-file-label').text(file.name);

                // Auto-fill file_name
                $('#file_name').val(file.name);

                // Auto-fill file_size
                $('#file_size').val(file.size);

                // Auto-set file_path
                const defaultPath = '{{ str_replace("\\", "/", public_path("label")) }}';
                $('#file_path').val(defaultPath + '/' + file.name);
            } else {
                $(this).next('.custom-file-label').text('Pilih file Excel...');
            }
        });
    </script>
@endsection

