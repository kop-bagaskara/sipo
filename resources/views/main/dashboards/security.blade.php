@extends('main.layouts.main')

@section('title')
    Dashboard Security
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
                    <a href="{{ route('dashboard.development') }}" class="btn btn-info w-100 text-left mb-2">
                        <i class="mdi mdi-flask mr-2"></i>Development Item
                    </a>
                    @if(auth()->user()->divisi == 8 || auth()->user()->divisi == 1)
                    <a href="{{ route('dashboard.supplier') }}" class="btn btn-info w-100 text-left mb-2">
                        <i class="mdi mdi-truck mr-2"></i>Dashboard Supplier
                    </a>
                    @endif
                    @if(auth()->user()->divisi == 11)
                    <a href="{{ route('dashboard.security') }}" class="btn btn-info w-100 text-left mb-2 active">
                        <i class="mdi mdi-shield-account mr-2"></i>Dashboard Security
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Security Dashboard Content (Right Side) --}}
        <div class="col-md-9">

    {{-- Navigation between dashboards --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="btn-group" role="group">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">Overview</a>
                <a href="{{ route('dashboard.ppic') }}" class="btn btn-outline-primary">Dashboard PPIC</a>
                <a href="{{ route('dashboard.prepress') }}" class="btn btn-outline-primary">Dashboard Prepress</a>
                <a href="{{ route('dashboard.development') }}" class="btn btn-outline-primary">Development Item</a>
                @if(auth()->user()->divisi == 8 || auth()->user()->divisi == 1)
                <a href="{{ route('dashboard.supplier') }}" class="btn btn-outline-primary">Supplier</a>
                @endif
                @if(auth()->user()->divisi == 11)
                <a href="{{ route('dashboard.security') }}" class="btn btn-primary active">Security</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Security Dashboard Content --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-shield-account mr-2"></i>
                        Dashboard Security
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="mdi mdi-information mr-2"></i>
                        Dashboard Security - Data vehicle checklist dan goods movement
                    </div>

                    <div class="row" id="securityStats">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h2 id="secTotalChecklist">-</h2>
                                    <p class="mb-0">Total Checklist</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h2 id="secPendingReturn">-</h2>
                                    <p class="mb-0">Pending Return</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h2 id="secGoodsMovement">-</h2>
                                    <p class="mb-0">Goods Movement</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button class="btn btn-primary" onclick="loadSecurityData()">
                            <i class="mdi mdi-refresh mr-2"></i>Load Data
                        </button>
                    </div>
                </div>
            </div>
        </div>

        </div>
    </div>
@endsection

@section('scripts')
<script>
function loadSecurityData() {
    $.ajax({
        url: '{{ route("security.dashboard-data") }}',
        type: 'GET',
        success: function(response) {
            if (response.stats) {
                $('#secTotalChecklist').text(response.stats.total_checklist || 0);
                $('#secPendingReturn').text(response.stats.pending_return || 0);
                $('#secGoodsMovement').text(response.stats.goods_movement || 0);
            }
        },
        error: function(xhr) {
            console.error('Error loading security data:', xhr);
        }
    });
}

$(document).ready(function() {
    loadSecurityData();
});
</script>
@endsection

