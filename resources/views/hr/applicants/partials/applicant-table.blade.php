@php
    // Determine routes and URLs based on level
    if ($level === 'staff') {
        $indexUrl = url('sipo/hr/applicants');
        $createUrl = url('sipo/hr/applicants/create');
    } else {
        $indexUrl = url('sipo/hr/staff-applicants');
        $createUrl = url('sipo/hr/staff-applicants/create');
    }

    // Get statuses and positions from parent scope if not passed
    $statuses = $statuses ?? ['pending', 'test', 'interview', 'accepted', 'rejected'];
    $positions = $positions ?? \App\Models\Applicant::distinct()->pluck('posisi_dilamar')->filter();
@endphp

<!-- Filter Section -->
<div class="row mb-3">
    <div class="col-md-12">
        <form method="GET" action="{{ $indexUrl }}" class="form-inline">
            <div class="form-group mr-3">
                <label class="mr-2">Cari:</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nama, Email, atau Posisi">
            </div>
            <div class="form-group mr-3">
                <label class="mr-2">Status:</label>
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mr-3">
                <label class="mr-2">Posisi:</label>
                <select name="posisi" class="form-control">
                    <option value="">Semua Posisi</option>
                    @foreach($positions as $position)
                        <option value="{{ $position }}" {{ request('posisi') == $position ? 'selected' : '' }}>
                            {{ $position }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-info">
                <i class="mdi mdi-magnify"></i> Filter
            </button>
            <a href="{{ $indexUrl }}" class="btn btn-secondary ml-2">
                <i class="mdi mdi-refresh"></i> Reset
            </a>
            {{-- <a href="{{ $createUrl }}" class="btn btn-success ml-2">
                <i class="mdi mdi-plus"></i> Tambah Pelamar Baru
            </a> --}}
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>Posisi Dilamar</th>
                <th>Status</th>
                <th>Progress Test</th>
                <th>Tanggal Melamar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($applicants as $index => $applicant)
                <tr>
                    <td>{{ $applicants->firstItem() + $index }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div>
                                <strong>{{ $applicant->nama_lengkap }}</strong>
                                <br>
                                <small class="text-muted">{{ $applicant->no_telepon }}</small>
                            </div>
                        </div>
                    </td>
                    <td>{{ $applicant->email }}</td>
                    <td>{{ $applicant->posisi_dilamar }}</td>
                    <td>
                        @php
                            $statusColors = [
                                'pending' => 'secondary',
                                'test' => 'warning',
                                'interview' => 'info',
                                'accepted' => 'success',
                                'rejected' => 'danger'
                            ];
                        @endphp
                        <span class="badge badge-{{ $statusColors[$applicant->status] ?? 'secondary' }}">
                            {{ $applicant->status_formatted }}
                        </span>
                    </td>
                    <td>
                        @php
                            $completedTests = $applicant->testResults->count();
                            $percentage = ($completedTests / $totalTests) * 100;
                        @endphp
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%">
                                {{ $completedTests }}/{{ $totalTests }}
                            </div>
                        </div>
                    </td>
                    <td>{{ $applicant->tanggal_melamar->format('d/m/Y') }}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ $level === 'staff' ? url('sipo/hr/applicants/' . $applicant->id) : url('sipo/hr/staff-applicants/' . $applicant->id) }}"
                               class="btn btn-info btn-sm" title="Lihat Detail">
                                <i class="mdi mdi-eye"></i>
                            </a>
                            <a href="{{ $level === 'staff' ? url('sipo/hr/applicants/' . $applicant->id . '/edit') : url('sipo/hr/staff-applicants/' . $applicant->id . '/edit') }}"
                               class="btn btn-warning btn-sm" title="Edit">
                                <i class="mdi mdi-pencil"></i>
                            </a>
                            @if($completedTests < $totalTests)
                                <a href="{{ $level === 'staff' ? url('sipo/hr/' . $applicant->id . '/tests') : url('sipo/hr/staff-applicants/' . $applicant->id . '/tests') }}"
                                   class="btn btn-success btn-sm" title="Mulai Test">
                                    <i class="mdi mdi-play"></i> Test
                                </a>
                            @endif
                            <button type="button" class="btn btn-danger btn-sm"
                                    onclick="confirmDelete({{ $applicant->id }}, '{{ $level }}')" title="Hapus">
                                <i class="mdi mdi-delete"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">
                        <div class="alert alert-info">
                            <i class="mdi mdi-information"></i>
                            Tidak ada data pelamar
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
            Menampilkan {{ $applicants->firstItem() ?? 0 }} sampai {{ $applicants->lastItem() ?? 0 }}
            dari {{ $applicants->total() }} data
        </p>
    </div>
    <div>
        @if($applicants->hasPages())
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0">
                    {{-- Previous Page Link --}}
                    @if ($applicants->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link" aria-hidden="true">&laquo;</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $applicants->appends(request()->query())->previousPageUrl() }}" rel="prev">&laquo;</a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @php
                        $currentPage = $applicants->currentPage();
                        $lastPage = $applicants->lastPage();
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($lastPage, $currentPage + 2);
                    @endphp

                    @if($startPage > 1)
                        <li class="page-item">
                            <a class="page-link" href="{{ $applicants->appends(request()->query())->url(1) }}">1</a>
                        </li>
                        @if($startPage > 2)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                    @endif

                    @for($page = $startPage; $page <= $endPage; $page++)
                        @if ($page == $currentPage)
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $applicants->appends(request()->query())->url($page) }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endfor

                    @if($endPage < $lastPage)
                        @if($endPage < $lastPage - 1)
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        @endif
                        <li class="page-item">
                            <a class="page-link" href="{{ $applicants->appends(request()->query())->url($lastPage) }}">{{ $lastPage }}</a>
                        </li>
                    @endif

                    {{-- Next Page Link --}}
                    @if ($applicants->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $applicants->appends(request()->query())->nextPageUrl() }}" rel="next">&raquo;</a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link" aria-hidden="true">&raquo;</span>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif
    </div>
</div>

