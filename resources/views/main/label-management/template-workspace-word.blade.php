@extends('main.layouts.main')
@section('title')
    Word Editor - {{ $template->template_name }}
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.snow.css">
    <style>
        body {
            overflow: hidden;
        }
        .word-editor-container {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 100px);
        }
        .word-toolbar {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 8px 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            align-items: center;
        }
        .toolbar-group {
            display: flex;
            gap: 2px;
            padding: 0 8px;
            border-right: 1px solid #dee2e6;
            align-items: center;
        }
        .toolbar-group:last-child {
            border-right: none;
        }
        .toolbar-btn {
            padding: 6px 10px;
            border: 1px solid transparent;
            background: transparent;
            cursor: pointer;
            border-radius: 3px;
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 13px;
        }
        .toolbar-btn:hover {
            background: #e9ecef;
            border-color: #dee2e6;
        }
        .toolbar-btn.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        .toolbar-select {
            padding: 4px 8px;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            font-size: 12px;
            background: white;
        }
        .word-editor-area {
            flex: 1;
            display: flex;
            flex-direction: row;
            background: white;
            overflow: hidden;
        }
        .editor-content {
            flex: 1;
            padding: 40px;
            overflow-y: auto;
            background: #f5f5f5;
            background-image:
                linear-gradient(white 1px, transparent 1px),
                linear-gradient(90deg, white 1px, transparent 1px);
            background-size: 20px 20px;
            background-position: 0 0, 0 0;
            min-width: 0;
        }
        #wordEditor {
            min-height: 100%;
            background: white;
            padding: 40px;
            margin: 0 auto;
            max-width: 800px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.5;
        }
        #wordEditor table {
            position: relative;
            margin: 10px 0;
        }
        #wordEditor table:hover {
            outline: 2px dashed #007bff;
        }
        #wordEditor table .col-resize-controls {
            position: absolute;
            top: -60px; /* Position above the table, increased for more space */
            display: flex;
            flex-direction: column;
            gap: 4px;
            z-index: 1003;
            opacity: 0; /* Hidden by default */
            transition: opacity 0.2s;
            pointer-events: none; /* Do not block mouse events when hidden */
            align-items: center;
            background: rgba(255, 255, 255, 0.95);
            padding: 6px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            min-width: 80px;
            visibility: hidden;
        }
        #wordEditor table:hover .col-resize-controls,
        #wordEditor table .col-resize-controls:hover {
            opacity: 1; /* Show on table hover */
            pointer-events: auto; /* Enable mouse events when visible */
            visibility: visible;
        }
        #wordEditor table .col-resize-control-group {
            display: flex;
            flex-direction: column;
            gap: 2px;
            align-items: center;
            width: 100%;
        }
        #wordEditor table .col-resize-label {
            font-size: 10px;
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        #wordEditor table .col-resize-input-group {
            display: flex;
            gap: 2px;
            align-items: center;
            width: 100%;
        }
        #wordEditor table .col-resize-input {
            width: 60px;
            height: 24px;
            padding: 2px 4px;
            border: 1px solid #007bff;
            border-radius: 3px;
            font-size: 11px;
            text-align: center;
            background: white;
            color: #333;
        }
        #wordEditor table .col-resize-input:focus {
            outline: none;
            border-color: #0056b3;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }
        #wordEditor table .col-resize-btn {
            width: 22px;
            height: 22px;
            background: #007bff;
            color: white;
            border: 1px solid #0056b3;
            border-radius: 3px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            padding: 0;
            line-height: 1;
            transition: all 0.2s;
        }
        #wordEditor table .col-resize-btn:hover {
            background: #0056b3;
            transform: scale(1.05);
        }
        #wordEditor table .col-resize-btn:active {
            background: #004085;
            transform: scale(0.95);
        }
        #wordEditor table .resize-handle {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 15px;
            height: 15px;
            background: #007bff;
            cursor: nwse-resize;
            border: 2px solid white;
            border-radius: 2px;
        }
        #wordEditor table .resize-handle:hover {
            background: #0056b3;
        }
        .field-placeholder {
            background: #fff3cd;
            border: 1px dashed #ffc107;
            padding: 2px 6px;
            border-radius: 3px;
            color: #856404;
            font-weight: bold;
            cursor: pointer;
        }
        .field-placeholder:hover {
            background: #ffeaa7;
        }
        .sidebar {
            width: 250px;
            min-width: 250px;
            background: #f8f9fa;
            border-left: 1px solid #dee2e6;
            padding: 15px;
            overflow-y: auto;
            flex-shrink: 0;
        }
        .field-list {
            margin-top: 15px;
        }
        .field-item {
            padding: 8px 12px;
            margin-bottom: 5px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .field-item:hover {
            background: #e9ecef;
            border-color: #007bff;
        }
        .field-item i {
            margin-right: 8px;
            color: #007bff;
        }
    </style>
@endsection

@section('content')
<div class="word-editor-container">
    <!-- Toolbar -->
    <div class="word-toolbar">
        <!-- File Group -->
        <div class="toolbar-group">
            <button class="toolbar-btn" id="btnSave" title="Simpan (Ctrl+S)">
                <i class="mdi mdi-content-save"></i> Simpan
            </button>
            <button class="toolbar-btn" id="btnPreview" title="Preview PDF">
                <i class="mdi mdi-file-pdf-box"></i> Preview
            </button>
        </div>

        <!-- Undo/Redo -->
        <div class="toolbar-group">
            <button class="toolbar-btn" id="btnUndo" title="Undo (Ctrl+Z)">
                <i class="mdi mdi-undo"></i>
            </button>
            <button class="toolbar-btn" id="btnRedo" title="Redo (Ctrl+Y)">
                <i class="mdi mdi-redo"></i>
            </button>
        </div>

        <!-- Font Group -->
        <div class="toolbar-group">
            <select class="toolbar-select" id="fontFamily">
                <option value="Times New Roman">Times New Roman</option>
                <option value="Arial">Arial</option>
                <option value="Calibri">Calibri</option>
                <option value="Courier New">Courier New</option>
                <option value="Georgia">Georgia</option>
            </select>
            <select class="toolbar-select" id="fontSize">
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12" selected>12</option>
                <option value="14">14</option>
                <option value="16">16</option>
                <option value="18">18</option>
                <option value="20">20</option>
                <option value="24">24</option>
                <option value="28">28</option>
                <option value="36">36</option>
            </select>
        </div>

        <!-- Format Group -->
        <div class="toolbar-group">
            <button class="toolbar-btn" id="btnBold" title="Bold (Ctrl+B)">
                <i class="mdi mdi-format-bold"></i>
            </button>
            <button class="toolbar-btn" id="btnItalic" title="Italic (Ctrl+I)">
                <i class="mdi mdi-format-italic"></i>
            </button>
            <button class="toolbar-btn" id="btnUnderline" title="Underline (Ctrl+U)">
                <i class="mdi mdi-format-underline"></i>
            </button>
            <button class="toolbar-btn" id="btnStrikethrough" title="Strikethrough">
                <i class="mdi mdi-format-strikethrough"></i>
            </button>
        </div>

        <!-- Alignment Group -->
        <div class="toolbar-group">
            <button class="toolbar-btn" id="btnAlignLeft" title="Align Left">
                <i class="mdi mdi-format-align-left"></i>
            </button>
            <button class="toolbar-btn" id="btnAlignCenter" title="Align Center">
                <i class="mdi mdi-format-align-center"></i>
            </button>
            <button class="toolbar-btn" id="btnAlignRight" title="Align Right">
                <i class="mdi mdi-format-align-right"></i>
            </button>
            <button class="toolbar-btn" id="btnAlignJustify" title="Justify">
                <i class="mdi mdi-format-align-justify"></i>
            </button>
        </div>

        <!-- List Group -->
        <div class="toolbar-group">
            <button class="toolbar-btn" id="btnBulletList" title="Bullet List">
                <i class="mdi mdi-format-list-bulleted"></i>
            </button>
            <button class="toolbar-btn" id="btnNumberList" title="Number List">
                <i class="mdi mdi-format-list-numbered"></i>
            </button>
        </div>

        <!-- Color Group -->
        <div class="toolbar-group">
            <input type="color" id="textColor" class="toolbar-select" style="width: 40px; height: 28px; padding: 2px;" title="Text Color" value="#000000">
            <input type="color" id="bgColor" class="toolbar-select" style="width: 40px; height: 28px; padding: 2px;" title="Background Color" value="#ffffff">
        </div>

        <!-- Page Orientation -->
        <div class="toolbar-group">
            <select class="toolbar-select" id="pageOrientation" title="Page Orientation">
                <option value="portrait" selected>Portrait</option>
                <option value="landscape">Landscape</option>
            </select>
        </div>

        <!-- Table Group -->
        <div class="toolbar-group">
            <button class="toolbar-btn" id="btnInsertTable" title="Insert Table">
                <i class="mdi mdi-table"></i> Table
            </button>
            <button class="toolbar-btn" id="btnMergeCell" title="Merge Selected Cells">
                <i class="mdi mdi-table-merge-cells"></i> Merge
            </button>
            <div class="dropdown" style="display: inline-block;">
                <button class="toolbar-btn dropdown-toggle" id="btnTableMenu" data-toggle="dropdown" title="Table Menu">
                    <i class="mdi mdi-table-cog"></i> Table Menu
                </button>
                <div class="dropdown-menu" aria-labelledby="btnTableMenu">
                    <a class="dropdown-item" href="#" id="btnInsertRowAbove"><i class="mdi mdi-table-row-plus-before"></i> Insert Row Above</a>
                    <a class="dropdown-item" href="#" id="btnInsertRowBelow"><i class="mdi mdi-table-row-plus-after"></i> Insert Row Below</a>
                    <a class="dropdown-item" href="#" id="btnInsertColLeft"><i class="mdi mdi-table-column-plus-before"></i> Insert Column Left</a>
                    <a class="dropdown-item" href="#" id="btnInsertColRight"><i class="mdi mdi-table-column-plus-after"></i> Insert Column Right</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" id="btnDeleteRow"><i class="mdi mdi-table-row-remove"></i> Delete Row</a>
                    <a class="dropdown-item" href="#" id="btnDeleteCol"><i class="mdi mdi-table-column-remove"></i> Delete Column</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" id="btnCopyTable"><i class="mdi mdi-content-copy"></i> Copy Table</a>
                    <a class="dropdown-item" href="#" id="btnPasteTable"><i class="mdi mdi-content-paste"></i> Paste Table</a>
                </div>
            </div>
            <button class="toolbar-btn" id="btnInsertLine" title="Insert Line">
                <i class="mdi mdi-minus"></i> Line
            </button>
        </div>

        <!-- Import -->
        <div class="toolbar-group">
            <button class="toolbar-btn" id="btnImportExample" title="Ambil Contoh dari Gambar">
                <i class="mdi mdi-image-import"></i> Ambil Contoh
            </button>
        </div>

        <!-- Back -->
        <div class="toolbar-group" style="margin-left: auto;">
            <a href="{{ route('label-management.template.edit', $template->id) }}" class="toolbar-btn">
                <i class="mdi mdi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Editor Area -->
    <div class="word-editor-area">
        <div class="editor-content">
            <div id="wordEditor" contenteditable="true" spellcheck="false">
                @if($template->html_template)
                    @php
                        $savedData = json_decode($template->html_template, true);
                        // Workspace hanya menampilkan single label untuk editing
                        // Jika ada singleLabelHTML, gunakan itu, jika tidak gunakan html biasa
                        $htmlContent = $savedData['singleLabelHTML'] ?? $savedData['html'] ?? '<p>Mulai ketik di sini...</p>';
                        $savedOrientation = $savedData['pageOrientation'] ?? 'portrait';

                        // Jika htmlContent berisi multiple labels (ada class label-item atau label-page-container),
                        // ambil hanya yang pertama
                        if (strpos($htmlContent, 'label-item') !== false || strpos($htmlContent, 'label-page-container') !== false) {
                            // Extract single label dari multiple labels
                            $dom = new DOMDocument();
                            @$dom->loadHTML('<?xml encoding="UTF-8">' . $htmlContent);
                            $xpath = new DOMXPath($dom);
                            $labelItems = $xpath->query('//div[contains(@class, "label-item")]');
                            if ($labelItems->length > 0) {
                                // Ambil content dari label pertama
                                $firstLabel = $labelItems->item(0);
                                $innerContent = '';
                                foreach ($firstLabel->childNodes as $child) {
                                    $innerContent .= $dom->saveHTML($child);
                                }
                                // Cari div dengan class label-content
                                $labelContent = $xpath->query('.//div[contains(@class, "label-content")]', $firstLabel);
                                if ($labelContent->length > 0) {
                                    $htmlContent = '';
                                    foreach ($labelContent->item(0)->childNodes as $child) {
                                        $htmlContent .= $dom->saveHTML($child);
                                    }
                                } else {
                                    $htmlContent = $innerContent;
                                }
                            }
                        }
                    @endphp
                    {!! $htmlContent !!}
                @else
                    <p>Mulai ketik di sini...</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <h6 style="margin: 0; font-weight: bold;">
                    <i class="mdi mdi-tag"></i> Field Data
                </h6>
                <button class="btn btn-sm btn-primary" id="btnManageFields" title="Kelola Field Mapping" style="padding: 2px 8px; font-size: 11px;">
                    <i class="mdi mdi-cog"></i>
                </button>
            </div>
            <div class="field-list">
                @if(count($availableFields) > 0)
                    @foreach($availableFields as $field)
                    <div class="field-item" data-field="{{ $field['name'] }}" data-label="{{ $field['label'] }}">
                        <i class="mdi mdi-tag"></i>
                        <strong>{{ $field['label'] }}</strong>
                        <small style="display: block; color: #666; margin-top: 2px;">{{ $field['name'] }}</small>
                    </div>
                    @endforeach
                @else
                    <div class="alert alert-warning" style="font-size: 11px; padding: 8px;">
                        Belum ada field mapping. Klik tombol <i class="mdi mdi-cog"></i> untuk menambahkan field.
                    </div>
                @endif
            </div>
        </div>
    </div>
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
                    <i class="mdi mdi-auto-fix"></i> Analisis & Generate
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script>
        $(document).ready(function() {
            const editor = document.getElementById('wordEditor');
            let undoStack = [];
            let redoStack = [];
            let maxUndoStack = 50;
            let pageOrientation = 'portrait'; // Initialize page orientation

            // Save state for undo/redo
            function saveState() {
                const html = editor.innerHTML;
                undoStack.push(html);
                if (undoStack.length > maxUndoStack) {
                    undoStack.shift();
                }
                redoStack = [];
            }

            // Initialize with saved state
            saveState();

            // Page Orientation function (define before use)
            function updatePageOrientation() {
                const editorEl = document.getElementById('wordEditor');
                if (pageOrientation === 'landscape') {
                    editorEl.style.maxWidth = '1100px';
                } else {
                    editorEl.style.maxWidth = '800px';
                }
            }

            // Load saved page orientation
            @if($template->html_template)
                @php
                    $savedData = json_decode($template->html_template, true);
                    $savedOrientation = $savedData['pageOrientation'] ?? 'portrait';
                @endphp
                pageOrientation = '{{ $savedOrientation }}';
                $('#pageOrientation').val(pageOrientation);
                updatePageOrientation();
            @else
                updatePageOrientation();
            @endif

            // Save state on input
            editor.addEventListener('input', function() {
                saveState();
            });

            // Undo
            $('#btnUndo').on('click', function() {
                if (undoStack.length > 1) {
                    redoStack.push(undoStack.pop());
                    editor.innerHTML = undoStack[undoStack.length - 1];
                }
            });

            // Redo
            $('#btnRedo').on('click', function() {
                if (redoStack.length > 0) {
                    undoStack.push(redoStack.pop());
                    editor.innerHTML = undoStack[undoStack.length - 1];
                }
            });

            // Keyboard shortcuts
            $(document).on('keydown', function(e) {
                if (e.ctrlKey || e.metaKey) {
                    if (e.key === 'z' && !e.shiftKey) {
                        e.preventDefault();
                        $('#btnUndo').click();
                    } else if (e.key === 'y' || (e.key === 'z' && e.shiftKey)) {
                        e.preventDefault();
                        $('#btnRedo').click();
                    } else if (e.key === 's') {
                        e.preventDefault();
                        $('#btnSave').click();
                    }
                }
            });

            // Format commands
            function execCommand(command, value = null) {
                document.execCommand(command, false, value);
                editor.focus();
            }

            // Font Family
            $('#fontFamily').on('change', function() {
                execCommand('fontName', $(this).val());
            });

            // Font Size
            $('#fontSize').on('change', function() {
                execCommand('fontSize', $(this).val());
            });

            // Format buttons
            $('#btnBold').on('click', function() {
                execCommand('bold');
                $(this).toggleClass('active', document.queryCommandState('bold'));
            });

            $('#btnItalic').on('click', function() {
                execCommand('italic');
                $(this).toggleClass('active', document.queryCommandState('italic'));
            });

            $('#btnUnderline').on('click', function() {
                execCommand('underline');
                $(this).toggleClass('active', document.queryCommandState('underline'));
            });

            $('#btnStrikethrough').on('click', function() {
                execCommand('strikeThrough');
                $(this).toggleClass('active', document.queryCommandState('strikeThrough'));
            });

            // Alignment
            $('#btnAlignLeft').on('click', function() {
                execCommand('justifyLeft');
                updateAlignmentButtons();
            });

            $('#btnAlignCenter').on('click', function() {
                execCommand('justifyCenter');
                updateAlignmentButtons();
            });

            $('#btnAlignRight').on('click', function() {
                execCommand('justifyRight');
                updateAlignmentButtons();
            });

            $('#btnAlignJustify').on('click', function() {
                execCommand('justifyFull');
                updateAlignmentButtons();
            });

            function updateAlignmentButtons() {
                $('.toolbar-btn').removeClass('active');
                if (document.queryCommandState('justifyLeft')) $('#btnAlignLeft').addClass('active');
                if (document.queryCommandState('justifyCenter')) $('#btnAlignCenter').addClass('active');
                if (document.queryCommandState('justifyRight')) $('#btnAlignRight').addClass('active');
                if (document.queryCommandState('justifyFull')) $('#btnAlignJustify').addClass('active');
            }

            // Lists
            $('#btnBulletList').on('click', function() {
                execCommand('insertUnorderedList');
            });

            $('#btnNumberList').on('click', function() {
                execCommand('insertOrderedList');
            });

            // Colors
            $('#textColor').on('change', function() {
                execCommand('foreColor', $(this).val());
            });

            $('#bgColor').on('change', function() {
                execCommand('backColor', $(this).val());
            });

            // Helper function to get current cell
            function getCurrentCell() {
                const selection = window.getSelection();
                if (selection.rangeCount === 0) return null;

                let node = selection.anchorNode;
                while (node && node.nodeName !== 'TD' && node.nodeName !== 'TH') {
                    node = node.parentNode;
                }
                return node;
            }

            // Helper function to get current table
            function getCurrentTable() {
                const cell = getCurrentCell();
                if (!cell) return null;
                return cell.closest('table');
            }

            // Add resize handle to tables
            function addResizeHandles() {
                const tables = editor.querySelectorAll('table');
                tables.forEach(function(table) {
                    // Remove existing handle and controls
                    const existingHandle = table.querySelector('.resize-handle');
                    if (existingHandle) {
                        existingHandle.remove();
                    }
                    const existingControls = table.querySelectorAll('.col-resize-controls');
                    existingControls.forEach(ctrl => ctrl.remove());

                    // Add new handle for table resize
                    const handle = document.createElement('div');
                    handle.className = 'resize-handle';
                    handle.style.position = 'absolute';
                    handle.style.bottom = '0';
                    handle.style.right = '0';
                    handle.style.width = '15px';
                    handle.style.height = '15px';
                    handle.style.background = '#007bff';
                    handle.style.cursor = 'nwse-resize';
                    handle.style.border = '2px solid white';
                    handle.style.borderRadius = '2px';
                    handle.style.zIndex = '1000';

                    table.style.position = 'relative';
                    table.appendChild(handle);

                    // Add column resize controls for each column
                    addColumnResizeControls(table);

                    // Make table resizable
                    let isResizing = false;
                    let startX, startY, startWidth, startHeight;

                    handle.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        isResizing = true;
                        startX = e.clientX;
                        startY = e.clientY;
                        startWidth = table.offsetWidth;
                        startHeight = table.offsetHeight;

                        document.addEventListener('mousemove', handleMouseMove);
                        document.addEventListener('mouseup', handleMouseUp);
                    });

                    function handleMouseMove(e) {
                        if (!isResizing) return;
                        const deltaX = e.clientX - startX;
                        const deltaY = e.clientY - startY;
                        table.style.width = (startWidth + deltaX) + 'px';
                        table.style.height = (startHeight + deltaY) + 'px';
                    }

                    function handleMouseUp() {
                        isResizing = false;
                        document.removeEventListener('mousemove', handleMouseMove);
                        document.removeEventListener('mouseup', handleMouseUp);
                        saveState();
                    }
                });
            }

            // Add column resize controls (buttons + and -) for each column
            function addColumnResizeControls(table) {
                const firstRow = table.rows[0];
                if (!firstRow || firstRow.cells.length < 1) {
                    console.log('No first row or cells found');
                    return;
                }

                console.log('Adding controls for', firstRow.cells.length, 'columns');

                let cumulativeWidth = 0;

                // Create controls for EACH column
                for (let i = 0; i < firstRow.cells.length; i++) {
                    const cell = firstRow.cells[i];
                    const cellWidth = cell.offsetWidth || cell.getBoundingClientRect().width;
                    const cellHeight = cell.offsetHeight || cell.getBoundingClientRect().height;
                    const cellCenter = cumulativeWidth + (cellWidth / 2);

                    // Create controls container for this column
                    const controls = document.createElement('div');
                    controls.className = 'col-resize-controls';
                    controls.dataset.colIndex = i;
                    controls.style.position = 'absolute';
                    controls.style.top = '-60px';
                    controls.style.left = (cellCenter - 40) + 'px'; // Center: min-width 80px, so -40px
                    controls.style.zIndex = '1003';
                    controls.style.opacity = '0';
                    controls.style.visibility = 'hidden';
                    controls.style.transition = 'opacity 0.3s ease, visibility 0.3s ease';

                    // Width control group
                    const widthGroup = document.createElement('div');
                    widthGroup.className = 'col-resize-control-group';

                    const widthLabel = document.createElement('div');
                    widthLabel.className = 'col-resize-label';
                    widthLabel.textContent = 'Width';
                    widthGroup.appendChild(widthLabel);

                    const widthInputGroup = document.createElement('div');
                    widthInputGroup.className = 'col-resize-input-group';

                    const widthInput = document.createElement('input');
                    widthInput.type = 'number';
                    widthInput.className = 'col-resize-input';
                    widthInput.value = Math.round(cellWidth);
                    widthInput.min = '20';
                    widthInput.step = '1';
                    widthInput.dataset.colIndex = i;
                    widthInput.dataset.type = 'width';

                    const widthDecrease = document.createElement('button');
                    widthDecrease.className = 'col-resize-btn';
                    widthDecrease.innerHTML = '−';
                    widthDecrease.type = 'button';
                    widthDecrease.title = 'Kurangi width';

                    const widthIncrease = document.createElement('button');
                    widthIncrease.className = 'col-resize-btn';
                    widthIncrease.innerHTML = '+';
                    widthIncrease.type = 'button';
                    widthIncrease.title = 'Tambah width';

                    widthInputGroup.appendChild(widthDecrease);
                    widthInputGroup.appendChild(widthInput);
                    widthInputGroup.appendChild(widthIncrease);
                    widthGroup.appendChild(widthInputGroup);

                    // Height control group
                    const heightGroup = document.createElement('div');
                    heightGroup.className = 'col-resize-control-group';

                    const heightLabel = document.createElement('div');
                    heightLabel.className = 'col-resize-label';
                    heightLabel.textContent = 'Height';
                    heightGroup.appendChild(heightLabel);

                    const heightInputGroup = document.createElement('div');
                    heightInputGroup.className = 'col-resize-input-group';

                    const heightInput = document.createElement('input');
                    heightInput.type = 'number';
                    heightInput.className = 'col-resize-input';
                    heightInput.value = Math.round(cellHeight);
                    heightInput.min = '20';
                    heightInput.step = '1';
                    heightInput.dataset.colIndex = i;
                    heightInput.dataset.type = 'height';

                    const heightDecrease = document.createElement('button');
                    heightDecrease.className = 'col-resize-btn';
                    heightDecrease.innerHTML = '−';
                    heightDecrease.type = 'button';
                    heightDecrease.title = 'Kurangi height';

                    const heightIncrease = document.createElement('button');
                    heightIncrease.className = 'col-resize-btn';
                    heightIncrease.innerHTML = '+';
                    heightIncrease.type = 'button';
                    heightIncrease.title = 'Tambah height';

                    heightInputGroup.appendChild(heightDecrease);
                    heightInputGroup.appendChild(heightInput);
                    heightInputGroup.appendChild(heightIncrease);
                    heightGroup.appendChild(heightInputGroup);

                    // Add to controls container
                    controls.appendChild(widthGroup);
                    controls.appendChild(heightGroup);

                    // Add event handlers
                    (function(colIndex, wInput, hInput) {
                        // Width controls
                        widthDecrease.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            const current = parseInt(wInput.value) || cellWidth;
                            wInput.value = Math.max(20, current - 5);
                            resizeColumn(table, colIndex, parseInt(wInput.value) - current, 'width');
                        });

                        widthIncrease.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            const current = parseInt(wInput.value) || cellWidth;
                            wInput.value = current + 5;
                            resizeColumn(table, colIndex, 5, 'width');
                        });

                        widthInput.addEventListener('change', function(e) {
                            const newValue = Math.max(20, parseInt(this.value) || cellWidth);
                            this.value = newValue;
                            const current = firstRow.cells[colIndex].offsetWidth;
                            resizeColumn(table, colIndex, newValue - current, 'width');
                        });

                        // Mouse wheel for width
                        widthInput.addEventListener('wheel', function(e) {
                            e.preventDefault();
                            const delta = e.deltaY > 0 ? -5 : 5;
                            const current = parseInt(this.value) || cellWidth;
                            this.value = Math.max(20, current + delta);
                            resizeColumn(table, colIndex, delta, 'width');
                        });

                        // Height controls
                        heightDecrease.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            const current = parseInt(hInput.value) || cellHeight;
                            hInput.value = Math.max(20, current - 5);
                            resizeColumn(table, colIndex, parseInt(hInput.value) - current, 'height');
                        });

                        heightIncrease.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            const current = parseInt(hInput.value) || cellHeight;
                            hInput.value = current + 5;
                            resizeColumn(table, colIndex, 5, 'height');
                        });

                        hInput.addEventListener('change', function(e) {
                            const newValue = Math.max(20, parseInt(this.value) || cellHeight);
                            this.value = newValue;
                            resizeColumn(table, colIndex, newValue, 'height');
                        });

                        // Mouse wheel for height
                        hInput.addEventListener('wheel', function(e) {
                            e.preventDefault();
                            const delta = e.deltaY > 0 ? -5 : 5;
                            const current = parseInt(this.value) || cellHeight;
                            this.value = Math.max(20, current + delta);
                            resizeColumn(table, colIndex, delta, 'height');
                        });
                    })(i, widthInput, heightInput);

                    table.appendChild(controls);

                    console.log('Created controls for column', i, 'at position', cellCenter - 40);

                    cumulativeWidth += cellWidth;
                }
            }

            // Function to resize a specific column (width or height)
            function resizeColumn(table, colIndex, deltaPx, type = 'width') {
                const firstRow = table.rows[0];
                if (!firstRow || !firstRow.cells[colIndex]) return;

                if (type === 'width') {
                    const currentWidth = firstRow.cells[colIndex].offsetWidth;
                    const newWidth = Math.max(20, currentWidth + deltaPx);

                    // Resize all cells in this column across all rows
                    for (let rowIndex = 0; rowIndex < table.rows.length; rowIndex++) {
                        const row = table.rows[rowIndex];
                        if (row.cells[colIndex]) {
                            row.cells[colIndex].style.width = newWidth + 'px';
                        }
                    }

                    // Update width input value
                    const controls = table.querySelector(`.col-resize-controls[data-col-index="${colIndex}"]`);
                    if (controls) {
                        const widthInput = controls.querySelector('input[data-type="width"]');
                        if (widthInput) {
                            widthInput.value = Math.round(newWidth);
                        }
                    }

                    // Update controls positions after resize
                    setTimeout(() => {
                        updateColumnControlsPositions(table);
                    }, 10);
                } else if (type === 'height') {
                    // For height, resize all cells in the first row (or all rows if needed)
                    const currentHeight = firstRow.cells[colIndex].offsetHeight;
                    const newHeight = Math.max(20, currentHeight + deltaPx);

                    // Resize all cells in this column across all rows
                    for (let rowIndex = 0; rowIndex < table.rows.length; rowIndex++) {
                        const row = table.rows[rowIndex];
                        if (row.cells[colIndex]) {
                            row.cells[colIndex].style.height = newHeight + 'px';
                        }
                    }

                    // Update height input value
                    const controls = table.querySelector(`.col-resize-controls[data-col-index="${colIndex}"]`);
                    if (controls) {
                        const heightInput = controls.querySelector('input[data-type="height"]');
                        if (heightInput) {
                            heightInput.value = Math.round(newHeight);
                        }
                    }
                }

                saveState();
            }

            // Update column controls positions
            function updateColumnControlsPositions(table) {
                const controls = table.querySelectorAll('.col-resize-controls');
                const firstRow = table.rows[0];
                if (!firstRow) return;

                let cumulativeWidth = 0;
                controls.forEach((control) => {
                    const colIndex = parseInt(control.dataset.colIndex);
                    if (firstRow.cells[colIndex]) {
                        const cellWidth = firstRow.cells[colIndex].offsetWidth || firstRow.cells[colIndex].getBoundingClientRect().width;
                        const cellCenter = cumulativeWidth + (cellWidth / 2);
                        control.style.left = (cellCenter - 40) + 'px'; // Updated for new min-width

                        // Update width input value
                        const widthInput = control.querySelector('input[data-type="width"]');
                        if (widthInput) {
                            widthInput.value = Math.round(cellWidth);
                        }

                        cumulativeWidth += cellWidth;
                    }
                });
            }

            // Force show controls on table hover (backup method using jQuery)
            $(document).on('mouseenter', '#wordEditor table', function() {
                $(this).find('.col-resize-controls').css({
                    'opacity': '1',
                    'visibility': 'visible',
                    'pointer-events': 'auto'
                });
            });

            $(document).on('mouseleave', '#wordEditor table', function() {
                $(this).find('.col-resize-controls').css({
                    'opacity': '0',
                    'visibility': 'hidden',
                    'pointer-events': 'none'
                });
            });

            // Insert Table
            $('#btnInsertTable').on('click', function() {
                const rows = prompt('Jumlah baris:', '3');
                const cols = prompt('Jumlah kolom:', '3');
                if (rows && cols) {
                    let table = '<table border="1" style="border-collapse: collapse; width: 100%; margin: 10px 0; position: relative;">';
                    for (let i = 0; i < parseInt(rows); i++) {
                        table += '<tr>';
                        for (let j = 0; j < parseInt(cols); j++) {
                            table += '<td style="padding: 5px; border: 1px solid #000;">&nbsp;</td>';
                        }
                        table += '</tr>';
                    }
                    table += '</table>';
                    execCommand('insertHTML', table);
                    setTimeout(addResizeHandles, 100);
                    saveState();
                }
            });

            // Update resize handles when content changes
            editor.addEventListener('input', function() {
                setTimeout(addResizeHandles, 100);
            });

            // Update column controls when table structure changes
            editor.addEventListener('DOMSubtreeModified', function() {
                const tables = editor.querySelectorAll('table');
                tables.forEach(table => {
                    setTimeout(() => {
                        updateColumnControlsPositions(table);
                    }, 50);
                });
            });

            // Initial add resize handles
            setTimeout(addResizeHandles, 500);

            // Insert Row Above
            $('#btnInsertRowAbove').on('click', function(e) {
                e.preventDefault();
                const cell = getCurrentCell();
                if (!cell) {
                    Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Silakan klik di dalam cell tabel terlebih dahulu', timer: 2000, showConfirmButton: false });
                    return;
                }
                const table = getCurrentTable();
                const row = cell.parentNode;
                const newRow = row.cloneNode(true);
                newRow.querySelectorAll('td, th').forEach(c => {
                    c.innerHTML = '&nbsp;';
                    c.removeAttribute('rowspan');
                    c.removeAttribute('colspan');
                });
                row.parentNode.insertBefore(newRow, row);
                saveState();
            });

            // Insert Row Below
            $('#btnInsertRowBelow').on('click', function(e) {
                e.preventDefault();
                const cell = getCurrentCell();
                if (!cell) {
                    Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Silakan klik di dalam cell tabel terlebih dahulu', timer: 2000, showConfirmButton: false });
                    return;
                }
                const table = getCurrentTable();
                const row = cell.parentNode;
                const newRow = row.cloneNode(true);
                newRow.querySelectorAll('td, th').forEach(c => {
                    c.innerHTML = '&nbsp;';
                    c.removeAttribute('rowspan');
                    c.removeAttribute('colspan');
                });
                row.parentNode.insertBefore(newRow, row.nextSibling);
                saveState();
            });

            // Insert Column Left
            $('#btnInsertColLeft').on('click', function(e) {
                e.preventDefault();
                const cell = getCurrentCell();
                if (!cell) {
                    Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Silakan klik di dalam cell tabel terlebih dahulu', timer: 2000, showConfirmButton: false });
                    return;
                }
                const table = getCurrentTable();
                const cellIndex = Array.from(cell.parentNode.cells).indexOf(cell);
                Array.from(table.rows).forEach(row => {
                    const newCell = document.createElement('td');
                    newCell.style.padding = '5px';
                    newCell.style.border = '1px solid #000';
                    newCell.innerHTML = '&nbsp;';
                    newCell.setAttribute('contenteditable', 'true');
                    if (row.cells[cellIndex]) {
                        row.insertBefore(newCell, row.cells[cellIndex]);
                    } else {
                        row.appendChild(newCell);
                    }
                });
                saveState();
            });

            // Insert Column Right
            $('#btnInsertColRight').on('click', function(e) {
                e.preventDefault();
                const cell = getCurrentCell();
                if (!cell) {
                    Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Silakan klik di dalam cell tabel terlebih dahulu', timer: 2000, showConfirmButton: false });
                    return;
                }
                const table = getCurrentTable();
                const cellIndex = Array.from(cell.parentNode.cells).indexOf(cell);
                Array.from(table.rows).forEach(row => {
                    const newCell = document.createElement('td');
                    newCell.style.padding = '5px';
                    newCell.style.border = '1px solid #000';
                    newCell.innerHTML = '&nbsp;';
                    newCell.setAttribute('contenteditable', 'true');
                    if (row.cells[cellIndex + 1]) {
                        row.insertBefore(newCell, row.cells[cellIndex + 1]);
                    } else {
                        row.appendChild(newCell);
                    }
                });
                saveState();
            });

            // Delete Row
            $('#btnDeleteRow').on('click', function(e) {
                e.preventDefault();
                const cell = getCurrentCell();
                if (!cell) {
                    Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Silakan klik di dalam cell tabel terlebih dahulu', timer: 2000, showConfirmButton: false });
                    return;
                }
                const row = cell.parentNode;
                if (row.parentNode.rows.length <= 1) {
                    Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Tidak dapat menghapus baris terakhir', timer: 2000, showConfirmButton: false });
                    return;
                }
                row.remove();
                saveState();
            });

            // Delete Column
            $('#btnDeleteCol').on('click', function(e) {
                e.preventDefault();
                const cell = getCurrentCell();
                if (!cell) {
                    Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Silakan klik di dalam cell tabel terlebih dahulu', timer: 2000, showConfirmButton: false });
                    return;
                }
                const table = getCurrentTable();
                const cellIndex = Array.from(cell.parentNode.cells).indexOf(cell);
                if (table.rows[0].cells.length <= 1) {
                    Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Tidak dapat menghapus kolom terakhir', timer: 2000, showConfirmButton: false });
                    return;
                }
                Array.from(table.rows).forEach(row => {
                    if (row.cells[cellIndex]) {
                        row.cells[cellIndex].remove();
                    }
                });
                saveState();
            });

            // Copy Table
            let copiedTable = null;
            $('#btnCopyTable').on('click', function(e) {
                e.preventDefault();
                const table = getCurrentTable();
                if (!table) {
                    Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Silakan klik di dalam tabel terlebih dahulu', timer: 2000, showConfirmButton: false });
                    return;
                }
                copiedTable = table.cloneNode(true);
                Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Tabel berhasil di-copy', timer: 1500, showConfirmButton: false });
            });

            // Paste Table
            $('#btnPasteTable').on('click', function(e) {
                e.preventDefault();
                if (!copiedTable) {
                    Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Tidak ada tabel yang di-copy', timer: 2000, showConfirmButton: false });
                    return;
                }
                const selection = window.getSelection();
                if (selection.rangeCount === 0) return;
                const range = selection.getRangeAt(0);
                const pastedTable = copiedTable.cloneNode(true);
                range.deleteContents();
                range.insertNode(pastedTable);
                saveState();
            });

            // Merge Cell
            $('#btnMergeCell').on('click', function() {
                const selection = window.getSelection();
                if (selection.rangeCount === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Silakan pilih beberapa cell yang ingin di-merge terlebih dahulu',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }

                const range = selection.getRangeAt(0);
                let startCell = range.startContainer;
                let endCell = range.endContainer;

                // Find the actual cell elements
                while (startCell && startCell.nodeName !== 'TD' && startCell.nodeName !== 'TH') {
                    startCell = startCell.parentNode;
                }
                while (endCell && endCell.nodeName !== 'TD' && endCell.nodeName !== 'TH') {
                    endCell = endCell.parentNode;
                }

                if (!startCell || !endCell) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Silakan pilih cell dalam tabel terlebih dahulu',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }

                if (startCell === endCell) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Pilih minimal 2 cell untuk di-merge',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }

                // Find table
                const table = startCell.closest('table');
                if (!table) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Silakan pilih cell dalam tabel terlebih dahulu',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }

                // Get all cells in selection range
                const allCells = Array.from(table.querySelectorAll('td, th'));
                const startIndex = allCells.indexOf(startCell);
                const endIndex = allCells.indexOf(endCell);
                const minIndex = Math.min(startIndex, endIndex);
                const maxIndex = Math.max(startIndex, endIndex);

                const selectedCells = allCells.slice(minIndex, maxIndex + 1);

                if (selectedCells.length < 2) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Pilih minimal 2 cell untuk di-merge',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }

                // Merge cells - combine content
                const firstCell = selectedCells[0];
                const lastCell = selectedCells[selectedCells.length - 1];

                // Save row references BEFORE removing cells
                const startRow = firstCell.parentNode;
                const endRow = lastCell.parentNode;
                const startRowIndex = Array.from(table.rows).indexOf(startRow);
                const endRowIndex = Array.from(table.rows).indexOf(endRow);

                // Save column references BEFORE removing cells
                const startCol = Array.from(startRow.cells).indexOf(firstCell);
                const endCol = Array.from(endRow.cells).indexOf(lastCell);

                // Combine content
                let mergedContent = firstCell.innerHTML.trim();
                for (let i = 1; i < selectedCells.length; i++) {
                    const cellContent = selectedCells[i].innerHTML.trim();
                    if (cellContent) {
                        mergedContent += (mergedContent ? ' ' : '') + cellContent;
                    }
                    selectedCells[i].remove();
                }

                // Calculate colspan and rowspan
                const rowspan = endRowIndex - startRowIndex + 1;
                const colspan = endCol - startCol + 1;

                firstCell.setAttribute('rowspan', rowspan);
                firstCell.setAttribute('colspan', colspan);
                firstCell.innerHTML = mergedContent || '&nbsp;';

                saveState();

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Cell berhasil di-merge',
                    timer: 1500,
                    showConfirmButton: false
                });
            });

            // Insert Line
            $('#btnInsertLine').on('click', function() {
                execCommand('insertHTML', '<hr style="border: 1px solid #000; margin: 10px 0;">');
            });

            // Insert Field
            $('.field-item').on('click', function() {
                const fieldName = $(this).data('field');
                const fieldLabel = $(this).data('label');
                const placeholder = '<span class="field-placeholder" contenteditable="false" data-field="' + fieldName + '">{{' + fieldName + '}}</span>';
                execCommand('insertHTML', placeholder);
            });

            // Update button states on selection change
            editor.addEventListener('selectionchange', function() {
                $('#btnBold').toggleClass('active', document.queryCommandState('bold'));
                $('#btnItalic').toggleClass('active', document.queryCommandState('italic'));
                $('#btnUnderline').toggleClass('active', document.queryCommandState('underline'));
                $('#btnStrikethrough').toggleClass('active', document.queryCommandState('strikeThrough'));
                updateAlignmentButtons();
            });

            // Page Orientation change handler
            $('#pageOrientation').on('change', function() {
                pageOrientation = $(this).val();
                updatePageOrientation();
            });

            // Save
            $('#btnSave').on('click', function() {
                const btn = $(this);
                const originalText = btn.html();
                btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...');

                const html = editor.innerHTML;
                const pageSize = pageOrientation === 'landscape' ? 'A4 landscape' : 'A4';
                const css = `@page {
    margin: 0;
    size: ${pageSize};
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
                        saveState(); // Update undo stack
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

            // Preview
            $('#btnPreview').on('click', function() {
                window.open('{{ route("label-management.template.preview", $template->id) }}', '_blank');
            });

            // Import Example
            $('#btnImportExample').on('click', function() {
                $('#modalImportExample').modal('show');
            });

            // Image file preview
            $('#imageFile').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (!file.type.match('image.*')) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Format tidak valid',
                            text: 'Hanya file gambar (PNG/JPG) yang diperbolehkan'
                        });
                        $(this).val('');
                        return;
                    }

                    if (file.size > 5 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File terlalu besar',
                            text: 'Ukuran file maksimal 5MB'
                        });
                        $(this).val('');
                        return;
                    }

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

                        if (response.success && response.html) {
                            editor.innerHTML = response.html;
                            saveState();

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'HTML berhasil dibuat dari gambar. Anda dapat mengedit sesuai kebutuhan.',
                                timer: 3000,
                                showConfirmButton: false
                            });

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

            // Field Mapping Management
            let fieldMappingIndex = {{ count($template->field_mapping ?? []) }};

            // Open manage fields modal
            $('#btnManageFields').on('click', function() {
                $('#modalManageFields').modal('show');
            });

            // Add new field
            $('#btnAddField').on('click', function() {
                const html = `
                    <div class="field-mapping-row mb-2 p-2 border rounded" data-index="${fieldMappingIndex}">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <label class="small">Cell Excel</label>
                                <input type="text" class="form-control form-control-sm field-excel-cell"
                                       placeholder="D5">
                            </div>
                            <div class="col-md-3">
                                <label class="small">Nama Field</label>
                                <input type="text" class="form-control form-control-sm field-name"
                                       placeholder="PC_NO">
                            </div>
                            <div class="col-md-4">
                                <label class="small">Label (Tampilan)</label>
                                <input type="text" class="form-control form-control-sm field-label"
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

            // Remove field
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

            // Save field mapping
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
                            location.reload(); // Reload untuk update sidebar
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

