@extends('main.layouts.main')

@section('title', 'Purchasing Tracking - Trial Item Khusus')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="mdi mdi-truck-delivery"></i>
                        Purchasing Tracking - Trial Item Khusus
                    </h4>
                    <div class="card-tools">
                        <span class="badge badge-info">Track status pengadaan item untuk trial khusus</span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Job Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-primary">Job Information</h6>
                            <table class="table table-borderless">
                                <tr><td><strong>Job Code:</strong></td><td id="jobCode">-</td></tr>
                                <tr><td><strong>Job Name:</strong></td><td id="jobName">-</td></tr>
                                <tr><td><strong>Customer:</strong></td><td id="customerName">-</td></tr>
                                <tr><td><strong>Specification:</strong></td><td id="specification">-</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-success">Process Information</h6>
                            <table class="table table-borderless">
                                <tr><td><strong>Process:</strong></td><td id="processName">-</td></tr>
                                <tr><td><strong>Department:</strong></td><td id="departmentName">-</td></tr>
                                <tr><td><strong>Assigned User:</strong></td><td id="assignedUserName">-</td></tr>
                                <tr><td><strong>Status:</strong></td><td id="processStatus">-</td></tr>
                            </table>
                        </div>
                    </div>

                    <!-- Purchasing Tracking Form -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-warning">
                                    <h6 class="mb-0 text-white">
                                        <i class="mdi mdi-clipboard-text"></i>
                                        Item Tracking Status
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <form id="purchasingTrackingForm">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="item_name">Nama Item</label>
                                                    <input type="text" class="form-control" id="item_name" name="item_name" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="supplier_name">Nama Supplier</label>
                                                    <input type="text" class="form-control" id="supplier_name" name="supplier_name" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="order_status">Status Pemesanan</label>
                                                    <select class="form-control" id="order_status" name="order_status" required>
                                                        <option value="">Pilih Status</option>
                                                        <option value="not_ordered">Belum Dipesan</option>
                                                        <option value="ordered">Sudah Dipesan</option>
                                                        <option value="confirmed">Konfirmasi Supplier</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="delivery_status">Status Pengiriman</label>
                                                    <select class="form-control" id="delivery_status" name="delivery_status" required>
                                                        <option value="">Pilih Status</option>
                                                        <option value="not_shipped">Belum Dikirim</option>
                                                        <option value="shipped">Sedang Dikirim</option>
                                                        <option value="received">Sudah Diterima</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="expected_delivery">Estimasi Kedatangan</label>
                                                    <input type="date" class="form-control" id="expected_delivery" name="expected_delivery">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="actual_delivery">Tanggal Kedatangan Aktual</label>
                                                    <input type="date" class="form-control" id="actual_delivery" name="actual_delivery">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="tracking_notes">Catatan Tracking</label>
                                            <textarea class="form-control" id="tracking_notes" name="tracking_notes" rows="3" placeholder="Catatan detail tracking item..."></textarea>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="next_action">Aksi Selanjutnya</label>
                                            <select class="form-control" id="next_action" name="next_action" required>
                                                <option value="">Pilih Aksi</option>
                                                <option value="waiting_delivery">Menunggu Kedatangan</option>
                                                <option value="ready_for_qc">Siap untuk QC Check</option>
                                                <option value="handover_to_ppic">Serahkan ke PPIC</option>
                                            </select>
                                        </div>
                                        
                                        <div class="text-right">
                                            <button type="button" class="btn btn-secondary" onclick="goBack()">
                                                <i class="mdi mdi-arrow-left"></i> Kembali
                                            </button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="mdi mdi-check"></i> Update Tracking
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    
    .form-control {
        border-radius: 8px;
        border: 1px solid #ddd;
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .btn {
        border-radius: 8px;
        padding: 8px 20px;
    }
    
    .table td {
        padding: 8px 12px;
        vertical-align: middle;
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Get process ID from URL
        const urlParams = new URLSearchParams(window.location.search);
        const processId = urlParams.get('process_id');
        
        if (processId) {
            loadProcessData(processId);
        }
        
        // Form submission
        $('#purchasingTrackingForm').on('submit', function(e) {
            e.preventDefault();
            updateTracking(processId);
        });
        
        // Auto-update next action based on delivery status
        $('#delivery_status').on('change', function() {
            const deliveryStatus = $(this).val();
            if (deliveryStatus === 'received') {
                $('#next_action').val('ready_for_qc');
            } else if (deliveryStatus === 'shipped') {
                $('#next_action').val('waiting_delivery');
            }
        });
    });
    
    function loadProcessData(processId) {
        $.ajax({
            url: `/sipo/development/user-execution/process/${processId}/data`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const process = response.process;
                    const job = response.job;
                    
                    // Populate job information
                    $('#jobCode').text(job.job_code);
                    $('#jobName').text(job.job_name);
                    $('#customerName').text(job.customer_name || '-');
                    $('#specification').text(job.specification || '-');
                    
                    // Populate process information
                    $('#processName').text(process.process_name);
                    $('#departmentName').text(process.department ? process.department.divisi : '-');
                    $('#assignedUserName').text(process.assigned_user ? process.assigned_user.name : '-');
                    $('#processStatus').text(process.status);
                    
                    // Load existing tracking data if any
                    if (process.tracking_data) {
                        loadTrackingData(process.tracking_data);
                    }
                }
            },
            error: function() {
                alert('Error loading process data');
            }
        });
    }
    
    function loadTrackingData(trackingData) {
        $('#item_name').val(trackingData.item_name || '');
        $('#supplier_name').val(trackingData.supplier_name || '');
        $('#order_status').val(trackingData.order_status || '');
        $('#delivery_status').val(trackingData.delivery_status || '');
        $('#expected_delivery').val(trackingData.expected_delivery || '');
        $('#actual_delivery').val(trackingData.actual_delivery || '');
        $('#tracking_notes').val(trackingData.tracking_notes || '');
        $('#next_action').val(trackingData.next_action || '');
    }
    
    function updateTracking(processId) {
        const formData = {
            item_name: $('#item_name').val(),
            supplier_name: $('#supplier_name').val(),
            order_status: $('#order_status').val(),
            delivery_status: $('#delivery_status').val(),
            expected_delivery: $('#expected_delivery').val(),
            actual_delivery: $('#actual_delivery').val(),
            tracking_notes: $('#tracking_notes').val(),
            next_action: $('#next_action').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        $.ajax({
            url: `/sipo/development/user-execution/process/${processId}/update-tracking`,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Tracking berhasil diupdate!');
                    // Redirect back to my processes
                    window.location.href = '{{ route("user-execution.my-processes") }}';
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat update tracking');
            }
        });
    }
    
    function goBack() {
        window.history.back();
    }
</script>
@endsection
