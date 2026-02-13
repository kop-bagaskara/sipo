@extends('main.layouts.main')
@section('title')
    Master Bank Soal
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .action-buttons .btn {
            margin: 0 2px;
            min-width: 32px;
            padding: 6px 10px;
            border-radius: 5px;
            transition: all 0.2s;
        }

        .action-buttons .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .action-buttons .btn-primary:hover {
            background-color: #0069d9;
            transform: scale(1.1);
        }

        .action-buttons .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }

        .action-buttons .btn-info:hover {
            background-color: #138496;
            transform: scale(1.1);
        }

        .action-buttons .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .action-buttons .btn-danger:hover {
            background-color: #c82333;
            transform: scale(1.1);
        }

        #datatable-questions_wrapper .dataTables_filter input {
            border-radius: 20px;
            padding: 8px 15px;
            border: 1px solid #ddd;
        }

        #datatable-questions_wrapper .dataTables_length select {
            border-radius: 5px;
            padding: 5px 10px;
            border: 1px solid #ddd;
        }

        #datatable-questions th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .page-item.active .page-link {
            background-color: #667eea;
            border-color: #667eea;
        }

        .page-link {
            color: #667eea;
        }

        .page-link:hover {
            color: #764ba2;
        }

        #datatable-questions_wrapper .dataTables_info {
            padding-top: 15px;
            color: #666;
        }

        #datatable-questions_wrapper .dataTables_paginate {
            padding-top: 15px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border: none;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 2px solid #667eea;
            padding: 20px 25px;
            border-radius: 10px 10px 0 0 !important;
        }

        .btn-add-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s;
        }

        .btn-add-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .modal-header {
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .modal-header h5 {
            color: white;
        }

        .modal-header .close {
            color: white;
            opacity: 0.8;
        }

        .modal-header .close:hover {
            opacity: 1;
        }

        .modal-content {
            border-radius: 10px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .modal-body .form-control {
            border-radius: 5px;
            border: 1px solid #e0e0e0;
            padding: 10px 15px;
            transition: all 0.3s;
        }

        .modal-body .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .modal-body label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .font-weight-600 {
            font-weight: 600;
        }

        .input-group-text {
            background-color: #667eea;
            color: white;
            border: 1px solid #667eea;
            font-weight: 600;
        }

        .question-preview {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .badge-type {
            background-color: #6f42c1;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-difficulty {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-difficulty-paling {
            background-color: #28a745;
            color: white;
        }

        .badge-difficulty-mudah {
            background-color: #82c91e;
            color: white;
        }

        .badge-difficulty-cukup {
            background-color: #ffc107;
            color: #333;
        }

        .badge-difficulty-menengah {
            background-color: #fd7e14;
            color: white;
        }

        .badge-difficulty-sulit {
            background-color: #dc3545;
            color: white;
        }
    </style>
@endsection
@section('page-title')
    Master Bank Soal
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <!-- Page Header -->
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="mt-2"><i class="mdi mdi-help-circle"></i> Master Bank Soal</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Portal Training</a></li>
                <li class="breadcrumb-item active">Master Bank Soal</li>
            </ol>
        </div>
    </div>

    <!-- Main Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0" style="font-weight: 600;">
                            <i class="mdi mdi-format-list-bulleted"></i> Daftar Bank Soal
                        </h4>
                    </div>
                    <div>
                        <a href="{{ route('hr.portal-training.master.question-banks.download-template') }}" class="btn btn-success" style="margin-right: 10px;">
                            <i class="mdi mdi-download"></i> Download Template Excel
                        </a>
                        <button type="button" class="btn btn-info" onclick="importQuestions()" style="margin-right: 10px;">
                            <i class="mdi mdi-upload"></i> Import
                        </button>
                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addModal">
                            <i class="mdi mdi-plus-circle"></i> Tambah Soal
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-questions" class="table table-hover" style="width: 100%;">
                            <thead class="thead-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th width="250">Soal</th>
                                    <th width="120">Kategori</th>
                                    <th width="150">Tema</th>
                                    <th width="100">Tipe Soal</th>
                                    <th width="100">Tipe</th>
                                    <th width="100">Kesulitan</th>
                                    <th width="80">Poin</th>
                                    <th width="100">Status</th>
                                    <th width="150" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title"><i class="mdi mdi-plus-circle"></i> Tambah Soal Baru</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="questionCategory" class="font-weight-600">Kategori Materi <span class="text-danger">*</span></label>
                                    <select name="category_id" id="questionCategory" class="form-control" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="questionDifficulty" class="font-weight-600">Tingkat Kesulitan <span class="text-danger">*</span></label>
                                    <select name="difficulty_level" id="questionDifficulty" class="form-control" required>
                                        <option value="">Pilih Kesulitan</option>
                                        <option value="paling mudah">Paling Mudah</option>
                                        <option value="mudah">Mudah</option>
                                        <option value="cukup">Cukup</option>
                                        <option value="menengah ke atas">Menengah ke Atas</option>
                                        <option value="sulit">Sulit</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="questionTheme" class="font-weight-600">Tema Soal <span class="text-muted">(Opsional)</span></label>
                                    <input type="text" name="theme" id="questionTheme" class="form-control" placeholder="Contoh: Pembeda Iso 9001:2015, Kepanjangan ISO, dll">
                                    <small class="text-muted">Tema untuk mengelompokkan soal dalam kategori yang sama</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="questionTypeNumber" class="font-weight-600">Tipe Soal <span class="text-muted">(Opsional)</span></label>
                                    <select name="type_number" id="questionTypeNumber" class="form-control">
                                        <option value="">Pilih Tipe</option>
                                        <option value="1">TIPE 1</option>
                                        <option value="2">TIPE 2</option>
                                        <option value="3">TIPE 3</option>
                                        <option value="4">TIPE 4</option>
                                        <option value="5">TIPE 5</option>
                                    </select>
                                    <small class="text-muted">Nomor tipe soal untuk variasi dalam tema yang sama</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="questionType" class="font-weight-600">Tipe Soal <span class="text-danger">*</span></label>
                                    <select name="type" id="questionType" class="form-control" required>
                                        <option value="">Pilih Tipe</option>
                                        <option value="pilihan ganda">Pilihan Ganda</option>
                                        <option value="essay">Essay</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="questionPoints" class="font-weight-600">Poin <span class="text-danger">*</span></label>
                                    <input type="number" name="points" id="questionPoints" class="form-control" value="10" min="1" max="100" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="questionText" class="font-weight-600">Soal <span class="text-danger">*</span></label>
                            <textarea name="question" id="questionText" class="form-control" rows="3" required placeholder="Tulis soal di sini..."></textarea>
                        </div>
                        <div id="optionsContainer">
                            <div class="form-group">
                                <label class="font-weight-600">Opsi Jawaban (untuk Pilihan Ganda)</label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">A</span>
                                    </div>
                                    <input type="text" name="options[0]" class="form-control" placeholder="Opsi A">
                                </div>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">B</span>
                                    </div>
                                    <input type="text" name="options[1]" class="form-control" placeholder="Opsi B">
                                </div>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">C</span>
                                    </div>
                                    <input type="text" name="options[2]" class="form-control" placeholder="Opsi C">
                                </div>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">D</span>
                                    </div>
                                    <input type="text" name="options[3]" class="form-control" placeholder="Opsi D">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="correctAnswer" class="font-weight-600">Jawaban Benar <span class="text-danger">*</span></label>
                            <input type="text" name="correct_answer" id="correctAnswer" class="form-control" required placeholder="Untuk pilihan ganda: A, B, C, atau D. Untuk essay: jawaban lengkap.">
                            <small class="text-muted">Untuk pilihan ganda, masukkan huruf opsi (A/B/C/D). Untuk essay, masukkan jawaban lengkap.</small>
                        </div>
                        <div class="form-group">
                            <label for="questionExplanation" class="font-weight-600">Penjelasan Jawaban (Opsional)</label>
                            <textarea name="explanation" id="questionExplanation" class="form-control" rows="2" placeholder="Penjelasan mengapa jawaban tersebut benar..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="mdi mdi-close"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-info">
                            <i class="mdi mdi-content-save"></i> Simpan Soal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title"><i class="mdi mdi-pencil"></i> Edit Soal</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editForm">
                    <input type="hidden" name="id" id="editId">
                    <div class="modal-body" id="editModalBody">
                        <!-- Will be populated dynamically -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="mdi mdi-close"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-info">
                            <i class="mdi mdi-content-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title"><i class="mdi mdi-upload"></i> Import Soal dari Excel/CSV</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="importForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="mdi mdi-information"></i>
                            <strong>Format Excel/CSV yang didukung:</strong><br>
                            <small>
                                <strong>Format Baru (Direkomendasikan):</strong> NO | TEMA | TIPE | PERTANYAAN | PILIHAN_A | PILIHAN_B | PILIHAN_C | PILIHAN_D | JAWABAN_BENAR | MATERIAL_ID | DIFFICULTY_LEVEL | POINTS<br>
                                <strong>Format Lama (Multiple TIPE per baris):</strong> NO | TEMA | TIPE 1 | TIPE 2 | TIPE 3 | TIPE ... (setiap kolom TIPE berisi soal lengkap dengan pertanyaan dan pilihan jawaban)<br>
                                <strong>Format CSV:</strong> question,type,category_id,difficulty_level,correct_answer,points,explanation,theme,type_number,option_a,option_b,option_c,option_d
                            </small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="importMaterialId" class="font-weight-600">Kategori Materi (Opsional)</label>
                                    <select name="material_id" id="importMaterialId" class="form-control">
                                        <option value="">Pilih Materi (Opsional)</option>
                                        @foreach($materials as $material)
                                            <option value="{{ $material->id }}">{{ $material->material_title }} (ID: {{ $material->id }})</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Jika dikosongkan, harus diisi di file Excel. Data yang tersedia: {{ count($materials) }} materi</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="importDifficultyLevelId" class="font-weight-600">Tingkat Kesulitan (Opsional)</label>
                                    <select name="difficulty_level_id" id="importDifficultyLevelId" class="form-control">
                                        <option value="">Pilih Tingkat Kesulitan (Opsional)</option>
                                        @foreach($difficultyLevels as $level)
                                            <option value="{{ $level->id }}">{{ $level->level_name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Jika dikosongkan, harus diisi di file Excel</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="importFile" class="font-weight-600">Pilih File Excel/CSV <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" name="file" id="importFile" class="custom-file-input" accept=".xlsx,.xls,.csv" required>
                                <label class="custom-file-label" for="importFile" id="importFileName">Pilih file Excel atau CSV...</label>
                            </div>
                            <small class="text-muted">Maksimal ukuran file: 10MB. Format: .xlsx, .xls, atau .csv</small>
                        </div>

                        <div class="form-group mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="font-weight-600 mb-0">Download Template Excel:</label>
                                <a href="{{ route('hr.portal-training.master.question-banks.download-template') }}" class="btn btn-sm btn-success">
                                    <i class="mdi mdi-download"></i> Download Template Excel
                                </a>
                            </div>
                            <div class="alert alert-warning">
                                <i class="mdi mdi-alert"></i>
                                <strong>Catatan Format Excel:</strong>
                                <ul class="mb-0 small">
                                    <li><strong>Format Baru (Direkomendasikan):</strong>
                                        <ul>
                                            <li>Kolom <strong>NO</strong>: Nomor urut (opsional)</li>
                                            <li>Kolom <strong>TEMA</strong>: Tema/kategori soal (opsional)</li>
                                            <li>Kolom <strong>TIPE</strong>: Tipe soal seperti "TIPE 1", "TIPE 2", dll (opsional)</li>
                                            <li>Kolom <strong>PERTANYAAN</strong>: Pertanyaan soal (wajib)</li>
                                            <li>Kolom <strong>PILIHAN_A, PILIHAN_B, PILIHAN_C, PILIHAN_D</strong>: Pilihan jawaban (minimal A dan B wajib)</li>
                                            <li>Kolom <strong>JAWABAN_BENAR</strong>: Jawaban benar (A, B, C, atau D) (wajib)</li>
                                            <li>Kolom <strong>MATERIAL_ID</strong>: ID Kategori Materi (wajib jika tidak diisi di form)</li>
                                            <li>Kolom <strong>DIFFICULTY_LEVEL</strong>: Nama tingkat kesulitan (wajib jika tidak diisi di form)</li>
                                            <li>Kolom <strong>POINTS</strong>: Poin soal (default: 10)</li>
                                        </ul>
                                    </li>
                                    <li><strong>Format Lama (Multiple TIPE per baris):</strong>
                                        <ul>
                                            <li>Kolom <strong>NO</strong>: Nomor urut (opsional)</li>
                                            <li>Kolom <strong>TEMA</strong>: Tema/kategori soal (akan digunakan untuk semua TIPE dalam baris yang sama)</li>
                                            <li>Kolom <strong>TIPE 1, TIPE 2, TIPE 3, TIPE</strong>: Setiap kolom berisi soal lengkap dengan format:<br>
                                                <code>Pertanyaan?<br>A. Pilihan A<br>B. Pilihan B<br>C. Pilihan C<br>D. Pilihan D</code>
                                            </li>
                                            <li>Kolom <strong>JAWABAN_TIPE1, JAWABAN_TIPE2, JAWABAN_TIPE3, JAWABAN_TIPE</strong>: Jawaban benar per TIPE (A, B, C, atau D) - <strong>OPSIONAL</strong></li>
                                            <li><strong>Cara menentukan jawaban benar (pilih salah satu):</strong>
                                                <ol>
                                                    <li><strong>Kolom terpisah:</strong> Isi kolom JAWABAN_TIPE1, JAWABAN_TIPE2, dst dengan A, B, C, atau D</li>
                                                    <li><strong>Tanda dalam cell:</strong> Tambahkan tanda <code>*</code>, <code>[CORRECT]</code>, <code>[BENAR]</code>, atau <code>[TRUE]</code> di akhir pilihan jawaban yang benar<br>
                                                        Contoh: <code>A. Pilihan A *</code> atau <code>B. Pilihan B [CORRECT]</code>
                                                    </li>
                                                </ol>
                                            </li>
                                            <li><strong>Catatan:</strong> Setiap TIPE akan di-create sebagai soal terpisah dengan TEMA yang sama. Jika tidak ada tanda atau kolom jawaban, default ke A.</li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-600">Keterangan Tingkat Kesulitan:</label>
                            <ul class="small text-muted mb-0">
                                @foreach($difficultyLevels as $level)
                                    <li>{{ $level->level_name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="mdi mdi-close"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-info">
                            <i class="mdi mdi-upload"></i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            var table = $('#datatable-questions').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("hr.portal-training.master.question-banks.getData") }}',
                    type: 'GET'
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'question_preview', name: 'question_preview' },
                    { data: 'category_name', name: 'category_name' },
                    { data: 'theme', name: 'theme' },
                    { data: 'type_number', name: 'type_number' },
                    { data: 'type_badge', name: 'type_badge' },
                    { data: 'difficulty_badge', name: 'difficulty_badge' },
                    { data: 'points', name: 'points' },
                    { data: 'status_badge', name: 'status_badge' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']]
            });

            // Toggle options based on question type
            $('#questionType').on('change', function() {
                if ($(this).val() === 'essay') {
                    $('#optionsContainer').hide();
                    $('#correctAnswer').attr('placeholder', 'Masukkan jawaban lengkap untuk essay...');
                } else {
                    $('#optionsContainer').show();
                    $('#correctAnswer').attr('placeholder', 'Masukkan huruf opsi (A/B/C/D)...');
                }
            });

            // Add Form
            $('#addForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route("hr.portal-training.master.question-banks.store") }}',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        $('#addModal').modal('hide');
                        table.ajax.reload();
                        $('#addForm')[0].reset();
                        $('#optionsContainer').show();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan.'
                        });
                    }
                });
            });

            // View button
            $(document).on('click', '.btn-view', function() {
                var id = $(this).data('id');
                $.get(`{{ route('hr.portal-training.master.question-banks.show', ':id') }}`.replace(':id', id), function(data) {
                    var optionsHtml = '';
                    if (data.options) {
                        var options = JSON.parse(data.options);
                        var labels = ['A', 'B', 'C', 'D'];
                        options.forEach(function(opt, i) {
                            optionsHtml += `<div><strong>${labels[i]}.</strong> ${opt}</div>`;
                        });
                    }

                    Swal.fire({
                        title: 'Detail Soal',
                        html: `
                            <div style="text-align: left;">
                                <p><strong>Soal:</strong><br>${data.question}</p>
                                <p><strong>Kategori:</strong> ${data.category_name || '-'}</p>
                                <p><strong>Tema:</strong> ${data.theme || '-'}</p>
                                <p><strong>Tipe Soal:</strong> ${data.type_number ? 'TIPE ' + data.type_number : '-'}</p>
                                <p><strong>Tingkat Kesulitan:</strong> ${data.difficulty_level || '-'}</p>
                                <p><strong>Tipe:</strong> ${data.type || '-'}</p>
                                ${optionsHtml ? '<p><strong>Opsi:</strong><br>' + optionsHtml + '</p>' : ''}
                                <p><strong>Jawaban Benar:</strong> ${data.correct_answer}</p>
                                ${data.explanation ? '<p><strong>Penjelasan:</strong><br>' + data.explanation + '</p>' : ''}
                                <p><strong>Poin:</strong> ${data.points}</p>
                            </div>
                        `,
                        width: '600px'
                    });
                });
            });

            // Edit button
            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $('#editId').val(id);
                $.get(`{{ route('hr.portal-training.master.question-banks.edit', ':id') }}`.replace(':id', id), function(data) {
                    // Clone the add form structure
                    var html = $('#addForm .modal-body').html();
                    $('#editModalBody').html(html);
                    
                    // Add hidden id field
                    $('#editModalBody').prepend('<input type="hidden" name="id" value="' + id + '">');

                    // Populate values
                    $('#editModalBody [name="category_id"]').val(data.category_id);
                    $('#editModalBody [name="difficulty_level"]').val(data.difficulty_level);
                    $('#editModalBody [name="theme"]').val(data.theme || '');
                    $('#editModalBody [name="type_number"]').val(data.type_number || '');
                    $('#editModalBody [name="type"]').val(data.type);
                    $('#editModalBody [name="points"]').val(data.points);
                    $('#editModalBody [name="question"]').val(data.question);
                    $('#editModalBody [name="correct_answer"]').val(data.correct_answer);
                    $('#editModalBody [name="explanation"]').val(data.explanation || '');

                    // Populate options if exists
                    if (data.options) {
                        var options = JSON.parse(data.options);
                        options.forEach((opt, i) => {
                            $('#editModalBody [name="options['+i+']"]').val(opt);
                        });
                    }

                    $('#editModal').modal('show');
                });
            });

            // Edit Form
            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                var id = $('#editId').val();
                $.ajax({
                    url: `{{ route('hr.portal-training.master.question-banks.update', ':id') }}`.replace(':id', id),
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        $('#editModal').modal('hide');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan.'
                        });
                    }
                });
            });

            // Delete button
            $(document).on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus Soal?',
                    text: 'Data yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('hr.portal-training.master.question-banks.destroy', ':id') }}`.replace(':id', id),
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: response.message
                                });
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: xhr.responseJSON?.message || 'Terjadi kesalahan.'
                                });
                            }
                        });
                    }
                });
            });
        });

        function importQuestions() {
            $('#importModal').modal('show');
        }

        // Handle file import
        $('#importFile').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $('#importFileName').text(fileName ? fileName : 'Pilih file Excel atau CSV...');
        });

        // Import form submit
        $('#importForm').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var file = $('#importFile')[0].files[0];

            if (!file) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Silakan pilih file terlebih dahulu.'
                });
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Memproses',
                text: 'Mohon tunggu, sedang mengimport soal...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route("hr.portal-training.master.question-banks.import") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Close loading alert first
                    Swal.close();

                    // Show success message with error details if any
                    setTimeout(function() {
                        var message = response.message || 'Soal berhasil diimport.';
                        var hasErrors = response.error_count > 0 && response.errors && response.errors.length > 0;
                        
                        if (hasErrors) {
                            // Build error details HTML
                            var errorDetails = '<div style="text-align: left; max-height: 300px; overflow-y: auto; margin-top: 15px;">';
                            errorDetails += '<strong>Detail Error (' + response.error_count + ' error):</strong><br><br>';
                            errorDetails += '<ul style="margin: 0; padding-left: 20px;">';
                            response.errors.forEach(function(error) {
                                errorDetails += '<li style="margin-bottom: 5px; font-size: 12px;">' + error + '</li>';
                            });
                            errorDetails += '</ul>';
                            errorDetails += '</div>';
                            
                            Swal.fire({
                                icon: response.success_count > 0 ? 'warning' : 'error',
                                title: response.success_count > 0 ? 'Berhasil dengan Error' : 'Gagal',
                                html: '<div style="text-align: center;">' + message + '</div>' + errorDetails,
                                width: '600px',
                                confirmButtonText: 'OK'
                            }).then(function() {
                                if (response.success_count > 0) {
                                    $('#importModal').modal('hide');
                                    $('#importForm')[0].reset();
                                    $('#importFileName').text('Pilih file Excel atau CSV...');
                                    table.ajax.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: message,
                                timer: 3000,
                                showConfirmButton: false
                            }).then(function() {
                                $('#importModal').modal('hide');
                                $('#importForm')[0].reset();
                                $('#importFileName').text('Pilih file Excel atau CSV...');
                                table.ajax.reload();
                            });
                        }
                    }, 300);
                },
                error: function(xhr) {
                    // Close loading alert first
                    Swal.close();

                    console.error('Import error:', xhr);
                    console.error('Response:', xhr.responseJSON);
                    console.error('Status:', xhr.status);
                    console.error('Text:', xhr.responseText);

                    var errorMessage = 'Terjadi kesalahan saat import.';
                    var errorDetails = [];

                    if (xhr.responseJSON) {
                        errorMessage = xhr.responseJSON.message || errorMessage;
                        if (xhr.responseJSON.errors && xhr.responseJSON.errors.length > 0) {
                            errorDetails = xhr.responseJSON.errors;
                        }
                    } else if (xhr.responseText) {
                        try {
                            var errorData = JSON.parse(xhr.responseText);
                            errorMessage = errorData.message || errorMessage;
                        } catch (e) {
                            errorMessage = 'Terjadi kesalahan server. Status: ' + xhr.status;
                        }
                    }

                    setTimeout(function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: errorMessage,
                            footer: errorDetails.length > 0 ? errorDetails.join('<br>') : '',
                            width: errorDetails.length > 0 ? '700px' : '400px'
                        });
                    }, 300);
                }
            });
        });

        // Download CSV Template
        function downloadTemplate() {
            var csvContent = [
                'question,type,category_id,difficulty_level,correct_answer,points,explanation,theme,type_number,option_a,option_b,option_c,option_d',
                '"Apa ibukota Indonesia?",pilihan ganda,1,mudah,A,10,"Jakarta adalah ibukota Indonesia",,,"Jakarta","Bandung","Surabaya","Medan"',
                '"Sebutkan 3 provinsi di Indonesia!",essay,1,mudah,"Jawa Barat, Jawa Tengah, Jawa Timur",10,"Salah satu jawaban yang benar",,,',
                '"Apa fungsi vitamin C?",pilihan ganda,2,cukup,C,15,"Vitamin C berfungsi sebagai antioksidan",,,"Membantu pencernaan","Menguatkan tulang","Antioksidan","Meningkatkan nafsu makan"',
                '"Jelaskan pengertian demokrasi!",essay,2,cukup,"Demokrasi adalah sistem pemerintahan dari rakyat, oleh rakyat, untuk rakyat",15,"Jawaban harus mencakup unsur rakyat",,,',
                '"What is one of the differences between ISO 9001 version 2015?",pilihan ganda,1,mudah,C,10,"Risk-based thinking","Pembeda Iso 9001:2015",1,"Option A","Option B","Risk-based thinking has been formed.","Option D"'
            ];

            var csvString = csvContent.join('\n');
            var blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
            var link = document.createElement('a');
            var url = URL.createObjectURL(blob);

            link.setAttribute('href', url);
            link.setAttribute('download', 'template_import_soal.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
@endsection
