@extends('main.layouts.main')
@section('title')
    Master Training
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
    Master Training
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Master Training</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">Master Training</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="mdi mdi-book-open-page-variant mr-2"></i>
                            Manajemen Training
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('hr.training.create') }}" class="btn btn-primary btn-sm">
                                <i class="mdi mdi-plus mr-1"></i>
                                Tambah Training
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Filter Form -->
                        <form method="GET" action="{{ route('hr.training.index') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="">Semua Status</option>
                                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>
                                                Draft</option>
                                            <option value="published"
                                                {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                                            <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>
                                                Ongoing</option>
                                            <option value="completed"
                                                {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="cancelled"
                                                {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="training_type">Tipe Training</label>
                                        <select name="training_type" id="training_type" class="form-control">
                                            <option value="">Semua Tipe</option>
                                            <option value="mandatory"
                                                {{ request('training_type') == 'mandatory' ? 'selected' : '' }}>Mandatory
                                            </option>
                                            <option value="optional"
                                                {{ request('training_type') == 'optional' ? 'selected' : '' }}>Optional
                                            </option>
                                            <option value="certification"
                                                {{ request('training_type') == 'certification' ? 'selected' : '' }}>
                                                Certification</option>
                                            <option value="skill_development"
                                                {{ request('training_type') == 'skill_development' ? 'selected' : '' }}>
                                                Skill Development</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="department_id">Departemen</label>
                                        <select name="department_id" id="department_id" class="form-control">
                                            <option value="">Semua Departemen</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}"
                                                    {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                                    {{ $department->divisi }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="search">Pencarian</label>
                                        <input type="text" name="search" id="search" class="form-control"
                                            value="{{ request('search') }}" placeholder="Nama atau kode training">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-search mr-1"></i>
                                        Filter
                                    </button>
                                    <a href="{{ route('hr.training.index') }}" class="btn btn-secondary">
                                        <i class="mdi mdi-times mr-1"></i>
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </form>

                        <!-- Training List -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Training</th>
                                        <th>Tipe</th>
                                        <th>Status</th>
                                        <th>Dibuat Oleh</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($trainings as $training)
                                        <tr>
                                            <td>
                                                <span class="badge badge-info">{{ $training->training_code }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $training->training_name }}</strong>
                                                @if ($training->description)
                                                    <br><small
                                                        class="text-muted">{{ Str::limit($training->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($training->training_type)
                                                    @case('mandatory')
                                                        <span class="badge badge-danger">Mandatory</span>
                                                    @break

                                                    @case('optional')
                                                        <span class="badge badge-success">Optional</span>
                                                    @break

                                                    @case('certification')
                                                        <span class="badge badge-warning">Certification</span>
                                                    @break

                                                    @case('skill_development')
                                                        <span class="badge badge-primary">Skill Development</span>
                                                    @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @switch($training->status)
                                                    @case('active')
                                                        <span class="badge badge-secondary">Active</span>
                                                    @break

                                                    @case('inactive')
                                                        <span class="badge badge-success">Inactive</span>
                                                    @break

                                                @endswitch
                                                @if (!$training->is_active)
                                                    <br><span class="badge badge-dark">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ $training->creator->name ?? 'N/A' }}</td>
                                            <td>{{ $training->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-info" 
                                                            onclick="viewTraining({{ $training->id }})" title="Lihat Detail">
                                                        <i class="mdi mdi-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-warning" 
                                                            onclick="editTraining({{ $training->id }})" title="Edit">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </button>
                                                    @if ($training->status == 'draft')
                                                        <button type="button" class="btn btn-sm btn-success"
                                                                onclick="publishTraining({{ $training->id }})" title="Publish">
                                                            <i class="mdi mdi-publish"></i>
                                                        </button>
                                                    @endif
                                                    @if ($training->participants_count == 0)
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="deleteTraining({{ $training->id }})" title="Hapus">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">
                                                    <div class="py-4">
                                                        <i class="mdi mdi-book-open-page-variant fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted">Belum ada training yang dibuat.</p>
                                                        <a href="{{ route('hr.training.create') }}" class="btn btn-primary">
                                                            <i class="mdi mdi-plus mr-1"></i>
                                                            Buat Training Pertama
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    <p class="text-muted mb-0">
                                        Menampilkan {{ $trainings->firstItem() ?? 0 }} - {{ $trainings->lastItem() ?? 0 }}
                                        dari {{ $trainings->total() }} training
                                    </p>
                                </div>
                                <div>
                                    {{ $trainings->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
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

        <!-- Edit Training Modal -->
        <div class="modal fade" id="editTrainingModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Training</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form id="editTrainingForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body" id="editTrainingContent">
                            <!-- Content will be loaded here -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Training Modal -->
        <div class="modal fade" id="deleteTrainingModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menghapus training ini?</p>
                        <p class="text-muted">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <form id="deleteTrainingForm" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Publish Training Modal -->
        <div class="modal fade" id="publishTrainingModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Konfirmasi Publish</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin mempublish training ini?</p>
                        <p class="text-muted">Training akan menjadi aktif dan dapat didaftarkan oleh karyawan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <form id="publishTrainingForm" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">Publish</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection

        @section('scripts')
            <script>
                $(document).ready(function() {
                    // Auto-submit form on filter change
                    $('#status, #training_type, #department_id').change(function() {
                        $(this).closest('form').submit();
                    });
                });

                // View Training
                function viewTraining(id) {
                    $.get(`/sipo/hr/training/${id}/view`, function(data) {
                        $('#viewTrainingContent').html(data);
                        $('#viewTrainingModal').modal('show');
                    });
                }

                // Edit Training
                function editTraining(id) {
                    $.get(`/sipo/hr/training/${id}/edit`, function(data) {
                        $('#editTrainingContent').html(data);
                        $('#editTrainingForm').attr('action', `/sipo/hr/training/${id}`);
                        $('#editTrainingModal').modal('show');
                    });
                }

                // Delete Training
                function deleteTraining(id) {
                    $('#deleteTrainingForm').attr('action', `/sipo/hr/training/${id}`);
                    $('#deleteTrainingModal').modal('show');
                }

                // Publish Training
                function publishTraining(id) {
                    $('#publishTrainingForm').attr('action', `/sipo/hr/training/${id}/publish`);
                    $('#publishTrainingModal').modal('show');
                }

                // Handle form submissions
                $('#editTrainingForm').on('submit', function(e) {
                    e.preventDefault();
                    const form = $(this);
                    const formData = form.serialize();
                    
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            $('#editTrainingModal').modal('hide');
                            location.reload();
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                // Handle validation errors
                                const errors = xhr.responseJSON.errors;
                                // You can display errors here
                                alert('Terjadi kesalahan validasi. Silakan periksa data yang diinput.');
                            } else {
                                alert('Terjadi kesalahan saat menyimpan data.');
                            }
                        }
                    });
                });

                $('#deleteTrainingForm').on('submit', function(e) {
                    e.preventDefault();
                    const form = $(this);
                    
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function(response) {
                            $('#deleteTrainingModal').modal('hide');
                            location.reload();
                        },
                        error: function() {
                            alert('Terjadi kesalahan saat menghapus data.');
                        }
                    });
                });

                $('#publishTrainingForm').on('submit', function(e) {
                    e.preventDefault();
                    const form = $(this);
                    
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function(response) {
                            $('#publishTrainingModal').modal('hide');
                            location.reload();
                        },
                        error: function() {
                            alert('Terjadi kesalahan saat mempublish training.');
                        }
                    });
                });
            </script>
        @endsection
