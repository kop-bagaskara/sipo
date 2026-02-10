@extends('main.layouts.main')
@section('title')
    Visual Workspace - {{ $template->template_name }}
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .workspace-container {
            display: flex;
            height: calc(100vh - 150px);
            gap: 15px;
        }
        .sidebar {
            width: 300px;
            background: #f8f9fa;
            border-right: 1px solid #dee2e6;
            padding: 20px;
            overflow-y: auto;
        }
        .editor-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .code-editor {
            flex: 1;
            display: flex;
            gap: 15px;
        }
        .editor-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: white;
        }
        .editor-header {
            padding: 10px 15px;
            background: #4472C4;
            color: white;
            font-weight: bold;
            border-radius: 4px 4px 0 0;
        }
        .editor-content {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
        }
        .preview-area {
            flex: 1;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: white;
            overflow: auto;
            padding: 20px;
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
            border-color: #4472C4;
        }
        .field-item.dragging {
            opacity: 0.5;
        }
        textarea.code-textarea {
            width: 100%;
            height: 100%;
            border: none;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            resize: none;
            padding: 10px;
        }
        .preview-frame {
            width: 100%;
            min-height: 500px;
            border: 1px solid #dee2e6;
            background: white;
        }
        .toolbar {
            padding: 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-group-custom {
            display: flex;
            gap: 10px;
        }
        .section-title {
            font-weight: bold;
            color: #4472C4;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #4472C4;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #4472C4;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 12px;
        }
        .field-badge {
            display: inline-block;
            padding: 2px 8px;
            background: #4472C4;
            color: white;
            border-radius: 3px;
            font-size: 11px;
            margin-right: 5px;
        }
    </style>
@endsection
@section('page-title')
    Visual Workspace - {{ $template->template_name }}
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Visual Workspace - {{ $template->template_name }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('label-management.index') }}">Label Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('label-management.customer.show', $customer->id) }}">{{ $customer->customer_name }}</a></li>
                <li class="breadcrumb-item active">Workspace</li>
            </ol>
        </div>
    </div>

    <div class="toolbar">
        <div>
            <h4 class="mb-0">{{ $template->template_name }}</h4>
            <small class="text-muted">Customer: {{ $customer->customer_name }}</small>
        </div>
        <div class="btn-group-custom">
            <button type="button" class="btn btn-success" id="btnSave">
                <i class="mdi mdi-content-save"></i> Simpan Template
            </button>
            <button type="button" class="btn btn-info" id="btnPreview">
                <i class="mdi mdi-eye"></i> Preview PDF
            </button>
            <a href="{{ route('label-management.template.edit', $template->id) }}" class="btn btn-secondary">
                <i class="mdi mdi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="workspace-container">
        <!-- Sidebar: Available Fields -->
        <div class="sidebar">
            <div class="section-title">
                <i class="mdi mdi-drag"></i> Available Fields
            </div>
            <div class="info-box">
                <i class="mdi mdi-information"></i>
                <strong>Tips:</strong> Gunakan placeholder <code>@{{field_name}}</code> untuk menampilkan nilai field. Contoh: <code>@{{ITEM}}</code>, <code>@{{PC_NO}}</code>
            </div>

            @if(count($availableFields) > 0)
                @foreach($availableFields as $field)
                <div class="field-item" draggable="true" data-field="{{ $field['name'] }}">
                    <span class="field-badge">{{ $field['name'] }}</span>
                    <strong>{{ $field['label'] }}</strong>
                </div>
                @endforeach
            @else
                <div class="alert alert-warning">
                    <i class="mdi mdi-alert"></i> Belum ada field mapping. Silakan edit template terlebih dahulu untuk menambahkan field.
                </div>
            @endif

            <div class="section-title mt-4">
                <i class="mdi mdi-code-tags"></i> Special Variables
            </div>
            <div class="field-item">
                <span class="field-badge">CUSTOMER</span>
                <code>@{{CUSTOMER}}</code> - Nama Customer
            </div>
            <div class="field-item">
                <span class="field-badge">QUANTITY</span>
                <code>@{{QUANTITY}}</code> - Jumlah Label
            </div>
        </div>

        <!-- Editor Area -->
        <div class="editor-area">
            <!-- Code Editors -->
            <div class="code-editor">
                <div class="editor-panel">
                    <div class="editor-header">
                        <i class="mdi mdi-code-tags"></i> HTML Template
                    </div>
                    <div class="editor-content">
                        <textarea id="htmlEditor" class="code-textarea" placeholder="Masukkan HTML template di sini...">@if($template->html_template){{ $template->html_template }}@else<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Label Template</title>
</head>
<body>
    <div class="label-container">
        <div class="header">
            <h1>PT. KRISANTHIUM OFFSET PRINTING</h1>
            <p>Jl. Rungkut Industri III /19 Surabaya Telp.8438096,8438182-Fax.8432186</p>
        </div>
        <div class="content">
            <div class="field-row">
                <span class="label">CUSTOMER:</span>
                <span class="value">@{{CUSTOMER}}</span>
            </div>
            <div class="field-row">
                <span class="label">ITEM:</span>
                <span class="value">@{{ITEM}}</span>
            </div>
            <div class="field-row">
                <span class="label">PC NO:</span>
                <span class="value">@{{PC_NO}}</span>
            </div>
        </div>
    </div>
</body>
</html>@endif</textarea>
                    </div>
                </div>
                <div class="editor-panel">
                    <div class="editor-header">
                        <i class="mdi mdi-palette"></i> CSS Styles
                    </div>
                    <div class="editor-content">
                        <textarea id="cssEditor" class="code-textarea" placeholder="Masukkan CSS styles di sini...">{{ $template->css_styles ?? '@page {
    margin: 0;
    size: A4;
}

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
    font-size: 12px;
}

.label-container {
    width: 100%;
    max-width: 800px;
    margin: 0 auto;
    border: 1px solid #000;
    padding: 15px;
}

.header {
    text-align: center;
    border-bottom: 2px solid #000;
    padding-bottom: 10px;
    margin-bottom: 15px;
}

.header h1 {
    font-size: 24px;
    font-weight: bold;
    margin: 0;
}

.field-row {
    display: flex;
    margin-bottom: 8px;
    font-size: 14px;
}

.field-row .label {
    font-weight: bold;
    width: 150px;
}

.field-row .value {
    flex: 1;
}' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Preview Area -->
            <div class="preview-area">
                <div class="section-title">
                    <i class="mdi mdi-eye"></i> Live Preview
                </div>
                <iframe id="previewFrame" class="preview-frame" frameborder="0"></iframe>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Update preview on editor change
            function updatePreview() {
                const html = $('#htmlEditor').val();
                const css = $('#cssEditor').val();

                // Replace placeholders with sample data
                let previewHtml = html
                    .replace(/\{\{CUSTOMER\}\}/g, '{{ $customer->customer_name }}')
                    .replace(/\{\{ITEM\}\}/g, 'Sample Item')
                    .replace(/\{\{PC_NO\}\}/g, '12345678')
                    .replace(/\{\{MC_NO\}\}/g, '87654321')
                    .replace(/\{\{WOT\}\}/g, 'WOT-240306-0001')
                    .replace(/\{\{TGL_PRODUKSI\}\}/g, '23/04/2024')
                    .replace(/\{\{MESIN_SHIFT\}\}/g, 'B3/2')
                    .replace(/\{\{OPERATOR\}\}/g, '900047')
                    .replace(/\{\{BATCH_NO\}\}/g, '1/2/1')
                    .replace(/\{\{NO_BOX\}\}/g, 'BOX-001')
                    .replace(/\{\{TANGGAL_KIRIM\}\}/g, '24/04/2024')
                    .replace(/\{\{ISI\}\}/g, '1200')
                    .replace(/\{\{QUANTITY\}\}/g, '1');

                // Replace all other field placeholders
                @foreach($availableFields as $field)
                previewHtml = previewHtml.replace(new RegExp('\\{\\{' + '{{ $field['name'] }}' + '\\}\\}', 'g'), 'Sample {{ $field['label'] }}');
                @endforeach

                // Inject CSS into HTML
                const fullHtml = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <style>${css}</style>
                    </head>
                    <body>${previewHtml.replace(/<!DOCTYPE html>[\s\S]*?<body[^>]*>/i, '').replace(/<\/body>[\s\S]*?<\/html>/i, '')}</body>
                    </html>
                `;

                const previewFrame = document.getElementById('previewFrame');
                previewFrame.srcdoc = fullHtml;
            }

            // Debounce function
            let previewTimeout;
            function debouncePreview() {
                clearTimeout(previewTimeout);
                previewTimeout = setTimeout(updatePreview, 500);
            }

            // Update preview on input
            $('#htmlEditor, #cssEditor').on('input', debouncePreview);

            // Initial preview
            updatePreview();

            // Drag and drop fields
            $('.field-item').on('dragstart', function(e) {
                e.originalEvent.dataTransfer.setData('text/plain', '@{{' + $(this).data('field') + '}}');
                $(this).addClass('dragging');
            });

            $('.field-item').on('dragend', function() {
                $(this).removeClass('dragging');
            });

            $('#htmlEditor').on('dragover', function(e) {
                e.preventDefault();
            });

            $('#htmlEditor').on('drop', function(e) {
                e.preventDefault();
                const field = e.originalEvent.dataTransfer.getData('text/plain');
                const textarea = this;
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                const text = textarea.value;
                const before = text.substring(0, start);
                const after = text.substring(end, text.length);

                textarea.value = before + field + after;
                textarea.selectionStart = textarea.selectionEnd = start + field.length;
                textarea.focus();
                updatePreview();
            });

            // Save template
            $('#btnSave').on('click', function() {
                const btn = $(this);
                const originalText = btn.html();
                btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...');

                $.ajax({
                    url: '{{ route("label-management.template.save-workspace", $template->id) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        html_template: $('#htmlEditor').val(),
                        css_styles: $('#cssEditor').val(),
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
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Preview PDF
            $('#btnPreview').on('click', function() {
                window.open('{{ route("label-management.template.preview", $template->id) }}', '_blank');
            });
        });
    </script>
@endsection

