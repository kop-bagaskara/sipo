@extends('main.layouts.main')

@section('content')
<div class="row page-titles">
    <div class="col-md-6 col-12">
        <h3 class="text-themecolor">Log Viewer PKB</h3>
        <p class="text-muted m-b-0">Pantau siapa yang membaca PKB dan durasinya</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">Aktivitas Terbaru</h4>
        <div class="table-responsive m-t-20">
            <table class="table table-bordered table-hover">
                <thead class="bg-light" >
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Divisi</th>
                        <th>Jabatan</th>
                        <th style="text-wrap: nowrap;">Hal. Awal</th>
                        <th style="text-wrap: nowrap;">Hal. Terakhir</th>
                        <th style="text-wrap: nowrap;">Total Hal. Dibaca</th>
                        <th style="text-wrap: nowrap;">Durasi (detik)</th>
                        <th style="text-wrap: nowrap;">Status</th>
                        <th style="text-wrap: nowrap;">Mulai</th>
                        <th style="text-wrap: nowrap;">Terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $idx => $log)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>
                                <div class="font-medium">{{ $log->user->name ?? 'Unknown' }}</div>
                                @php
                                    $divisiName = $log->user->divisiUser->divisi ?? null;
                                    $jabatanName = $log->user->jabatanUser->jabatan ?? null;
                                @endphp
                                {{-- <div class="text-muted small">{{ $divisiName ?? '-' }}</div> --}}
                                {{-- <div class="text-muted small">{{ $jabatanName ?? '-' }}</div> --}}
                                <div class="text-muted small">{{ $log->user->email ?? '' }}</div>
                            </td>
                            <td>{{ $divisiName ?? '-' }}</td>
                            <td>{{ $jabatanName ?? '-' }}</td>
                            <td>{{ $log->start_page }}</td>
                            <td>{{ $log->last_page_viewed }}</td>
                            <td>{{ $log->total_pages_viewed }}</td>
                            <td>{{ $log->time_spent_seconds ?? 0 }}</td>
                            @php
                                $isComplete = $log->marked_as_complete || ($log->last_page_viewed >= 46 && $log->session_end_at);
                                $isActiveWindow = $log->session_end_at && $log->session_end_at >= now()->subHours(6);
                            @endphp
                            <td>
                                @if ($isComplete)
                                    <span class="badge badge-success">Selesai</span>
                                @elseif ($isActiveWindow)
                                    <span class="badge badge-warning">Aktif / Belum selesai</span>
                                @else
                                    <span class="badge badge-danger">Idle >6 jam</span>
                                @endif
                            </td>
                            <td style="text-wrap: nowrap;">{{ optional($log->session_start_at)->format('d M Y H:i:s') }}</td>
                            <td style="text-wrap: nowrap;">{{ optional($log->session_end_at)->format('d M Y H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Belum ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="text-muted small m-t-10">Menampilkan {{ $logs->count() }} sesi terbaru (dibatasi 100).</p>
    </div>
</div>
@endsection

