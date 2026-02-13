@extends('main.layouts.main')
@section('title')
    Tambah Setting Navigasi Menu
@endsection
@section('page-title')
    Tambah Setting Navigasi Menu
@endsection
@section('body')
    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Tambah Setting Navigasi Menu</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('master.menu-navigation-settings.index') }}">Master Setting Navigasi Menu</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('master.menu-navigation-settings.store') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="menu_key">Menu Key <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('menu_key') is-invalid @enderror" id="menu_key" name="menu_key" value="{{ old('menu_key') }}" required>
                                <small class="form-text text-muted">Unique identifier untuk menu (misal: dashboard, hr.portal-training)</small>
                                @error('menu_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="menu_name">Nama Menu <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('menu_name') is-invalid @enderror" id="menu_name" name="menu_name" value="{{ old('menu_name') }}" required>
                                @error('menu_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="menu_icon">Icon Menu</label>
                                <input type="text" class="form-control @error('menu_icon') is-invalid @enderror" id="menu_icon" name="menu_icon" value="{{ old('menu_icon') }}" placeholder="mdi mdi-home">
                                <small class="form-text text-muted">Icon Material Design (misal: mdi mdi-home)</small>
                                @error('menu_icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="menu_route">Route Name</label>
                                <input type="text" class="form-control @error('menu_route') is-invalid @enderror" id="menu_route" name="menu_route" value="{{ old('menu_route') }}" placeholder="dashboard">
                                <small class="form-text text-muted">Route name untuk menu (opsional)</small>
                                @error('menu_route')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Divisi Diizinkan</label>
                                        <select class="form-control select2" id="allowed_divisi" name="allowed_divisi[]" multiple>
                                            @foreach($divisis as $divisi)
                                                <option value="{{ $divisi->id }}">{{ $divisi->divisi }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Kosongkan jika semua divisi diizinkan</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jabatan Diizinkan</label>
                                        <select class="form-control select2" id="allowed_jabatan" name="allowed_jabatan[]" multiple>
                                            @foreach($jabatans as $jabatan)
                                                <option value="{{ $jabatan->id }}">{{ $jabatan->jabatan }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Kosongkan jika semua jabatan diizinkan</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Divisi Dikecualikan</label>
                                        <select class="form-control select2" id="excluded_divisi" name="excluded_divisi[]" multiple>
                                            @foreach($divisis as $divisi)
                                                <option value="{{ $divisi->id }}">{{ $divisi->divisi }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Divisi yang tidak diizinkan mengakses</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Jabatan Dikecualikan</label>
                                        <select class="form-control select2" id="excluded_jabatan" name="excluded_jabatan[]" multiple>
                                            @foreach($jabatans as $jabatan)
                                                <option value="{{ $jabatan->id }}">{{ $jabatan->jabatan }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Jabatan yang tidak diizinkan mengakses</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="display_order">Urutan Tampilan</label>
                                        <input type="number" class="form-control" id="display_order" name="display_order" value="{{ old('display_order', 0) }}" min="0">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="form-check mt-4">
                                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Aktif</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-content-save"></i> Simpan
                                </button>
                                <a href="{{ route('master.menu-navigation-settings.index') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-close"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    placeholder: 'Pilih...',
                    allowClear: true
                });
            });
        </script>
    @endsection

