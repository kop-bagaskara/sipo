@extends('main.layouts.main')
@section('title')
    Edit Surat Perintah Lembur (SPL)
@endsection
@section('page-title')
    Edit Surat Perintah Lembur (SPL)
@endsection
@section('body')
    <body data-sidebar="colored">
@endsection
@section('content')
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h3 class="text-themecolor">Edit Surat Perintah Lembur (SPL)</h3>
        </div>
        <div class="col-md-7 align-self-center">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                <li class="breadcrumb-item"><a href="{{ route('hr.spl.index') }}">SPL</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary">
                    <h4 class="card-title mb-0"><i class="mdi mdi-pencil me-2"></i>Edit SPL: {{ $splRequest->spl_number }}</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <form action="{{ route('hr.spl.update', $splRequest->id) }}" method="POST" id="splEditForm">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="request_date" class="form-control @error('request_date') is-invalid @enderror"
                                       value="{{ old('request_date', $splRequest->request_date->format('Y-m-d')) }}" required>
                                @error('request_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Shift <span class="text-danger">*</span></label>
                                <select name="shift" class="form-select form-control @error('shift') is-invalid @enderror" required>
                                    <option value="">Pilih Shift</option>
                                    <option value="1" {{ old('shift', $splRequest->shift) == '1' ? 'selected' : '' }}>1 (Pagi)</option>
                                    <option value="2" {{ old('shift', $splRequest->shift) == '2' ? 'selected' : '' }}>2 (Siang)</option>
                                    <option value="3" {{ old('shift', $splRequest->shift) == '3' ? 'selected' : '' }}>3 (Malam)</option>
                                </select>
                                @error('shift')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mesin</label>
                                <input type="text" name="mesin" class="form-control @error('mesin') is-invalid @enderror"
                                       value="{{ old('mesin', $splRequest->mesin) }}" placeholder="Contoh: Mesin CNC-01">
                                @error('mesin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label">Keperluan <span class="text-danger">*</span></label>
                                <textarea name="keperluan" class="form-control @error('keperluan') is-invalid @enderror" rows="3" required>{{ old('keperluan', $splRequest->keperluan) }}</textarea>
                                @error('keperluan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary"><i class="mdi mdi-account-group me-2"></i>Daftar Karyawan Lembur</h5>
                                <hr>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:5%" class="text-center">No</th>
                                        <th style="width:35%">Nama</th>
                                        <th style="width:20%">NIP</th>
                                        <th style="width:20%">Jam Mulai</th>
                                        <th style="width:20%">Jam Selesai</th>
                                    </tr>
                                </thead>
                                <tbody id="employees-tbody">
                                    @foreach($splRequest->employees as $i => $emp)
                                        <tr>
                                            <td class="text-center">{{ $i + 1 }}</td>
                                            <td>
                                                <input type="text" name="employees[{{ $i }}][employee_name]" class="form-control form-control-sm"
                                                       value="{{ old('employees.'.$i.'.employee_name', $emp->employee_name) }}" required>
                                                <input type="hidden" name="employees[{{ $i }}][employee_id]" value="{{ old('employees.'.$i.'.employee_id', $emp->employee_id) }}">
                                                <input type="hidden" name="employees[{{ $i }}][is_manual]" value="{{ old('employees.'.$i.'.is_manual', $emp->is_manual ? 'true' : 'false') }}">
                                            </td>
                                            <td>
                                                <input type="text" name="employees[{{ $i }}][nip]" class="form-control form-control-sm"
                                                       value="{{ old('employees.'.$i.'.nip', $emp->nip) }}">
                                            </td>
                                            <td>
                                                <input type="time" name="employees[{{ $i }}][start_time]" class="form-control form-control-sm"
                                                       value="{{ old('employees.'.$i.'.start_time', $emp->start_time ? $emp->start_time->format('H:i') : '') }}" required>
                                            </td>
                                            <td>
                                                <input type="time" name="employees[{{ $i }}][end_time]" class="form-control form-control-sm"
                                                       value="{{ old('employees.'.$i.'.end_time', $emp->end_time ? $emp->end_time->format('H:i') : '') }}" required>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <a href="{{ route('hr.spl.show', $splRequest->id) }}" class="btn btn-secondary">
                                    <i class="mdi mdi-close"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-info">
                                    <i class="mdi mdi-content-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
