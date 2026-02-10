<!-- QC Verification Modal -->
<div class="modal fade" id="qcVerificationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="mdi mdi-clipboard-check"></i>
                    QC Verification - Trial Item Khusus
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
                            <tr><td><strong>Job Code:</strong></td><td id="qcJobCode">-</td></tr>
                            <tr><td><strong>Job Name:</strong></td><td id="qcJobName">-</td></tr>
                            <tr><td><strong>Type:</strong></td><td id="qcJobType">-</td></tr>
                            <tr><td><strong>Customer:</strong></td><td id="qcCustomerName">-</td></tr>
                            <tr><td><strong>Specification:</strong></td><td id="qcSpecification">-</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-success">Process Information</h6>
                        <table class="table table-borderless">
                            <tr><td><strong>Process:</strong></td><td id="qcProcessName">-</td></tr>
                            <tr><td><strong>Department:</strong></td><td id="qcDepartmentName">-</td></tr>
                            <tr><td><strong>Status:</strong></td><td id="qcProcessStatus">-</td></tr>
                        </table>
                    </div>
                </div>

                <!-- Item Information from Purchasing -->
                <div class="card bg-info text-white mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="mdi mdi-information"></i>
                            Item Information from Purchasing
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Item Name:</strong> <span id="qcItemName">-</span></p>
                                <p><strong>Specification:</strong> <span id="qcItemSpec">-</span></p>
                                <p><strong>Quantity:</strong> <span id="qcQuantity">-</span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Supplier:</strong> <span id="qcSupplier">-</span></p>
                                <p><strong>Received Date:</strong> <span id="qcReceivedDate">-</span></p>
                                <p><strong>Order Value:</strong> <span id="qcOrderValue">-</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- QC Verification Form -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="mdi mdi-clipboard-check"></i>
                            Quality Control Verification
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="qcVerificationForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="qcVerificationResult">Hasil Verifikasi</label>
                                        <select class="form-control" id="qcVerificationResult" name="verification_result" required>
                                            <option value="">Pilih Hasil</option>
                                            <option value="passed">PASSED - Item memenuhi standar</option>
                                            <option value="passed_with_notes">PASSED with Notes - Item OK dengan catatan</option>
                                            <option value="failed">FAILED - Item tidak memenuhi standar</option>
                                            <option value="conditional">CONDITIONAL - Item OK dengan syarat tertentu</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="qcQualityScore">Quality Score (1-10)</label>
                                        <input type="number" class="form-control" id="qcQualityScore" name="quality_score" min="1" max="10" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="qcDefectsFound">Defects Found</label>
                                        <select class="form-control" id="qcDefectsFound" name="defects_found">
                                            <option value="">Pilih Defect</option>
                                            <option value="none">None - Tidak ada defect</option>
                                            <option value="minor">Minor - Defect ringan</option>
                                            <option value="major">Major - Defect serius</option>
                                            <option value="critical">Critical - Defect kritis</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="qcInspectionDate">Tanggal Inspeksi</label>
                                        <input type="date" class="form-control" id="qcInspectionDate" name="inspection_date" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="qcRecommendations">Rekomendasi</label>
                                <select class="form-control" id="qcRecommendations" name="recommendations" required>
                                    <option value="">Pilih Rekomendasi</option>
                                    <option value="approve">Approve - Item dapat digunakan</option>
                                    <option value="approve_with_conditions">Approve with Conditions - Item OK dengan syarat</option>
                                    <option value="reject">Reject - Item tidak dapat digunakan</option>
                                    <option value="return_to_supplier">Return to Supplier - Kembalikan ke supplier</option>
                                    <option value="rework">Rework - Item perlu perbaikan</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="qcVerificationNotes">Catatan Verifikasi</label>
                                <textarea class="form-control" id="qcVerificationNotes" name="verification_notes" rows="4" placeholder="Detail hasil inspeksi, defect yang ditemukan, rekomendasi perbaikan, dll..."></textarea>
                            </div>

                            <div class="form-group">
                                <label for="qcNextAction">Aksi Selanjutnya</label>
                                <select class="form-control" id="qcNextAction" name="next_action" required>
                                    <option value="">Pilih Aksi</option>
                                    <option value="handover_to_rnd">Handover to RnD - Serahkan ke RnD untuk verifikasi final</option>
                                    <option value="return_to_purchasing">Return to Purchasing - Kembalikan ke purchasing</option>
                                    <option value="escalate_to_rnd">Escalate to RnD - Eskalasi ke RnD</option>
                                    <option value="rework_process">Rework Process - Mulai proses perbaikan</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="submitQcVerification()">
                    <i class="mdi mdi-check"></i> Submit QC Verification
                </button>
            </div>
        </div>
    </div>
</div>
