@extends('main.layouts.main')
@section('title')
    Data Trial Bahan Baku
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection
@section('page-title')
    Data Trial Bahan Baku
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Data Trial Bahan Baku</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active">Data Trial</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title">Daftar Pengajuan Trial</h4>
                        <a href="{{ route('trial.samples.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Buat Pengajuan Baru
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="trialSamplesTable">
                            <thead>
                                <tr>
                                    <th>No. Pengajuan</th>
                                    <th>Material</th>
                                    <th>Supplier</th>
                                    <th>Status</th>
                                    <th>Dibuat Oleh</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trialSamples as $trial)
                                    <tr>
                                        <td>
                                            <strong>{{ $trial->nomor_pengajuan }}</strong>
                                        </td>
                                        <td>
                                            <div><strong>{{ $trial->material_bahan }}</strong></div>
                                            <small class="text-muted">{{ $trial->nama_barang }}</small>
                                        </td>
                                        <td>
                                            <div>{{ $trial->nama_supplier }}</div>
                                            <small class="text-muted">{{ $trial->kode_supplier }}</small>
                                        </td>
                                        <td>
                                            @switch($trial->status)
                                                @case('draft')
                                                    <span class="badge badge-warning">Draft</span>
                                                    @break
                                                @case('submitted')
                                                    <span class="badge badge-info">Submitted</span>
                                                    @break
                                                @case('purchasing_review')
                                                    <span class="badge badge-primary">Purchasing Review</span>
                                                    @break
                                                @case('purchasing_approved')
                                                    <span class="badge badge-success">Purchasing Approved</span>
                                                    @break
                                                @case('purchasing_rejected')
                                                    <span class="badge badge-danger">Purchasing Rejected</span>
                                                    @break
                                                @case('qa_processing')
                                                    <span class="badge badge-info">QA Processing</span>
                                                    @break
                                                @case('qa_completed')
                                                    <span class="badge badge-warning">QA Completed</span>
                                                    @break
                                                @case('qa_verified')
                                                    <span class="badge badge-success">QA Verified</span>
                                                    @break
                                                @case('closed')
                                                    <span class="badge badge-secondary">Closed</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">{{ $trial->status }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div>{{ $trial->creator->name }}</div>
                                            <small class="text-muted">{{ $trial->creator->email }}</small>
                                        </td>
                                        <td>
                                            {{ $trial->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('trial.samples.show', $trial) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if($trial->status === 'draft' && $trial->created_by === auth()->id())
                                                    <button type="button" 
                                                            class="btn btn-sm btn-success submit-purchasing-btn" 
                                                            data-trial-id="{{ $trial->id }}"
                                                            title="Submit ke Purchasing">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </button>
                                                @endif
                                                
                                                @if($trial->status === 'submitted' && auth()->user()->hasRole('purchasing'))
                                                    <button type="button" 
                                                            class="btn btn-sm btn-success approve-btn" 
                                                            data-trial-id="{{ $trial->id }}"
                                                            title="Approve">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger reject-btn" 
                                                            data-trial-id="{{ $trial->id }}"
                                                            title="Reject">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                                
                                                @if($trial->status === 'purchasing_approved' && auth()->user()->hasRole('quality_assurance'))
                                                    <button type="button" 
                                                            class="btn btn-sm btn-primary qa-start-btn" 
                                                            data-trial-id="{{ $trial->id }}"
                                                            title="Mulai Proses QA">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                @endif
                                                
                                                @if($trial->status === 'qa_verified' && $trial->created_by === auth()->id())
                                                    <button type="button" 
                                                            class="btn btn-sm btn-secondary close-btn" 
                                                            data-trial-id="{{ $trial->id }}"
                                                            title="Close Pengajuan">
                                                        <i class="fas fa-lock"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data trial</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $trialSamples->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#trialSamplesTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
                }
            });

            // Handle submit to purchasing
            $('.submit-purchasing-btn').click(function() {
                const trialId = $(this).data('trial-id');
                
                Swal.fire({
                    title: 'Submit ke Purchasing?',
                    text: 'Pengajuan akan dikirim ke purchasing untuk review. Lanjutkan?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Submit!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/sipo/trial/samples/${trialId}/submit-purchasing`,
                            type: "POST",
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message,
                                        showConfirmButton: true
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message || 'Terjadi kesalahan',
                                        showConfirmButton: true
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'Terjadi kesalahan sistem';
                                
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: errorMessage,
                                    showConfirmButton: true
                                });
                            }
                        });
                    }
                });
            });

            // Handle approve
            $('.approve-btn').click(function() {
                const trialId = $(this).data('trial-id');
                
                Swal.fire({
                    title: 'Approve Pengajuan?',
                    text: 'Masukkan catatan (opsional):',
                    input: 'textarea',
                    inputPlaceholder: 'Catatan approval...',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Approve!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/sipo/trial/samples/${trialId}/purchasing-approve`,
                            type: "POST",
                            data: {
                                notes: result.value
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message,
                                        showConfirmButton: true
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message || 'Terjadi kesalahan',
                                        showConfirmButton: true
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'Terjadi kesalahan sistem';
                                
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: errorMessage,
                                    showConfirmButton: true
                                });
                            }
                        });
                    }
                });
            });

            // Handle reject
            $('.reject-btn').click(function() {
                const trialId = $(this).data('trial-id');
                
                Swal.fire({
                    title: 'Reject Pengajuan?',
                    text: 'Masukkan alasan rejection:',
                    input: 'textarea',
                    inputPlaceholder: 'Alasan rejection...',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Alasan rejection harus diisi!';
                        }
                    },
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Reject!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/sipo/trial/samples/${trialId}/purchasing-reject`,
                            type: "POST",
                            data: {
                                notes: result.value
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message,
                                        showConfirmButton: true
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message || 'Terjadi kesalahan',
                                        showConfirmButton: true
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'Terjadi kesalahan sistem';
                                
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: errorMessage,
                                    showConfirmButton: true
                                });
                            }
                        });
                    }
                });
            });

            // Handle QA start processing
            $('.qa-start-btn').click(function() {
                const trialId = $(this).data('trial-id');
                
                Swal.fire({
                    title: 'Mulai Proses QA?',
                    text: 'Pengajuan akan masuk ke tahap proses QA. Lanjutkan?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Mulai!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/sipo/trial/samples/${trialId}/qa-start-processing`,
                            type: "POST",
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message,
                                        showConfirmButton: true
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message || 'Terjadi kesalahan',
                                        showConfirmButton: true
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'Terjadi kesalahan sistem';
                                
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: errorMessage,
                                    showConfirmButton: true
                                });
                            }
                        });
                    }
                });
            });

            // Handle close
            $('.close-btn').click(function() {
                const trialId = $(this).data('trial-id');
                
                Swal.fire({
                    title: 'Close Pengajuan?',
                    text: 'Pengajuan akan di-close dan tidak bisa diubah lagi. Lanjutkan?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Close!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/sipo/trial/samples/${trialId}/close`,
                            type: "POST",
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message,
                                        showConfirmButton: true
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message || 'Terjadi kesalahan',
                                        showConfirmButton: true
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'Terjadi kesalahan sistem';
                                
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: errorMessage,
                                    showConfirmButton: true
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
