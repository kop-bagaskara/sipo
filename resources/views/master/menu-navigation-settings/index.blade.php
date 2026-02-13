@extends('main.layouts.main')
@section('title')
    Master Setting Navigasi Menu
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
@endsection
@section('page-title')
    Master Setting Navigasi Menu
@endsection
@section('body')
    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Master Setting Navigasi Menu</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Master Setting Navigasi Menu</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title">Daftar Setting Menu</h4>
                            <a href="{{ route('master.menu-navigation-settings.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Tambah Setting Menu
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table id="menuSettingsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Menu Key</th>
                                        <th>Nama Menu</th>
                                        <th>Route</th>
                                        <th>Divisi Diizinkan</th>
                                        <th>Jabatan Diizinkan</th>
                                        <th>Status</th>
                                        <th>Urutan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($settings as $index => $setting)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><code>{{ $setting->menu_key }}</code></td>
                                            <td>
                                                @if($setting->menu_icon)
                                                    <i class="{{ $setting->menu_icon }}"></i>
                                                @endif
                                                {{ $setting->menu_name }}
                                            </td>
                                            <td><code>{{ $setting->menu_route ?? '-' }}</code></td>
                                            <td>
                                                @if($setting->allowed_divisi && count($setting->allowed_divisi) > 0)
                                                    @php
                                                        $divisiNames = \App\Models\Divisi::whereIn('id', $setting->allowed_divisi)->pluck('divisi')->toArray();
                                                    @endphp
                                                    {{ implode(', ', $divisiNames) }}
                                                @else
                                                    <span class="text-muted">Semua Divisi</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($setting->allowed_jabatan && count($setting->allowed_jabatan) > 0)
                                                    @php
                                                        $jabatanNames = \App\Models\Jabatan::whereIn('id', $setting->allowed_jabatan)->pluck('jabatan')->toArray();
                                                    @endphp
                                                    {{ implode(', ', $jabatanNames) }}
                                                @else
                                                    <span class="text-muted">Semua Jabatan</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($setting->is_active)
                                                    <span class="badge badge-success">Aktif</span>
                                                @else
                                                    <span class="badge badge-secondary">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td>{{ $setting->display_order }}</td>
                                            <td>
                                                <a href="{{ route('master.menu-navigation-settings.edit', $setting->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="mdi mdi-pencil"></i> Edit
                                                </a>
                                                <form action="{{ route('master.menu-navigation-settings.destroy', $setting->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus setting ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="mdi mdi-delete"></i> Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#menuSettingsTable').DataTable({
                    responsive: true,
                    order: [[7, 'asc']]
                });
            });
        </script>
    @endsection

