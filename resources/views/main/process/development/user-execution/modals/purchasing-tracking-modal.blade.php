<!-- Purchasing Tracking Modal -->
<div class="modal fade" id="purchasingTrackingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">
                    <i class="mdi mdi-truck-delivery"></i>
                    Purchasing Tracking - Trial Item Khusus
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Job Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-primary">Job Information</h6>
                        <table class="table table-borderless">
                            <tr><td><strong>Job Code:</strong></td><td id="trackingJobCode">-</td></tr>
                            <tr><td><strong>Job Name:</strong></td><td id="trackingJobName">-</td></tr>
                            <tr><td><strong>Type:</strong></td><td id="trackingJobType">-</td></tr>
                            <tr><td><strong>Customer:</strong></td><td id="trackingCustomerName">-</td></tr>
                            <tr><td><strong>Specification:</strong></td><td id="trackingSpecification">-</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-success">Process Information</h6>
                        <table class="table table-borderless">
                            <tr><td><strong>Process:</strong></td><td id="trackingProcessName">-</td></tr>
                            <tr><td><strong>Department:</strong></td><td id="trackingDepartmentName">-</td></tr>
                            <tr><td><strong>Status:</strong></td><td id="trackingProcessStatus">-</td></tr>
                        </table>
                    </div>
                </div>

                <!-- Item Request Information -->
                <div class="card bg-info text-white mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="mdi mdi-information"></i>
                            Item Request Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Item Name:</strong> <span id="trackingItemName">-</span></p>
                                <p><strong>Specification:</strong> <span id="trackingItemSpec">-</span></p>
                                <p><strong>Quantity:</strong> <span id="trackingQuantity">-</span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Required Date:</strong> <span id="trackingRequiredDate">-</span></p>
                                <p><strong>Priority:</strong> <span id="trackingPriority">-</span></p>
                                <p><strong>Budget Estimate:</strong> <span id="trackingBudget">-</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Purchasing Tracking Form -->
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h6 class="mb-0">
                            <i class="mdi mdi-truck-delivery"></i>
                            Update Tracking Status
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="purchasingTrackingForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="trackingOrderStatus">Status Order</label>
                                        <select class="form-control" id="trackingOrderStatus" name="order_status" required>
                                            <option value="">Pilih Status</option>
                                            <option value="pending">Pending - Belum diproses</option>
                                            <option value="inquiry">Inquiry - Sedang survey supplier</option>
                                            <option value="quotation">Quotation - Sudah dapat penawaran</option>
                                            <option value="ordered">Ordered - Sudah dipesan</option>
                                            <option value="production">Production - Sedang diproduksi supplier</option>
                                            <option value="shipped">Shipped - Sudah dikirim</option>
                                            <option value="received">Received - Sudah diterima</option>
                                            <option value="cancelled">Cancelled - Dibatalkan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="trackingOrderDate">Tanggal Order</label>
                                        <input type="date" class="form-control" id="trackingOrderDate" name="order_date">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="trackingDeliveryDate">Estimasi Tanggal Pengiriman</label>
                                        <input type="date" class="form-control" id="trackingDeliveryDate" name="delivery_date">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="trackingReceivedDate">Tanggal Diterima</label>
                                        <input type="date" class="form-control" id="trackingReceivedDate" name="received_date">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="trackingSupplierName">Nama Supplier</label>
                                        <input type="text" class="form-control" id="trackingSupplierName" name="supplier_name" placeholder="Nama supplier yang dipilih">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="trackingOrderValue">Nilai Order</label>
                                        <input type="number" class="form-control" id="trackingOrderValue" name="order_value" placeholder="Rp" min="0">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="trackingNotes">Catatan Tracking</label>
                                <textarea class="form-control" id="trackingNotes" name="tracking_notes" rows="3" placeholder="Update status pengiriman, komunikasi dengan supplier, dll..."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="trackingNextAction">Aksi Selanjutnya</label>
                                <select class="form-control" id="trackingNextAction" name="next_action" required>
                                    <option value="">Pilih Aksi</option>
                                    <option value="continue_tracking">Continue Tracking - Lanjutkan monitoring</option>
                                    <option value="handover_to_qc">Handover to QC - Serahkan ke Quality Control</option>
                                    <option value="escalate_to_rnd">Escalate to RnD - Eskalasi ke RnD</option>
                                    <option value="return_to_ppic">Return to PPIC - Kembalikan ke PPIC</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" onclick="submitPurchasingTracking()">
                    <i class="mdi mdi-check"></i> Update Tracking
                </button>
            </div>
        </div>
    </div>
</div>
