@extends('main.layouts.main')
@section('title')
    Data Plan
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
            max-width: 20%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }
    </style>
@endsection
@section('page-title')
    Data Plan
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Pengajuan Pembelian Kertas</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Pengajuan Pembelian Kertas</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">List Pengajuan Pembelian Kertas</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('paper-procurement.index') }}">Pengajuan Pembelian
                                    Kertas</a></li>
                            <li class="breadcrumb-item active">Daftar Pengajuan</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Header dengan tombol tambah -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h5 class="card-title mb-0">Daftar Pengajuan Pembelian Kertas</h5>
                            </div>
                            <div class="col-md-6 text-end">
                                <a href="{{ route('paper-procurement.index') }}" class="btn btn-primary">
                                    <i class="mdi mdi-plus me-1"></i> Buat Pengajuan Baru
                                </a>
                            </div>
                        </div>

                        <!-- Filter dan Search -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <select class="form-select" id="filter-status">
                                    <option value="">Semua Status</option>
                                    <option value="Draft">Draft</option>
                                    <option value="Pending Approval">Pending Approval</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Rejected">Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filter-customer">
                                    <option value="">Semua Customer Group</option>
                                    <option value="TSPM">TSPM</option>
                                    <option value="UNILEVER">UNILEVER</option>
                                    <option value="NABATI">NABATI</option>
                                    <option value="OTHERS">OTHERS</option>
                                    <option value="VDR">VDR</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="search-input"
                                    placeholder="Cari nomor pengajuan, customer, atau bulan...">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                    <i class="mdi mdi-refresh"></i> Reset
                                </button>
                            </div>
                        </div>

                        <!-- Tabel Data -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="paper-procurement-table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Pengajuan</th>
                                        <th>Bulan Meeting</th>
                                        <th>Customer Group</th>
                                        <th>Periode</th>
                                        <th>Status</th>
                                        <th>Dibuat Oleh</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($paperProcurements as $index => $procurement)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong class="text-primary">{{ $procurement['request_number'] }}</strong>
                                            </td>
                                            <td>{{ $procurement['meeting_month'] }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $procurement['customer_group'] }}</span>
                                            </td>
                                            <td>{{ $procurement['period'] }}</td>
                                            <td>
                                                @if ($procurement['status'] == 'Draft')
                                                    <span class="badge bg-warning">{{ $procurement['status'] }}</span>
                                                @elseif($procurement['status'] == 'Pending Approval')
                                                    <span class="badge bg-primary">{{ $procurement['status'] }}</span>
                                                @elseif($procurement['status'] == 'Approved')
                                                    <span class="badge bg-success">{{ $procurement['status'] }}</span>
                                                @elseif($procurement['status'] == 'Rejected')
                                                    <span class="badge bg-danger">{{ $procurement['status'] }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $procurement['status'] }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $procurement['created_by'] }}</td>
                                            <td>{{ \Carbon\Carbon::parse($procurement['created_at'])->format('d/m/Y H:i') }}
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                        onclick="viewProcurement({{ $procurement['id'] }})"
                                                        title="Lihat Detail">
                                                        <i class="mdi mdi-eye"></i>
                                                    </button>
                                                    @if ($procurement['status'] == 'Draft')
                                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                            onclick="editProcurement({{ $procurement['id'] }})"
                                                            title="Edit">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </button>
                                                    @endif
                                                    @if ($procurement['status'] == 'Pending Approval')
                                                        <button type="button" class="btn btn-sm btn-outline-success"
                                                            onclick="approveProcurement({{ $procurement['id'] }})"
                                                            title="Approve">
                                                            <i class="mdi mdi-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="rejectProcurement({{ $procurement['id'] }})"
                                                            title="Reject">
                                                            <i class="mdi mdi-close"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination (jika diperlukan) -->
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p class="text-muted">Menampilkan 1-{{ count($paperProcurements) }} dari
                                    {{ count($paperProcurements) }} data</p>
                            </div>
                            <div class="col-md-6">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination pagination-sm justify-content-end">
                                        <li class="page-item disabled">
                                            <span class="page-link">Previous</span>
                                        </li>
                                        <li class="page-item active">
                                            <span class="page-link">1</span>
                                        </li>
                                        <li class="page-item disabled">
                                            <span class="page-link">Next</span>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

         @section('scripts')
             <script>
                 // Action functions - defined globally
                 function viewProcurement(id) {
                     window.location.href = '{{ route("paper-procurement.show", ["id" => "ID_PLACEHOLDER"]) }}';
                    //  window.location.href = '{{ url("paper-procurement") }}/' + id;
                 }

                 function editProcurement(id) {
                     alert('Edit procurement ID: ' + id);
                     // Implement edit functionality
                 }

                 function approveProcurement(id) {
                     if (confirm('Apakah Anda yakin ingin menyetujui pengajuan ini?')) {
                         alert('Procurement ID ' + id + ' approved!');
                         // Implement approve functionality
                     }
                 }

                 function rejectProcurement(id) {
                     if (confirm('Apakah Anda yakin ingin menolak pengajuan ini?')) {
                         alert('Procurement ID ' + id + ' rejected!');
                         // Implement reject functionality
                     }
                 }

                 function clearFilters() {
                     $('#filter-status, #filter-customer').val('');
                     $('#search-input').val('');
                     $('#paper-procurement-table tbody tr').show();
                 }

                 function filterTable() {
                     const statusFilter = $('#filter-status').val().toLowerCase();
                     const customerFilter = $('#filter-customer').val().toLowerCase();
                     const searchTerm = $('#search-input').val().toLowerCase();

                     $('#paper-procurement-table tbody tr').each(function() {
                         const row = $(this);
                         const status = row.find('td:eq(5)').text().toLowerCase();
                         const customer = row.find('td:eq(3)').text().toLowerCase();
                         const searchText = row.text().toLowerCase();

                         const statusMatch = !statusFilter || status.includes(statusFilter);
                         const customerMatch = !customerFilter || customer.includes(customerFilter);
                         const searchMatch = !searchTerm || searchText.includes(searchTerm);

                         if (statusMatch && customerMatch && searchMatch) {
                             row.show();
                         } else {
                             row.hide();
                         }
                     });
                 }

                 $(document).ready(function() {
                     // Filter functionality
                     $('#filter-status, #filter-customer').on('change', filterTable);
                     $('#search-input').on('keyup', filterTable);
                 });
             </script>
         @endsection
    @endsection
