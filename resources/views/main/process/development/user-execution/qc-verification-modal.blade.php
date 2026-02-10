<!-- QC Verification Modal -->
<div class="modal fade" id="qcVerificationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">QC Verification - Trial Item Khusus</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="qcVerificationForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="qcItemName">Nama Item</label>
                                <input type="text" class="form-control" id="qcItemName" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="qcItemSpec">Spesifikasi Item</label>
                                <input type="text" class="form-control" id="qcItemSpec" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="qcVerificationResult">Hasil Verifikasi</label>
                                <select class="form-control" id="qcVerificationResult" name="verification_result" required>
                                    <option value="">Pilih Hasil</option>
                                    <option value="ok">OK</option>
                                    <option value="not_ok">NOT OK</option>
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
                    
                    <div class="form-group">
                        <label for="qcDefectsFound">Defects yang Ditemukan</label>
                        <textarea class="form-control" id="qcDefectsFound" name="defects_found" rows="3" placeholder="Jelaskan defects yang ditemukan (jika ada)..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="qcRecommendations">Rekomendasi</label>
                        <textarea class="form-control" id="qcRecommendations" name="recommendations" rows="3" placeholder="Rekomendasi untuk item ini..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="qcVerificationNotes">Catatan Verifikasi</label>
                        <textarea class="form-control" id="qcVerificationNotes" name="verification_notes" rows="3" placeholder="Catatan detail verifikasi..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="qcNextAction">Aksi Selanjutnya</label>
                        <textarea class="form-control" id="qcNextAction" name="next_action" rows="2" placeholder="Apa yang akan dilakukan selanjutnya..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="submitQcVerification()">Submit Verification</button>
            </div>
        </div>
    </div>
</div>
