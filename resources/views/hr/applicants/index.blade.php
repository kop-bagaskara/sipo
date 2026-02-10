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
    <style>
        .accordion .card-header .btn-link {
            color: #333;
            text-decoration: none;
            width: 100%;
            text-align: left;
            padding: 15px;
            font-size: 16px;
            font-weight: 500;
        }
        .accordion .card-header .btn-link:hover {
            color: #007bff;
            text-decoration: none;
        }
        .accordion .card-header .btn-link:not(.collapsed) {
            color: #007bff;
            font-weight: 600;
        }
        .accordion .card-header .btn-link i {
            margin-right: 10px;
            font-size: 18px;
        }
        .accordion .card {
            border: 1px solid #dee2e6;
            margin-bottom: 10px;
        }
        .accordion .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        /* Pagination Styling */
        .pagination {
            margin-bottom: 0;
        }
        .pagination .page-link {
            padding: 8px 12px;
            border-radius: 4px;
            margin: 0 2px;
            border: 1px solid #dee2e6;
            color: #007bff;
            transition: all 0.3s ease;
        }
        .pagination .page-link:hover {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
            transform: translateY(-1px);
        }
        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
            z-index: 1;
        }
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            cursor: auto;
            background-color: #fff;
            border-color: #dee2e6;
        }
    </style>
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
            <!-- Accordion for Level Selection -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="accordion" id="levelAccordion">
                        <!-- Staff Level Panel -->
                        <div class="card">
                            <div class="card-header" id="headingStaff">
                                <h5 class="mb-0">
                                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseStaff" aria-expanded="true" aria-controls="collapseStaff">
                                        <i class="mdi mdi-account"></i> <strong>Staff Level</strong> - Data Level Staff (4 Test: Matematika, Krapelin, Buta Warna, Kepribadian)
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseStaff" class="collapse show" aria-labelledby="headingStaff" data-parent="#levelAccordion">
                                <div class="card-body">
                                    @include('hr.applicants.partials.applicant-table', [
                                        'applicants' => $applicants,
                                        'level' => 'staff',
                                        'totalTests' => 4,
                                        'statuses' => $statuses,
                                        'positions' => $positions
                                    ])
                                </div>
                            </div>
                        </div>

                        <!-- Under Staff Level Panel -->
                        <div class="card">
                            <div class="card-header" id="headingUnderStaff">
                                <h5 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseUnderStaff" aria-expanded="false" aria-controls="collapseUnderStaff">
                                        <i class="mdi mdi-account"></i> <strong>Under Staff Level</strong> - Data Level Under Staff (2 Test: Matematika, Buta Warna)
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseUnderStaff" class="collapse" aria-labelledby="headingUnderStaff" data-parent="#levelAccordion">
                                <div class="card-body">
                                    @include('hr.applicants.partials.applicant-table', [
                                        'applicants' => $applicantsUnderStaff,
                                        'level' => 'under-staff',
                                        'totalTests' => 2,
                                        'statuses' => $statuses,
                                        'positions' => $positions
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Old structure removed - now using partial view --}}
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
        window.confirmDelete = function(id, level) {
            // Determine delete URL based on level
            let deleteUrl;
            if (level === 'staff') {
                deleteUrl = '{{ url("sipo/hr/applicants") }}/' + id;
            } else {
                deleteUrl = '{{ url("sipo/hr/staff-applicants") }}/' + id;
            }

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
                    fetch(deleteUrl, {
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
