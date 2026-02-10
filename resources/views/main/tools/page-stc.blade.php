@extends('main.layouts.main')
@section('title')
    Stock Transfer
@endsection
@section('css')
    <link href="{{ asset('new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection
@section('page-title')
    Stock Transfer
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Stock Transfer</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Tools</a></li>
                            <li class="breadcrumb-item active">Stock Transfer</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">

            <div class="col">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h4 class="card-title text-center text-primary fw-bold">
                            <i class="bx bx-upload"></i> Upload & Filter Stock Transfer
                        </h4>

                        <!-- Pesan Sukses -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                                <i class="bx bx-check-circle"></i> {{ session('success') }}
                                <br>
                                <a href="{{ session('downloadLink') }}" class="btn btn-sm btn-success mt-2">
                                    <i class="bx bx-download"></i> Download Filtered File
                                </a>
                            </div>
                        @endif

                        <!-- Pesan Error -->
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                                <i class="bx bx-error"></i> {{ session('error') }}
                            </div>
                        @endif

                        <!-- Form Upload -->
                        <form action="{{ route('stc.import') }}" method="POST" enctype="multipart/form-data" class="text-center">
                            @csrf
                            <div class="row">
                                <div class="col-md-10">

                                    <div class="mb-3">
                                        <input type="file" name="file" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-2">

                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-filter"></i> Upload & Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    @endsection
    @section('scripts')
        <script src="{{ asset('new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/buttons.flash.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/buttons.print.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/dataTables.keyTable.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/dataTables.select.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/pdfmake.min.js') }}"></script>
        <script src="{{ asset('new/plugins/datatables/vfs_fonts.js') }}"></script>
        <script src="{{ asset('new/assets/pages/datatables-demo.js') }}"></script>

        <script>
            $(document).ready(function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });


            });
        </script>
    @endsection
