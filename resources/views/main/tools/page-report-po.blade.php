@extends('main.layouts.main')
@section('title')
    Report Purchase Order
@endsection
@section('css')
    <link href="{{ asset('new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection
@section('page-title')
    Report Purchase Order
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 font-size-18">Report Purchase Order</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Tools</a></li>
                            <li class="breadcrumb-item active">Report Purchase Order</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>

        <form action="{{ route('report.fetch') }}" method="POST">
            <div class="row">

                @csrf
                <div class="col">
                    @if (session('success'))
                        <div class="alert alert-success text-center" role="alert">
                            <i class="bx bx-check-circle"></i> {{ session('success') }}
                            <a href="{{ session('downloadLink') }}" class="btn btn-success w-100">
                                Download File
                            </a>
                        </div>
                    @endif
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col">
                                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control"
                                        required>
                                </div>
                                <div class="col">
                                    <label for="tanggal_akhir" class="form-label">Sampai</label>
                                    <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control"
                                        required>
                                </div>
                                <div class="col">
                                    <label for="" class="form-label text-white">Button</label>
                                    <button type="submit" class="btn btn-primary w-100">Tarik Data</button>
                                </div>
                            </div>

                            <div id="accordion" class="custom-accordion mb-4">
                                <div class="card mb-0">
                                    <div class="card-header" id="headingThree">
                                        <h5 class="m-0 font-size-15">
                                            <a class="collapsed d-block pt-2 pb-2 text-dark" data-toggle="collapse"
                                                href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                Kode Material untuk Detail Purchase Order <span class="float-right"><i
                                                        class="mdi mdi-chevron-down accordion-arrow"></i></span>
                                            </a>
                                        </h5>
                                    </div>
                                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                                        data-parent="#accordion">
                                        <div class="card-body">
                                            @foreach ($kodeitem as $item)
                                                <div class="form-check mb-4">
                                                    <input class="form-check-input" type="checkbox" name="kode_material[]"
                                                        value="{{ $item->kode_material }}" id="flexCheckChecked" checked>
                                                    <label class="form-check-label" for="flexCheckChecked">
                                                        {{ $item->kode_material }} -
                                                        {{ $item->kodeMaterialSIM->Name ?? null }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end card-->
                        </div>
                    </div>
                </div>





            </div>
        </form>
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
