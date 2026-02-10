@extends('layouts.app')

@section('title', 'Detail System Log')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-list"></i> Detail System Log #{{ $log->id }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.system-logs.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Informasi Dasar</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>ID Log:</strong></td>
                                            <td>{{ $log->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal:</strong></td>
                                            <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Log Type:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $log->log_type_badge_color }}">
                                                    {{ ucfirst($log->log_type) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Action Type:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $log->action_type_badge_color }}">
                                                    {{ ucfirst($log->action_type) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Table Name:</strong></td>
                                            <td><code>{{ $log->table_name }}</code></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Record ID:</strong></td>
                                            <td>{{ $log->record_id }}</td>
                                        </tr>
                                        @if($log->record_identifier)
                                        <tr>
                                            <td><strong>Record Identifier:</strong></td>
                                            <td><strong>{{ $log->record_identifier }}</strong></td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- User Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Informasi User</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>User:</strong></td>
                                            <td>
                                                @if($log->user_name)
                                                    <strong>{{ $log->user_name }}</strong>
                                                    @if($log->user_jabatan)
                                                        <br><small class="text-muted">{{ $log->user_jabatan }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">System</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>IP Address:</strong></td>
                                            <td>{{ $log->ip_address ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>User Agent:</strong></td>
                                            <td>
                                                @if($log->user_agent)
                                                    <small>{{ Str::limit($log->user_agent, 100) }}</small>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Deskripsi</h5>
                                </div>
                                <div class="card-body">
                                    <p>{{ $log->description }}</p>
                                    @if($log->changed_fields)
                                        <div class="alert alert-info">
                                            <strong>Field yang berubah:</strong> {{ $log->changed_fields }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Changes -->
                    @if($log->old_data || $log->new_data)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Perubahan Data</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @if($log->old_data)
                                        <div class="col-md-6">
                                            <h6 class="text-danger">
                                                <i class="fas fa-minus-circle"></i> Data Sebelum
                                            </h6>
                                            <pre class="bg-light p-3 rounded"><code>{{ json_encode($log->old_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                        </div>
                                        @endif

                                        @if($log->new_data)
                                        <div class="col-md-6">
                                            <h6 class="text-success">
                                                <i class="fas fa-plus-circle"></i> Data Sesudah
                                            </h6>
                                            <pre class="bg-light p-3 rounded"><code>{{ json_encode($log->new_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
