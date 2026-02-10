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
                            <i class="mdi mdi-calendar-alt mr-2"></i>
                            Jadwal Training
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('hr.training.schedule.create') }}" class="btn btn-primary btn-sm">
                                <i class="mdi mdi-plus mr-1"></i>
                                Buat Jadwal
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Filter Form -->
                        <form method="GET" action="{{ route('hr.training.schedule.index') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="training_id">Training</label>
                                        <select name="training_id" id="training_id" class="form-control">
                                            <option value="">Semua Training</option>
                                            @foreach ($trainings as $training)
                                                <option value="{{ $training->id }}"
                                                    {{ request('training_id') == $training->id ? 'selected' : '' }}>
                                                    {{ $training->training_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="">Semua Status</option>
                                            <option value="scheduled"
                                                {{ request('status') == 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                                            <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>
                                                Berlangsung</option>
                                            <option value="completed"
                                                {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                                            <option value="cancelled"
                                                {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="date_from">Dari Tanggal</label>
                                        <input type="date" name="date_from" id="date_from" class="form-control"
                                            value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="date_to">Sampai Tanggal</label>
                                        <input type="date" name="date_to" id="date_to" class="form-control"
                                            value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-info">
                                                <i class="mdi mdi-filter mr-1"></i>Filter
                                            </button>
                                            <a href="{{ route('hr.training.schedule.index') }}" class="btn btn-secondary">
                                                <i class="mdi mdi-times mr-1"></i>Reset
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Schedules Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">Training</th>
                                        <th width="12%">Tanggal</th>
                                        <th width="15%">Waktu</th>
                                        <th width="15%">Lokasi</th>
                                        <th width="10%">Status</th>
                                        <th width="15%">Dibuat Oleh</th>
                                        <th width="8%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($schedules as $index => $schedule)
                                        <tr>
                                            <td>{{ $schedules->firstItem() + $index }}</td>
                                            <td>
                                                <strong>{{ $schedule->training->training_name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $schedule->training->training_code }}</small>
                                            </td>
                                            <td>{{ $schedule->formatted_date }}</td>
                                            <td>
                                                {{ $schedule->start_time }} - {{ $schedule->end_time }}
                                            </td>
                                            <td>{{ $schedule->location ?? '-' }}</td>
                                            <td>
                                                @switch($schedule->status)
                                                    @case('scheduled')
                                                        <span class="badge badge-warning">Terjadwal</span>
                                                    @break

                                                    @case('ongoing')
                                                        <span class="badge badge-info">Berlangsung</span>
                                                    @break

                                                    @case('completed')
                                                        <span class="badge badge-success">Selesai</span>
                                                    @break

                                                    @case('cancelled')
                                                        <span class="badge badge-danger">Dibatalkan</span>
                                                    @break
                                                @endswitch
                                            </td>
                                            <td>{{ $schedule->creator->name ?? 'N/A' }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('hr.training.schedule.show', $schedule->id) }}"
                                                        class="btn btn-sm btn-info" title="Lihat Detail">
                                                        <i class="mdi mdi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('hr.training.schedule.edit', $schedule->id) }}"
                                                        class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="mdi mdi-pencil"></i>
                                                    </a>
                                                    @if ($schedule->status !== 'ongoing')
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="deleteSchedule({{ $schedule->id }})" title="Hapus">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">
                                                    <i class="mdi mdi-calendar-times me-2"></i>
                                                    Belum ada jadwal training
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if ($schedules->hasPages())
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $schedules->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>


            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            Apakah Anda yakin ingin menghapus jadwal training ini?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <form id="deleteForm" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="mdi mdi-trash-can"></i>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endsection

        @section('scripts')
            <script>
                function deleteSchedule(id) {
                    $('#deleteForm').attr('action', '{{ route('hr.training.schedule.destroy', ':id') }}'.replace(':id', id));
                    $('#deleteModal').modal('show');
                }
            </script>
        @endsection
