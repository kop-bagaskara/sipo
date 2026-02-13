<div class="item-header">
    <span><strong>Item #<?php echo e($itemIndex + 1); ?></strong></span>
    <button type="button" class="btn btn-sm btn-danger remove-item">
        <i class="mdi mdi-delete"></i> Hapus
    </button>
</div>
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label class="form-label">Material Code</label>
            <input type="text" class="form-control" name="items[<?php echo e($itemIndex); ?>][material_code]"
                   value="<?php echo e(old("items.$itemIndex.material_code", $item->material_code ?? '')); ?>"
                   placeholder="Kode material">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="form-label">Design Code</label>
            <input type="text" class="form-control" name="items[<?php echo e($itemIndex); ?>][design_code]"
                   value="<?php echo e(old("items.$itemIndex.design_code", $item->design_code ?? '')); ?>"
                   placeholder="DS.0230.0092">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="form-label">Item Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="items[<?php echo e($itemIndex); ?>][item_name]"
                   value="<?php echo e(old("items.$itemIndex.item_name", $item->item_name ?? '')); ?>"
                   placeholder="Nama item" required>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label class="form-label">DPC Group</label>
            <input type="text" class="form-control" name="items[<?php echo e($itemIndex); ?>][dpc_group]"
                   value="<?php echo e(old("items.$itemIndex.dpc_group", $item->dpc_group ?? '')); ?>"
                   placeholder="DPC 310 42,5 x 83">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label class="form-label">Remarks</label>
            <input type="text" class="form-control" name="items[<?php echo e($itemIndex); ?>][remarks]"
                   value="<?php echo e(old("items.$itemIndex.remarks", $item->remarks ?? '')); ?>"
                   placeholder="By PO, By PO + Forecast, dll">
        </div>
    </div>
</div>
<div class="weekly-data-row">
    <h6 class="mb-3">Weekly Data</h6>
    <div class="row">
    <?php
        $year = old('period_year', isset($forecast) && $forecast ? $forecast->period_year : date('Y'));
        $weeklyData = $item && $item->weeklyData ? $item->weeklyData->keyBy('week_number') : collect();
    ?>
        <?php for($week = 1; $week <= 5; $week++): ?>
            <?php
                $weekData = $weeklyData->get($week);
            ?>
            <div class="col-md-2">
                <div class="form-group">
                    <label class="week-label">W<?php echo e($week); ?>.<?php echo e($year); ?></label>
                    <input type="hidden" name="items[<?php echo e($itemIndex); ?>][weekly_data][<?php echo e($week); ?>][week_number]" value="<?php echo e($week); ?>">
                    <input type="hidden" name="items[<?php echo e($itemIndex); ?>][weekly_data][<?php echo e($week); ?>][year]" value="<?php echo e($year); ?>">
                    <input type="hidden" name="items[<?php echo e($itemIndex); ?>][weekly_data][<?php echo e($week); ?>][week_label]" value="W<?php echo e($week); ?>.<?php echo e($year); ?>">
                    <input type="number" class="form-control weekly-forecast-qty weekly-input"
                           name="items[<?php echo e($itemIndex); ?>][weekly_data][<?php echo e($week); ?>][forecast_qty]"
                           value="<?php echo e(old("items.$itemIndex.weekly_data.$week.forecast_qty", $weekData->forecast_qty ?? '')); ?>"
                           placeholder="QTY" step="0.01" min="0">
                    <input type="number" class="form-control weekly-forecast-ton weekly-input"
                           name="items[<?php echo e($itemIndex); ?>][weekly_data][<?php echo e($week); ?>][forecast_ton]"
                           value="<?php echo e(old("items.$itemIndex.weekly_data.$week.forecast_ton", $weekData->forecast_ton ?? '')); ?>"
                           placeholder="TON" step="0.0001" min="0">
                </div>
            </div>
        <?php endfor; ?>
    </div>
    <div class="row mt-2">
        <div class="col-md-12">
            <div class="alert alert-info mb-0">
                <strong>Total:</strong>
                Forecast QTY: <span class="item-total-qty"><?php echo e(number_format($item ? $item->forecast_qty : 0, 0, ',', '.')); ?></span> |
                Forecast TON: <span class="item-total-ton"><?php echo e(number_format($item ? $item->forecast_ton : 0, 4, ',', '.')); ?></span>
            </div>
        </div>
    </div>
</div>

<?php /**PATH C:\xampp\htdocs\sjm\sipo_krisan\resources\views/main/forecasting/partials/item-row.blade.php ENDPATH**/ ?>