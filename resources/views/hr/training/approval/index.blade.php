@extends('main.layouts.main')

@section('title', 'Approval Training')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-check mr-2"></i>
                        Approval Training
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('hr.training.approval.statistics') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-chart-bar mr-1"></i>
                            Statistik
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('hr.training.approval.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="training_id">Training</label>
                                    <select name="training_id" id="training_id" class="form-control">
                                        <option value="">Semua Training</option>
                                        @foreach($trainings as $training)
                                            <option value="{{ $training->id }}" {{ request('training_id') == $training->id ? 'selected' : '' }}>
                                                {{ $training->training_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="employee_id">Karyawan</label>
                                    <select name="employee_id" id="employee_id" class="form-control">
                                        <option value="">Semua Karyawan</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="department_id">Departemen</label>
                                    <select name="department_id" id="department_id" class="form-control">
                                        <option value="">Semua Departemen</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->divisi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search">Pencarian</label>
                                    <input type="text" name="search" id="search" class="form-control"
                                           value="{{ request('search') }}" placeholder="Nama atau kode training">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search mr-1"></i>
                                    Filter
                                </button>
                                <a href="{{ route('hr.training.approval.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times mr-1"></i>
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Bulk Actions -->
                    @if($participants->count() > 0)
                        <form id="bulkForm" method="POST" action="{{ route('hr.training.approval.bulk-approve') }}" class="mb-3">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bulk_approval_notes">Catatan untuk Semua (Opsional)</label>
                                        <textarea name="approval_notes" id="bulk_approval_notes" class="form-control" rows="2"
                                                  placeholder="Catatan yang akan diterapkan untuk semua pendaftaran yang disetujui..."></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-success" id="bulkApproveBtn" disabled>
                                                <i class="fas fa-check-double mr-1"></i>
                                                Setujui Terpilih
                                            </button>
                                            <button type="button" class="btn btn-secondary" id="selectAllBtn">
                                                <i class="fas fa-check-square mr-1"></i>
                                                Pilih Semua
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @endif

                    <!-- Participants List -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Training</th>
                                    <th>Karyawan</th>
                                    <th>Departemen</th>
                                    <th>Tipe Pendaftaran</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Catatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($participants as $participant)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="participant_ids[]" value="{{ $participant->id }}"
                                                   class="form-check-input participant-checkbox">
                                        </td>
                                        <td>
                                            <strong>{{ $participant->training->training_name }}</strong>
                                            <br><small class="text-muted">{{ $participant->training->training_code }}</small>
                                            <br>
                                            @switch($participant->training->training_type)
                                                @case('mandatory')
                                                    <span class="badge badge-danger">Mandatory</span>
                                                    @break
                                                @case('optional')
                                                    <span class="badge badge-success">Optional</span>
                                                    @break
                                                @case('certification')
                                                    <span class="badge badge-warning">Certification</span>
                                                    @break
                                                @case('skill_development')
                                                    <span class="badge badge-primary">Skill Development</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            <strong>{{ $participant->employee->name }}</strong>
                                            <br><small class="text-muted">{{ $participant->employee->email }}</small>
                                            <br><small class="text-muted">{{ $participant->employee->jabatanUser->jabatan ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            {{ $participant->employee->divisiUser->divisi ?? 'N/A' }}
                                        </td>
                                        <td>
                                            @switch($participant->registration_type)
                                                @case('mandatory')
                                                    <span class="badge badge-danger">Wajib</span>
                                                    @break
                                                @case('voluntary')
                                                    <span class="badge badge-success">Sukarela</span>
                                                    @break
                                                @case('recommended')
                                                    <span class="badge badge-warning">Direkomendasikan</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            {{ $participant->registered_at ? $participant->registered_at->format('d/m/Y H:i') : '-' }}
                                        </td>
                                        <td>
                                            @if($participant->notes)
                                                <span class="text-muted">{{ strlen($participant->notes) > 50 ? substr($participant->notes, 0, 50) . '...' : $participant->notes }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('hr.training.approval.show', $participant->id) }}"
                                                   class="btn btn-sm btn-info" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-success"
                                                        title="Setujui"
                                                        onclick="approveParticipant({{ $participant->id }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                        title="Tolak"
                                                        onclick="rejectParticipant({{ $participant->id }})">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="py-4">
                                                <i class="fas fa-user-check fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">Tidak ada pendaftaran yang menunggu persetujuan.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <p class="text-muted mb-0">
                                Menampilkan {{ $participants->firstItem() ?? 0 }} - {{ $participants->lastItem() ?? 0 }}
                                dari {{ $participants->total() }} pendaftaran
                            </p>
                        </div>
                        <div>
                            {{ $participants->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Setujui Pendaftaran</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="approval_notes">Catatan (Opsional)</label>
                        <textarea name="approval_notes" id="approval_notes" class="form-control" rows="3"
                                  placeholder="Tambahkan catatan persetujuan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Pendaftaran</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3"
                                  placeholder="Berikan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form on filter change
    $('#training_id, #employee_id, #department_id').change(function() {
        $(this).closest('form').submit();
    });

    // Select all functionality
    $('#selectAll').change(function() {
        $('.participant-checkbox').prop('checked', this.checked);
        updateBulkButton();
    });

    $('.participant-checkbox').change(function() {
        updateBulkButton();
        updateSelectAll();
    });

    $('#selectAllBtn').click(function() {
        $('.participant-checkbox').prop('checked', true);
        $('#selectAll').prop('checked', true);
        updateBulkButton();
    });

    function updateBulkButton() {
        const checkedCount = $('.participant-checkbox:checked').length;
        $('#bulkApproveBtn').prop('disabled', checkedCount === 0);
        $('#bulkApproveBtn').text(`Setujui Terpilih (${checkedCount})`);
    }

    function updateSelectAll() {
        const totalCheckboxes = $('.participant-checkbox').length;
        const checkedCheckboxes = $('.participant-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
    }

    // Bulk form submission
    $('#bulkForm').submit(function(e) {
        const checkedIds = $('.participant-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (checkedIds.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu pendaftaran.');
            return false;
        }

        // Add checked IDs to form
        checkedIds.forEach(function(id) {
            $('<input>').attr({
                type: 'hidden',
                name: 'participant_ids[]',
                value: id
            }).appendTo('#bulkForm');
        });
    });
});

function approveParticipant(participantId) {
    $('#approveForm').attr('action', `/training/approval/${participantId}/approve`);
    $('#approveModal').modal('show');
}

function rejectParticipant(participantId) {
    $('#rejectForm').attr('action', `/training/approval/${participantId}/reject`);
    $('#rejectModal').modal('show');
}
</script>
@endpush
