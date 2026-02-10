@extends('main.layouts.main')
@section('title')
    Visual Editor - {{ $template->template_name }}
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/grapesjs@0.21.7/dist/css/grapes.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/grapesjs-preset-webpage@1.0.2/dist/grapesjs-preset-webpage.min.css">
    <style>
        body {
            overflow: hidden;
        }
        .gjs-editor {
            height: calc(100vh - 60px);
        }
        .gjs-cv-canvas {
            background: #f5f5f5;
            background-image:
                linear-gradient(white 1px, transparent 1px),
                linear-gradient(90deg, white 1px, transparent 1px);
            background-size: 20px 20px;
        }
        .field-sidebar {
            width: 250px;
            background: #f8f9fa;
            border-right: 1px solid #dee2e6;
            padding: 15px;
            overflow-y: auto;
            height: calc(100vh - 60px);
        }
        .field-item {
            padding: 10px;
            margin-bottom: 8px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            cursor: move;
            transition: all 0.2s;
        }
        .field-item:hover {
            background: #e9ecef;
            border-color: #007bff;
            transform: translateX(5px);
        }
        .field-item i {
            margin-right: 8px;
            color: #007bff;
        }
        .field-item strong {
            display: block;
            font-size: 13px;
        }
        .field-item small {
            display: block;
            color: #666;
            font-size: 11px;
            margin-top: 4px;
        }
        .gjs-block {
            width: 90px;
            height: 60px;
            margin: 5px;
        }
        .gjs-block-label {
            font-size: 11px;
        }
        /* Custom block styles for label components */
        .gjs-block-label-field {
            background: #fff3cd;
            border: 2px dashed #ffc107;
        }
        .gjs-block-label-barcode {
            background: #d1ecf1;
            border: 2px dashed #17a2b8;
        }
        .gjs-block-label-table {
            background: #d4edda;
            border: 2px dashed #28a745;
        }
    </style>
@endsection

@section('content')
<!-- Toolbar -->
<div style="background: #f8f9fa; border-bottom: 1px solid #dee2e6; padding: 8px 15px; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <a href="{{ route('label-management.template.edit', $template->id) }}" class="btn btn-sm btn-secondary">
            <i class="mdi mdi-arrow-left"></i> Kembali
        </a>
        <button class="btn btn-sm btn-success" onclick="window.saveTemplate()" title="Simpan (Ctrl+S)">
            <i class="mdi mdi-content-save"></i> Simpan
        </button>
        <a href="{{ route('label-management.template.preview', $template->id) }}" class="btn btn-sm btn-info" target="_blank">
            <i class="mdi mdi-file-pdf-box"></i> Preview
        </a>
    </div>
    <div>
        <span class="text-muted" style="font-size: 12px;">
            <i class="mdi mdi-palette"></i> Visual Drag & Drop Editor
        </span>
    </div>
</div>

<div style="display: flex; height: calc(100vh - 100px);">
    <!-- Field Sidebar -->
    <div class="field-sidebar">
        <h6 style="margin-bottom: 15px; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
            <span><i class="mdi mdi-tag"></i> Field Data</span>
            <button class="btn btn-sm btn-primary" id="btnManageFields" title="Kelola Field Mapping" style="padding: 2px 8px; font-size: 11px;">
                <i class="mdi mdi-cog"></i>
            </button>
        </h6>
        <div class="field-list">
            @if(count($availableFields) > 0)
                @foreach($availableFields as $field)
                <div class="field-item" draggable="true" data-field="{{ $field['name'] }}" data-label="{{ $field['label'] }}">
                    <i class="mdi mdi-tag"></i>
                    <strong>{{ $field['label'] }}</strong>
                    <small>{{ $field['name'] }}</small>
                </div>
                @endforeach
            @else
                <div class="alert alert-warning" style="font-size: 11px; padding: 8px;">
                    Belum ada field mapping. Klik tombol <i class="mdi mdi-cog"></i> untuk menambahkan field.
                </div>
            @endif
        </div>
    </div>

    <!-- GrapesJS Editor -->
    <div id="gjs-editor" class="gjs-editor" style="flex: 1;"></div>
</div>

<!-- Modal Kelola Field Mapping -->
<div class="modal fade" id="modalManageFields" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-cog"></i> Kelola Field Mapping
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" style="font-size: 12px;">
                    <i class="mdi mdi-information"></i>
                    <strong>Field Mapping:</strong> Tentukan field-field yang bisa digunakan di template. Field ini akan muncul di sidebar untuk ditambahkan ke template.
                </div>

                <div id="fieldMappingContainer">
                    @php
                        $fieldMapping = $template->field_mapping ?? [];
                    @endphp
                    @if(count($fieldMapping) > 0)
                        @foreach($fieldMapping as $index => $field)
                        <div class="field-mapping-row mb-2 p-2 border rounded" data-index="{{ $index }}">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="small">Cell Excel</label>
                                    <input type="text" class="form-control form-control-sm field-excel-cell"
                                           value="{{ $field['excel_cell'] ?? '' }}"
                                           placeholder="D5">
                                </div>
                                <div class="col-md-3">
                                    <label class="small">Nama Field</label>
                                    <input type="text" class="form-control form-control-sm field-name"
                                           value="{{ $field['field_name'] ?? '' }}"
                                           placeholder="PC_NO">
                                </div>
                                <div class="col-md-4">
                                    <label class="small">Label (Tampilan)</label>
                                    <input type="text" class="form-control form-control-sm field-label"
                                           value="{{ $field['label'] ?? '' }}"
                                           placeholder="PC NO.">
                                </div>
                                <div class="col-md-1">
                                    <label class="small">&nbsp;</label>
                                    <button type="button" class="btn btn-sm btn-danger btn-block remove-field" title="Hapus">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input field-required"
                                               {{ isset($field['required']) && $field['required'] ? 'checked' : '' }}>
                                        <label class="form-check-label small">Wajib diisi</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-3">
                            Belum ada field mapping. Klik tombol "Tambah Field" di bawah untuk menambahkan.
                        </div>
                    @endif
                </div>

                <button type="button" class="btn btn-sm btn-success mt-2" id="btnAddField">
                    <i class="mdi mdi-plus"></i> Tambah Field
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSaveFields">
                    <i class="mdi mdi-content-save"></i> Simpan Field Mapping
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/grapesjs@0.21.7/dist/grapes.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/grapesjs-preset-webpage@1.0.2/dist/grapesjs-preset-webpage.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize GrapesJS with simplified configuration
            const editor = grapesjs.init({
                container: '#gjs-editor',
                height: '100%',
                width: 'auto',
                storageManager: {
                    type: 'local',
                    autosave: false,
                },
                plugins: ['gjs-preset-webpage'],
                pluginsOpts: {
                    'gjs-preset-webpage': {
                        modalImportTitle: 'Import Template',
                        modalImportLabel: '<div style="margin-bottom: 10px; font-size: 13px;">Paste here your HTML/CSS and click Import</div>',
                        modalImportContent: function(editor) {
                            return editor.getHtml() + '<style>' + editor.getCss() + '</style>';
                        },
                        textCleanCanvas: 'Clear All',
                    }
                },
                canvas: {
                    styles: [
                        'https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css'
                    ]
                },
            });

            // Add custom blocks for label components
            const fieldOptions = [
                @foreach($availableFields as $field)
                {value: '{{ $field['name'] }}', name: '{{ $field['label'] }}'},
                @endforeach
            ];

            editor.BlockManager.add('label-field', {
                label: '<div style="text-align: center; padding: 10px;"><i class="mdi mdi-tag" style="font-size: 24px; color: #ffc107;"></i><div style="margin-top: 5px; font-size: 11px;">Field Data</div></div>',
                category: 'Label Components',
                content: {
                    type: 'text',
                    content: '{' + '{' + 'FIELD_NAME' + '}' + '}',
                    style: {
                        padding: '10px',
                        'background-color': '#fff3cd',
                        border: '2px dashed #ffc107',
                        'border-radius': '4px',
                        'min-height': '30px',
                    },
                },
            });

            editor.BlockManager.add('label-table', {
                label: '<div style="text-align: center; padding: 10px;"><i class="mdi mdi-table" style="font-size: 24px; color: #28a745;"></i><div style="margin-top: 5px; font-size: 11px;">Table</div></div>',
                category: 'Label Components',
                content: {
                    type: 'table',
                    rows: 3,
                    columns: 3,
                    style: {
                        width: '100%',
                        'border-collapse': 'collapse',
                    },
                },
            });

            // Make field items draggable to canvas
            editor.Canvas.getDocument().addEventListener('dragover', function(e) {
                e.preventDefault();
            });

            editor.Canvas.getDocument().addEventListener('drop', function(e) {
                e.preventDefault();
                try {
                    const data = JSON.parse(e.originalEvent.dataTransfer.getData('text/plain'));
                    if (data && data.type === 'field') {
                        const component = editor.addComponent({
                            type: 'text',
                            content: '{{' + data.field + '}}',
                            style: {
                                padding: '10px',
                                'background-color': '#fff3cd',
                                border: '2px dashed #ffc107',
                                'border-radius': '4px',
                                'min-height': '30px',
                                'display': 'inline-block',
                            },
                        });
                        editor.select(component);
                    }
                } catch (err) {
                    // Not a field drop, ignore
                }
            });

            // Load saved template
            @if($template->html_template)
                @php
                    $savedData = json_decode($template->html_template, true);
                    $savedHTML = $savedData['html'] ?? $savedData['singleLabelHTML'] ?? '';
                    $savedCSS = $savedData['css'] ?? '';
                @endphp
                @if($savedHTML)
                    editor.setComponents('{!! addslashes($savedHTML) !!}');
                @endif
                @if($savedCSS)
                    editor.setStyle('{!! addslashes($savedCSS) !!}');
                @endif
            @endif


            // Save template
            window.saveTemplate = function() {
                const html = editor.getHtml();
                const css = editor.getCss();
                const pageOrientation = 'portrait'; // Can be made dynamic

                const saveData = {
                    html: html,
                    css: css,
                    pageOrientation: pageOrientation
                };

                $.ajax({
                    url: '{{ route("label-management.template.save-workspace", $template->id) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        html_template: JSON.stringify(saveData),
                        css_styles: css,
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message || 'Template berhasil disimpan',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat menyimpan';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage
                        });
                    }
                });
            };

            // Drag field from sidebar
            $('.field-item').on('dragstart', function(e) {
                const fieldName = $(this).data('field');
                const fieldLabel = $(this).data('label');
                e.originalEvent.dataTransfer.setData('text/plain', JSON.stringify({
                    type: 'field',
                    field: fieldName,
                    label: fieldLabel
                }));
            });

            // Keyboard shortcut for save
            $(document).on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                    e.preventDefault();
                    window.saveTemplate();
                }
            });

            // Field Mapping Management (same as before)
            let fieldMappingIndex = {{ count($template->field_mapping ?? []) }};

            $('#btnManageFields').on('click', function() {
                $('#modalManageFields').modal('show');
            });

            $('#btnAddField').on('click', function() {
                const html = `
                    <div class="field-mapping-row mb-2 p-2 border rounded" data-index="${fieldMappingIndex}">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <label class="small">Cell Excel</label>
                                <input type="text" class="form-control form-control-sm field-excel-cell" placeholder="D5">
                            </div>
                            <div class="col-md-3">
                                <label class="small">Nama Field</label>
                                <input type="text" class="form-control form-control-sm field-name" placeholder="PC_NO">
                            </div>
                            <div class="col-md-4">
                                <label class="small">Label (Tampilan)</label>
                                <input type="text" class="form-control form-control-sm field-label" placeholder="PC NO.">
                            </div>
                            <div class="col-md-1">
                                <label class="small">&nbsp;</label>
                                <button type="button" class="btn btn-sm btn-danger btn-block remove-field" title="Hapus">
                                    <i class="mdi mdi-delete"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input field-required">
                                    <label class="form-check-label small">Wajib diisi</label>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#fieldMappingContainer').append(html);
                fieldMappingIndex++;
            });

            $(document).on('click', '.remove-field', function() {
                Swal.fire({
                    title: 'Hapus Field?',
                    text: 'Apakah Anda yakin ingin menghapus field ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).closest('.field-mapping-row').remove();
                    }
                });
            });

            $('#btnSaveFields').on('click', function() {
                const btn = $(this);
                const originalText = btn.html();
                btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...');

                const fieldMapping = [];
                $('#fieldMappingContainer .field-mapping-row').each(function() {
                    const excelCell = $(this).find('.field-excel-cell').val();
                    const fieldName = $(this).find('.field-name').val();
                    const fieldLabel = $(this).find('.field-label').val();
                    const required = $(this).find('.field-required').is(':checked');

                    if (excelCell && fieldName) {
                        fieldMapping.push({
                            excel_cell: excelCell,
                            field_name: fieldName,
                            label: fieldLabel || fieldName,
                            required: required
                        });
                    }
                });

                if (fieldMapping.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Minimal harus ada 1 field mapping'
                    });
                    btn.prop('disabled', false).html(originalText);
                    return;
                }

                $.ajax({
                    url: '{{ route("label-management.template.update-field-mapping", $template->id) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        field_mapping: fieldMapping
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message || 'Field mapping berhasil disimpan',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat menyimpan';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage
                        });
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });
        });
    </script>
@endsection

