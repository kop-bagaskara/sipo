<script>
    // currentProcessId is already declared in execute-process.blade.php
    // currentProcessData is already declared in execute-process.blade.php

    // Set today's date as default for date inputs
    $(document).ready(function() {
        $('#modalProductionDate').val(new Date().toISOString().split('T')[0]);
        $('#modalRequiredDate').val(new Date().toISOString().split('T')[0]);
    });

    // Process Details Functions
    function viewProcessDetails(processId) {
        $.ajax({
            url: `/sipo/development/user-execution/process/${processId}/data`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const process = response.process;
                    const job = response.job;

                    let content = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary">Job Information</h6>
                                <table class="table table-borderless">
                                    <tr><td><strong>Job Code:</strong></td><td>${job.job_code}</td></tr>
                                    <tr><td><strong>Job Name:</strong></td><td>${job.job_name}</td></tr>
                                    <tr><td><strong>Type:</strong></td><td>${job.type === 'proof' ? 'Proof (Normal)' : 'Trial Item Khusus'}</td></tr>
                                    <tr><td><strong>Customer:</strong></td><td>${job.customer_name || '-'}</td></tr>
                                    <tr><td><strong>Specification:</strong></td><td>${job.specification || '-'}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success">Process Information</h6>
                                <table class="table table-borderless">
                                    <tr><td><strong>Process:</strong></td><td>${process.process_name}</td></tr>
                                    <tr><td><strong>Department:</strong></td><td>${process.department ? process.department.divisi : '-'}</td></tr>
                                    <tr><td><strong>Status:</strong></td><td>${process.status}</td></tr>
                                    <tr><td><strong>Duration:</strong></td><td>${process.estimated_duration || '-'} hrs</td></tr>
                                    <tr><td><strong>Notes:</strong></td><td>${process.notes || '-'}</td></tr>
                                </table>
                            </div>
                        </div>
                    `;

                    $('#processDetailsContent').html(content);
                    $('#processDetailsModal').modal('show');
                }
            },
            error: function() {
                alert('Error loading process details');
            }
        });
    }

    // Start Process Functions
    function openStartProcessModal(processId) {
        currentProcessId = processId;
        $('#startNotes').val('');
        $('#startProcessModal').modal('show');
    }

    function startProcess() {
        if (!currentProcessId) return;

        const notes = $('#startNotes').val();

        $.ajax({
            url: `/sipo/development/user-execution/process/${currentProcessId}/start`,
            type: 'POST',
            data: {
                notes: notes,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#startProcessModal').modal('hide');
                    $('#my-processes-table').DataTable().ajax.reload();
                    updateStatistics();
                    alert('Process berhasil dimulai!');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat start process');
            }
        });
    }

    // Complete Process Functions
    function openCompleteProcessModal(processId) {
        currentProcessId = processId;
        $('#completionNotes').val('');
        $('#completeProcessModal').modal('show');
    }

    function completeProcess() {
        if (!currentProcessId) return;

        const notes = $('#completionNotes').val();

        $.ajax({
            url: `/sipo/development/user-execution/process/${currentProcessId}/complete`,
            type: 'POST',
            data: {
                notes: notes,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#completeProcessModal').modal('hide');
                    $('#my-processes-table').DataTable().ajax.reload();
                    updateStatistics();
                    alert('Process berhasil diselesaikan!');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat complete process');
            }
        });
    }

    // PPIC Modal Functions
    function openPPICModal(processId) {
        currentProcessId = processId;
        loadProcessDataForModal(processId);
        $('#ppicModal').modal('show');
    }

    function loadProcessDataForModal(processId) {
        $.ajax({
            url: `/sipo/development/user-execution/process/${processId}/data`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const process = response.process;
                    const job = response.job;

                    // Populate modal fields
                    $('#modalJobCode').text(job.job_code);
                    $('#modalJobName').text(job.job_name);
                    $('#modalJobType').text(job.type === 'proof' ? 'Proof (Normal)' : 'Trial Item Khusus');
                    $('#modalCustomerName').text(job.customer_name || '-');
                    $('#modalSpecification').text(job.specification || '-');
                    $('#modalProcessName').text(process.process_name);
                    $('#modalDepartmentName').text(process.department ? process.department.divisi : '-');
                    $('#modalProcessStatus').text(process.status);

                    // Show appropriate form based on job type
                    if (job.type === 'proof') {
                        $('#modalProofForm').show();
                        $('#modalTrialKhususForm').hide();
                        $('#modalSubmitBtn').show().text('Set Production Schedule');
                    } else if (job.type === 'trial_khusus') {
                        $('#modalProofForm').hide();
                        $('#modalTrialKhususForm').show();
                        $('#modalSubmitBtn').show().text('Ajukan Permohonan ke Purchasing');
                    }
                }
            },
            error: function() {
                alert('Error loading process data');
            }
        });
    }

    function submitPPICForm() {
        if (!currentProcessId) return;

        let formData = {};
        let url = '';

        // Check which form is visible
        if ($('#modalProofForm').is(':visible')) {
            // Production Scheduling form
            formData = {
                production_date: $('#modalProductionDate').val(),
                production_shift: $('#modalProductionShift').val(),
                production_line: $('#modalProductionLine').val(),
                estimated_quantity: $('#modalEstimatedQuantity').val(),
                production_notes: $('#modalProductionNotes').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            url = `/sipo/development/user-execution/process/${currentProcessId}/production-schedule`;
        } else {
            // Item Request form
            formData = {
                item_name: $('#modalItemName').val(),
                item_specification: $('#modalItemSpecification').val(),
                required_quantity: $('#modalRequiredQuantity').val(),
                required_date: $('#modalRequiredDate').val(),
                priority_level: $('#modalPriorityLevel').val(),
                budget_estimate: $('#modalBudgetEstimate').val(),
                request_reason: $('#modalRequestReason').val(),
                additional_notes: $('#modalAdditionalNotes').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            url = `/sipo/development/user-execution/process/${currentProcessId}/item-request`;
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#ppicModal').modal('hide');
                    $('#my-processes-table').DataTable().ajax.reload();
                    updateStatistics();
                    alert('Data berhasil disubmit!');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat submit data');
            }
        });
    }

    // Purchasing Tracking Functions
    function openPurchasingTrackingModal(processId) {
        currentProcessId = processId;
        loadProcessDataForPurchasing(processId);
        $('#purchasingTrackingModal').modal('show');
    }

         function loadProcessDataForPurchasing(processId) {
         $.ajax({
             url: `/sipo/development/user-execution/process/${processId}/data`,
             type: 'GET',
             success: function(response) {
                 if (response.success) {
                     const process = response.process;
                     const job = response.job;
                     
                     // Populate job information
                     $('#trackingJobCode').text(job.job_code);
                     $('#trackingJobName').text(job.job_name);
                     $('#trackingJobType').text(job.type === 'proof' ? 'Proof (Normal)' : 'Trial Item Khusus');
                     $('#trackingCustomerName').text(job.customer_name || '-');
                     $('#trackingSpecification').text(job.specification || '-');
                     
                     // Populate process information
                     $('#trackingProcessName').text(process.process_name);
                     $('#trackingDepartmentName').text(process.department ? process.department.divisi : '-');
                     $('#trackingProcessStatus').text(process.status);
                     
                     // Load item request data if available
                     if (process.tracking_data && process.tracking_data.item_request) {
                         const itemRequest = process.tracking_data.item_request;
                         $('#trackingItemName').text(itemRequest.item_name || '-');
                         $('#trackingItemSpec').text(itemRequest.item_specification || '-');
                         $('#trackingQuantity').text(itemRequest.required_quantity || '-');
                         $('#trackingRequiredDate').text(itemRequest.required_date || '-');
                         $('#trackingPriority').text(itemRequest.priority_level || '-');
                         $('#trackingBudget').text(itemRequest.budget_estimate ? 'Rp ' + itemRequest.budget_estimate : '-');
                     }
                     
                     // Set today's date as default
                     $('#trackingOrderDate').val(new Date().toISOString().split('T')[0]);
                     $('#trackingDeliveryDate').val(new Date().toISOString().split('T')[0]);
                     $('#trackingReceivedDate').val(new Date().toISOString().split('T')[0]);
                 }
             },
             error: function() {
                 alert('Error loading process data');
             }
         });
     }

         function submitPurchasingTracking() {
         if (!currentProcessId) return;
         
         const formData = {
             order_status: $('#trackingOrderStatus').val(),
             order_date: $('#trackingOrderDate').val(),
             delivery_date: $('#trackingDeliveryDate').val(),
             received_date: $('#trackingReceivedDate').val(),
             supplier_name: $('#trackingSupplierName').val(),
             order_value: $('#trackingOrderValue').val(),
             tracking_notes: $('#trackingNotes').val(),
             next_action: $('#trackingNextAction').val(),
             _token: $('meta[name="csrf-token"]').attr('content')
         };
         
         $.ajax({
             url: `/sipo/development/user-execution/process/${currentProcessId}/update-tracking`,
             type: 'POST',
             data: formData,
             success: function(response) {
                 if (response.success) {
                     $('#purchasingTrackingModal').modal('hide');
                     $('#my-processes-table').DataTable().ajax.reload();
                     updateStatistics();
                     alert('Purchasing tracking berhasil diupdate!');
                 } else {
                     alert('Error: ' + response.message);
                 }
             },
             error: function() {
                 alert('Terjadi kesalahan saat update purchasing tracking');
             }
         });
     }

    // QC Verification Functions
    function openQcVerificationModal(processId) {
        currentProcessId = processId;
        loadProcessDataForQC(processId);
        $('#qcVerificationModal').modal('show');
    }

         function loadProcessDataForQC(processId) {
         $.ajax({
             url: `/sipo/development/user-execution/process/${processId}/data`,
             type: 'GET',
             success: function(response) {
                 if (response.success) {
                     const process = response.process;
                     const job = response.job;
                     
                     // Populate job information
                     $('#qcJobCode').text(job.job_code);
                     $('#qcJobName').text(job.job_name);
                     $('#qcJobType').text(job.type === 'proof' ? 'Proof (Normal)' : 'Trial Item Khusus');
                     $('#qcCustomerName').text(job.customer_name || '-');
                     $('#qcSpecification').text(job.specification || '-');
                     
                     // Populate process information
                     $('#qcProcessName').text(process.process_name);
                     $('#qcDepartmentName').text(process.department ? process.department.divisi : '-');
                     $('#qcProcessStatus').text(process.status);
                     
                     // Load item request data if available
                     if (process.tracking_data && process.tracking_data.item_request) {
                         const itemRequest = process.tracking_data.item_request;
                         $('#qcItemName').text(itemRequest.item_name || '-');
                         $('#qcItemSpec').text(itemRequest.item_specification || '-');
                         $('#qcQuantity').text(itemRequest.required_quantity || '-');
                     }
                     
                     // Load purchasing tracking data if available
                     if (process.tracking_data && process.tracking_data.purchasing_tracking) {
                         const purchasingTracking = process.tracking_data.purchasing_tracking;
                         $('#qcSupplier').text(purchasingTracking.supplier_name || '-');
                         $('#qcReceivedDate').text(purchasingTracking.received_date || '-');
                         $('#qcOrderValue').text(purchasingTracking.order_value ? 'Rp ' + purchasingTracking.order_value : '-');
                     }
                     
                     // Set today's date as default
                     $('#qcInspectionDate').val(new Date().toISOString().split('T')[0]);
                 }
             },
             error: function() {
                 alert('Error loading process data');
             }
         });
     }

         function submitQcVerification() {
         if (!currentProcessId) return;
         
         const formData = {
             verification_result: $('#qcVerificationResult').val(),
             quality_score: $('#qcQualityScore').val(),
             defects_found: $('#qcDefectsFound').val(),
             inspection_date: $('#qcInspectionDate').val(),
             recommendations: $('#qcRecommendations').val(),
             verification_notes: $('#qcVerificationNotes').val(),
             next_action: $('#qcNextAction').val(),
             _token: $('meta[name="csrf-token"]').attr('content')
         };
         
         $.ajax({
             url: `/sipo/development/user-execution/process/${currentProcessId}/qc-verification`,
             type: 'POST',
             data: formData,
             success: function(response) {
                 if (response.success) {
                     $('#qcVerificationModal').modal('hide');
                     $('#my-processes-table').DataTable().ajax.reload();
                     updateStatistics();
                     alert('QC verification berhasil disubmit!');
                 } else {
                     alert('Error: ' + response.message);
                 }
             },
             error: function() {
                 alert('Terjadi kesalahan saat submit QC verification');
             }
         });
     }
</script>
