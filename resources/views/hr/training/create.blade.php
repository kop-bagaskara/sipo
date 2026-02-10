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
                        <h3 class="card-title">
                            <i class="mdi mdi-plus mr-2"></i>
                            Tambah Training Baru
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('hr.training.index') }}" class="btn btn-secondary btn-sm">
                                <i class="mdi mdi-arrow-left mr-1"></i>
                                Kembali
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('hr.training.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            {{-- error  --}}
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="training_name">Nama Training <span class="text-danger">*</span></label>
                                        <input type="text" name="training_name" id="training_name"
                                            class="form-control @error('training_name') is-invalid @enderror"
                                            value="{{ old('training_name') }}" required>
                                        @error('training_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="training_type">Tipe Training <span class="text-danger">*</span></label>
                                        <select name="training_type" id="training_type"
                                            class="form-control @error('training_type') is-invalid @enderror" required>
                                            <option value="">Pilih Tipe Training</option>
                                            <option value="mandatory"
                                                {{ old('training_type') == 'mandatory' ? 'selected' : '' }}>Mandatory
                                            </option>
                                            <option value="optional"
                                                {{ old('training_type') == 'optional' ? 'selected' : '' }}>Optional</option>
                                            <option value="certification"
                                                {{ old('training_type') == 'certification' ? 'selected' : '' }}>
                                                Certification</option>
                                            <option value="skill_development"
                                                {{ old('training_type') == 'skill_development' ? 'selected' : '' }}>Skill
                                                Development</option>
                                        </select>
                                        @error('training_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="training_method">Metode Training <span
                                                class="text-danger">*</span></label>
                                        <select name="training_method" id="training_method"
                                            class="form-control @error('training_method') is-invalid @enderror" required>
                                            <option value="">Pilih Metode Training</option>
                                            <option value="classroom"
                                                {{ old('training_method') == 'classroom' ? 'selected' : '' }}>Kelas Tatap
                                                Muka</option>
                                            <option value="online"
                                                {{ old('training_method') == 'online' ? 'selected' : '' }}>Online</option>
                                            <option value="hybrid"
                                                {{ old('training_method') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                                            <option value="workshop"
                                                {{ old('training_method') == 'workshop' ? 'selected' : '' }}>Workshop
                                            </option>
                                            <option value="seminar"
                                                {{ old('training_method') == 'seminar' ? 'selected' : '' }}>Seminar
                                            </option>
                                        </select>
                                        @error('training_method')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Target Departments -->
                            <div class="form-group">
                                <label>Departemen Target</label>
                                <div class="row">
                                    @foreach ($departments as $department)
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input type="checkbox" name="target_departments[]"
                                                    value="{{ $department->id }}" class="form-check-input"
                                                    id="dept_{{ $department->id }}"
                                                    {{ in_array($department->id, old('target_departments', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="dept_{{ $department->id }}">
                                                    {{ $department->divisi }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="notes">Catatan Tambahan</label>
                                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-save mr-1"></i>
                                Simpan Training
                            </button>
                            <a href="{{ route('hr.training.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-times mr-1"></i>
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Form validation atau script lainnya jika diperlukan
            });
        </script>
    @endpush
