@extends('main.layouts.main')

@section('title', 'Detail Setting Email')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="mdi mdi-eye mr-2"></i>
                        Detail Setting Email Notification
                    </h4>
                    <div class="card-tools">
                        <a href="{{ route('email-notification-settings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="mdi mdi-arrow-left mr-1"></i>
                            Kembali
                        </a>
                        <a href="{{ route('email-notification-settings.edit', $setting->id) }}" class="btn btn-warning btn-sm">
                            <i class="mdi mdi-pencil mr-1"></i>
                            Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Nama Notifikasi:</strong></td>
                                    <td>{{ $setting->notification_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tipe Notifikasi:</strong></td>
                                    <td>
                                        <span class="badge badge-info">{{ $setting->notification_type }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Deskripsi:</strong></td>
                                    <td>{{ $setting->description ?? 'Tidak ada deskripsi' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($setting->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-secondary">Tidak Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat:</strong></td>
                                    <td>{{ $setting->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Terakhir Update:</strong></td>
                                    <td>{{ $setting->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h6 class="card-title mb-0 text-white">
                                        <i class="mdi mdi-account-group mr-2"></i>
                                        User yang Menerima Email
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @if($setting->users->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Nama</th>
                                                        <th>Divisi</th>
                                                        <th>Jabatan</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($setting->users as $index => $user)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $user->name }}</td>
                                                            <td>
                                                                <span class="badge badge-primary">{{ $user->divisi ?? 'N/A' }}</span>
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-info">{{ $user->jabatan ?? 'N/A' }}</span>
                                                            </td>
                                                            <td>
                                                                @if($user->pivot->is_active)
                                                                    <span class="badge badge-success">Aktif</span>
                                                                @else
                                                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="mt-3">
                                            <span class="badge badge-success">{{ $setting->users->count() }} user terdaftar</span>
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-3">
                                            <i class="mdi mdi-account-off mdi-48px"></i>
                                            <p class="mt-2">Belum ada user yang terdaftar</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
