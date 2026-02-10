@extends('main.layouts.main')
@section('title')
    Master Code Operator
@endsection
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <style>
        .table-responsive {
            overflow-x: auto;
        }
    </style>
@endsection
@section('page-title')
    Master Code Operator
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Master Code Operator</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('label-management.index') }}">Label Management</a></li>
                <li class="breadcrumb-item active">Master Code Operator</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="card-title mb-1">Master Code Operator</h4>
                            <p class="text-muted mb-0">Kelola master data code operator</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalOperator">
                                <i class="mdi mdi-plus"></i> Tambah Operator
                            </button>
                            <a href="{{ route('label-management.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>

                    <!-- Search Form -->
                    <form method="GET" action="{{ route('label-management.master-code-operator.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="search"
                                           value="{{ $search }}"
                                           placeholder="Cari berdasarkan Mesin, Nama, atau Kode...">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-magnify"></i> Cari
                                    </button>
                                    <a href="{{ route('label-management.master-code-operator.index') }}" class="btn btn-secondary">
                                        <i class="mdi mdi-refresh"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Mesin</th>
                                    <th>Nama</th>
                                    <th>Kode</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($operators as $operator)
                                    @php
                                        $machineData = null;
                                        if ($operator->Mesin) {
                                            $machineData = $machines->where('Code', $operator->Mesin)->first();
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $operator->id }}</td>
                                        <td>
                                            @if($operator->Mesin)
                                                <strong>{{ $operator->Mesin }}</strong>
                                                @if($machineData)
                                                    <br><small class="text-muted">{{ $machineData->Description }}</small>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $operator->Nama ?? '-' }}</td>
                                        <td>{{ $operator->Kode ?? '-' }}</td>
                                        <td>{{ $operator->created_at ? $operator->created_at->format('d/m/Y H:i') : '-' }}</td>
                                        <td>{{ $operator->updated_at ? $operator->updated_at->format('d/m/Y H:i') : '-' }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info"
                                                    onclick="editOperator({{ $operator->id }}, '{{ addslashes($operator->Mesin ?? '') }}', '{{ addslashes($operator->Nama ?? '') }}', '{{ addslashes($operator->Kode ?? '') }}')">
                                                <i class="mdi mdi-pencil"></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="deleteOperator({{ $operator->id }}, '{{ addslashes($operator->Nama ?? '') }}')">
                                                <i class="mdi mdi-delete"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $operators->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add/Edit Operator -->
    <div class="modal fade" id="modalOperator" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalOperatorTitle">Tambah Operator</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formOperator">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="operator_id" name="id">
                        <input type="hidden" id="form_method" name="_method" value="POST">
                        <div class="form-group">
                            <label for="Mesin">Mesin</label>
                            <select class="form-control" id="Mesin" name="Mesin">
                                <option value="">Pilih Mesin...</option>
                                @foreach($machines as $machine)
                                    <option value="{{ $machine->Code }}">{{ $machine->Code }} - {{ $machine->Description }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="Nama">Nama</label>
                            <input type="text" class="form-control" id="Nama" name="Nama" placeholder="Masukkan Nama">
                        </div>
                        <div class="form-group">
                            <label for="Kode">Kode</label>
                            <input type="text" class="form-control" id="Kode" name="Kode" placeholder="Masukkan Kode">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script>
        let isEditMode = false;

        // Reset form when modal is closed
        $('#modalOperator').on('hidden.bs.modal', function () {
            $('#formOperator')[0].reset();
            $('#operator_id').val('');
            $('#form_method').val('POST');
            $('#modalOperatorTitle').text('Tambah Operator');
            isEditMode = false;
        });

        // Form submit
        $('#formOperator').on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();
            const operatorId = $('#operator_id').val();
            const url = operatorId
                ? '{{ route("label-management.master-code-operator.update", ":id") }}'.replace(':id', operatorId)
                : '{{ route("label-management.master-code-operator.store") }}';
            const method = operatorId ? 'POST' : 'POST'; // Laravel uses POST with _method spoofing

            // Set method for PUT
            if (operatorId) {
                $('#form_method').val('PUT');
            } else {
                $('#form_method').val('POST');
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                }
            });
        });

        // Edit operator
        function editOperator(id, mesin, nama, kode) {
            isEditMode = true;
            $('#operator_id').val(id);
            $('#Mesin').val(mesin);
            $('#Nama').val(nama);
            $('#Kode').val(kode);
            $('#modalOperatorTitle').text('Edit Operator');
            $('#modalOperator').modal('show');
        }

        // Delete operator
        function deleteOperator(id, nama) {
            Swal.fire({
                title: 'Yakin?',
                text: 'Apakah Anda yakin ingin menghapus operator "' + nama + '"?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("label-management.master-code-operator.destroy", ":id") }}'.replace(':id', id),
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Gagal menghapus operator'
                            });
                        }
                    });
                }
            });
        }
    </script>
@endsection
