@extends('main.layouts.main')
@section('title')
    Jadwal Training
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
    Jadwal Training
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Jadwal Training</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">HR</a></li>
                    <li class="breadcrumb-item active">Jadwal Training</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-plus mr-2"></i>
                            Buat Jadwal Training
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('hr.training.schedule.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i>
                                Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('hr.training.schedule.store') }}">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="training_id">Training <span class="text-danger">*</span></label>
                                        <select name="training_id" id="training_id"
                                            class="form-control @error('training_id') is-invalid @enderror" required>
                                            <option value="">Pilih Training</option>
                                            @foreach ($trainings as $training)
                                                <option value="{{ $training->id }}"
                                                    {{ old('training_id', $training->id ?? '') == $training->id ? 'selected' : '' }}>
                                                    {{ $training->training_name }} ({{ $training->training_code }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('training_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="schedule_date">Tanggal Training <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="schedule_date" id="schedule_date"
                                            class="form-control @error('schedule_date') is-invalid @enderror"
                                            value="{{ old('schedule_date') }}" min="{{ date('Y-m-d') }}" required>
                                        @error('schedule_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_time">Waktu Mulai <span class="text-danger">*</span></label>
                                        <input type="time" name="start_time" id="start_time"
                                            class="form-control @error('start_time') is-invalid @enderror"
                                            value="{{ old('start_time') }}" required>
                                        @error('start_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_time">Waktu Selesai <span class="text-danger">*</span></label>
                                        <input type="time" name="end_time" id="end_time"
                                            class="form-control @error('end_time') is-invalid @enderror"
                                            value="{{ old('end_time') }}" required>
                                        @error('end_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="location">Lokasi</label>
                                        <input type="text" name="location" id="location"
                                            class="form-control @error('location') is-invalid @enderror"
                                            value="{{ old('location') }}"
                                            placeholder="Contoh: Ruang Meeting A, Zoom Meeting">
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="scheduled" selected>Terjadwal</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="description">Deskripsi/Keterangan</label>
                                        <textarea name="description" id="description" rows="4"
                                            class="form-control @error('description') is-invalid @enderror"
                                            placeholder="Tambahkan deskripsi atau keterangan tambahan untuk jadwal training ini...">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i>
                                            Simpan Jadwal
                                        </button>
                                        <a href="{{ route('hr.training.schedule.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times mr-1"></i>
                                            Batal
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        <script>
            // Auto-fill end time when start time changes
            document.getElementById('start_time').addEventListener('change', function() {
                const startTime = this.value;
                if (startTime) {
                    const [hours, minutes] = startTime.split(':');
                    const endHours = parseInt(hours) + 2; // Default 2 hours duration
                    const endTime = endHours.toString().padStart(2, '0') + ':' + minutes;
                    document.getElementById('end_time').value = endTime;
                }
            });

            // Validate end time is after start time
            document.getElementById('end_time').addEventListener('change', function() {
                const startTime = document.getElementById('start_time').value;
                const endTime = this.value;

                if (startTime && endTime && endTime <= startTime) {
                    alert('Waktu selesai harus setelah waktu mulai');
                    this.value = '';
                }
            });
        </script>
    @endsection
