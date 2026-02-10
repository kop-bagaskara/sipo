@extends('main.layouts.main')
@section('title')
    Workspace - {{ $customer->customer_name }}
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .customer-detail-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .customer-detail-header {
            border-bottom: 2px solid #4472C4;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .customer-detail-name {
            font-size: 24px;
            font-weight: bold;
            color: #4472C4;
            margin-bottom: 5px;
        }
        .customer-detail-item {
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .customer-detail-label {
            font-weight: bold;
            color: #666;
            display: inline-block;
            width: 150px;
        }
        .customer-detail-value {
            color: #333;
        }
        .template-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #fff;
            transition: all 0.3s ease;
        }
        .template-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .template-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            margin-right: 8px;
        }
        .template-badge-besar {
            background-color: #4472C4;
            color: white;
        }
        .template-badge-kecil {
            background-color: #70AD47;
            color: white;
        }
    </style>
@endsection
@section('page-title')
    Workspace - {{ $customer->customer_name }}
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Workspace - {{ $customer->customer_name }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('label-management.index') }}">Label Management</a></li>
                <li class="breadcrumb-item active">{{ $customer->customer_name }}</li>
            </ol>
        </div>
    </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-alert-circle"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if(request('generated'))
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-success alert-dismissible fade show" role="alert" style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="flex: 1;">
                                <i class="mdi mdi-check-circle"></i>
                                <strong>Label PDF berhasil digenerate!</strong>
                                <br>
                                <small>File: <code>{{ request('generated') }}</code></small>
                                <br>
                                <a href="{{ asset('storage/labels/' . request('generated')) }}" class="btn btn-sm btn-primary mt-2" target="_blank" download>
                                    <i class="mdi mdi-download"></i> Download PDF
                                </a>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="margin-left: 15px;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

    <div class="row">
        <!-- Customer Detail -->
        <div class="col-md-4">
            <div class="customer-detail-card">
                <div class="customer-detail-header">
                    <div class="customer-detail-name">{{ $customer->customer_name }}</div>
                    @if($customer->customer_code)
                        <div style="font-size: 12px; color: #999; font-family: monospace;">Code: {{ $customer->customer_code }}</div>
                    @endif
                </div>

                <div class="customer-detail-item">
                    <span class="customer-detail-label">Brand:</span>
                    <span class="customer-detail-value">{{ $customer->brand_name ?? '-' }}</span>
                </div>

                @if($customer->contact_person)
                <div class="customer-detail-item">
                    <span class="customer-detail-label">Contact:</span>
                    <span class="customer-detail-value">{{ $customer->contact_person }}</span>
                </div>
                @endif

                @if($customer->email)
                <div class="customer-detail-item">
                    <span class="customer-detail-label">Email:</span>
                    <span class="customer-detail-value">{{ $customer->email }}</span>
                </div>
                @endif

                @if($customer->phone)
                <div class="customer-detail-item">
                    <span class="customer-detail-label">Phone:</span>
                    <span class="customer-detail-value">{{ $customer->phone }}</span>
                </div>
                @endif

                @if($customer->address)
                <div class="customer-detail-item">
                    <span class="customer-detail-label">Address:</span>
                    <span class="customer-detail-value">{{ $customer->address }}</span>
                </div>
                @endif

                @if($customer->description)
                <div class="customer-detail-item">
                    <span class="customer-detail-label">Description:</span>
                    <span class="customer-detail-value">{{ $customer->description }}</span>
                </div>
                @endif

                <div class="mt-3">
                    <a href="{{ route('label-management.customer.edit', $customer->id) }}" class="btn btn-sm btn-info">
                        <i class="mdi mdi-pencil"></i> Edit Customer
                    </a>
                    <a href="{{ route('label-management.index') }}" class="btn btn-sm btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <!-- Template List & Form -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Template Label</h4>
                        <a href="{{ route('label-management.template.create', $customer->id) }}" class="btn btn-primary btn-sm">
                            <i class="mdi mdi-plus"></i> Buat Template Baru
                        </a>
                    </div>

                    <div class="row">
                        @php
                            $templateBesar = $customer->templates->firstWhere('template_type', 'besar');
                            $templateKecil = $customer->templates->firstWhere('template_type', 'kecil');
                            $otherTemplates = $customer->templates->filter(function($template) {
                                return $template->template_type != 'besar' && $template->template_type != 'kecil';
                            });
                        @endphp

                        <!-- Template Besar -->
                        @if($templateBesar)
                        <div class="col-md-6 mb-3">
                            <div class="template-card" style="height: 100%;">
                                <div class="d-flex flex-column justify-content-between" style="height: 100%;">
                                    <div>
                                        <div class="mb-3">
                                            <span class="template-badge template-badge-besar">
                                                BESAR
                                            </span>
                                            <strong>{{ $templateBesar->template_name }}</strong>
                                        </div>

                                        @if($templateBesar->field_mapping && count($templateBesar->field_mapping) > 0)
                                            <div style="font-size: 12px; color: #28a745;">
                                                <i class="mdi mdi-check-circle"></i> Template sudah dikonfigurasi
                                            </div>
                                            <div style="font-size: 11px; color: #666; margin-top: 5px;">
                                                {{ count($templateBesar->field_mapping) }} field tersedia
                                            </div>
                                        @else
                                            <div style="font-size: 12px; color: #ff9800;">
                                                <i class="mdi mdi-alert-outline"></i> Template belum dikonfigurasi
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('label-management.template.show', $templateBesar->id) }}" class="btn btn-sm btn-success btn-block" title="View Label History">
                                            <i class="mdi mdi-eye"></i> View
                                        </a>
                                        <div class="btn-group btn-block mt-2" role="group">
                                            <a href="{{ route('label-management.template.workspace', ['id' => $templateBesar->id, 'type' => 'simple']) }}" class="btn btn-sm btn-success" title="Simple Builder - Paling Mudah">
                                                <i class="mdi mdi-wizard-hat"></i> Simple
                                            </a>
                                            <a href="{{ route('label-management.template.workspace', ['id' => $templateBesar->id, 'type' => 'grapesjs']) }}" class="btn btn-sm btn-primary" title="Visual Drag & Drop Editor">
                                                <i class="mdi mdi-palette"></i> Visual
                                            </a>
                                            <a href="{{ route('label-management.template.workspace', $templateBesar->id) }}" class="btn btn-sm btn-warning" title="Word Editor">
                                                <i class="mdi mdi-file-word"></i> Word
                                            </a>
                                        </div>
                                        <a href="{{ route('label-management.template.edit', $templateBesar->id) }}" class="btn btn-sm btn-info btn-block mt-2" title="Edit Template">
                                            <i class="mdi mdi-pencil"></i> Edit Template
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="col-md-6 mb-3">
                            <div class="template-card" style="height: 100%; border: 2px dashed #ddd; background: #f9f9f9;">
                                <div class="d-flex flex-column justify-content-center align-items-center" style="height: 100%; min-height: 150px; text-align: center;">
                                    <i class="mdi mdi-file-document-outline" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                                    <p class="text-muted mb-3">Template Besar belum dibuat</p>
                                    <button class="btn btn-sm btn-primary" onclick="quickCreateTemplate('besar')">
                                        <i class="mdi mdi-plus"></i> Buat Template Besar
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Template Kecil -->
                        @if($templateKecil)
                        <div class="col-md-6 mb-3">
                            <div class="template-card" style="height: 100%;">
                                <div class="d-flex flex-column justify-content-between" style="height: 100%;">
                                    <div>
                                        <div class="mb-3">
                                            <span class="template-badge template-badge-kecil">
                                                KECIL
                                            </span>
                                            <strong>{{ $templateKecil->template_name }}</strong>
                                        </div>

                                        @if($templateKecil->field_mapping && count($templateKecil->field_mapping) > 0)
                                            <div style="font-size: 12px; color: #28a745;">
                                                <i class="mdi mdi-check-circle"></i> Template sudah dikonfigurasi
                                            </div>
                                            <div style="font-size: 11px; color: #666; margin-top: 5px;">
                                                {{ count($templateKecil->field_mapping) }} field tersedia
                                            </div>
                                        @else
                                            <div style="font-size: 12px; color: #ff9800;">
                                                <i class="mdi mdi-alert-outline"></i> Template belum dikonfigurasi
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('label-management.template.show', $templateKecil->id) }}" class="btn btn-sm btn-success btn-block" title="View Label History">
                                            <i class="mdi mdi-eye"></i> View
                                        </a>
                                        <div class="btn-group btn-block mt-2" role="group">
                                            <a href="{{ route('label-management.template.workspace', ['id' => $templateKecil->id, 'type' => 'simple']) }}" class="btn btn-sm btn-success" title="Simple Builder - Paling Mudah">
                                                <i class="mdi mdi-wizard-hat"></i> Simple
                                            </a>
                                            <a href="{{ route('label-management.template.workspace', ['id' => $templateKecil->id, 'type' => 'grapesjs']) }}" class="btn btn-sm btn-primary" title="Visual Drag & Drop Editor">
                                                <i class="mdi mdi-palette"></i> Visual
                                            </a>
                                            <a href="{{ route('label-management.template.workspace', $templateKecil->id) }}" class="btn btn-sm btn-warning" title="Word Editor">
                                                <i class="mdi mdi-file-word"></i> Word
                                            </a>
                                        </div>
                                        <a href="{{ route('label-management.template.edit', $templateKecil->id) }}" class="btn btn-sm btn-info btn-block mt-2" title="Edit Template">
                                            <i class="mdi mdi-pencil"></i> Edit Template
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="col-md-6 mb-3">
                            <div class="template-card" style="height: 100%; border: 2px dashed #ddd; background: #f9f9f9;">
                                <div class="d-flex flex-column justify-content-center align-items-center" style="height: 100%; min-height: 150px; text-align: center;">
                                    <i class="mdi mdi-file-document-outline" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                                    <p class="text-muted mb-3">Template Kecil belum dibuat</p>
                                    <button class="btn btn-sm btn-success" onclick="quickCreateTemplate('kecil')">
                                        <i class="mdi mdi-plus"></i> Buat Template Kecil
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Other Templates (Custom) -->
                        @foreach($otherTemplates as $template)
                        <div class="col-md-6 mb-3">
                            <div class="template-card" style="height: 100%;">
                                <div class="d-flex flex-column justify-content-between" style="height: 100%;">
                                    <div>
                                        <div class="mb-3">
                                            <span class="template-badge" style="background-color: #6c757d; color: white;">
                                                {{ strtoupper($template->template_type) }}
                                            </span>
                                            <strong>{{ $template->template_name }}</strong>
                                        </div>

                                        @if($template->field_mapping && count($template->field_mapping) > 0)
                                            <div style="font-size: 12px; color: #28a745;">
                                                <i class="mdi mdi-check-circle"></i> Template sudah dikonfigurasi
                                            </div>
                                            <div style="font-size: 11px; color: #666; margin-top: 5px;">
                                                {{ count($template->field_mapping) }} field tersedia
                                            </div>
                                        @else
                                            <div style="font-size: 12px; color: #ff9800;">
                                                <i class="mdi mdi-alert-outline"></i> Template belum dikonfigurasi
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('label-management.template.show', $template->id) }}" class="btn btn-sm btn-success btn-block" title="View Label History">
                                            <i class="mdi mdi-eye"></i> View
                                        </a>
                                        <div class="btn-group btn-block mt-2" role="group">
                                            <a href="{{ route('label-management.template.workspace', ['id' => $template->id, 'type' => 'simple']) }}" class="btn btn-sm btn-success" title="Simple Builder - Paling Mudah">
                                                <i class="mdi mdi-wizard-hat"></i> Simple
                                            </a>
                                            <a href="{{ route('label-management.template.workspace', ['id' => $template->id, 'type' => 'grapesjs']) }}" class="btn btn-sm btn-primary" title="Visual Drag & Drop Editor">
                                                <i class="mdi mdi-palette"></i> Visual
                                            </a>
                                            <a href="{{ route('label-management.template.workspace', $template->id) }}" class="btn btn-sm btn-warning" title="Word Editor">
                                                <i class="mdi mdi-file-word"></i> Word
                                            </a>
                                        </div>
                                        <a href="{{ route('label-management.template.edit', $template->id) }}" class="btn btn-sm btn-info btn-block mt-2" title="Edit Template">
                                            <i class="mdi mdi-pencil"></i> Edit Template
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- History Generate Label -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">History Label yang Pernah Dibuat</h4>

                    @if($customer->generations && $customer->generations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Template</th>
                                        <th>Field Values</th>
                                        <th>File PDF</th>
                                        <th>Quantity</th>
                                        <th>Tanggal Generate</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->generations as $index => $generation)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $generation->template->template_name ?? '-' }}</strong>
                                            </td>
                                            <td>
                                                <small>
                                                    @if($generation->field_values)
                                                        @foreach($generation->field_values as $key => $value)
                                                            @if(!empty($value))
                                                                <strong>{{ $key }}:</strong> {{ $value }}<br>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        -
                                                    @endif
                                                </small>
                                            </td>
                                            <td>
                                                <code>{{ $generation->pdf_file_name }}</code>
                                            </td>
                                            <td>{{ $generation->quantity }}</td>
                                            <td>{{ $generation->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ asset('storage/' . $generation->pdf_file_path) }}"
                                                   class="btn btn-sm btn-primary"
                                                   target="_blank"
                                                   download>
                                                    <i class="mdi mdi-download"></i> Download
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center" style="padding: 40px;">
                            <i class="mdi mdi-file-document-outline" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                            <p class="text-muted">Belum ada label yang pernah dibuat</p>
                            <small class="text-muted">Generate label terlebih dahulu untuk melihat history di sini</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- Modal Quick Create Template -->
<div class="modal fade" id="modalQuickCreate" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-plus-circle"></i> Buat Template Baru
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" style="font-size: 12px;">
                    <i class="mdi mdi-information"></i>
                    <strong>Quick Create:</strong> Buat template baru dengan cepat. Anda bisa mengatur field mapping dan upload file Excel nanti di workspace.
                </div>

                <form id="quickCreateForm">
                    <div class="form-group">
                        <label for="quick_template_name">Nama Template <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="quick_template_name"
                               placeholder="Contoh: Template Label Besar" required>
                        <small class="form-text text-muted">Nama template untuk identifikasi</small>
                    </div>

                    <div class="form-group">
                        <label for="quick_template_type">Tipe Template <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="quick_template_type" readonly>
                        <small class="form-text text-muted">Tipe template yang dipilih</small>
                    </div>

                    <div class="form-group">
                        <label for="quick_product_name">Nama Produk (Opsional)</label>
                        <input type="text" class="form-control" id="quick_product_name"
                               placeholder="Contoh: Pepsodent Herbal 150G">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnQuickCreate">
                    <i class="mdi mdi-content-save"></i> Buat & Buka Workspace
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script>
        function deleteTemplate(id, name) {
            Swal.fire({
                title: 'Yakin?',
                text: 'Apakah Anda yakin ingin menghapus template "' + name + '"?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create form for DELETE request
                    const form = $('<form>', {
                        'method': 'POST',
                        'action': '{{ url("sipo/label-management/template") }}/' + id
                    });

                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '_token',
                        'value': '{{ csrf_token() }}'
                    }));

                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '_method',
                        'value': 'DELETE'
                    }));

                    $('body').append(form);
                    form.submit();
                }
            });
        }

        // Quick Create Template
        function quickCreateTemplate(templateType) {
            $('#quick_template_type').val(templateType);
            $('#quick_template_name').val('Template Label ' + (templateType === 'besar' ? 'Besar' : 'Kecil'));
            $('#quick_product_name').val('');
            $('#modalQuickCreate').modal('show');
        }

        $('#btnQuickCreate').on('click', function() {
            const btn = $(this);
            const originalText = btn.html();
            btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Membuat...');

            const templateName = $('#quick_template_name').val();
            const templateType = $('#quick_template_type').val();
            const productName = $('#quick_product_name').val();

            if (!templateName || !templateType) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Nama Template wajib diisi'
                });
                btn.prop('disabled', false).html(originalText);
                return;
            }

            $.ajax({
                url: '{{ route("label-management.template.quick-create", $customer->id) }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    template_name: templateName,
                    template_type: templateType,
                    product_name: productName
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message || 'Template berhasil dibuat',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: response.message || 'Template sudah ada atau terjadi kesalahan'
                        });
                        btn.prop('disabled', false).html(originalText);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat membuat template';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).flat().join(', ');
                        }
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage
                    });
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Show success message if exists
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        @endif
    </script>
@endsection

