<!-- Process Details Modal -->
<div class="modal fade" id="processDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Process Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="processDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Start Process Modal -->
<div class="modal fade" id="startProcessModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Start Process</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="startProcessForm">
                    <div class="form-group">
                        <label for="startNotes">Catatan Start Process</label>
                        <textarea class="form-control" id="startNotes" name="startNotes" rows="3" placeholder="Masukkan catatan untuk start process..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="startProcess()">Start Process</button>
            </div>
        </div>
    </div>
</div>

<!-- Complete Process Modal -->
<div class="modal fade" id="completeProcessModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Complete Process</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="completeProcessForm">
                    <div class="form-group">
                        <label for="completionNotes">Catatan Completion</label>
                        <textarea class="form-control" id="completionNotes" name="completionNotes" rows="3" placeholder="Masukkan catatan completion..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="completeProcess()">Complete Process</button>
            </div>
        </div>
    </div>
</div>
