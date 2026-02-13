@extends('main.layouts.main')
@section('title')
    Master Data Training
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .action-buttons .btn {
            margin: 0 2px;
            min-width: 32px;
            padding: 6px 10px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }
        .btn-add-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
        }
    </style>
@endsection
@section('page-title')
    Master Data Training
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Master Data Training</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Portal Training</a></li>
                <li class="breadcrumb-item active">Master Data Training</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Daftar Master Data Training</h4>
                        <button type="button" class="btn btn-add-custom" id="addTrainingBtn">
                            <i class="mdi mdi-plus mr-1"></i>
                            Tambah Training
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-trainings" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Training</th>
                                    <th>Nama Training</th>
                                    <th>Tipe</th>
                                    <th>Metode</th>
                                    <th>Jumlah Sesi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Training Modal -->
    <div class="modal fade" id="addTrainingModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Training Baru</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="addTrainingForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="training_name">Nama Training <span class="text-danger">*</span></label>
                                    <input type="text" name="training_name" id="training_name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="training_type">Tipe Training <span class="text-danger">*</span></label>
                                    <select name="training_type" id="training_type" class="form-control" required>
                                        <option value="">Pilih Tipe Training</option>
                                        <option value="mandatory">Mandatory</option>
                                        <option value="optional">Optional</option>
                                        <option value="certification">Certification</option>
                                        <option value="skill_development">Skill Development</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="training_method">Metode Training <span class="text-danger">*</span></label>
                                    <select name="training_method" id="training_method" class="form-control" required>
                                        <option value="">Pilih Metode Training</option>
                                        <option value="classroom">Kelas Tatap Muka</option>
                                        <option value="online">Online</option>
                                        <option value="hybrid">Hybrid</option>
                                        <option value="workshop">Workshop</option>
                                        <option value="seminar">Seminar</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="notes">Catatan Tambahan</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                        </div>

                        <!-- Pengaturan Sesi Training -->
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="mb-0"><strong>Pengaturan Sesi Training</strong></label>
                                <button type="button" class="btn btn-sm btn-primary" id="addSessionBtn">
                                    <i class="mdi mdi-plus mr-1"></i>
                                    Tambah Sesi
                                </button>
                            </div>
                            <div id="sessionsContainer">
                                <!-- Sessions will be added here dynamically -->
                            </div>
                            <small class="text-muted">Atur urutan sesi training. Setiap sesi bisa memiliki video atau tidak.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Training</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Training Modal -->
    <div class="modal fade" id="editTrainingModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Training</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="editTrainingContent">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="submitEditForm">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Training Modal -->
    <div class="modal fade" id="viewTrainingModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Training</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="viewTrainingContent">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#datatable-trainings').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('hr.portal-training.master.training-masters.getData') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'training_code', name: 'training_code' },
                    { data: 'training_name', name: 'training_name' },
                    { data: 'training_type', name: 'training_type' },
                    { data: 'training_method', name: 'training_method' },
                    { data: 'sessions_count', name: 'sessions_count' },
                    { data: 'status_badge', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']]
            });

            let sessionCounter = 0;

            // Add new session
            $('#addSessionBtn').on('click', function() {
                sessionCounter++;
                const sessionHtml = `
                    <div class="card mb-3 session-item" data-session-index="${sessionCounter}">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Sesi ${sessionCounter}</h5>
                                <button type="button" class="btn btn-sm btn-danger remove-session-btn">
                                    <i class="mdi mdi-delete"></i> Hapus
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Judul Sesi <span class="text-danger">*</span></label>
                                        <input type="text" name="sessions[${sessionCounter}][session_title]"
                                            class="form-control" required placeholder="Contoh: Pengenalan K3">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Deskripsi</label>
                                        <textarea name="sessions[${sessionCounter}][description]"
                                            class="form-control" rows="2"
                                            placeholder="Deskripsi singkat tentang sesi ini"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tingkat Kesulitan <span class="text-danger">*</span></label>
                                        <select name="sessions[${sessionCounter}][difficulty_level_id]" class="form-control" required>
                                            <option value="">Pilih Tingkat Kesulitan</option>
                                            <option value="1">Mudah</option>
                                            <option value="2">Sedang</option>
                                            <option value="3">Sulit</option>
                                            <option value="4">Sangat Sulit</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tema Pertanyaan</label>
                                        <input type="text" name="sessions[${sessionCounter}][theme]"
                                            class="form-control"
                                            placeholder="Contoh: K3, ISO, dll (opsional)">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Jumlah Soal <span class="text-danger">*</span></label>
                                        <input type="number" name="sessions[${sessionCounter}][question_count]"
                                            class="form-control" min="1" value="5" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Nilai Min. Lulus <span class="text-danger">*</span></label>
                                        <input type="number" name="sessions[${sessionCounter}][passing_score]"
                                            class="form-control" min="0" max="100" step="0.01" value="70.00" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="sessions[${sessionCounter}][has_video]"
                                            class="form-check-input has-video-checkbox"
                                            id="has_video_${sessionCounter}" value="1">
                                        <label class="form-check-label" for="has_video_${sessionCounter}">
                                            <strong>Sesi ini memiliki video</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row video-fields" style="display: none;">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>URL Video atau Link Google Drive <span class="text-danger">*</span></label>
                                        <input type="text" name="sessions[${sessionCounter}][video_input]"
                                            class="form-control video-input-field"
                                            id="video_input_${sessionCounter}"
                                            placeholder="https://drive.google.com/file/d/... atau path/to/video.mp4">
                                        <small class="text-muted">
                                            <strong>Opsi 1 (Google Drive):</strong> Paste link Google Drive (contoh: https://drive.google.com/file/d/1a2b3c4d5e6f7g8h9i0j/view)<br>
                                            <strong>Opsi 2 (Local):</strong> Masukkan path video (contoh: storage/training/video.mp4 atau https://example.com/video.mp4)
                                        </small>
                                        <input type="hidden" name="sessions[${sessionCounter}][video_url]" class="video-url-hidden" value="">
                                        <input type="hidden" name="sessions[${sessionCounter}][google_drive_file_id]" class="google-drive-hidden" value="">
                                        <input type="hidden" name="sessions[${sessionCounter}][video_source]" class="video-source-hidden" value="local">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Durasi Video (detik)</label>
                                        <input type="number" name="sessions[${sessionCounter}][video_duration_seconds]"
                                            class="form-control" min="1"
                                            placeholder="3600">
                                        <small class="text-muted">Durasi dalam detik (contoh: 3600 = 1 jam)</small>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="sessions[${sessionCounter}][session_order]" value="${sessionCounter}">
                        </div>
                    </div>
                `;
                $('#sessionsContainer').append(sessionHtml);
                updateSessionNumbers();
            });

            // Remove session
            $(document).on('click', '.remove-session-btn', function() {
                $(this).closest('.session-item').remove();
                updateSessionNumbers();
            });

            // Function to detect if input is Google Drive link
            function isGoogleDriveLink(input) {
                if (!input || input.trim() === '') return false;
                const value = input.trim();
                return value.includes('drive.google.com') ||
                       value.includes('docs.google.com') ||
                       /^[a-zA-Z0-9_-]{20,}$/.test(value);
            }

            // Function to extract Google Drive file ID
            function extractGoogleDriveFileId(input) {
                if (!input) return null;
                const value = input.trim();
                if (/^[a-zA-Z0-9_-]{20,}$/.test(value)) return value;
                const match = value.match(/\/d\/([a-zA-Z0-9_-]+)/);
                if (match) return match[1];
                const match2 = value.match(/[?&]id=([a-zA-Z0-9_-]+)/);
                if (match2) return match2[1];
                return null;
            }

            // Auto-detect video source and update hidden fields
            $(document).on('input blur', '.video-input-field', function() {
                const input = $(this).val();
                const sessionItem = $(this).closest('.session-item, .card-body');
                const videoUrlHidden = sessionItem.find('.video-url-hidden');
                const googleDriveHidden = sessionItem.find('.google-drive-hidden');
                const videoSourceHidden = sessionItem.find('.video-source-hidden');

                if (isGoogleDriveLink(input)) {
                    const fileId = extractGoogleDriveFileId(input);
                    if (fileId) {
                        googleDriveHidden.val(fileId);
                        videoUrlHidden.val('');
                        videoSourceHidden.val('google_drive');
                    }
                } else if (input.trim() !== '') {
                    videoUrlHidden.val(input);
                    googleDriveHidden.val('');
                    videoSourceHidden.val('local');
                } else {
                    videoUrlHidden.val('');
                    googleDriveHidden.val('');
                    videoSourceHidden.val('local');
                }
            });

            // Toggle video fields
            $(document).on('change', '.has-video-checkbox', function() {
                const videoFields = $(this).closest('.card-body').find('.video-fields');
                const videoInput = videoFields.find('.video-input-field');

                if ($(this).is(':checked')) {
                    videoFields.slideDown();
                    videoInput.prop('required', true);
                } else {
                    videoFields.slideUp();
                    videoInput.prop('required', false);
                    videoInput.val('');
                    $(this).closest('.card-body').find('.video-url-hidden').val('');
                    $(this).closest('.card-body').find('.google-drive-hidden').val('');
                    $(this).closest('.card-body').find('.video-source-hidden').val('local');
                }
            });

            // Update session numbers
            function updateSessionNumbers() {
                $('.session-item').each(function(index) {
                    const newNumber = index + 1;
                    $(this).find('.card-header h5').text('Sesi ' + newNumber);
                    $(this).find('input[name*="[session_order]"]').val(newNumber);
                    $(this).attr('data-session-index', newNumber);

                    // Update all input names
                    $(this).find('input, textarea, select').each(function() {
                        const name = $(this).attr('name');
                        if (name && name.includes('sessions[')) {
                            const newName = name.replace(/sessions\[\d+\]/, `sessions[${newNumber}]`);
                            $(this).attr('name', newName);
                        }
                    });
                });
            }

            // Add Training Button
            $('#addTrainingBtn').on('click', function() {
                sessionCounter = 0;
                $('#sessionsContainer').empty();
                $('#addTrainingForm')[0].reset();
                $('#addTrainingModal').modal('show');
            });

            // Submit Add Form
            $('#addTrainingForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('hr.portal-training.master.training-masters.store') }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        $('#addTrainingModal').modal('hide');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorMsg = 'Validasi gagal:\n';
                            $.each(errors, function(key, value) {
                                errorMsg += value[0] + '\n';
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: errorMsg
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON.message || 'Terjadi kesalahan saat menyimpan data.'
                            });
                        }
                    }
                });
            });

            // View Training
            $(document).on('click', '.btn-view', function() {
                const id = $(this).data('id');
                $.get("{{ route('hr.portal-training.master.training-masters.index') }}/" + id, function(data) {
                    let content = `
                        <h5>${data.training_name}</h5>
                        <p><strong>Kode:</strong> ${data.training_code}</p>
                        <p><strong>Tipe:</strong> ${data.training_type}</p>
                        <p><strong>Metode:</strong> ${data.training_method}</p>
                        <p><strong>Status:</strong> ${data.status}</p>
                        <hr>
                        <h6>Sesi Training (${data.sessions.length})</h6>
                        <ul>
                    `;
                    data.sessions.forEach(function(session) {
                        content += `<li><strong>Sesi ${session.session_order}:</strong> ${session.session_title}`;
                        if (session.has_video) {
                            content += ` <span class="badge badge-info">Ada Video</span>`;
                        }
                        content += `</li>`;
                    });
                    content += `</ul>`;
                    $('#viewTrainingContent').html(content);
                    $('#viewTrainingModal').modal('show');
                });
            });

            // Edit Training
            $(document).on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                const editUrl = "{{ route('hr.portal-training.master.training-masters.index') }}/" + id + "/edit";
                const updateUrl = "{{ route('hr.portal-training.master.training-masters.index') }}/" + id;

                $.ajax({
                    url: editUrl,
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(data) {
                        $('#editTrainingContent').html(data);
                        // Update form action setelah form di-load
                        setTimeout(function() {
                            const form = $('#editTrainingForm');
                            if (form.length > 0) {
                                form.attr('action', updateUrl);
                            }
                        }, 100);
                        $('#editTrainingModal').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat data training.'
                        });
                    }
                });
            });

            // Submit Edit Form
            $(document).on('click', '#submitEditForm', function() {
                const form = $('#editTrainingForm');
                if (form.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Form tidak ditemukan.'
                    });
                    return;
                }

                // Validasi client-side
                let isValid = true;
                let errorFields = [];

                // Cek field required
                form.find('[required]').each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                        const fieldName = $(this).closest('.form-group').find('label').text().trim();
                        errorFields.push(fieldName || $(this).attr('name'));
                    }
                });

                // Cek select yang required
                form.find('select[required]').each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                        const fieldName = $(this).closest('.form-group').find('label').text().trim();
                        errorFields.push(fieldName || $(this).attr('name'));
                    }
                });

                if (!isValid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Mohon lengkapi semua field yang wajib diisi:\n' + errorFields.join('\n')
                    });
                    return;
                }

                const formData = form.serialize();
                const url = form.attr('action');
                const submitBtn = $(this);
                submitBtn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin mr-1"></i> Menyimpan...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                        $('#editTrainingModal').modal('hide');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).html('Simpan Perubahan');
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorMsg = 'Validasi gagal:\n';
                            $.each(errors, function(key, value) {
                                if (Array.isArray(value)) {
                                    errorMsg += key + ': ' + value[0] + '\n';
                                } else {
                                    errorMsg += key + ': ' + value + '\n';
                                }
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Error Validasi',
                                text: errorMsg
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data.'
                            });
                        }
                    }
                });
            });

            // Delete Training
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data training akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('hr.portal-training.master.training-masters.index') }}/" + id,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON.message || 'Terjadi kesalahan saat menghapus data.'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection

