@extends('main.layouts.main')

@section('title', 'PPIC - Production Planning & Item Request')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="mdi mdi-calendar-clock"></i>
                        PPIC - Production Planning & Item Request
                    </h4>
                    <div class="card-tools">
                        <span class="badge badge-info">Tentukan jadwal produksi atau kirim permintaan item</span>
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
                                <tr><td><strong>Type:</strong></td><td id="jobType">-</td></tr>
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

                    <!-- Dynamic Form Based on Job Type -->
                    <div id="proofForm" style="display: none;">
                        <!-- Proof (Normal) - Production Scheduling -->
                        <div class="card bg-success text-white">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="mdi mdi-calendar-check"></i>
                                    Production Scheduling (Proof Type)
                                </h6>
                            </div>
                            <div class="card-body">
                                <form id="productionSchedulingForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="production_date">Tanggal Produksi</label>
                                                <input type="date" class="form-control" id="production_date" name="production_date" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="production_shift">Shift Produksi</label>
                                                <select class="form-control" id="production_shift" name="production_shift" required>
                                                    <option value="">Pilih Shift</option>
                                                    <option value="shift_1">Shift 1 (06:00 - 14:00)</option>
                                                    <option value="shift_2">Shift 2 (14:00 - 22:00)</option>
                                                    <option value="shift_3">Shift 3 (22:00 - 06:00)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="production_line">Line Produksi</label>
                                                <input type="text" class="form-control" id="production_line" name="production_line" placeholder="Line A, Line B, dll" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="estimated_quantity">Estimasi Quantity</label>
                                                <input type="number" class="form-control" id="estimated_quantity" name="estimated_quantity" min="1" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="production_notes">Catatan Produksi</label>
                                        <textarea class="form-control" id="production_notes" name="production_notes" rows="3" placeholder="Catatan khusus untuk produksi..."></textarea>
                                    </div>
                                    
                                    <div class="text-right">
                                        <button type="button" class="btn btn-secondary" onclick="goBack()">
                                            <i class="mdi mdi-arrow-left"></i> Kembali
                                        </button>
                                        <button type="submit" class="btn btn-success">
                                            <i class="mdi mdi-check"></i> Set Production Schedule
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div id="trialKhususForm" style="display: none;">
                        <!-- Trial Khusus - Item Request to Purchasing -->
                        <div class="card bg-warning text-white">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="mdi mdi-cart-plus"></i>
                                    Item Request to Purchasing (Trial Khusus)
                                </h6>
                            </div>
                            <div class="card-body">
                                <form id="itemRequestForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="item_name">Nama Item yang Dibutuhkan</label>
                                                <input type="text" class="form-control" id="item_name" name="item_name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="item_specification">Spesifikasi Item</label>
                                                <input type="text" class="form-control" id="item_specification" name="item_specification" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="required_quantity">Quantity yang Dibutuhkan</label>
                                                <input type="number" class="form-control" id="required_quantity" name="required_quantity" min="1" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="required_date">Tanggal Dibutuhkan</label>
                                                <input type="date" class="form-control" id="required_date" name="required_date" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="priority_level">Level Prioritas</label>
                                                <select class="form-control" id="priority_level" name="priority_level" required>
                                                    <option value="">Pilih Prioritas</option>
                                                    <option value="low">Low</option>
                                                    <option value="medium">Medium</option>
                                                    <option value="high">High</option>
                                                    <option value="urgent">Urgent</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="budget_estimate">Estimasi Budget</label>
                                                <input type="number" class="form-control" id="budget_estimate" name="budget_estimate" placeholder="Rp" min="0">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="request_reason">Alasan Permintaan</label>
                                        <textarea class="form-control" id="request_reason" name="request_reason" rows="3" placeholder="Jelaskan mengapa item ini dibutuhkan untuk trial..."></textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="additional_notes">Catatan Tambahan</label>
                                        <textarea class="form-control" id="additional_notes" name="additional_notes" rows="2" placeholder="Catatan lain untuk purchasing..."></textarea>
                                    </div>
                                    
                                    <div class="text-right">
                                        <button type="button" class="btn btn-secondary" onclick="goBack()">
                                            <i class="mdi mdi-arrow-left"></i> Kembali
                                        </button>
                                        <button type="submit" class="btn btn-warning">
                                            <i class="mdi mdi-send"></i> Kirim Request ke Purchasing
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
        // Set today's date as default for production date
        $('#production_date').val(new Date().toISOString().split('T')[0]);
        
        // Get process ID from URL
        const urlParams = new URLSearchParams(window.location.search);
        const processId = urlParams.get('process_id');
        
        if (processId) {
            loadProcessData(processId);
        }
        
        // Form submissions
        $('#productionSchedulingForm').on('submit', function(e) {
            e.preventDefault();
            submitProductionSchedule(processId);
        });
        
        $('#itemRequestForm').on('submit', function(e) {
            e.preventDefault();
            submitItemRequest(processId);
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
                    $('#jobType').text(job.type === 'proof' ? 'Proof (Normal)' : 'Trial Item Khusus');
                    $('#customerName').text(job.customer_name || '-');
                    $('#specification').text(job.specification || '-');
                    
                    // Populate process information
                    $('#processName').text(process.process_name);
                    $('#departmentName').text(process.department ? process.department.divisi : '-');
                    $('#assignedUserName').text(process.assigned_user ? process.assigned_user.name : '-');
                    $('#processStatus').text(process.status);
                    
                    // Show appropriate form based on job type
                    if (job.type === 'proof') {
                        $('#proofForm').show();
                        $('#trialKhususForm').hide();
                    } else if (job.type === 'trial_khusus') {
                        $('#proofForm').hide();
                        $('#trialKhususForm').show();
                    }
                }
            },
            error: function() {
                alert('Error loading process data');
            }
        });
    }
    
    function submitProductionSchedule(processId) {
        const formData = {
            production_date: $('#production_date').val(),
            production_shift: $('#production_shift').val(),
            production_line: $('#production_line').val(),
            estimated_quantity: $('#estimated_quantity').val(),
            production_notes: $('#production_notes').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        $.ajax({
            url: `/sipo/development/user-execution/process/${processId}/production-schedule`,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Production schedule berhasil diset!');
                    window.location.href = '{{ route("user-execution.my-processes") }}';
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat set production schedule');
            }
        });
    }
    
    function submitItemRequest(processId) {
        const formData = {
            item_name: $('#item_name').val(),
            item_specification: $('#item_specification').val(),
            required_quantity: $('#required_quantity').val(),
            required_date: $('#required_date').val(),
            priority_level: $('#priority_level').val(),
            budget_estimate: $('#budget_estimate').val(),
            request_reason: $('#request_reason').val(),
            additional_notes: $('#additional_notes').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        $.ajax({
            url: `/sipo/development/user-execution/process/${processId}/item-request`,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Item request berhasil dikirim ke purchasing!');
                    window.location.href = '{{ route("user-execution.my-processes") }}';
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat kirim item request');
            }
        });
    }
    
    function goBack() {
        window.history.back();
    }
</script>
@endsection
