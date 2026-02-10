<!-- PPIC Production Planning Modal -->
<div class="modal fade" id="ppicModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">PPIC - Production Planning & Item Request</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Job Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-primary">Job Information</h6>
                        <table class="table table-borderless">
                            <tr><td><strong>Job Code:</strong></td><td id="modalJobCode">-</td></tr>
                            <tr><td><strong>Job Name:</strong></td><td id="modalJobName">-</td></tr>
                            <tr><td><strong>Type:</strong></td><td id="modalJobType">-</td></tr>
                            <tr><td><strong>Customer:</strong></td><td id="modalCustomerName">-</td></tr>
                            <tr><td><strong>Specification:</strong></td><td id="modalSpecification">-</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-success">Process Information</h6>
                        <table class="table table-borderless">
                            <tr><td><strong>Process:</strong></td><td id="modalProcessName">-</td></tr>
                            <tr><td><strong>Department:</strong></td><td id="modalDepartmentName">-</td></tr>
                            <tr><td><strong>Status:</strong></td><td id="modalProcessStatus">-</td></tr>
                        </table>
                    </div>
                </div>

                <!-- Dynamic Form Based on Job Type -->
                <div id="modalProofForm" style="display: none;">
                    <!-- Proof (Normal) - Production Scheduling -->
                    <div class="card bg-success text-white">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="mdi mdi-calendar-check"></i>
                                Production Scheduling (Proof Type)
                            </h6>
                        </div>
                        <div class="card-body">
                            <form id="modalProductionSchedulingForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="modalProductionDate">Tanggal Produksi</label>
                                            <input type="date" class="form-control" id="modalProductionDate" name="production_date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="modalProductionShift">Shift Produksi</label>
                                            <select class="form-control" id="modalProductionShift" name="production_shift" required>
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
                                            <label for="modalProductionLine">Line Produksi</label>
                                            <input type="text" class="form-control" id="modalProductionLine" name="production_line" placeholder="Line A, Line B, dll" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="modalEstimatedQuantity">Estimasi Quantity</label>
                                            <input type="number" class="form-control" id="modalEstimatedQuantity" name="estimated_quantity" min="1" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="modalProductionNotes">Catatan Produksi</label>
                                    <textarea class="form-control" id="modalProductionNotes" name="production_notes" rows="3" placeholder="Catatan khusus untuk produksi..."></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                                 <div id="modalTrialKhususForm" style="display: none;">
                     <!-- Trial Khusus - Item Request to Purchasing -->
                     <div class="card bg-warning text-white">
                         <div class="card-header">
                             <h6 class="mb-0">
                                 <i class="mdi mdi-cart-plus"></i>
                                 Ajukan Permohonan Item ke Purchasing (Trial Khusus)
                             </h6>
                         </div>
                         <div class="card-body">
                             <form id="modalItemRequestForm">
                                 <div class="row">
                                     <div class="col-md-6">
                                         <div class="form-group">
                                             <label for="modalItemName">Nama Item yang Dibutuhkan</label>
                                             <input type="text" class="form-control" id="modalItemName" name="item_name" required>
                                         </div>
                                     </div>
                                     <div class="col-md-6">
                                         <div class="form-group">
                                             <label for="modalItemSpecification">Spesifikasi Item</label>
                                             <input type="text" class="form-control" id="modalItemSpecification" name="item_specification" required>
                                         </div>
                                     </div>
                                 </div>
                                 
                                 <div class="row">
                                     <div class="col-md-6">
                                         <div class="form-group">
                                             <label for="modalRequiredQuantity">Quantity yang Dibutuhkan</label>
                                             <input type="number" class="form-control" id="modalRequiredQuantity" name="required_quantity" min="1" required>
                                         </div>
                                     </div>
                                     <div class="col-md-6">
                                         <div class="form-group">
                                             <label for="modalRequiredDate">Tanggal Dibutuhkan</label>
                                             <input type="date" class="form-control" id="modalRequiredDate" name="required_date" required>
                                         </div>
                                     </div>
                                 </div>
                                 
                                 <div class="row">
                                     <div class="col-md-6">
                                         <div class="form-group">
                                             <label for="modalPriorityLevel">Level Prioritas</label>
                                             <select class="form-control" id="modalPriorityLevel" name="priority_level" required>
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
                                             <label for="modalBudgetEstimate">Estimasi Budget</label>
                                             <input type="number" class="form-control" id="modalBudgetEstimate" name="budget_estimate" placeholder="Rp" min="0">
                                         </div>
                                     </div>
                                 </div>
                                 
                                 <div class="form-group">
                                     <label for="modalRequestReason">Alasan Permintaan</label>
                                     <textarea class="form-control" id="modalRequestReason" name="request_reason" rows="3" placeholder="Jelaskan mengapa item ini dibutuhkan untuk trial..."></textarea>
                                 </div>
                                 
                                 <div class="form-group">
                                     <label for="modalAdditionalNotes">Catatan Tambahan</label>
                                     <textarea class="form-control" id="modalAdditionalNotes" name="additional_notes" rows="2" placeholder="Catatan lain untuk purchasing..."></textarea>
                                 </div>
                             </form>
                         </div>
                     </div>
                 </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="modalSubmitBtn" onclick="submitPPICForm()" style="display: none;">
                    <i class="mdi mdi-check"></i> Submit
                </button>
            </div>
        </div>
    </div>
</div>
