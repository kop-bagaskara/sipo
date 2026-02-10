@extends('main.layouts.main')

@section('title', 'QC Verification - Trial Item Khusus')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="mdi mdi-clipboard-check"></i>
                        QC Verification - Trial Item Khusus
                    </h4>
                    <div class="card-tools">
                        <span class="badge badge-info">Verifikasi kualitas item dari purchasing</span>
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

                    <!-- Purchasing Tracking Summary -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card bg-info text-white">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="mdi mdi-truck-delivery"></i>
                                        Purchasing Tracking Summary
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Item:</strong><br>
                                            <span id="trackingItemName">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Supplier:</strong><br>
                                            <span id="trackingSupplier">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Order Status:</strong><br>
                                            <span id="trackingOrderStatus">-</span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Delivery Status:</strong><br>
                                            <span id="trackingDeliveryStatus">-</span>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <strong>Expected Delivery:</strong><br>
                                            <span id="trackingExpectedDelivery">-</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Actual Delivery:</strong><br>
                                            <span id="trackingActualDelivery">-</span>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <strong>Tracking Notes:</strong><br>
                                            <span id="trackingNotes">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- QC Verification Form -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-success">
                                    <h6 class="mb-0 text-white">
                                        <i class="mdi mdi-clipboard-check"></i>
                                        Quality Control Verification
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <form id="qcVerificationForm">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="verification_date">Tanggal Verifikasi</label>
                                                    <input type="date" class="form-control" id="verification_date" name="verification_date" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="verification_method">Metode Verifikasi</label>
                                                    <select class="form-control" id="verification_method" name="verification_method" required>
                                                        <option value="">Pilih Metode</option>
                                                        <option value="visual">Visual Check</option>
                                                        <option value="measurement">Measurement</option>
                                                        <option value="testing">Testing</option>
                                                        <option value="documentation">Documentation Review</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="verification_result">Hasil Verifikasi</label>
                                                    <select class="form-control" id="verification_result" name="verification_result" required>
                                                        <option value="">Pilih Hasil</option>
                                                        <option value="ok">OK - Sesuai Spesifikasi</option>
                                                        <option value="not_ok">NOT OK - Tidak Sesuai</option>
                                                        <option value="conditional">Conditional - Dengan Catatan</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="verification_score">Skor Kualitas (1-10)</label>
                                                    <input type="number" class="form-control" id="verification_score" name="verification_score" min="1" max="10" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="verification_details">Detail Verifikasi</label>
                                            <textarea class="form-control" id="verification_details" name="verification_details" rows="4" placeholder="Detail hasil verifikasi, termasuk parameter yang dicek..."></textarea>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="defects_found">Defects yang Ditemukan</label>
                                            <textarea class="form-control" id="defects_found" name="defects_found" rows="3" placeholder="Jelaskan defects jika ada (kosongkan jika OK)..."></textarea>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="recommendations">Rekomendasi</label>
                                            <select class="form-control" id="recommendations" name="recommendations" required>
                                                <option value="">Pilih Rekomendasi</option>
                                                <option value="accept">Accept - Item dapat digunakan</option>
                                                <option value="reject">Reject - Item tidak dapat digunakan</option>
                                                <option value="rework">Rework - Perlu perbaikan</option>
                                                <option value="return_supplier">Return to Supplier</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="next_action">Aksi Selanjutnya</label>
                                            <select class="form-control" id="next_action" name="next_action" required>
                                                <option value="">Pilih Aksi</option>
                                                <option value="handover_to_ppic">Serahkan ke PPIC</option>
                                                <option value="return_to_purchasing">Return ke Purchasing</option>
                                                <option value="escalate_to_rnd">Escalate ke RnD</option>
                                            </select>
                                        </div>
                                        
                                        <div class="text-right">
                                            <button type="button" class="btn btn-secondary" onclick="goBack()">
                                                <i class="mdi mdi-arrow-left"></i> Kembali
                                            </button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="mdi mdi-check"></i> Submit Verification
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
    
    .bg-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Set today's date as default
        $('#verification_date').val(new Date().toISOString().split('T')[0]);
        
        // Get process ID from URL
        const urlParams = new URLSearchParams(window.location.search);
        const processId = urlParams.get('process_id');
        
        if (processId) {
            loadProcessData(processId);
        }
        
        // Form submission
        $('#qcVerificationForm').on('submit', function(e) {
            e.preventDefault();
            submitVerification(processId);
        });
        
        // Auto-update next action based on verification result
        $('#verification_result').on('change', function() {
            const result = $(this).val();
            if (result === 'ok') {
                $('#next_action').val('handover_to_ppic');
            } else if (result === 'not_ok') {
                $('#next_action').val('return_to_purchasing');
            } else if (result === 'conditional') {
                $('#next_action').val('escalate_to_rnd');
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
                    
                    // Load tracking data if available
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
        $('#trackingItemName').text(trackingData.item_name || '-');
        $('#trackingSupplier').text(trackingData.supplier_name || '-');
        $('#trackingOrderStatus').text(trackingData.order_status || '-');
        $('#trackingDeliveryStatus').text(trackingData.delivery_status || '-');
        $('#trackingExpectedDelivery').text(trackingData.expected_delivery || '-');
        $('#trackingActualDelivery').text(trackingData.actual_delivery || '-');
        $('#trackingNotes').text(trackingData.tracking_notes || '-');
    }
    
    function submitVerification(processId) {
        const formData = {
            verification_date: $('#verification_date').val(),
            verification_method: $('#verification_method').val(),
            verification_result: $('#verification_result').val(),
            verification_score: $('#verification_score').val(),
            verification_details: $('#verification_details').val(),
            defects_found: $('#defects_found').val(),
            recommendations: $('#recommendations').val(),
            next_action: $('#next_action').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        $.ajax({
            url: `/sipo/development/user-execution/process/${processId}/qc-verification`,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Verifikasi QC berhasil disubmit!');
                    // Redirect back to my processes
                    window.location.href = '{{ route("user-execution.my-processes") }}';
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat submit verifikasi');
            }
        });
    }
    
    function goBack() {
        window.history.back();
    }
</script>
@endsection
