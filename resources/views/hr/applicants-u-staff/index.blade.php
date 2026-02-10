@extends('main.layouts.main')

@section('title')
    Data Pelamar
@endsection

@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection

@section('page-title')
    Data Pelamar
@endsection

@section('body')
    <body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Data Pelamar</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item active">Data Pelamar</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">Daftar Pelamar</h4>
                        </div>
                        {{-- <div class="col d-flex justify-content-end">
                            <a href="{{ route('hr.applicants.create') }}" class="btn btn-info" style="color: white;">
                                <i class="mdi mdi-plus"></i> Tambah Pelamar
                            </a>
                        </div> --}}
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('public.staff-applicant.index') }}" class="form-inline">
                                <div class="form-group mr-3">
                                    <label class="mr-2">Cari:</label>
                                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nama, Email, atau Posisi">
                                </div>
                                <div class="form-group mr-3">
                                    <label class="mr-2">Status:</label>
                                    <select name="status" class="form-control">
                                        <option value="">Semua Status</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mr-3">
                                    <label class="mr-2">Posisi:</label>
                                    <select name="posisi" class="form-control">
                                        <option value="">Semua Posisi</option>
                                        @foreach($positions as $position)
                                            <option value="{{ $position }}" {{ request('posisi') == $position ? 'selected' : '' }}>
                                                {{ $position }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-info">
                                    <i class="mdi mdi-magnify"></i> Filter
                                </button>
                                <a href="{{ route('public.staff-applicant.index') }}" class="btn btn-secondary ml-2">
                                    <i class="mdi mdi-refresh"></i> Reset
                                </a>
                            </form>
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Lengkap</th>
                                    <th>Email</th>
                                    <th>Posisi Dilamar</th>
                                    <th>Status</th>
                                    <th>Progress Test</th>
                                    <th>Tanggal Melamar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($applicants as $index => $applicant)
                                    <tr>
                                        <td>{{ $applicants->firstItem() + $index }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                {{-- @if($applicant->foto)
                                                    <img src="{{ Storage::url($applicant->foto) }}" alt="Foto" class="rounded-circle me-2" width="40" height="40">
                                                @else
                                                    <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="mdi mdi-account text-white"></i>
                                                    </div>
                                                @endif --}}
                                                <div>
                                                    <strong>{{ $applicant->nama_lengkap }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $applicant->no_telepon }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $applicant->email }}</td>
                                        <td>{{ $applicant->posisi_dilamar }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'pending' => 'secondary',
                                                    'test' => 'warning',
                                                    'interview' => 'info',
                                                    'accepted' => 'success',
                                                    'rejected' => 'danger'
                                                ];
                                            @endphp
                                            <span class="badge badge-{{ $statusColors[$applicant->status] ?? 'secondary' }}">
                                                {{ $applicant->status_formatted }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $completedTests = $applicant->testResults->count();
                                                $totalTests = 2; // Staff level hanya 2 test (matematika & buta warna)
                                                $percentage = ($completedTests / $totalTests) * 100;
                                            @endphp
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%">
                                                    {{ $completedTests }}/{{ $totalTests }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $applicant->tanggal_melamar->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('public.staff-applicant.show', $applicant) }}"
                                                   class="btn btn-info btn-sm" title="Lihat Detail">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>
                                                <a href="{{ route('public.staff-applicant.edit', $applicant) }}"
                                                   class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="mdi mdi-pencil"></i>
                                                </a>
                                                @if($completedTests < $totalTests)
                                                    <a href="{{ route('public.staff-applicant.tests', $applicant) }}"
                                                       class="btn btn-success btn-sm" title="Mulai Test">
                                                        <i class="mdi mdi-play"></i> Test
                                                    </a>
                                                @endif
                                                <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="confirmDelete({{ $applicant->id }})" title="Hapus">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="alert alert-info">
                                                <i class="mdi mdi-information"></i>
                                                Tidak ada data pelamar
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
                            <p class="text-muted">
                                Menampilkan {{ $applicants->firstItem() ?? 0 }} sampai {{ $applicants->lastItem() ?? 0 }}
                                dari {{ $applicants->total() }} data
                            </p>
                        </div>
                        <div>
                            {{ $applicants->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // SweetAlert untuk success message
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    toast: true,
                    position: 'top-end'
                });
            @endif

            // SweetAlert untuk error message
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                });
            @endif
        });

        // Define confirmDelete function globally
        window.confirmDelete = function(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data pelamar akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // AJAX Delete
                    fetch(`{{ route('public.staff-applicant.index') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            return response.json();
                        }
                        throw new Error('Network response was not ok');
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message || 'Data pelamar berhasil dihapus',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: data.message || 'Terjadi kesalahan saat menghapus data',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#d33'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Terjadi kesalahan saat menghapus data',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#d33'
                        });
                    });
                }
            });
        };
    </script>
@endsection
