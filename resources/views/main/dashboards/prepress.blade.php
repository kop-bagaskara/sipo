@extends('main.layouts.main')

@section('title')
    Dashboard Prepress
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
                    <a href="{{ route('dashboard.prepress') }}" class="btn btn-info w-100 text-left mb-2 active">
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
                    <a href="{{ route('dashboard.security') }}" class="btn btn-info w-100 text-left mb-2">
                        <i class="mdi mdi-shield-account mr-2"></i>Dashboard Security
                    </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Prepress Dashboard Content (Right Side) --}}
        <div class="col-md-9">
        <div class="col-12">
                <a href="{{ route('dashboard.prepress') }}" class="btn btn-primary active">Dashboard Prepress</a>
                <a href="{{ route('dashboard.development') }}" class="btn btn-outline-primary">Development Item</a>
                @if(auth()->user()->divisi == 8 || auth()->user()->divisi == 1)
                <a href="{{ route('dashboard.supplier') }}" class="btn btn-outline-primary">Supplier</a>
                @endif
                @if(auth()->user()->divisi == 11)
                <a href="{{ route('dashboard.security') }}" class="btn btn-outline-primary">Security</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Prepress Dashboard Content --}}
    <div class="row">
        {{-- Stats --}}
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h2>{{ $prepressData['stats']['total'] }}</h2>
                    <p class="mb-0">Total Jobs</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h2>{{ $prepressData['stats']['finish'] }}</h2>
                    <p class="mb-0">Finish</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h2>{{ $prepressData['stats']['in_progress'] }}</h2>
                    <p class="mb-0">In Progress</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h2>{{ $prepressData['stats']['assigned'] }}</h2>
                    <p class="mb-0">Assigned</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h2>{{ $prepressData['stats']['approved'] }}</h2>
                    <p class="mb-0">Approved</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h2>{{ $prepressData['stats']['urgent'] }}</h2>
                    <p class="mb-0">Urgent</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Department Breakdown --}}
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Marketing Jobs ({{ count($prepressData['departments']['marketing']['jobs']) }})</h4>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Job Order</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prepressData['departments']['marketing']['jobs'] as $job)
                                <tr>
                                    <td>{{ $job['nomor_job_order'] }}</td>
                                    <td>{{ Str::limit($job['customer'], 20) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $job['status'] == 'finish' ? 'success' : ($job['status'] == 'in_progress' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($job['status']) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($job['priority'] && $job['priority'] <= 3)
                                            <span class="badge badge-danger">Urgent</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Tidak ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Other Jobs ({{ count($prepressData['departments']['others']['jobs']) }})</h4>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Job Order</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prepressData['departments']['others']['jobs'] as $job)
                                <tr>
                                    <td>{{ $job['nomor_job_order'] }}</td>
                                    <td>{{ Str::limit($job['customer'], 20) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $job['status'] == 'finish' ? 'success' : ($job['status'] == 'in_progress' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($job['status']) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($job['priority'] && $job['priority'] <= 3)
                                            <span class="badge badge-danger">Urgent</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Tidak ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        </div>
    </div>
@endsection

