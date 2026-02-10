<!-- Purchasing Tracking Modal -->
<div class="modal fade" id="purchasingTrackingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Purchasing Tracking - Trial Item Khusus</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="purchasingTrackingForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="trackingItemName">Nama Item</label>
                                <input type="text" class="form-control" id="trackingItemName" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="trackingItemSpec">Spesifikasi Item</label>
                                <input type="text" class="form-control" id="trackingItemSpec" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="trackingOrderStatus">Status Pemesanan</label>
                                <select class="form-control" id="trackingOrderStatus" name="order_status" required>
                                    <option value="">Pilih Status</option>
                                    <option value="not_ordered">Belum Dipesan</option>
                                    <option value="ordered">Sudah Dipesan</option>
                                    <option value="in_transit">Dalam Pengiriman</option>
                                    <option value="received">Sudah Diterima</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="trackingOrderDate">Tanggal Pemesanan</label>
                                <input type="date" class="form-control" id="trackingOrderDate" name="order_date">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="trackingDeliveryDate">Tanggal Pengiriman</label>
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
                    
                    <div class="form-group">
                        <label for="trackingNotes">Catatan Tracking</label>
                        <textarea class="form-control" id="trackingNotes" name="tracking_notes" rows="3" placeholder="Update status tracking item..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="trackingNextAction">Aksi Selanjutnya</label>
                        <textarea class="form-control" id="trackingNextAction" name="next_action" rows="2" placeholder="Apa yang akan dilakukan selanjutnya..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" onclick="submitPurchasingTracking()">Update Tracking</button>
            </div>
        </div>
    </div>
</div>
