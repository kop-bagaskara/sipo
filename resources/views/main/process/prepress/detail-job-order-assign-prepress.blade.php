@extends('main.layouts.main')
@section('title')
    Job Order Prepress
@endsection
@section('css')
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('sipo_krisan/public/new/plugins/datatables/select.bootstrap4.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">

    <style>
        /* Attachment List Styling */
        .attachment-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .attachment-list .border {
            border: 1px solid #dee2e6 !important;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .attachment-list .border:hover {
            border-color: #4299e1 !important;
            box-shadow: 0 2px 8px rgba(66, 153, 225, 0.15);
            transform: translateY(-1px);
        }

        .attachment-list .badge {
            font-size: 11px;
            padding: 4px 8px;
        }

        .attachment-list .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        /* Scrollbar styling untuk attachment list */
        .attachment-list::-webkit-scrollbar {
            width: 6px;
        }

        .attachment-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .attachment-list::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .attachment-list::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@endsection
@section('page-title')
    Job Order Prepress
@endsection
@section('body')

    <body data-sidebar="colored">
    @endsection
    @section('content')
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h3 class="text-themecolor">Detail Job</h3>
            </div>
            <div class="col-md-7 align-self-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                    <li class="breadcrumb-item active">Detail Job</li>
                </ol>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">


                <div class="card">
                    <div class="card-body">

                        {{-- hidden inputan --}}
                        <input type="hidden" name="role" id="role" value="{{ Auth::user()->jabatan }}">
                        <input type="hidden" name="nama_pelapor" id="nama_pelapor" value="{{ $jobOrder->created_by }}">
                        <input type="hidden" name="nama_userlogin" id="nama_userlogin" value="{{ Auth::user()->name }}">
                        <input type="hidden" name="flag_two_shift" id="flag_two_shift" value="{{ $jobOrder->two_shift }}">

                        <h5>Job : {{ $jobOrder->job_title }}</h5>
                        <p class="card-subtitle mb-4">Issued by : {{ $jobOrder->created_by }}</p>

                        <div class="row">
                            <div class="col-sm-3 mb-2 mb-sm-0">
                                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                                    aria-orientation="vertical">
                                    <a class="nav-link active show" id="v-pills-home-tab" data-toggle="pill"
                                        href="#v-pills-home" role="tab" aria-controls="v-pills-home"
                                        aria-selected="true">
                                        <i class="mdi mdi-home-variant d-lg-none d-block"></i>
                                        <span class="d-none d-lg-block">Detail Task</span>
                                    </a>
                                    <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile"
                                        role="tab" aria-controls="v-pills-profile" aria-selected="false">
                                        <i class="mdi mdi-account-circle d-lg-none d-block"></i>
                                        <span class="d-none d-lg-block">Pengisian Pengerjaan</span>
                                    </a>
                                    <a class="nav-link" id="v-pills-settings-tab" data-toggle="pill"
                                        href="#v-pills-settings" role="tab" aria-controls="v-pills-settings"
                                        aria-selected="false">
                                        <i class="mdi mdi-settings-outline d-lg-none d-block"></i>
                                        <span class="d-none d-lg-block">Other</span>
                                    </a>
                                </div>
                            </div> <!-- end col-->

                            <div class="col-sm-9">
                                <div class="tab-content" id="v-pills-tabContent">
                                    <div class="tab-pane fade active show" id="v-pills-home" role="tabpanel"
                                        aria-labelledby="v-pills-home-tab">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="tanggal">Tanggal</label>
                                                <input type="date" name="tanggal" class="form-control" required
                                                    value="{{ date('Y-m-d', strtotime($jobOrder->tanggal_job_order)) }}" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="job_deadline">Job Deadline</label>
                                                <input type="date" name="job_deadline" class="form-control" required
                                                    value="{{ date('Y-m-d', strtotime($jobOrder->tanggal_deadline)) }}" readonly>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <table class="table table-bordered mb-2">
                                                    <tr>
                                                        <td style="width:5%"><b>No. </b></td>
                                                        <td style="width:20%"><b>Customer</b></td>
                                                        <td><input type="text" name="customer" class="form-control"
                                                                required value="{{ $jobOrder->customer }}" readonly></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:5%"><b>1. </b></td>
                                                        <td style="width:20%"><b>Product</b></td>
                                                        <td><input type="text" name="product" class="form-control"
                                                                required value="{{ $jobOrder->product }}" readonly></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:5%"><b>2. </b></td>
                                                        <td style="width:20%"><b>Kode Design</b></td>
                                                        <td><input type="text" name="kode_design" class="form-control"
                                                                required value="{{ $jobOrder->kode_design }}" readonly>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:5%"><b>3. </b></td>
                                                        <td style="width:20%"><b>Dimension</b></td>
                                                        <td><input type="text" name="dimension" class="form-control"
                                                                required value="{{ $jobOrder->dimension }}" readonly></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:5%"><b>4. </b></td>
                                                        <td style="width:20%"><b>Material</b></td>
                                                        <td><input type="text" name="material" class="form-control"
                                                                required value="{{ $jobOrder->material }}" readonly></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:5%"><b>5. </b></td>
                                                        <td style="width:20%"><b>Total Color</b></td>
                                                        <td><input type="text" name="total_color" class="form-control"
                                                                required value="{{ $jobOrder->total_color }}" readonly>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2"></td>
                                                        <td>
                                                            @php
                                                                $colorDetails = is_array($jobOrder->total_color_details)
                                                                    ? $jobOrder->total_color_details
                                                                    : json_decode($jobOrder->total_color_details, true);
                                                            @endphp

                                                            <table class="table table-bordered mb-2"
                                                                style="background:#fff;">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    <tr>
                                                                        <td style="width:10%">{{ $i }}. </td>
                                                                        <td style="width:40%">
                                                                            <input type="text"
                                                                                name="color[{{ $i }}]"
                                                                                id="color{{ $i }}"
                                                                                class="form-control"
                                                                                value="{{ isset($colorDetails[$i - 1]) ? $colorDetails[$i - 1] : '' }}"
                                                                                readonly>
                                                                        </td>
                                                                        <td style="width:10%">{{ $i + 5 }}. </td>
                                                                        <td style="width:40%">
                                                                            <input type="text"
                                                                                name="color[{{ $i + 5 }}]"
                                                                                id="color{{ $i + 5 }}"
                                                                                class="form-control"
                                                                                value="{{ isset($colorDetails[$i + 4]) ? $colorDetails[$i + 4] : '' }}"
                                                                                readonly>
                                                                        </td>
                                                                    </tr>
                                                                @endfor
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:5%"><b>6. </b></td>
                                                        <td style="width:20%"><b>Qty Order Estimation</b></td>
                                                        <td><input type="text" name="qty_order_estimation"
                                                                class="form-control" required
                                                                value="{{ $jobOrder->qty_order_estimation }}" readonly>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:5%"><b>7. </b></td>
                                                        <td style="width:20%"><b>Job Order</b></td>
                                                        <td>
                                                            @php
                                                                $jobOrderValue = $jobOrder->job_order;
                                                            @endphp
                                                            <input type="text" name="job_order" class="form-control"
                                                                value="{{ $jobOrderValue }}" readonly>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:5%"><b>8. </b></td>
                                                        <td style="width:20%"><b>File atau Data</b></td>
                                                        <td>
                                                            @php
                                                                // Pastikan $jobOrder->file_data berupa array
                                                                $fileDataList = is_array($jobOrder->file_data)
                                                                    ? $jobOrder->file_data
                                                                    : json_decode($jobOrder->file_data, true);
                                                            @endphp
                                                            <table class="table table-bordered mb-2"
                                                                style="background:#fff;">
                                                                <tr>
                                                                    <td style="width:10%">
                                                                        <input type="checkbox" name="file_data[]"
                                                                            value="Contoh Cetak"
                                                                            {{ in_array('Contoh Cetak', $fileDataList ?? []) ? 'checked' : 'disabled' }}
                                                                            readonly>
                                                                            <label for="file_data[]">&nbsp;</label>
                                                                    </td>
                                                                    <td style="width:40%">Contoh Cetak</td>
                                                                    <td style="width:10%">
                                                                        <input type="checkbox" name="file_data[]"
                                                                            value="Contoh Produk"
                                                                            {{ in_array('Contoh Produk', $fileDataList ?? []) ? 'checked' : 'disabled' }}
                                                                            readonly>
                                                                            <label for="file_data[]">&nbsp;</label>
                                                                    </td>
                                                                    <td style="width:30%">Contoh Produk</td>
                                                                </tr>
                                                                <tr>
                                                                    <td style="width:10%">
                                                                        <input type="checkbox" name="file_data[]"
                                                                            value="File Softcopy"
                                                                            {{ in_array('File Softcopy', $fileDataList ?? []) ? 'checked' : 'disabled' }}
                                                                            readonly>
                                                                            <label for="file_data[]">&nbsp;</label>
                                                                    </td>
                                                                    <td style="width:40%">File Softcopy</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:5%"><b>9. </b></td>
                                                        <td style="width:20%"><b>Prioritas</b></td>
                                                        <td><input type="text" name="prioritas_job"
                                                                class="form-control" required
                                                                value="{{ $jobOrder->prioritas_job }}" readonly></td>
                                                    <tr>
                                                        <td style="width:5%"><b>10. </b></td>
                                                        <td style="width:20%"><b>Catatan</b></td>
                                                        <td>
                                                            <input type="text" name="catatan" class="form-control"
                                                                required value="{{ $jobOrder->catatan }}" readonly>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:5%"><b>11. </b></td>
                                                        <td style="width:20%"><b>Attachment</b></td>
                                                        <td>
                                                            @if ($jobOrder->attachmentJobOrder && $jobOrder->attachmentJobOrder->count() > 0)
                                                                <div class="attachment-list">
                                                                    @foreach ($jobOrder->attachmentJobOrder as $attachment)
                                                                        <div class="d-flex align-items-center justify-content-between mb-2 p-2 border rounded">
                                                                            <div class="d-flex align-items-center">
                                                                                <i class="mdi mdi-file-document-outline mr-2" style="font-size: 20px; color: #4299e1;"></i>
                                                                                <span class="font-weight-medium">{{ $attachment->file_name }}</span>
                                                                                @if ($attachment->file_type)
                                                                                    <span class="badge badge-secondary ml-2">{{ strtoupper($attachment->file_type) }}</span>
                                                                                @endif
                                                                            </div>
                                                                            <div class="d-flex">
                                                                                <a href="{{ asset('sipo_krisan/public/' . $attachment->file_path) }}"
                                                                                    class="btn btn-primary btn-sm mr-1"
                                                                                    title="View"
                                                                                    target="_blank">
                                                                                    <i class="mdi mdi-eye"></i> View
                                                                                </a>
                                                                                <a href="{{ asset('sipo_krisan/public/' . $attachment->file_path) }}"
                                                                                    class="btn btn-info btn-sm mr-1"
                                                                                    title="Download"
                                                                                    download>
                                                                                    <i class="mdi mdi-download"></i> Download
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <p class="text-muted mb-0"><i>Tidak ada attachment</i></p>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </table>
                                                <hr>
                                                <div class="row">
                                                    <div class="col" style="text-align: center;">
                                                        <span> <b>Issued by</b></span>
                                                        <br>
                                                        <br>
                                                        <br>
                                                        <span>{{ $jobOrder->created_by }}</span>
                                                        <br>
                                                        <span>{{ $jobOrder->created_at }}</span>
                                                    </div>
                                                    <div class="col" style="text-align: center;">
                                                        <span> <b>Received by</b></span>
                                                        <br>
                                                        <br>
                                                        <br>
                                                        <span>{{ $jobOrder->received_by }}</span>
                                                        <br>
                                                        <span>{{ $jobOrder->received_at }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-profile" role="tabpanel"
                                        aria-labelledby="v-pills-profile-tab">
                                        <div class="row">
                                            <div class="col">
                                                <h5>Form Pengisian Pengerjaan Job Order</h5>
                                                <input type="text" name="id_job_order" id="id_job_order"
                                                    class="form-control" value="{{ $jobOrder->id }}" hidden>

                                                @php
                                                    $status_job = $jobOrder->status_job;
                                                    if ($status_job == 'ASSIGNED') {
                                                        $status_job = 'ASSIGNED';
                                                    } elseif ($status_job == 'PLAN') {
                                                        $status_job = 'Plan';
                                                    } elseif ($status_job == 'ASSIGN') {
                                                        $status_job = 'IN PROGRESS';
                                                    } elseif ($status_job == 'DATA TERISI') {
                                                        $status_job = 'DATA TERISI';
                                                    } elseif ($status_job == 'FINISH') {
                                                        $status_job = 'FINISH';
                                                    } elseif ($status_job == 'APPROVED') {
                                                        $status_job = 'APPROVED';
                                                    } elseif ($status_job == 'CLOSED') {
                                                        $status_job = 'CLOSED';
                                                    } elseif ($status_job == 'SHIFT_2') {
                                                        $status_job = 'SHIFT_2';
                                                    }

                                                @endphp
                                                <input type="text" name="status_job" id="status_job"
                                                    class="form-control" value="{{ $status_job }}" hidden>
                                            </div>

                                            <div class="col d-flex justify-content-end">
                                                <h5>Status : &nbsp;</h5>
                                                @if ($jobOrder->status_job == 'OPEN')
                                                    <h5 class="text-primary">OPEN</h5>
                                                @elseif ($jobOrder->status_job == 'PLAN')
                                                    <h5 class="text-info">PLAN</h5>
                                                @elseif ($jobOrder->status_job == 'ASSIGNED')
                                                    <h5 class="text-warning">ASSIGNED</h5>
                                                @elseif ($jobOrder->status_job == 'IN PROGRESS')
                                                    <h5 class="text-success">IN PROGRESS</h5>
                                                @elseif ($jobOrder->status_job == 'APPROVED')
                                                    <h5 class="text-info">APPROVED</h5>
                                                @elseif ($jobOrder->status_job == 'REJECTED')
                                                    <h5 class="text-danger">REJECTED</h5>
                                                @elseif ($jobOrder->status_job == 'COMPLETED')
                                                    <h5 class="text-success">COMPLETED</h5>
                                                @elseif ($jobOrder->status_job == 'ASSIGN')
                                                    <h5 class="text-warning">ASSIGN</h5>
                                                @elseif ($jobOrder->status_job == 'DATA TERISI')
                                                    <h5 class="text-info">DATA TERISI</h5>
                                                @elseif ($jobOrder->status_job == 'FINISH')
                                                    <h5 class="text-success">FINISH</h5>
                                                @elseif ($jobOrder->status_job == 'SHIFT_2')
                                                    <h5 class="text-warning">SHIFT_2</h5>
                                                @endif

                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <label for="tanggal" class="col-sm-2">Job</label>
                                            <div class="col-sm-10">
                                                <p>: {{ $jobOrder->job_order }}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="tanggal" class="col-sm-2">Sub Unit</label>
                                            <div class="col-sm-10">
                                                <p>: {{ $jobOrder->sub_unit_job }}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="tanggal" class="col-sm-2">Item</label>
                                            <div class="col-sm-10">
                                                <p>: {{ $jobOrder->kode_design . ' - ' . $jobOrder->product }}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="tanggal" class="col-sm-2">Estimasi Job</label>
                                            <div class="col-sm-4">
                                                <p>: {{ $jobOrder->est_job_default }} Menit</p>
                                            </div>
                                        </div>
                                        <hr>
                                        <h5>Detail Waktu Pengerjaan</h5>
                                        <br>
                                        <div class="row">
                                            <label for="tanggal" class="col-sm-2 ">In Progress</label>


                                            {{-- Cek apakah two_shift bernilai 1 --}}
                                            @php
                                                $finish = null;
                                                if ($jobOrder->two_shift == '1') {
                                                    // Ambil data berdasarkan status 'FINISH' dan job_order_id
                                                    // Cek user pertama (SHIFT 1)
                                                    $shift1 = App\Models\HandlingJobPrepress::where('id_job_order', $jobOrder->id)
                                                        ->where('status_handling', 'IN PROGRESS')
                                                        ->orderBy('date_handling', 'asc') // Ambil yang paling terbaru
                                                        ->first();

                                                    if ($shift1) {
                                                        $inProgress1 = $shift1->date_handling;
                                                        $user1 = $shift1->name_user_handle;
                                                    }

                                                    // Ambil data berdasarkan status 'FINISH' dan job_order_id, user lain (SHIFT 2)
                                                    $shift2 = App\Models\HandlingJobPrepress::where('id_job_order', $jobOrder->id)
                                                        ->where('status_handling', 'IN PROGRESS')
                                                        ->where('name_user_handle', '!=', $user1) // Menghindari mengambil user yang sama
                                                        ->orderBy('date_handling', 'asc') // Ambil yang paling terbaru
                                                        ->first();

                                                    if ($shift2) {
                                                        $inProgress2 = $shift2->date_handling;
                                                        $user2 = $shift2->name_user_handle;
                                                    }
                                                } else {
                                                    // Jika tidak ada dua shift, cukup ambil satu
                                                    $inProgress = App\Models\HandlingJobPrepress::where('id_job_order', $jobOrder->id)
                                                        ->where('status_handling', 'IN PROGRESS')
                                                        ->first();
                                                }
                                            @endphp

                                            <div class="col">
                                                @if ($jobOrder->two_shift == '1')
                                                    {{-- SHIFT 1 --}}
                                                    @if (isset($inProgress1) && isset($user1))
                                                        <p>: {{ $inProgress1 }}, Oleh: {{ $user1 }} <span style="color: blue;">(SHIFT 1)</span></p>
                                                    @endif

                                                    {{-- SHIFT 2 --}}
                                                    @if (isset($inProgress2) && isset($user2))
                                                        <p>: {{ $inProgress2 }}, Oleh: {{ $user2 }} <span style="color: blue;">(SHIFT 2)</span></p>
                                                    @endif
                                                @else
                                                    {{-- Untuk single shift --}}
                                                    <p>: {{ $inProgress->date_handling ?? '' }}, Oleh:
                                                        {{ $inProgress->name_user_handle ?? '' }}</p>
                                                @endif
                                            </div>

                                        </div>
                                        <div class="row">
                                            <label for="tanggal" class="col-sm-2">Finished</label>

                                            {{-- Cek apakah two_shift bernilai 1 --}}
                                            @php
                                                $finish = null;
                                                if ($jobOrder->two_shift == '1') {
                                                    // Ambil data berdasarkan status 'FINISH' dan job_order_id
                                                    // Cek user pertama (SHIFT 1)
                                                    $shift1 = App\Models\HandlingJobPrepress::where('id_job_order', $jobOrder->id)
                                                        ->where('status_handling', 'FINISH')
                                                        ->orderBy('date_handling', 'desc')
                                                        ->first();

                                                    if ($shift1) {
                                                        $finish1 = $shift1->date_handling;
                                                        $user1 = $shift1->name_user_handle;
                                                    }

                                                    // Ambil data berdasarkan status 'FINISH' dan job_order_id, user lain (SHIFT 2)
                                                    $shift2 = App\Models\HandlingJobPrepress::where('id_job_order', $jobOrder->id)
                                                        ->where('status_handling', 'FINISH')
                                                        ->where('name_user_handle', '!=', $user1)
                                                        ->orderBy('date_handling', 'desc')
                                                        ->first();

                                                    if ($shift2) {
                                                        $finish2 = $shift2->date_handling;
                                                        $user2 = $shift2->name_user_handle;
                                                    }
                                                } else {
                                                    // Jika tidak ada dua shift, cukup ambil satu
                                                    $finish = App\Models\HandlingJobPrepress::where('id_job_order', $jobOrder->id)
                                                        ->where('status_handling', 'FINISH')
                                                        ->first();
                                                }
                                            @endphp

                                            <div class="col">
                                                @if ($jobOrder->two_shift == '1')
                                                    {{-- SHIFT 1 --}}
                                                    @if (isset($finish1) && isset($user1))
                                                        <p>: {{ $finish1 }}, Oleh: {{ $user1 }} <span style="color: blue;">(SHIFT 1)</span></p>
                                                    @endif

                                                    {{-- SHIFT 2 --}}
                                                    @if (isset($finish2) && isset($user2))
                                                        <p>: {{ $finish2 }}, Oleh: {{ $user2 }} <span style="color: blue;">(SHIFT 2)</span></p>
                                                    @endif
                                                @else
                                                    {{-- Untuk single shift --}}
                                                    <p>: {{ $finish->date_handling ?? '' }}, Oleh: {{ $finish->name_user_handle ?? '' }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label for="tanggal" class="col-sm-2 ">Approved</label>
                                            <div class="col">
                                                <p>: {{ $approved->date_handling ?? '' }} , Oleh :
                                                    {{ $approved->name_user_handle ?? '' }} </p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="tanggal" class="col-sm-2 ">Closed</label>
                                            <div class="col">
                                                <p>: {{ $closed->date_handling ?? '' }} , Oleh :
                                                    {{ $closed->name_user_handle ?? '' }} </p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="tanggal" class="col-sm-2 ">Paused</label>
                                            <div class="col">
                                                <p>: {{ $pending->date_handling ?? '' }} , Oleh :
                                                    {{ $pending->name_user_handle ?? '' }} , Alasan :
                                                    {{ $pending->reason ?? '' }} </p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <label for="tanggal" class="col-sm-2 ">Real Time </label>
                                            <div class="col">

                                                @php
                                                    $seconds = $jobOrder->est_job_realtime ?? 0;
                                                    if ($seconds == null) {
                                                        $seconds = $jobOrder->est_job_default;
                                                    }

                                                    $days = floor($seconds / 86400); // 86400 detik = 1 hari
                                                    $hours = floor(($seconds % 86400) / 3600);
                                                    $minutes = floor(($seconds % 3600) / 60);
                                                @endphp

                                                <p>: {{ $days }} Hari, {{ $hours }} Jam,
                                                    {{ $minutes }} Menit</p>

                                            </div>
                                        </div>
                                        <br>

                                        {{-- attachment --}}
                                        <div class="row">
                                            <label for="tanggal" class="col-sm-2 col-form-label">Attachment</label>
                                            <div class="col">
                                                <input type="file" class="form-control" id="customFile"
                                                    name="customFile" multiple disabled>
                                                {{-- file list --}}
                                                <br>
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="attachment-list">
                                                            @if ($jobOrder->attachmentJobOrder && $jobOrder->attachmentJobOrder->count() > 0)
                                                                @foreach ($jobOrder->attachmentJobOrder as $item)
                                                                    <div class="d-flex align-items-center justify-content-between mb-2 p-2 border rounded">
                                                                        <div class="d-flex align-items-center">
                                                                            <i class="mdi mdi-file-document-outline mr-2" style="font-size: 20px; color: #4299e1;"></i>
                                                                            <span class="font-weight-medium">{{ $item->file_name }}</span>
                                                                            @if ($item->file_type)
                                                                                <span class="badge badge-secondary ml-2">{{ strtoupper($item->file_type) }}</span>
                                                                            @endif
                                                                            @php
                                                                                // Cek ukuran file untuk menampilkan badge
                                                                                $filePath = public_path($item->file_path);
                                                                                if (file_exists($filePath)) {
                                                                                    $fileSize = filesize($filePath);
                                                                                    $fileSizeMB = round($fileSize / 1024 / 1024, 2);
                                                                                    if ($fileSizeMB >= 100) {
                                                                                        echo '<span class="badge badge-warning ml-2">Large File (' . $fileSizeMB . ' MB)</span>';
                                                                                    }
                                                                                }
                                                                            @endphp
                                                                        </div>
                                                                        <div class="d-flex">
                                                                            <a href="{{ asset('sipo_krisan/public/' . $item->file_path) }}"
                                                                                class="btn btn-primary btn-sm mr-1"
                                                                                title="View"
                                                                                target="_blank">
                                                                                <i class="mdi mdi-eye"></i> View
                                                                            </a>
                                                                            <a href="{{ asset('sipo_krisan/public/' . $item->file_path) }}"
                                                                                class="btn btn-info btn-sm mr-1"
                                                                                title="Download"
                                                                                download>
                                                                                <i class="mdi mdi-download"></i> Download
                                                                            </a>
                                                                            @if (Auth::user()->jabatan == '3' || Auth::user()->jabatan == '4' || $jobOrder->created_by == Auth::user()->name)
                                                                                <form action="{{ route('prepress.job-order.delete-attachment', $item->id) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    @method('POST')
                                                                                    <button type="submit"
                                                                                        class="btn btn-danger btn-sm"
                                                                                        title="Delete"
                                                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus file ini?')">
                                                                                        <i class="mdi mdi-delete"></i> Delete
                                                                                    </button>
                                                                                </form>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <p class="text-muted mb-0"><i>Tidak ada attachment</i></p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <br>

                                        @if ($jobOrder->two_shift == '1')
                                            <span style="color: red;">Informasi Shift 1</span>
                                            <div class="row">
                                                <label for="tanggal" class="col-sm-2 col-form-label">Setup</label>
                                                <div class="col">
                                                    @php
                                                        $est_job_setup = explode(',', $jobOrder->est_job_setup);
                                                    @endphp
                                                    <input type="text" class="form-control" id="waktu_setup_value_shift_1"
                                                        name="waktu_setup_value_shift_1" placeholder="Waktu Setup Satuan Menit"
                                                        value="{{ $est_job_setup[0] }}" disabled>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label for="tanggal" class="col-sm-2 col-form-label">Downtime</label>
                                                <div class="col">
                                                    @php
                                                        $est_job_downtime = explode(',', $jobOrder->est_job_downtime);
                                                    @endphp
                                                    <input type="text" class="form-control" id="waktu_downtime_value_shift_1"
                                                        name="waktu_downtime_value_shift_1" placeholder="Waktu Downtime Satuan Menit"
                                                        value="{{ $est_job_downtime[0] }}" disabled>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label for="tanggal" class="col-sm-2 col-form-label">Catatan</label>
                                                <div class="col">
                                                    @php
                                                        $catatan_job = explode(',', $jobOrder->catatan_job);
                                                    @endphp
                                                    <input type="text" class="form-control" id="catatan_value_shift_1"
                                                        name="catatan_value_shift_1" value="{{ $catatan_job[0] }}"
                                                        placeholder="Catatan" disabled>
                                                </div>
                                            </div>
                                            <br>
                                        @endif

                                        <div class="row">
                                            <label for="tanggal" class="col-sm-2 col-form-label">Setup</label>
                                            <div class="col">
                                                @if ($jobOrder->two_shift == '1')
                                                    <input type="text" class="form-control" id="waktu_setup_value_shift_2"
                                                        name="waktu_setup_value_shift_2" placeholder="Waktu Setup Satuan Menit"
                                                        value="{{ $est_job_setup[1] ?? '' }}" disabled>
                                                @else
                                                    <input type="text" class="form-control" id="waktu_setup_value"
                                                    name="waktu_setup_value" placeholder="Waktu Setup Satuan Menit"
                                                    value="{{ $jobOrder->est_job_setup }}" disabled>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label for="tanggal" class="col-sm-2 col-form-label">Downtime</label>
                                            <div class="col">
                                                @if ($jobOrder->two_shift == '1')
                                                    <input type="text" class="form-control" id="waktu_downtime_value_shift_2"
                                                        name="waktu_downtime_value_shift_2" placeholder="Waktu Downtime Satuan Menit"
                                                        value="{{ $est_job_downtime[1] ?? '' }}" disabled>
                                                @else
                                                    <input type="text" class="form-control" id="waktu_downtime_value"
                                                    name="waktu_downtime_value" placeholder="Waktu Downtime Satuan Menit"
                                                    value="{{ $jobOrder->est_job_downtime }}" disabled>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label for="tanggal" class="col-sm-2 col-form-label">Catatan</label>
                                            <div class="col">
                                                @if ($jobOrder->two_shift == '1')
                                                    @php
                                                        $catatan_job = explode(',', $jobOrder->catatan_job);
                                                    @endphp
                                                    <input type="text" class="form-control" id="catatan_value_shift_2"
                                                        name="catatan_value_shift_2" value="{{ $catatan_job[1] ?? '' }}" placeholder="Catatan">
                                                @else
                                                    <input type="text" class="form-control" id="catatan_value"
                                                        name="catatan_value" value="{{ $jobOrder->catatan_job }}"
                                                        placeholder="Catatan">
                                                @endif
                                            </div>
                                        </div>
                                        <p style="color: red;">*Catatan : Waktu dalam satuan menit</p>
                                        <br>

                                        {{-- Div BUTTON --}}
                                        <div class="form-group row">
                                            <div class="col">
                                                {{-- Button untuk menyimpan catatan kerja --}}
                                                <button type="button" class="btn btn-info w-100 mb-2"
                                                    id="submit-catatan-kerja">
                                                    <span id="status_catatan_job">SIMPAN CATATAN</span>
                                                </button>

                                                {{-- Button untuk IN PROGRESS --}}
                                                <button type="button" class="btn btn-success w-100 mb-2"
                                                    id="submit-progress-data">
                                                    <span id="status_job_button">IN PROGRESS</span>
                                                </button>

                                                {{-- Button untuk PAUSE dengan 2 opsi --}}
                                                <button type="button" class="btn btn-warning w-100 mb-2"
                                                    id="submit-pause-data" style="display: none;">
                                                    <span id="status_job_button_pause">PAUSE</span>
                                                </button>

                                                {{-- Button untuk RESUME --}}
                                                <button type="button" class="btn btn-primary w-100 mb-2"
                                                    id="submit-resume-data" style="display: none;">
                                                    <span id="status_job_button_resume">RESUME</span>
                                                </button>

                                                {{-- Button untuk FINISH --}}
                                                <button type="button" class="btn btn-success w-100 mb-2"
                                                    id="submit-finish-data" style="display: none;">
                                                    <span id="status_job_button_finish">FINISH</span>
                                                </button>

                                                {{-- Button untuk UPDATE (setelah FINISH) --}}
                                                <button type="button" class="btn btn-warning w-100 mb-2"
                                                    id="submit-edit-progress-data" style="display: none;">
                                                    <span id="status_job_button_edit">UPDATE</span>
                                                </button>

                                                <button type="button" class="btn btn-danger w-100 mb-2"
                                                    id="submit-reject-data" style="display: none;">
                                                    <span id="status_job_button_reject">REJECT</span>
                                                </button>

                                                {{-- dissapprove --}}
                                                <button type="button" class="btn btn-danger w-100 mb-2"
                                                    id="submit-dissapprove-data" style="display: none;">
                                                    <span id="status_job_button_dissapprove">DISSAPPROVE</span>
                                                </button>

                                                {{-- button close job order --}}
                                                <button type="button" class="btn btn-danger w-100 mb-2"
                                                    id="submit-close-job-order" style="display: none;">
                                                    <span id="status_job_button_close">CLOSE JOB ORDER</span>
                                                </button>


                                                <span id="information_job_order" style="color: red;"></span>
                                            </div>
                                        </div>

                                        {{-- Modal untuk PAUSE --}}
                                        <div class="modal fade" id="pauseModal" tabindex="-1" role="dialog"
                                            aria-labelledby="pauseModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="pauseModalLabel">Pilih Alasan PAUSE
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Alasan PAUSE:</label>
                                                            <select class="form-control" id="pause_reason">
                                                                <option value="">-- Pilih Alasan --</option>
                                                                <option value="shift_2">Lanjut Shift 2 dengan PIC berbeda
                                                                </option>
                                                                <option value="urgent_job">Mengerjakan Job Urgent</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group" id="shift_2_options">
                                                            <label>Reason Pause:</label>
                                                            <input type="text" class="form-control" id="reason_pause"
                                                                placeholder="Reason Pause">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Batal</button>
                                                        <button type="button" class="btn btn-warning"
                                                            id="confirm-pause">Konfirmasi PAUSE</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                    </div>
                                    <div class="tab-pane fade" id="v-pills-settings" role="tabpanel"
                                        aria-labelledby="v-pills-settings-tab">
                                        <p class="mb-0">Food truck quinoa dolor sit amet, consectetuer adipiscing elit.
                                            Aenean
                                            commodo ligula eget dolor. Aenean massa. Cum sociis
                                            natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec
                                            quam felis, ultricies nec, pellentesque
                                            eu, pretium quis, sem. Nulla consequat massa quis enim. Cillum ad ut irure
                                            tempor
                                            velit nostrud occaecat ullamco
                                            aliqua anim Leggings sint. Veniam sint duis incididunt do esse magna mollit
                                            excepteur laborum qui.</p>
                                    </div>
                                </div> <!-- end tab-content-->
                            </div> <!-- end col-->
                        </div>
                    </div>
                    <!-- end row-->
                </div>
            </div>
        </div>
    @endsection
    @section('scripts')
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.bootstrap4.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.flash.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/buttons.print.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.keyTable.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/dataTables.select.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/pdfmake.min.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/plugins/datatables/vfs_fonts.js') }}"></script>
        <script src="{{ asset('sipo_krisan/public/new/assets/pages/datatables-demo.js') }}"></script>


        <script>
            $(document).ready(function() {



                var submitJobPrepress = "{{ route('prepress.job-order.submit') }}";

                $('#submitJobPrepress').submit(function(e) {

                    // alert('test');

                    e.preventDefault();
                    var formData = $(this).serializeArray();
                    var csrfToken = $('#csrf_tokens').val();

                    $.ajax({
                        url: submitJobPrepress,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        data: formData,
                        type: "POST",
                        dataType: 'json',
                        success: function(response) {
                            console.log(response);
                            if (response.errors) {
                                $.each(response.errors, function(key, value) {
                                    $('#' + key).next('.error-message').text(value).show();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Job Order Prepress berhasil disubmit!',
                                    showConfirmButton: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href =
                                            "{{ route('prepress.job-order.data.index') }}";
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Server error atau response bukan JSON!',
                                showConfirmButton: true
                            });
                        },
                    });
                });

                $('body').on('click', '#submit-edit-progress-data', function() {
                    var formData = new FormData();
                    formData.append('status_job', $('#status_job').val());
                    formData.append('id_job_order', $('#id_job_order').val());
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                    $('#status_job').val('IN PROGRESS');
                    $('#status_job_button_edit').text('IN PROGRESS');
                    $('#submit-edit-progress-data').removeClass('btn-warning').addClass('btn-success');

                    $.ajax({
                        url: '{{ route('progress-data-prepress.submit') }}',
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: formData,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            }).then((result) => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Server error atau response bukan JSON!',
                                showConfirmButton: true
                            });
                        }
                    });
                });

                $('body').on('click', '#submit-progress-data', function() {
                    // alert('test');

                    // Jika multiple file:
                    var formData = new FormData();
                    formData.append('status_job', $('#status_job').val());
                    formData.append('progress', $('#progress').val());
                    formData.append('id_job_order', $('#id_job_order').val());
                    formData.append('waktu_setup', $('#waktu_setup_value').val());
                    formData.append('waktu_setup_shift', $('#waktu_setup_value_shift_1').val());
                    formData.append('waktu_setup_shift_2', $('#waktu_setup_value_shift_2').val());
                    formData.append('catatan_shift', $('#catatan_value_shift_1').val());
                    formData.append('catatan_shift_2', $('#catatan_value_shift_2').val());
                    formData.append('waktu_downtime', $('#waktu_downtime_value').val());
                    formData.append('waktu_downtime_shift', $('#waktu_downtime_value_shift_1').val());
                    formData.append('waktu_downtime_shift_2', $('#waktu_downtime_value_shift_2').val());
                    formData.append('catatan', $('#catatan_value').val());
                    formData.append('catatan_shift', $('#catatan_value_shift_1').val());
                    formData.append('catatan_shift_2', $('#catatan_value_shift_2').val());
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                    formData.append('pending_opt', $('#pending_opt').val());


                    var fileInput = $('#customFile')[0];

                    for (let i = 0; i < fileInput.files.length; i++) {
                        formData.append('file[]', fileInput.files[i]);
                    }

                    $.ajax({
                        url: '{{ route('progress-data-prepress.submit') }}',
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        processData: false,
                        contentType: false,
                        data: formData,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            }).then((result) => {
                                // Refresh datatable jika perlu
                                window.location.reload();
                            });

                            // Refresh datatable jika perlu
                            $('#datatable-job-order-prepress-for-plan').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Server error atau response bukan JSON!',
                                showConfirmButton: true
                            });
                        }
                    });
                });

                // Workflow Management System
                var statusJob = $('#status_job').val();
                var role = $('#role').val();
                var nama_pelapor = $('#nama_pelapor').val();
                var nama_userlogin = $('#nama_userlogin').val();
                var flag_two_shift = $('#flag_two_shift').val();

                console.log('Current Status:', statusJob);

                // Initialize workflow based on current status
                initializeWorkflow(statusJob);

                function initializeWorkflow(status) {
                    // Reset all buttons
                    $('#submit-progress-data').hide();
                    $('#submit-pause-data').hide();
                    $('#submit-resume-data').hide();
                    $('#submit-finish-data').hide();
                    $('#submit-edit-progress-data').hide();
                    $('#submit-catatan-kerja').show();

                    // Enable/disable form fields
                    $('#customFile').prop('disabled', true);
                    $('#waktu_setup_value').prop('disabled', true);
                    $('#waktu_downtime_value').prop('disabled', true);
                    $('#catatan_value').prop('disabled', false);

                    switch (status) {
                        case 'ASSIGN':
                        case 'ASSIGNED':
                            // Ready to start work
                            $('#status_job').val('IN PROGRESS');
                            $('#status_job_button').text('IN PROGRESS');
                            $('#submit-progress-data').show();
                            $('#submit-catatan-kerja').hide();
                            $('#submit-edit-progress-data').hide();
                            $('#submit-pause-data').hide();
                            $('#submit-finish-data').hide();
                            $('#submit-resume-data').hide();
                            $('#catatan_value').prop('disabled', true);

                            break;

                        case 'IN PROGRESS':
                            // Currently working - can pause or finish
                            $('#status_job').val('IN PROGRESS');
                            $('#status_job_button').text('IN PROGRESS');
                            $('#submit-pause-data').show();
                            $('#submit-finish-data').hide();
                            $('#customFile').prop('disabled', false);
                            $('#waktu_setup_value').prop('disabled', false);
                            $('#waktu_downtime_value').prop('disabled', false);
                            break;

                        case 'PENDING':
                            // Paused - can resume
                            $('#status_job').val('PENDING');
                            $('#submit-resume-data').show();
                            $('#submit-reject-data').show();
                            $('#customFile').prop('disabled', true);
                            $('#waktu_setup_value').prop('disabled', true);
                            $('#waktu_downtime_value').prop('disabled', true);
                            $('#catatan_value').prop('disabled', true);
                            $('#submit-pause-data').hide();
                            $('#submit-finish-data').hide();
                            $('#submit-resume-data').show();
                            $('#submit-reject-data').hide();
                            $('#submit-edit-progress-data').hide();
                            $('#submit-catatan-kerja').hide();
                            $('#information_job_order').text('Job Order sudah di pause');
                            break;

                        case 'COMPLETED':
                            $('#status_job').val('COMPLETED');
                            $('#submit-reject-data').show();
                            $('#customFile').prop('disabled', false);
                            $('#waktu_setup_value').prop('disabled', false);
                            $('#waktu_downtime_value').prop('disabled', false);
                            $('#catatan_value').prop('disabled', false);

                            if (flag_two_shift == '1') {
                                $('#waktu_setup_value_shift_2').prop('disabled', false);
                                $('#waktu_downtime_value_shift_2').prop('disabled', false);
                                $('#catatan_value_shift_2').prop('disabled', false);
                                $('#waktu_setup_value').prop('disabled', false);
                            } else {
                                $('#waktu_setup_value_shift_1').prop('disabled', false);
                                $('#waktu_downtime_value_shift_1').prop('disabled', false);
                                $('#catatan_value_shift_1').prop('disabled', false);
                            }

                            $('#submit-catatan-kerja').show();
                            $('#submit-pause-data').show();
                            $('#submit-finish-data').show();
                            $('#submit-resume-data').hide();
                            $('#submit-reject-data').hide();
                            break;

                        case 'FINISH':
                            // Finished - can edit or wait for approval
                            $('#status_job').val('FINISH');

                            if (flag_two_shift == '1') {
                                $('#waktu_setup_value_shift_2').prop('disabled', true);
                                $('#waktu_downtime_value_shift_2').prop('disabled', true);
                                $('#catatan_value_shift_2').prop('disabled', true);
                                $('#waktu_setup_value').prop('disabled', true);
                            } else {
                                $('#waktu_setup_value_shift_1').prop('disabled', false);
                                $('#waktu_downtime_value_shift_1').prop('disabled', false);
                                $('#catatan_value_shift_1').prop('disabled', false);
                            }
                            if (role == '3' || role == '4') { // HEAD role
                                $('#status_job_button').text('APPROVE');
                                $('#status_job').val('APPROVED');
                                $('#submit-progress-data').show();
                                $('#submit-catatan-kerja').hide();
                                $('#submit-edit-progress-data').hide();
                                $('#submit-pause-data').hide();
                                $('#submit-finish-data').hide();
                                $('#submit-resume-data').hide();
                                $('#catatan_value').prop('disabled', true);
                                $('#waktu_setup_value').prop('disabled', true);
                                $('#waktu_downtime_value').prop('disabled', true);
                                $('#submit-reject-data').show();
                            } else {
                                $('#submit-edit-progress-data').hide();
                                $('#customFile').prop('disabled', true);
                                $('#waktu_setup_value').prop('disabled', true);
                                $('#waktu_downtime_value').prop('disabled', true);
                                $('#catatan_value').prop('disabled', true);
                                $('#submit-reject-data').hide();
                                $('#submit-progress-data').hide();
                                $('#submit-pause-data').hide();
                                $('#submit-finish-data').hide();
                                $('#submit-resume-data').hide();
                                $('#submit-reject-data').hide();
                                $('#submit-catatan-kerja').hide();
                                $('#information_job_order').text('Job Order sudah selesai dan menunggu approval');

                            }
                            break;

                        case 'SHIFT_2':
                            $('#status_job').val('SHIFT_2');
                            $('#submit-progress-data').hide();
                            $('#submit-catatan-kerja').hide();
                            $('#submit-edit-progress-data').hide();
                            $('#submit-pause-data').hide();
                            $('#submit-finish-data').hide();
                            $('#submit-resume-data').hide();
                            $('#submit-reject-data').hide();
                            $('#submit-close-job-order').hide();
                            $('#submit-dissapprove-data').hide();
                            $('#submit-catatan-kerja').hide();
                            $('#customFile').prop('disabled', true);
                            $('#waktu_setup_value').prop('disabled', true);
                            $('#waktu_downtime_value').prop('disabled', true);
                            $('#catatan_value').prop('disabled', true);
                            $('#information_job_order').text('Job Order menunggu Assign Shift 2');
                            break;

                        case 'CLOSED':
                            $('#status_job').val('CLOSED');
                            $('#submit-close-job-order').hide();
                            $('#submit-dissapprove-data').hide();
                            $('#submit-reject-data').hide();
                            $('#submit-catatan-kerja').hide();
                            $('#information_job_order').text('Job Order sudah diclose');
                            if (flag_two_shift == '1') {
                                $('#waktu_setup_value_shift_2').prop('disabled', true);
                                $('#waktu_downtime_value_shift_2').prop('disabled', true);
                                $('#catatan_value_shift_2').prop('disabled', true);
                                $('#waktu_setup_value').prop('disabled', true);
                            }
                            break;

                        case 'APPROVED':
                            // Approved - final state
                            $('#status_job_button').text('APPROVED');
                            $('#status_job').val('APPROVED');
                            $('#customFile').prop('disabled', true);
                            $('#waktu_setup_value').prop('disabled', true);
                            $('#waktu_downtime_value').prop('disabled', true);
                            $('#catatan_value').prop('disabled', true);
                            $('#submit-catatan-kerja').hide();
                            $('#information_job_order').text('Job Order sudah diapprove dan menunggu Close Job Order');

                            if (role == '3' || role == '4') {
                                $('#submit-dissapprove-data').show();
                            } else {
                                $('#submit-dissapprove-data').hide();
                            }

                            if (nama_pelapor == nama_userlogin) {
                                $('#submit-close-job-order').show();
                            } else {
                                $('#submit-close-job-order').hide();
                            }

                            if (flag_two_shift == '1') {
                                $('#waktu_setup_value_shift_2').prop('disabled', true);
                                $('#waktu_downtime_value_shift_2').prop('disabled', true);
                                $('#catatan_value_shift_2').prop('disabled', true);
                                $('#waktu_setup_value').prop('disabled', true);
                            } else {
                                $('#waktu_setup_value_shift_1').prop('disabled', true);
                                $('#waktu_downtime_value_shift_1').prop('disabled', true);
                                $('#catatan_value_shift_1').prop('disabled', true);
                            }


                            break;

                        default:
                            $('#submit-progress-data').show();
                            break;
                    }
                }

                // DISSAPPROVE Button Click
                $('#submit-dissapprove-data').click(function() {
                    var formData = new FormData();
                    formData.append('status_job', 'DISSAPPROVE');
                    formData.append('id_job_order', $('#id_job_order').val());
                    formData.append('catatan', $('#catatan_value').val());
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                    // confirmation
                    Swal.fire({
                        title: 'Dissapprove Job Order ?',
                        text: 'Job order akan dikembalikan ke status APPROVED',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Dissapprove',
                        cancelButtonText: 'Batal'
                    }).then((result) => {

                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ route('progress-data-prepress.submit') }}',
                                type: "POST",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                processData: false,
                                contentType: false,
                                data: formData,
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 2000
                                    }).then((result) => {
                                        window.location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: 'Server error atau response bukan JSON!',
                                        showConfirmButton: true
                                    });
                                }
                            });
                        }
                    });
                });

                // CLOSE JOB ORDER Button Click
                $('#submit-close-job-order').click(function() {
                    var formData = new FormData();
                    formData.append('status_job', 'CLOSED');
                    formData.append('id_job_order', $('#id_job_order').val());
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                    // confirmation
                    Swal.fire({
                        title: 'Close Job Order ?',
                        text: 'Job order akan diclose',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Close',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ route('progress-data-prepress.submit') }}',
                                type: "POST",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                processData: false,
                                contentType: false,
                                data: formData,
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 2000
                                    }).then((result) => {
                                        window.location.href = '{{ route('prepress.job-order.data.index') }}';
                                    });
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: 'Server error atau response bukan JSON!',
                                        showConfirmButton: true
                                    });
                                }
                            });
                        }
                    });
                });





                // REJECT Button Click
                $('#submit-reject-data').click(function() {
                    var formData = new FormData();
                    formData.append('status_job', 'REJECT');
                    formData.append('id_job_order', $('#id_job_order').val());
                    formData.append('catatan', $('#catatan_value').val());
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                    // confirmation
                    Swal.fire({
                        title: 'Reject Job Order ?',
                        text: 'Job order akan dikembalikan ke status COMPLETED',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Tolak',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ route('progress-data-prepress.submit') }}',
                                type: "POST",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                processData: false,
                                contentType: false,
                                data: formData,
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: response.message,
                                        showConfirmButton: false,
                                        timer: 2000
                                    }).then((result) => {
                                        window.location.reload();
                                    });
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: 'Server error atau response bukan JSON!',
                                        showConfirmButton: true
                                    });
                                }
                            });
                        }
                    });
                });

                // PAUSE Button Click
                $('#submit-pause-data').click(function() {
                    $('#pauseModal').modal('show');
                });

                // Confirm PAUSE
                $('#confirm-pause').click(function() {
                    var reason = $('#reason_pause').val();
                    var pending_opt = $('#pause_reason').val();
                    var picShift2 = $('#pic_shift_2').val();

                    if (!reason) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Silakan pilih alasan PAUSE!',
                            showConfirmButton: true
                        });
                        return;
                    }

                    var formData = new FormData();
                    formData.append('status_job', reason === 'shift_2' ? 'SHIFT_2' : 'PENDING');
                    formData.append('id_job_order', $('#id_job_order').val());
                    formData.append('reason_pause', reason);
                    formData.append('pending_opt', pending_opt);
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                    $.ajax({
                        url: '{{ route('progress-data-prepress.submit') }}',
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        processData: false,
                        contentType: false,
                        data: formData,
                        success: function(response) {
                            $('#pauseModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            }).then((result) => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Server error atau response bukan JSON!',
                                showConfirmButton: true
                            });
                        }
                    });
                });

                // RESUME Button Click
                $('#submit-resume-data').click(function() {
                    var formData = new FormData();
                    formData.append('status_job', 'IN PROGRESS');
                    formData.append('id_job_order', $('#id_job_order').val());
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                    $.ajax({
                        url: '{{ route('progress-data-prepress.resume') }}',
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        processData: false,
                        contentType: false,
                        data: formData,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            }).then((result) => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Server error atau response bukan JSON!',
                                showConfirmButton: true
                            });
                        }
                    });
                });

                // FINISH Button Click
                $('#submit-finish-data').click(function() {
                    var formData = new FormData();
                    formData.append('status_job', 'FINISH');
                    formData.append('id_job_order', $('#id_job_order').val());
                    formData.append('waktu_setup', $('#waktu_setup_value').val());
                    formData.append('waktu_downtime', $('#waktu_downtime_value').val());
                    formData.append('catatan', $('#catatan_value').val());
                    formData.append('waktu_setup_shift', $('#waktu_setup_value_shift_1').val());
                    formData.append('waktu_downtime_shift', $('#waktu_downtime_value_shift_1').val());
                    formData.append('waktu_setup_shift_2', $('#waktu_setup_value_shift_2').val());
                    formData.append('waktu_downtime_shift_2', $('#waktu_downtime_value_shift_2').val());
                    formData.append('catatan_shift', $('#catatan_value_shift_1').val());
                    formData.append('catatan_shift_2', $('#catatan_value_shift_2').val());
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                    var fileInput = $('#customFile')[0];
                    for (let i = 0; i < fileInput.files.length; i++) {
                        formData.append('file[]', fileInput.files[i]);
                    }

                    $.ajax({
                        url: '{{ route('progress-data-prepress.finish') }}',
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        processData: false,
                        contentType: false,
                        data: formData,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            }).then((result) => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Server error atau response bukan JSON!',
                                showConfirmButton: true
                            });
                        }
                    });
                });


                $('body').on('click', '#submit-catatan-kerja', function() {
                    console.log('submit-catatan-kerja');
                    $('#status_job').val('CHANGE');
                    var formData = new FormData();
                    formData.append('catatan', $('#catatan_value').val());
                    formData.append('id_job_order', $('#id_job_order').val());
                    formData.append('status_job', $('#status_job').val());
                    formData.append('progress', $('#progress').val());
                    formData.append('waktu_setup', $('#waktu_setup_value').val());
                    formData.append('waktu_downtime', $('#waktu_downtime_value').val());
                    formData.append('waktu_setup_shift', $('#waktu_setup_value_shift_1').val());
                    formData.append('waktu_downtime_shift', $('#waktu_downtime_value_shift_1').val());
                    formData.append('waktu_setup_shift_2', $('#waktu_setup_value_shift_2').val());
                    formData.append('waktu_downtime_shift_2', $('#waktu_downtime_value_shift_2').val());
                    formData.append('catatan_shift', $('#catatan_value_shift_1').val());
                    formData.append('catatan_shift_2', $('#catatan_value_shift_2').val());
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                    var fileInput = $('#customFile')[0];

                    for (let i = 0; i < fileInput.files.length; i++) {
                        formData.append('file[]', fileInput.files[i]);
                    }
                    $.ajax({
                        url: '{{ route('progress-data-prepress.submit') }}',
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        processData: false,
                        contentType: false,
                        data: formData,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            }).then((result) => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Server error atau response bukan JSON!',
                                showConfirmButton: true
                            });
                        }
                    });
                });



            });
        </script>
    @endsection
