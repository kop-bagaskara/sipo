@extends('main.layouts.main')

@section('title', 'Detail Pengaturan Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="mdi mdi-eye mr-2"></i>
                        Detail Pengaturan Notifikasi
                    </h4>
                    <div class="card-tools">
                        <a href="{{ route('notification-settings.index') }}" class="btn btn-secondary btn-sm">
                            <i class="mdi mdi-arrow-left mr-1"></i>
                            Kembali
                        </a>
                        <a href="{{ route('notification-settings.edit', $setting->id) }}" class="btn btn-warning btn-sm ml-2">
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
                                    <td width="30%"><strong>Tipe Notifikasi</strong></td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ ucwords(str_replace('_', ' ', $setting->notification_type)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Target Type</strong></td>
                                    <td>
                                        @switch($setting->target_type)
                                            @case('divisi')
                                                <span class="badge badge-primary">Divisi</span>
                                                @break
                                            @case('jabatan')
                                                <span class="badge badge-success">Jabatan</span>
                                                @break
                                            @case('specific_user')
                                                <span class="badge badge-warning">User Spesifik</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Target Value</strong></td>
                                    <td>
                                        @if($setting->target_type === 'specific_user')
                                            @php
                                                $user = $users->firstWhere('id', $setting->target_value);
                                            @endphp
                                            @if($user)
                                                <strong>{{ $user->name }}</strong><br>
                                                <small class="text-muted">{{ $user->divisi }} - {{ $user->jabatan }}</small>
                                            @else
                                                <span class="text-danger">User tidak ditemukan</span>
                                            @endif
                                        @else
                                            <strong>{{ $setting->target_value }}</strong>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>
                                        @if($setting->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-secondary">Tidak Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Email</strong></td>
                                    <td>
                                        @if($setting->send_email)
                                            <span class="badge badge-success">
                                                <i class="mdi mdi-check"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="mdi mdi-close"></i> Tidak Aktif
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Website</strong></td>
                                    <td>
                                        @if($setting->send_website)
                                            <span class="badge badge-success">
                                                <i class="mdi mdi-check"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="mdi mdi-close"></i> Tidak Aktif
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat</strong></td>
                                    <td>{{ $setting->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Terakhir Update</strong></td>
                                    <td>{{ $setting->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($setting->description)
                        <div class="row mt-3">
                            <div class="col-12">
                                <h6><strong>Deskripsi:</strong></h6>
                                <p class="text-muted">{{ $setting->description }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="mdi mdi-information mr-2"></i>
                                <strong>Informasi:</strong> Pengaturan ini akan mengirim notifikasi ke 
                                @if($setting->target_type === 'divisi')
                                    semua user dengan divisi <strong>{{ $setting->target_value }}</strong>
                                @elseif($setting->target_type === 'jabatan')
                                    semua user dengan jabatan <strong>{{ $setting->target_value }}</strong>
                                @else
                                    user spesifik <strong>{{ $users->firstWhere('id', $setting->target_value)->name ?? 'N/A' }}</strong>
                                @endif
                                saat ada {{ ucwords(str_replace('_', ' ', $setting->notification_type)) }} baru.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
