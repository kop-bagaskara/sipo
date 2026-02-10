@extends('main.layouts.main')
@section('title')
    Visual Builder - {{ $template->template_name }}
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css">
    <style>
        .workspace-tabs {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        .workspace-tabs .nav-tabs {
            border-bottom: none;
        }
        .workspace-tabs .nav-link {
            color: #495057;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 12px 20px;
        }
        .workspace-tabs .nav-link:hover {
            border-bottom-color: #dee2e6;
            color: #007bff;
        }
        .workspace-tabs .nav-link.active {
            color: #007bff;
            background: transparent;
            border-bottom-color: #007bff;
            font-weight: 600;
        }
        .tab-content {
            height: calc(100vh - 250px);
        }
        .tab-pane {
            height: 100%;
        }
        .builder-container {
            display: flex;
            height: 100%;
            gap: 15px;
        }
        .toolbox {
            width: 250px;
            background: #f8f9fa;
            border-right: 1px solid #dee2e6;
            padding: 15px;
            overflow-y: auto;
        }
        .canvas-area {
            flex: 1;
            background: #e9ecef;
            border: 2px dashed #adb5bd;
            position: relative;
            overflow: auto;
            padding: 20px;
        }
        .properties-panel {
            width: 300px;
            background: #f8f9fa;
            border-left: 1px solid #dee2e6;
            padding: 15px;
            overflow-y: auto;
        }
        .tool-item {
            padding: 12px;
            margin-bottom: 8px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            cursor: move;
            transition: all 0.2s;
            text-align: center;
        }
        .tool-item:hover {
            background: #e9ecef;
            border-color: #4472C4;
        }
        .tool-item i {
            font-size: 24px;
            color: #4472C4;
            display: block;
            margin-bottom: 5px;
        }
        .canvas-element {
            position: absolute;
            border: 2px solid #4472C4;
            background: white;
            cursor: move;
            min-width: 100px;
            min-height: 30px;
            padding: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .canvas-element.selected {
            border-color: #ff6b6b;
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.3);
        }
        .canvas-element .element-content {
            width: 100%;
            height: 100%;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .canvas-element .element-content[data-align="left"] {
            justify-content: flex-start;
        }
        .canvas-element .element-content[data-align="right"] {
            justify-content: flex-end;
        }
        .canvas-element .element-content[data-valign="top"] {
            align-items: flex-start;
        }
        .canvas-element .element-content[data-valign="bottom"] {
            align-items: flex-end;
        }
        .canvas-element .element-handle {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 20px;
            height: 20px;
            background: #ff6b6b;
            border-radius: 50%;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
        }
        .canvas-element.selected .element-handle {
            display: flex;
        }
        .resize-handle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #4472C4;
            border: 1px solid white;
            cursor: nwse-resize;
        }
        .resize-handle.nw { top: -5px; left: -5px; cursor: nw-resize; }
        .resize-handle.ne { top: -5px; right: -5px; cursor: ne-resize; }
        .resize-handle.sw { bottom: -5px; left: -5px; cursor: sw-resize; }
        .resize-handle.se { bottom: -5px; right: -5px; cursor: se-resize; }
        .canvas-element.selected .resize-handle {
            display: block;
        }
        .resize-handle {
            display: none;
        }
        .section-title {
            font-weight: bold;
            color: #4472C4;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #4472C4;
            font-size: 14px;
        }
        .property-group {
            margin-bottom: 15px;
        }
        .property-label {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #666;
        }
        .property-input {
            width: 100%;
            padding: 6px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 12px;
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
        .element-type-badge {
            position: absolute;
            top: -12px;
            left: 5px;
            background: #4472C4;
            color: white;
            padding: 2px 6px;
            font-size: 10px;
            border-radius: 3px;
        }
        .canvas-element.selected .element-type-badge {
            background: #ff6b6b;
        }
    </style>
@endsection
@section('page-title')
    Visual Builder - {{ $template->template_name }}
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Visual Builder - {{ $template->template_name }}</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('label-management.index') }}">Label Management</a></li>
                <li class="breadcrumb-item"><a href="{{ route('label-management.customer.show', $customer->id) }}">{{ $customer->customer_name }}</a></li>
                <li class="breadcrumb-item active">Visual Builder</li>
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
            <button type="button" class="btn btn-warning" id="btnImportExample" title="Upload gambar Excel untuk auto-generate workspace">
                <i class="mdi mdi-image-import"></i> Ambil Contoh
            </button>
            <button type="button" class="btn btn-warning" id="btnClear">
                <i class="mdi mdi-delete"></i> Clear Canvas
            </button>
            <a href="{{ route('label-management.template.edit', $template->id) }}" class="btn btn-secondary">
                <i class="mdi mdi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="workspace-tabs" style="background: #f8f9fa; border-bottom: 2px solid #dee2e6; padding: 0 15px;">
        <ul class="nav nav-tabs" role="tablist" style="border-bottom: none;">
            <li class="nav-item">
                <a class="nav-link active" id="word-tab" data-toggle="tab" href="#word-editor" role="tab">
                    <i class="mdi mdi-file-word"></i> Word Editor
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="visual-tab" data-toggle="tab" href="#visual-editor" role="tab">
                    <i class="mdi mdi-pencil-ruler"></i> Visual Editor
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="html-tab" data-toggle="tab" href="#html-editor" role="tab">
                    <i class="mdi mdi-code-tags"></i> HTML Editor
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="preview-tab" data-toggle="tab" href="#live-preview" role="tab">
                    <i class="mdi mdi-eye"></i> Live Preview
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content" style="height: calc(100vh - 250px);">
        <!-- Visual Editor Tab -->
        <div class="tab-pane fade show active" id="visual-editor" role="tabpanel">
            <div class="builder-container">
                <!-- Toolbox -->
                <div class="toolbox">
            <div class="section-title">
                <i class="mdi mdi-puzzle"></i> Komponen
            </div>
            
            <div class="tool-item" draggable="true" data-type="text">
                <i class="mdi mdi-format-text"></i>
                <div>Text</div>
            </div>
            
            <div class="tool-item" draggable="true" data-type="field">
                <i class="mdi mdi-database"></i>
                <div>Field Data</div>
            </div>
            
            <div class="tool-item" draggable="true" data-type="header">
                <i class="mdi mdi-format-header-1"></i>
                <div>Header</div>
            </div>
            
            <div class="tool-item" draggable="true" data-type="table">
                <i class="mdi mdi-table"></i>
                <div>Table</div>
            </div>
            
            <div class="tool-item" draggable="true" data-type="line">
                <i class="mdi mdi-minus"></i>
                <div>Line/Divider</div>
            </div>

            <div class="section-title mt-4">
                <i class="mdi mdi-drag"></i> Available Fields
            </div>
            @if(count($availableFields) > 0)
                @foreach($availableFields as $field)
                <div class="tool-item" draggable="true" data-type="field" data-field="{{ $field['name'] }}" data-label="{{ $field['label'] }}">
                    <i class="mdi mdi-tag"></i>
                    <div>{{ $field['label'] }}</div>
                    <small style="color: #666;">{{ $field['name'] }}</small>
                </div>
                @endforeach
            @else
                <div class="alert alert-warning" style="font-size: 11px; padding: 8px;">
                    Belum ada field mapping
                </div>
            @endif

            <div class="section-title mt-4">
                <i class="mdi mdi-code-tags"></i> Special
            </div>
            <div class="tool-item" draggable="true" data-type="field" data-field="CUSTOMER" data-label="Customer Name">
                <i class="mdi mdi-account"></i>
                <div>Customer</div>
            </div>
            <div class="tool-item" draggable="true" data-type="field" data-field="QUANTITY" data-label="Quantity">
                <i class="mdi mdi-numeric"></i>
                <div>Quantity</div>
            </div>
        </div>

        <!-- Canvas Area -->
        <div class="canvas-area" id="canvas">
            <div style="text-align: center; color: #999; padding: 50px;">
                <i class="mdi mdi-drag" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                <p>Drag komponen dari toolbox ke sini untuk mulai membuat layout</p>
            </div>
        </div>

                <!-- Properties Panel -->
                <div class="properties-panel" id="propertiesPanel">
            <div class="section-title">
                <i class="mdi mdi-cog"></i> Properties
            </div>
            <div id="noSelection" style="text-align: center; padding: 20px; color: #999;">
                <i class="mdi mdi-cursor-pointer" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>
                <p>Pilih elemen untuk mengatur properties</p>
            </div>
            <div id="elementProperties" style="display: none;">
                <div class="property-group">
                    <label class="property-label">Tipe Elemen</label>
                    <input type="text" class="property-input" id="propType" readonly>
                </div>
                <div class="property-group">
                    <label class="property-label">Field (jika Field Data)</label>
                    <select class="property-input" id="propField">
                        <option value="">-- Pilih Field --</option>
                        @foreach($availableFields as $field)
                        <option value="{{ $field['name'] }}">{{ $field['label'] }} ({{ $field['name'] }})</option>
                        @endforeach
                        <option value="CUSTOMER">Customer Name</option>
                        <option value="QUANTITY">Quantity</option>
                    </select>
                </div>
                <div class="property-group">
                    <label class="property-label">Text/Content</label>
                    <textarea class="property-input" id="propText" rows="3"></textarea>
                </div>
                <div class="property-group">
                    <label class="property-label">X Position (px)</label>
                    <input type="number" class="property-input" id="propX">
                </div>
                <div class="property-group">
                    <label class="property-label">Y Position (px)</label>
                    <input type="number" class="property-input" id="propY">
                </div>
                <div class="property-group">
                    <label class="property-label">Width (px)</label>
                    <input type="number" class="property-input" id="propWidth">
                </div>
                <div class="property-group">
                    <label class="property-label">Height (px)</label>
                    <input type="number" class="property-input" id="propHeight">
                </div>
                <div class="property-group">
                    <label class="property-label">Font Size (px)</label>
                    <input type="number" class="property-input" id="propFontSize" value="12">
                </div>
                <div class="property-group">
                    <label class="property-label">Font Weight</label>
                    <select class="property-input" id="propFontWeight">
                        <option value="normal">Normal</option>
                        <option value="bold">Bold</option>
                    </select>
                </div>
                <div class="property-group">
                    <label class="property-label">Text Align (Horizontal)</label>
                    <select class="property-input" id="propTextAlign">
                        <option value="left">Left</option>
                        <option value="center">Center</option>
                        <option value="right">Right</option>
                    </select>
                </div>
                <div class="property-group">
                    <label class="property-label">Vertical Align</label>
                    <select class="property-input" id="propVerticalAlign">
                        <option value="top">Top</option>
                        <option value="middle">Middle (Center)</option>
                        <option value="bottom">Bottom</option>
                    </select>
                </div>
                <div class="property-group">
                    <label class="property-label">Display Type</label>
                    <select class="property-input" id="propDisplay">
                        <option value="block">Block</option>
                        <option value="flex">Flex</option>
                        <option value="inline-block">Inline Block</option>
                    </select>
                </div>
                <div class="property-group">
                    <label class="property-label">Justify Content (jika Flex)</label>
                    <select class="property-input" id="propJustifyContent">
                        <option value="flex-start">Start</option>
                        <option value="center">Center</option>
                        <option value="flex-end">End</option>
                        <option value="space-between">Space Between</option>
                        <option value="space-around">Space Around</option>
                    </select>
                </div>
                <div class="property-group">
                    <label class="property-label">Align Items (jika Flex)</label>
                    <select class="property-input" id="propAlignItems">
                        <option value="flex-start">Top</option>
                        <option value="center">Center</option>
                        <option value="flex-end">Bottom</option>
                        <option value="stretch">Stretch</option>
                    </select>
                </div>
                <div class="property-group">
                    <label class="property-label">Border</label>
                    <input type="checkbox" id="propBorder"> Tampilkan Border
                </div>
                <div class="property-group">
                    <button type="button" class="btn btn-danger btn-sm btn-block" id="btnDeleteElement">
                        <i class="mdi mdi-delete"></i> Hapus Elemen
                    </button>
                </div>
            </div>
    </div>
</div>

<!-- Modal Import Contoh -->
<div class="modal fade" id="modalImportExample" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="mdi mdi-image-import"></i> Ambil Contoh dari Gambar
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="mdi mdi-information"></i>
                    <strong>Cara menggunakan:</strong>
                    <ol style="margin: 10px 0 0 20px; padding: 0;">
                        <li>Buat label di Excel sesuai keinginan</li>
                        <li>Screenshot atau export Excel sebagai gambar (PNG/JPG)</li>
                        <li>Upload gambar di bawah ini</li>
                        <li>Sistem akan otomatis menganalisis layout dan membuat workspace</li>
                    </ol>
                </div>
                
                <div class="form-group">
                    <label>Upload Gambar Label (PNG, JPG, maks 5MB)</label>
                    <input type="file" class="form-control" id="imageFile" accept="image/png,image/jpeg,image/jpg">
                    <small class="form-text text-muted">Format: PNG atau JPG, ukuran maksimal 5MB</small>
                </div>
                
                <div id="imagePreview" style="display: none; margin-top: 15px;">
                    <label>Preview:</label>
                    <div style="border: 1px solid #ddd; padding: 10px; text-align: center; background: #f9f9f9;">
                        <img id="previewImage" src="" alt="Preview" style="max-width: 100%; max-height: 400px;">
                    </div>
                </div>
                
                <div id="processingStatus" style="display: none; margin-top: 15px;">
                    <div class="alert alert-warning">
                        <i class="mdi mdi-loading mdi-spin"></i> Sedang menganalisis gambar dengan AI...
                        <div class="progress mt-2" style="height: 20px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnProcessImage" disabled>
                    <i class="mdi mdi-auto-fix"></i> Analisis & Generate Workspace
                </button>
                </div>
            </div>
        </div>

        <!-- HTML Editor Tab -->
        <div class="tab-pane fade" id="html-editor" role="tabpanel" style="height: 100%; display: flex; flex-direction: column;">
            <div style="display: flex; height: 100%; border-top: 1px solid #dee2e6;">
                <!-- HTML Editor -->
                <div style="flex: 1; display: flex; flex-direction: column; border-right: 1px solid #dee2e6;">
                    <div style="padding: 10px 15px; background: #f8f9fa; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <i class="mdi mdi-code-tags"></i> <strong>HTML Template</strong>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" id="btnSyncFromHTML">
                            <i class="mdi mdi-sync"></i> Sync ke Visual Editor
                        </button>
                    </div>
                    <div id="htmlEditorContainer" style="flex: 1; overflow: hidden;">
                        <textarea id="htmlCodeEditor" style="width: 100%; height: 100%; border: none; padding: 0; font-family: 'Courier New', monospace; font-size: 13px; resize: none;">&lt;div class="label-container"&gt;
    &lt;!-- Your HTML here --&gt;
&lt;/div&gt;</textarea>
                    </div>
                </div>
                <!-- CSS Editor -->
                <div style="flex: 1; display: flex; flex-direction: column;">
                    <div style="padding: 10px 15px; background: #f8f9fa; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <i class="mdi mdi-palette"></i> <strong>CSS Styles</strong>
                        </div>
                        <button type="button" class="btn btn-sm btn-success" id="btnUpdatePreview">
                            <i class="mdi mdi-refresh"></i> Update Preview
                        </button>
                    </div>
                    <div id="cssEditorContainer" style="flex: 1; overflow: hidden;">
                        <textarea id="cssCodeEditor" style="width: 100%; height: 100%; border: none; padding: 0; font-family: 'Courier New', monospace; font-size: 13px; resize: none;">@page {
    margin: 0;
    size: A4;
}

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
}

.label-container {
    position: relative;
    width: 100%;
    min-height: 500px;
}</textarea>
                </div>
            </div>
        </div>

        <!-- Live Preview Tab -->
        <div class="tab-pane fade" id="live-preview" role="tabpanel" style="height: 100%; display: flex; flex-direction: column;">
            <div style="padding: 10px 15px; background: #f8f9fa; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <i class="mdi mdi-eye"></i> <strong>Live Preview</strong>
                    <small class="text-muted ml-2">Preview akan update otomatis saat edit HTML/CSS</small>
                </div>
                <button type="button" class="btn btn-sm btn-info" id="btnRefreshPreview">
                    <i class="mdi mdi-refresh"></i> Refresh
                </button>
            </div>
            <iframe id="livePreviewFrame" style="flex: 1; width: 100%; border: none; background: white;"></iframe>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/css/css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/htmlmixed/htmlmixed.min.js"></script>
    <script>
        $(document).ready(function() {
            let elements = [];
            let selectedElement = null;
            let elementCounter = 0;
            let isDragging = false;
            let dragOffset = { x: 0, y: 0 };
            let htmlEditor, cssEditor;
            let isSyncing = false; // Prevent infinite loop during sync

            // Initialize CodeMirror editors after a short delay to ensure containers are rendered
            setTimeout(function() {
                const htmlContainer = document.getElementById('htmlEditorContainer');
                const cssContainer = document.getElementById('cssEditorContainer');
                
                htmlEditor = CodeMirror.fromTextArea(document.getElementById('htmlCodeEditor'), {
                    mode: 'htmlmixed',
                    theme: 'monokai',
                    lineNumbers: true,
                    lineWrapping: true,
                    indentUnit: 2,
                    tabSize: 2
                });
                htmlEditor.setSize(null, htmlContainer.offsetHeight);

                cssEditor = CodeMirror.fromTextArea(document.getElementById('cssCodeEditor'), {
                    mode: 'css',
                    theme: 'monokai',
                    lineNumbers: true,
                    lineWrapping: true,
                    indentUnit: 2,
                    tabSize: 2
                });
                cssEditor.setSize(null, cssContainer.offsetHeight);

                // Resize on window resize
                $(window).on('resize', function() {
                    if (htmlEditor && cssEditor) {
                        htmlEditor.setSize(null, htmlContainer.offsetHeight);
                        cssEditor.setSize(null, cssContainer.offsetHeight);
                    }
                });

                // Load saved HTML/CSS if exists
                if (window.savedHTML && htmlEditor) {
                    htmlEditor.setValue(window.savedHTML);
                }
                if (window.savedCSS && cssEditor) {
                    cssEditor.setValue(window.savedCSS);
                }

                // Update preview when editors are ready
                updateLivePreview();
            }, 100);

            // Load saved template if exists
            @if($template->html_template)
            try {
                const savedData = JSON.parse('{!! addslashes($template->html_template) !!}');
                if (savedData.elements) {
                    elements = savedData.elements;
                    renderElements();
                }
                // Store HTML/CSS to set later after editors are initialized
                window.savedHTML = savedData.html || null;
                window.savedCSS = savedData.css || null;
            } catch(e) {
                console.error('Error loading saved template:', e);
            }
            @endif

            // Update live preview when HTML/CSS changes
            let previewTimeout;
            htmlEditor.on('change', function() {
                clearTimeout(previewTimeout);
                previewTimeout = setTimeout(updateLivePreview, 500);
            });
            cssEditor.on('change', function() {
                clearTimeout(previewTimeout);
                previewTimeout = setTimeout(updateLivePreview, 500);
            });

            // Initial preview update
            updateLivePreview();

            // Drag from toolbox
            $('.tool-item').on('dragstart', function(e) {
                const type = $(this).data('type');
                const field = $(this).data('field');
                const label = $(this).data('label');
                
                e.originalEvent.dataTransfer.setData('application/json', JSON.stringify({
                    type: type,
                    field: field,
                    label: label
                }));
            });

            // Drop on canvas
            $('#canvas').on('dragover', function(e) {
                e.preventDefault();
            });

            $('#canvas').on('drop', function(e) {
                e.preventDefault();
                const data = JSON.parse(e.originalEvent.dataTransfer.getData('application/json'));
                const rect = this.getBoundingClientRect();
                const x = e.originalEvent.clientX - rect.left;
                const y = e.originalEvent.clientY - rect.top;

                addElement(data.type, x, y, data.field, data.label);
            });

            function addElement(type, x, y, field = null, label = null) {
                const element = {
                    id: 'elem_' + (elementCounter++),
                    type: type,
                    x: x,
                    y: y,
                    width: type === 'header' ? 400 : type === 'table' ? 500 : 200,
                    height: type === 'header' ? 50 : type === 'line' ? 2 : 30,
                    text: type === 'field' && label ? label : type === 'text' ? 'Text' : type === 'header' ? 'Header' : '',
                    field: field || '',
                    fontSize: type === 'header' ? 24 : 12,
                    fontWeight: type === 'header' ? 'bold' : 'normal',
                    textAlign: 'left',
                    verticalAlign: 'top',
                    display: 'block',
                    justifyContent: 'flex-start',
                    alignItems: 'flex-start',
                    border: type === 'table' || type === 'field'
                };

                elements.push(element);
                renderElements();
                selectElement(element.id);
            }

            function renderElements() {
                $('#canvas').empty();
                if (elements.length === 0) {
                    $('#canvas').html('<div style="text-align: center; color: #999; padding: 50px;"><i class="mdi mdi-drag" style="font-size: 48px; display: block; margin-bottom: 10px;"></i><p>Drag komponen dari toolbox ke sini untuk mulai membuat layout</p></div>');
                    return;
                }

                elements.forEach(function(elem) {
                    // Determine if should use flex for alignment
                    const useFlex = elem.display === 'flex';
                    const baseStyle = {
                        left: elem.x + 'px',
                        top: elem.y + 'px',
                        width: elem.width + 'px',
                        height: elem.height + 'px',
                        fontSize: elem.fontSize + 'px',
                        fontWeight: elem.fontWeight,
                        border: elem.border ? '2px solid #4472C4' : 'none',
                        display: 'flex', // Always use flex for proper alignment
                        flexDirection: 'column' // Default to column for vertical alignment
                    };
                    
                    const $elem = $('<div class="canvas-element"></div>')
                        .attr('data-id', elem.id)
                        .css(baseStyle);

                    // Add type badge
                    $elem.append('<span class="element-type-badge">' + elem.type.toUpperCase() + '</span>');

                    // Add content with proper alignment
                    let content = '';
                    if (elem.type === 'field' && elem.field) {
                        content = '{{' + elem.field + '}}';
                    } else if (elem.type === 'line') {
                        content = '<hr style="margin:0; border: 1px solid #000;">';
                    } else {
                        content = elem.text || '';
                    }
                    
                    // Apply alignment to content wrapper (useFlex already declared above)
                    
                    // Determine horizontal alignment
                    let justifyContent = 'flex-start';
                    if (useFlex) {
                        justifyContent = elem.justifyContent || 'flex-start';
                    } else {
                        if (elem.textAlign === 'center') justifyContent = 'center';
                        else if (elem.textAlign === 'right') justifyContent = 'flex-end';
                        else justifyContent = 'flex-start';
                    }
                    
                    // Determine vertical alignment
                    let alignItems = 'flex-start';
                    if (useFlex) {
                        alignItems = elem.alignItems || 'flex-start';
                    } else {
                        if (elem.verticalAlign === 'middle') alignItems = 'center';
                        else if (elem.verticalAlign === 'bottom') alignItems = 'flex-end';
                        else alignItems = 'flex-start';
                    }
                    
                    const contentStyle = {
                        width: '100%',
                        height: '100%',
                        display: 'flex',
                        justifyContent: justifyContent,
                        alignItems: alignItems,
                        textAlign: elem.textAlign || 'left'
                    };
                    
                    const $content = $('<div class="element-content"></div>')
                        .css(contentStyle)
                        .attr('data-align', elem.textAlign || 'left')
                        .attr('data-valign', elem.verticalAlign || 'top');
                    $content.html(content);
                    $elem.append($content);

                    // Add delete handle
                    $elem.append('<div class="element-handle"><i class="mdi mdi-close"></i></div>');

                    // Add resize handles
                    $elem.append('<div class="resize-handle nw"></div>');
                    $elem.append('<div class="resize-handle ne"></div>');
                    $elem.append('<div class="resize-handle sw"></div>');
                    $elem.append('<div class="resize-handle se"></div>');

                    // Click to select
                    $elem.on('mousedown', function(e) {
                        if (e.target.classList.contains('element-handle') || e.target.closest('.element-handle')) {
                            deleteElement(elem.id);
                            return;
                        }
                        selectElement(elem.id);
                        e.stopPropagation();
                    });

                    // Make draggable
                    makeDraggable($elem, elem);

                    // Make resizable
                    makeResizable($elem, elem);

                    $('#canvas').append($elem);
                });
                
                // Update HTML/CSS editors when elements change (if not syncing)
                if (!isSyncing && htmlEditor && cssEditor) {
                    const template = generateTemplate();
                    htmlEditor.setValue(template.html);
                    cssEditor.setValue(template.css);
                }
            }

            function makeDraggable($elem, elem) {
                let isDragging = false;
                let startX, startY, startLeft, startTop;

                $elem.on('mousedown', function(e) {
                    if (e.target.classList.contains('resize-handle') || e.target.closest('.resize-handle')) {
                        return;
                    }
                    isDragging = true;
                    startX = e.clientX;
                    startY = e.clientY;
                    startLeft = parseInt($elem.css('left'));
                    startTop = parseInt($elem.css('top'));
                    e.preventDefault();
                });

                $(document).on('mousemove', function(e) {
                    if (!isDragging) return;
                    const dx = e.clientX - startX;
                    const dy = e.clientY - startY;
                    const newLeft = startLeft + dx;
                    const newTop = startTop + dy;
                    $elem.css({ left: newLeft + 'px', top: newTop + 'px' });
                    elem.x = newLeft;
                    elem.y = newTop;
                    updateProperties();
                });

                $(document).on('mouseup', function() {
                    isDragging = false;
                });
            }

            function makeResizable($elem, elem) {
                $elem.find('.resize-handle').on('mousedown', function(e) {
                    e.stopPropagation();
                    const handle = $(this);
                    const startX = e.clientX;
                    const startY = e.clientY;
                    const startWidth = elem.width;
                    const startHeight = elem.height;
                    const startLeft = elem.x;
                    const startTop = elem.y;

                    $(document).on('mousemove.resize', function(e) {
                        const dx = e.clientX - startX;
                        const dy = e.clientY - startY;
                        
                        if (handle.hasClass('se')) {
                            elem.width = Math.max(50, startWidth + dx);
                            elem.height = Math.max(20, startHeight + dy);
                        } else if (handle.hasClass('sw')) {
                            elem.width = Math.max(50, startWidth - dx);
                            elem.height = Math.max(20, startHeight + dy);
                            elem.x = startLeft + dx;
                        } else if (handle.hasClass('ne')) {
                            elem.width = Math.max(50, startWidth + dx);
                            elem.height = Math.max(20, startHeight - dy);
                            elem.y = startTop + dy;
                        } else if (handle.hasClass('nw')) {
                            elem.width = Math.max(50, startWidth - dx);
                            elem.height = Math.max(20, startHeight - dy);
                            elem.x = startLeft + dx;
                            elem.y = startTop + dy;
                        }

                        $elem.css({
                            left: elem.x + 'px',
                            top: elem.y + 'px',
                            width: elem.width + 'px',
                            height: elem.height + 'px'
                        });
                        updateProperties();
                    });

                    $(document).on('mouseup.resize', function() {
                        $(document).off('mousemove.resize mouseup.resize');
                    });
                });
            }

            function selectElement(id) {
                selectedElement = elements.find(e => e.id === id);
                $('.canvas-element').removeClass('selected');
                $('.canvas-element[data-id="' + id + '"]').addClass('selected');
                updatePropertiesPanel();
            }

            function updatePropertiesPanel() {
                if (!selectedElement) {
                    $('#noSelection').show();
                    $('#elementProperties').hide();
                    return;
                }

                $('#noSelection').hide();
                $('#elementProperties').show();

                const elem = selectedElement;
                $('#propType').val(elem.type);
                $('#propField').val(elem.field || '');
                $('#propText').val(elem.text || '');
                $('#propX').val(elem.x);
                $('#propY').val(elem.y);
                $('#propWidth').val(elem.width);
                $('#propHeight').val(elem.height);
                $('#propFontSize').val(elem.fontSize);
                $('#propFontWeight').val(elem.fontWeight);
                $('#propTextAlign').val(elem.textAlign);
                $('#propVerticalAlign').val(elem.verticalAlign || 'top');
                $('#propDisplay').val(elem.display || 'block');
                $('#propJustifyContent').val(elem.justifyContent || 'flex-start');
                $('#propAlignItems').val(elem.alignItems || 'flex-start');
                $('#propBorder').prop('checked', elem.border);
            }

            // Update properties on change
            $('#propField, #propText, #propX, #propY, #propWidth, #propHeight, #propFontSize, #propFontWeight, #propTextAlign, #propVerticalAlign, #propDisplay, #propJustifyContent, #propAlignItems, #propBorder').on('change input', function() {
                if (!selectedElement) return;
                updateElement();
            });

            function updateElement() {
                if (!selectedElement) return;
                const elem = selectedElement;
                elem.field = $('#propField').val();
                elem.text = $('#propText').val();
                elem.x = parseInt($('#propX').val()) || 0;
                elem.y = parseInt($('#propY').val()) || 0;
                elem.width = parseInt($('#propWidth').val()) || 100;
                elem.height = parseInt($('#propHeight').val()) || 30;
                elem.fontSize = parseInt($('#propFontSize').val()) || 12;
                elem.fontWeight = $('#propFontWeight').val();
                elem.textAlign = $('#propTextAlign').val();
                elem.verticalAlign = $('#propVerticalAlign').val() || 'top';
                elem.display = $('#propDisplay').val() || 'block';
                elem.justifyContent = $('#propJustifyContent').val() || 'flex-start';
                elem.alignItems = $('#propAlignItems').val() || 'flex-start';
                elem.border = $('#propBorder').is(':checked');

                renderElements();
                selectElement(elem.id);
            }

            function updateProperties() {
                if (selectedElement) {
                    const $elem = $('.canvas-element[data-id="' + selectedElement.id + '"]');
                    selectedElement.x = parseInt($elem.css('left'));
                    selectedElement.y = parseInt($elem.css('top'));
                    selectedElement.width = parseInt($elem.css('width'));
                    selectedElement.height = parseInt($elem.css('height'));
                    updatePropertiesPanel();
                }
            }

            function deleteElement(id) {
                elements = elements.filter(e => e.id !== id);
                if (selectedElement && selectedElement.id === id) {
                    selectedElement = null;
                }
                renderElements();
                updatePropertiesPanel();
            }

            $('#btnDeleteElement').on('click', function() {
                if (selectedElement) {
                    deleteElement(selectedElement.id);
                }
            });

            // Click canvas to deselect
            $('#canvas').on('click', function(e) {
                if (e.target === this || $(e.target).hasClass('canvas-area')) {
                    selectedElement = null;
                    $('.canvas-element').removeClass('selected');
                    updatePropertiesPanel();
                }
            });

            // Generate HTML/CSS from elements
            function generateTemplate() {
                let html = '<div class="label-container" style="position: relative; width: 100%; min-height: 500px;">\n';
                elements.forEach(function(elem) {
                    // Container style
                    const containerStyle = `position: absolute; left: ${elem.x}px; top: ${elem.y}px; width: ${elem.width}px; height: ${elem.height}px; font-size: ${elem.fontSize}px; font-weight: ${elem.fontWeight};`;
                    const borderStyle = elem.border ? 'border: 1px solid #000; padding: 5px;' : '';
                    
                    // Content wrapper style with alignment
                    const contentDisplay = elem.display === 'flex' ? 'flex' : 'block';
                    const contentStyle = `width: 100%; height: 100%; display: ${contentDisplay}; ${elem.display === 'flex' ? `justify-content: ${elem.justifyContent || 'flex-start'}; align-items: ${elem.alignItems || 'flex-start'};` : ''} text-align: ${elem.textAlign || 'left'}; vertical-align: ${elem.verticalAlign || 'top'};`;
                    
                    let content = '';
                    if (elem.type === 'field' && elem.field) {
                        content = '{{' + elem.field + '}}';
                    } else if (elem.type === 'line') {
                        content = '<hr style="margin:0; border: 1px solid #000;">';
                    } else {
                        content = elem.text || '';
                    }

                    html += `    <div class="element-${elem.type}" style="${containerStyle} ${borderStyle}"><div style="${contentStyle}">${content}</div></div>\n`;
                });
                html += '</div>';

                const css = `@page {
    margin: 0;
    size: A4;
}

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
}

.label-container {
    position: relative;
    width: 100%;
    min-height: 500px;
}`;

                // Update HTML/CSS editors if they exist
                if (htmlEditor && !isSyncing) {
                    htmlEditor.setValue(html);
                }
                if (cssEditor && !isSyncing) {
                    cssEditor.setValue(css);
                }

                return { html, css };
            }

            // Sync HTML/CSS to visual editor when HTML is edited
            function syncFromHTML() {
                if (!htmlEditor || !cssEditor) return;
                
                const html = htmlEditor.getValue();
                const css = cssEditor.getValue();
                
                // Parse HTML and convert to elements (simplified)
                // This is a basic implementation - you might want to use a proper HTML parser
                // For now, just show a message
                Swal.fire({
                    icon: 'info',
                    title: 'Info',
                    text: 'Fitur sync dari HTML ke Visual Editor akan segera tersedia. Untuk saat ini, gunakan Visual Editor untuk membuat layout, atau edit langsung di HTML Editor.',
                    timer: 3000,
                    showConfirmButton: false
                });
            }

            // Save template
            $('#btnSave').on('click', function() {
                const btn = $(this);
                const originalText = btn.html();
                btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...');

                // Get HTML and CSS from editors
                const html = htmlEditor.getValue();
                const css = cssEditor.getValue();
                
                // Also generate from visual editor (for compatibility)
                const template = generateTemplate();
                const saveData = {
                    elements: elements,
                    html: html || template.html, // Use HTML editor if available, else generated
                    css: css || template.css // Use CSS editor if available, else generated
                };

                $.ajax({
                    url: '{{ route("label-management.template.save-workspace", $template->id) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        html_template: JSON.stringify(saveData),
                        css_styles: template.css,
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

            // Clear canvas
            $('#btnClear').on('click', function() {
                Swal.fire({
                    title: 'Yakin?',
                    text: 'Semua elemen akan dihapus',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        elements = [];
                        selectedElement = null;
                        renderElements();
                        updatePropertiesPanel();
                    }
                });
            });

            // Preview PDF
            $('#btnPreview').on('click', function() {
                window.open('{{ route("label-management.template.preview", $template->id) }}', '_blank');
            });

            // Update Live Preview
            function updateLivePreview() {
                const html = htmlEditor.getValue();
                const css = cssEditor.getValue();
                const iframe = document.getElementById('livePreviewFrame');
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                
                iframeDoc.open();
                iframeDoc.write(`
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        ${css}
    </style>
</head>
<body>
    ${html}
</body>
</html>
                `);
                iframeDoc.close();
            }

            // Sync from HTML to Visual Editor
            $('#btnSyncFromHTML').on('click', function() {
                syncFromHTML();
            });

            // Update Preview button
            $('#btnUpdatePreview').on('click', function() {
                updateLivePreview();
            });

            // Refresh Preview button
            $('#btnRefreshPreview').on('click', function() {
                updateLivePreview();
            });

            // Update preview when switching to preview tab
            $('#preview-tab').on('shown.bs.tab', function() {
                updateLivePreview();
            });

            // Refresh CodeMirror when HTML tab is shown
            $('#html-tab').on('shown.bs.tab', function() {
                if (htmlEditor) {
                    htmlEditor.refresh();
                }
                if (cssEditor) {
                    cssEditor.refresh();
                }
            });

            // Import Example Modal
            $('#btnImportExample').on('click', function() {
                $('#modalImportExample').modal('show');
            });

            // Image file preview
            $('#imageFile').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file type
                    if (!file.type.match('image.*')) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Format tidak valid',
                            text: 'Hanya file gambar (PNG/JPG) yang diperbolehkan'
                        });
                        $(this).val('');
                        return;
                    }

                    // Validate file size (5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File terlalu besar',
                            text: 'Ukuran file maksimal 5MB'
                        });
                        $(this).val('');
                        return;
                    }

                    // Show preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#previewImage').attr('src', e.target.result);
                        $('#imagePreview').show();
                        $('#btnProcessImage').prop('disabled', false);
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#imagePreview').hide();
                    $('#btnProcessImage').prop('disabled', true);
                }
            });

            // Process image with AI
            $('#btnProcessImage').on('click', function() {
                const fileInput = $('#imageFile')[0];
                if (!fileInput.files || !fileInput.files[0]) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Silakan pilih gambar terlebih dahulu'
                    });
                    return;
                }

                const file = fileInput.files[0];
                const formData = new FormData();
                formData.append('image', file);
                formData.append('_token', '{{ csrf_token() }}');

                // Show processing status
                $('#processingStatus').show();
                $('#btnProcessImage').prop('disabled', true);

                $.ajax({
                    url: '{{ route("label-management.template.import-example", $template->id) }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#processingStatus').hide();
                        
                        if (response.success) {
                            // Clear existing elements
                            elements = [];
                            
                            // If AI returned HTML/CSS directly
                            if (response.html && response.css) {
                                htmlEditor.setValue(response.html);
                                cssEditor.setValue(response.css);
                                updateLivePreview();
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'HTML/CSS berhasil dibuat dari gambar. Silakan cek tab HTML Editor dan Live Preview.',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                                
                                // Switch to HTML Editor tab
                                $('#html-tab').tab('show');
                                
                            } else if (Array.isArray(response.elements)) {
                                // Legacy: Load elements for visual editor
                                elements = response.elements;
                                
                                // Adjust coordinates if needed (scale to fit canvas)
                                const canvasWidth = $('.canvas-area').width() || 800;
                                const canvasHeight = $('.canvas-area').height() || 600;
                                
                                // If AI returned image dimensions, scale accordingly
                                if (response.imageWidth && response.imageHeight) {
                                    const scaleX = canvasWidth / response.imageWidth;
                                    const scaleY = canvasHeight / response.imageHeight;
                                    const scale = Math.min(scaleX, scaleY, 1); // Don't scale up
                                    
                                    elements.forEach(function(elem) {
                                        elem.x = (elem.x * scale);
                                        elem.y = (elem.y * scale);
                                        elem.width = (elem.width * scale);
                                        elem.height = (elem.height * scale);
                                    });
                                }
                                
                                renderElements();
                                updatePropertiesPanel();
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Workspace berhasil dibuat dari gambar. Anda dapat mengedit elemen sesuai kebutuhan.',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Peringatan',
                                    text: 'Gambar berhasil dianalisis, tetapi tidak ada elemen yang terdeteksi. Silakan coba gambar lain atau buat manual.'
                                });
                            }
                            
                            $('#modalImportExample').modal('hide');
                            $('#imageFile').val('');
                            $('#imagePreview').hide();
                            $('#btnProcessImage').prop('disabled', true);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Gagal menganalisis gambar'
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#processingStatus').hide();
                        $('#btnProcessImage').prop('disabled', false);
                        
                        let errorMessage = 'Terjadi kesalahan saat menganalisis gambar';
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
            });

            // Reset modal on close
            $('#modalImportExample').on('hidden.bs.modal', function() {
                $('#imageFile').val('');
                $('#imagePreview').hide();
                $('#processingStatus').hide();
                $('#btnProcessImage').prop('disabled', true);
            });
        });
    </script>
@endsection

