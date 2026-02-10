@extends('main.layouts.main')
@section('title')
    Master Item Unilever
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
    Master Item Unilever
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Master Item Unilever</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('label-management.index') }}">Label Management</a></li>
                <li class="breadcrumb-item active">Master Item Unilever</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="card-title mb-1">Master Item Unilever</h4>
                            <p class="text-muted mb-0">Kelola master data item Unilever</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalItem">
                                <i class="mdi mdi-plus"></i> Tambah Item
                            </button>
                            <a href="{{ route('label-management.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>

                    <!-- Search Form -->
                    <form method="GET" action="{{ route('label-management.master-item-unilever.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="search"
                                           value="{{ $search }}"
                                           placeholder="Cari berdasarkan Kode Design, Nama Item, PC, MC, atau QTY...">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="mdi mdi-magnify"></i> Cari
                                    </button>
                                    <a href="{{ route('label-management.master-item-unilever.index') }}" class="btn btn-secondary">
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
                                    <th>Kode Design</th>
                                    <th>Nama Item</th>
                                    <th>PC</th>
                                    <th>MC</th>
                                    <th>QTY</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    @php
                                        $materialData = null;
                                        if ($item->KodeDesign) {
                                            $materialData = \App\Models\MasterMaterial::where('Code', $item->KodeDesign)->first();
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>
                                            @if($item->KodeDesign)
                                                <strong>{{ $item->KodeDesign }}</strong>
                                                @if($materialData)
                                                    <br><small class="text-muted">{{ $materialData->Name }}</small>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $item->NamaItem ?? '-' }}</td>
                                        <td>{{ $item->PC ?? '-' }}</td>
                                        <td>{{ $item->MC ?? '-' }}</td>
                                        <td>{{ $item->QTY ?? '-' }}</td>
                                        <td>{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}</td>
                                        <td>{{ $item->updated_at ? $item->updated_at->format('d/m/Y H:i') : '-' }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info"
                                                    onclick="editItem({{ $item->id }}, '{{ addslashes($item->KodeDesign ?? '') }}', '{{ addslashes($item->NamaItem ?? '') }}', '{{ addslashes($item->PC ?? '') }}', '{{ addslashes($item->MC ?? '') }}', '{{ addslashes($item->QTY ?? '') }}')">
                                                <i class="mdi mdi-pencil"></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                    onclick="deleteItem({{ $item->id }}, '{{ addslashes($item->NamaItem ?? '') }}')">
                                                <i class="mdi mdi-delete"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $items->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add/Edit Item -->
    <div class="modal fade" id="modalItem" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalItemTitle">Tambah Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formItem">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="item_id" name="id">
                        <input type="hidden" id="form_method" name="_method" value="POST">
                        <div class="form-group">
                            <label for="KodeDesign">Kode Design</label>
                            <input type="text" class="form-control" id="KodeDesign" name="KodeDesign" placeholder="Masukkan Kode Design, lalu tekan Enter atau klik di luar untuk mencari...">
                            <small class="text-muted">Setelah diisi, sistem akan mencari di database untuk mengisi Nama Item</small>
                        </div>
                        <div class="form-group">
                            <label for="NamaItem">Nama Item</label>
                            <input type="text" class="form-control" id="NamaItem" name="NamaItem" placeholder="Masukkan Nama Item">
                        </div>
                        <div class="form-group">
                            <label for="PC">PC</label>
                            <input type="text" class="form-control" id="PC" name="PC" placeholder="Masukkan PC">
                        </div>
                        <div class="form-group">
                            <label for="MC">MC</label>
                            <input type="text" class="form-control" id="MC" name="MC" placeholder="Masukkan MC">
                        </div>
                        <div class="form-group">
                            <label for="QTY">QTY</label>
                            <input type="text" class="form-control" id="QTY" name="QTY" placeholder="Masukkan QTY">
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        let isEditMode = false;

        // Search material after Kode Design is filled
        $('#KodeDesign').on('blur', function() {
            const kodeDesign = $(this).val().trim();
            if (kodeDesign && !isEditMode) {
                // Search material in database
                $.ajax({
                    url: '{{ route("label-management.search-master-material") }}',
                    data: { search: kodeDesign },
                    dataType: 'json',
                    success: function(data) {
                        if (data.materials && data.materials.length > 0) {
                            // Find exact match
                            const material = data.materials.find(m => m.Code === kodeDesign);
                            if (material) {
                                // Auto-fill Nama Item if empty
                                if (!$('#NamaItem').val()) {
                                    $('#NamaItem').val(material.Name);
                                }
                            }
                        }
                    },
                    error: function() {
                        console.log('Error searching material');
                    }
                });
            }
        });

        // Also search on Enter key
        $('#KodeDesign').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                $(this).blur();
            }
        });

        // Reset form when modal is closed
        $('#modalItem').on('hidden.bs.modal', function () {
            $('#formItem')[0].reset();
            $('#item_id').val('');
            $('#form_method').val('POST');
            $('#modalItemTitle').text('Tambah Item');
            isEditMode = false;
        });

        // Form submit
        $('#formItem').on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();
            const itemId = $('#item_id').val();
            const url = itemId
                ? '{{ route("label-management.master-item-unilever.update", ":id") }}'.replace(':id', itemId)
                : '{{ route("label-management.master-item-unilever.store") }}';
            const method = itemId ? 'POST' : 'POST'; // Laravel uses POST with _method spoofing

            // Set method for PUT
            if (itemId) {
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

        // Edit item
        function editItem(id, kodeDesign, namaItem, pc, mc, qty) {
            isEditMode = true;
            $('#item_id').val(id);
            $('#KodeDesign').val(kodeDesign);
            $('#NamaItem').val(namaItem);
            $('#PC').val(pc);
            $('#MC').val(mc);
            $('#QTY').val(qty);
            $('#modalItemTitle').text('Edit Item');
            $('#modalItem').modal('show');
        }

        // Delete item
        function deleteItem(id, namaItem) {
            Swal.fire({
                title: 'Yakin?',
                text: 'Apakah Anda yakin ingin menghapus item "' + namaItem + '"?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("label-management.master-item-unilever.destroy", ":id") }}'.replace(':id', id),
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
                                text: 'Gagal menghapus item'
                            });
                        }
                    });
                }
            });
        }
    </script>
@endsection
