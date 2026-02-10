@extends('main.layouts.main')

@section('title')
    Dashboard Supplier
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
                    <a href="{{ route('dashboard.supplier') }}" class="btn btn-info w-100 text-left mb-2 active">
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

        {{-- Supplier Dashboard Content (Right Side) --}}
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
                <a href="{{ route('dashboard.supplier') }}" class="btn btn-primary active">Supplier</a>
                @endif
                @if(auth()->user()->divisi == 11)
                <a href="{{ route('dashboard.security') }}" class="btn btn-outline-primary">Security</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Supplier Dashboard Content --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-truck mr-2"></i>
                        Dashboard Supplier
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="mdi mdi-information mr-2"></i>
                        Dashboard Supplier - Ticket dan Timeline Delivery
                    </div>

                    <div class="row" id="supplierStats">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h2 id="supTotalTickets">-</h2>
                                    <p class="mb-0">Total Tickets</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h2 id="supPendingTickets">-</h2>
                                    <p class="mb-0">Pending</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h2 id="supCompletedTickets">-</h2>
                                    <p class="mb-0">Completed</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h2 id="supTodayDeliveries">-</h2>
                                    <p class="mb-0">Today's Deliveries</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button class="btn btn-primary" onclick="loadSupplierData()">
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
function loadSupplierData() {
    $.ajax({
        url: '{{ route("supplier-tickets.dashboard-data") }}',
        type: 'GET',
        success: function(response) {
            if (response.stats) {
                $('#supTotalTickets').text(response.stats.total || 0);
                $('#supPendingTickets').text(response.stats.pending || 0);
                $('#supCompletedTickets').text(response.stats.completed || 0);
                $('#supTodayDeliveries').text(response.stats.today_deliveries || 0);
            }
        },
        error: function(xhr) {
            console.error('Error loading supplier data:', xhr);
        }
    });
}

$(document).ready(function() {
    loadSupplierData();
});
</script>
@endsection

