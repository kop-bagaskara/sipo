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
                        <h4 class="card-title">
                            <i class="mdi mdi-account-group"></i>
                            Daftar Training untuk Manajemen Peserta
                        </h4>
                        <p class="card-title-desc">Kelola peserta training dari database paytest</p>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-check-all me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="mdi mdi-block-helper me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">Kode Training</th>
                                        <th width="25%">Nama Training</th>
                                        <th width="15%">Tipe</th>
                                        <th width="15%">Metode</th>
                                        <th width="10%">Status</th>
                                        <th width="10%">Peserta</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($trainings as $index => $training)
                                        <tr>
                                            <td>{{ $trainings->firstItem() + $index }}</td>
                                            <td>
                                                <span class="badge badge-info">{{ $training->training_code }}</span>
                                            </td>
                                            <td>{{ $training->training_name }}</td>
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
                                                @switch($training->training_method)
                                                    @case('classroom')
                                                        <span class="badge badge-info">Kelas</span>
                                                    @break
                                                    @case('online')
                                                        <span class="badge badge-primary">Online</span>
                                                    @break
                                                    @case('hybrid')
                                                        <span class="badge badge-warning">Hybrid</span>
                                                    @break
                                                    @case('workshop')
                                                        <span class="badge badge-success">Workshop</span>
                                                    @break
                                                    @case('seminar')
                                                        <span class="badge badge-secondary">Seminar</span>
                                                    @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @switch($training->status)
                                                    @case('active')
                                                        <span class="badge badge-success">Active</span>
                                                    @break
                                                    @case('inactive')
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    @break
                                                    @case('ongoing')
                                                        <span class="badge badge-warning">Ongoing</span>
                                                    @break
                                                    @case('completed')
                                                        <span class="badge badge-info">Completed</span>
                                                    @break
                                                    @case('cancelled')
                                                        <span class="badge badge-danger">Cancelled</span>
                                                    @break
                                                @endswitch
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-primary">{{ $training->participants->count() }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('hr.training.management.show', $training->id) }}" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="Kelola Peserta">
                                                        <i class="mdi mdi-account"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                <i class="mdi mdi-information-outline me-2"></i>
                                                Belum ada training yang tersedia
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($trainings->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $trainings->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
