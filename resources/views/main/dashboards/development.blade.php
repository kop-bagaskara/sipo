@extends('main.layouts.main')

@section('title')
    Dashboard Development
@endsection

@section('page-title')
    Dashboard
@endsection

@section('body')
<body data-sidebar="colored">
@endsection

@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Dashboard</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>

    <div class="row">
        {{-- List Dashboard Navigation (Left Side) --}}
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="text-primary">List Dashboard</h5>
                    <hr>
                    <a href="{{ route('dashboard') }}" class="btn btn-info w-100 text-left mb-2">
                        <i class="mdi mdi-view-dashboard mr-2"></i>Overview Data
                    </a>
                    <a href="{{ route('dashboard.ppic') }}" class="btn btn-info w-100 text-left mb-2">
                        <i class="mdi mdi-view-dashboard mr-2"></i>Dashboard PPIC
                    </a>
                    <a href="{{ route('dashboard.prepress') }}" class="btn btn-info w-100 text-left mb-2">
                        <i class="mdi mdi-view-dashboard mr-2"></i>Dashboard Prepress
                    </a>
                    <a href="{{ route('dashboard.development') }}" class="btn btn-info w-100 text-left mb-2 active">
                        <i class="mdi mdi-flask mr-2"></i>Development Item
                    </a>
                    @if(auth()->user()->divisi == 8 || auth()->user()->divisi == 1)
                    <a href="{{ route('dashboard.supplier') }}" class="btn btn-info w-100 text-left mb-2">
                        <i class="mdi mdi-truck mr-2"></i>Dashboard Supplier
                    </a>
                    @endif
                    @if(auth()->user()->divisi == 11)
                    <a href="{{ route('dashboard.security') }}" class="btn btn-info w-100 text-left mb-2">
                        <i class="mdi mdi-shield-account mr-2"></i>Dashboard Security
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Development Dashboard Content (Right Side) --}}
        <div class="col-md-9">

    {{-- Navigation between dashboards --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="btn-group" role="group">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">Overview</a>
                <a href="{{ route('dashboard.ppic') }}" class="btn btn-outline-primary">Dashboard PPIC</a>
                <a href="{{ route('dashboard.prepress') }}" class="btn btn-outline-primary">Dashboard Prepress</a>
                <a href="{{ route('dashboard.development') }}" class="btn btn-primary active">Development Item</a>
                @if(auth()->user()->divisi == 8 || auth()->user()->divisi == 1)
                <a href="{{ route('dashboard.supplier') }}" class="btn btn-outline-primary">Supplier</a>
                @endif
                @if(auth()->user()->divisi == 11)
                <a href="{{ route('dashboard.security') }}" class="btn btn-outline-primary">Security</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Development Dashboard Content --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-flask mr-2"></i>
                        Dashboard Development - RnD Workspace
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="mdi mdi-information mr-2"></i>
                        Data akan dimuat secara otomatis. Untuk akses penuh ke RnD Workspace, kunjungi 
                        <a href="/sipo/development/rnd-workspace" class="alert-link">halaman RnD Workspace</a>.
                    </div>

                    {{-- Stats Loading Placeholder --}}
                    <div class="row mb-4" id="devStats">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h2 id="devTotalJobs">-</h2>
                                    <p class="mb-0">Total Jobs</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h2 id="devDraftJobs">-</h2>
                                    <p class="mb-0">Draft</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h2 id="devInProgressJobs">-</h2>
                                    <p class="mb-0">In Progress</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h2 id="devCompletedJobs">-</h2>
                                    <p class="mb-0">Completed</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button class="btn btn-primary" onclick="loadDevelopmentData()">
                            <i class="mdi mdi-refresh mr-2"></i>Load Data
                        </button>
                        <a href="/sipo/development/rnd-workspace" class="btn btn-dark">
                            <i class="mdi mdi-open-in-new mr-2"></i>Open RnD Workspace
                        </a>
                    </div>
                </div>
            </div>
        </div>

        </div>
    </div>
@endsection

@section('scripts')
<script>
function loadDevelopmentData() {
    $.ajax({
        url: '{{ route("development.rnd-workspace.data") }}',
        type: 'GET',
        data: {
            dashboard_only: true
        },
        success: function(response) {
            if (response.data) {
                $('#devTotalJobs').text(response.data.draft + response.data.in_progress + response.data.completed);
                $('#devDraftJobs').text(response.data.draft || 0);
                $('#devInProgressJobs').text(response.data.in_progress || 0);
                $('#devCompletedJobs').text(response.data.completed || 0);
            }
        },
        error: function(xhr) {
            console.error('Error loading development data:', xhr);
        }
    });
}

// Auto-load on page ready
$(document).ready(function() {
    loadDevelopmentData();
});
</script>
@endsection

